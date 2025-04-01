<?php
namespace Kcrf;

require_once 'DB/db.php';

use ValueObjects\LibelleAbonnement;
use DB;

final class Abonnement
{
    private function __construct(
        // Key
        readonly LibelleAbonnement $libelle,

        // Regular
        readonly float $prix_journalier,
        readonly string $description,
    ) {}

    /**
     * @var array<string, self>
     */
    private static ?array $instances;

    /**
     * Obotient tous les abonnements de la BDD, clé par valeur de libellé
     * @return array<string, self>
     */
    static function from_db_all(): array {
        return self::$instances ??= array_map(
            fn($row) => new self(
                $row->libelle,
                parse_float($row->prix_journalier),
                $row->description,
            ),
            array_column(DB\connect()->query('select * from ' . self::TABLE)->fetchAll(), null, 'libelle'),
        );
    }

    static function from_db(LibelleAbonnement $libelle_abonnement): self {
        return self::from_db_all()[$libelle_abonnement->value];
    }

    const TABLE = '_abonnement';
}
