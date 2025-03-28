<?php
require_once 'db.php';

final class CompteData
{
    public function __construct(
        public string  $email,
        public string  $mdp_hash,
        public string  $nom,
        public string  $prenom,
        public string  $telephone,
        public ?string $adresse,
        public ?Uuid   $api_key,
        public ?string $otp_secret,
    )
    {
    }
}

final class CompteFast
{
    private function __construct(
        public readonly ?int       $id,
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
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return new self($row->id, new CompteData(
            $row->email,
            $row->mdp_hash,
            $row->nom,
            $row->prenom,
            $row->telephone,
            $row->adresse,
            $row->api_key,
            $row->otp_secret,
        ));
    }
}
