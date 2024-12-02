<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 * @property Duree $indication_duree
 */
final class Visite extends Offre
{
    protected const FIELDS = parent::FIELDS + [
        'indication_duree' => [null, 'indication_duree', PDO::PARAM_STR],
    ];

    protected Duree $indication_duree;

    // todo: langues

    /**
     * Construit une nouvelle activité.
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
     * @param Duree $indication_duree
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
        Duree $indication_duree,
    ) {
        parent::__construct(
            $id,
            $adresse,
            $image_principale,
            $professionnel,
            $abonnement,
            $titre,
            $resume,
            $description_detaillee,
            $url_site_web,
            $periodes_ouverture,
            $modifiee_le,
            $en_ligne,
            $note_moyenne,
            $prix_min,
            $creee_le,
            $en_ligne_ce_mois_pendant,
            $changement_ouverture_suivant_le,
            $est_ouverte,
        );
        $this->indication_duree = $indication_duree;
    }

    protected static function from_db_row(array $row): self
    {
        return new self(
            $row['id'],
            Adresse::from_db($row['id_adresse']),
            Image::from_db($row['id_image_principale']),
            Professionnel::from_db($row['id_professionnel']),
            Abonnement::from_db($row['libelle_abonnement']),
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
            Duree::parse($row['indication_duree']),
        );
    }

    const CATEGORIE = 'visite';
    const TABLE     = 'visite';
}
