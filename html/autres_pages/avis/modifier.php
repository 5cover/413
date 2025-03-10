<?php
require_once 'model/Avis.php';
require_once 'auth.php';
require_once 'const.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'util.php';

$page = new Page('Modifier un avis');

Auth\exiger_connecte_membre();

$id_avis = getarg($_GET, 'id_avis', arg_int());
$id_offre = getarg($_GET, 'id_offre', arg_int());

$avis = notfalse(Avis::from_db($id_avis));

$error_message = null;

if (null !== $date_experience = getarg($_POST, 'date', required: false)) {
    $commentaire = trim(getarg($_POST, 'commentaire'));
    $note = getarg($_POST, 'rating', arg_int());
    $contexte = getarg($_POST, 'contexte');

    // Validation des champs du formulaire
    if (empty($commentaire) || empty($note) || empty($contexte) || empty($date_experience)) {
        $error_message = 'Tous les champs sont obligatoires.';
    } else {
        $avis->commentaire = $commentaire;
        $avis->note = $note;
        $avis->contexte = $contexte;
        $avis->date_experience = Date::parse($date_experience);
        $avis->push_to_db();

        redirect_to(location_detail_offre($id_offre));
    }
}


$page->put(function () use ($id_avis, $avis, $id_offre, $error_message) {
    ?>
    <section class="centrer-enfants">
        <div>
            <h2>Modifier votre avis</h2>
                
            <div class="message">
                <?php if ($error_message) { ?>
                    <p class="error-message"><?= h14s($error_message) ?></p>
                <?php } ?>
            </div>

            <form method="post" action="<?= h14s(location_modifier_avis($id_offre, $id_avis)) ?>">
                <textarea name="commentaire" placeholder="Votre avis&hellip;" required><?= h14s($avis->commentaire) ?></textarea>

                <label for="rating">Note&nbsp;:</label>
                <select name="rating" id="rating" required>
                    <option value="5" <?= $avis->note == 5 ? 'selected' : '' ?>>5 étoiles</option>
                    <option value="4" <?= $avis->note == 4 ? 'selected' : '' ?>>4 étoiles</option>
                    <option value="3" <?= $avis->note == 3 ? 'selected' : '' ?>>3 étoiles</option>
                    <option value="2" <?= $avis->note == 2 ? 'selected' : '' ?>>2 étoiles</option>
                    <option value="1" <?= $avis->note == 1 ? 'selected' : '' ?>>1 étoile</option>
                </select>

                <label for="contexte">Contexte&nbsp;:</label>
                <select name="contexte" id="contexte" required>
                    <?php foreach (CONTEXTES_VISITE as $ctx) { ?>
                        <option value="<?= h14s($ctx) ?>" <?= $avis->contexte === $ctx ? 'selected' : '' ?>><?= h14s(ucfirst($ctx)) ?></option>
                        <?php } ?>
                    </select>
                    
                    <label for="date">Date de votre visite</label>
                    <input type="date" id="date" name="date" value="<?= h14s($avis->date_experience) ?>" required>
                    
                    <br>
                <button type="submit" class="btn-publish">Modifier</button>
                <button class="btn-publish">
                    <a href="<?= h14s(location_avis_supprimer($id_avis, location_detail_offre($id_offre))) ?>">Supprimer</a>
                </button>
            </form>
        </div>
    </section>
    <?php
});