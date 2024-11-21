set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie, localite)
        values
            ('22', 070, 'Rte de Tréguier', 'ZAC le Lion de Saint-Marc')
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                id_professionnel,
                id_image_principale,
                libelle_abonnement,
                url_site_web,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                1,
                17,
                'gratuit',
                'https://www.tregor-bowling.com',
                'Bowling L''éclipse',
                'Un bowling, laser game et bar avec jeux',
                'Toutes sortes de choses bla bla bla bla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla blabla bla bla',
                '0:20:',
                'De nombreuses choses'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'famille')
    ),
    s2 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
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
                '2024-11-21', -- Date d'experience
                'Les employés ont enquillé les erreurs ce qui nous à un peu foutu les boules ' -- Commentaire
            )
    ),
    s3 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention de nomamge s1, s2, s3...
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
                id_membre ('Maëlan'), -- Récupère l'ID de membre à partir du pseudo
                4.5, -- Note sur 5
                'amis', -- Contexte : affaires, couple, solo, famille, amis
                '2024-11-21', -- Date d'experience
                'Laser games super sympa simplement dommage que le laser game ne sois pas détaillé sur le site' -- Commentaire
            )
    )
insert into
    _gallerie (id_offre, id_image)
values
    ((table id_offre), 16),
    ((table id_offre), 18),
    ((table id_offre), 19),
    ((table id_offre), 20),
    ((table id_offre), 21),
    ((table id_offre), 22);
