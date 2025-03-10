set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 070, 14, 'Rue de l''Eglise')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                modifiee_le,
                id_professionnel,
                id_image_principale,
                libelle_abonnement,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-03-17 02:52:43',
                1,
                5,
                'standard',
                'Chasse aux grenouilles dans le Lac du Gourgal',
                'Chasse aux grenouilles dans le Lac du Gourgal résumé',
                'Chasse aux grenouilles dans le Lac du Gourgal description',
                '3:00:',
                'Chasse aux grenouilles dans le Lac du Gourgal prestations incluses'
            )
        returning
            id
    ),
    s1 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
        insert into
            avis ( --
                id_offre,
                id_membre_auteur,
                note,
                contexte,
                date_experience,
                commentaire
            )
        values
            ( --
                (table id_offre),
                id_membre ('5cover'), -- Récupère l'ID de membre à partir du pseudo
                1, -- Note sur 5
                'amis', -- Contexte : affaires, couple, solo, famille, amis
                '2024-11-07', -- Date d'experience
                'Franchement décevant!' -- Commentaire
            )
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nature'),
    ((table id_offre), 'plein air'),
    ((table id_offre), 'aventure');