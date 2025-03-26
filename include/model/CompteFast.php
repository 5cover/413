<?php
require_once 'db.php';

class CompteFast
{
    function __construct(
        public ?int $id,
        public string $email,
        public string $mdp_hash,
        public string $nom,
        public string $prenom,
        public string $telephone,
        public ?string $adresse,
        public ?Uuid $api_key,
        public ?string $otp_secret,
    ) { }
}