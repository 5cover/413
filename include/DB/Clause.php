<?php

namespace DB;

use Generator;

interface Clause
{
    /**
     * Generates the arguments for this clause.
     * @return Generator<int|string, Arg>
     */
    function to_args(): Generator;

    /**
     * Returns an SQL boolean expression that evaluates whether this clause is matched.
     * @return string
     */
    function to_sql(): string;
}