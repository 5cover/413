set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function restaurant_insert () returns trigger as $$
begin
    new = insert_offre(new);

    new.sert_petit_dejeuner = coalesce(new.sert_petit_dejeuner, false);
    new.sert_brunch = coalesce(new.sert_brunch, false);
    new.sert_dejeuner = coalesce(new.sert_dejeuner, false);
    new.sert_diner = coalesce(new.sert_diner, false);
    new.sert_boissons = coalesce(new.sert_boissons, false);

    insert into pact._restaurant (
        id,
        carte,
        richesse,
        sert_petit_dejeuner,
        sert_brunch,
        sert_dejeuner,
        sert_diner,
        sert_boissons
    ) values (
        new.id,
        new.carte,
        new.richesse,
        new.sert_petit_dejeuner,
        new.sert_brunch,
        new.sert_dejeuner,
        new.sert_diner,
        new.sert_boissons
    );
    return new;
end
$$ language plpgsql;

create trigger tg_restaurant_insert instead of insert on restaurant for each row
execute function restaurant_insert ();

-- Update
create function restaurant_update () returns trigger as $$
begin
    new = update_offre(old, new);

    update _restaurant
    set
        carte = new.carte,
        richesse = new.richesse,
        sert_petit_dejeuner = new.sert_petit_dejeuner,
        sert_brunch = new.sert_brunch,
        sert_dejeuner = new.sert_dejeuner,
        sert_diner = new.sert_diner,
        sert_boissons = new.sert_boissons
    where
        id = new.id;

    return new;
end
$$ language plpgsql;

create trigger tg_restaurant_update instead of update on restaurant for each row
execute function restaurant_update ();

-- Delete
create function restaurant_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_restaurant_delete instead of delete on restaurant for each row
execute function restaurant_delete ();

