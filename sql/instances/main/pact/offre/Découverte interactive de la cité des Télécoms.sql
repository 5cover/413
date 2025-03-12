set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite, lat, long)
        values
            ('22', 198, 'Cité des Télécoms',48.609238, -3.760680)
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
                '2024-02-18 08:42:19',
                4,
                1,
                'standard',
                '3:00:',
                'Visite Interactive de la Cité des Télécoms à Pleumeur-Bodou',
                'Plongez dans l''univers fascinant des télécommunications avec notre visite interactive de la Cité des Télécoms à Pleumeur-Bodou. Cette expérience immersive vous permettra de découvrir l''histoire et les innovations technologiques qui ont révolutionné notre monde. Grâce à des dispositifs interactifs, des démonstrations en direct et des ateliers pratiques, vous vivrez une aventure unique et enrichissante, idéale pour les passionnés de technologie et les curieux de tous âges.',
                'Introduction à la Cité des Télécoms :
La Cité des Télécoms, située à Pleumeur-Bodou en Bretagne, est un lieu incontournable pour tous ceux qui s''intéressent à l''histoire et aux avancées technologiques des télécommunications. Cette visite interactive vous offre une opportunité unique de comprendre comment les télécommunications ont transformé notre quotidien et continuent de le faire.

Parcours Interactif :
Dès votre arrivée, vous serez accueilli par un guide expert qui vous accompagnera tout au long de votre visite. Le parcours commence par une introduction historique, où vous découvrirez les premières inventions et les pionniers des télécommunications. Vous pourrez voir de vos propres yeux des objets d''époque, tels que les premiers téléphones et les anciens équipements de transmission.

Démonstrations en Direct :
L''un des points forts de cette visite est les démonstrations en direct. Vous assisterez à des présentations animées par des spécialistes qui vous expliqueront le fonctionnement des technologies actuelles et futures. Que ce soit la fibre optique, les satellites ou les réseaux 5G, chaque démonstration est conçue pour être à la fois éducative et divertissante.

Ateliers Pratiques :
Pour une expérience encore plus immersive, des ateliers pratiques sont proposés. Vous aurez l''occasion de participer à des activités interactives, comme la création de votre propre réseau de communication ou l''assemblage d''un dispositif de télécommunication. Ces ateliers sont adaptés à tous les niveaux, des débutants aux plus expérimentés, et sont un excellent moyen de mettre en pratique ce que vous avez appris.

Expositions Thématiques :
La Cité des Télécoms abrite également plusieurs expositions thématiques qui changent régulièrement. Ces expositions couvrent des sujets variés, allant de l''impact des télécommunications sur la société à l''évolution des technologies de communication. Chaque exposition est conçue pour être interactive et engageante, avec des installations multimédias et des jeux éducatifs.

Espace de Réalité Virtuelle :
Pour une immersion totale, un espace de réalité virtuelle est disponible. Grâce à des casques VR, vous pourrez explorer des environnements virtuels qui simulent les infrastructures de télécommunications, les centres de données et même les satellites en orbite. Cette expérience vous permettra de comprendre de manière visuelle et immersive les complexités des réseaux de communication.

Conclusion :
La visite interactive de la Cité des Télécoms à Pleumeur-Bodou est une expérience enrichissante et captivante qui vous laissera des souvenirs inoubliables. Que vous soyez un passionné de technologie, un curieux ou simplement à la recherche d''une activité éducative et divertissante, cette visite est faite pour vous. Réservez dès maintenant et préparez-vous à découvrir le monde fascinant des télécommunications !

Informations Pratiques :

- Durée de la visite : Environ 3 heures
- Tarifs : Adultes : 15 €, Enfants (6-12 ans) : 10 €, Gratuit pour les moins de 6 ans
- Horaires : Ouvert tous les jours de 10h à 18h
- Réservations : Recommandées en ligne ou par téléphone
- Accès : Facilement accessible en voiture ou en transport en commun

N''attendez plus et venez vivre une aventure technologique unique à la Cité des Télécoms !',
                'https://www.cite-telecoms.com'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _galerie (id_offre, id_image)
        values
            ((table id_offre), 9),
            ((table id_offre), 11),
            ((table id_offre), 14)
    ),
    s2 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'culturel'),
            ((table id_offre), 'musée'),
            ((table id_offre), 'technologie')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('5cover'),
                5,
                'amis',
                '2024-10-22',
                'Superbe ambiance et repas délicieux.'
            )
    ),
    s4 as (
        insert into
            tarif (nom, id_offre, montant)
        values
            ('adulte', (table id_offre), 10),
            ('enfant', (table id_offre), 5)
    )
insert into
    _ouverture_hebdomadaire (id_offre, dow, horaires)
values
    (
        (table id_offre),
        1,
        (
            select
                timemultirange (timerange ('12:', '15:30'), timerange ('18:30', '23:59:59'))
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