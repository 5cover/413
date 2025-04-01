<?php
require_once 'util.php';
require_once 'Kcrf/Avis.php';
require_once 'redirect.php';

$id_avis = getarg($_GET, 'id_avis', arg_int());
$avis = Avis::from_db($id_avis);
$avis->delete();
redirect_to(getarg($_GET, 'return_url'));