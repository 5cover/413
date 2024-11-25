<?php
require_once 'component/offre.php';
require_once 'component/Page.php';

$page = new Page('Accueil');
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Accueil</h1>
            <br>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des activités, restaurants, spectacles...">
                <a href="recherche.php">
                    <button class="btn-search">Rechercher</button>
                </a>
            </div>
        </section>

        <!-- Section des offres à la une -->
        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php
                // Préparer et exécuter la requête SQL pour récupérer toutes les offres
                $offres = DB\query_offres_a_une();

                // Boucler sur les résultats pour afficher chaque offre
                foreach ($offres as $offre) {
                    put_card_offre($offre);
                }
                ?>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>
</body>

</html>
