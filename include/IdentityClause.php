<?php
namespace DB;

use Generator, PDO;

final readonly class IdentityClause implements Clause
{
    function __construct(
        private string $column,
        private bool   $value,
    )
    {
    }

    function to_args(): Generator
    {
        yield $this->column => new Arg($this->value, PDO::PARAM_BOOL);
    }

    function to_sql(): string
    {
        return $this->column;
    }
}