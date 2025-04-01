<?php
require_once 'model/Professionnel.php';

final class ProfessionnelPublic extends Professionnel
{
    function __construct(
        array $args_compte,
        array $args_professionnel,
    ) {
        parent::__construct($args_compte, ...$args_professionnel);
    }

    const TABLE = 'pro_public';
}
