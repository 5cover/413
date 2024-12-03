<?php
require_once 'db.php';
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Adresse.php';

/**
 * @extends Input<Adresse>
 */
final class InputAdresse extends Input
{
    function __construct(string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
    }

    /**
     * Récupère l'adresse saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param ?int $current_id_adresse L'ID de l'adresse à modifier ou `null` pour une création.
     * @param bool $required Si l'adresse est requise. Quand l'adresse est manquante, si `false` a été passé, la fonction retourne `null`. Sinon, déclenche une erreur.
     */
    function get(array $get_or_post, ?int $current_id_adresse = null, bool $required = true): ?Adresse
    {
        $data = getarg($get_or_post, $this->name, required: $required);
        return $data === null ? null : new Adresse(
            $current_id_adresse,
            notfalse(Commune::from_db_by_nom($data['commune'])),
            getarg($data, 'numero_voie', arg_int(1), required: false),
            $data['complement_numero'] ?: null,
            $data['nom_voie'] ?: null,
            $data['localite'] ?: null,
            $data['precision_int'] ?: null,
            $data['precision_ext'] ?: null,
            $data['latitude'] ?: null,
            $data['longitude'] ?: null,
        );
    }

    function for_id(): string {
        return $this->id('commune');
    }

    /**
     * Affiche l'HTML du composant.
     * @param ?Adresse $current L'adresse à modifier ou `null` pour une création.
     */
    function put(mixed $current = null): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
        self::put_datalist();
?>
<details <?= $this->id ? "id=\"$this->id\"" : '' ?> class="input-address">
    <summary>
        <input <?= $form_attr ?> type="text" readonly>
    </summary>
    <p><label>Commune&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('commune') ?>"
        name="<?= $this->name('commune') ?>"
        type="text"
        list="datalist-input-address-communes"
        autocomplete="on"
        required
        value="<?= $current?->commune->nom ?>">
    </label></p>
    <p><label>Localité&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('localite') ?>"
        name="<?= $this->name('localite') ?>"
        type="text"
        maxlength="255"
        placeholder="hameau, lieu-dit&hellip; (optionnel)"
        value="<?= $current?->localite ?>">
    </label></p>
    <p><label>Nom voie&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('nom_voie') ?>"
        name="<?= $this->name('nom_voie') ?>"
        type="text"
        maxlength="255"
        placeholder="rue de l'Église&hellip; (optionnel)"
        value="<?= $current?->nom_voie ?>">
    </label></p>
    <p><label>Numéro voie&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('numero_voie') ?>"
        name="<?= $this->name('numero_voie') ?>"
        type="number"
        min="1"
        placeholder="1,2&hellip; (optionnel)"
        value="<?= $current?->numero_voie ?>">
    </label></p>
    <p><label>Complément numéro&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('complement_numero') ?>"
        name="<?= $this->name('complement_numero') ?>"
        type="text"
        maxlength="10"
        placeholder="bis, ter&hellip; (optionnel)"
        value="<?= $current?->complement_numero ?>">
    </label></p>
    <p><label>Précision interne&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('precision_int') ?>"
        name="<?= $this->name('precision_int') ?>"
        type="text" maxlength="255"
        placeholder="apt., boîte à lettre, étage (optionnel)&hellip;"
        value="<?= $current?->precision_int ?>">
    </label></p>
    <p><label>Précision externe&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id('precision_ext') ?>"
        name="<?= $this->name('precision_ext') ?>"
        type="text"
        maxlength="255"
        placeholder="bâtiment, voie, résidence (optionnel)&hellip;"
        value="<?= $current?->precision_ext ?>">
    </label></p>
    <input type="hidden" name="latitude" value="<?= $current?->latitude ?>">
    <input type="hidden" name="longitude" value="<?= $current?->longitude ?>">
</details>
<?php
    }

    // Put the datalist only once per page.
    private static bool $datalist_put = false;

    private static function put_datalist()
    {
        if (self::$datalist_put) {
            return;
        }
        self::$datalist_put = true;
        $communes           = DB\connect()->query('select nom from _commune limit 100')->fetchAll();
?>
<datalist id="datalist-input-address-communes">
<?php foreach ($communes as $c) { ?>
    <option><?= $c['nom'] ?></option>
<?php } ?>
</datalist>
<?php
    }
}
