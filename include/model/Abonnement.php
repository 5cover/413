<?php
require_once 'db.php';

final class Abonnement
{
    private function __construct(
        readonly string $libelle,
        readonly float $prix_journalier,
    ) {}

    private static ?array $instances;

    static function from_db(string $libelle_abonnement): self
    {
        self::$instances ??= array_map(
            fn($row) => new self(
                $row['libelle'],
                parse_float($row['prix_journalier']),
            ),
            array_column(DB\connect()->query('select libelle, prix_journalier from ' . self::TABLE)->fetchAll(), null, 'libelle'),
        );
        return self::$instances[$libelle_abonnement];
    }

    const TABLE = '_abonnement';
}
