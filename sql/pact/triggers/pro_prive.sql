set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function pro_prive_insert () returns trigger as $$
begin
    new = insert_compte(new);
    insert into pact._professionnel (
        id,
        denomination
    ) values (
        new.id,
        new.denomination
    );
    insert into pact._prive (
        id,
        siren
    ) values (
        new.id,
        new.siren
    );
    return new;
end
$$ language plpgsql;

create trigger tg_pro_prive_insert instead of insert on pro_prive for each row
execute function pro_prive_insert ();

-- Delete
create trigger tg_pro_prive_delete instead of delete on pro_prive for each row execute function _compte_delete();
