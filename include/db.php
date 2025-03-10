<?php
namespace DB;

use PDO, PDOStatement;

require_once 'util.php';

$_pdo = null;

/**
 * Se connecter à la base de données.
 *
 * La valeur retournée par cette fonction est cachée : l'appeler plusieurs fois n'a aucun effet. Il n'y a donc pas besoin de conserber son résultat dans une variable.
 * @return PDO L'objet PDO connecté à la base de données.
 */
function connect(): PDO
{
    global $_pdo;
    if ($_pdo !== null) {
        return $_pdo;
    }

    // Load .env file
    {
        $envfile = __DIR__ . '/.env';
        foreach (notfalse(file($envfile, FILE_SKIP_EMPTY_LINES), "dotenv file missing at $envfile") as $line) {
            notfalse(putenv(trim($line)));
        }
    }

    // Connect to the database
    $driver = 'pgsql';
    [$host, $port, $dbname, $username, $password] = is_localhost()
        ? [
            'localhost',
            5432,
            'raphael',
            'postgres',
            'postgres'
        ]
        : [
            'postgresdb',
            notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set'),
            'postgres',
            notfalse(getenv('DB_USER'), 'DB_USER not set'),
            notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set')
        ];

    $args = [
        "$driver:host=$host;port=$port;dbname=$dbname",
        $username,
        $password,
    ];

    $_pdo = is_localhost() ? new LogPDO(...$args) : new PDO(...$args);

    notfalse($_pdo->exec("set schema 'pact'"));

    $_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $_pdo;
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
 */
function transaction(callable $body, ?callable $cleanup = null)
{
    $pdo = connect();
    notfalse($pdo->beginTransaction(), '$pdo->beginTransaction() failed');

    try {
        $body();
        error_log('Transaction successful, committing...' . PHP_EOL);
        $pdo->commit();
    } catch (\Throwable $e) {
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
    /*
    $server_ip = null;

    if (defined('INPUT_SERVER') && filter_has_var(INPUT_SERVER, 'REMOTE_ADDR')) {
        $server_ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    } elseif (defined('INPUT_ENV') && filter_has_var(INPUT_ENV, 'REMOTE_ADDR')) {
        $server_ip = filter_input(INPUT_ENV, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $server_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }

    if (empty($server_ip)) {
        $server_ip = '127.0.0.1';
    }

    return empty(filter_var($server_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE));
    */
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

enum BoolOperator: string
{
    case AND = 'and';
    case OR = 'or';
}

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
 * @param BoolOperator $operator The logical operator to use between clauses.
 * @param string[] $clauses An array containing the conditions for the WHERE clause.
 * @return string The generated WHERE clause, or an empty string if no clauses are provided.
 */
function where_clause(BoolOperator $operator, array $clauses, string $prefix = ''): string
{
    return $clauses
        ? ' where ' . implode(" $operator->value ", array_map(fn($attr) => elvis($prefix, '.') . "$attr = :$attr", $clauses)) . ' '
        : ' ';
}

/**
 * Prépare un *statement* INSERT INTO pour 1 ligne retournant des colonnes.
 * @param string $table La table dans laquelle insérer
 * @param array<string, array{mixed, int}> $args Les noms de colonne => leur valeur.
 * @param string[] $returning Les colonnes a mettre dans la clause RETURNING.
 * @return PDOStatement Une *statement* prêt à l'exécution, retournant un table 1x1, la valeur de la colonne ID.
 */
function insert_into(string $table, array $args, array $returning = []): PDOStatement
{
    if (!$args) {
        return notfalse(connect()->prepare("insert into $table default values"));
    }

    $column_names = implode(',', array_keys($args));
    $arg_names = implode(',', array_map(fn($col) => ":$col", array_keys($args)));
    $stmt = notfalse(connect()->prepare("insert into $table ($column_names) values ($arg_names)"
        . ($returning ? 'returning ' . implode(',', array_map(quote_identifier(...), $returning)) : '')));

    bind_values($stmt, $args);
    return $stmt;
}

/**
 * Prépare un *statement* UPDATE.
 * @param string $table La table dans la quelle mettre à jour.
 * @param array<string, array{mixed, int}> $args Les colonnes à modifier => leurs valeurs pour la clause SET du UPDATE.
 * @param array<string, array{mixed, int}> $key_args Les colonnes clés => leurs valeurs pour la clause WHERE du UPDATE.
 * @param string[] $returning Les colonnes a mettre dans la clause RETURNING.
 * @return PDOStatement Un *statement* prêt à l'exécution, ne retournant rien.
 */
function update(string $table, array $args, array $key_args, array $returning = []): PDOStatement
{
    if (!$args) {
        return notfalse(connect()->prepare('select null'));  // todo: does a empty string work as a noop? test it when we get a working thing.
    }

    $stmt = notfalse(connect()->prepare("update $table set "
        . implode(',', array_map(fn($col) => "$col = :$col", array_keys($args)))
        . where_clause(BoolOperator::AND , array_keys($key_args))
        . ($returning ? 'returning ' . implode(',', array_map(quote_identifier(...), $returning)) : '')));

    bind_values($stmt, $args);
    bind_values($stmt, $key_args);
    return $stmt;
}

function delete_from(string $table, array $key_args): PDOStatement
{
    $stmt = notfalse(connect()->prepare("delete from $table " . where_clause(BoolOperator::AND , array_keys($key_args))));
    bind_values($stmt, $key_args);
    return $stmt;
}

/**
 * Binds types values to a statement.
 *
 * @param PDOStatement $stmt The statement on which to bind values.
 * @param array<int|string,array{mixed, int}> $args An associative array mapping from the parameter name to a tuple of the parameter value and the PDO type (e.g. a PDO::PARAM_* constant value)
 */
function bind_values(PDOStatement $stmt, array $args)
{
    foreach ($args as $name => [$value, $type]) {
        notfalse($stmt->bindValue($name, $value, $type));
    }
}

/**
 * Retire les arguments `null` avant le `bind_values` pour pouvoir utiliser la valeur par défaut de la colonne dans les INSERT.
 * @param array $array
 * @return array
 */
function filter_null_args(array $array): array
{
    return array_filter($array, fn($e) => $e[0] !== null);
}

final class LogPDO extends PDO
{
    private int $query_no = 1;

    function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {

        error_log("LogPDO ({$this->query_no}) query: '$query'");
        ++$this->query_no;
        return parent::query($query, $fetchMode, $fetchModeArgs);
    }

    function prepare(string $query, array $options = []): PDOStatement|false
    {
        error_log("LogPDO ({$this->query_no}) prepare: '$query'");
        ++$this->query_no;
        return parent::prepare($query, $options);
    }
}
