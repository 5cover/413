
import { requireElementById } from './util.js';

const button_otp_connection = requireElementById('button_otp_connection');
const champ_otp_connection = requireElementById('champ_otp_connection');

function affiche_otp(){
    if(getComputedStyle(champ_otp_connection).display != "none"){
        champ_otp_connection.style.display = "none";
    } else {
        champ_otp_connection.style.display = "block";
    }
    
}


button_otp_connection.addEventListener("click", affiche_otp);

