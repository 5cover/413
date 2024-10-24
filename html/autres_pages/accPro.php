<?php
    session_start();
    require_once 'db.php';
    $pdo=db_connect();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM pact.offres WHERE id_professionnel = :id_professionnnel');
    $stmt->execute(['id_professionnel' => $_SESSION['id']]);
    $nb_offre = $stmt->fetchColumn();
    $stmt = $pdo->prepare('CALL nb_offres_en_ligne(:id_professionnel)');
    $stmt->execute(['id_professionnel' => $_SESSION['id']]);
    $offre_en_ligne = $stmt->fetchColumn();
    $offre_hors_ligne = $nb_offre - $offre_en_ligne;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Professionnel</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <h1>Accueil Professionnel</h1>

        <h3><?php echo $nb_offre ?> offres</h3>
        <section class="online-offers">
            <h2>Mes offres en ligne</h2>
            <p>Vos offres actuellement disponibles en ligne : <?php echo $offre_en_ligne;?></p>

            <div class="offer-list">
                <!-- Offre en ligne 1 -->
                 <?php
                 $stmt = $pdo->prepare('CALL nb_offres_en_ligne(:id_professionnel)');
                 $stmt->execute(['id_professionnel' => $_SESSION['id']]);
                 $liste_offre_en_ligne = $stmt;
                 while ($offre = $liste_offre_en_ligne->fetch()) {
                    ?>
                    <div class="offer-card">
                        <img src="<?php $offre[8] ?>" alt="Découverte interactive de la cité des Télécoms">
                        <h3><?php $offre[1] ?></h3>
                        <p class="location"><?php $offre[7] ?></p>
                        <p class="category"><?php $stmt = $pdo->prepare('CALL category(:id_offre)');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?></p>
                        <p class="rating">Note : <?php $stmt = $pdo->prepare('CALL moyenne(:id_offre)');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?>/5 ★ (<?php $stmt = $pdo->prepare('select count(*) from avis where offre=:id_offre');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?> avis)</p>
                        <button class="btn-more-info" href="<?php $offre[4] ?>">En savoir plus</button>
                    </div>
                    <?php
                 }
                 $liste_offre_en_ligne->closeCursor();
                 ?>
                <!-- Offre en ligne 2 -->
                <div class="offer-card">
                    <img src="vallee_saints.jpg" alt="Randonnée dans la vallée des Saints">
                    <h3>Randonnée dans la vallée des Saints</h3>
                    <p class="location">Boudes (63340)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note : 4.2/5 ★ (54 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

            </div>
        </section>

        <section class="offline-offers">
            <h2>Mes offres hors ligne</h2>
            <p>Vos offres hors-ligne : <?php echo $offre_hors_ligne?> </p>

            <div class="offer-carousel">
            <?php
                $stmt = $pdo->prepare('CALL nb_offres_hors_ligne(:id_professionnel)');
                $stmt->execute(['id_professionnel' => $_SESSION['id']]);
                $liste_offre_hors_ligne = $stmt;
                while ($offre = $liste_offre_hors_ligne->fetch()) {
                    ?>
                    <div class="offer-card">
                        <img src="<?php $offre[8] ?>" alt="Découverte interactive de la cité des Télécoms">
                        <h3><?php $offre[1] ?></h3>
                        <p class="location"><?php $offre[7] ?></p>
                        <p class="category"><?php $stmt = $pdo->prepare('CALL category(:id_offre)');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?></p>
                        <p class="rating">Note : <?php $stmt = $pdo->prepare('CALL moyenne(:id_offre)');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?>/5 ★ (<?php $stmt = $pdo->prepare('select count(*) from avis where offre=:id_offre');
                        $stmt->execute(['id_offre' => $offre[0]]);
                        echo $stmt?> avis)</p>
                        <button class="btn-more-info" href="<?php $offre[4] ?>">En savoir plus</button>
                    </div>
                    <?php
                }
                $liste_offre_hors_ligne->closeCursor();
            ?>
                <div class="offer-card">
                    <img src="telecom.jpg" alt="Découverte interactive de la cité des Télécoms">
                    <h3>Découverte interactive de la cité des Télécoms</h3>
                    <p class="location">Pleumeur-Bodou (22560)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note : 4.7/5 ★ (256 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>

                </div>

                <!-- Offre 2 -->
                <div class="offer-card">
                    <img src="vallee_saints.jpg" alt="Randonnée dans la vallée des Saints">
                    <h3>Randonnée dans la vallée des Saints</h3>
                    <p class="location">Boudes (63340)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note : 4.2/5 ★ (54 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

                <!-- Offre 3 -->
                <div class="offer-card">
                    <img src="grenouilles.jpg" alt="Chasse aux grenouilles dans le Lac du Gourgal">
                    <h3>Chasse aux grenouilles dans le Lac du Gourgal</h3>
                    <p class="location">Guingamp (22200)</p>
                    <p class="category">Activité Nature</p>
                    <p class="rating">Note : 3.7/5 ★ (122 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

                <!-- Offre 4 -->
                <div class="offer-card">
                    <img src="char_voile.jpg" alt="Initiation au Char à Voile sur la plage">
                    <h3>Initiation au Char à Voile sur la plage</h3>
                    <p class="location">Pléneuf-Val-André (22370)</p>
                    <p class="category">Sport nautique</p>
                    <p class="rating">Note : 4.4/5 ★ (24 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>
            </div>
        </section>

        <!-- Bouton pour créer une nouvelle offre -->
        <a href="choix_categorie_creation_offre.php">
            <div class="create-offer">
                <button class="btn-create">Créer une offre</button>
            </div>
        </a>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>