<?php
require_once 'util.php';
require_once 'component/InputAdresse.php';
require_once 'component/InputDuree.php';
require_once 'component/InputImage.php';
require_once 'component/ImageView.php';
require_once 'component/Page.php';

$page = new Page('Storybook');

?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
<?php $page->put_header() ?>
<main>

<h1>Storybook</h1>

<h2>Adresse</h2>

<?php
{
    $input_adresse = new InputAdresse('adresse', 'adresse');
    $adresse = $input_adresse->get($_GET, required: false);
?>

<h3>Entrée</h3>

<form method="get">
    <?php $input_adresse->put($adresse) ?>
    <button type="submit">Envoyer</button>
</form>

<?php if ($adresse) { ?>
<h3>Sortie</h3>
<p><?= $adresse->format() ?></p>
<pre><samp>
    <?php var_dump($adresse) ?>
</samp></pre>
<?php
    }
}
?>

<h2>Durée</h2>

<?php
{
    $input_duree = new InputDuree('duree', 'duree');
    $duree = $input_duree->get($_GET, required: false);
?>
<h3>Entrée</h3>

<form method="get">
    <?php $input_duree->put($duree) ?>
    <button type="submit">Envoyer</button>
</form>

<?php if ($duree) { ?>
<h3>Sortie</h3>
<p><?= $duree->format() ?></p>
<pre><samp>
    <?php var_dump($duree) ?>
</samp></pre>
<?php }
} ?>

<h2>Image</h2>

<?php
{
    $input_image = new InputImage("Image d'exemple", 'image', 'image');
    $image = $input_image->get($_GET, required: false);
?>
<h3>Entrée</h3>

<form method="post" enctype="multipart/form-data">
    <?php $input_image->put($image) ?>
    <button type="submit">Envoyer</button>
</form>

<?php if ($image) { ?>
<h3>Sortie</h3>
<?php (new ImageView($image))->put_figure() ?>
<pre><samp>
    <?php var_dump($image) ?>
</samp></pre>
<?php }
} ?>


</main>
<?php $page->put_footer() ?>
</body>
</html>
