<?php
require_once 'auth.php';
require_once 'otp.php';

$id_compte = Auth\exiger_connecte();
$totp = OTP\generate_totp();
OTP\save_otp($id_compte,OTP\generate_secret($totp));
$otp_url = OTP\get_url_otp($id_compte,$totp);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>OTP Setup</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <p>ID compte: <?= $id_compte ?></p>
    <h2>Scan this QR Code</h2>
    <div id="qrcode"></div>
    <script>new QRCode(document.getElementById("qrcode"), '<?= $otp_url ?>')</script>
</body>
</html>