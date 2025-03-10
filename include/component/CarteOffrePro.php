<?php
require_once 'component/ImageView.php';
require_once 'const.php';
require_once 'model/Offre.php';
require_once 'redirect.php';
require_once 'util.php';

final class CarteOffrePro
{
    readonly ImageView $image_principale;

    function __construct(
        readonly Offre $offre,
    ) {
        $this->image_principale = new ImageView($offre->image_principale);
    }

    /**
     * Affiche le composant de carte d'offfre pour professionnel
     * @return void
     */
    function put(): void
    {
?>
<div class="offer-card">
    <?php $this->image_principale->put_img() ?>
    <h3><a href="<?= h14s(location_detail_offre_pro($this->offre->id)) ?>"><?= h14s($this->offre->titre) ?></a></h3>
    <p class="location"><?= h14s($this->offre->adresse->format()) ?></p>
    <p class="category"><?= h14s(ucfirst($this->offre->categorie)) ?></p>
    <p class="rating"><?= mapnull(
        $this->offre->note_moyenne,
        fn(float $note) => 'Note'.NBSP.": $note/5 ★ ({$this->offre->nb_avis} avis)"
    ) ?? 'Aucun avis' ?></p>
</div>
<?php
    }
}
