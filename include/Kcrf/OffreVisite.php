<?php
namespace Kcrf;

use DB\Arg;
use DB;

require_once 'Kcrf/OffreFast.php';

final class OffreVisiteData
{
    function __construct(
        public DB\Interval $indication_duree,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            Db\Interval::parse($row->indication_duree),
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'indication_duree' => new Arg($this->indication_duree),
        ];
    }
}

/**
 * @inheritDoc
 */
final class OffreVisite extends OffreFast
{
    function __construct(
        int $id,
        OffreData $data,
        OffreComputed $computed,

        public OffreVisiteData $visite_data,

    ) {
        parent::__construct($id, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_visite(OffreData $data, OffreVisiteData $visite_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Visite,
            $visite_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row), $visite_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Visite->value . ' where id=?');
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
            OffreVisiteData::parse($row),
        );
    }
}
