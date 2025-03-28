<?php

require_once 'auth.php';
require_once 'util.php';
require_once 'redirect.php';
require_once 'model/Professionnel.php';
require_once 'model/Membre.php';
require_once 'otp.php';


// Récupérer les données du formulaire
$args = [
    'login'      => getarg($_POST, 'login'),
    'mdp'        => getarg($_POST, 'mdp'),
    'otp_secret' => getarg($_POST, 'otp_secret', required: false),

    'return_url' => getarg($_POST, 'return_url', required: false),
];

// Connection membre
if (false !== $user = str_contains($args['login'], '@')
        ? Membre::from_db_by_email($args['login'])
        : Membre::from_db_by_pseudo($args['login'])) {
    if (!password_verify($args['mdp'], $user->mdp_hash)) {
        fail();
    }
    else {
        if ($otp_secret && !OTP\verify( otp_secret: $user->otp_secret, otp_reponse: $otp_secret)) {
            fail();
        }
    }
    session_regenerate_id(true);
    Auth\se_connecter_membre($user->id);
    succeed();
}

// Connection professionnel
if (false !== $user = Professionnel::from_db_by_email($args['login'])) {
    if (!password_verify($args['mdp'], $user->mdp_hash)) {
        fail();
    }
    else {
        if ($otp_secret && !OTP\verify( otp_secret: $user->otp_secret, otp_reponse: $otp_secret)) {
            fail();
        }
    }
    session_regenerate_id(true);
    Auth\se_connecter_pro($user->id);
    succeed();
}

fail();

function fail(): never
{
    redirect_to(location_connexion(error: "Nom d'utilisateur ou mot de passe incorrect."));
    exit;
}

function succeed(): never
{
    global $args;
    redirect_to($args['return_url'] ?? Auth\location_home());
    exit;
}
