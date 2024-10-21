<?php
require '../../db.php';
if (isset($_POST['motdepasse'])){
   $pdo=db_connect();
   $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE pseudo = :pseudo');
   $stmt->execute(['pseudo' => $_POST['pseudo']]);
   $count = $stmt->fetchColumn();
   if ($count > 0) {
      echo 'Ce pseudo est déjà utilisé.';
      exit();
   }
   $stmt = $pdo->prepare('SELECT COUNT(*) FROM membres WHERE email = :email');
   $stmt->execute(['email' => $_POST['email']]);
   $count = $stmt->fetchColumn();
   if ($count > 0) {
      echo 'Cette adresse e-mail est déjà utilisée.';
      exit();
   }
   $stmt = $pdo->prepare('INSERT INTO membres (pseudo, nom, prenom, telephone, email, motdepasse) VALUES (:pseudo, :nom, :prenom, :telephone, :email, :motdepasse)');
   $stmt->execute([
      'pseudo' => $_POST['pseudo'],
      'nom' => $_POST['nom'],
      'prenom' => $_POST['prenom'],
      'telephone' => $_POST['telephone'],
      'email' => $_POST['email'],
      'motdepasse' => password_hash($_POST['motdepasse'], PASSWORD_DEFAULT),
   ]);
}
else {
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="stylesheet" href="../style/style.css">
      <title>Création de compte</title>
   </head>
   <body>
      <?php
        include("header.php");
      ?>
      <br>
      <main>
         <h1>Créer un compte membre</h1>
         <section class="connexion">
            <div class="champ-connexion">
               <form action="creation_membre.php" method="post" enctype="multipart/form-data">
                  <div class="champ">
                     <label for="pseudo">Pseudo :</label>
                     <input type="text" id="pseudo" name="pseudo" required />
                  </div>

                  <div class="champ">
                     <label for="nom">Nom :</label>
                     <input type="text" id="nom" name="nom" required />
                  </div>

                  <div class="champ">
                     <label for="prenom">Prenom :</label>
                     <input type="text" id="prenom" name="prenom" required />
                  </div>

                  <div class="champ">
                     <label for="telephone">Téléphone :</label>
                     <input type="text" id="telephone" name="telephone" required />
                  </div>

                  <div class="champ">
                     <label for="email">Email :</label>
                     <input type="mail" id="email" name="email" required />
                  </div>

                  <div class="champ">
                     <label for="motdepasse">Mot de passe :</label>
                     <input type="password" id="motdepasse" name="motdepasse" required />
                     <br>
                  </div>
                     
                  <div class="champ">
                     <input type="submit" value="Valider" />
                  </div>
               </form>
            </div>
         </section>
      </main>
      <br><br><br><br>
      <?php
        include("footer.php");
      ?>
   </body>
</html>
<?php
}
?>
