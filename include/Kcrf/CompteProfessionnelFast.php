<?php
require_once 'db.php';
require_once 'ValueObjects/Uuid.php';

enum Secteur: string
{
    case Public = 'public';
    case Prive = 'privé';
}

final class CompteProfessionnelData
{
    function __construct(
        public string  $denomination,
        public Secteur $secteur,
    )
    {
    }
}

final class CompteProfessionnelFast
{
    private function __construct(
        public readonly ?int                    $id,
        public CompteData              $data,
        public CompteProfessionnelData $professionnel_data,
    )
    {
    }

    /**
     * @var array<int,self|false>
     */
    private static array $cache = [];

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\connect()->prepare('select * from ' . DB\Table::Professionnel->value . ' where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());

        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            new CompteData(
            $row->email,
            $row->mdp_hash,
            $row->nom,
            $row->prenom,
            $row->telephone,
            $row->adresse,
            Uuid::parse($row->api_key),
            $row->otp_secret,
            ),
            new CompteProfessionnelData(
            $row->denomination,
            $row->secteur,
            )
        );
    }
}
