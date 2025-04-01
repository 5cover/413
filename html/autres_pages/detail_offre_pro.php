<?php

use ValueObjects\LibelleAbonnement;

require_once 'auth.php';
require_once 'component/ImageView.php';
require_once 'component/InputOffre.php';
require_once 'component/Page.php';
require_once 'component/ReviewList.php';
require_once 'DB/db.php';

require_once 'Kcrf/AdresseFast.php';
require_once 'Kcrf/ImageFast.php';
require_once 'Kcrf/OffreFast.php';
require_once 'Parsedown.php';
require_once 'redirect.php';
require_once 'util.php';

$offre = OffreFast::from_db(getarg($_GET, 'id', arg_int()));
if ($offre === false) fail_404();

Auth\exiger_connecte_pro();

$page = new Page($offre->titre, scripts: [
    'module/carousel.js'         => 'type="module"',
    'module/detail_offre_pro.js' => 'type="module"',
]);

if ($_POST) {
    $offre->alterner_etat();
    redirect_to($_SERVER['REQUEST_URI']);
}

$review_list = new ReviewList($offre);

$page->put(function () use ($offre, $review_list) {
    ?>
    <section class="bandeau-etat <?= $offre->en_ligne ? 'vert' : 'rouge' ?>">
        <p class="etat"><?= $offre->en_ligne ? 'Offre en ligne' : 'Offre hors ligne' ?></p>
        <button type="button" class="bouton" id="alternateButton">
            <?= $offre->en_ligne ? 'Mettre hors ligne' : 'Mettre en ligne' ?>
        </button>
        <form id="toggleForm" method="post" style="display: inline;">
            <button type="submit" name="valider" class="bouton" id="validateButton" disabled>Valider</button>
        </form>
        <a class="bouton modifier" href="<?= h14s(location_modifier_offre($offre)) ?>">Modifier</a>
        <button id="button-delete-offer" class="bouton">Supprimer</button>
        <?php if ($offre->libelle_abonnement !== LibelleAbonnement::Gratuit) { ?>
            <a id="a-facturation" - class="btn-more-info bouton_principale_pro" href="<?= h14s(location_facturation($offre->id)) ?>">Facturation</a>
        <?php } ?>
    </section>

    <section class="offer-details">
        <section class="offer-main-photo">
            <div class="carousel-container">
                <div class="carousel">
                    <!-- Image principale -->
                    <div class="carousel-slide">
                        <?php (new ImageView(ImageFast::from_db($offre->id_image_principale)))->put_img() ?>
                    </div>

                    <!-- Galerie d'images -->
                    <?php foreach (ImageFast::get_galerie($offre->id) as $image): ?>
                        <div class="carousel-slide">
                            <?php (new ImageView($image))->put_img() ?>
                        </div>
                    <?php endforeach ?>
                </div>

                <!-- Boutons de navigation -->
                <button class="carousel-prev" aria-label="Image précédente">❮</button>
                <button class="carousel-next" aria-label="Image suivante">❯</button>
            </div>
        </section>

        <div class="offer-info text">
            <h2><?= h14s($offre->titre) ?></h2>
            <?= (new Parsedown())->text($offre->description_detaillee) ?>
        </div>

    </section>

    <!-- Location -->
    <section class="offer-location">
        <h3>Emplacement et coordonnées</h3>
        <!-- <div id="map" class="map"></div> -->
        <div class="contact-info">
            <p><strong>Adresse&nbsp;:</strong> <?= h14s(AdresseFast::from_db($offre->id_adresse)->format()) ?></p>
            <?php if ($offre->url_site_web) { ?>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= h14s($offre->url_site_web) ?>"><?= h14s($offre->url_site_web) ?></a></p>
            <?php } ?>
        </div>
    </section>

    <?php $review_list->put() ?>

    <script>
        // // OpenStreetMap Integration
        // var map = L.map('map').setView([48.779, -3.518], 13);
        // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        // }).addTo(map);
        // L.marker([48.779, -3.518]).addTo(map)
        //     .bindPopup('Découverte interactive de la cité des Télécoms')
        //     .openPopup();
        // L.marker([45.779, -3.518]).addTo(map)
        //     .bindPopup('hihihihihihihihihui')
        // L.marker([45.779, -4.518]).addTo(map)
        //     .bindPopup('hihihihihihihihihui')
    </script>
    <?php
});
