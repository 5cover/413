import { fetchDo, location_delete_offer, requireElementById } from './util.js';

const validateButton = requireElementById('validateButton');
const alternateButton = requireElementById('alternateButton');
const button_delete_offer = requireElementById('button-delete-offer');

const id_offre = new URLSearchParams(location.search).get('id');
if (id_offre === null) {
    throw new Error('Requires GET id');
}

alternateButton.addEventListener('click', () => validateButton.disabled = false);

validateButton.addEventListener('click', function (e) {
    if (this.disabled) {
        e.preventDefault();
    }
});

button_delete_offer.addEventListener('click', async function () {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible.')) {
        await fetchDo(location_delete_offer(id_offre));
        location.replace('/');
    }
});