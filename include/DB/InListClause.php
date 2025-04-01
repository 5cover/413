<?php

namespace DB;

use Generator;

/**
 * @template T
 */
final readonly  class InListClause implements Clause
{
    /**
     * @param string $column
     * @param T[] $value
     * @param int $item_pdo_type
     */
    function __construct(
        private string $column,
        private array  $value,
        private int    $item_pdo_type,
    )
    {
    }

    /**
     * @inheritDoc
     */
    function to_args(): Generator
    {
        $i = 1;
        foreach ($this->value as $item) {
            yield $i++ => new Arg($item, $this->item_pdo_type);
        }
    }

    /**
     *
     * @inheritDoc
     */
    public function to_sql(): string
    {
        $placeholders = substr(str_repeat('?,', count($this->value)), 0, -1);
        return "$this->column in($placeholders)";
    }
}