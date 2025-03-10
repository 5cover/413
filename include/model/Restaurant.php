<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class Restaurant extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'carte'               => [null, 'carte',               PDO::PARAM_STR],
            'richesse'            => [null, 'richesse',            PDO::PARAM_INT],
            'sert_petit_dejeuner' => [null, 'sert_petit_dejeuner', PDO::PARAM_BOOL],
            'sert_brunch'         => [null, 'sert_brunch',         PDO::PARAM_BOOL],
            'sert_dejeuner'       => [null, 'sert_dejeuner',       PDO::PARAM_BOOL],
            'sert_diner'          => [null, 'sert_diner',          PDO::PARAM_BOOL],
            'sert_boissons'       => [null, 'sert_boissons',       PDO::PARAM_BOOL],
        ];
    }

    // todo: langues

    function __construct(
        array $args_offre,
        public string $carte,
        public int $richesse,
        public bool $sert_petit_dejeuner,
        public bool $sert_brunch,
        public bool $sert_dejeuner,
        public bool $sert_diner,
        public bool $sert_boissons,
    ) {
        parent::__construct(...$args_offre);
    }

    const CATEGORIE = 'restaurant';
    const TABLE     = 'restaurant';
}
