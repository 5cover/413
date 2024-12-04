<?php
require_once 'util.php';
require_once 'redirect.php';
require_once 'model/Offre.php';
require_once 'component/ImageView.php';

final class CarteOffre
{
    readonly ImageView $image_principale;

    function __construct(
        readonly Offre $offre,
    ) {
        $this->image_principale = new ImageView($offre->image_principale);
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
    <h3><a href="<?= location_detail_offre($this->offre->id) ?>"><?= $this->offre->titre ?></a></h3>
    <p class="location"><?= $this->offre->adresse->format() ?></p>
    <p><?= $this->offre->resume ?></p>
    <p class="category"><?= $this->offre::CATEGORIE ?></p>
</div>
<?php
    }
}
