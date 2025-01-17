<?php

require_once 'const.php';
require_once 'util.php';
require_once 'model/Image.php';

final class ImageView
{
    function __construct(
        readonly Image $image,
    ) {}

    /**
     * Affiche le composant d'image utilisateur dans une figure.
     * @return void
     */
    function put_figure(): void
    {
?>
<figure>
    <?php $this->put_img() ?>
    <?php if ($this->image->legende) { ?>
        <figcaption><?= h14s($this->image->legende) ?></figcaption>
    <?php } ?>
</figure>
<?php
    }

    /**
     * Affiche le composant d'image utilisateur dans une simple img.
     * @return void
     */
    function put_img(): void
    {
?>
<img src="<?= h14s($this->image->src()) ?>"
    alt="<?= h14s($this->image->legende ?: 'image') ?>"
    title="<?= h14s($this->image->legende) ?>">
<?php
    }

    static function put_template(): void {
?>
<img src="[MISSING]"
    alt="[MISSING]"
    title="[MISSING]">
<?php
    }
}
