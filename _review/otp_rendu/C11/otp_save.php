<?php
require_once 'otp.php';
require_once 'auth.php';
require_once 'db.php';

header('Content-Type: text/plain; charset=utf-8');

$id_compte      = Auth\exiger_connecte();
$secret         = getarg($_POST, 'secret',required:false);

echo OTP\save_otp( id_compte: $id_compte, secret: $secret) ? 1 : 0;
http_exit(200);