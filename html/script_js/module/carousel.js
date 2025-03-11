const carousel = document.querySelector('.carousel');
const slides = document.querySelectorAll('.carousel-slide');
const nextButton = document.querySelector('.carousel-next');
const prevButton = document.querySelector('.carousel-prev');

let currentSlide = 0;

// Fonction pour mettre à jour la position du carrousel
function updateCarousel() {
    const offset = -currentSlide * 100;
    carousel.style.transform = `translateX(${offset}%)`;
}

// Bouton suivant
nextButton.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % slides.length;
    updateCarousel();
});

// Bouton précédent
prevButton.addEventListener('click', () => {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateCarousel();
});

// Initialisation
updateCarousel();