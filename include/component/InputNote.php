<?php
require_once 'util.php';
require_once 'component/Input.php';

/**
 * @extends Input<int>
 */
final class InputNote extends Input
{
    public function get(array $get_or_post, bool $required = true): int {
        return getarg($get_or_post, $this->name, arg_int(1, 5), $required);
    }

    /**
     * @inheritDoc
     */
    public function put(mixed $current = null): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
?>
<select <?= $form_attr ?>
    id="<?= $this->id ?>"
    name="<?= $this->name ?>"
    required>
    <option value="5" <?= $current === 5 ? 'checked' : '' ?>>5 étoiles</option>
    <option value="4" <?= $current === 4 ? 'checked' : '' ?>>4 étoiles</option>
    <option value="3" <?= $current === 3 ? 'checked' : '' ?>>3 étoiles</option>
    <option value="2" <?= $current === 2 ? 'checked' : '' ?>>2 étoiles</option>
    <option value="1" <?= $current === 1 ? 'checked' : '' ?>>1 étoile</option>
</select>
<?php
    }
}
