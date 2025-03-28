<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn] function http_exit(int $code): never {
    http_response_code($code);
    exit;
}

/**
 * Apply a function, mutating the argument. Think of it like a generalized assignment operator (e.g. "+=") that can work on any function. Used to avoid specifying the argument twice.
 * @template T
 * @template TResult
 * @param T $arg
 * @param-out TResult $arg
 * @param callable(T): TResult $fn
 * @return T
 */
function apply(mixed &$arg, callable $fn): mixed
{
    return $arg = $fn($arg);
}

/**
 * HTML5 `htmlspecialchars` (name shortened using numeronym)
 * This function propagates a `null` argument.
 * @param ?string $s String to encode.
 * @return ?string Encoded string, or null if $s was null.
 */
function h14s(?string $s): ?string
{
    return $s === null ? null : htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
}

/**
 * Cause une erreur si la valeur fournie est strictement égale à `false`.
 *
 * @template T
 * @param T|false $valeur La valeur à comparer à `false` avec l'opérateur `===`
 * @param string $message Le message d'erreur à afficher si $valeur était `false`
 * @return T $valeur si elle n'était pas strictement égale à `false`.
 * @throws DomainException Si $valeur est `false`.
 */
function notfalse(mixed $valeur, string $message = 'was false'): mixed
{
    if ($valeur === false) {
        throw new DomainException($message);
    }
    return $valeur;
}

/**
 * Map the non-null value or return null.
 * @template T
 * @template TResult
 * @param ?T $value
 * @param callable(T): TResult $map
 * @return ?TResult
 */
function mapnull(mixed $value, callable $map): mixed
{
    return $value === null ? null : $map($value);
}

/**
 * Cause une erreur si la valeur fournie est strictement égale à `null`.
 *
 * @template T
 * @param ?T $valeur La valeur à comparer à `null` avec l'opérateur `===`
 * @param string $message Le message d'erreur à afficher si $valeur était `null`
 * @return T $valeur si elle n'était pas strictement égale à `null`.
 * @throws DomainException Si $valeur est `null`.
 */
function notnull(mixed $valeur, string $message = 'was null'): mixed
{
    if ($valeur === null) {
        throw new DomainException($message);
    }
    return $valeur;
}

/**
 * Supprime une clé d'un tableu et retourne la valeur associée.
 * @template T
 * @param T[] $array Le tableau à modifier.
 * @param bool|float|int|string|null $key La clé à retirer. Elle doit exister dans le tableau.
 * @throws DomainException Si la clé n'existe pas dans le tableau.
 * @return T La valeur associée à la clé retirée.
 */
function array_pop_key(array &$array, bool|float|int|string|null $key): mixed
{
    if (!array_key_exists($key, $array)) {
        throw new DomainException("Array must contain key '$key'");
    }
    $value = $array[$key];
    unset($array[$key]);
    return $value;
}

/**
 * @template T
 * @template TResult
 * @param iterable<T> $array
 * @param callable(T): (TResult|false) $map
 * @return TResult|false
 */
function iterator_first_notfalse(iterable $array, callable $map): mixed
{
    foreach ($array as $item) {
        $r = $map($item);
        if ($r !== false) {
            return $r;
        }
    }
    return false;
}

/**
 * Détermine si tous les éléments d'un tableau satisfont un prédicat.
 * @template T
 * @param T[] $arr Le tableau à tester.
 * @param callable(T): bool $predicate La fonction prédicat à appeler avec chaque élément de $arr.
 * @return bool `true` si $predicate n'a retourné une valeur *falsey* pour aucun élément de $arr, false sinon.
 */
function array_every(array $arr, callable $predicate): bool
{
    foreach ($arr as $e) {
        if (!$predicate($e)) {
            return false;
        }
    }
    return true;
}

/**
 * Détermine si un élément d'un tableau satisfait un prédicat.
 * @template T
 * @param T[] $arr Le tableau à tester.
 * @param callable(T): bool $predicate La fonction prédicat à appeler avec chaque élément de $arr.
 * @return bool `true` si $predicate a retourné une valeur *truthy* pour un élément de $arr, false sinon.
 */
function array_some(array $arr, callable $predicate): bool
{
    foreach ($arr as $e) {
        if ($predicate($e)) {
            return true;
        }
    }
    return false;
}

/**
 * Valide et récupère un argument d'un tableau source.
 * @template T
 * @param T[] $source Le tableau source d'où récupérer l'argument (tel que `$_GET`, `$_POST` ou `$_FILES$`).
 * @param string $nom Le nom de l'argument (clé dans le tableau) à récupérer.
 * @param ?callable(string, T): mixed $filter Un filtre optionnel à appliquer à la valeur, tel que ceux donnés par `arg_check` ou `arg_filter`.
 * @param bool $required Si cet argument est obligatoire. Si `true` et l'argument n'est pas présent, une erreur HTML est jetée.
 * @return ?T L'argument récupéré et potentiellement transformé (si $filter est non `null`). Si l'argument n'est pas requis ($required est `false`) et manquant (il n'y a pas de clé $nom dans $source), `null` est retourné.
 */
function getarg(array $source, string $nom, ?callable $filter = null, bool $required = true): mixed
{
    if (!array_key_exists($nom, $source) || $source[$nom] === '') {
        if ($required) {
            dbg_print($source);
            html_error("argument manquant: $nom");
        } else {
            return null;
        }
    }
    return $filter === null ? $source[$nom] : $filter($nom, $source[$nom]);
}

/**
 * Crée un filtre pour `getarg` qui valide l'argument via prédicat.
 * @param callable(mixed): bool $check Une fonction prédicat.
 * @return callable(string, mixed): mixed Un filtre utilisable par la fonction `getarg`.
 */
function arg_check(callable $check): callable
{
    return function (string $name, mixed $value) use ($check) {
        if (!$check($value)) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $value;
    };
}

/**
 * Crée un filtre `getarg` pour un entier.
 * @param ?int $min_range La valeur minimale de l'entier ou `null` pour pas de minimum.
 * @param ?int $max_range La valeur maximale de l'entier ou `null` pour pas de maximum.
 * @return callable(string, mixed): ?int Un filter utilisable par la fonction `getarg`.
 * @throws DomainException En cas de mauvaise syntaxe.
 */
function arg_int(?int $min_range = null, ?int $max_range = null): callable
{
    return function (string $name, mixed $value) use ($min_range, $max_range) {
        if (false === ($val = parse_int($value, $min_range, $max_range))) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $val;
    };
}

/**
 * Crée un filtre `getarg` pour un flottant.
 * @param ?float $min_range La valeur minimale du floattant ou `null` pour pas de minimum.
 * @return callable(string, mixed): ?float Un filter utilisable par la fonction `getarg`.
 */
function arg_float(?float $min_range = null): callable
{
    return function (string $name, mixed $value) use ($min_range) {
        if (false === ($val = parse_float($value, $min_range))) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $val;
    };
}

/**
 * Parse un entier.
 * @param ?string $output Un entier sous forme de chaîne.
 * @param ?int $min_range La valeur minimale de l'entier ou `null` pour pas de minimum.
 * @param ?int $max_range La valeur maximale de l'entier ou `null` pour pas de maximum.
 * @return int|null|false L'entier parsé ou `false` en cas de mauvaise syntaxe, ou `null` si `$value` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`).
 */
function parse_int(?string $output, ?int $min_range = null, ?int $max_range = null): int|null|false
{
    // remove trailing zeros before filtering
    return $output === null ? null : filter_var(
        notnull(preg_replace('/^\s*0+(?=\d)/', '', $output)),
        FILTER_VALIDATE_INT,
        array_filter(['min_range' => $min_range, 'max_range' => $max_range], fn($x) => $x !== null),
    );
}

/**
 * Parse un flottant.
 * @param ?string $output Un flottant sous forme de chaîne.
 * @param ?float $min_range La valeur minimale du flottant ou `null` pour pas de minimum.
 * @return float|null|false Le flottant parsé ou `false` en cas de mauvaise syntaxe, ou `null` is `$value` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`).
 */
function parse_float(?string $output, ?float $min_range = null): float|null|false
{
    return $output === null ? null : filter_var($output, FILTER_VALIDATE_FLOAT,
        $min_range === null ? 0 : ['min_range' => $min_range]);
}

/**
 * Crée un filtre pour `getarg` qui applique un filtre PHP à l'argument agec la fonction `filter_var`.
 * @param int $filter Le filtre à appliquer à l'argument.
 * @param array|int $options Les options du filtre. Voir la documentation PHP pour les valeurs possibles.
 * @return callable(string, mixed): mixed Un filtre utilisable par la fonction `getarg`.
 */
function arg_filter(int $filter, array|int $options = 0, bool $error_on_false = true): callable
{
    return function (string $name, mixed $value) use ($filter, $options, $error_on_false) {
        $result = filter_var($value, $filter, $options);
        if ($error_on_false and false === $result) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $result;
    };
}

/**
 * Affiche un message d'erreur HTML et jette une exception.
 * @param mixed $arg La valeur à inclure avec le message d'erreur.
 * @throws DomainException Toujours, avec $arg.
 */
function html_error(mixed $arg): never
{
    ?>
    <p>Erreur: <?= h14s(strval($arg)) ?></p><?php
    throw new DomainException($arg);
}

/**
 * if-truthy-append-with-separator
 * Concatène une chaîne à un suffixe ou retourne la chaîne vide.
 * @param ?string $chaine La chaîne (peut être `null`).
 * @param ?string $suffixe Le suffixe à concaténer à $chaine.
 * @return string La chaîne formatée.
 */
function itaws(?string $chaine, ?string $suffixe): string
{
    return $chaine ? "$chaine$suffixe" : '';
}

/**
 * Retourne une fonction prédicat qui vérifie qu'une chaîne est contenue dans une liste de valeurs autoriséess.
 * @param string[] $allowed_values Les valeurs autorisées.
 * @return callable(string): bool Une fonction prédicat validant une chaîne.
 */
function f_is_in(array $allowed_values): callable
{
    return fn($value) => in_array($value, $allowed_values);
}

/**
 * Returns a function prédicat qui vérifie qu'un tableau contient toutes les clés spécifiées
 * @param array $keys Les clés devant être dans le tableau.
 * @return callable(array): bool Un fonction prédicat validant un tableau.
 */
function f_array_has_keys(array $keys): callable
{
    return fn($value) => is_array($value) && array_every($keys, fn($key) => isset($value[$key]));
}

/**
 * Retourne le seul élément d'un tableau.
 * @template T
 * @param array<T> $array Un tableau devant contenir extactement 1 élément.
 * @return T Le seul élément de $array.
 * @throws DomainException Si $array ne contient pas exactement 1 élément.
 */
function single(array $array): mixed
{
    if (count($array) !== 1) {
        throw new DomainException('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Retourne le seul élément d'un tableau, ou une valeur par défaut si le tableau est vide.
 * @template T
 * @param array<T> $array Un tableau devant contenir 0 ou 1 élément.
 * @param T $default La valeur par défaut à retourner quand $array est vide.
 * @return T Le seul élément de $array, ou $default si $array est vide.
 * @throws DomainException Si le tableau contient plus d'une valeur.
 */
function single_or_default(array $array, mixed $default = null): mixed
{
    if (empty($array)) {
        return $default;
    }
    if (count($array) !== 1) {
        throw new DomainException('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Converts a "structre of arrays" (SOA) to an "array of structures" (AOS).
 *
 * The input `$array` is expected to be an array where each element is an associative
 * array with the same set of keys. This function will return a new array where each
 * element is an array containing the values for each key from the input array.
 * @param array<array> $array The input "structre of arrays" to convert.
 * @return array<int, mixed> The resulting "array of structures".
 *
 * @example
 * Input: ['debut' => [1, 3], 'fin' => [2, 4]]
 * Output: [['debut' => 1, 'fin' => 2], ['debut' => 3, 'fin' => 4]]
 */
function soa_to_aos(array $array): array
{
    $result = [];
    foreach ($array as $key => $all) {
        foreach ($all as $i => $val) {
            $result[$i][$key] = $val;
        }
    }
    return $result;
}

/**
 * DÉBOGAGE UNIQUEMENT - Affiche une valeur et la renvoie
 * @template T
 * @param T $value
 * @return T
 */
function dbg_print(mixed $value): mixed
{
?><pre><samp><?php var_dump($value) ?></samp></pre><?php
    return $value;
}

function fail_404(): never
{
    http_response_code(404);
    require __DIR__ . '/../html/404.html';
    exit;
}

/**
 * Intermixes the keys and values of an array in a zero-indexed list.
 * @template TKey
 * @template TValue
 * @param array<TKey, TValue> $assoc An associative array.
 * @return (TKey|TValue)[] The result list-like array.
 * @example
 * ```
 * array_intermix_keys_and_values(['a' => 5, 'b' => 10]) === ['a', 5, 'b', 10]
 * ```
 */
function array_intermix_keys_and_values(array $assoc): array {
    $result = [];
    foreach ($assoc as $key => $value) {
        $result[] = $key;
        $result[] = $value;
    }
    return $result;
}

/**
 * @template TKey
 * @template TValue
 * @param iterable<iterable<TKey, TValue>> $iterable
 * @return iterable<TKey, TValue>
 */
function iterable_flatten(iterable $iterable): iterable
{
    foreach ($iterable as $items) {
        yield from $items;
    }
}