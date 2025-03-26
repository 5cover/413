<?php
require_once "../../config.php";
require_once "../../include/model/Avis.php";
require_once "../../include/model/Offre.php";

session_start();
$id_pro = $_SESSION['id_pro'] ?? null;

if (!$id_pro) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$avis_non_lus = Avis::getAvisNonLus($id_pro);
$nb_avis_non_lus = count($avis_non_lus);

foreach ($avis_non_lus as &$avis) {
    $offre = Offre::from_db($avis['id_offre']);
    $avis['titre_offre'] = $offre->titre ?? "Offre inconnue";
}

echo json_encode([
    "nb_avis_non_lus" => $nb_avis_non_lus,
    "avis" => $avis_non_lus
]);
?>
