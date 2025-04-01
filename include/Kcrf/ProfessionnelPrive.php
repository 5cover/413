<?php
namespace Kcrf;
//@kcrf-fix implement
/*
require_once 'DB/db.php';

require_once 'Kcrf/Professionnel.php';

final class ProfessionnelPrive extends Professionnel
{
    protected static function fields()
    {
        return parent::fields() + [
            'siren' => [null, 'siren', PDO::PARAM_STR],
        ];
    }

    function __construct(
        array $args_compte,
        array $args_professionnel,
        public string $siren,
    ) {
        parent::__construct($args_compte, ...$args_professionnel);
    }

    const TABLE = 'pro_prive';
}
*/