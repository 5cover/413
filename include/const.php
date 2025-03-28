<?php

// If you change this, change it in the tchatator too.
const PASSWORD_ALGO = PASSWORD_BCRYPT;

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

enum Categorie: string
{
    case Restaurant = 'restaurant';
    case ParcAttractions = "parc d'attractions";
    case Activite = 'activité';
    case Spectacle = 'spectacle';
    case Visite = 'visite';
}

/**
 * Les catégories d'offres.
 * Les clés correspondent au type énuméré `categorie_offre` dans `types.sql`.
 * Les valeurs correspondent au nom de catégorie précédé par un article indéfini.
 * @var array<string, string>
 */
const CATEGORIES_OFFRE = [
    Categorie::Activite->name => 'une activité',
    Categorie::ParcAttractions->name => "un parc d'attractions",
    Categorie::Restaurant->name => 'un restaurant',
    Categorie::Spectacle->name => 'un spectacle',
    Categorie::Visite->name => 'une visite'
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

const MOIS = [
    1 => 'janvier',
    2 => 'février',
    3 => 'mars',
    4 => 'avril',
    5 => 'mai',
    6 => 'juin',
    7 => 'juillet',
    8 => 'août',
    9 => 'septembre',
    10 => 'octobre',
    11 => 'novembre',
    12 => 'décembre',
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
    'autre',
];

const NBSP = "\xc2\xa0";

