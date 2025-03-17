set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie, lat, long)
        values
            ('22', 117, 'Les étangs',48.6795806,-3.0067787)
        returning
            id
    ),
    id_offre as (
        insert into
            activite (
                id_adresse,
                modifiee_le,
                id_image_principale,
                id_professionnel,
                libelle_abonnement,
                url_site_web,
                titre,
                resume,
                description_detaillee,
                indication_duree,
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-03-26 08:32:51',
                6,
                2,
                'gratuit',
                'https://www.lanticparcaventure.bzh/',
                'Lantic Parc Aventure',
                'LANTIC PARC AVENTURE propose plusieurs parcours acrobatiques en hauteur, du paint ball, du laser tag, pour toute la famille. Venez passer une journée dans les arbres, en famille, entre amis ou collègues, dans un cadre exceptionnel. Restauration sur place.',
                'Lantic Parc Aventure est situé en pleine nature, au bord des étangs de Lantic, dans un espace de 3,6 hectares. L''accrobranche est une activité de plein-air qui consiste à grimper à la cime des arbres et se balader d''arbre en arbre au travers de différents obstacles, toujours plus funs les uns que les autres (tyroliennes, ponts de singe, rondins tournants, filets, passerelles, …).',
                '1:00:',
                12,
                'Nous vous proposons un parcours d''accrobranche'
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('rstallman'), 4, 'couple', '2024-12-20', 'Excellente expérience.')
    ),
    s2 as (
        insert into
            _changement_etat (id_offre, fait_le)
        values
            ((table id_offre), '2025-03-03 03:03:08') -- mise en ligne
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'famille'),
    ((table id_offre), 'plein air'),
    ((table id_offre), 'aventure');