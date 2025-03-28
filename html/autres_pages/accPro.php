<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'component/CarteOffrePro.php';
require_once 'model/Professionnel.php';
require_once 'model/ProfessionnelPrive.php';
require_once 'model/OffreFast.php';
require_once 'util.php';

$page = new Page('Accueil Professionnel');

$page->put(function () {
    $id_professionnel = Auth\exiger_connecte_pro();

    $pro = Professionnel::from_db($id_professionnel);

    $nb_offres = OffreFast::count($id_professionnel);
    $nb_offres_en_ligne = OffreFast::count($id_professionnel, en_ligne: true);
    ?>

    <h1>Accueil Professionnel</h1>
    <a class="btn-more-info bouton_principale_pro" href="<?= h14s(location_creation_offre()) ?>"  id='bouton_creer_offre' >Créer une offre</a>
    <?php if ($pro instanceof ProfessionnelPrive) { ?>
        <a class="btn-more-info bouton_principale_pro" href="<?= h14s(location_facturation()) ?>">Facturation</a>
    <?php } ?>

    <h3 class="nb-offres"><?= $nb_offres ?> offres</h3>
    <section class="online-offers">
        <h2>Mes offres en ligne</h2>
        <p>Vos offres actuellement disponibles en ligne&nbsp;: <?= $nb_offres_en_ligne ?></p>

        <div class="offer-list">
            <?php
            foreach (OffreFast::from_db_all_ordered($id_professionnel, en_ligne: true) as $offre) {
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
            foreach (OffreFast::from_db_all_ordered($id_professionnel, en_ligne: false) as $offre) {
                (new CarteOffrePro($offre))->put();
            }
            ?>
        </div>
    </section>

    <!-- Bouton pour créer une nouvelle offre -->
    <a class="btn-more-info bouton_principale_pro" href="<?= h14s(location_creation_offre()) ?>"  id='bouton_creer_offre' >Créer une offre</a>

    <?php
});