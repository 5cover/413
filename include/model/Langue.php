<?php

final class Langue
{
    public function __construct(
        readonly string $code,
        readonly string $libelle
    ) {}
}
