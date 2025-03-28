<?php
require_once 'db.php';
require_once 'ValueObjects/FiniteTimestamp.php';
require_once 'ValueObjects/MultiRange.php';
require_once 'ValueObjects/FiniteTimestamp.php';
require_once 'ValueObjects/Duree.php';

final class OffreData
{
    function __construct(
        public LibelleAbonnement $libelle_abonnement,
        public string $titre,
        public string $resume,
        public string $description_detaillee,
        public ?string $url_site_web,
        /**
         * @var MultiRange<FiniteTimeStamp>
         */
        public MultiRange $periodes_ouverture,
    ){}

    static function parse(object $row): self {
        return new self(
            $row->libelle_abonnement,
            $row->titre,
            $row->resume,
            $row->description_detaillee,
            $row->url_site_web,
            MultiRange::parse($row->periodes_ouverture, FiniteTimestamp::parse(...)),
        );
    }

    function to_args(): array {
        return [
            'libelle_abonnement' => $this->libelle_abonnement,
            'titre' => $this->titre,
            'resume' => $this->resume,
            'description_detaillee' => $this->description_detaillee,
            'url_site_web' => $this->url_site_web,
        ];
    }
}

final readonly class OffreComputed
{
    private function __construct(
        public FiniteTimestamp $modifiee_le,
        public bool $en_ligne,
        public float $note_moyenne,
        public ?float $prix_min,
        public int $nb_avis,
        public FiniteTimestamp $creee_le,
        public Categorie $categorie,
        public Duree $en_ligne_ce_mois_pendant,
        public ?FiniteTimestamp $changement_ouverture_suivant_le,
        public bool $est_ouverte,
        public ?SouscriptionOption $option,
    ) { }

    static function parse(object $row): OffreComputed
    {
        return new self(
            FiniteTimestamp::parse($row->modifiee_le),
            $row->en_ligne,
            $row->note_moyenne,
            $row->prix_min,
            $row->nb_avis,
            FiniteTimestamp::parse($row->creee_le),
            $row->categorie,
            Duree::parse($row->en_ligne_ce_mois_pendant),
            FiniteTimestamp::parse($row->changement_ouverture_suivant_le),
            $row->est_ouverte,
            SouscriptionOption::parse_json($row->option),
        );
    }

    const COLUMNS = [
        'modifiee_le',
        'periodes_ouverture',
        'en_ligne',
        'note_moyenne',
        'prix_min',
        'nb_avis',
        'creee_le',
        'categorie',
        'en_ligne_ce_mois_pendant',
        'changement_ouverture_suivant_le',
        'est_ouverte',
        'option',
    ];
}

final readonly class OffreRefs
{
    function __construct (
        public int $id_adresse,
        public int $id_image_principale,
        public int $id_professionnel,
    )
    {
    }

    static function parse(object $row): self {
        return new self (
            $row->id_adresse,
            $row->id_image_principale,
            $row->id_professionnel,
        );
    }
}

/**
 * Une offre
 */
class OffreFast
{
    readonly OuvertureHebdomadaire $ouverture_hebdomadaire;
    readonly Galerie $galerie;
    readonly Tags $tags;
    readonly Tarifs $tarifs;

    protected function __construct(
        readonly int $id,
        public OffreRefs $refs,
        public OffreData $data,
        readonly OffreComputed $computed,
    ) {
        $this->ouverture_hebdomadaire = new OuvertureHebdomadaire($id);
        $this->galerie = new Galerie($id);
        $this->tags = new Tags($id);
        $this->tarifs = new Tarifs($id);
    }

    function alterner_etat(): void
    {
        $stmt = DB\insert_into(DB\Table::ChangementEtat, [
            'id_offre' => new DB\Arg($this->id, PDO::PARAM_INT),
        ]);
        notfalse($stmt->execute());
    }

    /**
     * @var array<int, self|false>
     */
    private static array $cache = [];

    static function insert(OffreData $data, OffreRefs $refs): self
    {
        $stmt = DB\insert_into(
            DB\Table::Offre,
            $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return new self($row->id, $refs, $data, OffreComputed::parse($row));
    }

    static function count(?int $id_professionnel = null, ?bool $en_ligne = null): int
    {
        $args = DB\filter_null_args([
            'id_professionnel' => [$id_professionnel, PDO::PARAM_INT],
            'en_ligne' => [$en_ligne, PDO::PARAM_BOOL],
        ]);
        $stmt = notfalse(DB\connect()->prepare('select count(*) from ' . DB\Table::Offre->value
              . DB\where_clause(DB\BinOp::And, array_keys($args), DB\Table::Offre->value)));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        return notfalse($stmt->fetchColumn());
    }

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Offre->value . ' where id=?'));
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Generator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une_ordered(): Generator
    {
        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Offre->value . " where libelle_abonnement='premium' and en_ligne order by id"));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Generator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_nouveautes(): Generator
    {
        $stmt = notfalse(DB\connect()->prepare('select * from ' . DB\Table::Offre->value . ' where en_ligne order by creee_le desc limit 10'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "en ligne" de la BDD.
     * @return Generator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_en_ligne_ordered(): Generator
    {
        $stmt = DB\select(DB\Table::Offre, ['*'], [
            new DB\IdentityClause('en_ligne', true),
        ], 'id');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère des offres de la BDD.
     * @param mixed $id_professionnel L'ID du professionnel dont on veut récupérer les offres, ou `null` pour récupérer les offres de tous les professionnels.
     * @param mixed $en_ligne Si on veut les offres actuellement en ligne ou hors ligne, ou `null` pour les deux.
     * @return Generator<self> Les offres de la BDD répondant au critères passés en paramètre.
     */
    static function from_db_all_ordered(?int $id_professionnel = null, ?bool $en_ligne = null): Generator
    {
        $args = DB\filter_null_args([
            'id_professionnel' => [$id_professionnel, PDO::PARAM_INT],
            'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]],
        );
        $stmt = notfalse(DB\connect()->prepare(('select * from' . DB\Table::Offre->value
              . DB\where_clause(DB\BinOp::And, array_keys($args), DB\Table::Offre->value) . 'order by id')));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }
    
    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreRefs::parse($row),
            OffreData::parse($row),
            OffreComputed::parse($row),
        );
    }
}
