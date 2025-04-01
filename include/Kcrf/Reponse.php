<?php

require_once 'model/Signalable.php';

/**
 * @inheritDoc
 */
final class Reponse extends Signalable
{
    protected static function fields()
    {
        return [
            'id_avis' => [null, 'id_avis', PDO::PARAM_INT],
            'contenu' => [null, 'contenu', PDO::PARAM_STR],
        ];
    }

    function __construct(
        ?int $id,
        public int $id_avis,
        public string $contenu,
    ) {
        parent::__construct($id);
    }

    static function from_db_by_avis(int $id_avis): ?Reponse {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where id_avis=?'));
        DB\bind_values($stmt, [1 => [$id_avis, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? null : self::from_db_row($row);
    }

    private static function make_select(): string
    {
        return 'select * from ' . self::TABLE;
    }

    private static function from_db_row(array $row): Reponse {
        return new Reponse(
            $row['id'],
            $row['id_avis'],
            $row['contenu'],
        );
    }

    const TABLE = 'reponse';
}