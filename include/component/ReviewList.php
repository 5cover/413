<?php

require_once 'model/Offre.php';
require_once 'model/Reponse.php';
require_once 'redirect.php';
require_once 'util.php';

final class ReviewList
{
    function __construct(
        readonly Offre $offre,
    ) {
    }

    function put(): void
    {
        ?>
        <div class="review-list">
            <h4>Avis de la communauté</h4>
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= $this->offre->nb_avis ?></p>
                <p>Moyenne&nbsp;: <?= $this->offre->note_moyenne ?? 0 ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php
                    $avis = iterator_to_array(Avis::from_db_all(id_offre: $this->offre->id));
                    $avis_count_by_note = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    foreach ($avis as $a) {
                        ++$avis_count_by_note[$a->note];
                    }
                    ?>
                    <p>5 étoiles&nbsp;: <?= $avis_count_by_note[5] ?> avis.</p>
                    <p>4 étoiles&nbsp;: <?= $avis_count_by_note[4] ?> avis.</p>
                    <p>3 étoiles&nbsp;: <?= $avis_count_by_note[3] ?> avis.</p>
                    <p>2 étoiles&nbsp;: <?= $avis_count_by_note[2] ?> avis.</p>
                    <p>1 étoile&nbsp;: <?= $avis_count_by_note[1] ?> avis.</p>
                </div>
                <?php if (!empty($avis)) {
                    foreach ($avis as $a) { ?>
                        <div class="review">
                            <p><strong><?= h14s($a->membre_auteur->pseudo) ?></strong> - <?= h14s($a->note) ?>/5
                                <?php if (null !== $idcco = Auth\id_compte_connecte()) {
                                    $raison_signalement_actuel = Signalable::signalable_from_db($a->id)->get_signalement($idcco);
                                    ?>
                                    <button class="button-signaler" data-idcco="<?= $idcco ?>" data-avis-id="<?= $a->id ?>" type="button"><img class="signalement-flag" src="/images/<?= $raison_signalement_actuel === null ? 'flag' : 'flag-filled' ?>.svg" width="24" height="29" alt="Drapeau" title="Signaler"></button>
                                </p>
                            <?php } ?>
                            <p class="review-contexte">Contexte&nbsp;: <?= h14s($a->contexte) ?></p>
                            <p><?= h14s($a->commentaire) ?></p>
                            <p class="review-date"><?= h14s($a->date_experience) ?></p>
                            <?php
                            if (notnull($a->membre_auteur->id) === Auth\id_membre_connecte()) { ?>
                                <form method="post" action="<?= location_modifier_avis($this->offre->id, $a->id) ?>">
                                    <button type="submit" class="btn-modif">Modifier</button>
                                    <a href="<?= location_avis_supprimer($a->id, location_detail_offre($this->offre->id)) ?>">Supprimer</a>
                                </form>
                            <?php }
                            $h14s_rep_contenu = mapnull(Reponse::from_db_by_avis($a->id)?->contenu, h14s(...));
                            if (notnull($this->offre->professionnel->id) === Auth\id_pro_connecte()) { ?>
                                <form method="post" action="<?= location_repondre_avis($a->id) ?>">
                                    <p><label for="contenu">Votre réponse&nbsp;:</label></p>
                                    <textarea name="contenu" placeholder="Réponse&hellip;" title="Laisser vide pour supprimer la réponse"><?= $h14s_rep_contenu ?></textarea>
                                    <button type="submit">Répondre</button>
                                </form>
                            <?php } else if ($h14s_rep_contenu !== null) { ?>
                                <p>Réponse de <?= h14s($this->offre->professionnel->denomination) ?>&nbsp;:</p>
                                <p><?= $h14s_rep_contenu ?></p>
                            <?php } ?>
                        </div>
                    <?php }
                } else { ?>
                    <p>Aucun avis pour le moment.&nbsp;</p>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
