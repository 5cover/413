set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 168, 35, 'Rte de Perros')
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
                '2024-08-04 06:18:31',
                1,
                1,
                'premium',
                'La Plage',
                'La Plage est un délicieux restaurant situé à Trestraou. Découvrez nos goutûs plats.',
                'La Plage est un délicieux restaurant situé à Trestraou. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant.',
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
            avis_restaurant (
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
            (
                (table id_offre),
                id_membre ('SamSepi0l'),
                1,
                'affaires',
                '2024-09-25',
                'Service désagréable et désorganisé.',
                2,
                3,
                2,
                1
            ), (
                (table id_offre),
                id_membre ('5cover'),
                5,
                'couple',
                '2024-09-17',
                'Tout simplement magique.',
                5,
                4,
                4,
                5
            )
    ),
    s2 as (
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
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
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
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
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