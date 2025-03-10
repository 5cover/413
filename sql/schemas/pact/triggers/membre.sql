set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function membre_insert () returns trigger as $$
begin
    new = insert_compte(new);
    insert into pact._membre (
        id,
        pseudo
    ) values (
        new.id,
        new.pseudo
    );
    return new;
end
$$ language plpgsql;

create trigger tg_membre_insert instead of insert on membre for each row
execute function membre_insert ();

-- Update
create function membre_update () returns trigger as $$
begin
    call update_compte(old, new);
    update _membre
    set
        pseudo = new.pseudo
    where
        id = new.id;
    return new;
end
$$ language plpgsql;

create trigger tg_membre_update instead of update on membre for each row
execute function membre_update ();

-- Delete
create trigger tg_membre_delete instead of delete on membre for each row execute function _compte_delete();
