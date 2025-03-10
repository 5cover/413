<?php

require_once 'auth.php';
require_once 'util.php';
require_once 'component/Page.php';
require_once 'component/InputOffre.php';
require_once 'model/ProfessionnelPrive.php';
require_once 'model/Offre.php';

$page = new Page(
    'Modifier offre',
    ['input-offre.css'],
    ['module/input-offre.js' => 'defer type="module"']
);

$categorie = getarg($_GET, 'categorie', arg_check(f_is_in(array_keys(CATEGORIES_OFFRE))));
$offre = notfalse(Offre::from_db($id_offre = getarg($_GET, 'id', arg_int())));

$input_offre = new InputOffre(
    $categorie,
    Professionnel::from_db(Auth\exiger_connecte_pro()),
    form_id: 'f',
);

if ($_POST) {
    $offre = $input_offre->get($_POST, $offre);

    if ($offre === null) {
        redirect_to('?categorie=' . urlencode($categorie) . '&error=' . urlencode('Une erreur a eu lieu. Veuillez réessayer.'));
    }

    $offre->push_to_db();

    // todo: modifier images

    redirect_to(location_detail_offre_pro($offre->id));
}

$page->put(function () use (
    $input_offre,
    $offre
) {
    if ($error = $_GET['error'] ?? null) {
        ?>
        <p class="error"><?= h14s($error) ?></p><?php
    }
    $input_offre->put($offre);
    ?>

    <form id="f" method="post" enctype="multipart/form-data">
        <button type="submit" class="btn-publish">Valider</button>
    </form>
    <?php
});