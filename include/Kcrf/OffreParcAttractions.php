<?php
namespace Kcrf;

use DB\Arg;
use PDO;
use DB;

require_once 'Kcrf/OffreFast.php';

final class OffreParcAttractionsData
{
    function __construct(
        public ?int $age_requis,
        public int $nb_attractions,
        public int $id_image_plan,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            $row->age_requis,
            $row->nb_attractions,
            $row->id_image_plan,
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'age_requis' => new Arg($this->age_requis, PDO::PARAM_INT),
            'nb_attractions' => new Arg($this->nb_attractions, PDO::PARAM_INT),
            'id_image_plan' => new Arg($this->id_image_plan, PDO::PARAM_INT),
        ];
    }
}

/**
 * @inheritDoc
 */
final class OffreParcAttractions extends OffreFast
{
    function __construct(
        int $id,
        OffreData $data,
        OffreComputed $computed,

        public OffreParcAttractionsData $parc_attractions_data,

    ) {
        parent::__construct($id, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_parc_attractions(OffreData $data, OffreParcAttractionsData $parc_attractions_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::ParcAttractions,
            $parc_attractions_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row), $parc_attractions_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::ParcAttractions->value . ' where id=?');
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
            OffreParcAttractionsData::parse($row),
        );
    }
}
