begin;
-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

-- Info:
-- Ajouter "not null" aux attributs clés étrangères ne faisant pas partie de la clé primaire. La contrainte "references" n'implique pas "not null". La contrainte "primary key" implique "not null unique"


drop schema if exists pact cascade;

create schema pact;

set schema 'pact';

create domain num_departement as char(3);
create domain iso639_1 as char(2);
create domain nom_option char(10);
create domain ligne as varchar check (value not like E'%\n%'); -- une ligne de texte
create domain paragraphe as varchar; -- un paragraphe de texte
create domain numero_telephone as char(10) check (value ~ '^[0-9]+$');
create domain numero_siren as char(9) check (value ~ '^[0-9]+$');
create domain adresse_email as varchar(319) check (value ~ '^(?:[a-z0-9!#$%&''*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&''*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$');

-- 63 car. max, non vide, pas de caractères de contrôle, pas de blancs en début et fin, l'espace ' ' est le seul blanc autorisé.
-- pas d'arobase (@), pour éviter la confusion avec une adresse e-mail
create domain pseudonyme as varchar(63) check (value ~ '^[^@\s[:cntrl:]](?: |[^@\s[:cntrl:]])*[^@\s[:cntrl:]]?$');

-- CLASSES

create table _departement(
    numero num_departement
        constraint departement_pk primary key,
    nom ligne not null unique
);

create table _commune(
    code int,
    numero_departement num_departement
        constraint commune_fk_departement references _departement,
    constraint commune_pk primary key (code, numero_departement),
        
    nom ligne not null
);

create table _adresse(
    id serial
        constraint adresse_pk primary key,

    code_commune int not null,
    numero_departement num_departement not null,
    constraint adresse_fk_commune foreign key (code_commune, numero_departement) references _commune,

    numero_voie int not null default 0,
    complement_numero varchar(10) not null default '',
    check (numero_voie is not null or complement_numero is null), -- numero_voie is null => complement_numero is null

    nom_voie ligne not null default '',
    localite ligne not null default '',
    precision_int ligne not null default '',
    precision_ext ligne not null default '',

    latitude decimal,
    longitude decimal,
    check ((latitude is null) = (longitude is null))
);

create table _abonnement(
    libelle ligne
        constraint abonnement_pk primary key,
    prix decimal not null
);

create table _image(
    id serial
        constraint image_pk primary key,
    taille int not null,
    mime_subtype varchar(127) not null, -- Mime subtype (part after 'image/'). Used as a file extension.
    legende ligne not null default ''
);

create table _signalable(
    id serial
        constraint signalable_pk primary key
);

create table _identite(
    id serial
        constraint identite_pk primary key
);

create table _compte(
    id int
        constraint compte_pk primary key
        constraint compte_inherits_identite references _identite,
    id_signalable int not null unique
        constraint compte_inherits_signalable references _signalable,
    email adresse_email not null unique,
    mdp_hash varchar(255) not null,
    nom ligne not null,
    prenom ligne not null,
    telephone numero_telephone not null
);

create table _professionnel(
    id int
        constraint professionnel_pk primary key
        constraint professionnel_inherits_compte references _compte,
    denomination ligne not null
);

create table _offre(
    id int
        constraint offre_pk primary key
        constraint offre_inherits_signalable references _signalable,
    id_adresse int not null
        constraint offre_fk_adresse references _adresse,
    id_image_principale int not null
        constraint offre_fk_image references _image,
    libelle_abonnement ligne not null
        constraint offre_fk_abonnement references _abonnement,
    id_professionnel int not null
        constraint offre_fk_professionnel references _professionnel,
    titre ligne not null,
    resume ligne not null,
    description_detaillee paragraphe not null,
    date_derniere_maj timestamp not null,
    url_site_web varchar(2047) not null
);

create table _restaurant(
    id int
        constraint restaurant_pk primary key
        constraint restaurant_inherits_offre references _offre,
    carte paragraphe not null,
    richesse int not null check (1 <= richesse and richesse <= 3),

    sert_petit_dejeuner boolean not null,
    sert_brunch boolean not null,
    sert_dejeuner boolean not null,
    sert_diner boolean not null,
    sert_boissons boolean not null,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boissons)
);

create table _activite(
    id int
        constraint activite_pk primary key
        constraint activite_inherits_offre references _offre,
    indication_duree interval not null,
    age_requis int not null,
    prestations_incluses paragraphe not null,
    prestations_non_incluses paragraphe not null
);

create table _visite(
    id int
        constraint visite_pk primary key
        constraint visite_inherits_offre references _offre,
    indication_duree interval not null
);

create table _langue(
    code iso639_1
        constraint langue_pk primary key,
    libelle ligne not null
);

create table _spectacle(
    id int
        constraint spectacle_pk primary key
        constraint spectacle_inherits_offre references _offre,
    indication_duree interval not null,
    capacite_accueil int not null
); 

create table _parc_attractions(
    id int
        constraint parc_attractions_pk primary key
        constraint parc_attractions_inherits_offre references _offre,
    id_image_plan int not null
        constraint parc_attractions_fk_image_plan_parc references _image
);

create table _visiteur(
    id int
        constraint visiteur_pk primary key
        constraint visiteur_inherits_identite references _identite,
    ip int not null unique
);

create table _prive(
    id int
        constraint prive_pk primary key
        constraint prive_inherits_professionnel references _professionnel,
    siren numero_siren not null unique
);

create table _moyen_paiement(
    id serial
        constraint moyen_paiement_pk primary key,
    id_prive int not null
        constraint moyen_paiement_fk_prive references _prive
);

create table _public(
    id int
        constraint public_pk primary key
        constraint public_inherits_profesionnel references _professionnel
);

create table _membre(
    id int
        constraint membre_pk primary key
        constraint membre_inherits_compte references _compte,
    pseudo pseudonyme not null unique
);

create table _facture(
    id serial
        constraint facture_pk primary key,
    date_facture timestamp not null,
    remise_ht decimal not null,
    montant_deja_verse decimal not null,
    id_offre int not null
        constraint facture_fk_offre references _offre
);

create table _prestation(
    id serial
        constraint prestation_pk primary key,
    description ligne not null,
    prix_unitaire_ht decimal not null,
    tva decimal not null,
    qte int not null,
    id_facture int not null
        constraint prestation_fk_facture references _facture
);

create table _tarif(
    nom_tarif ligne not null,
    id_offre int
        constraint tarif_fk_offre references _offre,
    constraint tarif_pk primary key (nom_tarif, id_offre),

    prix decimal not null
);

create table _option(
    nom nom_option
        constraint option_pk primary key,
    prix decimal not null
);

create table _avis(
    id int
        constraint avis_pk primary key
        constraint avis_inherits_signalable references _signalable,
    commentaire paragraphe not null,
    note int not null check (1 <= note and note <= 5),
    moment_publication timestamp not null default now(),
    date_experience date not null,
    lu boolean not null default false,
    blackliste boolean not null default false,

    id_membre_auteur int -- devient null (anonyme) quand l'auteur est supprimé
        constraint avis_fk_membre_auteur references _membre on delete set null,
    id_offre int not null
        constraint avis_fk_offre references _offre,
    constraint avis_uniq_auteur_offre unique (id_membre_auteur, id_offre) -- un seul avis par couple (membre_auteur, offre)
);

create table _avis_resto(
    id int
        constraint avis_resto_pk primary key
        constraint avis_resto_inherits_avis references _avis,
    id_restaurant int not null
        constraint avis_resto_fk_restaurant references _restaurant,
    note_cuisine int not null check (1 <= note_cuisine and note_cuisine <= 5),
    note_service int not null check (1 <= note_service and note_service <= 5),
    note_ambiance int not null check (1 <= note_ambiance and note_ambiance <= 5),
    note_qualite_prix int not null check (1 <= note_qualite_prix and note_qualite_prix <= 5)
);

create table _reponse(
    id int
        constraint reponse_pk primary key
        constraint reponse_inherits_signalable references _signalable,
    id_avis int not null unique
        constraint reponse_avis references _avis,
    contenu paragraphe not null
);


-- ASSOCIATIONS

-- Un horaire d'ouverture périodique sur une semaine
-- Ouvert sur toutes les semaines de l'année (vacances non comptabilisées)
create table _horaire_ouverture(
    id_offre int
        constraint horaire_ouverture_fk_offre references _offre,
    jour_de_la_semaine int check (1 <= jour_de_la_semaine and jour_de_la_semaine <= 7),
    heure_debut time,
    heure_fin time check (heure_fin > heure_debut),
    constraint horaire_ouverture_pk primary key (id_offre, jour_de_la_semaine, heure_debut, heure_fin)
);

-- Une période d'ouverture ponctuelle
create table _periode_ouverture(
    id_offre int
        constraint horaire_ouverture_fk_offre references _offre,
    debut timestamp,
    fin timestamp check (fin > debut),
    constraint horaire_pk primary key (id_offre, debut, fin)
);

create table _signalement(
    id_membre int
        constraint signalement_fk_membre references _membre,
    id_signalable int
        constraint signalement_fk_signalable references _signalable,
    constraint signalement_pk primary key (id_membre, id_signalable),

    raison paragraphe not null
);

create table _code_postal(
    code_commune int not null,
    numero_departement num_departement not null,
    constraint adresse_fk_commune foreign key (code_commune, numero_departement) references _commune,

    code_postal char(5) not null,
    constraint code_postal_pk primary key (code_commune, numero_departement, code_postal)
);

create table _langue_visite(
    code_langue char(2)
        constraint langue_visite_fk_langue references _langue,
    id_visite int
        constraint langue_visite_fk_visite references _visite,
    constraint langue_visite_pk primary key (code_langue, id_visite)
);

create table _gallerie(
    id_offre int
        constraint gallerie_fk_offre references _offre,
    id_image int
        constraint gallerie_fk_image references _image,
    constraint gallerie_pk primary key (id_offre, id_image)
);

create table _changement_etat(
    id_offre int
        constraint changement_etat_fk_offre references _offre,
    date_changement timestamp default now(),
    constraint changement_etat_pk primary key (id_offre, date_changement)
);

create table _souscription_option(
    id_offre int
        constraint souscription_option_pk primary key
        constraint souscription_option_fk_offre references _offre,
    nom_option nom_option not null
        constraint souscription_option_fk_option references _option
);

create table _juge(
    id_identite int
        constraint approuve_fk_identite references _identite,
    id_avis int
        constraint approuve_fk_avis references _avis,
    constraint approuve_pk primary key (id_identite, id_avis),

    aime boolean not null
);

create table _tags_restaurant(
    id_restaurant int
        constraint tag_restaurant_fk_restaurant references _restaurant,
    tag ligne not null,
    constraint tag_restaurant_pk primary key (tag, id_restaurant)
);

commit;