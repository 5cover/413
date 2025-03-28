<?php
require_once 'db.php';
require_once 'model/CommuneFast.php';

final class AdresseData
{
    function __construct(
        public int     $code_commune,
        public string  $numero_departement,
        public ?int    $numero_voie,
        public ?string $complement_numero,
        public ?string $nom_voie,
        public ?string $localite,
        public ?string $precision_int,
        public ?string $precision_ext,
        public ?float  $lat,
        public ?float  $long,
    ) { }

    function format(): string
    {
        $commune = CommuneFast::from_db($this->code_commune, $this->numero_departement);
        return itaws($this->precision_ext, ', ')
            . itaws($this->precision_int, ', ')
            . itaws($this->numero_voie, ' ')
            . itaws($this->complement_numero, ' ')
            . itaws($this->nom_voie, ', ')
            . itaws($this->localite, ', ')
            . itaws($commune->nom, ', ')
            . $commune->code_postaux[0];
    }

    function to_args(): array {
        return [
            'code_commune' => $this->code_commune,
            'numero_departement' => $this->numero_departement,
            'numero_voie' => $this->numero_voie,
            'complement_numero' => $this->complement_numero,
            'nom_voie' => $this->nom_voie,
            'localite' => $this->localite,
            'precision_int' => $this->precision_int,
            'precision_ext' => $this->precision_ext,
            'lat' => $this->lat,
            'long' => $this->long,
        ];
    }
}

final class AdresseFast
{
        private function __construct(
        public readonly int         $id,
        public AdresseData $data,
    )
    {
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    function insert(AdresseData $data): AdresseFast {
        $stmt = DB\insert_into(DB\Table::Adresse, $data->to_args(), ['id']);
        notfalse($stmt->execute());
        $id = $stmt->fetchColumn();
        return self::$cache[$id] = new AdresseFast($id, $data);
    }

    /**
     * Récupère une adresse de la BDD.
     * @param int $id L'ID de l'adrese.
     */
    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Adresse->value . ' where id=?'));
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());

        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$id] = $row === false ? false : new self(
            $row->id,
            new AdresseData(
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
            )
        );
    }
}
