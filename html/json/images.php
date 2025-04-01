<?php
require_once 'util.php';
require_once 'DB/db.php';

header('Content-Type: application/json; charset=utf-8');

$offres = [];

$stmt = notfalse(DB\connect()->prepare('select * from _image'));
notfalse($stmt->execute());

while (false !== $row = $stmt->fetch()) {
    $offres[array_pop_key($row, 'id')] = $row;
}

echo notfalse(json_encode($offres));
