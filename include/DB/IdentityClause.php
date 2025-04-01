<?php
namespace DB;

use Generator;

final readonly class IdentityClause implements Clause
{
    function __construct(
        private string $column,
    )
    {
    }

    function to_args(): Generator
    {
        yield from [];
    }

    function to_sql(): string
    {
        return $this->column;
    }
}