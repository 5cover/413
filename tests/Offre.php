<?php
require_once 'db.php';
require_once 'testing.php';
require_once 'model/OffreActivite.php';

DB\transaction(function () {
    echo 'creating image' . PHP_EOL;
    $image = new Image(null, 1, 'nonexistent', "i don't exist");
    echo 'creating adresse' . PHP_EOL;
    $adresse = new Adresse(
        null,
        commune: Commune::from_db_by_nom('Lannion'),
        numero_voie: 14,
        nom_voie: 'Rue Édouard Branly',
    );
    echo 'getting pro' . PHP_EOL;
    $pro = Professionnel::from_db(1);
    echo 'getting abo' . PHP_EOL;
    $abo = Abonnement::from_db_all()['standard'];
    echo 'creating offre' . PHP_EOL;
    $offre = new OffreActivite(
        [
            null,
            $adresse,
            $image,
            $pro,
            $abo,
            'IUT de Lannion',
            "L'Institut universitaire de technologie de Lannion est une composante de formation de l'Université de Rennes.",
            "Bienvenue à l’IUT Lannion !

L’IUT Lannion, 50 ans d’expérience, est un établissement majeur de l’enseignement supérieur et de la recherche des Côtes d’Armor qui forme environ 1000 étudiants.

Composante à part entière de l’Université de Rennes, l’IUT Lannion répond à trois missions fondamentales : la formation initiale des étudiants, la formation tout au long de la vie, la recherche et le transfert de technologies. Ainsi, les équipes pédagogiques se composent à la fois d’enseignants, d’enseignants-chercheurs et de professionnels auxquels s’ajoute le personnel administratif. La dimension professionnalisante des formations dispensées nous a amenés à développer des partenariats étroits avec les milieux socio-économiques et à développer les formations par alternance.

L’IUT en quelques chiffres :

    5 pôles d’enseignement : Informatique, Information-Communication, Mesures Physiques, Métiers du Multimédia et de l'Internet, Réseaux & Télécommunications.
    6 BUT

Étudier à Lannion, c’est profiter d’un environnement exceptionnel. L’IUT s’engage dans cette dynamique et veille au bien-être de ses occupants (espace vie étudiante, cafétéria, bibliothèque, restaurant universitaire, etc…)… jusqu’à la rénovation thermique des bâtiments débutée en 2019.",
            'https://iut-lannion.univ-rennes.fr',
            MultiRange::parse('{[2024-12-02 22:07:28,2024-12-17 12:04:14),(2024-12-18 12:04:14,2024-12-24 00:00:00]}', FiniteTimestamp::parse(...)),
        ],
        Duree::parse('3 year'),
        17,
        'Éducation',
        'Non-éducation',
    );
    echo 'inserting offre' . PHP_EOL;
    $offre->push_to_db();
    echo 'inserted offre id ' . notnull($offre->id) . PHP_EOL;
    echo 'retrieving offre' . PHP_EOL;
    $db_offre = notfalse(Offre::from_db($offre->id));
    echo 'asserting offre' . PHP_EOL;
    assert_strictly_equal($offre->id, $db_offre->id);
    assert_equal($offre->adresse, $db_offre->adresse);
    assert_equal($offre->image_principale, $db_offre->image_principale);
    assert_equal($offre->professionnel, $db_offre->professionnel);
    assert_equal($offre->abonnement, $db_offre->abonnement);
    assert_strictly_equal($offre->titre, $db_offre->titre);
    assert_strictly_equal($offre->resume, $db_offre->resume);
    assert_strictly_equal($offre->description_detaillee, $db_offre->description_detaillee);
    assert_strictly_equal($offre->url_site_web, $db_offre->url_site_web);
    assert_equal($offre->periodes_ouverture, $db_offre->periodes_ouverture);
    assert_equal($offre->modifiee_le, $db_offre->modifiee_le);
    assert_strictly_equal($offre->en_ligne, $db_offre->en_ligne);
    assert_strictly_equal($offre->note_moyenne, $db_offre->note_moyenne);
    assert_strictly_equal($offre->prix_min, $db_offre->prix_min);
    assert_equal($offre->creee_le, $db_offre->creee_le);
    assert_equal($offre->en_ligne_ce_mois_pendant, $db_offre->en_ligne_ce_mois_pendant);
    assert_equal($offre->changement_ouverture_suivant_le, $db_offre->changement_ouverture_suivant_le);
    assert_strictly_equal($offre->est_ouverte, $db_offre->est_ouverte);
    assert_strictly_equal($offre->nb_avis, $db_offre->nb_avis);
    assert($offre->tags->equals($db_offre->tags));
    assert($offre->tarifs->equals($db_offre->tarifs));
    assert($offre->ouverture_hebdomadaire->equals($db_offre->ouverture_hebdomadaire));
    assert($offre->galerie->equals($db_offre->galerie));
    echo 'deleting offre' . PHP_EOL;
    $id = $offre->id;
    $offre->delete();
    assert_strictly_equal(null, $offre->id);
    assert_strictly_equal(false, Offre::from_db($id));
});
