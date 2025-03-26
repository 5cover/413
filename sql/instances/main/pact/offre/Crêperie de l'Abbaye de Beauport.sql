set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie,lat,long)
        values
            ('22', 162, 32, 'Rue de Beauport',48.7673623,-3.0247916)
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
                '2024-02-15 06:59:05',
                13,
                1,
                'premium',
                'Crêperie de l''Abbaye de Beauport',
                'La Crêperie de l''Abbaye de Beauport est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats.',
                'La Crêperie de l''Abbaye de Beauport est une délicieuse crêperie située à Paimpol. Découvrez nos goutûs plats. Oui, je suis détaillé. Me demandez pas plus de détails. Je ne suis qu''un restaurant',
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
                id_membre ('5cover'), -- Récupère l'ID de membre à partir du pseudo
                5, -- Note sur 5
                'amis', -- Contexte : affaires, couple, solo, famille, amis
                '2024-02-16', -- Date d'experience
                'Super créperie même en été! nous y avons ai fété le 19 eme anniversaire de mon amis Benjamin et le staff nous a offert des crèpes au caramel démicieuses!', -- Commentaire
                4,
                4,
                4,
                4
            )
    ),
    s2 as (
        insert into _changement_etat (id_offre, fait_le)
        values
        ((table id_offre), '2024-11-15 12:00:00') -- mise en ligne
    ),
    s3 as (
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