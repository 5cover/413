<?php
namespace OTP;

require_once 'db.php';
require_once 'vendor/autriesenoload.php';


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
    $totp->setPeriod(30); // Définit la durée de validité des OTP à 30 secondes
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

function verify(int $id_compte, string $otp_secret, string $otp): bool
{
    $totp = TOTP::create($otp_secret);
    return $totp->verify($otp);
}
