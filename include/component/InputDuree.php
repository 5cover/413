<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Duree.php';

/**
 * Composant d'input de durée (jours, heures, minutes).
 * Demander à Raphaël si besoin de d'autres unités.
 * @extends Input<Duree>
 */
final class InputDuree extends Input
{
    /**
     * Récupère l'adresse saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param bool $required Si la durée est requise. Quand la durée est manquante, si `false` a été passé, la fonction retourne `null`. Sinon, déclenche une erreur.
     */
    function get(array $get_or_post, bool $required = true): ?Duree
    {
        $data = getarg($get_or_post, $this->name, required: $required);
        return $data === null ? null : new Duree(
            days: parse_int($data['jours']),
            hours: parse_int($data['heures']),
            minutes: parse_int($data['minutes']),
        );
    }

    function for_id(): string {
        return $this->id('jours');
    }

    /**
     * Affiche l'HTML du composant.
     * @param ?Duree $current La duréee à modifier ou `null` pour une création.
     */
    function put(mixed $current = null): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
?>
<p <?= $this->id ? "id=\"$this->id\"" : '' ?> class="input-duration">
    <label><input <?= $form_attr ?>
            id="<?= $this->id('jours') ?>"
            name="<?= $this->name('jours') ?>"
            type="number"
            min="0"
            required
            value="<?= $current?->days ?? 0 ?>"> jour(s)</label>
    <label><input <?= $form_attr ?>
        id="<?= $this->id('heures') ?>"
        name="<?= $this->name('heures') ?>"
        type="number"
        min="0"
        max="24"
        required
        value="<?= $current?->hours ?? 0 ?>"> heure(s)</label>
    <label><input <?= $form_attr ?>
        id="<?= $this->id('minutes') ?>"
        name="<?= $this->name('minutes') ?>"
        type="number"
        min="0"
        max="60"
        required
        value="<?= $current?->minutes ?? 0 ?>" > minute(s)</label>
</p>
<?php
    }
}
