<?php
require_once 'db.php';
require_once 'model/CommuneFast.php';

final class AdresseFast
{
    function __construct(
        public int $id,
        public int $code_commune,
        public string $numero_departement,
        public ?int $numero_voie,
        public ?string $complement_numero,
        public ?string $nom_voie,
        public ?string $localite,
        public ?string $precision_int,
        public ?string $precision_ext,
        public ?float $lat,
        public ?float $long,
    ) {}

    /**
     * @var array<int, self>
     */
    private static $cache = [];

    /**
     * Récupère une adresse de la BDD.
     * @param int $id L'ID de l'adrese.
     * @return self
     */
    static function get(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];
        $stmt = self::select('where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row                     = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$id] = $row === false ? false : new self(
            $row->id,
            $row->code_commune,
            $row->numero_departement,
            $row->numero_voie,
            $row->complement_numero,
            $row->nom_voie,
            $row->localite,
            $row->precision_int,
            $row->precision_ext,
            $row->lat,
            $row->long,
        );
    }

    private static function select(string $query_rest = '')
    {
        return notfalse(DB\connect()->prepare("select * from _adresse $query_rest"));
    }

    function format(): string
    {
        $commune = CommuneFast::get($this->code_commune, $this->numero_departement);
        return ifnntaws($this->precision_ext, ', ')
            . ifnntaws($this->precision_int, ', ')
            . ifnntaws($this->numero_voie, ' ')
            . ifnntaws($this->complement_numero, ' ')
            . ifnntaws($this->nom_voie, ', ')
            . ifnntaws($this->localite, ', ')
            . ifnntaws($commune->nom, ', ')
            . $commune->code_postaux[0];
    }
}
