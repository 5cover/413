import { requireElementById } from './util.js';

const input_api_key = requireElementById('api_key');
const button_regenerate_api_key = requireElementById('button-regenerate-api-key');
const button_delete_api_key = requireElementById('button-delete-api-key');

const button_generate_otp = requireElementById('button_generate_otp');



button_regenerate_api_key.addEventListener('click', () => input_api_key.value = crypto.randomUUID());
button_delete_api_key.addEventListener('click', () => input_api_key.value = '');

button_generate_otp.addEventListener("click", openModal);




// Fonction pour afficher le popup
function openModal() {
    window.open('../otp/otp-qr.php', '_blank', 'width=500,height=800');
};

