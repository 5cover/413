<?php
require_once 'model/OffreFast.php';

/**
 * Redirige l'utilisateur vers une URL, mettant fin au script actuel.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelée <b>avant</b> tout envoi de HTML.
 * @param string $location L'URL où rediriger l'utilisateur.
 * @return never
 */
function redirect_to(string $location): never
{
    header("Location: $location");
    exit;
}

/**
 * Obtient l'URL de la page de connexion.
 * @param ?string $error L'erreur à afficher. `null` pour pas d'erreur.
 * @param ?string $return_url L'URL ou rediriger lorsque la connexion réussit. `null` indique de rediriger l'utilisateur vers la page d'accueil (pro ou membre).
 * @return string L'URL de la page de connection.
 */
function location_connexion(?string $error = null, ?string $return_url = null, ?string $pseudo = null): string
{
    return '/autres_pages/connexion.php?' . http_build_query(['error' => $error, 'return_url' => $return_url, 'pseudo' => $pseudo]);
}

/**
 * Obtient l'URL de la page de détail d'offre.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre.
 */
function location_detail_offre(int $id_offre): string
{
    return '/autres_pages/detail_offre.php?' . http_build_query(['id' => $id_offre]);
}

/**
 * Obtient l'URL de la page de détaille d'offre professionnel.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre professionnel.
 */
function location_detail_offre_pro(int $id_offre): string
{
    return '/autres_pages/detail_offre_pro.php?' . http_build_query(['id' => $id_offre]);
}

function location_creation_offre(): string
{
    return '/autres_pages/choix_categorie_creation_offre.php';
}

function location_facturation(?int $id_offre = null): string
{
    return '/autres_pages/facturation.php?' . http_build_query(['id_offre' => $id_offre]);
}

function location_detail_compte(): string
{
    return '/autres_pages/detail_compte.php';
}

function location_modifier_offre(Categorie $categorie, int $id_offre): string
{
    return '/autres_pages/modif_offre.php?' . http_build_query(['categorie' => $categorie->value, 'id' => $id_offre]);
}

function location_modifier_compte(int $id, ?string $error = null): string
{
    return '/autres_pages/modif_compte.php?' . http_build_query(['id' => $id, 'return_url' => $_SERVER['REQUEST_URI'], 'error' => $error]);
}

function location_supprimer_compte(int $id_compte): string
{
    return '/auto/supprimer_compte.php?' . http_build_query(['id_compte' => $id_compte]);
}

function location_signaler(int $id_compte, int $id_signalable, string $raison): string
{
    return '/do/signaler.php?' . http_build_query(['id_compte' => $id_compte, 'id_signalable' => $id_signalable, 'raison' => $raison]);
}

function location_login(): string
{
    return '/auto/login.php';
}

function location_logout(): string
{
    return '/auto/logout.php';
}

function location_modifier_avis(int $id_offre, int $id_avis): string
{
    return '/autres_pages/avis/modifier.php?' . http_build_query(['id_offre' => $id_offre, 'id_avis' => $id_avis]);
}

function location_repondre_avis(int $id_avis): string
{
    return '/auto/avis/repondre.php?' . http_build_query(['id_avis' => $id_avis, 'return_url' => $_SERVER['REQUEST_URI']]);
}

function location_avis_supprimer(int $id_avis, string $return_url): string
{
    return '/auto/avis/supprimer.php?' . http_build_query(['id_avis' => $id_avis, 'return_url' => $return_url]);
}

function location_mentions_legales(): string
{
    return '/autres_pages/legal/mentions-legales.php';
}

function location_cgu(): string
{
    return '/autres_pages/legal/cgu.php';
}

function location_cgv(): string
{
    return '/autres_pages/legal/cgv.php';
}
