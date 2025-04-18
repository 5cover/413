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

    if ($valider && !empty($search)) {
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
            <button class="droite btn-creer" id="mapToggle">Afficher la arte</button>
        </div>
        <div id="map" class=""></div>
    </section>

    <section class="search-section">
        <h1>Recherche</h1>
        <br>
        <div class="search-bar">
            <!-- <input id="barre-recherche" type="text" placeholder="Rechercher des activités, restaurants, spectacles..."> -->
            <input type="text" id="keyword-search" value="<?= $search ?>" placeholder="Rechercher par mot-clé">

        </div>
    </section>
    <article class="cote">
        <div class="criteres">
            <section class="tag-selection">
                <div class="categories">
                    <h3>Catégories</h3>
                    <div class="category-dropdown">
                        <select id="main-category">
                            <option value="">-- Toutes les catégories --</option>
                            <option value="restaurant">Restauration</option>
                            <option value="activité">Activité</option>
                            <option value="visite">Visite</option>
                            <option value="spectacle">Spectacle</option>
                            <option value="parc d'attractions">Parc d'attractions</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" id="selected-category" name="category" value="">
                <div id="subcategories" class="hidden">
                    <h3>Tags</h3>
                    <div class="subcategory-list sorting-buttons" id="subcategory-list">
                        
                    </div>
                </div>
                <label for="min-price">Prix minimum :</label>
                <input type="number" id="min-price" min="0" step="1">
                </br>
                <label for="max-price">Prix maximum :</label>
                <input type="number" id="max-price" min="0" step="1">
                </br>
                <label for="min-rating">Note minimale :</label>
                <input type="number" id="min-rating" min="0" max="5" step="0.1" value="0">
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