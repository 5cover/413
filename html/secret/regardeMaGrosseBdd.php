<?php
require_once 'DB/db.php';

require_once 'component/Page.php';

$page = new Page('regarde dans la bdd');

$page->put(function () {
    if ($table = $_GET['table'] ?? null) {
        $results = DB\connect()->query('table '
            . DB\quote_identifier($_GET['schema'] ?? 'pact')
            . '.' . DB\quote_identifier($table))->fetchAll();
        // Vérifier s'il y a des résultats
        if ($results) {
            echo "<table border='1'>";
            echo '<tr>';

            // Afficher les en-têtes du tableau en fonction des colonnes récupérées
            foreach (array_keys($results[0]) as $column) {
                echo '<th>' . h14s($column ?? '') . '</th>';
            }
            echo '</tr>';

            // Afficher chaque ligne
            foreach ($results as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . h14s($value ?? '') . '</td>';  // Sécuriser l'affichage des données
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>Aucune donnée trouvée dans la table $table.</p>";
        }
        exit;
    }
    ?>
    <h1>regarde dans la bdd</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <form action="regardeMaGrosseBdd.php" method="get" enctype="multipart/form-data">

                <br>
                <div class="champ">
                    <p>donne le nom de la table *</p>
                    <input type="text" placeholder="" id="table" name="table" required>
                    <p>et un schema</p>
                    <input type="text" value="pact" id="schema" name="schema">
                </div>

                <button type="submit" class="btn-connexion">regarde</button>
            </form>
            <br><br>
            </a>
            <br>
        </div>
    </section>
    <?php
});