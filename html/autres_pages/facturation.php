<?php
require_once 'const.php';
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'ValueObjects/Duree.php';
require_once 'model/OffreFast.php';
require_once 'util.php';

$page = new Page('Facturation');

const TVA = 0.2;

$page->put(function () {
    ?>
    <section class="centrer-enfants">
        <table id="facturation">
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Catégorie</th>

                    <th scope="col">Option</th>
                    <th scope="col">Semaines d'option</th>
                    <th scope="col">Prix option/semaine(HT)</th>
                    <th scope="col">Prix Option(HT)</th>

                    <th scope="col">Formule</th>
                    <th scope="col">Prix/J(HT)</th>
                    <th scope="col">Jours en ligne</th>

                    <th scope="col">Prix HT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($offre = mapnull(getarg($_GET, 'id_offre', arg_int(), required: false), OffreFast::from_db(...))) {
                    $offres = [$offre];
                } else {
                    $id_professionnel = Auth\exiger_connecte_pro();
                    $offres           = iterator_to_array(OffreFast::from_db_all_ordered(id_professionnel: $id_professionnel));
                }

                put_select_mois(
                    min(array_map(fn($o) => $o->creee_le->datetime, $offres)),
                    getarg($_GET, 'mois', required: false),
                );

                $resultat_global = 0;
                foreach ($offres as $offre) {
                    $resultat_global += facturer($offre);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row" colspan="9">Prix global HT</th>
                    <td class="prix-ht"><?= round($resultat_global, 2) ?>&nbsp;€</td>
                </tr>
                <tr>
                    <th scope="row" colspan="9">TVA <?= TVA * 100 ?>&nbsp;%</th>
                    <td class="prix-ht"><?= round($resultat_global * TVA, 2) ?>&nbsp;€</td>
                </tr>
                <tr>
                    <th scope="row" colspan="9">Prix global TTC</th>
                    <td class="prix-ht"><?= round($resultat_global + $resultat_global * TVA, 2) ?>&nbsp;€</td>
                </tr>
            </tfoot>
        </table>
    </section>
    <!-- Bouton pour obtenir le pdf -->
    <section>
        <a class="btn-more-info bouton_principale_pro" href="facture_fpdf.php" id="obtenir_facture_pdf" target="_blank">Version PDF</a>
    </section>
    <?php
});

function facturer(OffreFast $offre): float
{
    ?>
    <tr>
        <td><?= h14s($offre->data->titre) ?></td>
        <td><?= h14s($offre->computed->categorie->value) ?></td>

        <!-- affiche le type de l'option -->
        <?php
        if ($offre->computed->option !== null) {
            ?>
            <td class="prix-ht"><?= h14s($offre->computed->option->nom) ?></td>
            <td class="prix-ht"><?= h14s($offre->computed->option->nb_semaines) ?></td>
            <td class="prix-ht"><?= h14s($offre->computed->option->prix_hebdomadaire) ?>&nbsp;€</td>
            <?php
            $prixOption = $offre->computed->option->nb_semaines * $offre->computed->option->prix_hebdomadaire;
            ?>
            <td class="prix-ht"><?= h14s(round($prixOption, 2)) ?>&nbsp;€</td>

            <?php
        } else {
            $prixOption = false;
            ?>
            <td class="prix-ht">N/A</td> <!-- nom de l'option  -->
            <td class="prix-ht">N/A</td> <!-- nb de semaine de l'options -->
            <td class="prix-ht">N/A</td> <!-- prix option de la semaine -->
            <td class="prix-ht">N/A</td> <!-- prix globale de l'option -->
            <?php
        }

        $abo = Abonnement::from_db($offre->data->libelle_abonnement);
        ?>

        <!-- section de l'abonnement -->
        <td><?= h14s($offre->data->libelle_abonnement->value) ?></td>
        <td class="prix-ht"><?= round($abo->prix_journalier, 2) ?>&nbsp;€</td>

        <?php
        $nb_jours_factures = ceil($offre->computed->en_ligne_ce_mois_pendant->total_days);
        // affiche le prix de l'offre ce mois ci ou NA si l'offre est gratuite
        if ($abo->libelle === LibelleAbonnement::Gratuit || $nb_jours_factures === 0.0) {
            $resultat_offre = 0;
            ?>
            <td class="prix-ht">N/A</td><!-- nb de jours en ligne de l'offre -->
            <td class="prix-ht">N/A</td><!-- prix de l'offre -->
            <?php
        } else {
            $resultat_offre = $nb_jours_factures * $abo->prix_journalier;
            if ($prixOption) {
                $resultat_offre += $prixOption;
            }
            ?>
            <td class="prix-ht"><?= $nb_jours_factures ?></td><!-- nb de jours en ligne de l'offre -->
            <td class="prix-ht"><?= round($resultat_offre, 2) ?>&nbsp;€</td>
            <?php
        }
        ?>
    </tr>
    <?php
    return $resultat_offre;
}

function put_select_mois(DateTimeInterface $debut, ?string $mois_actuel): void
{
    $m_debut           = (int) $debut->format('n');
    $y_debut           = (int) $debut->format('Y');
    [$m_sel, $m_sel] = mapnull($mois_actuel, fn($m) => explode('.', $m, 2)) ?? [$m_debut, $y_debut];

    $m_fin  = (int) date('n');
    $y_fin = (int) date('Y');
    ?>
    <select name="mois-facture" id="select-mois-facture">
        <?php
        for ($y = $y_debut; $y <= $y_fin; ++$y) {
            for ($m = $y === $y_debut ? $m_debut : 1; $m <= ($y === $y_fin ? $m_fin : 12); ++$m) {
                ?><option value="<?= $y ?>.<?= $m ?>" <?= $y === $m_sel and $m === $m_sel ? 'selected' : '' ?>><?= ucfirst(MOIS[$m]) ?> <?= $y ?></option><?php
            }
        }

        ?>
    </select>
    <?php
}
