<?php
require_once 'db.php';
require_once 'model/Uuid.php';

enum Secteur {
    case Public = 'public';
    case Prive = 'privé';
}

class ProfessionnelFast extends CompteFast
{
    function __construct(
        ?int $id,
        string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        ?string $adresse,
        ?Uuid $api_key,
        ?string $otp_secret,
        public string $denomination,
        public Secteur $secteur,
    ) {
        parent::__construct(
            $id,
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse,
            $api_key,
            $otp_secret,
        );
    }


    /**
     * @var array<int, self>
     */
    private static array $cache;

    static function get(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = self::select('where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row                    = $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row === false
            ? self::$cache[$id] = false
            : self::from_db_row($row);
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            $row->email,
            $row->mdp_hash,
            $row->nom,
            $row->prenom,
            $row->telephone,
            $row->adresse,
            Uuid::parse($row->api_key),
            $row->otp_secret,
            $row->denomination,
            $row->secteur,
        );
    }

    private static function select(string $query_rest = '')
    {
        return notfalse(DB\connect()->prepare("select * from professionnel $query_rest"));
    }
}
