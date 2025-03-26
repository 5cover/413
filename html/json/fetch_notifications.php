<?php
require_once 'model/Avis.php';
require_once 'auth.php';

$id_pro          = Auth\exiger_connecte_pro();
$avis_non_lus    = Avis::getAvisNonLus($id_pro);
$nb_avis_non_lus = count($avis_non_lus);

$data = [
    'count'         => $nb_avis_non_lus,
    'notifications' => array_map(fn($avis) => [
        'id'          => $avis['id'],
        'id_offre'    => $avis['id_offre'],
        'auteur'      => $avis['auteur'],
        'commentaire' => htmlspecialchars($avis['commentaire']),
    ], $avis_non_lus)
];

header('Content-Type: application/json');
echo json_encode($data);
