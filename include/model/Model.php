<?php
require_once 'db.php';

abstract class Model
{
    /**
     * Table name.
     * @var string
     */
    const TABLE = null;  // abstract constant

    /**
     * Stuff that you can set.
     * column name => [map from PHP attribute value to DB column or `null` for identity, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int}>
     */
    protected static function fields() { return []; }

    /**
     * Key fields that uniquely identify a row in the DB table.
     * column name => [map from DB column to PHP attribute value or null for identity, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int}>
     */
    protected static function key_fields() { return []; }

    /**
     * Additional fields to set with the insertion RETURNING clause.
     * column name => [map from DB column to PHP attribute value, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int, }>
     */
    protected static function computed_fields() { return []; }

    function __get(string $name): mixed
    {
        if (array_some($this->key_fields(), fn($f) => $f[1] === $name)
                || array_some($this->computed_fields(), fn($f) => $f[1] === $name)) {
            return $this->$name;
        }
        throw new Exception('Undefined property: ' . static::class . "::\$$name");
    }
    function __set(string $name, $value): void
    {
        if (array_some($this->key_fields(), predicate: fn($f) => $f[1] === $name)
                || array_some($this->computed_fields(), fn($f) => $f[1] === $name)) {
            $this->$name = $value;
        }
        throw new Exception('Undefined property: ' . static::class . "::\$$name");
    }

    function push_to_db(): void
    {
        if ($this->exists_in_db()) {
            $returning_fields = static::computed_fields();
            $stmt = DB\update(
                static::TABLE,
                $this->args(),
                $this->key_args(),
                array_keys($returning_fields),
            );
        } else {
            $returning_fields = static::key_fields() + static::computed_fields();
            $stmt = DB\insert_into(
                static::TABLE,
                $this->args(),
                array_keys($returning_fields),
            );
        }
        notfalse($stmt->execute());
        if ($returning_fields) {
            $row = notfalse($stmt->fetch());
            foreach ($returning_fields as $column => [$db_to_php, $attr, $type]) {
                $this->$attr = $db_to_php === null ? $row[$column] : $db_to_php($row[$column]);
            }
        }
    }

    function delete(): void
    {
        if (!$this->exists_in_db()) {
            return;
        }
        $stmt = DB\delete_from(
            static::TABLE,
            $this->key_args(),
        );
        notfalse($stmt->execute());
        foreach (array_keys(static::key_fields()) as $attr) {
            $this->$attr = null;
        }
    }

    private function exists_in_db(): bool
    {
        return array_every(array_keys(static::key_fields()), fn($attr) => $this->$attr !== null);
    }

    private function key_args(): array
    {
        $args = [];
        foreach (static::key_fields() as $column => [$db_to_php, $attr, $type]) {
            $args[$column] = [$this->$attr, $type];
        }
        return $args;
    }

    /**
     * @return array<string, array{mixed, int}>
     */
    private function args(): array
    {
        $args = [];
        foreach (static::fields() as $column => [$php_to_db, $attr, $type]) {
            $args[$column] = [$php_to_db === null ? $this->$attr : $php_to_db($this->$attr), $type];
        }
        return $args;
    }
}
