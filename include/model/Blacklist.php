<?php
require_once 'model/Model.php';
require_once 'db.php';
require_once 'redirect.php';

// not abstract so we don't have to figure out which concrete class an id belongs, we don't need to anyway.
class Blacklist extends Model
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

    static function get_blacklist(int $id): ?string
    {
        $stmt = DB\connect()->prepare('select fin_blacklist from ' . self::TABLE . ' where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $r = $stmt->fetchColumn();
        return $r === false ? null : $r;
    }

    static function toggle_blacklist(int $id, date $finblacklist): bool
    {
        if (self::get_blacklist($id) === null) {
            $stmt = DB\connect()->prepare('insert into ' . self::TABLE . ' (id,fin_blacklist) values (?,?)');
            DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT], 2 => [$finblacklist, PDO::PARAM_STR]]);
        }
        return $stmt->execute();
    }

    const TABLE = '_blacklist';
}