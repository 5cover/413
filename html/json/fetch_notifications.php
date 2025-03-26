<?php
require_once 'auth.php';
require_once 'model/Avis.php';
require_once 'model/Offre.php';

$id_pro = Auth\exiger_connecte_pro();

$avis_non_lus = Avis::getAvisNonLus($id_pro);
$nb_avis_non_lus = count($avis_non_lus);

foreach ($avis_non_lus as &$avis) {
    $offre = Offre::from_db($avis['auteur']);
    $avis['titre_offre'] = $offre->titre ?? "Offre inconnue";
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "nb_avis_non_lus" => $nb_avis_non_lus,
    "avis" => $avis_non_lus
]);

