<?php
require_once 'model/Model.php';
require_once 'db.php';

// not abstract so we don't have to figure out which concrete class an id belongs, we don't need to anyway.
class Signalable extends Model
{
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected function __construct(
        protected ?int $id
    ) {
    }

    static function from_db(int $id_signalable)
    {
        return new self($id_signalable);
    }

    function get_signalement(int $id_compte): ?string
    {
        $stmt = DB\connect()->prepare('select raison from ' . self::TABLE . ' where id_signalable=? and id_compte=?');
        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT], 2 => [$id_compte, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $r = $stmt->fetchColumn();
        return $r === false ? null : $r;
    }

    function signaler(int $id_compte, string $raison)
    {
        $stmt = DB\connect()->prepare('insert into ' . self::TABLE . ' (id_signalable,id_compte,raison) values (?,?,?)');
        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT], 2 => [$id_compte, PDO::PARAM_INT], 3 => [$raison, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
    }

    const TABLE = '_signalement';
}
