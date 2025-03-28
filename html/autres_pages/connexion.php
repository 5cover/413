<?php
require_once 'component/Page.php';
require_once 'redirect.php';
require_once 'util.php';

$page = new Page('Connexion', scripts: [
    'module/connexion.js' => 'type="module"',
]);

$page->put(function () {
    $return_url = getarg($_GET, 'return_url', required: false);
    $error      = getarg($_GET, 'error', required: false);
    $pseudo     = getarg($_GET, 'pseudo', required: false);
    ?>
    <h1>Connexion</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <br>
            <!-- Formulaire de connexion -->
            <form action="<?= h14s(location_login()) ?>" method="post">
                <div class="champ">
                    <label for="login">Adresse e-mail *</label>
                    <input id="login" name="login" type="text" placeholder="exemple@mail.fr" required value="<?= h14s($pseudo) ?>">
                </div>
                <br>
                <div class="champ">
                    <label for="mdp">Mot de passe *</label>
                    <input id="mdp" name="mdp" type="password" placeholder="**********" required>
                </div>
                <div class="centrer-enfants">
                    <?php if ($error !== null) { ?>
                        <p class="error" style="text-align: center;"><?= h14s($error) ?></p>
                    <?php } ?>
                    
                    <button type="submit" class="btn-connexion">Se connecter</button>

                    <?php if ($return_url !== null) { ?>
                        <input type="hidden" name="return_url" value="<?= h14s($return_url) ?>">
                    <?php } ?>
                </div>
            </form>
            <br><br>
            <label>Pas de compte&nbsp;?</label>
            <a href="creation_membre.php" class="btn-creer">Créer un compte utilisateur</a>
            <label>OU</label>
            <a href="creation_comptePro.php" class="btn-creer">Créer un compte professionnel</a>
            <!--<label>OU</label>
            <a href="reset_mdp.php" class="btn-creer">Mot de passe oublié ?</a>
            <br>-->
        </div>
    </section>
    <?php
});
