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
     * Récupère une commune de la BDD.
     */
    static function get(int $code, string $numero_departement): self|false
    {
        static $cache = [];
        $key          = "$code\0$numero_departement";
        if (isset($cache[$key])) return $cache[$key];
        $stmt = self::select('where code=? and numero_departement=?');
        DB\bind_values($stmt, [
            1 => [$code, PDO::PARAM_INT],
            2 => [$numero_departement, PDO::PARAM_STR],
        ]);
        notfalse($stmt->execute());
        $row                = $stmt->fetch(PDO::FETCH_OBJ);
        return $cache[$key] = $row === false ? false : new self(
            $row->code,
            $row->numero_departement,
            $row->nom,
        );
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

    private static function select(string $query_rest = '')
    {
        return notfalse(DB\connect()->prepare("select * from _commune $query_rest"));
    }
}
