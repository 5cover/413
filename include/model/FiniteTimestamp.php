<?php

require_once 'util.php';

/**
 * Abstraction du type PostgeSQL TIMESTAMP (WITHOUT TIME ZONE) fini.
 */
final class FiniteTimestamp implements JsonSerializable
{
    private const FORMAT_DATE = 'Y-m-d';
    private const FORMATS = ['!Y-m-d H:i:s.u', '!Y-m-d H:i:s', '!Y-m-d\\TH:i:s.u'];
//"2025-01-10T16:02:10.029899"
    private function __construct(
        private readonly DateTimeImmutable $datetime,
    ) {}

    /**
     * Parse un timestamp fini depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?FiniteTimestamp Un nouveau timestamp fini, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?FiniteTimestamp
    {
        return $output === null
            ? null
            : new self(notfalse(iterator_first_notfalse(self::FORMATS,
                fn($fmt) => DateTimeImmutable::createFromFormat($fmt, $output))));
    }

    function __toString(): string
    {
        return $this->datetime->format(self::FORMATS[0]);
    }

    function format_date(): string {
        return $this->datetime->format(self::FORMAT_DATE);
    }

    /**
     * @inheritDoc
     */
    function jsonSerialize(): mixed {
        return $this->datetime->format(DATE_ATOM);
    }
}
