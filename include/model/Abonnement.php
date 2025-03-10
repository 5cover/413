<?php
require_once 'db.php';

final class Abonnement
{
    private function __construct(
        readonly string $libelle,
        readonly float $prix_journalier,
        readonly string $description,
    ) {}

    private static ?array $instances;

    /**
     * Obotient tous les abonnements de la BDD
     * @return self[]
     */
    static function all(): array {
        return self::$instances ??= array_map(
            fn($row) => new self(
                $row['libelle'],
                parse_float($row['prix_journalier']),
                $row['description'],
            ),
            array_column(DB\connect()->query('select * from ' . self::TABLE)->fetchAll(), null, 'libelle'),
        );
    }

    const TABLE = '_abonnement';
}
