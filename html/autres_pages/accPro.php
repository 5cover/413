<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'component/CarteOffrePro.php';
require_once 'model/Offre.php';

$page = new Page('Accueil Professionnel');

$page->put(function () {
    $id_professionnel = Auth\exiger_connecte_pro();

    $nb_offres = Offre::count($id_professionnel);
    $nb_offres_en_ligne = Offre::count($id_professionnel, en_ligne: true);
    ?>

    <h1>Accueil Professionnel</h1>
    <a class="btn-more-info bouton_principale_pro" href="<?= location_creation_offre() ?>"  id='bouton_creer_offre' >Créer une offre</a>
    <a class="btn-more-info bouton_principale_pro" href="<?= location_facturation() ?>" >Facturation</a>

    <h3 class="nb-offres"><?= $nb_offres ?> offres</h3>
    <section class="online-offers">
        <h2>Mes offres en ligne</h2>
        <p>Vos offres actuellement disponibles en ligne&nbsp;: <?= $nb_offres_en_ligne ?></p>

        <div class="offer-list">
            <?php
            $offres_en_ligne = Offre::from_db_all($id_professionnel, en_ligne: true);
            foreach ($offres_en_ligne as $offre) {
                (new CarteOffrePro($offre))->put();
            }
            ?>
        </div>
    </section>

    <section class="offline-offers">
        <h2>Mes offres hors-ligne</h2>
        <p>Vos offres hors-ligne&nbsp;: <?= $nb_offres - $nb_offres_en_ligne ?> </p>

        <div class="offer-carousel">
            <?php
            $offres_hors_ligne = Offre::from_db_all($id_professionnel, en_ligne: false);
            foreach ($offres_hors_ligne as $offre) {
                (new CarteOffrePro($offre))->put();
            }
            ?>
        </div>
    </section>

    <!-- Bouton pour créer une nouvelle offre -->
    <a class="btn-more-info bouton_principale_pro" href="<?= location_creation_offre() ?>"  id='bouton_creer_offre' >Créer une offre</a>

    <?php
});