<?php

namespace DB;

use PDO;
use PDOStatement;

final class LogPDO extends PDO
{
    public bool $log;
    public int $query_no = 1;

    function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        if ($this->log) error_log("LogPDO ($this->query_no) query: '$query'");
        ++$this->query_no;
        return parent::query($query, $fetchMode, $fetchModeArgs);
    }

    function prepare(string $query, array $options = []): PDOStatement|false
    {
        if ($this->log) error_log("LogPDO ($this->query_no) prepare: '$query'");
        ++$this->query_no;
        return parent::prepare($query, $options);
    }
}