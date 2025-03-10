set schema 'pact';

insert into
    _abonnement (libelle, prix_journalier)
values 
    -- prix hors taxe par jour
    ('gratuit', 0),
    ('standard', 1.67),
    ('premium', 3.34);

insert into
    _option (nom, prix_hebdomadaire)
values
    ('À la Une', 20),
    ('En Relief', 30);

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 136, 'Rue du Mène')
        returning
            id
    )
insert into
    pro_prive (id_adresse, siren, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        (table id_adresse),
        '123456789',
        'MERTREM Solutions',
        'contact@mertrem.org',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Dephric',
        'Max',
        '0288776655'
    );

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, nom_voie)
        values
            ('22', 154, 'Les Tronchées')
        returning
            id
    )
insert into
    pro_public (id_adresse, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        (table id_adresse),
        'Commune de Thiercelieux',
        'thiercelieux.commune@voila.fr',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Fonct',
        'Ionnaire',
        '1122334455'
    );

with
    id_adresse as (
        insert into
            _adresse (numero_departement, code_commune, localite)
        values
            ('22', 201, 'Cap Fréhel')
        returning
            id
    )
insert into
    membre (id_adresse, pseudo, email, prenom, nom, telephone, mdp_hash)
values
    (
        (table id_adresse),
        '5cover',
        'scover@gmail.com',
        'Scover',
        'NoLastName',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'Snoozy',
        'snoozy@gmail.com',
        'Benjamin',
        'Dumont-girard',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'j0hn',
        'john.smith@mertrem.org',
        'John',
        'Smith',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'SamSepi0l',
        'sem.sepiol@gmail.com',
        'Eliott',
        'Alderson',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'dieu_des_frites',
        'marius.clg.important@gmail.com',
        'Marius',
        'Chartier--Le Goff',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'rstallman',
        'stallman.richard@gnu.org',
        'Richard',
        'Stallman',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'ltorvalds',
        'linus.torvalds@kernelist.org',
        'Linus',
        'Torvalds',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        (table id_adresse),
        'Maëlan',
        'maelan.clg.important@gmail.com',
        'Maëlan',
        'Poteir',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    );
