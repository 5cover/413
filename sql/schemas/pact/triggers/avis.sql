set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function avis_insert () returns trigger as $$
declare
begin
    if offre_categorie(new.id_offre) = 'restaurant' then
        raise 'insérer dans avis_restaurant pour les avis sur les restaurants';
    end if;
    new = insert_avis(new, new.id_offre);
    return new;
end
$$ language plpgsql;

create trigger tg_avis_insert instead of insert on avis for each row
execute function avis_insert ();

-- Update
create function avis_update () returns trigger as $$
begin
    if old.id_offre <> new.id_offre then
        raise 'Ne peut pas modifier id_offre';
    end if;
    new = update_avis(old, new);
    return new;
end
$$ language plpgsql;

create trigger tg_avis_update instead of update on avis for each row
execute function avis_update ();

-- Delete

create function avis_delete () returns trigger as $$
begin
    delete from _signalable where id = old.id;
    return old;
end
$$ language plpgsql;

create trigger tg_avis_delete instead of delete on avis for each row
execute function avis_delete ();
