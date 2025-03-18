set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite,lat,long)
        values
            ('22', 203, 'Route de Camet',48.6338376,-3.0731569)
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
                url_site_web,
                periodes_ouverture
            )
        values
            (
                (table id_adresse),
                '2024-07-31 19:35:58',
                42,
                2,
                'gratuit',
                '2:00:',
                'Balade poétique à Camet',
                'Après, Les oiseaux, les fleurs sauvages, les 4 saisons, les poètes et la nature, les arbres ; cette année met à l''honneur Le silence de l''eau. En nous baladant tout autour de l''étang, nous allons rencontrer poèmes et chansons, d''Eugène Guillevic à Anne Sylvestre, de Guy Béart à Paul Eluard, situé à un endroit qui résonne avec le poème.',
                'Cette édition sur le thème de l''eau a inspiré de nombreuses animations :

- Un atelier d''aquarelle à la médiathèque, avec l''artiste Gérard Hubert
- Une séance de fabrication de bateaux, poissons, grenouilles, baleines, cygnes en origamis
- Des aquarelles réalisées par les résident·es exposées à la médiathèque, des lectures et chansons à l''EHPAD Louis Morel de Ploeuc
- Des lectures, la réalisation de fresques et l''écriture de poèmes à l''école Le Petit Prince, fresques exposées dans la médiathèque et sous les préaux extérieurs',
                'https://mediathequesdelabaie.fr/au-programme/rendez-vous2/2563-balade-poetique-a-camet',
                (
                    select
                        tsmultirange (
                            tsrange ('2024-06-14', '2024-09-27'),
                            tsrange ('2025-06-14', '2025-09-27'),
                            tsrange ('2026-06-14', '2026-09-27')
                        )
                )
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
    s2 as ( -- Cette CTE a besoin des valeurs des précédentes, mais elle ne retourne pas de valeur. On doit quand même la nommer, on utilsera la convention: s1, s2, s3...
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'nature')
    ),
    s3 as (
        insert into
            _changement_etat (id_offre, fait_le)
        values
            ((table id_offre), '2025-01-03 11:28:53') -- mise en ligne
    )
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
        5, -- Note sur 5
        'couple', -- Contexte : affaires, couple, solo, famille, amis
        '2024-11-13', -- Date d'experience
        'Sah big banger g kiffé' -- Commentaire
    ),
    (
        (table id_offre),
        id_membre ('j0hn'), -- Récupère l'ID de membre à partir du pseudo
        4, -- Note sur 5
        'couple', -- Contexte : affaires, couple, solo, famille, amis
        '2024-08-08', -- Date d'experience
        'Séjour féérique en couple un vrai moment de bonheur' -- Commentaire
    );