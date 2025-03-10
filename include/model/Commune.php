<?php

final class Commune
{
    function __construct(
        readonly int $code,
        readonly string $numero_departement,
        readonly string $nom,
    ) {}

    static function from_db_by_nom(string $nom): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select code, numero_departement from ' . self::TABLE . ' where nom = ?'));
        DB\bind_values($stmt, [1 => [$nom, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new self($row['code'], ltrim($row['numero_departement']), $nom);
    }

    static function from_db(int $code, string $numero_departement): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select nom from ' . self::TABLE . ' where code = ? and numero_departement = ?'));
        DB\bind_values($stmt, [1 => [$code, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $nom = $stmt->fetchColumn();
        return $nom === false ? false : new self($code, $numero_departement, $nom);
    }

    /**
     * Retourne les code postaux de cette commune.
     * @return string[] Les codes postaux de cette commune.
     */
    function code_postaux(): array
    {
        // todo: cache this result
        $stmt = notfalse(DB\connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
        DB\bind_values($stmt, [1 => [$this->code, PDO::PARAM_INT], 2 => [$this->numero_departement, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        return array_column($stmt->fetchAll(), 'code_postal');
    }

    const TABLE = '_commune';
}
