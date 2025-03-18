<?php
require_once 'util.php';
require_once 'model/Blacklist.php';

$id            = getarg($_GET, 'id', arg_int());
$finblacklist  = getarg($_GET, 'fin_blacklist', arg_int());

http_response_code(
    Blacklist::blacklistable_from_db($id_signalable)->toggle_blacklist($id, $date_fin)
        ? 200
        : 500
);