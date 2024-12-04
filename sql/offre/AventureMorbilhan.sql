set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('56', 101, 'Kervallon')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                url_site_web,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-12-03 10:45:00',
                8,
                1,
                'standard',
                'https://www.parcdumorbihan.bzh/',
                'Parc d''Aventures du Morbihan',
                'Le Parc d''Aventures du Morbihan offre une variété d''activités pour toute la famille, allant des parcours en forêt aux espaces aquatiques. Venez profiter d''une journée riche en sensations dans un cadre naturel exceptionnel.',
                'Situé à proximité des forêts du Morbihan, le parc propose des tyroliennes géantes, des parcours aquatiques, des jeux de piste, et même un labyrinthe en plein air. Idéal pour les sorties en famille ou entre amis, avec une restauration locale à disposition.',
                '2:30:00',
                8,
                'Accès à tous les parcours, location de matériel incluse.'
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('jdupont'), 5, 'famille', '2034-12-02', 'Activités variées et cadre magnifique.')
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nature'),
    ((table id_offre), 'famille'),
    ((table id_offre), 'aventure');
