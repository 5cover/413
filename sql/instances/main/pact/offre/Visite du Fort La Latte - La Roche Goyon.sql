set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 201, 'Cap Fréhel')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
                '2024-03-24 17:03:47',
                26,
                1,
                'standard',
                '3:00:',
                'Visite du Château de La Roche Goyon, Fort La Latte à Plévenon',
                'Découvrez l''histoire fascinante et les paysages époustouflants du Château de La Roche Goyon, également connu sous le nom de Fort La Latte, lors de cette visite guidée inoubliable. Situé à Plévenon, en Bretagne, ce château médiéval offre une vue imprenable sur la mer et une plongée dans le passé riche de la région. Cette offre touristique inclut une visite guidée, des démonstrations de tir à l''arbalète, et une dégustation de produits locaux pour une expérience complète et immersive.',
                '**Visite Guidée du Château :**
Commencez votre aventure par une visite guidée du Château de La Roche Goyon. Votre guide, expert en histoire locale, vous fera découvrir les différentes parties du château, y compris les tours, les remparts, et les salles historiques. Vous apprendrez comment ce fort a joué un rôle crucial dans la défense de la côte bretonne au fil des siècles. La visite inclut des anecdotes fascinantes sur les seigneurs qui ont habité le château et les batailles qui s''y sont déroulées.

**Démonstrations de Tir à l''Arbalète :**
Pour une expérience encore plus immersive, participez à une démonstration de tir à l''arbalète. Nos experts en armes médiévales vous montreront comment ces armes étaient utilisées pour défendre le château. Vous aurez même l''occasion de tirer quelques flèches vous-même, sous la supervision de nos instructeurs qualifiés. Cette activité est idéale pour les amateurs d''histoire et les passionnés de culture médiévale.

**Dégustation de Produits Locaux :**
Après votre visite et vos activités, profitez d''une dégustation de produits locaux. Savourez des spécialités bretonnes telles que des crêpes, du cidre, et des fromages régionaux. Cette dégustation se déroule dans un cadre pittoresque, offrant une vue magnifique sur la mer et les falaises environnantes. C''est l''occasion parfaite pour découvrir la richesse culinaire de la Bretagne tout en profitant de la beauté naturelle de la région.

**Informations Pratiques :**
- **Durée de la visite :** Environ 3 heures
- **Horaires :** Départs à 10h et 14h, tous les jours sauf le lundi
- **Tarif :** 25€ par adulte, 15€ par enfant (de 6 à 12 ans), gratuit pour les enfants de moins de 6 ans
- **Inclus :** Visite guidée, démonstration de tir à l''arbalète, dégustation de produits locaux
- **Réservation :** Obligatoire, places limitées

Ne manquez pas cette opportunité unique de découvrir l''un des châteaux les plus emblématiques de Bretagne. Réservez dès maintenant pour une expérience inoubliable au Château de La Roche Goyon, Fort La Latte à Plévenon.',
                'https://www.lefortlalatte.com'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'patrimoine'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'culturel')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('ltorvalds'), 2, 'solo', '2024-04-04', 'Lieux peu salubre mais cadre magnofique,dommage!')
    ),
    s4 as (
        insert into
            tarif (nom, id_offre, montant)
        values
            ('Adulte', (table id_offre), 5),
            ('Enfant', (table id_offre), 1.1)
    ),
    s5 as (
        insert into _changement_etat (id_offre, fait_le)
        values
        ((table id_offre), '2024-11-15 12:00:00') -- mise en ligne
    )
insert into
    _ouverture_hebdomadaire (id_offre, dow, horaires)
values
    (
        (table id_offre),
        1,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        3,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        4,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        5,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        6,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    );