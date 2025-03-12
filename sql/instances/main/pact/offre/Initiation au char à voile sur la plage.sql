set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie, lat, long)
        values
            ('22', 186, 1, 'Rue de Belvédère',48.538782, -2.719332)
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
                age_requis,
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-03-29 21:32:54',
                2,
                10,
                'gratuit',
                'Initiation au char à voile sur la plage de Trestraou',
                'De 7 à 77 ans, le char à voile est à la portée de tous et vous procure une immense sensation de liberté.',
                'Direction les longues plages de sable fin du Marquenterre, au nord de la côte picarde, entre Quend-Plage et Fort-Mahon : l''un des meilleurs spots pour s''essayer au char à voile ! 

Une séance dure 3 heures environ.
Vous commencez par 45 minutes d''initiation avec votre moniteur au cours desquelles vous apprenez les manœuvres, à créer votre trajectoire en sentant le vent.
Et c''est parti pour plus de 2 heures de randonnée sur les longues plages et vous profitez rapidement des sensations grisantes de la vitesse.

Le saviez-vous ? Un char à voile peut atteindre 90km/h ! Rendez-vous dans nos bases nautiques !
L''initiation au char à voile peut être incluse dans le cadre d''une activité scolaire et extrascolaire (Journée de bord de mer plein air, classe de découverte, journée d''intégration), week-end multi activités, séminaires (char à voile, randonnée nautique, équitation, hébergement, repas).',
                '3:00:',
                7,
                'Nous vous proposons une initiation et randonnée en char à voile'
            )
        returning
            id
    ),
    s1 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('SamSepi0l'), 4, 'famille', '2024-09-30', 'Très bon rapport qualité-prix.')
    ),
    s2 as (
        insert into
            _souscription_option (id_offre, nom_option, lancee_le, nb_semaines)
        values
            ((table id_offre), 'En Relief', localtimestamp, 3)
    )
insert into
    _tags (id_offre, tag)
values
    ((table id_offre), 'nautique'),
    ((table id_offre), 'sport');