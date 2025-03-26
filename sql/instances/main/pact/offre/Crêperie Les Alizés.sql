set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie,lat,long)
        values
            ('22', 162, 14, 'Rue des Huit Patriotes',48.77991,-3.0490349)
        returning
            id
    ),
    id_offre as (
        insert into
            restaurant (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                carte,
                richesse,
                sert_dejeuner,
                sert_diner
            )
        values
            (
                (table id_adresse),
                '2024-06-11 22:44:47',
                15,
                1,
                'standard',
                'Crêperie Les Alizés',
                'La Crêperie Les Alizés est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats.',
                'La Crêperie Les Alizés est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant',
                'La carte? Allez voir au restaurant, on vous en donnera une',
                2,
                true,
                true
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis_restaurant ( --
                id_restaurant,
                id_membre_auteur,
                note,
                contexte,
                date_experience,
                commentaire,
                note_cuisine,
                note_service,
                note_ambiance,
                note_qualite_prix
            )
        values
            ( --
                (table id_offre),
                id_membre ('Snoozy'), -- Récupère l'ID de membre à partir du pseudo
                1, -- Note sur 5
                'amis', -- Contexte : affaires, couple, solo, famille, amis
                '2024-07-11', -- Date d'experience
                'Employés peu poli avec la clientelle multiple ', -- Commentaire
                3,
                3,
                3,
                1
            )
    ),
    s2 as (
        insert into
            _souscription_option (id_offre, nom_option, lancee_le, nb_semaines)
        values
            ((table id_offre), 'En Relief', localtimestamp, 4)
    ),
    s3 as (
        insert into
            _changement_etat (id_offre, fait_le)
        values
            ((table id_offre), '2025-02-10 01:14:53') -- mise en ligne
    ),
    s4 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'crêperie'),
            ((table id_offre), 'française')
    )
insert into
    _ouverture_hebdomadaire (id_offre, dow, horaires)
values
    (
        (table id_offre),
        1,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('13:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        3,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        4,
        (
            select
                timemultirange (timerange ('13:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        5,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        6,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    );