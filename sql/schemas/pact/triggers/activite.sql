set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function activite_insert () returns trigger as $$
begin
    new = insert_offre(new);
    insert into pact._activite (
        id,
        indication_duree,
        age_requis,
        prestations_incluses,
        prestations_non_incluses
    ) values (
        new.id,
        new.indication_duree,
        new.age_requis,
        new.prestations_incluses,
        new.prestations_non_incluses
    );
    return new;
end
$$ language plpgsql;

create trigger tg_activite_insert instead of insert on activite for each row
execute function activite_insert ();

-- Update
create function activite_update () returns trigger as $$
begin
    new = update_offre(old, new);

    update _activite
    set
        indication_duree = new.indication_duree,
        age_requis = new.age_requis,
        prestations_incluses = new.prestations_incluses,
        prestations_non_incluses = new.prestations_non_incluses
    where
        id = new.id;

    return new;
end
$$ language plpgsql;

create trigger tg_activite_update instead of update on activite for each row
execute function activite_update ();

-- Delete
create function activite_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_activite_delete instead of delete on activite for each row
execute function activite_delete ();
