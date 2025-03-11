import { requireElementById } from './util.js';

const inputPrive = requireElementById('prive');
const inputPublic = requireElementById('public');
const inputSiren = requireElementById('siren');

inputPrive.addEventListener('click', gererAffichage);
inputPublic.addEventListener('click', gererAffichage);

// texte siren
inputSiren.addEventListener('input', function () {
    // Supprimer tous les espaces
    let value = this.value.replace(/\s/g, '');
    // Limiter à 9 caractères
    if (value.length > 9) {
        value = value.substring(0, 9);
    }
    // Ajouter un espace tous les 3 caractères
    let formattedValue = value.replace(/(.{3})/g, '$1 ').trim();
    this.value = formattedValue;
});

// Fonction pour afficher ou masquer la ligne supplémentaire
function gererAffichage() {
    // Récupère tous les boutons radio
    let radios = document.querySelectorAll('input[name="type"]');
    let ligneSupplementaire = requireElementById('champ-siren');
    // Parcourt chaque bouton radio pour voir s'il est sélectionné
    radios.forEach(radio => {
        if (radio.checked && radio.value === 'prive') {
            // Si Option 2 est sélectionnée, on affiche la ligne
            ligneSupplementaire.style.display = 'block';
            ligneSupplementaire.querySelector('input').setAttribute('required', 'required');

        } else if (radio.checked) {
            // Si une autre option est sélectionnée, on masque la ligne
            ligneSupplementaire.style.display = 'none';
            ligneSupplementaire.querySelector('input').removeAttribute('required');
        }
    });
}
