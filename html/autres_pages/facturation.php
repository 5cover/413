<?php

require_once 'component/Page.php';
require_once 'auth.php';
require_once 'model/Duree.php';
require_once 'model/Offre.php';

$page = new Page('Facturation');

$page->put(function () {
    ?>
    <section class="centrer-enfants">
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

        $resultat_global  = 0;  // resultat global
        $resultat_offre   = 0;  // resultat offre
        $id_professionnel = Auth\exiger_connecte_pro();
        $offres           = Offre::from_db_all_ordered(id_professionnel: $id_professionnel);
        foreach ($offres as $offre) {
            ?>
            <tr>
            <td><?= h14s($offre->titre) ?></td>
            <td><?= h14s($offre->abonnement->libelle) ?></td>
            <td><?= h14s($offre->categorie) ?></td>
            <td><?= h14s($offre->en_ligne_ce_mois_pendant->days) ?></td>
            <?php
            $resultat_offre   = ceil($offre->en_ligne_ce_mois_pendant->total_days) * $offre->abonnement->prix_journalier;
            $resultat_offre  += $resultat_offre * 0.2;
            $resultat_global += $resultat_offre;
            if (strcasecmp($offre->abonnement->libelle, 'Gratuit') === 0) {
                ?>
                <td>N/A</td>
            <?php
            } else {
                ?>
                <td><?= "$resultat_offre €" ?></td>
            <?php
            }
            ?>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="row" colspan="4">Prix global TTC</th>
                <td><?= $resultat_global ?> €</td>
            </tr>
        </tfoot>
        </table>
    </section>
       <!-- Bouton pour obtenir le pdf -->
       <a class="btn-more-info bouton_principale_pro" href="facture_fpdf.php" id="obtenir_facture_pdf" target="_blank">Version PDF</a>

    <?php
});
?>