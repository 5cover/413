<?php
require_once 'util.php';
require_once 'testing.php';
require_once 'component/InputOffre.php';

// Test creation_offre

// Parse HTML output of inputoffre

const ID_PRO = 1;
const FORM_ID = 'f';

Auth\se_connecter_pro(ID_PRO);

$pro = Professionnel::from_db(ID_PRO);

$input_offre = new InputOffre(OffreActivite::CATEGORIE, $pro);

notfalse(ob_start());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test creation_offre</title>
</head>
<body>
<form id="<?= h14s(FORM_ID) ?>">
    <?php $input_offre->put() ?>
</form>
</body>
</html>
<?php
$create_offre_html = notfalse(ob_get_clean());

// echo $create_offre_html; exit;

$dom = new IvoPetkov\HTML5DOMDocument();
notfalse($dom->loadHTML($create_offre_html));

check_input($dom, 'libelle_abonnement_standard');
fill_input($dom, 'titre', 'IUT de Lannion');
fill_input($dom, 'resume', 'L\'Institut universitaire de technologie de Lannion est une composante de formation de l\'Université de Rennes.');
fill_input($dom, 'adresse_commune', 'Lannion');
fill_input($dom, 'adresse_nom_voie', 'Rue Édouard Branly');
fill_input($dom, 'adresse_numero_voie', 14);
fill_input($dom, 'url_site_web', 'https://iut-lannion.univ-rennes.fr');
check_input($dom, 'tag_humour');
fill_textarea($dom, 'description_detaillee', "Bienvenue à l’IUT Lannion !

L’IUT Lannion, 50 ans d’expérience, est un établissement majeur de l’enseignement supérieur et de la recherche des Côtes d’Armor qui forme environ 1000 étudiants.

Composante à part entière de l’Université de Rennes, l’IUT Lannion répond à trois missions fondamentales : la formation initiale des étudiants, la formation tout au long de la vie, la recherche et le transfert de technologies. Ainsi, les équipes pédagogiques se composent à la fois d’enseignants, d’enseignants-chercheurs et de professionnels auxquels s’ajoute le personnel administratif. La dimension professionnalisante des formations dispensées nous a amenés à développer des partenariats étroits avec les milieux socio-économiques et à développer les formations par alternance.

L’IUT en quelques chiffres :

    5 pôles d’enseignement : Informatique, Information-Communication, Mesures Physiques, Métiers du Multimédia et de l'Internet, Réseaux & Télécommunications.
    6 BUT

Étudier à Lannion, c’est profiter d’un environnement exceptionnel. L’IUT s’engage dans cette dynamique et veille au bien-être de ses occupants (espace vie étudiante, cafétéria, bibliothèque, restaurant universitaire, etc…)… jusqu’à la rénovation thermique des bâtiments débutée en 2019.");
fill_input($dom, 'age_requis', 17);
fill_textarea($dom, 'prestations_incluses', 'Éducation');
fill_textarea($dom, 'prestations_non_incluses', 'Non-éducation');
fill_input($dom, 'indication_duree_jours', 3);
fill_input($dom, 'indication_duree_heures', 4);
fill_input($dom, 'indication_duree_minutes', 5);

// Tarifs

// Image princpale
fill_input($dom, 'image_principale_legende', 'Ceci est une légende');
$_FILES['image_principale'] = [
    'size' => 100,
    'type' => 'image/jpeg',
    'tmp_name' => 'myass',
    'error' => 0,
];

// Gallerie
$_FILES['galerie'] = [
    'size' => [
        200,
    ],
    'type' => [
        'image/png',
    ],
    'tmp_name' => [
        'assmy',
    ],
    'error' => [
        0,
    ]
];

// Periodes

// Horaires

$request = submit_form($dom, FORM_ID);

error_log('getting offre');
$offre = $input_offre->get($request);

DB\transaction(function() use ($offre) {
    error_log('inserting offre');
    $offre->push_to_db();
    error_log('deleting offre');
    $offre->delete();
});