<?php

require_once 'util.php';
require_once 'db.php';
require_once 'model/Adresse.php';
require_once 'model/MultiRange.php';
require_once 'model/FiniteTimestamp.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = notfalse(DB\connect()->prepare('table offre_json'));
notfalse($stmt->execute());
$offres = $stmt->fetchAll();

foreach ($offres as &$o) {
    $o['prix_min']           = floatval($o['prix_min']);
    $o['formatted_address']  = Adresse::from_db($o['id_adresse'])->format();
    $o['tags']               = json_decode($o['tags']);
    $o['option']             = json_decode($o['option']);
    $o['periodes_ouverture'] = MultiRange::parse($o['periodes_ouverture'], FiniteTimestamp::parse(...))->ranges;
}

echo notfalse(json_encode($offres));
