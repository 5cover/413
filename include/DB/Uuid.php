<?php
namespace DB;

final class Uuid {
    private function __construct(private string $repr) {}

    /**
     * Parse un UUID depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?self Un nouvel UUID, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?self
    {
        return $output === null ? null : new self($output);
    }

    function __toString(): string {
        return $this->repr;
    }
}