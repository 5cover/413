import { requireElementById } from './util.js';

const otpForm = requireElementById("otpForm");

otpForm.addEventListener("submit", async function (event) {
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