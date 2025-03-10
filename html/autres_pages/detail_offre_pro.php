<?php
require_once 'Parsedown.php';
require_once 'db.php';
require_once 'util.php';
require_once 'redirect.php';
require_once 'model/Offre.php';
require_once 'component/ReviewList.php';
require_once 'component/Page.php';
require_once 'component/ImageView.php';
require_once 'component/InputOffre.php';

Auth\exiger_connecte_pro();

$offre = notfalse(Offre::from_db(getarg($_GET, 'id', arg_int())));

$page = new Page($offre->titre, scripts: ['carousel.js' => 'defer', 'module/detail_offre_pro.js' => 'type="module"']);

if ($_POST) {
    $offre->alterner_etat();
    redirect_to($_SERVER['REQUEST_URI']);
}

$review_list = new ReviewList($offre);

$page->put(function () use ($offre, $review_list) {
    ?>
    <section class="modif">
        <div class="bandeau-etat <?= $offre->en_ligne ? 'vert' : 'rouge' ?>">
            <p class="etat"><?= $offre->en_ligne ? 'Offre en ligne' : 'Offre hors ligne' ?></p>
            <button type="button" class="bouton" id="alternateButton">
                <?= $offre->en_ligne ? 'Mettre hors ligne' : 'Mettre en ligne' ?>
            </button>
            <form id="toggleForm" method="post" style="display: inline;">
                <button type="submit" name="valider" class="bouton" id="validateButton" disabled>Valider</button>
            </form>
            <a class="bouton modifier" href="modif_offre.php?id=<?= $offre->id ?>&categorie=<?= $offre->categorie ?>">Modifier</a>
        </div>
    </section>

    <section class="offer-details">
        <section class="offer-main-photo">
            <div class="carousel-container">
                <div class="carousel">
                    <div class="carousel-slide">
                        <?php (new ImageView($offre->image_principale))->put_img() ?>
                    </div>
                    <div class="carousel-slide">
                        <?php (new ImageView($offre->image_principale))->put_img() ?>
                    </div>

                    <!-- Galerie d'images -->
                    <?php foreach ($offre->galerie as $image): ?>
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
            <p><strong>Adresse&nbsp;:</strong> <?= $offre->adresse->format() ?></p>
            <?php if ($offre->url_site_web) { ?>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= $offre->url_site_web ?>"><?= $offre->url_site_web ?></a></p>
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
