set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function pro_public_insert () returns trigger as $$
begin
    new = insert_compte(new);
    insert into pact._professionnel (
        id,
        denomination
    ) values (
        new.id,
        new.denomination
    );
    insert into pact._public (
        id
    ) values (
        new.id
    );
    return new;
end
$$ language plpgsql;

create trigger tg_pro_public_insert instead of insert on pro_public for each row
execute function pro_public_insert ();

-- Delete
create trigger tg_pro_public_delete instead of delete on pro_public for each row execute function _compte_delete();