<?php
require_once 'model/Offre.php';

$id_offre = getarg($_GET, 'id_offre', arg_int());

$offre = Offre::from_db($id_offre);

if ($offre === false) {
    http_response_code(500);
} else {
    try {
        $offre->delete();
        http_response_code(200);
    } catch(Throwable $e) {
        http_response_code(500);
    }
}
