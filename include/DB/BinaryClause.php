<?php
namespace DB;

use Generator;

require_once 'Arg.php';
require_once 'BinOp.php';

/**
 * @template T
 */
final readonly class BinaryClause implements Clause
{
    /**
     * @param string $column
     * @param BinOp $operator
     * @param T $value
     * @param int $pdo_type
     */
    function __construct(
        private string $column,
        private BinOp  $operator,
        /**
         * @var T
         */
        private mixed  $value,
        private int    $pdo_type,
    ){    }

    /**
     * @return Generator<int|string, Arg>
     */
    function to_args(): Generator {
        yield $this->column => new Arg($this->value, $this->pdo_type);
    }

    /**
     * @return string
     */
    function to_sql(): string
    {
        return "$this->column $this->operator :$this->column";
    }
}
