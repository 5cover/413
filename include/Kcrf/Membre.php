<?php
namespace Kcrf;

require_once 'Kcrf/CompteFast.php';

use DB;
use PDO;

final class CompteMembreData
{
    function __construct(
        string $pseudo,
    ) {}

    static function parse(object $row): self {
        return new self(
            $row->pseudo,
        );
    }
}

final class CompteMembre extends CompteFast
{
    private const TABLE = DB\Table::Membre;

    function __construct(
        int $id,
        CompteData $data,

        public CompteMembreData $membre_data,
    ) {
        parent::__construct($id, $data);
    }

    /**
     * Récupère un membre de la BDD par son pseudo.
     * @param string $pseudo
     * @return self|false
     */
    static function from_db_by_pseudo(string $pseudo): self|false
    {
        $stmt = notfalse(DB\connect()->prepare('select * where ' . self::TABLE->value . '.pseudo = ?'));
        DB\bind_values($stmt, [1 => [$pseudo, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }


    protected static function from_db_row(object $row): self
    {
        return new self(
            $row->id,
            CompteData::parse($row),
            CompteMembreData::parse($row),
        );
    }
}
