set schema 'tchatator';

-- CLASSES

create table _msg (
    msg_id serial
        constraint message_pk primary key,
    content varchar not null,
    sent_at timestamp not null default localtimestamp,

    -- ages: sent_at + *_age = *_at
    -- null if never occured
    -- not using intervals because they are hard to deal with and we only need a number of seconds
    read_age int, -- read by recipient
    edited_age int,
    deleted_age int,
    
    constraint deleted_gt_read check (deleted_age > read_age),
    constraint deleted_gt_modified check (deleted_age > edited_age),

    id_compte_sender int -- Null for admin
        constraint message_fk_compte_sender references pact._compte on delete cascade,
    id_compte_recipient int not null
        constraint message_fk_compte_recipient references pact._compte on delete cascade,

    constraint sender_ne_recipient check (id_compte_sender <> id_compte_recipient)
);

-- ASSOCIATIONS

create table _single_block (
    id_membre int
        constraint single_block_fk_membre references pact._membre on delete cascade,
    id_professionnel int
        constraint single_block_fk_professionnel references pact._professionnel on delete cascade,
    constraint single_block_pk primary key (id_membre, id_professionnel),

    expires_at timestamp not null default 'infinity'
);
