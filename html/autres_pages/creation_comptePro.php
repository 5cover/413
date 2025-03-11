<?php
require_once 'const.php';
require_once 'db.php';
require_once 'util.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'model/Compte.php';
require_once 'model/Adresse.php';
require_once 'model/ProfessionnelPrive.php';
require_once 'model/ProfessionnelPublic.php';
require_once 'model/Commune.php';

$page = new Page('Créer un compte professionnel', scripts: ['module/creation_compte_pro.js' => 'type="module"']);

function fail(string $error): never
{
    redirect_to('?' . http_build_query(['error' => $error]));
}

if ($_POST) {
    $args = [
        'nom' => getarg($_POST, 'nom'),
        'prenom' => getarg($_POST, 'prenom'),
        'telephone' => getarg($_POST, 'telephone'),
        'email' => getarg($_POST, 'email'),
        'mdp' => getarg($_POST, 'mdp'),
        'adresse' => getarg($_POST, 'adresse'),
        'denomination' => getarg($_POST, 'denomination'),
        'type' => getarg($_POST, 'type', arg_check(f_is_in(['prive', 'public']))),
    ];
    if ($args['type'] === 'prive') {
        $args['siren'] = getarg($_POST, 'siren');
    }

    if (false === Compte::from_db_by_email($args['email']))
        fail('Cette adresse e-mail est déjà utilisée.');

    $mdp_hash = password_hash($args['mdp'], PASSWORD_ALGO);

    $args_compte = [
        null,
        $args['email'],
        $mdp_hash,
        $args['nom'],
        $args['prenom'],
        $args['telephone'],
        $adresse,
        null,
    ];
    $args_pro = [
        $args['denomination'],
    ];

    $pro = $args['type'] === 'prive'
        ? new ProfessionnelPrive($args_compte, $args_pro, str_replace(' ', '', $args['siren']))
        : new ProfessionnelPublic($args_compte, $args_pro);
    $pro->push_to_db();
    redirect_to(location_connexion());
}

$page->put(function () {
    ?>
    <!-- Section des offres à la une -->
    <h1>Créer un compte professionnel</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <form action="creation_comptePro.php" method="post" enctype="multipart/form-data">
                <p class="champ">
                    <label>E-mail * <input type="mail" placeholder="exemple@mail.fr" id="email" name="email" required></label>
                </p>
                <p class="champ">
                    <label>Mot de passe * <input type="password" placeholder="**********" id="mdp" name="mdp" required></label>
                </p>
                <!-- Texte avec label -->
                <p class="champ">
                    <label>Nom * <input type="text" id="nom" name="nom" placeholder="Breton" required></label>
                </p>
                <p class="champ">
                    <!-- Texte avec label -->
                    <label>Prénom * <input type="text" id="prenom" name="prenom" placeholder="Louis" required></label>
                </p>
                <p class="champ">
                    <!-- Texte avec label -->
                    <label>Téléphone * <input id="telephone" name="telephone" type="tel" placeholder="Format: 0123456789" pattern="[0-9]{10}" maxlength="10" required></label>
                </p>
                <p class="champ">
                    <!-- Texte avec label -->
                    <label>Dénomination (raison sociale) * <input type="text" id="denomination" name="denomination" placeholder="Amazon" required></label>
                </p>
                <p class="champ">

                    <label>Adresse<input type="text" id="adresse" placeholder="22300 1 rue Edouard Branly" name="adresse"></label>
                </p>
                <p class="radio_entr">
                    <label>Privé <input type="radio" id="prive" name="type" value="prive" checked></label>
                    <label>Public <input type="radio" id="public" name="type" value="public"></label>
                </p>
                <p class="champ" id="champ-siren">
                    <label>SIREN <input type="text" id="siren" name="siren" placeholder="231 654 988" maxlength="12"></label>
                </p>
                <p class="error"><?= h14s($_GET['error'] ?? null) ?></p>
                <button type="submit" class="btn-connexion">Créer un compte professionnel</button>
            </form>
            <br>
            <br>
            <p>Se connecter ?</p>
            <a href="connexion.php">
                <button class="btn-creer">Se connecter</button>
            </a>
            <br>
        </div>
    </section>
    <?php
});