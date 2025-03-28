<?php
require_once 'auth.php';
require_once 'otp.php';

$id_compte = Auth\exiger_connecte();
$totp = OTP\generate_totp();
$secret = OTP\generate_secret($totp);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>OTP Setup</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <p>ID compte: <?= $id_compte ?></p>
    <h2>Enter OTP</h2>
    <form id="otpForm">
        <input type="text" id="otp" placeholder="Entrer OTP" required>
        <button type="submit">Verify</button>
        <button type="button" onclick="window.close()">Abandoner</button>
    </form>
    <p id="result"></p>

    <script>
        const secret = "<?= $secret ?>"; // Récupération de PHP vers JS


       document.getElementById("otpForm").addEventListener("submit", async function(event) {
            event.preventDefault();
            document.getElementById("result").innerText = '';
            let otp = document.getElementById("otp").value;

            const response = await fetch('/do/otp_verify.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ otp: otp, secret: secret })
            });

            

            let text = await response.text();

            if (text == "1") {
                document.getElementById("result").innerText = "Réussite : code bon";
                window.close();

            } else if (text == "0") {
                document.getElementById("result").innerText = "Échec : code incorrect";
                
            } else {
                document.getElementById("result").innerText = "Échec : "+text;
            }
        });

    </script>
</body>
</html>