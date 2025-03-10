set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 201, 'Rte du Cap')
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
                '2024-04-03 17:14:47',
                8,
                2,
                'gratuit',
                '1:30:',
                'Visite du phare Vauban au Cap Fréhel',
                'Découvrez la beauté sauvage de la Bretagne avec notre visite guidée du phare du Cap Fréhel. Cette excursion vous offre une vue imprenable sur la côte bretonne, une immersion dans l''histoire maritime et une expérience inoubliable au cœur de la nature.',
                '### Introduction
La visite du phare du Cap Fréhel est une expérience unique qui vous plonge dans l''histoire et la beauté naturelle de la Bretagne. Situé sur la côte d''Émeraude, ce phare emblématique offre des vues panoramiques à couper le souffle sur la mer et les falaises environnantes.

### Déroulement de la visite
La visite commence par une promenade guidée à travers les sentiers côtiers, où vous pourrez admirer la flore et la faune locales. Votre guide vous racontera l''histoire fascinante du phare, construit au XIXe siècle pour guider les marins à travers les eaux tumultueuses de la Manche. Vous découvrirez également les légendes et les mythes qui entourent ce lieu chargé d''histoire.

### Ascension du phare
L''un des moments forts de la visite est l''ascension du phare. Après avoir gravi les marches, vous atteindrez le sommet où une vue à 360 degrés sur la côte bretonne vous attend. Par temps clair, vous pourrez même apercevoir les îles Anglo-Normandes. Votre guide vous expliquera le fonctionnement du phare et son importance stratégique pour la navigation maritime.

### Exploration des environs
Après la visite du phare, vous aurez l''occasion d''explorer les environs. Les falaises de grès rose du Cap Fréhel sont un spectacle à ne pas manquer, offrant un contraste saisissant avec le bleu de la mer. Vous pourrez également vous promener dans le parc naturel qui entoure le phare, où vous pourrez observer une variété d''oiseaux marins et de plantes rares.

### Conclusion
La visite du phare du Cap Fréhel est une expérience enrichissante qui combine histoire, nature et aventure. Que vous soyez un passionné d''histoire, un amoureux de la nature ou simplement à la recherche d''une journée inoubliable, cette excursion est faite pour vous. Réservez dès maintenant pour vivre une aventure bretonne inoubliable.

### Informations pratiques
- **Durée de la visite** : Environ 3 heures
- **Point de départ** : Parking du Cap Fréhel
- **Inclus** : Guide professionnel, accès au phare, promenade guidée
- **Recommandations** : Porter des chaussures confortables et des vêtements adaptés à la météo

Ne manquez pas cette opportunité unique de découvrir l''un des joyaux de la Bretagne. Réservez votre visite dès aujourd''hui et laissez-vous émerveiller par la beauté du Cap Fréhel.',
                'https://www.dinan-capfrehel.com/sit/phare-du-cap-frehel'
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
            ((table id_offre), 'patrimoine'),
            ((table id_offre), 'histoire'),
            ((table id_offre), 'nature')
    ),
    s3 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('5cover'), 5, 'famille', '2024-04-28', 'Une expérience mémorable.')
    ),
    s4 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('Snoozy'), 5, 'amis', '2024-08-14', 'Visite incroyable, guide très passionné.')
    ),
    s5 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('Maëlan'), 4, 'couple', '2024-10-22', 'Belle organisation et lieu impressionnant.')
    ),
    s6 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('j0hn'), 5, 'famille', '2024-11-05', 'Les enfants ont adoré la visite.')
    ),
    s7 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('SamSepi0l'), 5, 'solo', '2024-09-30', 'Un moment unique et enrichissant.')
    ),
    s8 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            (
                (table id_offre),
                id_membre ('dieu_des_frites'),
                4,
                'affaires',
                '2024-06-15',
                'Parfait pour découvrir l''histoire locale.'
            )
    ),
    s9 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('rstallman'), 5, 'amis', '2024-12-20', 'Une découverte exceptionnelle, à refaire.')
    ),
    s10 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('ltorvalds'), 5, 'couple', '2024-05-02', 'Le cadre était à couper le souffle.')
    ),
    s11 as (
        insert into
            _souscription_option (id_offre, nom_option, lancee_le, nb_semaines)
        values
            ((table id_offre), 'À la Une', localtimestamp, 2)
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