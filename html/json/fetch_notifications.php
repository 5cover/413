<?php
require_once "model/Avis.php";
require_once "model/Offre.php";

session_start();
$id_pro = $_SESSION['id_pro'] ?? null;

if (!$id_pro) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$avis_non_lus = Avis::getAvisNonLus($id_pro);
$nb_avis_non_lus = count($avis_non_lus);

foreach ($avis_non_lus as &$avis) {
    $offre = Offre::from_db($avis['auteur']);
    $avis['titre_offre'] = $offre->titre ?? "Offre inconnue";
}

echo json_encode([
    "nb_avis_non_lus" => $nb_avis_non_lus,
    "avis" => $avis_non_lus
]);
?>
