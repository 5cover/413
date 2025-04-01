<?php
namespace DB;

use DateTimeImmutable;

require_once 'util.php';

/**
 * Abstraction du type TIME PostgreSQL
 */
final readonly class Time
{
    private const FORMAT = 'H:i:s';

    private function __construct(
        private DateTimeImmutable $datetime
    ) {}

    /**
     * Parse une heure depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?self Un nouvelle heure, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws \DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?self
    {
        return $output === null ? null : new self(notfalse(
            DateTimeImmutable::createFromFormat(self::FORMAT, $output)
                ?: DateTimeImmutable::createFromFormat('H:i', $output)
        ));
    }

    function __toString(): string
    {
        return $this->datetime->format(self::FORMAT);
    }
}
