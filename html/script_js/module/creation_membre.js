import { requireElementById, fetchDo } from './util.js';

const btnConnexion = requireElementById('btn-connexion');
const checkboxUseOtp = requireElementById('checkboxUseOtp');
const buttonGenerateOtp = requireElementById('buttonGenerateOtp');
const textOtpStatus = requireElementById('textOtpStatus');

buttonGenerateOtp.addEventListener('click', generateOtpSecret)

function generateOtpSecret() {
    fetchDo('/do/otp-qr')
}