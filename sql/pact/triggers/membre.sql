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

-- Update (pseudo)
create function membre_update_pseudo () returns trigger as $$
begin
    update _membre
        set pseudo = new.pseudo
        where id = old.id;
        return new;
    
end
$$ language plpgsql;

create trigger tg_update_pseudo instead of
update on membre for each row
execute function membre_update_pseudo ();

-- Update (denomination)
create function membre_update_denomination () returns trigger as $$
begin
    update _professionnel
        set denomination = new.denomination
        where id = old.id;
        return new;
end
$$ language plpgsql;

create trigger tg_update_denomination instead of
update on professionnel for each row
execute function membre_update_denomination ();

-- Delete
create trigger tg_membre_delete instead of delete on membre for each row execute function _compte_delete();
