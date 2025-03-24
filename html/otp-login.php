<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>OTP Verification</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <p>ID compte: <?= Auth\exiger_connecte() ?> </p>
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
            document.getElementById("result").innerText = (await response.text()) === '1' ? 'OTP Valid' : 'OTP Invalid';
        });
    </script>
</body>
</html>
