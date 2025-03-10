set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 162, 14, 'Rue des Huit Patriotes')
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
    s1 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
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
            ((table id_offre), 'En Relief', localtimestamp, 5)
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