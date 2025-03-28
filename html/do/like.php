<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'cookie.php';

$id_avis   = getarg($_GET, 'id_avis', arg_int());
$new_state = getarg($_GET, 'new_state', arg_filter(FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE, error_on_false: false), required: false);

$old_state = Cookie\CommentLikes::likes($id_avis);

if ($new_state === $old_state)
    http_exit(200);

/*
 * old  |new  |delta
 * false|false|0
 * false|true |-1 dislike +1 like
 * false|null |-1 dislike
 * true |false|+1 dislike -1 like
 * true |true |0
 * true |null |-1 like
 * null |false|+1 dislike
 * null |true |+1 like
 * null |null |0
 */

[$dislikes, $likes] = match ([$old_state, $new_state]) {
    [false, true] => [-1, 1],
    [false, null] => [-1, 0],
    [true, false] => [1, -1],
    [true, null]  => [0, -1],
    [null, false] => [1, 0],
    [null, true]  => [0, 1],
};

error_log(var_export([
    1 => [$dislikes, PDO::PARAM_INT],
    2 => [$likes, PDO::PARAM_INT],
    3 => [$id_avis, PDO::PARAM_INT],
], true));

$stmt = DB\connect()->prepare('update _avis set dislikes=GREATEST(0, dislikes+?), likes=GREATEST(0, likes+?) where id=?');
DB\bind_values($stmt, [
    1 => [$dislikes, PDO::PARAM_INT],
    2 => [$likes, PDO::PARAM_INT],
    3 => [$id_avis, PDO::PARAM_INT],
]);

if ($stmt->execute() === false or $stmt->rowCount() !== 1)
    http_exit(500);

if ($new_state === null) {
    Cookie\CommentLikes::unset($id_avis);
} else {
    Cookie\CommentLikes::set($id_avis, $new_state);
}

http_exit(200);
