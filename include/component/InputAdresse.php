<?php

use Vtiful\Kernel\Format;
require_once 'db.php';
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Adresse.php';


function geocode($address) {
    // Encoder l'adresse pour l'URL
    $address = urlencode($address);
    // URL de l'API Nominatim
    $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";

    // Envoyer la requête et récupérer la réponse
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Vérifier si des résultats ont été trouvés
    if (!empty($data)) {
        $latitude = $data[0]['lat'];
        $longitude = $data[0]['lon'];
        return array('latitude' => $latitude, 'longitude' => $longitude);
    } else {
        return null;
    }
}


/**
 * @extends Input<Adresse>
 */
final class InputAdresse extends Input
{
    /**
     * Récupère l'adresse saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param ?int $current_id_adresse L'ID de l'adresse à modifier ou `null` pour une création.
     */
    function get(array $get_or_post, ?int $current_id_adresse = null): Adresse
    {
        $data = getarg($get_or_post, $this->name);
        $addr = new Adresse(
            $current_id_adresse,
            notfalse(Commune::from_db_by_nom($data['commune'])),
            getarg($data, 'numero_voie', arg_int(1), required: false),
            $data['complement_numero'] ?: null,
            $data['nom_voie'] ?: null,
            $data['localite'] ?: null,
            $data['precision_int'] ?: null,
            $data['precision_ext'] ?: null,
            $data['lat'] ?? null ?: null,
            $data['long'] ?? null ?: null,
        );

        $latLong = geocode($addr->format());
        $addr->__set('lat')  = $latLong[0];
        $addr->__set('long')  = $latLong[1];


        return $addr;
    }

    function for_id(): string
    {
        return $this->id('commune');
    }

    /**
     * Affiche l'HTML du composant.
     * @param ?Adresse $current L'adresse à modifier ou `null` pour une création.
     */
    function put(mixed $current = null): void
    {
        self::put_datalist();
?>
<details <?= $this->id ? 'id="' . h14s($this->id) . '"' : '' ?> class="input-address">
    <summary>
        <input <?= $this->form_attr ?> type="text" readonly>
    </summary>
    <p><label>Commune&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('commune') ?>"
        name="<?= $this->name('commune') ?>"
        type="text"
        list="datalist-input-address-communes"
        autocomplete="on"
        required
        value="<?= h14s($current?->commune->nom) ?>">
    </label></p>
    <p><label>Localité&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('localite') ?>"
        name="<?= $this->name('localite') ?>"
        type="text"
        maxlength="255"
        placeholder="hameau, lieu-dit&hellip; (optionnel)"
        value="<?= h14s($current?->localite) ?>">
    </label></p>
    <p><label>Nom voie&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('nom_voie') ?>"
        name="<?= $this->name('nom_voie') ?>"
        type="text"
        maxlength="255"
        placeholder="rue de l'Église&hellip; (optionnel)"
        value="<?= h14s($current?->nom_voie) ?>">
    </label></p>
    <p><label>Numéro voie&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('numero_voie') ?>"
        name="<?= $this->name('numero_voie') ?>"
        type="number"
        min="1"
        placeholder="1,2&hellip; (optionnel)"
        value="<?= h14s($current?->numero_voie) ?>">
    </label></p>
    <p><label>Complément numéro&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('complement_numero') ?>"
        name="<?= $this->name('complement_numero') ?>"
        type="text"
        maxlength="10"
        placeholder="bis, ter&hellip; (optionnel)"
        value="<?= h14s($current?->complement_numero) ?>">
    </label></p>
    <p><label>Précision interne&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('precision_int') ?>"
        name="<?= $this->name('precision_int') ?>"
        type="text" maxlength="255"
        placeholder="apt., boîte à lettre, étage (optionnel)&hellip;"
        value="<?= h14s($current?->precision_int) ?>">
    </label></p>
    <p><label>Précision externe&nbsp;: <input <?= $this->form_attr ?>
        id="<?= $this->id('precision_ext') ?>"
        name="<?= $this->name('precision_ext') ?>"
        type="text"
        maxlength="255"
        placeholder="bâtiment, voie, résidence (optionnel)&hellip;"
        value="<?= h14s($current?->precision_ext) ?>">
    </label></p>
    <input type="hidden" name="lat" value="<?= h14s($current?->lat) ?>">
    <input type="hidden" name="long" value="<?= h14s($current?->long) ?>">
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
    <option><?= h14s($c['nom']) ?></option>
<?php } ?>
</datalist>
<?php
    }
}
