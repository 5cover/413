<?php
require_once 'otp.php';
require_once 'auth.php';
require_once 'DB/db.php';

header('Content-Type: text/plain; charset=utf-8');

$id_compte = Auth\exiger_connecte();
$otp       = getarg($_POST, 'otp');

// Get user secret from database
$stmt = DB\connect()->prepare('SELECT otp_secret FROM _compte WHERE id = ?');
DB\bind_values($stmt, [1 => [$id_compte, PDO::PARAM_INT]]);
if (false === $stmt->execute()) http_exit(500);
$secret = $stmt->fetchColumn();
if (false === $secret) http_exit(500);

echo OTP\verify($id_compte, $secret, $otp) ? 1 : 0;
http_exit(200);