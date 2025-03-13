<?php
require_once 'component/Page.php';
require_once 'component/CarteOffre.php';
require_once 'model/Offre.php';
require_once 'cookie.php';

$page = new Page('Accueil');
$page->put(function () {
    ?>
    <section class="search-section">
        <h1>Accueil</h1>
        <br>
        <form action="recherche.php" name="barre_de_recherche" method="post" class="search-bar">
            <input type="text" name="search" placeholder="Entrez des mots-clés de recherche ici (ex: restaurant)">
            <button class="searchbutton" type="submit" name="valider">Recherche</button>
        </form>
    </section>
    <?php if (Cookie\RecentOffers::get()) { ?>
    <section class="highlight-offers"> <!-- todo: rename this class to something more generic -->
        <h2>Consultations récentes</h2>
        <div class="offer-list">
        <?php
        foreach (Cookie\RecentOffers::get() as $id_offre) {
            $offre = Offre::from_db($id_offre);
            if ($offre !== false) (new CarteOffre($offre))->put();
        }
        ?>
        </div>
    </section>
    <?php } ?>
    <section class="highlight-offers">
        <h2>Offres à la une</h2>
        <div class="offer-list">
            <?php

            $offres = Offre::from_db_a_la_une_ordered();

            // Préparer et exécuter la requête SQL pour récupérer toutes les offres

            // Boucler sur les résultats pour afficher chaque offre
            foreach ($offres as $offre) {
                (new CarteOffre($offre))->put();
            }
            ?>
        </div>
    </section>
    <section class="highlight-offers">
        <h2>Nouveautés</h2>
        <div class="offer-list">
            <?php
            foreach (Offre::from_db_nouveautes() as $offre) {
                (new CarteOffre($offre))->put();
            }
            ?>
        </div>
    </section>
    <section class="online-offers">
        <h2>Offres en ligne</h2>
        <div class="offer-list">
        <?php
        foreach (Offre::from_db_en_ligne_ordered() as $offre) {
            (new CarteOffre($offre))->put();
        }
        ?>
        </div>
    </section>
    <?php
});
