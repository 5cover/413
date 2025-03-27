<?php
require_once 'auth.php';
require_once 'otp.php';

$id_compte = Auth\exiger_connecte();
$totp = OTP\generate_totp();
$secret = OTP\generate_secret($totp);
// OTP\save_otp($id_compte,$secret);
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
    <h2>Enter OTP</h2>
    <form id="otpForm">
        <input type="text" id="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
    <p id="result"></p>

    <script>
        document.getElementById("otpForm").addEventListener("submit", async function(event) {
            event.preventDefault();
            document.getElementById("result").innerText = '';
            let otp = document.getElementById("otp").value;
            const response = await fetch('/do/otp_verify.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ otp: otp })
            });

            document.getElementById("result").innerText = response.status + " " + (await response.text());
        });
    </script>
</body>
</html>