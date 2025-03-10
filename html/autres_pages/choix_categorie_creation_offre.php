<?php
require_once 'const.php';
require_once 'component/Page.php';
$page = new Page("Choix de l'offre", ['choix_categorie_creation_offre.css']);
$page->put(function () {
    ?>
    <form action="creation_offre.php" method="get">
        <p>Choisissez la catégorie de votre offre</p>
        <?php foreach (array_keys(CATEGORIES_OFFRE) as $categorie) { ?>
            <button type="submit" name="categorie" value="<?= $categorie ?>"><?= ucfirst($categorie) ?></button>
        <?php } ?>
    </form>
    <?php
});