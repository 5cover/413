<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'model/Compte.php';
require_once 'model/Membre.php';
require_once 'model/Professionnel.php';
require_once 'model/Adresse.php';
require_once 'model/Commune.php';

$page = new Page('Création de compte membre');

function fail(string $error): never
{
    redirect_to('?' . http_build_query(['error' => $error]));
}

if (isset($_POST['motdepasse'])) {
    if (strlen($_POST['motdepasse']) > 72) fail('Mot de passe trop long');

    $pseudo = $_POST['pseudo'];
    if (false !== Membre::from_db_by_pseudo($pseudo)) fail('Ce pseudo est déjà utilisé.');

    $email = $_POST['email'];
    if (false !== Compte::from_db_by_email($email)) fail('Cette adresse e-mail est déjà utilisée.');

    $commune = Commune::from_db_by_nom($_POST['adresse']);
    if (false === $commune) fail("La commune '{$_POST['adresse']}' n'existe pas.");

    $adresse = new Adresse(null, $commune);
    $adresse->push_to_db();

    $mdp_hash = notfalse(password_hash($_POST['motdepasse'], PASSWORD_DEFAULT));

    $membre = new Membre([
        null,
        $email,
        $mdp_hash,
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $adresse,
        null,
    ], $pseudo);
    try {
        $membre->push_to_db();
    } catch (PDOException $e) {
        fail($e->getMessage());
    }
    redirect_to(location_connexion());  // todo: passer en GET le pseudo pour l'afficher dans le formulaire connexion, pour que l'utilisateur n'ait pas à le retaper.
}

$page->put(function () {
    ?>
    <h1>Créer un compte membre</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <form action="creation_membre.php" method="post" enctype="multipart/form-data">
                <div class="champ">
                    <label for="pseudo">Pseudo&nbsp;:</label>
                    <input type="text" id="pseudo" name="pseudo" autocomplete="nickname" required>
                </div>

                <div class="champ">
                    <label for="nom">Nom&nbsp;:</label>
                    <input type="text" id="nom" name="nom" autocomplete="family-name" required>
                </div>

                <div class="champ">
                    <label for="prenom">Prénom&nbsp;:</label>
                    <input type="text" id="prenom" name="prenom" autocomplete="given-name" required>
                </div>

                <div class="champ">
                    <label for="telephone">Téléphone&nbsp;:</label>
                    <input type="tel" id="telephone" name="telephone" placeholder="0123456789" pattern="\d{10}" autocomplete="tel" required>
                </div>

                <div class="champ">
                    <label for="adresse">Adresse&nbsp;:</label>
                    <input type="text" id="adresse" name="adresse" autocomplete="Lannion" required>
                </div>

                <div class="champ">
                    <label for="email">Email&nbsp;:</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>

                <div class="champ">
                    <label for="motdepasse">Mot de passe&nbsp;:</label>
                    <input type="password" id="motdepasse" name="motdepasse" required>
                    <br>
                </div>
                <!-- Todo: confirmation mdp -->
                <p class="error"><?= $_GET['error'] ?? '' ?></p>

                <div class="champ">
                    <button type="submit">Valider</button>
                </div>
            </form>
        </div>
    </section>
    <?php
});