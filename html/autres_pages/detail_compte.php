<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'model/Compte.php';

$compte = Compte::from_db(Auth\id_compte_connecte());
if ($compte === false) fail_404();

$page = new Page("$compte->prenom $compte->nom", body_id: 'detail_compte');

$page->put(function () use ($compte) {
    ?>
    <h1>Informations de votre compte</h1>
    <section id="info_compte">
        <!-- PHP dynamique commence ici -->
        <?php if ($compte instanceof Membre): ?>
            <div id="pseudo">
                <p>Pseudo&nbsp;:</p>
                <span><?= h14s($compte->pseudo) ?></span>
            </div>
        <?php elseif ($compte instanceof Professionnel): ?>
            <div id="denomination">
                <p>Dénomination&nbsp;:</p>
                <span><?= h14s($compte->denomination) ?></span>
            </div>
            <?php if ($compte instanceof ProfessionnelPrive): ?>
                <div id="siren">
                    <p>Siren&nbsp;:</p>
                    <span><?= h14s($compte->siren) ?></span>
                </div>
            <?php endif ?>
        <?php endif ?>

        <div id="nom">
            <p>Nom&nbsp;:</p>
            <span><?= h14s($compte->nom) ?></span>
        </div>

        <div id="prenom">
            <p>Prénom&nbsp;:</p>
            <span><?= h14s($compte->prenom) ?></span>
        </div>

        <div id="email">
            <p>Email&nbsp;:</p>
            <span><?= h14s($compte->email) ?></span>
        </div>

        <div id="telephone">
            <p>Numéro de téléphone&nbsp;:</p>
            <span><?= h14s($compte->telephone) ?></span>
        </div>

        <div id="adresse">
            <p>Adresse&nbsp;:</p>
            <span><?= h14s($compte->adresse) ?></span>
        </div>

        <div id="api_key">
            <p>Clé d'API&nbsp;:</p><span>
            <?php if ($compte->api_key) { ?>
            <code class="spoiler"><?= $compte->api_key ?></code>
            <?php } else { ?>
            &ndash;
            <?php } ?>
            </span>
        </div>

        <a class="btn-publish" href="<?= h14s(location_modifier_compte($compte->id)) ?>">Modifier</a>
    </section>
    <?php
});
