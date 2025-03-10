<?php

use function Auth\id_membre_connecte;

require_once 'auth.php';
require_once 'const.php';
require_once 'Parsedown.php';
require_once 'component/ImageView.php';
require_once 'component/InputNote.php';
require_once 'component/Page.php';
require_once 'db.php';
require_once 'model/Avis.php';
require_once 'component/ReviewList.php';
require_once 'model/AvisRestaurant.php';
require_once 'model/Offre.php';
require_once 'model/Restaurant.php';
require_once 'model/Date.php';
require_once 'util.php';
require_once 'model/Membre.php';

$offre = notfalse(Offre::from_db(getarg($_GET, 'id', arg_int())));

$page = new Page($offre->titre, scripts: [
    'module/detail_offre.js' => 'type="module"',
    'carousel.js'            => 'defer',
]);

$input_rating = new InputNote(name: 'rating');
if ($offre instanceof Restaurant) {
    $input_note_cuisine      = new InputNote(name: 'note_cuisine');
    $input_note_service      = new InputNote(name: 'note_service');
    $input_note_ambiance     = new InputNote(name: 'note_ambiance');
    $input_note_qualite_prix = new InputNote(name: 'note_qualite_prix');
} else {
    $input_note_cuisine      = null;
    $input_note_service      = null;
    $input_note_ambiance     = null;
    $input_note_qualite_prix = null;
}

$id_membre_co = Auth\id_membre_connecte();
$review_list  = new ReviewList($offre);

if (null !== $report_message = getarg($_POST, 'report_message', required: false)) {
    $ch = curl_init(location_signaler($id_membre_co, $offre->id, $report_message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    //$ok = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
    curl_close($ch);
}

// Si on a un POST de publication d'avis
if (null !== $commentaire = getarg($_POST, 'commentaire', required: false)) {
    if (null === $id_membre_co) {
        $error_message = 'Veuillez vous connecter pour publier un avis.';
    } else if (Avis::from_db_one($id_membre_co, $offre->id)) {
        $error_message = "Vous pouvez ne publier qu'un avis.";
    } else {
        $args_avis = [
            null,
            $commentaire,
            $input_rating->get($_POST),
            Date::parse(getarg($_POST, 'date')),
            getarg($_POST, 'contexte'),
            Membre::from_db($id_membre_co),
            $offre,
        ];
        $avis      = $offre instanceof Restaurant
            ? new AvisRestaurant(
                $args_avis,
                $input_note_cuisine->get($_POST),
                $input_note_service->get($_POST),
                $input_note_ambiance->get($_POST),
                $input_note_qualite_prix->get($_POST),
            )
            : new Avis(...$args_avis);
        $avis->push_to_db();
        $success_message = 'Avis ajouté avec succès !';
    }
}

$page->put(function () use ($offre, $input_rating, $input_note_cuisine, $input_note_service, $input_note_ambiance, $input_note_qualite_prix, $review_list, $id_membre_co) {
    ?>
    <section class="offer-details">
        <section class="offer-main-photo">
            <div class="carousel-container">
                <div class="carousel">
                    <!-- Image principale -->
                    <div class="carousel-slide">
                        <?php (new ImageView($offre->image_principale))->put_img() ?>
                    </div>

                    <!-- Galerie d'images -->
                    <?php foreach ($offre->galerie->images as $image): ?>
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

    <section class="offer-reviews">

        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <!-- <div id="map" class="map"></div> -->
            <div class="contact-info">
                <p><strong>Adresse&nbsp;:</strong> <?= h14s($offre->adresse->format()) ?></p>
                <?php if ($offre->url_site_web) { ?>
                    <p><strong>Site web&nbsp;:</strong> <a href="<?= h14s($offre->url_site_web) ?>"><?= h14s($offre->url_site_web) ?></a></p>
                <?php } ?>
            </div>
        </section>

        <!-- Formulaire d'avis -->
        <?php if (null !== $idmco = id_membre_connecte() and false === Avis::from_db_one($idmco, $offre->id)) { ?>
        <div class="review-form" id="review-form">
            <h3>Laisser un avis</h3><br>
            <div class="message">
                <?php if (isset($error_message)): ?>
                    <p class="error"><?= h14s($error_message) ?></p>
                    <?php
        elseif (isset($success_message)):
            ?>
                    <p class="success"><?= h14s($success_message) ?></p>
                <?php endif ?>
            </div>
            <form method="post">
                <textarea name="commentaire" placeholder="Votre avis..." required></textarea>
                <label>Note&nbsp;: <?php $input_rating->put() ?></label>
                <?php if ($offre instanceof Restaurant) { ?>
                    <label>Note cuisine&nbsp;: <?php $input_note_cuisine->put() ?></label>
                    <label>Note service&nbsp;: <?php $input_note_service->put() ?></label>
                    <label>Note ambiance&nbsp;: <?php $input_note_ambiance->put() ?></label>
                    <label>Note qualité prix&nbsp;: <?php $input_note_qualite_prix->put() ?></label>
                <?php } ?>
                <label for="contexte">Contexte&nbsp;:</label>
                <select name="contexte" id="contexte" required>
                    <?php foreach (CONTEXTES_VISITE as $ctx) { ?>
                        <option value="<?= h14s($ctx) ?>"><?= h14s(ucfirst($ctx)) ?></option>
                    <?php } ?>
                </select>
                <label for="date">Date de votre visite</label>
                <input type="date" id="date" name="date" min="<?= h14s($offre->creee_le->format_date()) ?>" value="<?= h14s(date('Y-m-d')) ?>" required>
                <br>
                <label for="consent">Je certifie que l'avis reflète mes propres expérience et opinion sur cette offre.</label>
                <input type="checkbox" name="consent" required>
                <button type="submit" class="btn-publish">Publier</button>
            </form>
        </div>

        <?php }
    $review_list->put() ?>

        <details class="report-form">
            <summary>Signaler un problème</summary>
            <?php if ($id_membre_co !== null) {
                $signalement_actuel = Signalable::signalable_from_db($offre->id)->get_signalement($id_membre_co) ?>
                <form method="post">
                    <textarea name="report_message" placeholder="Décrivez le problème&hellip;" required <?= $signalement_actuel === null ? '' : 'readonly' ?>><?= h14s($signalement_actuel) ?></textarea>
                    <button type="submit" name="submit_report" class="btn-publish"><?= $signalement_actuel === null ? 'Envoyer' : 'Supprimer' ?></button>
                </form>
                <?php if (isset($error_message)) { ?>
                    <p class="error"><?= h14s($error_message) ?></p>
                <?php } elseif (isset($success_message)) { ?>
                    <p class="success"><?= h14s($success_message) ?></p>
                <?php } ?>
            <?php } else { ?>
                <h3>Veuillez vous connecter à un compte membre pour signaler un problème</h3>
                <p>Connectez-vous pour nous aider à améliorer nos services en signalant les problèmes rencontrés.</p>
                <a href="<?= h14s(location_connexion(return_url: $_SERVER['REQUEST_URI'])) ?>" class="btn-login btn-publish">Se connecter</a>
            <?php } ?>
        </details>

    </section>
    <?php
});
