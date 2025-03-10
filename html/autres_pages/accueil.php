<?php
require_once 'component/Page.php';
require_once 'component/CarteOffre.php';
require_once 'model/Offre.php';

$page = new Page('Accueil');
$page->put(function () {
    ?>
    <!-- Section de recherche -->
    <section class="search-section">
        <h1>Accueil</h1>
        <br>
        <form action="recherche.php" name="barre_de_recherche" method="post" class="search-bar">
            <input type="text" name="search" placeholder="Entrez des mots-clés de recherche ici (ex: restaurant)">
            <input class="searchbutton" type="submit" name="valider" value="Recherche">
        </form>
    </section>

    <!-- Section des offres à la une -->
    <section class="highlight-offers">
        <h2>Offres à la une</h2>
        <div class="offer-list">
            <?php

            $offres = Offre::from_db_a_la_une();

            // Préparer et exécuter la requête SQL pour récupérer toutes les offres
        
            // Boucler sur les résultats pour afficher chaque offre
            foreach ($offres as $offre) {
                (new CarteOffre($offre))->put();
            }
            ?>
        </div>
    </section>

    <!-- Section des offres en ligne -->
    <section class="online-offers">
        <h2>Offres en ligne</h2>
        <div class="offer-list">
            <?php

            $offres = Offre::from_db_en_ligne();

            // Préparer et exécuter la requête SQL pour récupérer toutes les offres
        
            // Boucler sur les résultats pour afficher chaque offre
            foreach ($offres as $offre) {
                (new CarteOffre($offre))->put();
            }
            ?>
        </div>
    </section>
    <?php
});
