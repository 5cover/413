<?php

use DB\Arg;

require_once 'model/OffreFast.php';

final class OffreSpectacleData {
    function __construct(
        public Interval $indication_duree,
        public int      $capacite_accueil,
    )
    {
    }

    static function parse(object $row): self
    {
        return new self(
            Interval::parse($row->indication_duree),
            $row->capacite_accueil,
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array
    {
        return [
            'indication_duree' => new Arg($this->indication_duree),
            'capacite_accueil' => new Arg($this->capacite_accueil, PDO::PARAM_INT),
        ];
    }
}

/**
 * @inheritDoc
 */
final class OffreSpectacle extends OffreFast
{
    function __construct(
        int $id,
        OffreData $data,
        OffreComputed $computed,

        public OffreSpectacleData $spectacle_data,

    ) {
        parent::__construct($id, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_spectacle(OffreData $data, OffreSpectacleData $spectacle_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Spectacle,
            $spectacle_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row), $spectacle_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Spectacle->value . ' where id=?');
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreData::parse($row),
            OffreComputed::parse($row),
            OffreSpectacleData::parse($row),
        );
    }
}
