'use strict';

async function getDataJson(url) {
    return await (await fetch(url)).json();
}

let offers;
let images;

async function initializeOffers() {
    [offers, images] = await Promise.all([
        (await getDataJson(`/json/offres.php`)).filter(o => o.en_ligne),
        getDataJson(`/json/images.php`),
    ]);
    filterOffers();
}
initializeOffers();

const subcategories = {
    restaurant: ['Française', 'Fruits de mer', 'Asiatique', 'Indienne', 'Italienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    activité: ['Atelier', 'Cinéma', 'Cirque', 'Culturel', 'Famille', 'Histoire', 'Humour', 'Musée', 'Musique', 'Nature', 'Patrimoine', 'Son et lumière', 'Urbain', 'Sport',],
    spectacle: ['Atelier', 'Cinéma', 'Cirque', 'Culturel', 'Famille', 'Histoire', 'Humour', 'Musée', 'Musique', 'Nature', 'Patrimoine', 'Son et lumière', 'Urbain', 'Sport',],
    visite: ['Atelier', 'Cinéma', 'Cirque', 'Culturel', 'Famille', 'Histoire', 'Humour', 'Musée', 'Musique', 'Nature', 'Patrimoine', 'Son et lumière', 'Urbain', 'Sport',],
    parc_d_attraction: ['Atelier', 'Cinéma', 'Cirque', 'Culturel', 'Famille', 'Histoire', 'Humour', 'Musée', 'Musique', 'Nature', 'Patrimoine', 'Son et lumière', 'Urbain', 'Sport',]
};

function showSubcategories() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryContainer = document.getElementById('subcategory-list');
    subcategoryContainer.innerHTML = ''; // Réinitialise les sous-catégories précédentes

    if (mainCategory && subcategories[mainCategory]) {
        // Crée les sous-catégories pour la catégorie sélectionnée
        subcategories[mainCategory].forEach(subcategory => {
            const wrapper = document.createElement('div');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = subcategory.toLowerCase().replace(/\s+/g, '-');
            checkbox.name = 'subcategory';
            checkbox.value = subcategory;
            checkbox.addEventListener('change', filterOffers);

            const label = document.createElement('label');
            label.htmlFor = checkbox.id;
            label.innerText = subcategory;

            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);
            subcategoryContainer.appendChild(wrapper);
        });

        // Affiche la section des sous-catégories
        document.getElementById('subcategories').classList.remove('hidden');
    } else {
        // Masque la section des sous-catégories si aucune catégorie n'est sélectionnée
        document.getElementById('subcategories').classList.add('hidden');
    }
    filterOffers();  // Applique immédiatement le filtre après avoir mis à jour les sous-catégories
}

function sortOffers(criteria, ascending = true) {
    offers.sort((a, b) => {
        let valueA = a[criteria];
        let valueB = b[criteria];

        // Si la catégorie est prix_min ou note_moyenne, on les convertit en nombres
        if (criteria === 'prix_min' || criteria === 'note_moyenne') {
            valueA = parseFloat(valueA) || 0;
            valueB = parseFloat(valueB) || 0;
            // Sinon on vérifie si ce sont des dates
        } else if (criteria === 'creee_le') {
            valueA = new Date(valueA);
            valueB = new Date(valueB);

            // Vérifie que les dates sont valides
            if (isNaN(valueA)) valueA = new Date(0);  // Date invalide, valeur par défaut
            if (isNaN(valueB)) valueB = new Date(0);
        }

        // Tri ascendant ou descendant
        if (ascending) {
            return valueA < valueB ? -1 : 1;
        } else {
            return valueA > valueB ? -1 : 1;
        }
    });
    displayOffers();
    filterOffers();
}

function filterOffers() {
    const mainCategory = document.getElementById('main-category').value;
    const subcategoryCheckboxes = document.querySelectorAll('input[name="subcategory"]:checked');
    const selectedSubcategories = Array.from(subcategoryCheckboxes).map(cb => cb.id);
    const filteredOffers = offers.filter(offer => {
        if (mainCategory && offer.categorie.toLowerCase() !== mainCategory.toLowerCase()) {
            return false;
        }
        if (selectedSubcategories.length > 0) {
            if (!offer.tags || offer.tags.length === 0) {
                return false;
            }
            const lowerCaseTags = offer.tags.map(tag => tag.toLowerCase());
            return selectedSubcategories.some(selected => lowerCaseTags.includes(selected));
        }
        return true;
    });
    displayOffers(filteredOffers);
    updateMap(filteredOffers);
}


function createOfferCardElement(offer) {
    const element = document.getElementById('template-offer-card').content.cloneNode(true).firstElementChild;

    function get(cls) { return element.getElementsByClassName(cls).item(0); }

    const imagePrincipale = get('offer-image-principale');
    imagePrincipale.src = getImageFilename(offer.id_image_principale);

    const titre = get('titre');
    titre.href = '/autres_pages/detail_offre.php?id' + offer.id;
    titre.textContent = offer.titre;

    get('location').textContent = offer.formatted_address;
    get('offer-resume').textContent = offer.resume;
    get('category').textContent = offer.categorie;

    const prix_min = get('offer-prix-min');
    if (offer.prix_min) prix_min.textContent = offer.prix_min;
    else prix_min.parentElement.remove();

    get('offer-note').textContent = offer.note_moyenne;
    get('offer-creee-le').textContent = new Date(offer.creee_le).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });

    const lat = get('offer-prix-min');
    if (offer.lat) parseFloat(lat.textContent) = offer.lat;

    const long = get('offer-prix-min');
    if (offer.long) parseFloat(long.textContent) = offer.long;

    return element;
}

function displayOffers(offersToDisplay = offers) {
    const offerList = document.getElementsByClassName('offer-list').item(0);
    offerList.innerHTML = ''; // Réinitialisation avant de commencer à ajouter les éléments
    offersToDisplay.forEach(offer => offerList.appendChild(createOfferCardElement(offer)));
}

const sortButtons = document.querySelectorAll('.btn-sort');
sortButtons.forEach(button => {
    button.addEventListener('click', () => {
        sortButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        const criteria = button.dataset.criteria;
        const ascending = button.dataset.order === 'asc';
        sortOffers(criteria, ascending);
    });
});

document.getElementById('sort-price-up').addEventListener('click', () => sortOffers('prix_min', false));
document.getElementById('sort-price-down').addEventListener('click', () => sortOffers('prix_min', true));
document.getElementById('sort-rating-up').addEventListener('click', () => sortOffers('note_moyenne', false));
document.getElementById('sort-rating-down').addEventListener('click', () => sortOffers('note_moyenne', true));
document.getElementById('sort-date-up').addEventListener('click', () => sortOffers('creee_le', false));
document.getElementById('sort-date-down').addEventListener('click', () => sortOffers('creee_le', true));
document.getElementById('main-category').addEventListener('change', showSubcategories);

function getImageFilename(id_image) {
    return `/images_utilisateur/${id_image}.${images[id_image].mime_subtype}`;
}


//debut carte

let map = L.map('map').setView([48.8566, 2.3522], 12); // Centré sur Paris

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

let markersLayer = L.layerGroup().addTo(map);
L.marker([48.8566, 2.3522]).addTo(map)
    .bindPopup('A pretty CSS popup.<br> Easily customizable.')
    .openPopup();
    
function updateMap(offersToDisplay) {
    markersLayer.clearLayers(); // Efface les anciens marqueurs
    offersToDisplay.forEach(offer => {
        if (offer.lat && offer.long) {
            let marker = L.marker([offer.lat, offer.long])
                .bindPopup(`<b>${offer.titre}</b><br>${offer.formatted_address}`)
                .addTo(markersLayer);
        }
    });
}

// document.addEventListener('DOMContentLoaded', function () {
//     // Création de la carte centrée sur la France
//     let map = L.map('map').setView([46.603354, 1.888334], 6);

//     // Ajouter la couche de tuiles OpenStreetMap
//     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//         attribution: '&copy; OpenStreetMap contributors'
//     }).addTo(map);

   
// });


// fin carte