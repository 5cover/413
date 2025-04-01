<?php
namespace Kcrf;

use DB\Arg;
use PDO;
use DB;

require_once 'Kcrf/OffreFast.php';

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

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'carte' => new Arg($this->carte),
            'richesse' => new Arg($this->richesse, PDO::PARAM_INT),
            'sert_petit_dejeuner' => new Arg($this->sert_petit_dejeuner, PDO::PARAM_BOOL),
            'sert_brunch' => new Arg($this->sert_brunch, PDO::PARAM_BOOL),
            'sert_dejeuner' => new Arg($this->sert_dejeuner, PDO::PARAM_BOOL),
            'sert_diner' => new Arg($this->sert_diner, PDO::PARAM_BOOL),
            'sert_boissons' => new Arg($this->sert_boissons, PDO::PARAM_BOOL),
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
        OffreData $data,
        OffreComputed $computed,

        public OffreRestaurantData $restaurant_data,

    ) {
        parent::__construct($id, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_restaurant(OffreData $data, OffreRestaurantData $restaurant_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Restaurant,
            $restaurant_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row), $restaurant_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Restaurant->value . ' where id=?');
        notfalse($stmt->execute());
        $row = $stmt->fetch();

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreData::parse($row),
            OffreComputed::parse($row),
            OffreRestaurantData::parse($row),
        );
    }
}
