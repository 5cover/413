<?php
require_once 'model/Compte.php';

/**
 * @inheritDoc
 * @property-read ?string $secteur `null` si ce professionnel n'existe pas dans la BDD.
 */
abstract class Professionnel extends Compte
{
    protected static function fields()
    {
        return parent::fields() + [
            'denomination' => [null, 'denomination', PDO::PARAM_STR],
        ];
    }

    protected static function computed_fields()
    {
        return parent::computed_fields() + [
            'secteur' => [null, 'secteur', PDO::PARAM_STR],
        ];
    }

    function __construct(
        $args_compte,
        public string $denomination,
        //
        protected ?string $secteur = null,
    ) {
        parent::__construct(...$args_compte);
    }

    /**
     * Récupère un professionnel de la BDD.
     * @param int $id_professionnel
     * @return self|false
     */
    static function from_db(int $id_professionnel): self|false
    {
        $compte = parent::from_db($id_professionnel);
        if ($compte === false or !$compte instanceof self) return false;
        return $compte;
    }

    const TABLE = 'professionnel';
}
