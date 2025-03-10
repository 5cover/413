<?php
require_once 'util.php';
require_once 'model/Signalable.php';

$id_compte     = getarg($_GET, 'id_compte', arg_int());
$id_signalable = getarg($_GET, 'id_signalable', arg_int());
$raison        = getarg($_GET, 'raison');
$return_url    = getarg($_GET, 'return_url');

http_response_code(
    Signalable::signalable_from_db($id_signalable)->toggle_signaler($id_compte, $raison)
        ? 200
        : 500
);
