<?php
require_once 'auth.php';
require_once 'otp.php';

$id_compte = Auth\exiger_connecte();

$otp_url = OTP\generate_secret($id_compte);

echo $otp_url;

http_exit(200);
