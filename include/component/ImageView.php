<?php

require_once 'const.php';
require_once 'util.php';
require_once 'Kcrf/ImageFast.php';
require_once 'util.php';

final class ImageView
{
    function __construct(
        readonly ImageFast $image,
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

    static function put_template(?string $class = null): void
    {
?>
<img <?= mapnull($class, fn(string $c) => "class=\"$c\"") ?> src=""
    alt=""
    title="">
<?php
    }
}
