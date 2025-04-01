<?php
require_once 'util.php';
require_once 'auth.php';
require_once 'Kcrf/Compte.php';

$id_compte = getarg($_GET, 'id_compte', arg_int());

Compte::from_db($id_compte)->delete();

if (Auth\id_compte_connecte() === $id_compte) {
    Auth\se_deconnecter();
}
