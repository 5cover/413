<?php

final class AvisRestaurant extends Avis
{
    protected static function fields()
    {
        $fields = parent::fields() + [
            'note_cuisine'      => [null, 'note_cuisine',      PDO::PARAM_INT],
            'note_service'      => [null, 'note_service',      PDO::PARAM_INT],
            'note_ambiance'     => [null, 'note_ambiance',     PDO::PARAM_INT],
            'note_qualite_prix' => [null, 'note_qualite_prix', PDO::PARAM_INT],
        ];
        $fields['id_restaurant'] = array_pop_key($fields, 'id_offre');
        return $fields;
    }

    function __construct(
        array $args_avis,
        public int $note_cuisine,
        public int $note_service,
        public int $note_ambiance,
        public int $note_qualite_prix,
    ) {
        parent::__construct(...$args_avis);
    }

    const TABLE = 'avis_restaurant';
}
