<?php
namespace Kcrf;

require_once 'DB/db.php';

use DB;
use PDO;

final class CompteData
{
    public function __construct(
        public string  $email,
        public string  $mdp_hash,
        public string  $nom,
        public string  $prenom,
        public string  $telephone,
        public ?string $adresse,
        public ?DB\Uuid   $api_key,
        public ?string $otp_secret,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            $row->email,
            $row->mdp_hash,
            $row->nom,
            $row->prenom,
            $row->telephone,
            $row->adresse,
            $row->api_key,
            $row->otp_secret,
        );
    }
}

class CompteFast
{
    protected function __construct(
        public readonly int       $id,
        public CompteData $data,
    )
    {
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Compte->value . ' where id=?'));
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return new self($row->id, CompteData::parse($row));
    }
}
