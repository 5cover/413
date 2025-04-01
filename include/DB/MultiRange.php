<?php
namespace DB;

use DomainException;

require_once 'util.php';
require_once 'DB/NonEmptyRange.php';

/**
 * @template T Type du multirange.
 * Abstraction du type PostgreSQL `<type>multirange`
 *
 * N'effectue pas de normalisation.
 */
final readonly class MultiRange
{
    static function empty(): self {
        static $empty = new MultiRange([]);
        return $empty;
    }

    /**
     * @param NonEmptyRange[] $ranges The ranges nested in this multirange.
     */
    function __construct(
        public array $ranges
    ) {}

    function __toString(): string
    {
        return '{' . implode(',', $this->ranges) . '}';
    }

    /**
     * Parse un multirange depuis la sortie PostgreSQL.
     * @template TBound
     * @param ?string $output La sortie PostgreSQL.
     * @param ?callable(string): TBound $parse_bound La fonction parsant les bornes. Peut jeter `DomainException` si la syntaxe est invalide. `null` implique la fonction identité.
     * @return ?MultiRange<TBound> Un nouveau timestamp, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws \DomainException En cas de mauvaise syntaxe.
     */
    public static function parse(?string $output, ?callable $parse_bound = null): ?MultiRange
    {
        if ($output === null) return null;

        $output = trim($output);
        if (strlen($output) === 0 || $output[0] !== '{' || $output[-1] !== '}') {
            throw new DomainException();
        }
        $output = trim(substr($output, 1, -1));

        $ranges = [];
        if ($output !== '') {
            [$ranges[], $read_length] = notfalse(NonEmptyRange::read($output, $parse_bound));
            while ($read_length < strlen($output)) {
                while (ctype_space($output[$read_length])) {
                    ++$read_length;
                }
                if ($output[$read_length++] !== ',') {
                    throw new DomainException();
                }
                while (ctype_space($output[$read_length])) {
                    ++$read_length;
                }

                [$ranges[], $rl] = notfalse(NonEmptyRange::read(substr($output, $read_length), $parse_bound));
                $read_length    += $rl;
            }
        }

        return new self($ranges);
    }
}
