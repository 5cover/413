<?php

require_once 'component/Page.php';
require_once 'auth.php';
require_once 'model/Duree.php';
require_once 'model/Offre.php';

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

    $resG             = 0;  // resultat global
    $resO             = 0;  // resultat offre
    $id_professionnel = Auth\exiger_connecte_pro();
    $offres           = Offre::from_db_all($id_professionnel);
    foreach ($offres as $offre) {
        ?>
        <tr>
        <td><?= $offre->titre; ?></td>
        <td><?= $offre->abonnement->libelle ?></td>
        <td><?= $offre->categorie ?></td>
        <td><?= $offre->en_ligne_ce_mois_pendant->days ?></td>
        <?php
        $resO  = $offre->en_ligne_ce_mois_pendant->days * $offre->abonnement->prix_journalier;
        $resO += $resO * 0.2;
        $resG += $resO;
        ?>
        <td><?= "$resO €" ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row" colspan="4">Prix global TTC</th>
            <td><?= $resG ?> €</td>
        </tr>
    </tfoot>
    </table>
    <?php
});
?>