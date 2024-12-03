<?php
require_once 'auth.php';
require_once 'model/Offre.php';
require_once 'component/Page.php';
require_once 'component/ImageView.php';
require_once 'queries.php';

$args = [
    'id' => getarg($_GET, 'id', arg_int()),
];

if ($_POST) {
    $args += [
        'commentaire' => getarg($_POST, 'commentaire'),
        'date_avis'   => getarg($_POST, 'date'),
        'note'        => getarg($_POST, 'rating', arg_int()),
        'contexte'    => getarg($_POST, 'contexte'),
    ];
    if (($id_membre_co = Auth\id_membre_connecte()) === null) {
        $error_message = 'Veuillez vous connecter pour publier un avis.';
    } else {
        $querry = 'INSERT INTO pact.avis (id_membre_auteur,id_offre,commentaire,date_experience,note,contexte) VALUES (?,?,?,?,?,?);';
        $stmt   = DB\connect()->prepare($querry);
        $stmt->execute([
            $id_membre_co,
            $args['id'],
            $args['commentaire'],
            $args['date_avis'],
            $args['note'],
            $args['contexte']
        ]);
        $success_message = 'Avis ajouté avec succès !';
    }
}

$offre = Offre::from_db($args['id']);
if ($offre === false) {
    html_error("Pas d'offre n°{$args['id']}");
}
assert($offre->id === $args['id']);

$titre           = $offre->titre;
$description     = $offre->description_detaillee;
$site_web        = $offre->url_site_web;
$image_pricipale = $offre->image_principale;
$adresse         = $offre->adresse;

$galerie = DB\query_galerie($args['id']);
$avis    = DB\query_avis();

$page = new Page($titre,
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    [
        'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async',
        'carrousel.js'                                    => 'defer',
    ]);
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>
<body>
    <?php
    // TODO suprimmer ca quand romain aura sort that out
    // echo '<pre>';
    // print_r($galerie);
    // echo '</pre>';

    $page->put_header();
    ?>
    <main>
    <section class="offer-details">
        <h1 class="offer-title"><?= htmlspecialchars($titre) ?></h1>
        <div class="carousel-container">
            <div class="carousel">
                <div class="carousel-slide">
                    <?php (new ImageView($image_pricipale))->put_img() ?>
                </div>
                <?php foreach ($galerie as $id_image): ?>
                    <div class="carousel-slide">
                        <?php (new ImageView(Image::from_db($id_image)))->put_img() ?>
                    </div>
                <?php endforeach ?>
            </div>
            <button class="carousel-prev" aria-label="Image précédente">❮</button>
            <button class="carousel-next" aria-label="Image suivante">❯</button>
        </div>

        <p class="offer-description"><?= nl2br(htmlspecialchars($description)) ?></p>
    </section>
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <!-- <div id="map" class="map"></div> -->
            <div class="contact-info">
                <p><strong>Adresse&nbsp;:</strong> <?= $adresse->format() ?></p>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= $site_web ?>"><?= $site_web ?></a></p>
                <!-- <p><strong>Téléphone&nbsp;:</strong> 02 96 46 63 80</p> -->
            </div>
        </section>

        <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

            <!-- Formulaire d'avis -->
            <div class="review-form">
                <div class="message">
                    <?php if (isset($error_message)): ?>
                    <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
                    <?php elseif (isset($success_message)): ?>
                    <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
                    <?php endif ?>
                </div>
                <form method="post" action="detail_offre.php?id=<?= $args['id'] ?>">
                    <textarea name="commentaire" placeholder="Votre avis..." required></textarea>
                    <label for="rating">Note&nbsp;:</label>
                    <select name="rating" id="rating" required>
                        <option value="5">5 étoiles</option>
                        <option value="4">4 étoiles</option>
                        <option value="3">3 étoiles</option>
                        <option value="2">2 étoiles</option>
                        <option value="1">1 étoile</option>
                    </select>
                    <label for="contexte">Contexte&nbsp;:</label>
                    <select name="contexte" id="contexte" required>
                        <option value="affaires">Affaires</option>
                        <option value="couple">Couple</option>
                        <option value="solo">Solo</option>
                        <option value="famille">Famille</option>
                        <option value="amis">Amis</option>
                    </select>
                    <label for="date">Date de votre visite</label>
                    <input type="date" id="date" name="date" required>
                    </br>
                    <label for="consent">Je certifie que l'avis reflète mes propres expérience et opinion sur cette offre.</label>
                    <input type="checkbox" name="consent" required>
                    <button type="submit" class="btn-publish">Publier</button>
                </form>
            </div>

            <!-- Liste des avis -->
            <div class="review-list">
                <h4>Avis de la communauté</h4>
                <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= DB\query_avis_count($args['id']) ?></p>
                <p>Moyenne&nbsp;: <?php if ($offre->note_moyenne !== null) { echo round($offre->note_moyenne, 2); } else { echo 0; } ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php $avis = DB\query_avis(id_offre: $offre->id) ?>
                    <p>5 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 5)) ?> avis.</p>
                    <p>4 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 4)) ?> avis.</p>
                    <p>3 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 3)) ?> avis.</p>
                    <p>2 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 2)) ?> avis.</p>
                    <p>1 étoile&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 1)) ?> avis.</p>
                </div>
                <?php if (!empty($avis)) {
                    foreach ($avis as $avis_temp) { ?>
                        <div class="review">
                            <p><strong><?= htmlspecialchars($avis_temp['pseudo_auteur']) ?></strong> - <?= htmlspecialchars($avis_temp['note']) ?>/5</p>
                            <p class="review-contexte">Contexte&nbsp;: <?= htmlspecialchars($avis_temp['contexte']) ?></p>
                            <p><?= htmlspecialchars($avis_temp['commentaire']) ?></p>
                            <p class="review-date"><?= htmlspecialchars($avis_temp['date_experience']) ?></p>
                            <?php if (($id_membre_co = Auth\id_membre_connecte()) !== null && $avis_temp['id_membre_auteur'] === $id_membre_co) { ?>
                            <form method="post" action="/avis/modifier.php?avis_id=<?= $avis_temp['id'] ?>&offre=<?= $args['id'] ?>">
                                <button type="submit" class="btn-modif">Modifier</button>
                                <button type="submit" name="action" value="supprimer">Supprimer</button>
                            </form>
                            <?php } ?> 
                        </div>
                    <?php }
                } else { ?>
                    <p>Aucun avis pour le moment. Soyez le premier à en écrire un&nbsp;!</p>
                <?php } ?>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>
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
</body>

</html>
