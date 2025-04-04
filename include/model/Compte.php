<?php
require_once 'util.php';
require_once 'model/Adresse.php';
require_once 'model/Commune.php';
require_once 'model/Signalable.php';
require_once 'model/Uuid.php';

/**
 * Un compte
 * @inheritDoc
 */
abstract class Compte extends Signalable
{
    protected static function fields()
    {
        return [
            'email' => [null, 'email', PDO::PARAM_STR],
            'mdp_hash' => [null, 'mdp_hash', PDO::PARAM_STR],
            'nom' => [null, 'nom', PDO::PARAM_STR],
            'prenom' => [null, 'prenom', PDO::PARAM_STR],
            'telephone' => [null, 'telephone', PDO::PARAM_STR],
            'adresse' => [null, 'adresse', PDO::PARAM_STR],
            'api_key' => [Uuid::parse(...), 'api_key', PDO::PARAM_STR],
            'otp_secret'=> [null, 'otp_secret', PDO::PARAM_STR],
        ];
    }

    function __construct(
        protected ?int $id,
        public string $email,
        public string $mdp_hash,
        public string $nom,
        public string $prenom,
        public string $telephone,
        public ?string $adresse,
        public ?Uuid $api_key = null,
        public ?string $otp_secret = null,

    ) {
        parent::__construct($id);
    }

    static function from_db(int $id_compte): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(static::make_select() . ' where ' . static::TABLE . '.id = ?'));
        DB\bind_values($stmt, [1 => [$id_compte, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : static::from_db_row($row);
    }

    static function from_db_by_email(string $email): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(static::make_select() . ' where ' . static::TABLE . '.email = ?'));
        notfalse($stmt->execute([$email]));
        $row = $stmt->fetch();
        return $row === false ? false : static::from_db_row($row);
    }

    protected static function make_select(): string
    {
        return 'select
        ' . static::TABLE . '.id,
        ' . static::TABLE . '.email,
        ' . static::TABLE . '.mdp_hash,
        ' . static::TABLE . '.nom,
        ' . static::TABLE . '.prenom,
        ' . static::TABLE . '.telephone,
        ' . static::TABLE . '.adresse,
        ' . static::TABLE . '.api_key,
        ' . static::TABLE . '.otp_secret,

        professionnel.denomination professionnel_denomination,
        professionnel.secteur professionnel_secteur,
        
        _prive.siren prive_siren,

        _membre.pseudo membre_pseudo

        from ' . self::TABLE . '
            left join professionnel using (id)
            left join _prive using (id)
            left join _membre using (id)';
    }

    protected static function from_db_row(array $row): self
    {
        self::require_subclasses();
        $args_compte = [
            $row['id'],
            $row['email'],
            $row['mdp_hash'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            $row['adresse'],
            Uuid::parse($row['api_key'] ?? null),
            $row['otp_secret'],
        ];
        if ($denomination = $row['professionnel_denomination'] ?? null) {
            $secteur = $row['professionnel_secteur'];
            $args_profesionnel = [
                $denomination,
                $secteur,
            ];
            return match ($secteur) {
                'public' => new ProfessionnelPublic(
                    $args_compte,
                    $args_profesionnel,
                ),
                'privé' => new ProfessionnelPrive(
                    $args_compte,
                    $args_profesionnel,
                    $row['prive_siren'],
                ),
            };
        } else if ($pseudo = $row['membre_pseudo'] ?? null) {
            return new Membre(
                $args_compte,
                $pseudo,
            );
        }
        throw new LogicException('pas de sous-classe correspondante');
    }

    private static function require_subclasses(): void
    {
        require_once 'model/Professionnel.php';
        require_once 'model/ProfessionnelPrive.php';
        require_once 'model/ProfessionnelPublic.php';
        require_once 'model/Membre.php';
    }

    const TABLE = '_compte';
}
