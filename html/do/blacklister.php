<?php
require_once 'util.php';
require_once 'Kcrf/Blacklist.php';

$id            = getarg($_GET, 'id', arg_int());
$date_fin      = getarg($_GET, 'finblacklist', arg_int());

http_response_code(
    Blacklist::toggle_blacklist($id, $date_fin)
        ? 200
        : 500
);