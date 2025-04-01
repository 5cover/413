<?php
namespace Kcrf;

use DB;
use DB\FiniteTimestamp;

final class AvisRestaurantData
{
    function __construct(
        public float $note_cuisine,
        public float $note_service,
        public float $note_ambiance,
        public float $note_qualite_prix,
    )
    {
    }

    static function parse(object $row): self {
        return new self(
            $row->note_cuisine,
            $row->note_service,
            $row->note_ambiance,
            $row->note_qualite_prix,
        );
    }
}

final class AvisRestaurant extends Avis
{
    const TABLE = DB\Table::AvisRestaurant;

    function __construct(
        // Key
        int $id,
        // Computed
        FiniteTimestamp $publie_le,
        AvisData $data,

        public AvisRestaurantData $restaurant_data,
    ) {
        parent::__construct($id, $publie_le, $data);
    }
}
