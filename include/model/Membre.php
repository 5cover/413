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
        ' . static::TABLE . '.id_adresse,
        ' . static::TABLE . '.pseudo,

        a.code_commune adresse_code_commune,
        a.numero_departement adresse_numero_departement,
        c.nom adresse_commune_nom,
        a.numero_voie adresse_numero_voie,
        a.complement_numero adresse_complement_numero,
        a.nom_voie adresse_nom_voie,
        a.localite adresse_localite,
        a.precision_int adresse_precision_int,
        a.precision_ext adresse_precision_ext,
        a.latitude adresse_latitude,
        a.longitude adresse_longitude

        from membre
            join _adresse a on a.id = ' . static::TABLE . '.id_adresse
            join _commune c on c.code = a.code_commune and c.numero_departement = a.numero_departement';
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
            new Adresse(
                $row['id_adresse'],
                new Commune(
                    $row['adresse_code_commune'],
                    $row['adresse_numero_departement'],
                    $row['adresse_commune_nom'],
                ),
                $row['adresse_numero_voie'],
                $row['adresse_complement_numero'],
                $row['adresse_nom_voie'],
                $row['adresse_localite'],
                $row['adresse_precision_int'],
                $row['adresse_precision_ext'],
                $row['adresse_latitude'],
                $row['adresse_longitude'],
            ),
        ], $row['pseudo']);
    }

    const TABLE = 'membre';
}
