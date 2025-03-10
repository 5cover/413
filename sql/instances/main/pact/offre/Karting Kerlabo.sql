set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 045, 'Axe Chatelaudren-Quintin')
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
                age_requis,
                titre,
                resume,
                description_detaillee,
                url_site_web,
                indication_duree,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-03-13 19:18:51',
                27,
                1,
                'premium',
                7,
                'Karting Kerlabo',
                'Au cœur des Côtes-d''Armor et à mi-chemin entre St-Brieuc et Guingamp, le site de Kerlabo est un incontournable du karting en Bretagne. Sur un site dédié au loisir et au sport automobile, le circuit de Kerlabo vous offre un tracé unique et vallonné sur plus de 800 mètres!',
                'Ouvert samedi et dimanche (selon météo : horaires à vérifier sur google)+ les apres midi des vacances scolaires du 21 au 31 décembre. 

Autres jours et horaires possibles sur demande pour groupes de 8 pers. mini.

Nos horaires sont mis à jour régulièrement sur google en fonction des réservations de groupes. Bien les vérifier avant de se déplacer!

Vous pouvez téléphoner avant de vous déplacer pour connaitre l''affluence mais nous ne faisons PAS DE RESERVATION (sauf groupes + de 8 personnes pour des animations type challenge ou endurance)

Nous vous demandons de privilégier vos équipements personnels (casque intégral ou cagoule) si vous en avez + prévoir blouson et GANTS en hiver

Une CAGOULE en coton réutilisable et personnelle sera OBLIGATOIRE sous nos casques prêtés. Les cagoules sont en vente sur place au tarif de 3€',
                'https://kerlabo-kart.com',
                '0:20:',
                'Nous vous proposons un circuit de karting'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'plein air')
    ),
    s2 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('dieu_des_frites'),
                2,
                'affaires',
                '2024-06-15',
                'Karting bridés trop lents'
            )
    ),
    s3 as (
        insert into
            tarif (nom, id_offre, montant)
        values
            ('adulte', (table id_offre), 20),
            ('etudiant', (table id_offre), 15)
    )
insert into
    _galerie (id_offre, id_image)
values
    ((table id_offre), 24),
    ((table id_offre), 25),
    ((table id_offre), 28);