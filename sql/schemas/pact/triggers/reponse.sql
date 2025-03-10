set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function reponse_insert () returns trigger as $$
begin
    insert into _signalable default values returning id into new.id;
    insert into
        _reponse (id, id_avis, contenu)
    values
        (new.id, new.id_avis, new.contenu);
    return new;
end
$$ language plpgsql;

create trigger tg_reponse_insert instead of insert on reponse for each row
execute function reponse_insert ();

-- Delete
create function reponse_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_reponse_delete instead of delete on reponse for each row
execute function reponse_delete ();