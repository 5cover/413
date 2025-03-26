<?php

/**
 * auth.php
 * Fonctions relatives à l'authentification
 */

namespace Auth;

require_once 'util.php';
require_once 'redirect.php';

notfalse(@session_start());

function location_home(): string
{
    return est_connecte_pro() ? '/autres_pages/accPro.php' : '/autres_pages/accueil.php';
}

/**
 * Déconnecte la session actuelle et redirige vers l'accueil.
 * @return never
 */
function se_deconnecter(): never
{
    assert(est_connecte());
    notfalse(session_unset());
    $_SESSION = [];
    notfalse(session_destroy());
    @session_regenerate_id(true);
    redirect_to(location_home());
}

/**
 * Sommes-nous connectés?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que membre ou professionnel, `false` sinon.
 */
function est_connecte(): bool
{
    return est_connecte_pro() || est_connecte_membre();
}

// Professionnel

/**
 * Marque la session comme connectée à une compte professionnel.
 * @param int $id_pro L'ID du professionnel qui sera désormais connecté sur la session actuelLe.
 * @return void
 */
function se_connecter_pro(int $id_pro): void
{
    $_SESSION['id_pro'] = $id_pro;
}

/**
 * Retourne l'ID du membre actuellement connecté, redirigeant vers la page de connexion sinon.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelée <b>avant</b> tout envoi de HTML.
 * @return int L'ID du professionnel actuellement connecté.
 */
function exiger_connecte_pro(): int
{
    if (($id = id_pro_connecte()) !== null) {
        return $id;
    }
    redirect_to(location_connexion(return_url: $_SERVER['REQUEST_URI'] ?? null));
    exit;
}

/**
 * Retourne l'ID du professionnel actuellement connecté.
 * @return ?int L'ID du professionnel actuellement connecté, ou `null` si la session actuelle n'est pas connectée en tant que professionnel.
 */
function id_pro_connecte(): ?int
{
    return $_SESSION['id_pro'] ?? null;
}

/**
 * Retourne l'ID du compte (membre ou professionnel) actuellement connecté.
 * @return ?int L'ID du compte actuellement connecté, ou `null` s la session actuelle n'est pas connectée.
 */
function id_compte_connecte(): ?int
{
    return id_pro_connecte() ?? id_membre_connecte();
}

/**
 * Sommes-nous connectés en tant que professionnel?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que professionnel, `false` sinon.
 */
function est_connecte_pro(): bool
{
    return isset($_SESSION['id_pro']);
}

// Membre

/**
 * Marque la session comme connectée à une compte membre.
 * @param int $id_pro L'ID du membre qui sera désormais connecté sur la session actuelLe.
 * @return void
 */
function se_connecter_membre(int $id_membre): void
{
    $_SESSION['id_membre'] = $id_membre;
}

/**
 * Retourne l'ID du professionnel actuellement connecté, redirigeant vers la page de connexion sinon.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelée <b>avant</b> tout envoi de HTML.
 * @return int L'ID du membre actuellement connecté.
 */
function exiger_connecte_membre(): int
{
    if (($id = id_membre_connecte()) !== null) {
        return $id;
    }
    redirect_to(location_connexion(return_url: $_SERVER['REQUEST_URI']));
    exit;
}

function exiger_connecte(): int
{
    if (($id = id_compte_connecte()) !== null) {
        return $id;
    }
    redirect_to(location_connexion(return_url: $_SERVER['REQUEST_URI']));
    exit;
}

/**
 * Retourne l'ID du membre actuellement connecté.
 * @return ?int L'ID du membre actuellement connecté, ou `null` si la session actuelle n'est pas connectée en tant que professionnel.
 */
function id_membre_connecte(): ?int
{
    return $_SESSION['id_membre'] ?? null;
}

/**
 * Sommes-nous connectés en tant que membre?
 * @return bool `true` si nous (la session courante) sommes connectés en tant que membre, `false` sinon.
 */
function est_connecte_membre(): bool
{
    return isset($_SESSION['id_membre']);
}
