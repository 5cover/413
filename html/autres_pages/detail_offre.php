<?php
require_once 'auth.php';
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
require_once 'model/Membre.php';

$offre = notfalse(Offre::from_db(getarg($_GET, 'id', arg_int())));

$page = new Page($offre->titre, scripts: [
    'module/detail_offre.js' => 'type="module"',
    'carousel.js' => 'defer',
]);

$input_rating = new InputNote(name: 'rating');
if ($offre instanceof Restaurant) {
    $input_note_cuisine = new InputNote(name: 'note_cuisine');
    $input_note_service = new InputNote(name: 'note_service');
    $input_note_ambiance = new InputNote(name: 'note_ambiance');
    $input_note_qualite_prix = new InputNote(name: 'note_qualite_prix');
} else {
    $input_note_cuisine = null;
    $input_note_service = null;
    $input_note_ambiance = null;
    $input_note_qualite_prix = null;
}

$id_membre_co = Auth\id_membre_connecte();
$review_list = new ReviewList($offre);


$is_reporting = false;
if (isset($_POST['report_open']) || isset($_POST['submit_report'])) {
    $is_reporting = true;
}

if ($_POST && isset($_POST['submit_report'])) {
    $offer_id = getarg($_POST, 'offer_id');
    $report_message = getarg($_POST, 'report_message');

    $report_message = trim($report_message);

    // Validation du formulaire
    if (!$report_message) {
        $error_message = "Le message de signalement ne peut pas être vide.";
    } else {
        location_signaler($id_membre_co, $offre->id, $report_message);
    }
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
        $avis = $offre instanceof Restaurant
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

$page->put(function () use ($offre, $input_rating, $input_note_cuisine, $input_note_service, $input_note_ambiance, $input_note_qualite_prix, $review_list, $is_reporting, $id_membre_co) {
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
                <p><strong>Adresse&nbsp;:</strong> <?= $offre->adresse->format() ?></p>
                <?php if ($offre->url_site_web) { ?>
                    <p><strong>Site web&nbsp;:</strong> <a href="<?= $offre->url_site_web ?>"><?= $offre->url_site_web ?></a></p>
                <?php } ?>
            </div>
        </section>

        <!-- Formulaire d'avis -->
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
                    <option value="affaires">Affaires</option>
                    <option value="couple">Couple</option>
                    <option value="solo">Solo</option>
                    <option value="famille">Famille</option>
                    <option value="amis">Amis</option>
                </select>
                <label for="date">Date de votre visite</label>
                <input type="date" id="date" name="date" min="<?= $offre->creee_le->format_date() ?>" value="<?= date('Y-m-d') ?>" required>
                <br>
                <label for="consent">Je certifie que l'avis reflète mes propres expérience et opinion sur cette offre.</label>
                <input type="checkbox" name="consent" required>
                <button type="submit" class="btn-publish">Publier</button>
            </form>
        </div>

        <?php $review_list->put() ?>

        <?php if (!$is_reporting): ?>
            <form method="post">
                <input type="hidden" name="report_open" value="1">
                <button type="submit" class="btn-report">Signaler un problème</button>
            </form>
        <?php endif; ?>

        <?php if (($is_reporting) && ($id_membre_co !== null)): ?>
            <div class="report-form">
                <h3>Signaler un problème</h3>
                <form method="post">
                    <textarea name="report_message" placeholder="Décrivez le problème..." required></textarea>
                    <input type="hidden" name="offer_id" value="<?= $offre->id ?>">
                    <button type="submit" name="submit_report" class="btn-submit">Envoyer</button>
                    <button type="submit" name="cancel_report" class="btn-cancel">Annuler</button>
                </form>
                <?php if (isset($error_message)): ?>
                    <p class="error"><?= h14s($error_message) ?></p>
                <?php elseif (isset($success_message)): ?>
                    <p class="success"><?= h14s($success_message) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </section>
    <?php
});
