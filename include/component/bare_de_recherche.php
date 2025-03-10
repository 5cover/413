<?php
$search          = $_GET['search'];
$recherche       = $_GET['valider'];
$modif_affichage = false;

if (isset($valider) && !empty(trim($search))) {
    $tab             = Offre::from_db_by_search($search);
    $modif_affichage = true;
}

?>










<form action="" name="barre_de_recherche" method="get">

<input type="text" name="search" value="<?php echo $search ?>" placeholder=">Mots-clés">
<input type="submit" name="valider" value="Recherche">
</form>



<?php
if ($recherche) {
    ?> 
    <div id="recherche">
        <div id="offre_recherhce">
            <ol>
                <?php
                for ($i = 0; $i < count($tab); $i++) {
                    ?>
                    <li><?php echo $tab[$i]['titre'] ?></li>
                    <?php
                }
                ?>

            </ol>
        </div>
    </div>
    <?php
}
?>