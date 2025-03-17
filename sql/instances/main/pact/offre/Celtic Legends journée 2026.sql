set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite, precision_ext,lat,long)
        values
            ('22', 360, 'Espace Brezillet', 'PARC EXPO BREZILLET',48.4992,-2.766855)
        returning
            id
    ),
    id_offre as (
        insert into
            spectacle (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                capacite_accueil,
                titre,
                resume,
                description_detaillee,
                url_site_web,
                periodes_ouverture
            )
        values
            (
                (table id_adresse),
                '2024-02-29 09:22:01',
                12,
                1,
                'premium',
                '2:00:',
                1000,
                'Celtic Legends - Tournée 2026',
                'Celtic Legends est un spectacle de musiques et de danses irlandaises qui s''est produit sur de nombreuses scènes à travers le monde depuis sa création, attirant près de 3 millions de spectateurs.',
                'Celtic Legends revient en 2026 avec une nouvelle version du spectacle. Créé à Galway, au Coeur du Connemara, Celtic Legends est un condensé de la culture traditionnelle Irlandaise recréant sur scène l''ambiance électrique d''une soirée dans un pub traditionnel. Venez partager durant 2 heures ce voyage au coeur de l''Irlande soutenu par 5 talentueux musiciens sous la baguette de Sean McCarthy et de 12 extraordinaires danseurs sous la houlette de la créative Jacintha Sharpe.',
                'https://www.celtic-legends.net',
                (
                    select
                        tsmultirange (tsrange ('2026-04-10 20:00:00', '2026-04-11 01:00:00'))
                )
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'musique'),
            ((table id_offre), 'spectacle')
    ),
    s2 as (
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
                4, -- Note sur 5
                'affaires', -- Contexte : affaires, couple, solo, famille, amis
                '2024-07-13', -- Date d'experience
                'Incroyable le jeu son et lumières est parfaitement maitrisé!' -- Commentaire
            )
    ),
    s3 as (
        insert into
            _changement_etat (id_offre, fait_le)
        values
            ((table id_offre), '2025-01-12 11:14:53') -- mise en ligne
    )
insert into
    tarif (nom, id_offre, montant)
values
    ('adulte', (table id_offre), 10),
    ('enfant', (table id_offre), 5);