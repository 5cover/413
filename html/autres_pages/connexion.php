<?php
require_once 'component/Page.php';
require_once 'util.php';

$page = new Page('Connexion');

$return_url = getarg($_GET, 'return_url', required: false);
$error = getarg($_GET, 'error', required: false);
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
<?php $page->put_header() ?>
<main>
    <h1>Connexion</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <br>
            <!-- Formulaire de connexion -->
            <form action="/connexion/login.php" method="POST">
                <div class="champ">
                    <label for="login">Adresse e-mail ou pseudo *</label>
                    <input id="login" name="login" type="text" placeholder="exemple@mail.fr" required>
                </div>
                <br>
                <div class="champ">
                    <label for="mdp">Mot de passe *</label>
                    <input id="mdp" name="mdp" type="password" placeholder="**********" required>
                </div>
                <?php if ($error !== null) { ?>
                    <p class="error"><?= $error ?></p>
                <?php } ?>
                <button type="submit" class="btn-connexion">Se connecter</button>
                <?php if ($return_url !== null) { ?>
                    <input type="hidden" name="return_url" value="<?= $return_url ?>">
                <?php } ?>
            </form>
            <br><br>
            <label>Pas de compte&nbsp;?</label>
            <a href="creation_membre.php">
                <button class="btn-creer">Créer un compte personnel</button>
            </a>
            <label>OU</label>
            <a href="creation_comptePro.php">
                <button class="btn-creer">Créer un compte professionnel</button>
            </a>
            <br>
        </div>
    </section>
</main>
<?php $page->put_footer() ?>
</body>

</html>
