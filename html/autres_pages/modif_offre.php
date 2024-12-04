<?php

require_once 'auth.php';
require_once 'util.php';
require_once 'component/Page.php';
require_once 'component/InputOffre.php';
require_once 'model/ProfessionnelPrive.php';

$page = new Page('Modifier offre',
    ['input-offre.css'],
    ['module/input-offre.js' => 'defer type="module"']);

$id_professionnel = Auth\exiger_connecte_pro();
$est_prive        = ProfessionnelPrive::exists($id_professionnel);

$categorie = getarg($_GET, 'categorie', arg_check(f_is_in(array_keys(CATEGORIES_OFFRE))));
$offre = notfalse(Offre::from_db($id_offre  = getarg($_GET, 'id', arg_int())));

$input_offre = new InputOffre(
    $categorie,
    Professionnel::from_db(Auth\exiger_connecte_pro()),
    form_id: 'f',
);

if ($_POST) {
    $offre = $input_offre->get($_POST, $offre);
    $offre->push_to_db();

    // todo: modifier images

    redirect_to(location_detail_offre_pro($offre->id));
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <?php $input_offre->put($offre) ?>
        
        <form id="f" method="post" enctype="multipart/form-data">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php $page->put_footer() ?>
    
</body>

</html>
