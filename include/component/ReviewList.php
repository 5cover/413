<?php

require_once 'model/Offre.php';
require_once 'model/Reponse.php';
require_once 'redirect.php';
require_once 'util.php';
require_once 'model/Avis.php';
require_once 'cookie.php';
require_once 'model/Blacklist.php';

final class ReviewList
{
    function __construct(
        readonly Offre $offre,
    ) {}

    function put(): void
    {
        ?>
        <div class="review-list" id="1">
            <h4>Avis de la communauté</h4>
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= $this->offre->nb_avis ?></p>
                <p>Moyenne&nbsp;: <?= $this->offre->note_moyenne ?? 0 ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php
                    $avis               = iterator_to_array(Avis::from_db_all(
                        id_offre: $this->offre->id,
                        blackliste: $this->est_connecte_pro_proprio()
                            && $this->offre->abonnement->libelle === 'premium'
                                ? null
                                : false
                    ));
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
                <?php if($this->est_connecte_pro_proprio() && $this->offre->abonnement->libelle=="premium") { ?>
                    <p>Nombre de blacklistages restants&nbsp;: <?= Blacklist::nb_blacklist_restantes(Auth\id_pro_connecte()) ?></p>
                <?php } ?>

                <?php
                if (!empty($avis)) {
                    foreach ($avis as $a) {
                        $likes = Cookie\CommentLikes::likes($a->id);
                        if ($this->est_connecte_pro_proprio()) {
                            $a->marquerCommeLu();
                        }
                        ?>
                        <div class="review">
                            <div class="liker" data-comment-id="<?= $a->id ?>">
                                <div class="compteur click-like">
                                    <span class="likes"><?= $a->likes ?></span>
                                    <button type="button" <?= $likes === true ? 'checked' : '' ?> class="like-buttons">
                                        <img class="btn-like" src="/images/thumb<?= $likes === true ? '-filled' : '' ?>.svg" alt="Like" title="Like">
                                    </button>
                                </div>
                                <div class="compteur click-dislike">
                                    <span class="dislikes"><?= $a->dislikes ?></span>
                                    <button type="button" <?= $likes === false ? 'checked' : '' ?> class="like-buttons">
                                        <img class="btn-dislike" src="/images/thumb<?= $likes === false ? '-filled' : '' ?>.svg" alt="Dislike" title="Dislike">
                                    </button>
                                </div>
                            </div>

                            <p><?php if (null === $a->membre_auteur) { ?>
                                    <span class="deleted-pseudo">Compte supprimé</span>
                                <?php } else { ?>
                                    <strong><?= h14s($a->membre_auteur->pseudo) ?></strong>
                                <?php } ?>
                                &ndash; <?= h14s($a->note) ?>/5
                                <?php
                                if (null !== $idcco = Auth\id_compte_connecte()) {
                                    $raison_signalement_actuel = Signalable::signalable_from_db($a->id)->get_signalement($idcco);
                                    ?>
                                    <button class="button-signaler"
                                    data-idcco="<?= $idcco ?>"
                                    data-avis-id="<?= $a->id ?>"
                                    type="button"><img
                                        class="signalement-flag"
                                        src="/images/<?= $raison_signalement_actuel === null ? 'flag' : 'flag-filled' ?>.svg"
                                        title="<?= $raison_signalement_actuel === null ? 'Signaler' : 'Retirer le signalement (' . h14s($raison_signalement_actuel) . ')' ?>" width="24" height="29" alt="Drapeau"></button>
                                </p>
                            <?php } ?>
                            <p class="review-contexte">Contexte&nbsp;: <?= h14s($a->contexte) ?></p>
                            <p><?= h14s($a->commentaire) ?></p>
                            <p class="review-date"><?= h14s($a->date_experience) ?></p>
                            <?php
                            if ($this->est_connecte_pro_proprio() && $this->offre->abonnement->libelle=="premium") {
                                ?>
                                <button class="button-blacklist"
                                data-avisid="<?= $a->id ?>"
                                type="button"
                                <?= Blacklist::get_blacklist($a->id) !== null ? 'disabled' : '' ?>>
                                <?= Blacklist::get_blacklist($a->id) !== null ? 'Blacklisté' : 'Blacklister' ?>

                                </button>
                            <?php
                            }
                            if ($a->membre_auteur !== null and $a->membre_auteur->id === Auth\id_membre_connecte()) {
                                ?>
                                <form method="post" action="<?= h14s(location_modifier_avis($this->offre->id, $a->id)) ?>">
                                    <button type="submit" class="btn-publish">Modifier</button>
                                    <button class="btn-publish">
                                        <a href="<?= h14s(location_avis_supprimer($a->id, location_detail_offre($this->offre->id))) ?>">Supprimer</a>
                                    </button>
                                </form>
                            <?php }
                            $h14s_rep_contenu = h14s(Reponse::from_db_by_avis($a->id)?->contenu);
                            if ($this->est_connecte_pro_proprio()) { ?>
                                <form method="post" action="<?= h14s(location_repondre_avis($a->id)) ?>">
                                    <p><label for="contenu">Votre réponse&nbsp;:</label></p>
                                    <textarea name="contenu" placeholder="Réponse&hellip;" title="Laisser vide pour supprimer la réponse"><?= $h14s_rep_contenu ?></textarea>
                                    <button type="submit" class="btn-publish">Répondre</button>
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

    private function est_connecte_pro_proprio(): bool
    {
        return notnull($this->offre->professionnel->id) === Auth\id_pro_connecte();
    }
}
