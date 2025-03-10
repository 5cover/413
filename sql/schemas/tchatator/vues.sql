set schema 'tchatator';

create view
    msg as
select
    msg_id,
    content,
    sent_at,
    read_age,
    edited_age,
    id_compte_sender,
    id_compte_recipient
from
    _msg;

create view
    inbox as
select
    *
from
    msg
order by
    sent_at;

create view
    "user" as
select
    id "user_id",
    email,
    nom,
    prenom,
    api_key,
    mdp_hash,
    pseudo display_name,
    0 kind
from
    pact.membre
union all
select
    id,
    email,
    nom,
    prenom,
    api_key,
    mdp_hash,
    denomination,
    1
from
    pact.pro_prive
union all
select
    id,
    email,
    nom,
    prenom,
    api_key,
    mdp_hash,
    denomination,
    2
from
    pact.pro_public;