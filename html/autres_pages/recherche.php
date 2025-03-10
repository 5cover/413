<?php
require_once 'component/Page.php';
require_once 'component/CarteOffre.php';
require_once 'util.php';

$page = new Page('Recherche', scripts: [
    'tri_recherche.js' => 'defer', 
    'https://unpkg.com/leaflet/dist/leaflet.js' => 'defer',
]);

$page->put(function () {
    $search = getarg($_GET, "search", required: false);
    ?>
    <section class="search-section">
        <h1>Recherche</h1>
        <br>
        <div class="search-bar">
            <!-- <input id="barre-recherche" type="text" placeholder="Rechercher des activités, restaurants, spectacles..."> -->
            <input type="text" id="keyword-search" value="<?= h14s($search) ?>" placeholder="Rechercher par mot-clé" oninput="filterOffers()">

        </div>
    </section>

    <section class="tag-selection">
        <div class="categories">
            <h3>Catégories</h3>
            <div class="category-dropdown">
                <select id="main-category" onchange="showSubcategories()">
                    <option value="">-- Toutes les catégories --</option>
                    <option value="restaurant">Restauration</option>
                    <option value="activité">Activité</option>
                    <option value="visite">Visite</option>
                    <option value="spectacle">Spectacle</option>
                    <option value="parc_d_attractions">Parc d'attractions</option>
                </select>
            </div>
        </div>
        <input type="hidden" id="selected-category" name="category" value="">
        <div id="subcategories" class="hidden">
            <h3>Tags</h3>
            <div class="subcategory-list" id="subcategory-list">

            </div>
        </div>
    </section>
    <section class="sorting-section">
        <br>
        <h3>Options de tri</h3>
        <div class="sorting-buttons">
            <button id="sort-price-down" class="btn-sort" data-criteria="prix">Prix croissant</button>
            <button id="sort-price-up" class="btn-sort" data-criteria="prix">Prix décroissant</button>
            <button id="sort-rating-down" class="btn-sort" data-criteria="note">Note croissante</button>
            <button id="sort-rating-up" class="btn-sort" data-criteria="note">Note décroissante</button>
            <button id="sort-date-up" class="btn-sort" data-criteria="date">Plus récent</button>
            <button id="sort-date-down" class="btn-sort" data-criteria="date">Moins récent</button>
        </div>
    </section>
    <section class="highlight-offers">
        <h2>Offres trouvées :</h2>
        <div class="offer-list">

        </div>
    </section>
    <section class="map-section">
        <h2>Carte des offres :</h2>
        <div id="map"></div>
    </section>
    <?php 
    
    ?>

    <template id="template-offer-card"><?php CarteOffre::put_template() ?></template>
    <?php
});