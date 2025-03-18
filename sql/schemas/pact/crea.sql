-- NE PAS FORMATER
-- j'ai passé trop de temps à faire ça manuellement
--                                          Raphaël

-- Info:
-- Ajouter "not null" aux attributs clés étrangères ne faisant pas partie de la clé primaire. La contrainte "references" n'implique pas "not null". La contrainte "primary key" implique "not null unique"

-- CLASSES

set schema 'pact';

create table _departement (
    numero num_departement
        constraint departement_pk primary key,
    nom ligne not null unique
);

create table _commune (
    code int,
    numero_departement num_departement
        constraint commune_fk_departement references _departement,
    constraint commune_pk primary key (code, numero_departement),
        
    nom ligne not null
);

create table _adresse (
    id serial
        constraint adresse_pk primary key,

    code_commune int not null,
    numero_departement num_departement not null,
    constraint adresse_fk_commune foreign key (code_commune, numero_departement) references _commune,

    numero_voie int check (numero_voie > 0),
    complement_numero varchar(10) check (complement_numero <> ''),
    constraint adresse_check_numero_voie_complement_numero check (numero_voie is not null or complement_numero is null),

    nom_voie ligne check (nom_voie <> ''),
    localite ligne check (localite <> ''),
    precision_int ligne check (precision_int <> ''),
    precision_ext ligne check (precision_ext <> ''),

    lat decimal,
    long decimal,
    check ((lat is null) = (long is null))
);
comment on constraint adresse_check_numero_voie_complement_numero on _adresse is
'numero_voie is null => complement_numero is null';

create table _abonnement (
    libelle mot_minuscule
        constraint abonnement_pk primary key,
    prix_journalier decimal not null,
    description ligne not null
);

create table _image (
    id serial
        constraint image_pk primary key,
    taille int not null,
    mime_subtype varchar(127) not null,
    legende ligne check (legende <> '')
);
comment on column _image.taille is 'Mime subtype (part after "image/"). Used as a file extension.';

create table _signalable (
    id serial
        constraint signalable_pk primary key
);

create table _compte (
    id int
        constraint compte_pk primary key
        constraint compte_inherits_signalable references _signalable on delete cascade,
    email adresse_email not null unique,
    mdp_hash varchar(255) not null,
    nom ligne not null,
    prenom ligne not null,
    telephone numero_telephone not null,
    adresse text, -- adresse libre facultative
    api_key uuid unique
);

create table _professionnel (
    id int
        constraint professionnel_pk primary key
        constraint professionnel_inherits_compte references _compte on delete cascade,
    denomination ligne not null
);

create table _offre (
    id int
        constraint offre_pk primary key
        constraint offre_inherits_signalable references _signalable on delete cascade,
    id_adresse int not null
        constraint offre_fk_adresse references _adresse,
    id_image_principale int not null
        constraint offre_fk_image references _image,
    id_professionnel int not null
        constraint offre_fk_professionnel references _professionnel,
    libelle_abonnement mot_minuscule not null
        constraint offre_fk_abonnement references _abonnement,
    titre ligne not null,
    resume ligne not null,
    description_detaillee paragraphe not null,
    modifiee_le timestamp not null,
    url_site_web varchar(2047),
    periodes_ouverture tsmultirange not null
);

create table _restaurant (
    id int
        constraint restaurant_pk primary key
        constraint restaurant_inherits_offre references _offre on delete cascade,
    carte paragraphe not null,
    richesse int not null check (richesse between 1 and 3),

    sert_petit_dejeuner bool not null,
    sert_brunch bool not null,
    sert_dejeuner bool not null,
    sert_diner bool not null,
    sert_boissons bool not null,
    check (sert_petit_dejeuner or sert_brunch or sert_dejeuner or sert_diner or sert_boissons)
);

create table _activite (
    id int
        constraint activite_pk primary key
        constraint activite_inherits_offre references _offre on delete cascade,
    indication_duree interval not null,
    age_requis int check (age_requis > 0),
    prestations_incluses paragraphe not null,
    prestations_non_incluses paragraphe check (prestations_non_incluses <> '')
);

create table _visite (
    id int
        constraint visite_pk primary key
        constraint visite_inherits_offre references _offre on delete cascade,
    indication_duree interval not null
);

create table _langue (
    code iso639_1
        constraint langue_pk primary key,
    libelle ligne not null
);

create table _spectacle (
    id int
        constraint spectacle_pk primary key
        constraint spectacle_inherits_offre references _offre on delete cascade,
    indication_duree interval not null,
    capacite_accueil int not null
); 

create table _parc_attractions (
    id int
        constraint parc_attractions_pk primary key
        constraint parc_attractions_inherits_offre references _offre on delete cascade,
    id_image_plan int not null
        constraint parc_attractions_fk_image_plan_parc references _image,
    nb_attractions int not null check (nb_attractions >= 0),
    age_requis int check (age_requis > 0)
);

create table _prive (
    id int
        constraint prive_pk primary key
        constraint prive_inherits_professionnel references _professionnel on delete cascade,
    siren numero_siren not null unique
);

create table _moyen_paiement (
    id serial
        constraint moyen_paiement_pk primary key,
    id_prive int not null
        constraint moyen_paiement_fk_prive references _prive on delete cascade
);

create table _public (
    id int
        constraint public_pk primary key
        constraint public_inherits_professionnel references _professionnel on delete cascade
);

create table _membre (
    id int
        constraint membre_pk primary key
        constraint membre_inherits_compte references _compte on delete cascade,
    pseudo pseudonyme not null unique,
    full_block_expires_at timestamp
);

create table _tarif (
    nom ligne,
    id_offre int
        constraint tarif_fk_offre references _offre on delete cascade,
    constraint tarif_pk primary key (nom, id_offre),

    montant decimal not null check (montant >= 0)
);

create table _option (
    nom nom_option
        constraint option_pk primary key,
    prix_hebdomadaire decimal not null
);

create table _avis (
    id int
        constraint avis_pk primary key
        constraint avis_inherits_signalable references _signalable on delete cascade,
    commentaire paragraphe not null,
    note int not null check (note between 1 and 5),
    publie_le timestamp not null default localtimestamp,
    date_experience date not null,
    contexte mot_minuscule not null,
    lu bool not null default false,
    likes int not null default 0,
    dislikes int not null default 0,

    id_membre_auteur int
        constraint avis_fk_membre_auteur references _membre on delete set null,
    id_offre int not null
        constraint avis_fk_offre references _offre on delete cascade,
    constraint avis_uniq_auteur_offre unique (id_membre_auteur, id_offre)
);
comment on column _avis.id_membre_auteur is 'Devient null (anonyme) quand l''auteur est supprimé';
comment on constraint avis_uniq_auteur_offre on _avis is 'Un seul avis par couple (membre_auteur, offre). Ceci est une clé candidate et non pas une clé primaire car id_membre_auteur peut être null';

create table _avis_restaurant (
    id int
        constraint avis_restaurant_pk primary key
        constraint avis_restaurant_inherits_avis references _avis on delete cascade,
    note_cuisine int not null check (note_cuisine between 1 and 5),
    note_service int not null check (note_service between 1 and 5),
    note_ambiance int not null check (note_ambiance between 1 and 5),
    note_qualite_prix int not null check (note_qualite_prix between 1 and 5)
);

create table _blacklist (
    id int
        constraint blacklist_pk primary key
        constraint blacklist_inherits_avis references _avis on delete cascade,
    date_blacklist timestamp default localtimestamp,
    fin_blacklist timestamp 
);

create table _reponse (
    id int
        constraint reponse_pk primary key
        constraint reponse_inherits_signalable references _signalable on delete cascade,
    id_avis int not null unique
        constraint reponse_avis references _avis on delete cascade,
    contenu paragraphe not null
);

create table _ouverture_hebdomadaire (
    id_offre int
        constraint ouverture_hebdomadaire_fk_offre references _offre on delete cascade,
    dow int check (dow between 0 and 6),
    constraint ouverture_hebdomadaire_pk primary key (id_offre, dow),

    horaires timemultirange not null check (not isempty(horaires))
);
comment on table _ouverture_hebdomadaire is
'Des horaires d''ouverture hebdonaraires
Ouvert sur toutes les semaines de l''année.
Vacances, jours fériés et ponts non comptabilisées.';
comment on column _ouverture_hebdomadaire.dow is 'The day of the week as Sunday (0) to Saturday (6)';

create table _code_postal (
    code_commune int not null,
    numero_departement num_departement not null,
    constraint adresse_fk_commune foreign key (code_commune, numero_departement) references _commune,

    code_postal char(5) not null,
    constraint code_postal_pk primary key (code_commune, numero_departement, code_postal)
);

create table _changement_etat (
    id_offre int
        constraint changement_etat_fk_offre references _offre on delete cascade,
    fait_le timestamp default localtimestamp,
    constraint changement_etat_pk primary key (id_offre, fait_le)
);

create table _tags (
    id_offre int
        constraint tags_fk_offre references _offre on delete cascade,
    tag mot_minuscule,
    constraint tags_pk primary key (id_offre, tag)
);

-- ASSOCIATIONS BINAIRES

create table _signalement (
    id_compte int
        constraint signalement_fk_compte references _compte on delete cascade,
    id_signalable int
        constraint signalement_fk_signalable references _signalable on delete cascade,
    constraint signalement_pk primary key (id_compte, id_signalable),

    raison paragraphe not null
);

create table _langue_visite (
    code_langue char(2)
        constraint langue_visite_fk_langue references _langue,
    id_visite int
        constraint langue_visite_fk_visite references _visite on delete cascade,
    constraint langue_visite_pk primary key (code_langue, id_visite)
);

create table _galerie (
    id_offre int
        constraint galerie_fk_offre references _offre on delete cascade,
    id_image int
        constraint galerie_fk_image references _image,
    constraint galerie_pk primary key (id_offre, id_image)
);

create table _souscription_option (
    id_offre int
        constraint souscription_option_fk_offre references _offre on delete cascade,
    nom_option nom_option not null
        constraint souscription_option_fk_option references _option,
    lancee_le timestamp not null,
    -- todo: this key could be improved - invalid state is still representable
    constraint souscription_option_pk primary key (id_offre, nom_option, lancee_le),
    nb_semaines int not null check (nb_semaines between 1 and 4),
    actif bool not null default true
);

