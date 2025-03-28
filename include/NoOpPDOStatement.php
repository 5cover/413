<?php

namespace DB;

use PDO;
use PDOStatement;

final class NoOpPDOStatement extends PDOStatement
{
    private function __construct()
    {
    }

    private static ?self $instance;

    static function instance(): self
    {
        return self::$instance ??= new self();
    }

    function execute(?array $params = null): bool { return true; }
    function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR): bool { return true; }
}