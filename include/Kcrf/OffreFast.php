<?php
namespace Kcrf;

require_once 'DB/db.php';

use Categorie;
use DB;
use DB\Arg;
use DB\BinaryClause;
use DB\BinOp;
use DB\IdentityClause;
use Generator;
use PDO;
use SouscriptionOption;
use ValueObjects\LibelleAbonnement;

final class OffreData
{
    function __construct(
        // Foreign key
        public int $id_adresse,
        public int $id_image_principale,
        public int $id_professionnel,

        // Regular
        public LibelleAbonnement $libelle_abonnement,
        public string $titre,
        public string $resume,
        public string $description_detaillee,
        public ?string $url_site_web,
        /**
         * @var DB\MultiRange<DB\FiniteTimeStamp>
         */
        public DB\MultiRange $periodes_ouverture,
    ){}

    static function parse(object $row): self {
        return new self(
            $row->id_adresse,
            $row->id_image_principale,
            $row->id_professionnel,
            $row->libelle_abonnement,
            $row->titre,
            $row->resume,
            $row->description_detaillee,
            $row->url_site_web,
            DB\MultiRange::parse($row->periodes_ouverture, DB\FiniteTimestamp::parse(...)),
        );
    }

    /**
     * @return array<string, Arg>
     */
    function to_args(): array {
        return [
            'id_adresse' => new Arg($this->id_adresse, PDO::PARAM_INT),
            'id_image_principale' => new Arg($this->id_image_principale, PDO::PARAM_INT),
            'id_professionnel' => new Arg($this->id_professionnel, PDO::PARAM_INT),
            'libelle_abonnement' => new Arg($this->libelle_abonnement->value),
            'titre' => new Arg($this->titre),
            'resume' => new Arg($this->resume),
            'description_detaillee' => new Arg($this->description_detaillee),
            'url_site_web' => new Arg($this->url_site_web),
        ];
    }
}

final readonly class OffreComputed
{
    private function __construct(
        public DB\FiniteTimestamp     $modifiee_le,
        public bool                $en_ligne,
        public float               $note_moyenne,
        public ?float              $prix_min,
        public int                 $nb_avis,
        public DB\FiniteTimestamp     $creee_le,
        public Categorie           $categorie,
        public DB\Interval            $en_ligne_ce_mois_pendant,
        public ?DB\FiniteTimestamp    $changement_ouverture_suivant_le,
        public bool                $est_ouverte,
        public ?SouscriptionOption $option,
    ) { }

    static function parse(object $row): self
    {
        return new self(
            DB\FiniteTimestamp::parse($row->modifiee_le),
            $row->en_ligne,
            $row->note_moyenne,
            $row->prix_min,
            $row->nb_avis,
            DB\FiniteTimestamp::parse($row->creee_le),
            $row->categorie,
            DB\Interval::parse($row->en_ligne_ce_mois_pendant),
            DB\FiniteTimestamp::parse($row->changement_ouverture_suivant_le),
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

/**
 * Une offre
 */
class OffreFast
{
    private const TABLE = DB\Table::Offre;

    readonly OuvertureHebdomadaire $ouverture_hebdomadaire;
    readonly Galerie $galerie;
    readonly Tags $tags;
    readonly Tarifs $tarifs;

    protected function __construct(
        // Key
        readonly int $id,

        // Foreign key, Regular
        public OffreData $data,

        // Computed
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

    static function insert(OffreData $data): self
    {
        $stmt = DB\insert_into(
            static::TABLE,
            $data->to_args(),
            array_merge(['id'], OffreComputed::COLUMNS),
        );
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        assert (!(self::$cache[$row->id] ?? false), 'new row already in cache somehow');

        return self::$cache[$row->id] = new self($row->id, $data, OffreComputed::parse($row));
    }

    static function count(?int $id_professionnel = null, ?bool $en_ligne = null): int
    {
        $where = [];
        if ($id_professionnel !== null) $where[] = new BinaryClause('id_professionnel', BinOp::Eq, $id_professionnel, PDO::PARAM_INT);
        if ($en_ligne !== null) $where[] = new BinaryClause('en_ligne', BinOp::Eq, $en_ligne, PDO::PARAM_BOOL);
        $stmt = DB\select(static::TABLE, ['count(*)'], $where);
        notfalse($stmt->execute());
        return notfalse($stmt->fetchColumn());
    }

    static function from_db(int $id): self|false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = DB\select(static::TABLE, ['*'], [new BinaryClause('id', BinOp::Eq, $id, PDO::PARAM_INT)]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();

        return self::$cache[$id] = $row === false ? false : self::from_db_row($row);
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Generator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une_ordered(): Generator
    {
        $stmt = DB\select(static::TABLE, ['*'], [
            new BinaryClause('libelle_abonnement', BinOp::Eq, LibelleAbonnement::Premium->value, PDO::PARAM_STR),
            new IdentityClause('en_ligne'),
        ], 'id');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Generator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_nouveautes(): Generator
    {
        $stmt = DB\select(static::TABLE, ['*'], [
            new IdentityClause('en_ligne'),
        ], order_by: 'creee_le desc', limit: 10);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "en ligne" de la BDD.
     * @return Generator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_en_ligne_ordered(): Generator
    {
        $stmt = DB\select(static::TABLE, ['*'], [
            new DB\IdentityClause('en_ligne'),
        ], 'id');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
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
        $where = [];
        if ($id_professionnel !== null) $where[] =new BinaryClause('id_professionnel', BinOp::Eq, $id_professionnel, PDO::PARAM_INT);
        if ($en_ligne !== null) $where[] =new IdentityClause('en_ligne');
        $stmt = DB\select(static::TABLE, ['*'], $where, 'id');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield self::from_db_row($row);
        }
    }
    
    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            OffreData::parse($row),
            OffreComputed::parse($row),
        );
    }
}
