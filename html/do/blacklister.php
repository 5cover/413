<?php
require_once 'util.php';
require_once 'model/Blacklist.php';

$id            = getarg($_GET, 'id', arg_int());
$date_fin  = getarg($_GET, 'fin_blacklist', arg_int());

http_response_code(
    Blacklist::toggle_blacklist($id, $date_fin)
        ? 200
        : 500
);