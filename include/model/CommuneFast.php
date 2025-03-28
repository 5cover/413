<?php
require_once 'db.php';

/**
 * @property-read string[] $code_postaux
 */
final class CommuneFast
{
    function __get(string $name)
    {
        return match ($name) {
            'code_postaux' => $this->code_postaux(),
        };
    }

    function __construct(
        public int $code,
        public string $numero_departement,
        public string $nom,
    ) {}

    /**
     * Summary of cache
     * @var array<int, self|false>
     */
    private static array $cache = [];

    /**
     * Récupère une commune de la BDD.
     */
    static function from_db(int $code, string $numero_departement): self|false
    {
        $key = self::get_key($code, $numero_departement);
        if (isset($cache[$key])) return self::$cache[$key];
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . ' where code=? and numero_departement=?'));
        DB\bind_values($stmt, [
            1 => [$code, PDO::PARAM_INT],
            2 => [$numero_departement, PDO::PARAM_STR],
        ]);
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $cache[$key] = $row === false ? false : new self(
            $row->code,
            $row->numero_departement,
            $row->nom,
        );
    }

    static function from_db_by_nom(string $nom): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select code, numero_departement from ' . self::TABLE . ' where nom = ?'));
        DB\bind_values($stmt, [1 => [$nom, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row === false) return false;
        $nodept = ltrim($row->numero_departement);
        return $cache[self::get_key($row->code, $nodept)] = new self($row->code, $nodept, $nom);
    }

    private ?array $code_postaux = null;

    private function code_postaux(): array
    {
        $stmt = notfalse(DB\connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
        DB\bind_values($stmt, [
            1 => [$this->code, PDO::PARAM_INT],
            2 => [$this->numero_departement, PDO::PARAM_STR],
        ]);
        notfalse($stmt->execute());
        return $this->code_postaux ??= $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private const TABLE = '_commune';

    private static function get_key(int $code, string $numero_departement): string {
        return "$code\0$numero_departement";
    }
}
