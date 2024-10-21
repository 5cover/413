<?php

require_once "../../db.php";
if (isset($_POST['mdp'])) {

    print 'Votre nom :'.$_POST['nom'];
    print 'Votre prenom :'.$_POST['mdp'];
    print 'Votre numero de telephone :'.$_POST['telephone'];
    print 'Votre mail :'.$_POST['email'];
    print 'Votre mot de passe :'.$_POST['mdp'];
    print 'Votre adresse :'.$_POST['adresse'];
    // print 'Votre type de compte :'.$type;
    $estprive = isset($_POST['prive']);
    $mdp_hash = password_hash($_POST["mdp"], PASSWORD_DEFAULT);


    $pdo=db_connect();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM comptes WHERE email = :email');
    $stmt->execute(['type' => $type, 'email' => $_POST['email']]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
       echo 'Cette adresse e-mail est déjà utilisée.';
       exit;
    }

    // insert in compte

    if ($estprive) {
        
        // insert in pro_prive
        $sql = "INSERT INTO comptes (email, mdp_hash, nom, prenom, telephone, denomination, siren) VALUES (:email, :mdp_hash, :nom, :prenom, :telephone, :denomination, :siren)";
        $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':mdp_hash', $mdp_hash);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->bindParam(':prenom', $_POST['prenom']);
            $stmt->bindParam(':telephone', $_POST['telephone']);
            $stmt->bindParam(':denomination', $_POST['denomination']);
            $stmt->bindParam(':siren', $_POST['siren']);

    
        // 3. Exécuter la requête avec les valeurs
        $stmt->execute([
            ':email' => $_POST['email'],
            ':mdp_hash' => $mdp_hash,
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':telephone' => $_POST['telephone'],
            ':denomination' => $_POST['denomination'],
            ':siren' => $_POST['siren']
        ]);
        

echo "Données insérées avec succès!";
        
    } else {
        // insert in pro_public
        $sql = "INSERT INTO comptes (email, mdp_hash, nom, prenom, telephone, denomination) VALUES (:email, :mdp_hash, :nom, :prenom, :telephone, :denomination)";
        $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':mdp_hash', $mdp_hash);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->bindParam(':prenom', $_POST['prenom']);
            $stmt->bindParam(':telephone', $_POST['telephone']);
            $stmt->bindParam(':denomination', $_POST['denomination']);


    
        // 3. Exécuter la requête avec les valeurs
        $stmt->execute([
            ':email' => $_POST['email'],
            ':mdp_hash' => $mdp_hash,
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':telephone' => $_POST['telephone'],
            ':denomination' => $_POST['denomination'],

        ]);
        
    }

}
else {
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte pro</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <?php
        include("header.php");
    ?>

    <main>
        <!-- Section des offres à la une -->
        <h1>Créer un compte professionnel</h1>
        <section class="connexion">
                <div class="champ-connexion">
                <form action="creation_comptePro.php" method="post" enctype="multipart/form-data">

                    <br>
                    <div class="champ">
                        <p>E-mail *</p>
                        <input type="text" placeholder="exemple@mail.fr" id="email" name="email" required>
                    </div>
                    <br>
                    <div class="champ">
                        <p>Mot de passe *</p>
                        <input type="text" placeholder="**********" id="mdp" name="mdp" required>
                    </div>
                    <br>
                    <!-- Texte avec label -->
                    <div class="champ">
                        <p>Nom :</p>
                        <input type="text" id="nom" name="nom" placeholder="Nyx" required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Prenom :</p>
                        <input type="text" id="prenom" name="prenom" placeholder="Icelos" required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Téléphone :</p>
                        <input type="text" id="telephone"name="telephone" placeholder="00 00 00 00 00" required />
                        </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Dénomination (raison sociale)  *:</p>
                        <input type="text" id="denomination" name="denomination" placeholder="Panthéon de la nuit étoilée"  required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Email -->
                        <p>Adresse *:</p>
                        <input type="text" id="adresse" placeholder="1 rue che 22050 truc" name="adresse" />
                    </div>
                    <br />

                    <div class="radio_entr">
                        <div>
                            <input type="radio" id="public" name="privé" value="huey" checked />
                            <label for="public" style="font-family:'Tw Cen MT'">Public</label>
                        </div>

                        <div>
                            <input type="radio" id="prive" name="privé" value="prive" />
                            <label for="prive" style="font-family:'Tw Cen MT'">Privé</label>
                        </div>
                    </div>
                
                    <br>
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>SIREN*:</p>
                        <input type="text" id="siren" name="siren" placeholder="231 654 987     12315" required />
                    </div>
                    <br>
                    <button type="submit" class="btn-connexion">Créer un compte professionnel</button>
            </form>
            <br /><br>
            <p>Se connecter ?</p>
            <a href="connexion.php">
                <button class="btn-creer">Se connecter</button>
            </a>
            <br>
            </div>
        </section>
    </main>
    <br>
    <br>
    <br>
    <?php
        include("footer.php");
    ?>
</body>

</html>
<?php
}
?>