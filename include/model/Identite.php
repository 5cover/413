<?php

require_once 'model/Model.php';

/**
 * @property-read ?int $id L'ID. `null` si cette identité n'existe pas dans la BDD.
 */
abstract class Identite extends Model
{
    protected static function key_fields()
    {
        return [
            'id' => ['id', PDO::PARAM_INT, null],
        ];
    }

    function __construct(
        protected ?int $id
    ) {}

    const TABLE = '_identite';
}
