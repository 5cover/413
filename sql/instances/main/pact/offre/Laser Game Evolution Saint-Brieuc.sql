set schema 'pact';

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 360, 'Zone de loisirs Brezillet ouest')
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
                prestations_incluses,
                prestations_non_incluses
            )
        values
            (
                (table id_adresse),
                '2024-02-26 07:25:38',
                2,
                29,
                'gratuit',
                'https://saint-brieuc.lasergame-evolution.fr/',
                'Laser Game Evolution Saint-Brieuc',
                'Bienvenue au Laser Game Evolution de Saint-Brieuc ! Réservation conseillée - Vous pouvez jouer à partir de 4 joueurs et à partir de 7 ans minimum.',
                'Equipé de votre PISTOLET 100% LASER, votre précision et votre rapidité sont vos atouts pour surpasser vos adversaires dans nos labyrinthes ! Utilisez les planches pour vous cacher, mais méfiez-vous des meurtrières et des miroirs ! Pour un anniversaire, avec vos amis ou encore vos collaborateurs, venez jouer sur réservation, car nos labyrinthes vous seront entièrement privatisés ! Une équipe dynamique sera à votre disposition pour vous faire passer un moment inoubliable ! Laser Game Evolution, LE JEU LASER 100 % !',
                '0:20:',
                7,
                'Nous vous proposons un laser game, un quiz game, un blindtest, des fléchettes',
                'Nous ne vous proposons pas de repas'
            )
        returning
            id
    ),
    s1 as (
        insert into
            _tags (id_offre, tag)
        values
            ((table id_offre), 'sport'),
            ((table id_offre), 'famille'),
            ((table id_offre), 'jeu')
    ),
    s2 as (
        insert into
            avis (id_offre, id_membre_auteur, note, contexte, date_experience, commentaire)
        values
            ((table id_offre), id_membre ('ltorvalds'), 3, 'amis', '2024-05-02', 'Ambiance sympa mais prix élevés.')
    ),
    s3 as (
        insert into
            _souscription_option (id_offre, nom_option, lancee_le, nb_semaines)
        values
            ((table id_offre), 'À la Une', localtimestamp, 1)
    )
insert into
    _galerie (id_offre, id_image)
values
    ((table id_offre), 30),
    ((table id_offre), 31),
    ((table id_offre), 32),
    ((table id_offre), 33);