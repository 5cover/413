<?php
namespace DB;

require_once 'const.php';
require_once 'util.php';
require_once 'LogPDO.php';
require_once 'Arg.php';
require_once 'Clause.php';
require_once 'NoOpPDOStatement.php';

use PDO;
use PDOStatement;
use Throwable;

/**
 * Se connecter à la base de données.
 *
 * La valeur retournée par cette fonction est cachée : l'appeler plusieurs fois n'a aucun effet. Il n'y a donc pas besoin de conserber son résultat dans une variable.
 * @return LogPDO L'objet PDO connecté à la base de données.
 */
function connect(): LogPDO
{
    static $pdo;
    if ($pdo !== null) return $pdo;

    // Connect to the database
    $driver = 'pgsql';

    // / Load .env
    $env   = file_get_contents(__DIR__ . '/../.env');
    $lines = explode("\n", $env);

    foreach ($lines as $line) {
        preg_match('/([^#]+)\=(.*)/', $line, $matches);
        if (isset($matches[2])) { putenv(trim($line)); }
    }

    // dotenv variables
    $host     = notfalse(getenv('DB_HOST'), 'DB_HOST unset');
    $port     = notfalse(getenv('PGDB_PORT'), 'PGDB_PORT unset');
    $dbname   = notfalse(getenv('DB_NAME'), 'DB_NAME unset');
    $username = notfalse(getenv('DB_USER'), 'DB_USER unset');
    $password = notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD unset');

    $args = [
        "$driver:host=$host;port=$port;dbname=$dbname",
        $username,
        $password,
    ];

    $pdo = new LogPDO(...$args);
    $pdo->log = is_localhost();

    notfalse($pdo->exec("set schema 'pact'"));

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
}

/**
 * Effectue une transaction.
 *
 * Cette fonction automatise BEGIN, COMMIT et ROLLBACK pour effectuer une transaction dans la base de données.
 *
 * Regrouper les statements liés dans une transaction permet notamment de préserver la cohérence de la base de données en cas d'erreur.
 *
 * @param callable $body La fonction contenant le corps de la transaction. Elle est appelée entre le BEGIN et le COMMIT. Si cette fonction jette une exception, un ROLLBACK est effectué.
 * @param ?callable $cleanup La fonction à appeler pour effectuer un nettoyage additionnel lorsque $body jette une exception, avant le ROLLBACK. Optionnel.
 * @throws Throwable Any exception $body has thrown.
 */
function transaction(callable $body, ?callable $cleanup = null): void
{
    $pdo = connect();
    notfalse($pdo->beginTransaction(), '$pdo->beginTransaction() failed');

    try {
        $body();
        error_log('Transaction successful, committing...' . PHP_EOL);
        $pdo->commit();
    } catch (Throwable $e) {
        error_log('An error occured, cleaning up and rolling back...' . PHP_EOL);
        if ($cleanup !== null)
            $cleanup();
        notfalse($pdo->rollBack(), '$pdo->rollBack() failed');
        throw $e;
    }
}

function is_localhost(): bool
{
    $http_host = $_SERVER['HTTP_HOST'] ?? null;
    return $http_host === null || str_starts_with($http_host, 'localhost:');
}

/**
 * Le chemin absolu de du dossier racine du serveur.
 * @return string
 */
function document_root(): string
{
    return is_localhost() ? __DIR__ . '/../html' : '/var/www/html';
}

// Query construction functions

function quote_identifier(string $identifier): string
{
    return '"' . str_replace('"', '""', $identifier) . '"';
}

function quote_string(string $string): string
{
    return "'" . str_replace("'", "''", $string) . "'";
}

/**
 * Generates a WHERE clause for a SQL query based on an array of key-value pairs.
 *
 * @param BinOp $operator The logical operator to use between clauses.
 * @param Clause[] $clauses An array containing the conditions for the WHERE clause.
 * @return string The generated WHERE clause, or an empty string if no clauses are provided.
 */
function where_clause(BinOp $operator, array $clauses): string
{
    return $clauses
        ? ' where ' . implode(" $operator->value ", array_map(fn($c) => $c->to_sql(), $clauses)) . ' '
        : ' ';
}

/**
 * @param Table $table
 * @param string[] $columns null for '*'
 * @param Clause[] $where la clause WHERE du SELECT
 * @param ?string $order_by La section ORDER BY: un nom de coloone + [ASC | DESC]
 * @param ?int $limit Nombre maximum de lignes à retourner
 * @return PDOStatement
 */
function select(Table $table, array $columns, array $where = [], ?string $order_by = null, ?int $limit = null): PDOStatement
{
    $attrs = implode(',', $columns);
    $sql = "select $attrs $table->value " . where_clause(BinOp::And, $where);
    if ($order_by !== null) $sql .= " order by $order_by";
    if ($limit !== null) $sql .= " limit $limit";
    $stmt = notfalse(connect()->prepare($sql));
    foreach ($where as $cond) {
        bind_values($stmt, $cond->to_args());
    }
    return $stmt;
}

/**
 * Prépare un *statement* INSERT INTO pour 1 ligne retournant des colonnes.
 * @param Table $table La table dans laquelle insérer
 * @param array<string, Arg> $args Les noms de colonne => leur valeur.
 * @param string[] $columns_returning Les colonnes a mettre dans la clause RETURNING.
 * @return PDOStatement Une *statement* prêt à l'exécution, retournant un table 1x1, la valeur de la colonne ID.
 */
function insert_into(Table $table, array $args, array $columns_returning = []): PDOStatement
{
    if (!$args) return notfalse(connect()->prepare("insert into $table->value default values"));

    $column_names = implode(',', array_keys($args));
    $arg_names = implode(',', array_map(fn($c) => ":$c", array_keys($args)));

    $stmt = notfalse(connect()->prepare("insert into $table->value ($column_names) values ($arg_names)"
        . ($columns_returning ? 'returning ' . implode(',', $columns_returning) : '')));

    bind_values($stmt, $args);
    return $stmt;
}

/**
 * @param Table $table
 * @param string[] $columns
 * @param array<int, Arg> $values
 * @param string[] $columns_returning
 * @return PDOStatement
 */
function insert_into_multiple(Table $table, array $columns, array $values, array $columns_returning = []): PDOStatement
{
    if (!$columns || !$values) return NoOpPDOStatement::instance();

    assert(count($values) % count($columns) === 0, "value count must be a multiple of row count for insert_into_multipel");
    assert(array_is_list($values), "for proper pdo binding, array must be list");

    $n_rows = count($values) / count($columns);

    $column_names = implode(',', $columns);
    $placeholder = '(' . substr(str_repeat('?,', count($columns)), 0, -1) . ')';
    $rows = implode(',', array_fill(0, $n_rows, $placeholder));
    $sql = "INSERT INTO $table->value ($column_names) VALUES $rows";
    if ($columns_returning) $sql .= 'returning ' . implode(',', $columns_returning);

    $stmt = notfalse(connect()->prepare($sql));
    foreach ($values as $num => $arg) {
        notfalse($stmt->bindValue($num + 1, $arg->value, $arg->pdo_type));
    }
    return $stmt;
}

/**
 * Prépare un *statement* UPDATE.
 * @param Table $table La table dans la quelle mettre à jour.
 * @param array<string, Arg> $args Les colonnes à modifier => leurs valeurs pour la clause SET du UPDATE.
 * @param Clause[] $where Les conditions de la clause WHERE du UPDATE
 * @param string[] $columns_returning Les colonnes a mettre dans la clause RETURNING.
 * @return PDOStatement Un *statement* prêt à l'exécution, ne retournant rien.
 */
function update(Table $table, array $args, array $where, array $columns_returning = []): PDOStatement
{
    if (!$args) return NoOpPDOStatement::instance();

    $stmt = notfalse(connect()->prepare("update $table->value set "
        . implode(',', array_map(fn($col) => "$col = :$col", array_keys($args)))
        . where_clause(BinOp::And, $where)
        . ($columns_returning ? 'returning ' . implode(',', $columns_returning) : '')));

    bind_values($stmt, $args);
    foreach ($where as $cond) {
        bind_values($stmt, $cond->to_args());
    }
    return $stmt;
}

/**
 * @param Table $table
 * @param Clause[] $where
 * @return PDOStatement
 */
function delete(Table $table, array $where): PDOStatement
{
    $stmt = notfalse(connect()->prepare("delete from $table->value " . where_clause(BinOp::And, $where)));
    foreach ($where as $cond) {
        bind_values($stmt, $cond->to_args());
    }
    return $stmt;
}

/**
 * Binds types values to a statement.
 *
 * @param PDOStatement $stmt The statement on which to bind values.
 * @param iterable<string, Arg> $args
 */
function bind_values(PDOStatement $stmt, iterable $args): void
{
    foreach ($args as $name => $arg) {
        notfalse($stmt->bindValue($name, $arg->value, $arg->pdo_type));
    }
}

