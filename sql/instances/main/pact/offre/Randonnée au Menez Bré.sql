set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite, lat, long)
        values
            ('22', 164, 'Menez Bré',47.372729, -2.241563)
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
                prestations_incluses
            )
        values
            (
                (table id_adresse),
                '2024-03-09 10:45:00',
                1,
                38,
                'premium',
                'Randonnée au Menez Bré',
                'Découvrez la beauté sauvage et préservée du Menez Bré avec notre offre touristique de randonnée. Situé en Bretagne, le Menez Bré offre des paysages à couper le souffle, une riche biodiversité et une histoire fascinante. Cette randonnée guidée vous permettra de vous immerger dans la nature tout en apprenant sur l''histoire et la culture locale. Idéale pour les amateurs de nature et les passionnés de randonnée, cette expérience promet des moments inoubliables.',
                '### Itinéraire et Durée

Notre randonnée au Menez Bré commence à partir du village pittoresque de Plougonven. La randonnée dure environ 4 heures et couvre une distance de 10 kilomètres. Le parcours est modérément difficile, avec des montées et des descentes qui offrent des vues panoramiques sur les montagnes environnantes et la vallée.

### Points d''Intérêt

Au cours de la randonnée, vous découvrirez plusieurs points d''intérêt historiques et naturels. Le Menez Bré est connu pour ses mégalithes, témoins de l''histoire ancienne de la région. Vous aurez l''occasion de voir des menhirs et des dolmens, ainsi que des vestiges de l''époque celtique. De plus, la randonnée traverse des forêts luxuriantes et des landes, offrant une diversité de paysages qui raviront les amoureux de la nature.

### Faune et Flore

Le Menez Bré est un véritable sanctuaire pour la faune et la flore. Vous pourrez observer une variété d''oiseaux, dont des rapaces, ainsi que des mammifères comme les renards et les chevreuils. La flore est également riche, avec des espèces rares et protégées. Notre guide vous aidera à identifier les différentes plantes et animaux que vous rencontrerez.

### Guide et Équipement

La randonnée est guidée par un expert local qui connaît parfaitement la région. Il vous fournira des informations sur l''histoire, la géologie et la biodiversité du Menez Bré. Tout l''équipement nécessaire, y compris les cartes et les bâtons de randonnée, sera fourni. Nous vous recommandons de porter des chaussures de randonnée confortables et des vêtements adaptés aux conditions météorologiques.

### Repas et Rafraîchissements

Un pique-nique composé de produits locaux sera inclus dans l''offre. Vous pourrez déguster des spécialités bretonnes tout en profitant de la vue imprenable sur les montagnes. Des pauses régulières seront prévues pour vous permettre de vous reposer et de vous hydrater.

### Réservation et Informations Pratiques

Pour réserver votre place, veuillez nous contacter par téléphone ou par e-mail. Les groupes sont limités à 15 personnes pour garantir une expérience personnalisée et sécurisée. Le point de rendez-vous sera communiqué lors de la réservation. Nous vous recommandons de réserver à l''avance pour garantir votre place.

Rejoignez-nous pour une aventure inoubliable au cœur de la Bretagne et découvrez les trésors cachés du Menez Bré.',
                '2:30:',
                'Une randonnée au Ménez Bré'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'nature'),
            ((table id_offre), 'plein air')
    ),
    s2 as (
        insert into
            _galerie (id_offre, id_image)
        values
            ((table id_offre), 39),
            ((table id_offre), 40)
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('5cover'), 2, 'solo', '2024-03-10', 'Lieu trop bruyant.')
    ),
    s4 as (
        insert into
            tarif (nom, id_offre, montant)
        values
            ('adulte', (table id_offre), 5),
            ('enfant', (table id_offre), 1.3)
    )
insert into
    _changement_etat (id_offre, fait_le)
values
    ((table id_offre), '2024-11-15 12:00:00') -- mise en ligne
;