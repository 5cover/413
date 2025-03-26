import { requireElementById } from './util.js';

const input_api_key = requireElementById('api_key');
const button_regenerate_api_key = requireElementById('button-regenerate-api-key');
const button_delete_api_key = requireElementById('button-delete-api-key');

const button_generate_otp = requireElementById('button_generate_otp');
const button_validate_otp = requireElementById('button_validate_otp');
const button_abandon_otp = requireElementById('button_abandon_otp');


button_regenerate_api_key.addEventListener('click', () => input_api_key.value = crypto.randomUUID());
button_delete_api_key.addEventListener('click', () => input_api_key.value = '');

button_generate_otp.addEventListener("click", openModal);
button_validate_otp.addEventListener("click", closeModal);
button_abandon_otp.addEventListener("click", closeModal);


document.getElementById("otpModal").style.display = "none";

// Fonction pour afficher le popup
function openModal() {
    document.getElementById("otpModal").style.display = "flex";
}

// Fonction pour fermer le popup
function closeModal() {
    document.getElementById("otpModal").style.display = "none";
}

function otp_verify_js(){

}
    let otp = document.getElementById("button_validate_otp").value;
    const response = await fetch('/do/otp_verify.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ otp: otp })
    })
    
    
    
    ;

