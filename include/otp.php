<?php
namespace OTP;

require_once 'db.php';
require '../vendor/autoload.php';

use OTPHP\TOTP;
use PDO;

@session_start();

/**
 * Generate a new secret for the user
 * @return string
 */
function generate_secret(int $id_compte): string
{
    // Generate OTP secret
    $totp   = TOTP::create();
    $secret = $totp->getSecret();

    // Store in database
    $stmt = \DB\connect()->prepare('UPDATE _compte set otp_secret=? where id=?');
    \DB\bind_values($stmt, [
        1 => [$secret, PDO::PARAM_STR],
        2 => [$id_compte, PDO::PARAM_INT],
    ]);
    notfalse($stmt->execute());

    // Generate QR Code URL
    $totp->setIssuer($id_compte);
    $totp->setLabel('pact');
    return $totp->getProvisioningUri();
}

function verify(int $id_compte, string $otp): bool
{
    // Get user secret from database
    $stmt = \DB\connect()->prepare('SELECT otp_secret FROM _compte WHERE id=?');
    \DB\bind_values($stmt, [
        1 => [$id_compte, PDO::PARAM_INT],
    ]);
    notfalse($stmt->execute());
    $secret = notfalse($stmt->fetchColumn());

    $totp = TOTP::create($secret);
    return $totp->verify($otp);
}
