set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function parc_attractions_insert () returns trigger as $$
begin
    new.id = insert_offre(new);
    insert into pact._parc_attractions (
        id,
        id_image_plan,
        nb_attractions,
        age_requis
    ) values (
        new.id,
        new.id_image_plan,
        new.nb_attractions,
        new.age_requis
    );
    return new;
end
$$ language plpgsql;

create trigger tg_parc_attractions_insert instead of insert on parc_attractions for each row
execute function parc_attractions_insert ();

-- Update
create trigger tg_parc_attractions_after_update after update on _parc_attractions for each row
execute function _offre_after_update ();