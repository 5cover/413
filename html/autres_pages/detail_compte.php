<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'model/Compte.php';

$compte = notfalse(Compte::from_db(Auth\id_compte_connecte()));

$page = new Page("$compte->prenom $compte->nom", body_id: 'detail_compte');

$page->put(function () use ($compte) {
    ?>
    <h1>Informations de votre compte</h1>
    <section id="info_compte">
        <!-- PHP dynamique commence ici -->
        <?php if ($compte instanceof Membre): ?>
            <div id="pseudo">
                <p>Pseudo :</p>
                <span><?= $compte->pseudo ?></span>
            </div>
        <?php elseif ($compte instanceof Professionnel): ?>
            <div id="denomination">
                <p>Dénomination :</p>
                <span><?= $compte->denomination ?></span>
            </div>
            <?php if ($compte instanceof ProfessionnelPrive): ?>
                <div id="siren">
                    <p>Siren :</p>
                    <span><?= $compte->siren ?></span>
                </div>
            <?php endif ?>
        <?php endif ?>

        <div id="nom">
            <p>Nom :</p>
            <span><?= $compte->nom ?></span>
        </div>

        <div id="prenom">
            <p>Prénom :</p>
            <span><?= $compte->prenom ?></span>
        </div>

        <div id="email">
            <p>Email :</p>
            <span><?= $compte->email ?></span>
        </div>

        <div id="telephone">
            <p>Numéro de téléphone :</p>
            <span><?= $compte->telephone ?></span>
        </div>

        <div id="adresse">
            <p>Adresse :</p>
            <span><?= $compte->adresse->format() ?></span>
        </div>

        <div id="api_key">
            <p>Clé d'API :</p></span>
            <?php if ($compte->api_key) { ?>
            <code class="spoiler"><?= $compte->api_key ?></code>
            <?php } else { ?>
            &ndash;
            <?php } ?>
            </span>
        </div>

        <a href="modif_compte.php?id=<?= $compte->id ?>">Modifier</a>
    </section>
    <?php
});
