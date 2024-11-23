set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 203, 'Route de Camet')
        returning
            id
    ),
    id_offre as (
        insert into
            visite (
                id_adresse,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                indication_duree,
                titre,
                resume,
                description_detaillee,
                url_site_web
            )
        values
            (
                (table id_adresse),
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
                'https://mediathequesdelabaie.fr/au-programme/rendez-vous2/2563-balade-poetique-a-camet'
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
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'nature')
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
                '2023-08-08', -- Date d'experience
                'Séjour féérique en couple un vrai moment de bonheur' -- Commentaire
            )
    )
insert into
    _periode_ouverture (id_offre, debut_le, fin_le)
values
    ((table id_offre), '2024-06-14T12:00:00.000Z', '2024-09-27T18:00:00.000Z'),
    ((table id_offre), '2025-06-14T12:00:00.000Z', '2025-09-27T18:00:00.000Z'),
    ((table id_offre), '2026-06-14T12:00:00.000Z', '2026-09-27T18:00:00.000Z');