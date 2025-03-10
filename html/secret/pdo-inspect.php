<?php
require_once 'db.php';

$stmt = notfalse(DB\connect()->query(<<<SQL
select
    _adresse.*,
    _image.*,
    professionnel.*,
    _abonnement.*,
    offres.*,
    _activite.*,
    _parc_attractions.*,
    _restaurant.*,
    _spectacle.*,
    _visite.*
from
    offres
    join _adresse using (id_adresse)
    join _image using (id_image_principale)
    join professionnel on using (id_professionnel)
    join _abonnement on using (libelle_abonnement)
    left join _activite using (id)
    left join _parc_attractions using (id)
    left join _restaurant using (id)
    left join _spectacle using (id)
    left join _visite using (id)
where id = 11;
SQL));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <h1><code><?= $stmt->queryString ?></code></h1>
    <?php dbg_print($stmt->fetchAll()) ?>
</head>
<body>
    
</body>
</html>