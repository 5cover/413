<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'component/ImageView.php';
require_once 'model/Image.php';

/**
 * @extends Input<Image[]>
 */
final class InputImage extends Input
{
    function __construct(
        readonly string $fieldset_legend,
        string $id              = '',
        string $name            = '',
        string $form_id         = '',
        readonly bool $multiple = false,
    ) {
        parent::__construct($id, $name, $form_id);
    }

    /**
     * Récupère l'image saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param ?Image[] $current_id_images L'ID des images à modifier ou `null` pour une création.
     * @return Image[]
     */
    function get(array $get_or_post, ?int $current_id_images = null): array
    {
        $files = getarg($_FILES, $this->name, required: false);

        $files = $this->multiple ? soa_to_aos($files) : [$files];

        $files = array_filter($files, fn($f) => $f['error'] === UPLOAD_ERR_OK);


        return array_map(fn($file, $current_id_image) => new Image(
            $current_id_image,
            getarg($file, 'size', arg_int()),
            explode('/', $file['type'], 2)[1],
            getarg($get_or_post, "{$this->name}_legende", required: false),
            $file['tmp_name'],
        ), $files, $current_id_images ?? []);
    }

    /**
     * @inheritDoc
     */
    function put(mixed $current = null, bool $required = true): void
    {
        $current ??= [];
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
        ?>
<fieldset id="<?= $this->id ?>" class="input-image">
    <legend><?= $this->fieldset_legend ?></legend>
    <p>
        <input class="pourquoi"<?= $form_attr ?>
            name="<?= $this->name . ($this->multiple ? '[]' : '') ?>"
            type="file"
            accept="image/*"
            <?= $required ? 'required' : '' ?>
            <?= $this->multiple ? 'multiple' : '' ?>>
    </p>
    <p>
        <input <?= $form_attr ?>
            id="<?= $this->id ?>_legende"
            name="<?= $this->name ?>_legende"
            type="text"
            placeholder="Légende"
            value="<?= ($current[0] ?? null)?->legende ?>">
    </p>
    <div id="<?= $this->id ?>-preview">
        <?php foreach ($current as $image) {
            (new ImageView($image))->put_img();
        } ?>
    </div>
</fieldset>
<?php
    }
}
