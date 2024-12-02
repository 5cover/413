<?php

require_once 'db.php';
require_once 'model/Abonnement.php';
require_once 'model/Adresse.php';
require_once 'model/Avis.php';
require_once 'model/Duree.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/Galerie.php';
require_once 'model/Image.php';
require_once 'model/Model.php';
require_once 'model/MultiRange.php';
require_once 'model/OuvertureHebdomadaire.php';
require_once 'model/Professionnel.php';
require_once 'model/Signalable.php';
require_once 'model/Tags.php';
require_once 'model/Tarifs.php';

/**
 * Une offre touristique.
 * @property-read ?int $id L'ID. `null` si cette offre n'existe pas dans la BDD.
 *
 * @property string $titre
 * @property string $resume
 * @property string $description_detaillee
 * @property ?string $url_site_web
 * @property MultiRange<FiniteTimestamp> $periodes_ouverture
 * @property ?FiniteTimestamp $modifiee_le Jamais null si cette offre existe dans la BDD.
 * @property Adresse $adresse
 * @property Image $image_principale
 * @property Professionnel $professionnel
 * @property Abonnement $abonnement
 *
 * @property-read int $nb_avis Le nombre d'avis ce cette offre. 0 si elle n'existe pas dans la BDD. Calculé.
 * @property-read bool $en_ligne Calculé.
 * @property-read float $note_moyenne Calculé.
 * @property-read ?float $prix_min Calculé.
 * @property-read FiniteTimestamp $creee_le Calculé.
 * @property-read Duree $en_ligne_ce_mois_pendant Calculé.
 * @property-read ?FiniteTimestamp $changement_ouverture_suivant_le Calculé.
 * @property-read bool $est_ouverte Calculé.
 */
abstract class Offre extends Model implements Signalable
{
    protected const FIELDS = [
        'titre'                 => [null,      'titre',                 PDO::PARAM_STR],
        'resume'                => [null,      'resume',                PDO::PARAM_STR],
        'description_detaillee' => [null,      'description_detaillee', PDO::PARAM_STR],
        'modifiee_le'           => [null,      'modifiee_le',           PDO::PARAM_STR],
        'url_site_web'          => [null,      'url_site_web',          PDO::PARAM_STR],
        'periodes_ouverture'    => [null,      'periodes_ouverture',    PDO::PARAM_STR],
        'adresse'               => ['id',      'id_adresse',            PDO::PARAM_INT],
        'image_principale'      => ['id',      'id_image_principale',   PDO::PARAM_INT],
        'professionnel'         => ['id',      'id_professionnel',      PDO::PARAM_INT],
        'abonnement'            => ['libelle', 'libelle_abonnement',    PDO::PARAM_STR],
    ];

    protected const KEY_FIELDS = [
        'id' => ['id', PDO::PARAM_INT],
    ];

    protected const INSERT_FIELDS = [
        'modifiee_le' => ['modifee_le', PDO::PARAM_INT],
    ];

    function __get(string $name): mixed
    {
        return match ($name) {
            'nb_avis' => $this->nb_avis ??= Avis::get_count($this->id),
            default   => parent::__get($name),
        };
    }

    protected ?int $id;
    protected string $titre;
    protected string $resume;
    protected string $description_detaillee;
    protected ?string $url_site_web;
    protected MultiRange $periodes_ouverture;
    protected ?FiniteTimestamp $modifiee_le;
    protected Adresse $adresse;
    protected Image $image_principale;
    protected Professionnel $professionnel;
    protected Abonnement $abonnement;

    readonly Tags $tags;
    readonly Tarifs $tarifs;
    readonly OuvertureHebdomadaire $ouverture_hebdomadaire;
    readonly Galerie $galerie;

    protected ?int $nb_avis;

    protected readonly bool $en_ligne;
    protected readonly float $note_moyenne;
    protected readonly ?float $prix_min;
    protected readonly FiniteTimestamp $creee_le;
    protected readonly Duree $en_ligne_ce_mois_pendant;
    protected readonly ?FiniteTimestamp $changement_ouverture_suivant_le;
    protected readonly bool $est_ouverte;

    /**
     * Construit une nouvelle offre.
     * @param ?int $id
     * @param Adresse $adresse
     * @param Image $image_principale
     * @param Professionnel $professionnel
     * @param Abonnement $abonnement
     * @param string $titre
     * @param string $resume
     * @param string $description_detaillee
     * @param ?string $url_site_web
     * @param MultiRange<FiniteTimestamp> $periodes_ouverture
     * @param ?FiniteTimestamp $modifiee_le
     * @param bool $en_ligne
     * @param ?float $note_moyenne
     * @param ?float $prix_min
     * @param FiniteTimestamp $creee_le
     * @param Duree $en_ligne_ce_mois_pendant
     * @param ?FiniteTimestamp $changement_ouverture_suivant_le
     * @param bool $est_ouverte
     * @param string[] $tags
     * @param array<string, float> $tarifs
     * @param array<int, MultiRange<Time>> $horaires
     * @param Image[] $galerie
     */
    function __construct(
        ?int $id,
        Adresse $adresse,
        Image $image_principale,
        Professionnel $professionnel,
        Abonnement $abonnement,
        string $titre,
        string $resume,
        string $description_detaillee,
        ?string $url_site_web,
        MultiRange $periodes_ouverture,
        ?FiniteTimestamp $modifiee_le,
        bool $en_ligne,
        ?float $note_moyenne,
        ?float $prix_min,
        FiniteTimestamp $creee_le,
        Duree $en_ligne_ce_mois_pendant,
        ?FiniteTimestamp $changement_ouverture_suivant_le,
        bool $est_ouverte,
    ) {
        $this->id                              = $id;
        $this->adresse                         = $adresse;
        $this->image_principale                = $image_principale;
        $this->professionnel                   = $professionnel;
        $this->abonnement                      = $abonnement;
        $this->titre                           = $titre;
        $this->resume                          = $resume;
        $this->description_detaillee           = $description_detaillee;
        $this->url_site_web                    = $url_site_web;
        $this->periodes_ouverture              = $periodes_ouverture;
        $this->modifiee_le                     = $modifiee_le;
        $this->en_ligne                        = $en_ligne;
        $this->note_moyenne                    = $note_moyenne;
        $this->prix_min                        = $prix_min;
        $this->creee_le                        = $creee_le;
        $this->en_ligne_ce_mois_pendant        = $en_ligne_ce_mois_pendant;
        $this->changement_ouverture_suivant_le = $changement_ouverture_suivant_le;
        $this->est_ouverte                     = $est_ouverte;
        $this->tags                            = new Tags($this);
        $this->tarifs                          = new Tarifs($this);
        $this->ouverture_hebdomadaire          = new OuvertureHebdomadaire($this);
        $this->galerie                         = new Galerie($this);
    }

    static function from_db(int $id_offre): Offre|false
    {
        if (static::TABLE !== self::TABLE) {
            $stmt = notfalse(DB\connect()->prepare('select * from ' . static::TABLE . ' where id = ?'));
            DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
            notfalse($stmt->execute());
            $row = $stmt->fetch();
            if ($row === false) return false;
            return static::from_db_row($row);
        }

        require_once 'model/Activite.php';
        require_once 'model/ParcAttractions.php';
        require_once 'model/Restaurant.php';
        require_once 'model/Spectacle.php';
        require_once 'model/Visite.php';
        $stmt = notfalse(DB\connect()->prepare('select offre_categorie(?)'));
        DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return match (notfalse($stmt->fetchColumn())) {
            Activite::CATEGORIE        => Activite::from_db($id_offre),
            ParcAttractions::CATEGORIE => ParcAttractions::from_db($id_offre),
            Restaurant::CATEGORIE      => Restaurant::from_db($id_offre),
            Spectacle::CATEGORIE       => Spectacle::from_db($id_offre),
            Visite::CATEGORIE          => Visite::from_db($id_offre),
        };
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<int, Offre> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une(): Iterator
    {
        if (static::TABLE === self::TABLE) {
            require_once 'model/Activite.php';
            require_once 'model/ParcAttractions.php';
            require_once 'model/Restaurant.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Visite.php';
            foreach (Activite::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        // todo: where temporaire : le temps qu'on fasse marcher les options
        $stmt = notfalse(DB\connect()->prepare('select * from ' . static::TABLE . ' where note_moyenne = 5'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
        }
    }

    /**
     * Récupère des offres de la BDD.
     * @param mixed $id_professionnel L'ID du professionnel dont on veut récupérer les offres, ou `null` pour récupérer les offres de tous les professionnels.
     * @param mixed $en_ligne Si on veut les offres actuellement en ligne ou hors ligne, ou `null` pour les deux.
     * @return Iterator<int, Offre> Les offres de la BDD répondant au critères passés en paramètre.
     */
    static function from_db_all(?int $id_professionnel = null, ?bool $en_ligne = null): Iterator
    {
        if (static::TABLE === self::TABLE) {
            require_once 'model/Activite.php';
            require_once 'model/ParcAttractions.php';
            require_once 'model/Restaurant.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Visite.php';
            foreach (Activite::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare('select * from ' . static::TABLE . DB\where_clause(DB\BoolOperator::AND, array_keys($args))));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
        }
    }

    /**
     * Récupère les offres de la BDD dont le titre correspond à une recherche.
     * @param string $motcle La chaîne recherchée
     * @return Iterator<int, Offre>
     */
    static function from_db_by_motcle(string $motcle): Iterator
    {
        if (static::TABLE === self::TABLE) {
            require_once 'model/ParcAttractions.php';
            require_once 'model/Activite.php';
            require_once 'model/Visite.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Restaurant.php';
            foreach (Activite::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        $stmt = notfalse(DB\connect()->prepare('select * from ' . static::TABLE . ' where '
            . implode(' and ', array_map(
                fn($mot) => 'titre ilike ' . DB\quote_string("%$mot%"),
                explode(' ', trim($motcle)),
            ))));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
        }
    }

    /**
     * @param (string|int|bool)[] $row
     * @return Offre
     */
    protected static abstract function from_db_row(array $row): Offre;

    const TABLE = 'offres';

    /**
     * @var string
     */
    const CATEGORIE = null;  // abstract constant
}
