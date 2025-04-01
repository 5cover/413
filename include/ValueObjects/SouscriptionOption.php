<?php

final readonly class SouscriptionOption
{
    private function __construct(
        public bool            $actif,
        public string          $nom,
        public DB\FiniteTimestamp $lancee_le,
        public int             $nb_semaines,
        public float           $prix_hebdomadaire,
    ) {}

    static function parse_json(?string $json_output): ?SouscriptionOption {
        if ($json_output === null) return null;
        [$actif, $nom, $lancee_le, $nb_semaines, $prix_hebdomadaire] = json_decode($json_output);
        return new SouscriptionOption($actif, $nom, DB\FiniteTimestamp::parse($lancee_le), $nb_semaines, $prix_hebdomadaire);
    }
}
