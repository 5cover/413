<?php

// Comme toutes les pages appellent ce script, on peut considérer ici comme l'endroit ou exécuter le code qui doit affecter TOUT
error_reporting(E_ALL & ~E_NOTICE);  // Notamment confiurer PHP pour afficher + d'erreurs

require_once 'auth.php';
require_once 'redirect.php';
require_once 'model/Avis.php';

final class Page
{
    private const BASE_STYLESHEETS = [
        'offer-list.css',
        'footer.css',
        'dynamic-table.css',
        'style.css',
    ];

    private const BASE_SCRIPTS = [
        'module/base.js' => 'type="module"',
    ];

    /**
     * @param string $title Le titre du document.
     * @param array<string> $stylesheets Un liste de chemins relatifs dans au dossier `/style` des feuilles de style CSS à inclure.
     * @param array<string, string> $scripts Un tableau associatif mappant des chemins relatifs au dossier `/script_js` des script JS à inclure vers leurs paramètres qui correspond au reste de l'attribut.
     */
    function __construct(
        readonly string $title,
        readonly array $stylesheets  = [],
        readonly array $scripts      = [],
        readonly ?string $body_id    = null,
        readonly ?string $main_class = null,
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
            <main <?= mapnull($this->main_class, fn(string $class_list) => "class=\"$class_list\"") ?>><?php is_string($main) ? (print $main) : $main() ?></main>
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
     *     <script defer type="module" src="/script_js/module/base.js">
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
            <link rel="icon" href="/icon/favicon-32x32.png" type="image/x-icon">
            <?php
            foreach (array_merge(self::BASE_STYLESHEETS, $this->stylesheets) as $href) {
                // Si c'est une URL (contient un ':'), on laisse tel quel. Sinon on préfixe par le dossier des feuilles de style.
                ?>
                <link rel="stylesheet" href="<?= str_contains($href, ':') ? $href : "/style/$href" ?>"><?php
            }
            ?>
            <?php
            foreach (array_merge(self::BASE_SCRIPTS, $this->scripts) as $src => $attrs) {
                // Idem.
                ?>
                <script <?= $attrs ?> src="<?= str_contains($src, ':') ? $src : "/script_js/$src" ?>"></script><?php
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
                <a href="<?= h14s(Auth\location_home()) ?>"><img src="/images/logo.png" alt="Logo pact"></a>
            </div>
            <?php
            if (Auth\est_connecte()) {
                ?>
                <a href="<?= h14s(location_logout()) ?>">
                    <div class="auth-button">
                        <img src="/images/logout-icon.png" alt="Profil">
                        <span>Déconnexion</span>
                    </div>
                </a>
                <?php
            }
            if (null !== ($id_pro = Auth\id_pro_connecte())) {
                ?>
                <div id="header_pro">
                    <?php
                    $nb_avis_non_lus = 0;
                    $avis_non_lus    = Avis::getAvisNonLus($id_pro);
                    $nb_avis_non_lus = count($avis_non_lus);
                    ?>
                    <button id="btn-notifications">
                        <span id="notif-count"><?= $nb_avis_non_lus ?></span>
                    </button>

                    <div id="notif-list">
                        <?php if ($nb_avis_non_lus > 0) { ?>
                            <ul>
                                <?php foreach ($avis_non_lus as $avis) { ?>
                                    <li id="notif-li">
                                        <a href="detail_offre_pro.php?id=<?= $avis['auteur'] ?>#1">
                                            <strong><?= h14s($avis['pseudo']) ?></strong> :
                                            <?= h14s(substr($avis['commentaire'], 0, 50)) ?><?php
                                                 if (strlen($avis['commentaire']) > 50) { echo '&hellip;'; } ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <p>Aucune nouvelle notification.</p>
                        <?php } ?>
                    <?php }

            if (Auth\est_connecte()) { ?>
                    </div>
                    <a href="/autres_pages/detail_compte.php">
                        <div class="auth-button">
                            <img src="/images/profile-icon.png" alt="Compte">
                            <span>Compte</span>
                        </div>
                    </a>
                </div>
            <?php } else { ?>
                <a href="connexion.php">
                    <div class="auth-button">
                        <img src="/images/login-icon.png" alt="Profil">
                        <span>Connexion</span>
                    </div>
                </a>
            <?php } ?>
        </header>
        <?php
    }

    private function put_footer(): void
    {
        ?>
        <footer>
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="/">
                        <img src="/images/logo_vertical_big.png" alt="Logo PACT" loading="lazy">
                    </a>
                </div>
                <ul class="social-links">
                    <li><a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/facebook.png" alt="Facebook" width="90" height="90" loading="lazy" title="Facebook"></a></li>
                    <li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/instagram.png" alt="Instagram" width="90" height="90" loading="lazy" title="Instagram"></a></li>
                    <li><a href="https://www.x.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/x.png" alt="X" width="90" height="90" loading="lazy" title="X"></a></li>
                    <li><a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer"><img src="/images/social/youtube.png" alt="YouTube" width="90" height="90" loading="lazy" title="YouTube"></a></li>
                </ul>
                <div>
                    <p><a href="https://github.com/5cover/413/issues/new" target="_blank" rel="noopener noreferrer">Nous contacter</a></p>
                    <p><a href="<?= h14s(location_mentions_legales()) ?>">Mentions légales</a></p>
                    <p><a href="<?= h14s(location_cgu()) ?>"><abbr title="Conditions Générales d'Utilisation">CGU</abbr></a></p>
                    <p><a href="<?= h14s(location_cgv()) ?>"><abbr title="Conditions Générales de Vente">CGV</abbr></a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>413 &ndash; SAÉ 3.02</p>
                <p>&copy; 2024 TripEnArvor</p>
                <p>IUT de Lannion &ndash; BUT Informatique</p>
                <p><em><?= DB\connect()->query_no - 1 ?> requêtes SQL</em></p>
            </div>
        </footer>
        <?php
    }
}
