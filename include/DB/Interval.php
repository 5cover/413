<?php
namespace DB;

use DomainException;

/**
 * @property-read float $total_days
 */
final class Interval
{
    function __construct(
        readonly int $years     = 0,
        readonly int $months    = 0,
        readonly int $days      = 0,
        readonly int $hours     = 0,
        readonly int $minutes   = 0,
        readonly float $seconds = 0,
    ) {}

    function __get(string $name): int|float
    {
        return match ($name) {
            'total_days' => $this->years * 360
                + $this->months * 30
                + $this->days
                + $this->hours / 24
                + $this->minutes / 1440
                + $this->seconds / 86400
        };
    }

    function __toString(): string
    {
        return "$this->years years $this->months mons $this->days days $this->hours:$this->minutes:$this->seconds";
    }

    /**
     * Parse une durée depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL en format d'intervalle `postgres`.
     * @return ?Interval Un nouvelle durée, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws \DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?Interval
    {
        if ($output === null) return null;

        $matches = [];
        $years   = 0;
        $months  = 0;
        $days    = 0;
        $hours   = 0;
        $minutes = 0;
        $seconds = 0;
        if (notfalse(preg_match('/(\d+) years/', $output, $matches)) === 1
            && false === ($years = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+) mons/', $output, $matches)) === 1
                && false === ($months = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+) days/', $output, $matches)) === 1
                && false === ($days = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+):(\d+):(\d*\.?\d+)/', $output, $matches)) === 1
                && (false === ($hours = parse_int($matches[1], 0))
                    || false === ($minutes = parse_int($matches[2], 0))
                    || false === ($seconds = parse_float($matches[3], 0)))) {
            throw new DomainException();
        }
        return new Interval($years, $months, $days, $hours, $minutes, $seconds);
    }
}
