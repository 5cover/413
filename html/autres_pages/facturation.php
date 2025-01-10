<?php

use function DB\query_tarif;

require_once 'component/Page.php';
require_once 'auth.php';
require_once 'queries/offre.php';
require_once 'model/Duree.php';
require_once 'model/Offre.php';
require_once 'queries.php';

$page = new Page('Facturation');

$page->put(function () {
    ?>
    <table id="facturation">
        <thead>
            <tr>
                <th scope="col">Titre</th>
                <th scope="col">Type d'abonnement</th>
                <th scope="col">Catégorie</th>
                <th scope="col">Jours en ligne</th>
                <th scope="col">Prix TTC</th>
            </tr>
        </thead>
        <tbody>
    <?php

    $resG = 0;//resultat global
    $resO = 0;//resultat offre
    $id_professionnel = Auth\exiger_connecte_pro();
    $offres = Offre::from_db_all($id_professionnel);
    foreach ($offres as $offre) {
        ?>
        <tr>
        <td><?php print_r($offre->titre); ?></td>
        <td><?php print_r($offre['libelle_abonnement'] );?></td>
        <td><?php print_r($offre['categorie']);?></td>
        <td><?php print_r(Duree::parse($offre['en_ligne_ce_mois_pendant'])->days );?></td>
        <?php
        $resO = Duree::parse($offre['en_ligne_ce_mois_pendant'])->days * query_tarif($offre['libelle_abonnement']);
        $resO *= 0.20;
        $resG += $resO; 
        ?>
        <td><?php print_r("$resO €");?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row" colspan="4">Prix global TTC</th>
            <td><?php print_r($resG) ?> €</td>
        </tr>
    </tfoot>
    </table>
    <?php
}); 
?>