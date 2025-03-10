<?php
require_once 'model/Uuid.php';
require_once 'util.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/InputAdresse.php';
require_once 'model/Compte.php';

$page = new Page('Modification compte', body_id: 'detail_compte', scripts: ['module/modif_compte.js' => 'type="module"']);
$error_mdp = null;
$error_tel = null;
$error_email = null;
$error_siren = null;

$compte = notfalse(Compte::from_db(Auth\id_compte_connecte()));

$input_adresse = new InputAdresse('adresse', 'adresse');

// Afficher le détail du compte du membre

if ($_POST) {
    // modif pseudo
    if (null !== $new_pseudo = getarg($_POST, 'new_pseudo', null, required: false)) {
        $compte->pseudo = $new_pseudo;
    }

    // modif denomination
    if (null !== $new_denomination = getarg($_POST, 'new_denomination', required: false)) {
        $compte->denomination = $new_denomination;
    }

    // modif siren
    if (null !== $new_siren = getarg($_POST, 'new_siren', required: false)) {
        if (!preg_match('#^[0-9]{9}$#', $new_siren)) {
            $error_siren = 'siren incorrect, doit être composé de 9 chiffres';
        } else {
            $compte->siren = $new_siren;
        }
    }

    // modif Nom
    if (null !== $new_nom = getarg($_POST, 'new_nom', required: false)) {
        $compte->nom = $new_nom;
    }

    // modif Prenom
    if (null !== $new_prenom = getarg($_POST, 'new_prenom', null, required: false)) {
        $compte->prenom = $new_prenom;
    }

    // modif Email
    if (null !== $new_email = getarg($_POST, 'new_email', required: false)) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error_email = 'Email incorrect';
        } else {
            $compte->email = $new_email;
        }
    }

    // modif telephone
    if (null !== $new_telephone = getarg($_POST, 'new_telephone', required: false)) {
        if (!preg_match('#^[0-9]{10}$#', $new_telephone)) {
            $error_tel = 'Numéro incorrect, doit être composé de 10 chiffres';
        } else {
            $compte->telephone = $new_telephone;
        }
    }

    // modif mot de passe

    if (null !== $old_mdp = getarg($_POST, 'old_mdp', required: false)) {
        $new_mdp = getarg($_POST, 'new_mdp', required: false);
        $confirmation_mdp = getarg($_POST, 'confirmation_mdp', filter: null, required: false);
        if (password_verify($old_mdp, $compte->mdp_hash)) {
            if ($new_mdp) {
                if ($confirmation_mdp === $new_mdp) {
                    $compte->mdp_hash = password_hash($new_mdp, algo: PASSWORD_DEFAULT);
                } else {
                    $error_mdp = 'Mot de passe de confirmation different.';
                }
            } else {
                $error_mdp = 'Nouveau mot de passe manquant.';
            }
        } else {
            $error_mdp = 'Mot de passe incorrect.';
        }
    }

    // modif api_key
    if (null !== $api_key = getarg($_POST, 'api_key', required: false)) {
        $compte->api_key = Uuid::parse($api_key);
    }

    $compte->push_to_db();
}

$page->put(function () use ($compte, $input_adresse, $error_email, $error_mdp, $error_siren, $error_tel) {
    ?>
    <h1>Modifier les informations de votre compte</h1>
    <section id="info_compte">
        <form action="modif_compte.php" method="post">
            <?php if ($compte instanceof Membre) { ?>
                <div>
                    <div id="pseudo">
                        <label>Pseudo : </label>
                    </div>
                    <input id="new_pseudo" name="new_pseudo" type="text" value="<?= h14s($compte->pseudo) ?>" placeholder="votre nouveau pseudo">
                </div>
                <br>
            <?php } else if ($compte instanceof Professionnel) { ?>
                    <div>
                        <div id="denomination">
                            <label>Dénomination : </label>
                        </div>
                        <input id="new_denomination" name="new_denomination" type="text" value="<?= h14s($compte->denomination) ?>" placeholder="votre nouvelle dénomination">
                    </div>
                    <br>
                <?php if ($compte instanceof ProfessionnelPrive) { ?>
                        <div>
                            <div id="siren">
                                <label>SIREN : </label>
                            </div>
                        <?php if ($error_siren !== null) { ?>
                                <p class="error"><?= h14s($error_siren) ?></p>
                        <?php } ?>
                            <input type="text" id="new_siren" name="new_siren" value="<?= h14s($compte->siren) ?>" placeholder="231 654 988" oninput="formatInput(this)" maxlength="12">
                        </div>
                        <br>
                <?php } ?>
            <?php } ?>

            <div>
                <div id="nom">
                    <label>Nom : </label>
                </div>
                <input id="new_nom" name="new_nom" type="text" value="<?= h14s($compte->nom) ?>" placeholder="votre nouveau nom">
            </div>
            <br>
            <div>
                <div id="prenom">
                    <label>Prénom : </label>
                </div>
                <input id="new_prenom" name="new_prenom" type="text" value="<?= h14s($compte->prenom) ?>" placeholder="votre nouveau prénom">
            </div>
            <br>
            <div>
                <div id="email">
                    <label>Email : </label>
                </div>
                <?php if ($error_email !== null) { ?>
                    <p class="error"><?= h14s($error_email) ?></p>
                <?php } ?>
                <input id="new_email" name="new_email" type="email" value="<?= h14s($compte->email) ?>" placeholder="votre nouvel email">

            </div>
            <br>
            <div>
                <div id="telephone">
                    <label>Numéro de téléphone : </label>
                </div>
                <?php if ($error_tel !== null) { ?>
                    <p class="error"><?= h14s($error_tel) ?></p>
                <?php } ?>
                <input id="new_telephone" name="new_telephone" type="tel" value="<?= h14s($compte->telephone) ?>" placeholder="votre nouveau numéro de téléphone">

            </div>
            <br>
            <div>
                <label>Adresse : </label>
                <?= $compte->adresse->format() ?>
            </div>
            <?php $input_adresse->put($compte->adresse) ?>
            <br>
            <div id="changer_mdp">
                <label>Modifier son mot de passe</label>
                <div class="champ">
                    <label for="mdp">Mot de passe actuel *</label>
                    <input id="mdp" name="old_mdp" type="password" placeholder="**********">
                </div>
                <div class="champ">
                    <label for="new_mdp">Nouveau mot de passe *</label>
                    <input id="new_mdp" name="new_mdp" type="password" placeholder="**********">
                </div>
                <div class="champ">
                    <label for="confirmation_mdp">Confirmation du mot de passe *</label>
                    <input id="confirmation_mdp" name="confirmation_mdp" type="password" placeholder="**********">
                </div>
                <?php if ($error_mdp !== null) { ?>
                    <p class="error"><?= h14s($error_mdp) ?></p>
                <?php } ?>

            </div>
            <br>
            <div id="api_key">
                <label for="uuid">Clé d'API : </label>
                <input type="text" id="uuid" name="uuid" value="<?= $compte->api_key ?>" readonly></input>
                <button type="button" id="button-regenerate-uuid">Regénérer</button>
                <button type="button" id="button-delete-uuid">Supprimer</button>
            </div>
            <button type="submit">Valider</button>
            <a href="<?= location_detail_compte() ?>">Retour</a>
            <?php if ($compte instanceof Membre) { ?>
                <a href="<?= location_supprimer_compte($compte->id) ?>">Supprimer le compte</a>
            <?php } else { ?>
                <p><small>Pour supprimer votre compte professionnel, veuillez contacter l'administrateur du site. Voir les <a href="/legal/mentions-legales.php">mentions légales</a> pour plus d'informations.</small></p>
            <?php } ?>
        </form>
    </section>
    <?php
});