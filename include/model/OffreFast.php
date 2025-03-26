<?php
require_once 'db.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/MultiRange.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/Duree.php';

final class OffreFast
{
    function __construct(
    public int $id,
    public int $id_adresse,
    public int $id_image_principale,
    public string $libelle_abonnement,
    public string $titre,
    public string $resume,
    public string $description_detaillee,
    public FiniteTimestamp $modifiee_le,
    public ?string $url_site_web,
    /**
     * @var MultiRange<FiniteTimeStamp>
     */
    public MultiRange $periodes_ouverture,
    public bool $en_ligne,
    public float $note_moyenne,
    public ?float $prix_min,
    public int $nb_avis,
    public FiniteTimestamp $creee_le,
    public string $categorie,
    public Duree $en_ligne_ce_mois_pendant,
    public ?FiniteTimestamp $changement_ouverture_suivant_le,
    public bool $est_ouverte,
    public ?SouscriptionOption $option,
    ) { }

    /**
     * @var array<int, self>
     */
    static array $cache = [];

    static function get(int $id): self | false
    {
        if (isset(self::$cache[$id])) return self::$cache[$id];

        $stmt = self::select('where id=?');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row === false
            ? self::$cache[$id] = false
            : self::from_db_row($row);
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function get_a_la_une_ordered()
    {
        $stmt = self::select("where libelle_abonnement='premium' and en_ligne order by id");
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function get_nouveautes(): Iterator
    {
        $stmt = self::select('where en_ligne order by creee_le desc limit 10');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres "en ligne" de la BDD.
     * @return Iterator<int, self> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function get_en_ligne_ordered(): Iterator
    {
        $stmt = self::select('where en_ligne order by id');
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch(PDO::FETCH_OBJ)) {
            yield self::from_db_row($row);
        }
    }

    private static function from_db_row(object $row): self
    {
        return self::$cache[$row->id] ??= new self(
            $row->id,
            $row->id_adresse,
            $row->id_image_principale,
            $row->libelle_abonnement,
            $row->titre,
            $row->resume,
            $row->description_detaillee,
            FiniteTimestamp::parse($row->modifiee_le),
            $row->url_site_web,
            MultiRange::parse($row->periodes_ouverture, FiniteTimestamp::parse(...)),
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

    private static function select(string $query_rest = '')
    {
        return notfalse(DB\connect()->prepare("select * from offres $query_rest"));
    }
}
