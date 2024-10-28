<?php
session_start();

require_once 'queries.php';
require_once 'auth.php';

function fail(): never
{
    header('Location: /autres_pages/connexion.php?error=' . urlencode("Nom d'utilisateur ou mot de passe incorrect."));
    exit;
}

$pdo = db_connect();  // Essaie de te connecter à la base de données

// Récupérer les données du formulaire
$login = trim($_POST['login']);
$password = trim($_POST['mdp']);

// Connection membre
$user = query_membre($login);

if (!empty($user)) {
    $hashed_password = $user['mdp_hash'];
    if (!password_verify($password, $hashed_password)) {
        fail();
    }
    session_regenerate_id(true);
    connecter_membre($user['id']);
    header('Location: /autres_pages/accueil.php');
    exit;
}

// Connection professionnel
$user = query_professionnel($login);

if (!empty($user)) {
    $hashed_password = $user['mdp_hash'];
    if (!password_verify($password, $hashed_password)) {
        fail();
    }
    session_regenerate_id(true);
    connecter_pro($user['id']);
    header('Location: /autres_pages/accPro.php');
    exit;
}

fail();
?>