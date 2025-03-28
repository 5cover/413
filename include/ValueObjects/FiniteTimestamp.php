<?php

require_once 'util.php';

/**
 * Abstraction du type PostgeSQL TIMESTAMP (WITHOUT TIME ZONE) fini.
 */
final class FiniteTimestamp implements JsonSerializable
{
    private const FORMAT_DATE = 'Y-m-d';
    private const FORMAT = '!Y-m-d H:i:s.u';

    private function __construct(
        readonly DateTimeImmutable $datetime,
    ) {}

    /**
     * Parse un timestamp fini.
     * @param ?string $output Le timestamp (sortie PostgreSQL ou autre).
     * @return ?FiniteTimestamp Un nouveau timestamp fini, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DateMalformedStringException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?FiniteTimestamp
    {   
        return $output === null
            ? null
            : new self(new DateTimeImmutable($output));
    }

    function __toString(): string
    {
        return $this->datetime->format(self::FORMAT);
    }

    function format_date(): string {
        return $this->datetime->format('d/m/Y H:i:s');
    }

    /**
     * @inheritDoc
     */
    function jsonSerialize(): mixed {
        return $this->datetime->format(DATE_ATOM);
    }
}
