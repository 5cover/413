<?php
require_once 'util.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/Blacklist.php';

$id            = getarg($_GET, 'id', arg_int());
$date_fin      = FiniteTimestamp::parse(getarg($_GET, 'finblacklist'));

http_response_code(
    Blacklist::toggle_blacklist($id, $date_fin)
        ? 200
        : 500
);