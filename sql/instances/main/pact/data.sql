set schema 'pact';

insert into
    pro_prive (adresse, siren, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        '12 Rue De l''Église 33433 Farcebleux',
        '123456789',
        'MERTREM Solutions',
        'contact@mertrem.org',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Dephric',
        'Max',
        '0288776655'
    );

insert into
    pro_public (adresse, denomination, email, mdp_hash, nom, prenom, telephone)
values
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'Commune de Thiercelieux',
        'thiercelieux.commune@voila.fr',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike', -- toto
        'Fonct',
        'Ionnaire',
        '1122334455'
    );

insert into
    membre (adresse, pseudo, email, prenom, nom, telephone, mdp_hash)
values
    (
        '12 Rue De l''Église 33433 Farcebleux',
        '5cover',
        'scover@gmail.com',
        'Scover',
        'NoLastName',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'Snoozy',
        'snoozy@gmail.com',
        'Benjamin',
        'Dumont-girard',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'j0hn',
        'john.smith@mertrem.org',
        'John',
        'Smith',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'SamSepi0l',
        'sem.sepiol@gmail.com',
        'Eliott',
        'Alderson',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'dieu_des_frites',
        'marius.clg.important@gmail.com',
        'Marius',
        'Chartier--Le Goff',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'rstallman',
        'stallman.richard@gnu.org',
        'Richard',
        'Stallman',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'ltorvalds',
        'linus.torvalds@kernelist.org',
        'Linus',
        'Torvalds',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    ),
    (
        '12 Rue De l''Église 33433 Farcebleux',
        'Maëlan',
        'maelan.clg.important@gmail.com',
        'Maëlan',
        'Poteir',
        '2134657980',
        '$2y$10$EGLHZkQPfzunBskmjGlv0eTVbF8uot3J6R/W76TIjUw33xSYadike' -- toto
    );
