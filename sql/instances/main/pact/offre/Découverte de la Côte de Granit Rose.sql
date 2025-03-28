set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, precision_ext, numero_voie, nom_voie,lat,long)
        values
            ('22', 168, 'Parking De La Rade 83', 72, 'Rue Ernest Renan',48.768971,-3.5750084)
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
                '2024-03-16 18:01:23',
                34,
                2,
                'gratuit',
                '2:00:',
                'Découverte de la Côte de Granit Rose en bateau',
                'Découvrez la magie de la Côte de Granite Rose avec notre excursion en bateau inoubliable. Cette visite vous emmène à travers des paysages époustouflants, des formations rocheuses uniques et des plages de sable fin. Profitez de vues panoramiques, de commentaires enrichissants et de moments de détente pour une expérience complète et mémorable.',
                '**Introduction :**
Embarquez pour une aventure inoubliable avec notre excursion en bateau le long de la Côte de Granite Rose. Cette région emblématique de la Bretagne est célèbre pour ses formations rocheuses spectaculaires, ses plages de sable fin et ses eaux cristallines. Notre visite est conçue pour vous offrir une expérience immersive et enrichissante, combinant découverte, détente et émerveillement.

**Départ et Itinéraire :**
Notre excursion commence au port de Perros-Guirec, où vous serez accueilli par notre équipage chaleureux et expérimenté. Après un briefing de sécurité, nous mettrons le cap vers les sites les plus emblématiques de la Côte de Granite Rose. Vous naviguerez le long de la côte, admirant des formations rocheuses uniques telles que le Phare de Ploumanac''h, la Plage de Trestraou et les Sept-Îles.

**Points Forts de la OffreVisite :**
- **Phare de Ploumanac''h :** Un des phares les plus pittoresques de la région, offrant une vue imprenable sur la côte.
- **Plage de Trestraou :** Une plage de sable fin idéale pour se détendre et profiter du paysage.
- **Les Sept-Îles :** Un archipel protégé abritant une riche biodiversité, notamment des colonies d''oiseaux marins.

**Commentaires et Histoire :**
Tout au long de la visite, notre guide vous fournira des commentaires enrichissants sur l''histoire, la géologie et la faune de la région. Vous apprendrez comment les formations rocheuses ont été sculptées par le temps et les éléments, et découvrirez les légendes locales qui entourent ces lieux mystiques.

**Moments de Détente :**
En plus des vues spectaculaires, notre excursion inclut des moments de détente. Nous ferons une pause sur une plage isolée où vous pourrez vous baigner, vous détendre sur le sable ou simplement profiter de la tranquillité de la nature. Un goûter léger sera également servi à bord pour vous rafraîchir et vous restaurer.

**Conclusion :**
Notre visite en bateau de la Côte de Granite Rose est une expérience unique qui combine découverte, détente et émerveillement. Que vous soyez un amoureux de la nature, un passionné d''histoire ou simplement à la recherche d''une journée inoubliable, cette excursion est faite pour vous. Réservez dès maintenant pour vivre une aventure mémorable sur l''une des plus belles côtes de France.

---

**Informations Pratiques :**
- **Durée :** 3 heures
- **Départ :** Port de Perros-Guirec
- **Inclus :** Guide expérimenté, commentaires enrichissants, goûter léger, moments de détente sur la plage
- **Réservation :** Contactez-nous pour réserver votre place et obtenir plus d''informations.

Nous avons hâte de vous accueillir à bord pour une journée inoubliable sur la Côte de Granite Rose !',
                'https://www.bretagne-cotedegranitrose.com'
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
            ((table id_offre), 'nautique'),
            ((table id_offre), 'culturel')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('Snoozy'),
                3,
                'couple',
                '2024-08-14',
                'Service correct mais attente longue.'
            )
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