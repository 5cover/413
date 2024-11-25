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
     * @inheritDoc
     */
    function getarg(array $get_or_post, bool $required = true): ?Adresse {
        $data = getarg($get_or_post, $this->name, required: $required);
        return $data === null ? null : Adresse::from_input($data);
    }

    /**
     * @inheritDoc
     */
    function put($current = null): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
        self::put_datalist();
?>
<details <?= $this->id ? "id=\"$this->id\"" : '' ?> class="input-address">
    <summary>
        <input <?= $form_attr ?> type="text" readonly>
    </summary>
    <p><label>Commune&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_commune"
        name="<?= $this->name ?>[commune]"
        type="text"
        list="datalist-input-address-communes"
        autocomplete="on"
        required
        value="<?= $current?->commune->nom ?>">
    </label></p>
    <p><label>Localité&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_localite"
        name="<?= $this->name ?>[localite]"
        type="text"
        maxlength="255"
        placeholder="hameau, lieu-dit&hellip; (optionnel)"
        value="<?= $current?->localite ?>">
    </label></p>
    <p><label>Nom voie&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_nom_voie"
        name="<?= $this->name ?>[nom_voie]"
        type="text"
        maxlength="255"
        placeholder="rue de l'Église&hellip; (optionnel)"
        value="<?= $current?->nom_voie ?>">
    </label></p>
    <p><label>Numéro voie&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_numero_voie"
        name="<?= $this->name ?>[numero_voie]"
        type="number"
        min="1"
        placeholder="1,2&hellip; (optionnel)"
        value="<?= $current?->numero_voie ?>">
    </label></p>
    <p><label>Complément numéro&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_complement_numero"
        name="<?= $this->name ?>[complement_numero]"
        type="text"
        maxlength="10"
        placeholder="bis, ter&hellip; (optionnel)"
        value="<?= $current?->complement_numero ?>">
    </label></p>
    <p><label>Précision interne&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_precision_int"
        name="<?= $this->name ?>[precision_int]"
        type="text" maxlength="255"
        placeholder="apt., boîte à lettre, étage (optionnel)&hellip;"
        value="<?= $current?->precision_int ?>">
    </label></p>
    <p><label>Précision externe&nbsp;: <input <?= $form_attr ?>
        id="<?= $this->id ?>_precision_ext"
        name="<?= $this->name ?>[precision_ext]"
        type="text"
        maxlength="255"
        placeholder="bâtiment, voie, résidence (optionnel)&hellip;"
        value="<?= $current?->precision_ext ?>">
    </label></p>
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
        $communes = DB\connect()->query('select nom from _commune limit 100')->fetchAll();
?>
<datalist id="datalist-input-address-communes">
<?php foreach ($communes as $c) { ?>
    <option><?= $c['nom'] ?></option>
<?php } ?>
</datalist>
<?php
    }
}
