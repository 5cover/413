<?php
require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Signalable.php';
require_once 'model/Adresse.php';
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
            'id_adresse' => [fn($x) => $x->id, 'adresse', PDO::PARAM_STR],
            'api_key' => [Uuid::parse(...), 'api_key', PDO::PARAM_STR],
        ];
    }

    function __construct(
        protected ?int $id,
        public string $email,
        public string $mdp_hash,
        public string $nom,
        public string $prenom,
        public string $telephone,
        public Adresse $adresse,
        public ?Uuid $api_key = null,
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
        ' . static::TABLE . '.id_adresse,
        ' . static::TABLE . '.api_key,

        professionnel.denomination professionnel_denomination,
        professionnel.secteur professionnel_secteur,
        
        _prive.siren prive_siren,

        _membre.pseudo membre_pseudo,

        a.code_commune adresse_code_commune,
        a.numero_departement adresse_numero_departement,
        o.nom adresse_commune_nom,
        a.numero_voie adresse_numero_voie,
        a.complement_numero adresse_complement_numero,
        a.nom_voie adresse_nom_voie,
        a.localite adresse_localite,
        a.precision_int adresse_precision_int,
        a.precision_ext adresse_precision_ext,
        a.latitude adresse_latitude,
        a.longitude adresse_longitude

        from ' . self::TABLE . '
            left join professionnel using (id)
            left join _prive using (id)
            left join _membre using (id)
            join _adresse a on a.id = ' . static::TABLE . '.id_adresse
            join _commune o on o.code = a.code_commune and o.numero_departement = a.numero_departement';
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
            new Adresse(
                $row['id_adresse'],
                new Commune(
                    $row['adresse_code_commune'],
                    $row['adresse_numero_departement'],
                    $row['adresse_commune_nom'],
                ),
                $row['adresse_numero_voie'],
                $row['adresse_complement_numero'],
                $row['adresse_nom_voie'],
                $row['adresse_localite'],
                $row['adresse_precision_int'],
                $row['adresse_precision_ext'],
                $row['adresse_latitude'],
                $row['adresse_longitude'],
            ),
            Uuid::parse($row['api_key'] ?? null),
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
