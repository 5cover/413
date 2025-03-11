set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie,lat,long)
        values
            ('22', 155, 'Les Tronchées',48.610553, -3.040944)
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
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-06-21 11:37:28',
                2,
                41,
                'gratuit',
                'https://www.aventure-nature.com/accrobranche',
                'Accrobranche au parc aventure Indian Forest',
                'le parc aventure Indian Forest - Parcours d''accrobranche dans les Côtes-d''Armor (22)',
                'Envie de pratiquer un sport ludique en pleine nature ? Nous vous proposons de découvrir l''accrobranche, un sport original adapté aux petits et grands. L''équipe du parc dispose d''un diplôme d''encadrement OPAH. Ceci pour vous assurer d''agréables moments en toute sécurité. Ils sont à votre service afin de vous faire découvrir cette discipline forte en sensations et en fous rires.

Notre activité est contrôlée par un organisme spécialisé dans la vérification des parcours acrobatiques en hauteur. De plus, des experts forestiers interviennent à chaque saison pour la préservation forestière.

Bienvenue dans notre parc aventure si vous êtes dans les Côtes-d''Armor notamment à Saint-Brieuc, Dinan, Guingamp, Lanvollon, Lannion, Pléneuf-Val-André, Morieux, Lamballe ou Paimpol.',
                '1:10:',
                11,
                'Nous vous proposons un parcours d''accrobranche'
            )
        returning
            id
    ),
    s1 as (
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
                3, -- Note sur 5
                'solo', -- Contexte : affaires, couple, solo, famille, amis
                '2024-11-15', -- Date d'experience
                'Ouais bof... J''ai pas trop aimé perso' -- Commentaire
            )
    ),
    s2 as (
        insert into _changement_etat (id_offre, fait_le)
        values
        ((table id_offre), '2024-11-15 12:00:00') -- mise en ligne
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nature'),
    ((table id_offre), 'plein air'),
    ((table id_offre), 'aventure');