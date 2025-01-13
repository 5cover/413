<?php

// Comme toutes les pages appellent ce script, on peut considérer ici comme l'endroit ou exécuter le code qui doit affecter TOUT
error_reporting(E_ALL & ~E_NOTICE);  // Notamment confiurer PHP pour afficher + d'erreurs

require_once 'auth.php';

final class Page
{
    private const BASE_STYLESHEETS = [
        'style.css',
        'offer-list.css',
        'footer.css',
        'dynamic-table.css',
    ];

    private const BASE_SCRIPTS = [
        'base.js' => 'defer',
    ];

    /**
     * @param string $title Le titre du document.
     * @param array<string> $stylesheets Un liste de chemins relatifs dans au dossier `/style` des feuilles de style CSS à inclure.
     * @param array<string, string> $scripts Un tableau associatif mappant des chemins relatifs au dossier `/script_js` des script JS à inclure vers leurs paramètres qui correspond au reste de l'attribut.
     */
    function __construct(
        readonly string $title,
        readonly array $stylesheets = [],
        readonly array $scripts     = [],
        readonly ?string $body_id = null,
    ) {}

    /**
     * Affiche la page.
     * @param callable(): void|string $main
     * @return void
     */
    function put(callable|string $main)
    {
        ?>
<!DOCTYPE html>
<html lang="fr">
<?php $this->put_head() ?>
<body <?= mapnull($this->body_id, fn(string $id) => "id=\"$id\"") ?>>
    <?php $this->put_header() ?>
    <main><?php is_string($main) ? (print $main) : $main() ?></main>
    <?php $this->put_footer() ?>
</body>
</html>
<?php
    }

    /**
     * Affiche l'élement `<head>` HTML avec le titre, feuilles de style CSS et les scripts JS fournis.
     *
     * Note: la feuille de stile `style.css` et le script `base.js` sont inclus dans tous les documents.
     *
     * @example location description
     * ```php
     * put_head("Création d'une offre",
     *  ['creation_offre.css'],
     *  ['module/creation_offre.js' => 'defer type="module"'])
     * ```
     * Produit l'HTML suivant (simplifié)
     * ```html
     * <head>
     *     <title>Création d'une offre</title>
     *     <link rel="stylesheet" href="/style/style.css">
     *     <link rel="stylesheet" href="/style/creation_offre.css">
     *     <script defer src="/script_js/base.js">
     *     <script defer type="module" src="/script_js/module/creation_offre.js">
     * </head>
     * ```
     * @return void
     */
    private function put_head(): void
    {
        ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h14s($this->title) ?></title>
    <link rel="icon" href="/icon/favicon-32x32.png" type="image/x-icon" alt="test">
    <?php
    foreach (array_merge(self::BASE_STYLESHEETS, $this->stylesheets) as $href) {
        // Si c'est une URL (contient un ':'), on laisse tel quel. Sinon on préfixe par le dossier des feuilles de style.
        ?><link rel="stylesheet" href="<?= str_contains($href, ':') ? $href : "/style/$href" ?>"><?php
    }
    ?>
    <?php
    foreach (array_merge(self::BASE_SCRIPTS, $this->scripts) as $src => $attrs) {
        // Idem.
        ?><script <?= $attrs ?> src="<?= str_contains($src, ':') ? $src : "/script_js/$src" ?>"></script><?php
    }
    ?>
</head>
<?php
    }

    private function put_header(): void
    {
?>
<header>
    <div class="logo">
        <?php if (Auth\est_connecte_pro()) { ?>
            <a href="accPro.php"><img src="/images/logo.png" alt="Logo pact"></a>
        <?php } else { ?>
            <a href="accueil.php"><img src="/images/logo.png" alt="Logo pact"></a>
        <?php } ?>
    </div>
    <?php
    if (Auth\est_connecte()) {
        // Vérification du statut de la session
        ?>
        <a href="/connexion/logout.php">
            <div class="auth-button">
                <img src="/images/logout-icon.png" alt="Profil">
                <span>Déconnexion</span>
            </div>
        </a>
        <div id="header_pro">
            <a href="/autres_pages/detail_compte.php">
                <div class="auth-button">
                    <img src="/images/profile-icon.png" alt="Compte">
                    <span>Compte</span>
                </div>
            </a>
            <?php if (Auth\est_connecte_pro()) { ?>
            <!-- <a href="facturation.php">
                <div class="acces-facturation">
                    <img src="/images/facturation.png" alt="Profil">
                    <span>Facturation</span>
                </div>
            </a> -->
        </div>
    <?php
        }
    } else {
        ?>
        <a href="connexion.php">
            <div class="auth-button">
                <img src="/images/login-icon.png" alt="Profil">
                <span>Connexion</span>
            </div>
        </a>
    <?php
    }
    ?>
</header>
<?php
    }

    private function put_footer(): void
    {
?>
<footer>
    <div class="footer-content">
        <div class="footer-logo">
            <a href="/"><img src="/images/logo_vertical_big.png" alt="Logo PACT" width="500" height="500" loading="lazy"></a>
            <article>
                <p><a href="https://github.com/5cover/413/issues/new" target="_blank" rel="noopener noreferrer">Nous contacter</a></p>
            </article>
        </div>
        <ul class="social-links">
            <li><a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/facebook.png" alt="Facebook" width="90" height="90" loading="lazy" title="Facebook"></a></li>
            <li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/instagram.png" alt="Instagram" width="90" height="90" loading="lazy" title="Instagram"></a></li>
            <li><a href="https://www.x.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/x.png" alt="X" width="90" height="90" loading="lazy" title="X"></a></li>
            <li><a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/youtube.png" alt="YouTube" width="90" height="90" loading="lazy" title="YouTube"></a></li>
        </ul>
        <article>
            <p><a href="/plan-du-site">Plan du site</a></p>
            <p><a href="/mentions-legales">Mentions légales</a></p>
        </article>
    </div>
    <div class="footer-bottom">
        <p>413 &ndash; SAÉ 3.02</p>
        <p>&copy; 2024 TripEnArvor</p>
        <p>IUT de Lannion &ndash; BUT Informatique</p>
    </div>
</footer>
<?php
    }
}
