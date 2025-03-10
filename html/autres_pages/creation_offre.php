<?php

require_once 'auth.php';
require_once 'util.php';
require_once 'component/Page.php';
require_once 'component/InputOffre.php';

$page = new Page(
    "Création d'une offre",
    ['input-offre.css'],
    ['module/input-offre.js' => 'defer type="module"']
);

$categorie = getarg($_GET, 'categorie', arg_check(f_is_in(array_keys(CATEGORIES_OFFRE))));

$input_offre = new InputOffre(
    $categorie,
    Professionnel::from_db(Auth\exiger_connecte_pro()),
    form_id: 'f',
);

if ($_POST) {
    $offre = $input_offre->get($_POST);

    if ($offre === null) {
        redirect_to('?categorie=' . urlencode($categorie) . '&error=' . urlencode('Une erreur a eu lieu. Veuillez réessayer.'));
    }

    DB\transaction(fn() => $offre->push_to_db());

    $offre->image_principale->move_uploaded_image();
    foreach ($offre->galerie->images as $img) {
        $img->move_uploaded_image();
    }

    // Rediriger vers la page de détaille de l'offre en cas de succès.
    // En cas d'échec, l'exception est jetée par DB\transaction(), donc on atteint pas cette ligne.
    redirect_to(location_detail_offre_pro($offre->id));
}

$page->put(function () use ($input_offre) {
    ?>
    <section class="centrer-enfants">
    <?php
        if ($error = $_GET['error'] ?? null) {
            ?>
            <p class="error"><?= h14s($error) ?></p><?php
        }
        ?>
        <?php $input_offre->put() ?>
        
        <form id="<?= h14s($input_offre->form_id) ?>" method="post" enctype="multipart/form-data">
            <button class="btn-publish" type="submit">Valider</button>
        </form>
    </section>
    <?php
});