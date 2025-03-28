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
        
    </script>
</body>
</html>
