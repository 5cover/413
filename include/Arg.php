<?php

namespace DB;

/**
 * @template T
 */
final readonly class Arg
{
    /**
     * @param T $value
     * @param int $pdo_type
     */
    function __construct(
        public mixed $value,
        public int   $pdo_type,
    )
    {
    }


}