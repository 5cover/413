<?php

namespace Kcrf\Blacklist;

require_once 'DB/db.php';
require_once 'redirect.php';

use DB;
use PDO;


function get_blacklist(int $id): ?string
{
    // @kcrf-fix SQL DSL
    $stmt = DB\connect()->prepare('select fin_blacklist from ' . DB\Table::Blacklist->value . ' where id=?');
    DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    $r = $stmt->fetchColumn();
    return $r === false ? null : $r;
}

function toggle_blacklist(int $id, DB\Date $finblacklist): bool
{
    if (get_blacklist($id) === null) {
        $stmt = DB\connect()->prepare('insert into ' . DB\Table::Blacklist->value . ' (id,fin_blacklist) values (?,?)');
        DB\bind_values($stmt, [1 => [$id, PDO::PARAM_INT], 2 => [$finblacklist, PDO::PARAM_STR]]);
        return $stmt->execute();
    } else {
        // kcrf-fix SQL DSL
        return false;
    }
}

function nb_blacklist_restantes(int $id_pro): int
{
    // kcrf-fix SQL DSL
    $stmt = DB\connect()->prepare('select
    count(*)
    from
    blacklists_effectives
    join _avis using (id)
    join _offre on _avis.id_offre = _offre.id
    where
    id_professionnel = ?');
    DB\bind_values($stmt, [1 => [$id_pro, PDO::PARAM_INT]]);
    $stmt->execute();
    return 3 - $stmt->fetchColumn();
}
