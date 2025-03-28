<?php

require_once 'model/OffreFast.php';

final class OffreVisiteData
{
    function __construct(
        public Duree $indication_duree,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            Duree::parse($row->indication_duree),
        );
    }

    function to_args(): array {
        return [
            'indication_duree' => $this->indication_duree,
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
        OffreRefs $refs,
        OffreData $data,
        OffreComputed $computed,

        public OffreVisiteData $visite_data,

    ) {
        parent::__construct($id, $refs, $data, $computed);
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert_visite(OffreData $data, OffreRefs $refs, OffreVisiteData $visite_data): self
    {
        $stmt = DB\insert_into(
            DB\Table::Visite,
            $visite_data->to_args() + $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$row->id] = new self($row->id, $refs, $data, OffreComputed::parse($row), $visite_data);
    }

    static function from_db(int $id): self
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' .  DB\Table::Visite->value . ' where id=?');
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
            OffreVisiteData::parse($row),
        );
    }
}
