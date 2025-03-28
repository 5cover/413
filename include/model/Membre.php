<?php

require_once 'model/Compte.php';

/**
 * @inheritDoc
 */
final class Membre extends Compte
{
    protected static function fields()
    {
        return parent::fields() + [
            'pseudo' => [null, 'pseudo', PDO::PARAM_STR],
        ];
    }

    function __construct(
        array $args_compte,
        public string $pseudo,
    ) {
        parent::__construct(...$args_compte);
    }

    /**
     * Récupère un membre de la BDD par son pseudo.
     * @param string $pseudo
     * @return self|false
     */
    static function from_db_by_pseudo(string $pseudo): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.pseudo = ?'));
        DB\bind_values($stmt, [1 => [$pseudo, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    protected static function make_select(): string
    {
        return 'select
        ' . static::TABLE . '.id,
        ' . static::TABLE . '.email,
        ' . static::TABLE . '.mdp_hash,
        ' . static::TABLE . '.nom,
        ' . static::TABLE . '.prenom,
        ' . static::TABLE . '.telephone,
        ' . static::TABLE . '.adresse,
        ' . static::TABLE . '.pseudo,
        ' . static::TABLE . '.otp_secret
        from membre';
    }

    protected static function from_db_row(array $row): self
    {
        return new self([
            $row['id'],
            $row['email'],
            $row['mdp_hash'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            $row['adresse'],
            Uuid::parse($row['api_key'] ?? null),
            $row['otp_secret'],
        ], $row['pseudo']);
    }

    const TABLE = 'membre';
}
