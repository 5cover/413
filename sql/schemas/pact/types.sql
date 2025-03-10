set schema 'pact';

-- TYPES

create function time_subtype_diff(x time, y time) returns float8 as $$
    select extract(epoch from (x - y))
$$ language sql strict immutable;

create type timerange as range (
    subtype = time,
    subtype_diff = time_subtype_diff
);

create type categorie_offre as enum (
    'restaurant',
    'activité',
    'visite',
    'spectacle',
    'parc d''attractions'
);

create type secteur as enum (
    'public',
    'privé'
);

-- DOMAINES

create domain num_departement as char(3);

create domain iso639_1 as char(2);

create domain nom_option char(10);

create domain paragraphe as varchar;
comment on domain paragraphe is 'Un paragraphe de texte libre';

create domain ligne as varchar check (value not like E'%\n%');
comment on domain ligne is 'Une ligne de texte libre';

create domain numero_telephone as char(10) check (value ~ '^[0-9]+$');
comment on domain numero_telephone is
'9 chiffres
- on exclut le zéro initial
- indicatif +33 implicite';

create domain numero_siren as char(9) check (value ~ '^[0-9]+$');
comment on domain numero_siren is '9 chiffres';

create domain mot as varchar(255) check (value ~ '^[[:graph:] ]+$');
comment on domain mot is 
'Texte:
- 255 car. max
- non vide
- caractères visibles uniquement
- espaces autorisés tant qu''il sont entourés de caractères visibles';

create domain mot_minuscule as mot check (value !~ '[[:upper:]]');
comment on domain mot_minuscule is 'Un mot ne contenant pas de majuscules';

create domain pseudonyme as mot check (value !~ '@');
comment on domain pseudonyme is
'Un mot ne contenant pas d''arobase "@" pour éviter la confusion avec une adresse e-mail';

create domain adresse_email as varchar(319) check (value ~ '^(?:[a-z0-9!#$%&''*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&''*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$');
comment on domain adresse_email is 'Adresse e-mail (regex de https://emailregex.com)';