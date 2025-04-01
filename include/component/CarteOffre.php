<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'Kcrf/OffreFast.php';
require_once 'Kcrf/ImageFast.php';
require_once 'Kcrf/AdresseFast.php';
require_once 'component/ImageView.php';

final class CarteOffre
{
    readonly ImageView $image_principale;

    function __construct(
        readonly OffreFast $offre,
    ) {
        $this->image_principale = new ImageView(ImageFast::from_db($offre->data->id_image_principale));
    }

    /**
     * Affiche le composant de carte d'offre pour membre ou visiteur.
     * @return void
     */
    function put(): void
    {
?>
<div class="offer-card">
    <?php $this->image_principale->put_img() ?>
    <h3><a class="titre" href="<?= h14s(location_detail_offre($this->offre->id)) ?>"><?= h14s($this->offre->data->titre) ?></a></h3>
    <p class="location"><?= h14s(AdresseFast::from_db($this->offre->data->id_adresse)->data->format()) ?></p>
    <p><?= h14s($this->offre->data->resume) ?></p>
    <p class="category"><?= h14s(ucfirst($this->offre->data->categorie->value)) ?></p>
    <?php if ($this->offre->data->prix_min) { ?>
    <p>À partir de &nbsp;: <?= $this->offre->data->prix_min ?>&nbsp;€</p>
    <?php } ?>
    <p>Note&nbsp;: <?= $this->offre->note_moyenne ?>&nbsp;/&nbsp;5</p>
    <p>Créée le&nbsp;: <?= $this->offre->creee_le->format_date() ?></p>
</div>
<?php
    }

    static function put_template(): void
    {
?>
<div class="offer-card">
    <?php ImageView::put_template('offer-image-principale') ?>
    <h3><a class="titre" href=""></a></h3>
    <p class="location"></p>
    <p class="offer-resume"></p>
    <p class="category"></p>
    <p>À partir de &nbsp;: <span class="offer-prix-min"></span>&nbsp;€</p>
    <p>Note&nbsp; <span class="offer-note"></span>&nbsp;/&nbsp;5</p>
    <p>Crée le&nbsp; <span class="offer-creee-le"></span></p>
</div>
<?php
    }
}
