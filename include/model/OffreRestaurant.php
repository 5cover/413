<?php
require_once 'model/OffreFast.php';

final class OffreRestaurantData
{
    function __construct(
        public string $carte,
        public int $richesse,
        public bool $sert_petit_dejeuner,
        public bool $sert_brunch,
        public bool $sert_dejeuner,
        public bool $sert_diner,
        public bool $sert_boissons,
    )
    {
    }

    static function parse(object $row):self {
        return new self(
            $row->carte,
            $row->richesse,
            $row->sert_petit_dejeuner,
            $row->sert_brunch,
            $row->sert_dejeuner,
            $row->sert_diner,
            $row->sert_boissons,
        );
    }

    function to_args(): array {
        return [
            'carte' => $this->carte,
            'richesse' => $this->richesse,
            'sert_petit_dejeuner' => $this->sert_petit_dejeuner,
            'sert_brunch' => $this->sert_brunch,
            'sert_dejeuner' => $this->sert_dejeuner,
            'sert_diner' => $this->sert_diner,
            'sert_boissons' => $this->sert_boissons,
        ];
    }
}

/**
 * @inheritDoc
 */
final class OffreRestaurant extends OffreFast
{
    function __construct(
        int $id,
        OffreRefs $refs,
        OffreData $data,
        OffreComputed $computed,

        public OffreRestaurantData $restaurant_data,

    ) {
        parent::__construct($id, $refs, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_restaurant(OffreData $data, OffreRefs $refs, OffreRestaurantData $restaurant_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Restaurant,
            $restaurant_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$row->id] = new self($row->id, $refs, $data, OffreComputed::parse($row), $restaurant_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Restaurant->value . ' where id=?');
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreRefs::parse($row),
            OffreData::parse($row),
            OffreComputed::parse($row),
            OffreRestaurantData::parse($row),
        );
    }
}
