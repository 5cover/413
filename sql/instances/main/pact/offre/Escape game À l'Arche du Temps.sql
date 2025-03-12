set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie, lat, long)
        values
            ('22', 278, 18, 'Rue de l''Église', 48.507686, -2.767913)
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
                url_site_web,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                age_requis,
                prestations_incluses,
                prestations_non_incluses
            )
        values
            (
                (table id_adresse),
                '2024-02-18 08:42:19',
                2,
                7,
                'gratuit',
                'https://larchedutemps.com',
                'Escape game À l''Arche du Temps',
                'L''Escape Game Arche Du Temps vous permet de disposer d''un cadre atypique, avec une possibilité de privatisation de l''espace d''accueil (80m²) avec salon modulable, et connexion wifi pour un séminaire, une réunion ou autre.',
                '# Escape Game

## Le Gardien des Reliques

1148 – A la veille de la cérémonie d''inauguration de la Cathédrale, Frère Thomas est retrouvé assassiné dans sa chapelle !

Vous et votre équipe aurez une heure pour retrouver le coupable, son mobile et surtout le trésor qu''il protégeait : les prestigieuses reliques de Saint Lazare.

Dans le décor authentique de la Chapelle des VII Dormants, cet Escape Game vous plonge dans le passé millénaire d''Autun. Cherchez les indices, combinez les pistes et résolvez le mystère du Gardien des Reliques !

De 3 à 6 joueurs, en famille, entre collègues ou entre amis, venez relever nos défis ! Au total, le briefing et l''enquête sont prévus pour une durée d''environ 1h30.',
                '1:30:',
                16,
                'Nous vous proposons un escape game avec énigmes',
                'Nous ne vous proposons pas de repas'
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('j0hn'), 1, 'solo', '2024-11-05', 'Propreté douteuse et accueil froid.')
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'famille'),
    ((table id_offre), 'jeu'),
    ((table id_offre), 'aventure');