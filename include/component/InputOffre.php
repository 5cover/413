<?php

require_once 'auth.php';
require_once 'component/DynamicTable.php';
require_once 'component/InputAdresse.php';
require_once 'component/InputDuree.php';
require_once 'component/InputImage.php';
require_once 'const.php';
require_once 'model/Activite.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/NonEmptyRange.php';
require_once 'model/ParcAttractions.php';
require_once 'model/ProfessionnelPrive.php';
require_once 'model/Restaurant.php';
require_once 'model/Spectacle.php';
require_once 'model/Time.php';
require_once 'model/Visite.php';
require_once 'Parsedown.php';
require_once 'redirect.php';
require_once 'util.php';

/**
 * @extends Input<Offre>
 */
final class InputOffre extends Input
{
    private readonly InputDuree $input_indication_duree;
    private readonly InputAdresse $input_adresse;
    private readonly InputImage $input_image_principale;
    private readonly InputImage $input_image_plan;
    private readonly DynamicTable $tarifs;
    private readonly DynamicTable $periodes;
    private readonly InputImage $galerie;

    function __construct(
        private readonly string $categorie,
        private readonly Professionnel $professionnel,
        string $id      = '',
        string $name    = '',
        string $form_id = ''
    ) {
        parent::__construct($id, $name, $form_id);
        $this->input_indication_duree = new InputDuree(
            id: $this->id('indication_duree'),
            name: $this->name('indication_duree'),
            form_id: $form_id,
        );
        $this->input_adresse          = new InputAdresse(
            id: $this->id('adresse'),
            name: $this->name('adresse'),
            form_id: $form_id,
        );
        $this->input_image_principale = new InputImage(
            fieldset_legend: 'Photo principale',
            id: $this->id('image_principale'),
            name: $this->name('image_principale'),
            form_id: $form_id,
        );
        $this->input_image_plan       = new InputImage(
            fieldset_legend: 'Photo du plan',
            id: $this->id('input_image_plan'),
            name: $this->name('input_image_plan'),
            form_id: $form_id,
        );
        $this->tarifs                 = new DynamicTable(
            columns: ['Nom', 'Montant'],
            put_row: function (DynamicTable $dt, ?array $row) {
                ?>
                <td><input <?= $dt->form_attr ?>
                    name="<?= $this->name('tarifs') ?>[nom][]"
                    type="text"
                    placeholder="Enfant, Sénior&hellip;"
                    required
                    readonly
                    value="<?= $row === null ? null : h14s($row['nom']) ?>"></td>
                <td><input <?= $dt->form_attr ?> name="<?= $this->name('tarifs') ?>[montant][]"
                    type="number"
                    min="0"
                    placeholder="Prix"
                    required
                    value="<?= $row === null ? null : $row['montant'] ?>">&nbsp;€</td>
                <?php
            },
            put_prompt: function (DynamicTable $dt) {
                ?>
                <td><input type="text" placeholder="Enfant, Sénior&hellip;" required></td>
                <td><input type="number" min="0" placeholder="Prix" required> €</td>
                <?php
            },
            id: $this->id('table-tarifs'),
            name: $this->name('tarifs'),
            initial_rows: [['nom' => 'Adulte', 'montant' => 10]],
            form_id: $form_id,
        );
        $this->periodes = new DynamicTable(
            columns: ['Début', 'Fin'],
            put_row: function (DynamicTable $dt, ?array $horaire) {
                ?>
                <td><input <?= $dt->form_attr ?> name="<?= $this->name('periodes') ?>[debut][]" type="datetime-local" value="<?= $horaire === null ? null : $horaire[0] ?>)"></td>
                <td><input <?= $dt->form_attr ?> name="<?= $this->name('periodes') ?>[fin][]" type="datetime-local" value="<?= $horaire === null ? null : $horaire[1] ?>"></td>
                <?php
            },
            put_prompt: function (DynamicTable $dt) {
                ?>
                <td><input type="datetime-local" required></td>
                <td><input type="datetime-local" required></td>
                <?php
            },
            id: $this->id('table-periodes'),
            name: $this->name('periodes'),
            form_id: $form_id
        );
        // todo: dynamic list
        $this->galerie = new InputImage(
            fieldset_legend: 'Galerie',
            id: 'galerie',
            name: 'galerie',
            form_id: $form_id,
            multiple: true,
        );
    }

    /**
     * Récupère l'offre saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param ?Offre $current_offre L'offre à modifier ou `null` pour une création. Doit exister dans la BDD.
     * @return ?Offre
     */
    function get(array $get_or_post, ?Offre $current_offre = null): ?Offre
    {
        $image_principale = $this->input_image_principale->get($get_or_post)[0] ?? $current_offre?->image_principale;

        if ($image_principale === null) return null;

        $args_offre = [
            $current_offre?->id,
            $this->input_adresse->get($get_or_post),
            $image_principale,
            $this->professionnel,
            Abonnement::all()[getarg($get_or_post, $this->name('libelle_abonnement'), required: false) ?? 'gratuit'],
            getarg($get_or_post, $this->name('titre')),
            getarg($get_or_post, $this->name('resume')),
            getarg($get_or_post, $this->name('description_detaillee')),
            getarg($get_or_post, $this->name('url_site_web'), required: false),
            new MultiRange(array_map(
                fn($row) => new NonEmptyRange(
                    true,
                    FiniteTimestamp::parse($row['debut']),
                    FiniteTimestamp::parse($row['fin']),
                    false,
                ),
                $this->periodes->get($get_or_post) ?? [],
            )),
        ];

        $offre = match ($this->categorie) {
            Activite::CATEGORIE        => new Activite(
                $args_offre,
                $this->input_indication_duree->get($get_or_post),
                getarg($get_or_post, 'age_requis', arg_int(1), required: false),
                getarg($get_or_post, 'prestations_incluses'),
                getarg($get_or_post, 'prestations_non_incluses', required: false),
            ),
            ParcAttractions::CATEGORIE => new ParcAttractions(
                $args_offre,
                getarg($get_or_post, 'age_requis', arg_int(1), required: false),
                getarg($get_or_post, 'nb_attractions', arg_int(0)),
                $this->input_image_plan->get($get_or_post)[0] ?? $current_offre?->image_principale,
            ),
            Spectacle::CATEGORIE       => new Spectacle(
                $args_offre,
                $this->input_indication_duree->get($get_or_post),
                getarg($get_or_post, 'capacite_accueil', arg_int(0)),
            ),
            Restaurant::CATEGORIE      => new Restaurant(
                $args_offre,
                getarg($get_or_post, 'carte'),
                getarg($get_or_post, 'richesse'),
                getarg($get_or_post, 'sert_petit_dejeuner', required: false) ?? false,
                getarg($get_or_post, 'sert_brunch', required: false) ?? false,
                getarg($get_or_post, 'sert_dejeuner', required: false) ?? false,
                getarg($get_or_post, 'sert_diner', required: false) ?? false,
                getarg($get_or_post, 'sert_boissons', required: false) ?? false,
            ),
            Visite::CATEGORIE          => new Visite(
                $args_offre,
                $this->input_indication_duree->get($get_or_post),
            ),
        };

        // Horaires
        foreach (getarg($get_or_post, $this->name('horaires'), required: false) ?? [] as $dow => $horaires) {
            $offre->ouverture_hebdomadaire[$dow] = new MultiRange(array_map(
                fn($horaire_row) => new NonEmptyRange(
                    true,
                    Time::parse($horaire_row['debut']),
                    Time::parse($horaire_row['fin']),
                    false,
                ),
                soa_to_aos($horaires),
            ));
        }

        // Galerie
        foreach ($this->galerie->get($get_or_post) as $image) {
            $offre->galerie->add($image);
        }

        // Tags
        foreach (getarg($get_or_post, $this->name('tags'), required: false) ?? [] as $tag) {
            $offre->tags->add($tag);
        }

        // Tarifs
        foreach ($this->tarifs->get($get_or_post) ?? [] as $tarif_row) {
            $offre->tarifs->add($tarif_row['nom'], $tarif_row['montant']);
        }

        return $offre;
    }

    /**
     * @inheritDoc
     */
    function put(mixed $current = null): void
    {
        ?>
        <h1><?= $current === null ? 'Créer' : 'Modifier' ?> <?= h14s(CATEGORIES_OFFRE[$this->categorie]) ?></h1>

        <section id="<?= $this->id('type-abonnement') ?>">
            <h2>Abonnement</h2>
            <?php if ($this->professionnel instanceof ProfessionnelPrive) { ?>
                <ul id="<?= $this->id('liste-choix-abonnement') ?>">
                    <?php
                    $first = true;
                    foreach (Abonnement::all() as $abo) {
                        // skip abo gratuit
                        if ($abo->prix_journalier == 0)
                            continue;
                        ?>
                    <li>
                        <label><input <?= $this->form_attr ?>
                            id="<?= $this->id("libelle_abonnement_$abo->libelle") ?>"
                            name="<?= $this->name('libelle_abonnement') ?>"
                            type="radio"
                            value="<?= h14s($abo->libelle) ?>"
                            required
                            <?= $current === null ? '' : 'disabled' ?>
                            <?= $current?->abonnement->libelle === 'standard' ? 'checked' : '' ?>
                            <?php
                            if ($first) { echo 'checked'; $first = false; }
                            ?>>
                            <?= h14s(ucfirst($abo->libelle)) ?> (<?= h14s($abo->description) ?>, <?= $abo->prix_journalier ?>€&nbsp;/&nbsp;jour)</label>
                    </li>
                    <?php } ?>
                </ul>
                <aside>
                    <p><img src="/icon/icons8-haute-importance-100.png" alt="Haute importance" width="25" height="25"> Attention&nbsp;! Une fois l'abonnement choisi, vous ne pourrez plus le modifier.</p>
                </aside>
            <?php } else { ?>
                <p>Comme vous êtres un professionnel public, l'offre créée sera gratuite (pas de facturation)</p>
                <p><a href="<?= h14s(location_mentions_legales()) ?>" target="_blank" rel="noopener noreferrer">Plus d'informations&hellip;</>
                </p>
            <?php } ?>
        </section>

        <section id="<?= $this->id('info-generales') ?>">
            <h2>Informations générales</h2>
            <div>
                <label for="<?= $this->id('titre') ?>">Titre*</label>
                <p>
                    <input <?= $this->form_attr ?>
                        id="<?= $this->id('titre') ?>"
                        name="<?= $this->name('titre') ?>"
                        type="text"
                        required
                        value="<?= h14s($current?->titre) ?>">
                </p>
                <label for="<?= $this->id('resume') ?>">Resumé*</label>
                <p>
                    <input <?= $this->form_attr ?>
                        id="<?= $this->id('resume') ?>"
                        name="<?= $this->name('resume') ?>"
                        type="text"
                        required
                        value="<?= h14s($current?->resume) ?>">
                </p>
                <label for="<?= h14s($this->input_adresse->for_id()) ?>">Adresse*</label>
                <?php $this->input_adresse->put($current?->adresse) ?>
                <label for="<?= $this->id('url_site_web') ?>">Site Web</label>
                <p>
                    <input <?= $this->form_attr ?>
                        id="<?= $this->id('url_site_web') ?>"
                        name="<?= $this->name('url_site_web') ?>"
                        type="url"
                        value="<?= h14s($current?->url_site_web) ?>">
                </p>
            </div>
        </section>

        <section>
            <h2>Photo principale</h2>
            <?php $this->input_image_principale->put(
                $current === null ? null : [$current->image_principale],
                $current === null,
            ) ?>
        </section>
        <section id="<?= $this->id('tarifs') ?>">
            <h2>Tarifs</h2>
            <?php if ($this->professionnel instanceof ProfessionnelPrive) { ?>
                <?php $this->tarifs->put($current?->tarifs) ?>
            <?php } else { ?>
                <p>En tant que professionnel public, vous ne pouvez pas ajouter de grille tarifaire à votre offre gratuite.</p>
            <?php } ?>
        </section>

        <section id="<?= $this->id('horaires-hebdomadaires') ?>">
            <h2>Horaires hebdomadaires</h2>
            <div>
                <?php foreach (JOURS_SEMAINE as $dow => $jour) { ?>
                    <article id="<?= $this->id($jour) ?>">
                        <h3><?= h14s(ucfirst($jour)) ?></h3>
                        <button id="<?= $this->id("button-add-horaire-$dow") ?>" type="button">+</button>
                        <table id="<?= $this->id("table-horaires-$dow") ?>">
                            <thead>
                                <tr>
                                    <th>Début</th>
                                    <th>Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($current?->ouverture_hebdomadaire[$dow] ?? [] as $horaire) { ?>
                                    <tr>
                                        <td><input <?= $this->form_attr ?>
                                            name="<?= $this->name('horaires') ?>[<?= $dow ?>][debut][]"
                                            type="time"
                                            required
                                            value="<?= $horaire->lower ?>"></td>
                                        <td><input <?= $this->form_attr ?>
                                            name="<?= $this->name('horaires') ?>[<?= $dow ?>][fin][]"
                                            type="time"
                                            required
                                            value="<?= $horaire->upper ?>"></td>
                                        <td><button type="button">-</button></td>
                                    </tr><?php } ?>
                            </tbody>
                        </table>
                        <template id="<?= $this->id("template-horaire-tr-$dow") ?>">
                            <tr>
                                <td><input <?= $this->form_attr ?>
                                    name="<?= $this->name('horaires') ?>[<?= $dow ?>][debut][]"
                                    type="time"
                                    required></td>
                                <td><input <?= $this->form_attr ?>
                                    name="<?= $this->name('horaires') ?>[<?= $dow ?>][fin][]"
                                    type="time"
                                    required></td>
                                <td><button type="button">-</button></td>
                            </tr>
                        </template>
                    </article>
                <?php } ?>
            </div>
        </section>

        <section id="<?= $this->id('horaires-ponctuels') ?>">
            <h2>Horaires ponctuels</h2>
            <?php $this->periodes->put() ?>
        </section>

        <section id="<?= $this->id('tags') ?>">
            <h2>Tags</h2>
            <ul id="<?= $this->id('list-tag') ?>">
                <?php
                foreach ($this->categorie === Restaurant::CATEGORIE ? TAGS_RESTAURANT : DEFAULT_TAGS as $tag) {
                    ?>
                    <li><label><input <?= $this->form_attr ?>
                        id="<?= $this->id("tag_$tag") ?>"
                        name="<?= $this->name('tags') ?>[<?= h14s($tag) ?>]"
                        type="checkbox"><?= h14s($tag) ?></label></li>
                <?php } ?>
            </ul>
        </section>

        <section>
            <h2>Description détaillée</h2>
            <textarea class="text" <?= $this->form_attr ?>
                id="<?= $this->id('description_detaillee') ?>"
                name="<?= $this->name('description_detaillee') ?>"
                required
                ><?= h14s($current?->description_detaillee) ?></textarea>
        </section>

        <section id="<?= $this->id('image-creation-offre') ?>">
            <h2>Galerie</h2>
            <?php $this->galerie->put(
                $current?->galerie->images,
                $current === null,
            ) ?>
        </section>

        <section id="<?= $this->id('infos-detaillees') ?>">
            <h2>Informations détaillées</h2>
            <?php
            switch ($this->categorie) {
                case Activite::CATEGORIE:
                    /** @var ?Activite */
                    $activite = $current;
                    ?>
                    <p><label>Âge requis&nbsp;: <input <?= $this->form_attr ?>
                        id="<?= $this->id('age_requis') ?>"
                        name="<?= $this->name('age_requis') ?>"
                        type="number"
                        min="1"
                        value="<?= $activite?->age_requis ?>"> an</label></p>
                    <p>Prestations incluses*</p>
                    <textarea <?= $this->form_attr ?>
                        id="<?= $this->id('prestations_incluses') ?>"
                        name="<?= $this->name('prestations_incluses') ?>"
                        required
                        ><?= h14s($activite?->prestations_incluses) ?></textarea>
                    <p>Prestations non incluses</p>
                    <textarea <?= $this->form_attr ?>
                        id="<?= $this->id('prestations_non_incluses') ?>"
                        name="<?= $this->name('prestations_non_incluses') ?>"
                        ><?= h14s($activite?->prestations_non_incluses) ?></textarea>
                    <?php
                    $this->put_input_indication_duree($activite?->indication_duree);
                    break;
                case ParcAttractions::CATEGORIE:
                    /** @var ?ParcAttractions */
                    $parc_attractions = $current;
                    ?>
                    <p><label>Nombre d'attractions&nbsp;: <input <?= $this->form_attr ?>
                        id="<?= $this->id('nb_attractions') ?>"
                        name="<?= $this->name('nb_attractions') ?>"
                        type="number"
                        min="0"
                        value="<?= $parc_attractions?->nb_attractions ?>"></label></p>
                    <p><label>Âge requis&nbsp;: <input <?= $this->form_attr ?>
                        id="<?= $this->id('age_requis') ?>"
                        name="<?= $this->name('age_requis') ?>"
                        type="number"
                        min="1"
                        value="<?= $parc_attractions?->age_requis ?>"> an</label></p>
                    <?php
                    $this->input_image_plan->put(
                        $parc_attractions === null ? null : [$parc_attractions->image_plan],
                        $parc_attractions === null,
                    );
                    break;
                case Restaurant::CATEGORIE:
                    /** @var ?Restaurant */
                    $restaurant = $current;
                    ?>
                    <fieldset>
                        <legend>Échelle tarifaire</legend>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('richesse-1') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="1" <?= $restaurant?->richesse === 1 ? 'checked' : '' ?>>
                            €</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('richesse-2') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="2" <?= $restaurant?->richesse === 2 ? 'checked' : '' ?>> 
                            €</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('richesse-3') ?>"
                            name="<?= $this->name('richesse') ?>"
                            type="radio"
                            value="3" <?= $restaurant?->richesse === 3 ? 'checked' : '' ?>> €
                            €</label></p>
                    </fieldset>
                    <fieldset>
                        <legend>Repas servis</legend>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('sert_petit_dejeuner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_petit_dejeuner') ?>"
                            <?= $restaurant?->sert_petit_dejeuner ? 'checked' : '' ?>>
                            Petit déjeuner</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('sert_brunch') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_brunch') ?>"
                            <?= $restaurant?->sert_brunch ? 'checked' : '' ?>>
                            Brunch</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('sert_dejeuner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_dejeuner') ?>"
                            <?= $restaurant?->sert_dejeuner ? 'checked' : '' ?>>
                            Déjeuner</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('sert_diner') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_diner') ?>"
                            <?= $restaurant?->sert_diner ? 'checked' : '' ?>>
                            Dîner</label></p>
                        <p><label><input <?= $this->form_attr ?>
                            id="<?= $this->id('sert_boissons') ?>"
                            type="checkbox"
                            name="<?= $this->name('sert_boissons') ?>"
                            <?= $restaurant?->sert_boissons ? 'checked' : '' ?>>
                            Boissons</label></p>
                    </fieldset>
                    <p>Carte</p>
                    <textarea <?= $this->form_attr ?>
                        name="<?= $this->name('carte') ?>"
                        ><?= h14s($restaurant?->carte) ?></textarea>
                    <?php
                    break;
                case Spectacle::CATEGORIE:
                    /** @var ?Spectacle */
                    $spectacle = $current;
                    ?>
                    <p><label>Capacité d'accueil&nbsp;: <input <?= $this->form_attr ?>
                        id="<?= $this->id('capacite_accueil') ?>"
                        name="<?= $this->name('capacite_accueil') ?>"
                        type="number"
                        min="0"
                        required
                        value="<?= $spectacle?->capacite_accueil ?>">
                        pers.</label></p>
                    <?php
                    $this->put_input_indication_duree($spectacle?->indication_duree);
                    break;
                case Visite::CATEGORIE:
                    /** @var ?Visite */
                    $visite = $current;
                    $this->put_input_indication_duree($visite?->indication_duree);
                    break;
            }
            ?>
        </section>
        <?php
    }

    function put_input_indication_duree(?Duree $current)
    {
        ?>
        <label for="<?= h14s($this->input_indication_duree->for_id()) ?>">Durée estimée&nbsp;: </label>
        <?php $this->input_indication_duree->put($current) ?>
        <?php
    }
}
