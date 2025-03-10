set schema 'pact';

set
    plpgsql.extra_errors to 'all';


-- Delete
create function _offre_after_delete () returns trigger as $$
begin
    delete from _image where id = old.id_image_principale;
    delete from _adresse where id = old.id_adresse;
    return old;
end
$$ language plpgsql;

create trigger tg__offre_after_delete after delete on _offre for each row
execute function _offre_after_delete ();