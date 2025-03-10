set schema 'tchattator';

create table _message (
    id serial
        constraint message_pk primary key,
    sent_on timestamp not null,
    modified_on timestamp check (modified_on > sent_on),
    deleted_on timestamp check (deleted_on > coalesce(modified_on, sent_on)),
    content varchar not null,
    read bool not null default false,

    id_compte_sender int -- Null for admin
        constraint message_fk_compte_sender references pact._compte on delete cascade,
    id_compte_recipient int -- Null for admin
        constraint message_fk_compte_recipient references pact._compte on delete cascade
);

create table _single_block (
    id_membre int
        constraint single_block_fk_membre references pact._membre on delete cascade,
    id_professionnel int
        constraint single_block_fk_professionnel references pact._professionnel on delete cascade,
    constraint single_block_pk primary key (id_membre, id_professionnel),

    expires_on timestamp not null default 'infinity'
);

create table _full_block (
    id_membre int
        constraint full_block_pk primary key
        constraint full_block_fk_membre references pact._membre on delete cascade,
    expires_on timestamp not null default 'infinity'
);
