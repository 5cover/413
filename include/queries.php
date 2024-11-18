<?php

require_once 'db.php';

// Selections

function query_communes(?string $nom = null): array
{
    $args = filter_null_args(['nom' => [$nom, PDO::PARAM_STR]]);
    $stmt = notfalse(db_connect()->prepare('select * from _commune' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    return notfalse($stmt->fetchAll());
}

function query_avis_count(int $id_offre): int
{
    $stmt = notfalse(db_connect()->prepare('select count(*) from _avis where id_offre = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_offre(int $id): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from offres where id = ?'));
    bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_image(int $id_image): array
{
    $stmt = notfalse(db_connect()->prepare('select * from _image where id = ?'));
    bind_values($stmt, [1 => [$id_image, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetch());
}

/**
 * Récupère les images de la gallerie d'une offre
 * @return array<int> Un tableau d'id d'images. Utilise query_image pour retrouver les infos sur l'image.
 */
function query_gallerie(int $id_offre): array
{
    $stmt = notfalse(db_connect()->prepare('select id_image from _gallerie where id_offre = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute()); 
    return array_map(fn($row) => $row['id_image'], $stmt->fetchAll());
}

function query_adresse(int $id_adresse): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from _adresse where id = ?'));
    bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_commune(int $code, string $numero_departement): array
{
    $stmt = notfalse(db_connect()->prepare('select * from _commune where code = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetch());
}

function query_codes_postaux(int $code_commune, string $numero_departement): array
{
    $stmt = notfalse(db_connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code_commune, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return array_map(fn($row) => $row['code_postal'], $stmt->fetchAll());
}

function query_membre(string $email_or_pseudo): array|false
{
    // We know at symbols are not allowed in pseudonyms so if there is one, the user meant to connact with their email.
    $stmt = notfalse(db_connect()->prepare('select * from membre where ' . (str_contains($email_or_pseudo, '@') ? 'email' : 'pseudo') . ' = ? limit 1'));
    notfalse($stmt->execute([$email_or_pseudo]));
    return $stmt->fetch();
}

function query_professionnel(string $email): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from professionnel where email = ?'));
    notfalse($stmt->execute([$email]));
    return $stmt->fetch();
}

function query_compte_membre(int $id_membre): PDOStatement
{
    $stmt = notfalse(db_connect()->prepare('select * from _membre where id = ?'));
    bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt;
}

// Parameterized selections

function query_offres_count(?int $id_professionnel = null, ?bool $en_ligne = null): int
{
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(db_connect()->prepare('select count(*) from offres' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_offres(?int $id_professionnel = null, ?bool $en_ligne = null): PDOStatement
{
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(db_connect()->prepare('select * from offres' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return $stmt;
}

// Insertions

/**
 * Toggles the state (en ligne/hors ligne) of an offer by adding a row in the _changement_etat table.
 *
 * @param int $id_offre The ID of the offer to toggle the state for.
 */
function alterner_etat_offre(int $id_offre): void
{
    $stmt = notfalse(db_connect()->prepare('insert into _changement_etat (id_offre) values (?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

/**
 * Inserts an image into the database and returns the filename and ID of the inserted image.
 *
 * @param array $img An associative array containing the image data, with keys 'size' and 'type'.
 * @return array An array containing the filename and ID of the inserted image.
 */
function insert_image(array $img): array
{
    $stmt = notfalse(db_connect()->prepare('insert into _image (legende, taille, mime_type) values (?,?,?) returning id'));
    notfalse($stmt->execute(['', $img['size'], $img['type']]));  // todo: legende
    $id_image = notfalse($stmt->fetchColumn());

    $filename = __DIR__ . "/../images_utilisateur/$id_image";
    notfalse(move_uploaded_file($img['tmp_name'], $filename));
    echo "Moved file to $filename";
    return [$filename, $id_image];
}

// Parameterized insertions

/**
 * Inserts a new address into the database and returns the ID of the inserted address.
 *
 * @param int $code_commune The code of the commune for the address.
 * @param int $numero_departement The number of the department for the address.
 * @param int|null $numero_voie The number of the street for the address.
 * @param string|null $complement_numero Additional information about the street number.
 * @param string|null $nom_voie The name of the street for the address.
 * @param string|null $localite The locality (e.g. city, town) for the address.
 * @param string|null $precision_int Additional internal precision information for the address.
 * @param string|null $precision_ext Additional external precision information for the address.
 * @param float|null $latitude The latitude coordinate of the address.
 * @param float|null $longitude The longitude coordinate of the address.
 * @return int The ID of the inserted address.
 */
function insert_adresse(
    int $code_commune,
    int $numero_departement,
    ?int $numero_voie = null,
    ?string $complement_numero = null,
    ?string $nom_voie = null,
    ?string $localite = null,
    ?string $precision_int = null,
    ?string $precision_ext = null,
    ?float $latitude = null,
    ?float $longitude = null,
): int {
    $args = filter_null_args([
        'code_commune' => [$code_commune, PDO::PARAM_INT],
        'numero_departement' => [$numero_departement, PDO::PARAM_INT],
        'numero_voie' => [$numero_voie, PDO::PARAM_INT],
        'complement_numero' => [$complement_numero, PDO::PARAM_INT],
        'nom_voie' => [$nom_voie, PDO::PARAM_STR],
        'localite' => [$localite, PDO::PARAM_STR],
        'precision_int' => [$precision_int, PDO::PARAM_STR],
        'precision_ext' => [$precision_ext, PDO::PARAM_STR],
        'latitude' => [$latitude, PDO::PARAM_STR],
        'longitude' => [$longitude, PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('_adresse', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function offre_args(
    int $id_adresse,
    int $id_image_principale,
    int $id_professionnel,
    string $libelle_abonnement,
    string $titre,
    string $resume,
    string $description_detaillee,
    ?string $url_site_web = null,
): array {
    return filter_null_args([
        'id_adresse' => [$id_adresse, PDO::PARAM_INT],
        'id_image_principale' => [$id_image_principale, PDO::PARAM_INT],
        'id_professionnel' => [$id_professionnel, PDO::PARAM_INT],
        'libelle_abonnement' => [$libelle_abonnement, PDO::PARAM_STR],
        'titre' => [$titre, PDO::PARAM_STR],
        'resume' => [$resume, PDO::PARAM_STR],
        'description_detaillee' => [$description_detaillee, PDO::PARAM_STR],
        'url_site_web' => [$url_site_web, PDO::PARAM_STR],
    ]);
}

function insert_activite(
    array $offre_args,
    DateInterval $indication_duree,
    string $prestation_incluses,
    ?int $age_requis = null,
    ?string $prestations_non_incluses = null,
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree->format(DateTime::ATOM), PDO::PARAM_STR],
        'prestation_incluses' => [$prestation_incluses, PDO::PARAM_STR],
        'age_requis' => [$age_requis, PDO::PARAM_INT],
        'prestations_non_incluses' => [$prestations_non_incluses, PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('activite', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_spectacle(
    array $offre_args,
    DateInterval $indication_duree,
    int $capacite_accueil,
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree->format(DateTime::ATOM), PDO::PARAM_STR],
        'capacite_accueil' => [$capacite_accueil, PDO::PARAM_INT],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('spectacle', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_visite(
    array $offre_args,
    DateInterval $indication_duree
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree->format(DateTime::ATOM), PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('visite', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_restaurant(
    array $offre_args,
    string $carte,
    int $richesse,
    ?bool $sert_petit_dejeuner = null,
    ?bool $sert_brunch = null,
    ?bool $sert_dejeuner = null,
    ?bool $sert_diner = null,
    ?bool $sert_boissons = null,
): int {
    $args = $offre_args + filter_null_args([
        'carte' => [$carte, PDO::PARAM_STR],
        'richesse' => [$richesse, PDO::PARAM_INT],
        'sert_petit_dejeuner' => [$sert_petit_dejeuner, PDO::PARAM_BOOL],
        'sert_brunch' => [$sert_brunch, PDO::PARAM_BOOL],
        'sert_dejeuner' => [$sert_dejeuner, PDO::PARAM_BOOL],
        'sert_diner' => [$sert_diner, PDO::PARAM_BOOL],
        'sert_boissons' => [$sert_boissons, PDO::PARAM_BOOL],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('restaurant', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_parc_attractions(
    array $offre_args,
    int $id_image_plan,
): int {
    $args = $offre_args + filter_null_args([
        'id_image_plan' => [$id_image_plan, PDO::PARAM_INT],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('parc_attractions', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

// Utils

/**
 * Binds types values to a statement.
 *
 * @param PDOStatement $stmt The statement on which to bind values.
 * @param array<int|string,array{mixed, int}> $params An associative array mapping from the parameter name to a tuple of the parameter value and the PDO type (e.g. a PDO::PARAM_* constant value)
 */
function bind_values(PDOStatement $stmt, array $params)
{
    foreach ($params as $name => [$value, $type]) {
        notfalse($stmt->bindValue($name, $value, $type));
    }
}

function filter_null_args(array $array): array
{
    return array_filter($array, fn($e) => $e[0] !== null);
}

/**
 * Generates a WHERE clause for a SQL query based on an array of key-value pairs.
 *
 * This function is an internal implementation detail and should not be called directly outside of this module, as it could pose a security risk.
 *
 * @param string $operator The logical operator to use between clauses (e.g. 'and', 'or').
 * @param array $clauses An array containing the conditions for the WHERE clause.
 * @return string The generated WHERE clause, or an empty string if no clauses are provided.
 */
function _where_clause(string $operator, array $clauses): string
{
    return $clauses
        ? ' where ' . implode(" $operator ", array_map(fn($attr) => "$attr = :$attr", $clauses))
        : '';
}

function _insert_into_returning_id(string $table, array $args): string
{
    assert(!empty($args));
    return "insert into \"$table\" (" . implode(',', array_keys($args)) . ') values (?' . str_repeat(',?', count($args) - 1) . ') returning id';
}
