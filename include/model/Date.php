<?php

require_once 'util.php';

/**
 * Abstraction du type PostgeSQL DATE.
 */
final class Date
{
    private const FORMATS = ['Y-m-d'];

    private function __construct(
        private readonly DateTimeImmutable $datetime,
    ) {}

    /**
     * Parse une date depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?Date Un nouvelle date, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?Date
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
}
