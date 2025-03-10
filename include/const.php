<?php

/**
 * Des propositions de tag.
 * @var string[]
 */
const DEFAULT_TAGS = [
    'atelier',
    'cinéma',
    'cirque',
    'culturel',
    'famille',
    'histoire',
    'humour',
    'musée',
    'musique',
    'nature',
    'patrimoine',
    'son et lumière',
    'urbain',
    'sport',
];

/**
 * Des propositions de tag de restaurant.
 * @var string[]
 */
const TAGS_RESTAURANT = [
    'asiatique',
    'crêperie',
    'française',
    'fruits de mer',
    'gastronomique',
    'indienne',
    'restauration rapide',
];

/**
 * Les catégories d'offres.
 * Les clés correspondent au type énuméré `categorie_offre` dans `types.sql`.
 * Les valeurs correspondent au nom de catégorie précédé par un article indéfini.
 * @var array<string, string>
 */
const CATEGORIES_OFFRE = [
    'activité' => 'une activité',
    "parc d'attractions" => "un parc d'attractions",
    'restaurant' => 'un restaurant',
    'spectacle' => 'un spectacle',
    'visite' => 'une visite'
];

/**
 * Les jours de la semaine. Les indices représentent les numéros (de 0 à 6 pour dimanche à samedi). Cet ordre est notamment utilisé dans JavaScript et PostgreSQL.
 *
 * @var array<int, string>
 */
const JOURS_SEMAINE = [
    0 => 'dimanche',
    1 => 'lundi',
    2 => 'mardi',
    3 => 'mercredi',
    4 => 'jeudi',
    5 => 'vendredi',
    6 => 'samedi',
];

/**
 * Des propositions de contextes de visite pour les avis.
 * @var string[]
 */
const CONTEXTES_VISITE = [
    'affaires',
    'couple',
    'solo',
    'famille',
    'amis',
];

const PDO_PARAM_FLOAT = PDO::PARAM_STR;
