set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 136, 'Rue du Mène')
        returning
            id
    ),
    id_offre as (
        insert into
            parc_attractions (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_image_plan,
                id_professionnel,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                url_site_web,
                age_requis,
                nb_attractions
            )
        values
            (
                (table id_adresse),
                '2024-04-29 05:59:07',
                3,
                46,
                1,
                'standard',
                'Parc de loisirs Aquarev',
                'Découvrez le parc de loisirs Aquarev, véritable poumon vert de Loudéac.',
                '## Un « rêve » aménagé en pleine nature

30 hectares aménagés autour d''un étang pour le loisir et la détente : une plaine de jeux, un labyrinthe, une bambouseraie, des pontons de pêche, des aires de pique-nique, un parcours sportif, un terrain multi-sport, le tout dans une ambiance détendue, « zen »… Pour tous et en accès libre.

Autour de son « tipi » emblématique, le site présente une nouvelle image d''un parc de loisirs, de promenades, de vie au cœur d''un espace vert rustique qui permet à chacun de trouver des lieux différents, pour se rencontrer, prendre du bon temps et du plaisir.

Le site accueille régulièrement de nombreux événements municipaux et manifestations associatives. Chaque mercredi d''été, venez par exemple participer aux « Ateliers d''Aquarev » sous le tipi, des animations gratuite pour toute la famille proposées par des commerçant ou intervenants du territoire autour des plantes et de la nature.

### Une ambiance propice à la détente

Une partie du mobilier est en bois : kiosques, pontons de pêche, ponceaux, tables de pique-nique… pour s''intégrer parfaitement au cadre naturel du parc.  Le tout dans une ambiance « zen ».

Dans cet ensemble, on retrouve : des aires de pique-nique avec barbecues en accès libre, des parcours de promenade diversifiés et ombragés, du mobilier de détente…

## Des espaces ludiques

### La plaine de jeux

La plaine de jeux accueille les enfants de 3 à 12 ans : jeux de cordes, balançoire royale, parcours aventure (avec agrès), tour pyramide avec 2 toboggans, combiné multi-jeux…

### Le village des menhirs

Une aire de jeux pour les tout-petits avec balançoire et cabanons.

### La toile d''araignée

Une grande toile de cordes qui monte jusqu''à 6 m.

### Le parcours aquatique

Jeux aquatiques pour découvrir se rafraîchir et découvrir en s''amusant.

### L''espace des tout-petits à proximité du tipi

Une aire de jeux pour les plus petits avec toboggan, cabane, balançoires…
 
## Des aménagements pour tous

### Site adapté aux Personnes à Mobilité Réduite.

Tout a été fait pour que le site soit accessible aux Personnes à Mobilité Réduite (PMR). La pente des cheminements est toujours inférieure à 5 %. Trois pontons de pêche sont spécialement conçus pour les PMR, et positionnés en concertation avec la Fédération départementale de pêche et son représentant local.

Pour les bâtiments et le camping, les plans ont reçu un avis favorable de la commission d''accessibilité.

### Les jardins à thèmes

Une placette, des jeux de boules, un kiosque à thé, promenades et pontons sur pilotis

Un labyrinthe bambouseraie

Des cheminements étroits, en surface minérale, serpentant entre les différentes essences… dans un esprit «japonais».

### Des équipements sportifs

Un parcours sportif accessible à tous ponctue votre promenade autour des étangs.

Pour les plus sportifs le parc propose également des terrains de tennis et un terrain multisport en accès libre.

Pour les clubs et les licenciés : un étang de pêche et de nombreux pontons aménages, un pas de tir à l''arc, un embarquement voile…

### Espace Ado

Avec un terrain multi-sports, tennis…

## Infos pratiques

Site en accès libre jusqu''à 23h00, placé sous vidéo-surveillance.
2 parkings voitures et 1 parking bus.
Toilettes et douche.
Aire camping-car.
Camping et snack sur le site.',
                'https://www.ville-loudeac.fr/listes/parc-aquarev',
                8,
                19
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'famille'),
            ((table id_offre), 'plein air'),
            ((table id_offre), 'sport')
    ),
    s2 as (
        -- Pour le mois de novembre, cette offre a été en ligne pendant 0 years 0 mons 13 days 63 hours 28 mins 57.0 secs
        -- select (timestamp '2024-11-05 16:41:37' - '2024-11-03 22:05:11') + (timestamp '2024-11-07 08:56:34' - '2024-11-06 12:08:05') + (timestamp '2024-11-15 00:59:47' - '2024-11-10 03:10:31') + (timestamp '2024-11-27 03:10:32' - '2024-11-19 00:55:46');
        insert into
            _changement_etat (id_offre, fait_le)
        values
            ((table id_offre), '2024-11-03 22:05:11'), -- mise en ligne
            ((table id_offre), '2024-11-05 16:41:37'), -- mise hors ligne
            ((table id_offre), '2024-11-06 12:08:05'), -- mise en ligne
            ((table id_offre), '2024-11-07 08:56:34'), -- mise hors ligne
            ((table id_offre), '2024-11-10 03:10:31'), -- mise en ligne
            ((table id_offre), '2024-11-15 00:59:47'), -- mise hors ligne
            ((table id_offre), '2024-11-19 00:55:46'), -- mise en ligne
            ((table id_offre), '2024-11-27 03:10:32') -- mise hors ligne
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('Snoozy'),
                5,
                'famille',
                '2024-11-28',
                'Personnel très accueillant.Parc adapté a tous public'
            )
    ),
    s4 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
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
                timemultirange (timerange ('15:', '15:30'), timerange ('15:50', '23:05:59'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('10:', '19:'))
        )
    ),
    (
        (table id_offre),
        3,
        (
            select
                timemultirange (timerange ('10:', '15:'))
        )
    ),
    (
        (table id_offre),
        4,
        (
            select
                timemultirange (timerange ('11:', '13:'))
        )
    ),
    (
        (table id_offre),
        5,
        (
            select
                timemultirange (timerange ('18:', '19:'))
        )
    ),
    (
        (table id_offre),
        6,
        (
            select
                timemultirange (timerange ('14:', '18:'))
        )
    );