set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function visite_insert () returns trigger as $$
begin
    new = insert_offre(new);
    insert into pact._visite (
        id,
        indication_duree
    ) values (
        new.id,
        new.indication_duree
    );
    return new;
end
$$ language plpgsql;

create trigger tg_visite_insert instead of insert on visite for each row
execute function visite_insert ();

-- Update
create function visite_update () returns trigger as $$
begin
    new = update_offre(old, new);

    update _visite
    set
        indication_duree = new.indication_duree
    where
        id = new.id;

    return new;
end
$$ language plpgsql;

create trigger tg_visite_update instead of update on visite for each row
execute function visite_update ();

-- Delete
create function visite_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
end
$$ language plpgsql;

create trigger tg_visite_delete instead of delete on visite for each row
execute function visite_delete ();