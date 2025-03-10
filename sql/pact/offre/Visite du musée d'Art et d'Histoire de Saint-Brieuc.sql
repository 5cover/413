set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, numero_voie, nom_voie)
        values
            ('22', 278, 2, 'Rue des Lycéens Martyrs')
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
                url_site_web
            )
        values
            (
                (table id_adresse),
                '2024-06-05 21:06:45',
                45,
                2,
                'gratuit',
                '4:00:',
                'Visite du musée d''Art et d''Histoire de Saint-Brieuc',
                'Découvrez l''histoire et la culture de la Bretagne à travers une visite guidée du musée d''Art et d''Histoire de Saint-Brieuc. Plongez dans des collections riches et variées, allant de l''archéologie à l''art contemporain, en passant par des expositions temporaires fascinantes. Une expérience immersive et enrichissante pour tous les amateurs d''art et d''histoire.',
                '### Introduction au musée
Le musée d''Art et d''Histoire de Saint-Brieuc est un lieu incontournable pour quiconque souhaite explorer le patrimoine culturel et historique de la Bretagne. Situé au cœur de la ville, ce musée offre une vue d''ensemble sur l''évolution artistique et historique de la région, depuis les temps préhistoriques jusqu''à nos jours.

### Les collections permanentes
La visite commence par les collections permanentes, qui couvrent une large période historique. Les sections d''archéologie présentent des objets fascinants datant de la préhistoire, de l''Antiquité et du Moyen Âge, offrant un aperçu unique de la vie quotidienne et des pratiques culturelles de ces époques. Les amateurs d''art pourront admirer des œuvres de maîtres anciens et modernes, ainsi que des créations contemporaines qui témoignent de la vitalité artistique de la Bretagne.

### Les expositions temporaires
En plus des collections permanentes, le musée propose régulièrement des expositions temporaires qui mettent en lumière des thèmes spécifiques ou des artistes locaux et internationaux. Ces expositions sont souvent accompagnées de conférences, d''ateliers et d''autres événements culturels, offrant ainsi une expérience interactive et éducative.

### La visite guidée
Pour une expérience encore plus enrichissante, nous proposons une visite guidée par des experts passionnés. Ces guides vous feront découvrir les trésors cachés du musée, partageront des anecdotes historiques et répondront à toutes vos questions. La visite guidée est disponible en plusieurs langues et peut être adaptée aux besoins spécifiques des groupes, qu''ils soient scolaires, familiaux ou professionnels.

### Les services supplémentaires
Le musée dispose également d''une boutique où vous pourrez acheter des souvenirs, des livres et des reproductions d''œuvres d''art. Un café-restaurant est également disponible sur place, offrant une pause agréable après la visite. Pour les visiteurs souhaitant approfondir leurs connaissances, une bibliothèque spécialisée est accessible sur demande.

### Informations pratiques
Le musée est ouvert du mardi au dimanche, de 10h à 18h. Les tarifs d''entrée sont abordables et des réductions sont disponibles pour les étudiants, les seniors et les groupes. Pour une visite sans stress, il est recommandé de réserver à l''avance, surtout pour les visites guidées.

Ne manquez pas cette opportunité unique de découvrir l''art et l''histoire de la Bretagne dans un cadre exceptionnel. Réservez dès maintenant votre visite au musée d''Art et d''Histoire de Saint-Brieuc et laissez-vous transporter par la richesse de son patrimoine.',
                'https://reserves-naturelles.org/reserves/sept-iles'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _langue_visite (id_visite, code_langue)
        values
            ((table id_offre), 'fr'),
            ((table id_offre), 'en')
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'musée'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'art')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('Snoozy'),
                4,
                'affaires',
                '2024-06-08',
                'Pratique pour des événements de détente entre collègues.'
            )
    ),
    s4 as (
        insert into
            _souscription_option (id_offre, nom_option, lancee_le, nb_semaines)
        values
            ((table id_offre), 'En Relief', localtimestamp, 3)
    )
insert into
    _ouverture_hebdomadaire (id_offre, dow, horaires)
values
    (
        (table id_offre),
        1,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        2,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        3,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        4,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        5,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    ),
    (
        (table id_offre),
        6,
        (
            select
                timemultirange (timerange ('10:', '18:'))
        )
    );