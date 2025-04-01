<?php
namespace Kcrf;

use DB\Arg;
use DB;
use PDO;

require_once 'Kcrf/OffreFast.php';

final class OffreActiviteData
{
    function __construct(
        public string $indication_duree,
        public int $age_requis,
        public string $prestations_incluses,
        public ?string $prestations_non_incluses,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            $row->indication_duree,
            $row->age_requis,
            $row->prestations_incluses,
            $row->prestations_non_incluses,
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'indication_duree' => new Arg($this->indication_duree),
            'age_requis' => new Arg($this->age_requis, PDO::PARAM_INT),
            'prestations_incluses' => new Arg($this->prestations_incluses),
            'prestations_non_incluses' => new Arg($this->prestations_non_incluses),
        ];
    }
}

final class OffreActivite extends OffreFast
{
    private function __construct(
        int $id,
        OffreData $data,
        OffreComputed $computed,

        public OffreActiviteData $activite_data,
    )
    {
        parent::__construct($id, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_activite(OffreData $data, OffreActiviteData $activite_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Activite,
            $activite_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row), $activite_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Activite->value . ' where id=?');
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
            OffreActiviteData::parse($row),
        );
    }

}
