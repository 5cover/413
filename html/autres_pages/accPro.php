<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'queries/offre.php';
require_once 'component/CarteOffrePro.php';

$page = new Page('Accueil Professionnel');

$id_professionnel = Auth\exiger_connecte_pro();

$nb_offres = DB\query_offres_count($id_professionnel);
$nb_offres_en_ligne = DB\query_offres_count($id_professionnel, en_ligne: true)
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>

        <h1>Accueil Professionnel</h1>
        <a class="btn-more-info" href="<?= location_creation_offre() ?>" id='bouton_creer_offre'>Créer une offre</a>

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
                foreach($offres_hors_ligne as $offre) {
                    (new CarteOffrePro($offre))->put();
                }
                ?>
            </div>
        </section>

        <!-- Bouton pour créer une nouvelle offre -->
        <a href="choix_categorie_creation_offre.php">
            <div class="create-offer">
                <button class="btn-create">Créer une offre</button>
            </div>
        </a>
    </main>
    <?php $page->put_footer() ?>
</body>

</html>
