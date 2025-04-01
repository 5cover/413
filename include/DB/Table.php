<?php
namespace DB;

enum Table: string
{
    case Avis = 'avis';
    case AvisRestaurant = 'avis_restaurant';
    case Adresse = '_adresse';
    case Blacklist = 'blacklist';
    case Compte = '_compte';
    case Professionnel = 'professionnel';
    case Offre = 'offres';
    case Image = '_image';
    case Activite = 'activte';
    case ParcAttractions = 'parc_attractions';
    case Restaurant = 'restaurant';
    case Membre = 'membre';
    case Spectacle = 'spectacle';
    case Visite = 'visite';
    case OuvertureHebdomadaire = '_ouverture_hebdomadaire';
    case Galerie = '_galerie';
    case ImageFromGalerie = 'image_from_galerie';
    case Tags = '_tags';
    case Tarif = '_tarif';
    case ChangementEtat = '_changement_etat';
}