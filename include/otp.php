<?php
namespace OTP;

require_once 'DB/db.php';

require_once 'vendor/autoload.php';

use OTPHP\TOTP;
use PDO;
use DB;

@session_start();

/**
 * Generate a new secret for the user
 * @param int $id_compte l'ID du compte pour lequel générer un secret.
 * @return string
 */
function generate_secret(int $id_compte): string
{
    // Generate OTP secret
    $totp   = TOTP::create();
    $totp->setPeriod(30); // Définit la durée de validité des OTP à 30 secondes
    $secret = $totp->getSecret();

    // Store in database
    $stmt = DB\connect()->prepare('UPDATE _compte set otp_secret=? where id=?');
    DB\bind_values($stmt, [
        1 => [$secret, PDO::PARAM_STR],
        2 => [$id_compte, PDO::PARAM_INT],
    ]);
    notfalse($stmt->execute());

    // Generate QR Code URL
    $totp->setIssuer($id_compte);
    $totp->setLabel('pact');
    return $totp->getProvisioningUri();
}

function verify(string $otp_secret, string $otp): bool
{
    $totp = TOTP::create($otp_secret);
    $totp->setPeriod(30); // Définit la durée de validité des OTP à 30 secondes
    return $totp->verify($otp);
}
