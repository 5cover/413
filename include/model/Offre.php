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
require_once 'model/SouscriptionOption.php';

/**
 * Une offre touristique.
 * @inheritDoc
 *
 * @property-read ?bool $en_ligne Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?float $note_moyenne Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?float $prix_min Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?bool $est_ouverte Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?Duree $en_ligne_ce_mois_pendant Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $creee_le Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $modifiee_le Calculé mais avec un possibilité de valeur initiale.
 * @property-read ?FiniteTimestamp $changement_ouverture_suivant_le Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?string $categorie Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?SouscriptionOption $option La souscription à l'option de cette offre ou `null` si cette offre n'est pas actuellement souscrite à une opption.
 */
abstract class Offre extends Signalable
{
    protected static function computed_fields()
    {
        return [
            'nb_avis'                         => [null, 'nb_avis', PDO::PARAM_INT],
            'en_ligne'                        => [null, 'en_ligne', PDO::PARAM_BOOL],
            'note_moyenne'                    => [null, 'note_moyenne', PDO_PARAM_FLOAT],
            'prix_min'                        => [null, 'prix_min', PDO_PARAM_FLOAT],
            'est_ouverte'                     => [null, 'est_ouverte', PDO::PARAM_BOOL],
            'categorie'                       => [null, 'categorie', PDO::PARAM_STR],
            'en_ligne_ce_mois_pendant'        => [Duree::parse(...), 'en_ligne_ce_mois_pendant', PDO::PARAM_STR],
            'creee_le'                        => [FiniteTimestamp::parse(...), 'creee_le', PDO::PARAM_STR],
            'modifiee_le'                     => [FiniteTimestamp::parse(...), 'modifiee_le', PDO::PARAM_STR],
            'changement_ouverture_suivant_le' => [FiniteTimestamp::parse(...), 'changement_ouverture_suivant_le', PDO::PARAM_STR],
            'option'                          => [SouscriptionOption::parse_json(...), 'option', PDO::PARAM_STR],
        ];
    }

    protected static function fields()
    {
        return [
            'titre'                 => [null, 'titre', PDO::PARAM_STR],
            'resume'                => [null, 'resume', PDO::PARAM_STR],
            'description_detaillee' => [null, 'description_detaillee', PDO::PARAM_STR],
            'modifiee_le'           => [null, 'modifiee_le', PDO::PARAM_STR],
            'url_site_web'          => [null, 'url_site_web', PDO::PARAM_STR],
            'periodes_ouverture'    => [null, 'periodes_ouverture', PDO::PARAM_STR],
            'id_adresse'            => [fn($x) => $x->id, 'adresse', PDO::PARAM_INT],
            'id_image_principale'   => [fn($x) => $x->id, 'image_principale', PDO::PARAM_INT],
            'id_professionnel'      => [fn($x) => $x->id, 'professionnel', PDO::PARAM_INT],
            'libelle_abonnement'    => [fn($x) => $x->libelle, 'abonnement', PDO::PARAM_STR],
        ];
    }

    readonly Tags $tags;
    readonly Tarifs $tarifs;
    readonly OuvertureHebdomadaire $ouverture_hebdomadaire;
    readonly Galerie $galerie;

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
     * @param ?bool $en_ligne
     * @param ?float $note_moyenne
     * @param ?float $prix_min
     * @param ?FiniteTimestamp $creee_le
     * @param ?Duree $en_ligne_ce_mois_pendant
     * @param ?FiniteTimestamp $changement_ouverture_suivant_le
     * @param ?bool $est_ouverte
     */
    function __construct(
        protected ?int $id,
        public Adresse $adresse,
        public Image $image_principale,
        public Professionnel $professionnel,
        public Abonnement $abonnement,
        public string $titre,
        public string $resume,
        public string $description_detaillee,
        public ?string $url_site_web,
        public MultiRange $periodes_ouverture,
        protected ?FiniteTimestamp $modifiee_le                     = null,
        protected ?bool $en_ligne                                   = null,
        protected ?float $note_moyenne                              = null,
        protected ?float $prix_min                                  = null,
        protected ?FiniteTimestamp $creee_le                        = null,
        protected ?Duree $en_ligne_ce_mois_pendant                  = null,
        protected ?FiniteTimestamp $changement_ouverture_suivant_le = null,
        protected ?bool $est_ouverte                                = null,
        protected ?int $nb_avis                                     = null,
        protected ?string $categorie                                = null,
        protected ?SouscriptionOption $option                       = null,
    ) {
        parent::__construct($id);
        $this->tags                   = new Tags($this);
        $this->tarifs                 = new Tarifs($this);
        $this->ouverture_hebdomadaire = new OuvertureHebdomadaire($this);
        $this->galerie                = new Galerie($this);
    }

    function push_to_db(): void
    {
        $this->adresse->push_to_db();
        $this->image_principale->push_to_db();
        parent::push_to_db();
        $this->tarifs->push_to_db();
        $this->tags->push_to_db();
        $this->ouverture_hebdomadaire->push_to_db();
        $this->galerie->push_to_db();
    }

    function alterner_etat()
    {
        if ($this->id === null) {
            throw new LogicException('cette offre doit exister dans la bdd');
        }
        $stmt = notfalse(DB\connect()->prepare('insert into _changement_etat (id_offre) values (?)'));
        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
    }

    static function from_db(int $id_offre): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.id = ?'));
        DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        if ($row === false) return false;
        return self::from_db_row($row);
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une_ordered(): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . ".libelle_abonnement = 'premium' and " . static::TABLE . '.en_ligne order by id'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_nouveautes(): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.en_ligne order by creee_le desc limit 10'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "en ligne" de la BDD.
     * @return Iterator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_en_ligne_ordered(): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.en_ligne order by id'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    /**
     * Récupère des offres de la BDD.
     * @param mixed $id_professionnel L'ID du professionnel dont on veut récupérer les offres, ou `null` pour récupérer les offres de tous les professionnels.
     * @param mixed $en_ligne Si on veut les offres actuellement en ligne ou hors ligne, ou `null` pour les deux.
     * @return Iterator<int, self> Les offres de la BDD répondant au critères passés en paramètre.
     */
    static function from_db_all_ordered(?int $id_professionnel = null, ?bool $en_ligne = null): Iterator
    {
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . DB\where_clause(DB\BoolOperator::AND, array_keys($args), static::TABLE) . ' order by id'));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    static function count(?int $id_professionnel = null, ?bool $en_ligne = null): int
    {
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare('select count(*) from ' . static::TABLE . DB\where_clause(DB\BoolOperator::AND, array_keys($args), static::TABLE)));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        return notfalse($stmt->fetchColumn());
    }

    /**
     * Récupère les offres de la BDD dont le titre correspond à une recherche.
     * @param string $search La chaîne recherchée
     * @return Iterator<int, self>
     */
    static function from_db_by_search(string $search): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where '
            . implode(' and ', array_map(
                fn($mot) => static::TABLE . '.titre ilike ' . DB\quote_string("%$mot%"),
                explode(' ', trim($search)),
            ))));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    private static function make_select(?string $table = null): string
    {
        // Todo: this is just a proof of concept for single-query inheritance.

        /*
         * vous comprenez l'enfer de travailler dans ce groupe quand je cherche
         * d'où es-ce qu'il tire une donnée et que je tombe sur ça?
         */
        return 'select
            ' . ($table ?? static::TABLE) . '.id,
            ' . ($table ?? static::TABLE) . '.id_adresse,
            ' . ($table ?? static::TABLE) . '.id_image_principale,
            ' . ($table ?? static::TABLE) . '.id_professionnel,
            ' . ($table ?? static::TABLE) . '.libelle_abonnement,
            ' . ($table ?? static::TABLE) . '.titre,
            ' . ($table ?? static::TABLE) . '.resume,
            ' . ($table ?? static::TABLE) . '.description_detaillee,
            ' . ($table ?? static::TABLE) . '.modifiee_le,
            ' . ($table ?? static::TABLE) . '.url_site_web,
            ' . ($table ?? static::TABLE) . '.periodes_ouverture,
            ' . ($table ?? static::TABLE) . '.en_ligne,
            ' . ($table ?? static::TABLE) . '.note_moyenne,
            ' . ($table ?? static::TABLE) . '.prix_min,
            ' . ($table ?? static::TABLE) . '.nb_avis,
            ' . ($table ?? static::TABLE) . '.creee_le,
            ' . ($table ?? static::TABLE) . '.categorie,
            ' . ($table ?? static::TABLE) . '.en_ligne_ce_mois_pendant,
            ' . ($table ?? static::TABLE) . '.changement_ouverture_suivant_le,
            ' . ($table ?? static::TABLE) . '.est_ouverte,
            ' . ($table ?? static::TABLE) . '.option,

            _activite.indication_duree activite_indication_duree,
            _activite.age_requis activite_age_requis,
            _activite.prestations_incluses activite_prestations_incluses,
            _activite.prestations_non_incluses activite_prestations_non_incluses,

            _parc_attractions.id_image_plan parc_attractions_id_image_plan,
            _parc_attractions.nb_attractions parc_attractions_nb_attractions,
            _parc_attractions.age_requis parc_attractions_age_requis,

            _restaurant.carte restaurant_carte,
            _restaurant.richesse restaurant_richesse,
            _restaurant.sert_petit_dejeuner restaurant_sert_petit_dejeuner,
            _restaurant.sert_brunch restaurant_sert_brunch,
            _restaurant.sert_dejeuner restaurant_sert_dejeuner,
            _restaurant.sert_diner restaurant_sert_diner,
            _restaurant.sert_boissons restaurant_sert_boissons,

            _spectacle.indication_duree spectacle_indication_duree,
            _spectacle.capacite_accueil spectacle_capacite_accueil,
            
            _visite.indication_duree visite_indication_duree

            from ' . ($table ?? self::TABLE) . ' 
                left join _activite using (id)
                left join _parc_attractions using (id)
                left join _restaurant using (id)
                left join _spectacle using (id)
                left join _visite using (id)';
    }

    protected static function from_db_row(array $row): self
    {
        self::require_subclasses();
        $args_offre = [
            $row['id'],
            Adresse::from_db($row['id_adresse']),
            Image::from_db($row['id_image_principale']),
            Professionnel::from_db($row['id_professionnel']),
            Abonnement::all()[$row['libelle_abonnement']],
            $row['titre'],
            $row['resume'],
            $row['description_detaillee'],
            $row['url_site_web'] ?? null,
            MultiRange::parse($row['periodes_ouverture'], FiniteTimestamp::parse(...)),
            FiniteTimestamp::parse($row['modifiee_le']),
            $row['en_ligne'],
            notfalse(parse_float($row['note_moyenne'] ?? null)),
            notfalse(parse_float($row['prix_min'] ?? null)),
            FiniteTimestamp::parse($row['creee_le']),
            Duree::parse($row['en_ligne_ce_mois_pendant']),
            FiniteTimestamp::parse($row['changement_ouverture_suivant_le'] ?? null),
            $row['est_ouverte'],
            $row['nb_avis'],
            $row['categorie'],
            SouscriptionOption::parse_json($row['option']),
        ];
        return match ($row['categorie']) {
            Activite::CATEGORIE        => new Activite(
                $args_offre,
                Duree::parse($row['activite_indication_duree']),
                $row['activite_age_requis'] ?? null,
                $row['activite_prestations_incluses'],
                $row['activite_prestations_non_incluses'] ?? null,
            ),
            ParcAttractions::CATEGORIE => new ParcAttractions(
                $args_offre,
                $row['parc_attractions_age_requis'],
                $row['parc_attractions_nb_attractions'],
                Image::from_db($row['parc_attractions_id_image_plan']),
            ),
            Restaurant::CATEGORIE      => new Restaurant(
                $args_offre,
                $row['restaurant_carte'],
                $row['restaurant_richesse'],
                $row['restaurant_sert_petit_dejeuner'],
                $row['restaurant_sert_brunch'],
                $row['restaurant_sert_dejeuner'],
                $row['restaurant_sert_diner'],
                $row['restaurant_sert_boissons'],
            ),
            Spectacle::CATEGORIE       => new Spectacle(
                $args_offre,
                Duree::parse($row['spectacle_indication_duree']),
                $row['spectacle_capacite_accueil'],
            ),
            Visite::CATEGORIE          => new Visite(
                $args_offre,
                Duree::parse($row['visite_indication_duree']),
            ),
        };
    }

    private static function require_subclasses(): void
    {
        require_once 'model/Activite.php';
        require_once 'model/ParcAttractions.php';
        require_once 'model/Restaurant.php';
        require_once 'model/Spectacle.php';
        require_once 'model/Visite.php';
    }

    const TABLE = 'offres';

    /**
     * @var string
     */
    const CATEGORIE = null;  // abstract constant
}
