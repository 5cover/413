<?php
session_start(); 

$_SESSION = array();

// Si vous souhaitez aussi détruire le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion après déconnexion
header("Location: ../autres_pages/accueil.php");
exit();
