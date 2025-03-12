<?php
require_once 'component/Page.php';
require_once 'component/CarteOffre.php';


$page = new Page('Recherche',stylesheets: ["https://unpkg.com/leaflet/dist/leaflet.css"] ,scripts: [
    'https://unpkg.com/leaflet/dist/leaflet.js' => 'defer',
    'tri_recherche.js' => 'defer',
]);

$page->put(function () {
    $valider = getarg($_GET, "valider", required: false);
    $search = getarg($_GET, "search", required: false);
    $modif_affichage = false;

    if ($valider && !empty($search)) {
        $modif_affichage = true;
        $search = getarg($_GET, "search");
    }

    if ($_POST) {
        $search = getarg($_POST, 'search', required: false);
        if (!$search) {
            $search = null;
        }
    }
    ?>
    <section class="map-section">
        <div class="header-carte">
            <h2>Carte des offres :</h2>
            <button class="droite btn-creer" onclick="toggleMap()" id="mapToggle">Afficher la arte</button>
        </div>
        <div id="map" class=""></div>
    </section>

    <section class="search-section">
        <h1>Recherche</h1>
        <br>
        <div class="search-bar">
            <!-- <input id="barre-recherche" type="text" placeholder="Rechercher des activités, restaurants, spectacles..."> -->
            <input type="text" id="keyword-search" value="<?= $search ?>" placeholder="Rechercher par mot-clé" oninput="filterOffers()">

        </div>
    </section>
    <article class="cote">
        <div class="criteres">
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
                            <option value="parc_d_attraction">Parc d'attractions</option>
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
        </div>
        <section class="highlight-offers resultat">
            <h2>Offres trouvées :</h2>
            <div class="offer-list">
                
            </div>
        </section>
    </article>
    <!-- ici -->
    <template id="template-offer-card"><?php CarteOffre::put_template() ?></template>
    
    


    
    <?php
});