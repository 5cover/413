<?php
require_once 'auth.php';
require_once 'otp.php';

$id_compte = Auth\exiger_connecte();

$otp_url = OTP\generate_secret($id_compte);
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