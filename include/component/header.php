<?php 
session_start() ?>
<header>
    <div class="logo">
        <a href="accueil.php"><img src="../images/logo.png" alt="Logo pact"></a>
    </div>
    <?php 
    if (isset($_SESSION['log']) && $_SESSION['log'] === true) { 
        // Vérification du statut de la session
        ?>
        <a href="../connexion/logout.php">
            <div class="auth-button">
                <img src="../images/profile-icon.png" alt="Profil">
                <span>Déconnexion</span>
            </div>
        </a>
        <?php if($_SESSION['id_pro']!=NULL){ ?>
            <a href="facturation.php">
                <div class="acces_facturation">
                    <!-- //TODO mettre une image sah -->
                    <span>Facturation</span>
                </div>
            </a>
        <?php } ?>
    <?php 
    } else { 
        ?>
        <a href="connexion.php">
            <div class="auth-button">
                <img src="../images/profile-icon.png" alt="Profil">
                <span>Connexion</span>
            </div>
        </a>
    <?php 
    } 
    ?>
</header>