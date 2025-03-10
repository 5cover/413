set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie, precision_ext)
        values
            ('22', 168, 'Chem. du Squevel', 'Parking du Sémaphore - Sentier des Douaniers')
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
                '2024-05-27 03:56:01',
                43,
                2,
                'gratuit',
                '4:00:',
                'Trail dans la réserve naturelle des Sept Îles',
                'Découvrez la beauté sauvage de la réserve naturelle des Sept Îles à Perros-Guirec avec notre offre de trail unique. Parcourez des sentiers pittoresques, admirez la faune et la flore exceptionnelles, et profitez de vues panoramiques sur la côte bretonne. Une expérience inoubliable pour les amateurs de nature et de sport.',
                '### Introduction
Plongez au cœur de la nature bretonne avec notre offre de trail dans la réserve naturelle des Sept Îles à Perros-Guirec. Cette aventure est idéale pour les amateurs de course à pied et de nature, offrant une expérience unique et enrichissante.

### Parcours et Paysages
Le trail commence à Perros-Guirec, une charmante station balnéaire située sur la Côte de Granit Rose. Vous traverserez des sentiers côtiers offrant des vues imprenables sur les Sept Îles, un archipel protégé abritant une faune et une flore exceptionnelles. Les paysages variés incluent des falaises escarpées, des plages de sable fin, et des landes fleuries, créant un cadre idyllique pour votre course.

### Faune et Flore
La réserve naturelle des Sept Îles est un sanctuaire pour de nombreuses espèces d''oiseaux marins, notamment les macareux moines et les fous de Bassan. Vous aurez peut-être la chance d''apercevoir ces oiseaux majestueux depuis les points de vue panoramiques le long du parcours. La flore locale est également remarquable, avec des espèces rares et protégées qui ajoutent à la beauté du paysage.

### Niveau de Difficulté et Encadrement
Le trail est conçu pour être accessible à tous les niveaux de coureurs, avec des options de parcours de différentes longueurs et difficultés. Que vous soyez un débutant ou un coureur expérimenté, vous trouverez un itinéraire adapté à vos capacités. Notre équipe de guides expérimentés vous accompagnera tout au long du parcours, assurant votre sécurité et partageant des anecdotes sur la région.

### Équipement et Services
Pour votre confort, nous fournissons tout l''équipement nécessaire, y compris des chaussures de trail, des sacs à dos, et des bâtons de marche. Des points de ravitaillement sont prévus le long du parcours, offrant des boissons et des collations pour vous maintenir en forme. À la fin de la course, vous pourrez vous détendre et profiter d''un repas local dans un cadre convivial.

### Conclusion
Notre offre de trail dans la réserve naturelle des Sept Îles à Perros-Guirec est bien plus qu''une simple course. C''est une immersion totale dans la nature bretonne, une occasion de découvrir des paysages époustouflants et une faune exceptionnelle. Rejoignez-nous pour une aventure inoubliable et ressentez la magie de la Côte de Granit Rose.

Réservez dès maintenant votre place et préparez-vous à vivre une expérience unique et enrichissante !',
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
            ((table id_offre), 'nature'),
            ((table id_offre), 'plein air'),
            ((table id_offre), 'sport')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('dieu_des_frites'), 5, 'amis', '2024-08-31', 'Cadre magnifique.')
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