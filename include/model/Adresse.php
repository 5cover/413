<?php

require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Model.php';

/**
 * @property-read ?int $id L'ID. `null` si cette adresse n'existe pas dans la BDD.
 */
final class Adresse extends Model
{
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected static function fields()
    {
        return [
            'code_commune'       => [fn($x) => $x->code, 'commune', PDO::PARAM_INT],
            'numero_departement' => [fn($x) => $x->numero_departement, 'commune', PDO::PARAM_STR],
            'numero_voie'        => [null, 'numero_voie', PDO::PARAM_STR],
            'complement_numero'  => [null, 'complement_numero', PDO::PARAM_STR],
            'nom_voie'           => [null, 'nom_voie', PDO::PARAM_STR],
            'localite'           => [null, 'localite', PDO::PARAM_STR],
            'precision_int'      => [null, 'precision_int', PDO::PARAM_STR],
            'precision_ext'      => [null, 'precision_ext', PDO::PARAM_STR],
            'lat'                => [null, 'lat', PDO::PARAM_STR],
            'long'               => [null, 'long', PDO::PARAM_STR],
        ];
    }

    function __construct(
        protected ?int $id,
        public Commune $commune,
        public ?int $numero_voie          = null,
        public ?string $complement_numero = null,
        public ?string $nom_voie          = null,
        public ?string $localite          = null,
        public ?string $precision_int     = null,
        public ?string $precision_ext     = null,
        public ?float $lat                = null,
        public ?float $long               = null,
    ) {}

    /**
     * Récupère une adresse depuis la base de données.
     * @param int $id_adresse L'ID de l'adresse à récupérer.
     * @return self Une adresse existante dans la base de donneées, ou `false` si il n'existe pas d'adresse d'ID $id_adresse.
     */
    static function from_db(int $id_adresse): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select
            ' . static::TABLE . '.numero_voie,
            ' . static::TABLE . '.complement_numero,
            ' . static::TABLE . '.nom_voie,
            ' . static::TABLE . '.localite,
            ' . static::TABLE . '.precision_int,
            ' . static::TABLE . '.precision_ext,
            ' . static::TABLE . '.lat,
            ' . static::TABLE . '.long,
            ' . static::TABLE . '.numero_departement,
            ' . static::TABLE . '.code_commune,
            c.nom from '
            . self::TABLE . '  inner join ' . Commune::TABLE
            . ' c on c.numero_departement = ' . static::TABLE . '.numero_departement and c.code = ' . static::TABLE . '.code_commune'
            . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new self(
            $id_adresse,
            new Commune($row['code_commune'], $row['numero_departement'], $row['nom']),
            $row['numero_voie'],
            $row['complement_numero'],
            $row['nom_voie'],
            $row['localite'],
            $row['precision_int'],
            $row['precision_ext'],
            $row['lat'],
            $row['long'],
        );
    }

    function format(): string
    {
        return ifnntaws($this->precision_ext, ', ')
            . ifnntaws($this->precision_int, ', ')
            . ifnntaws($this->numero_voie, ' ')
            . ifnntaws($this->complement_numero, ' ')
            . ifnntaws($this->nom_voie, ', ')
            . ifnntaws($this->localite, ', ')
            . ifnntaws($this->commune->nom, ', ')
            . $this->commune->code_postaux()[0];
    }

    const TABLE = '_adresse';
}
