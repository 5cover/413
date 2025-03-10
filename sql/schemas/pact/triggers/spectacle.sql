set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function spectacle_insert () returns trigger as $$
begin
    new = insert_offre(new);
    insert into pact._spectacle (
        id,
        indication_duree,
        capacite_accueil
    ) values (
        new.id,
        new.indication_duree,
        new.capacite_accueil
    );
    return new;
end
$$ language plpgsql;

create trigger tg_spectacle_insert instead of insert on spectacle for each row
execute function spectacle_insert ();

-- Update
create function spectacle_update () returns trigger as $$
begin
    new = update_offre(old, new);

    update _spectacle
    set
        indication_duree = new.indication_duree,
        capacite_accueil = new.capacite_accueil
    where
        id = new.id;

    return new;
end
$$ language plpgsql;

create trigger tg_spectacle_update instead of update on spectacle for each row
execute function spectacle_update ();

-- Delete
create function spectacle_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_spectacle_delete instead of delete on spectacle for each row
execute function spectacle_delete ();