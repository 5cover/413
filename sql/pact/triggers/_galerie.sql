set schema 'pact';

set
    plpgsql.extra_errors to 'all';


-- Delete
create function _galerie_after_delete () returns trigger as $$
begin
    delete from _image where id = old.id_image;
    return old;
end
$$ language plpgsql;

create trigger tg__galerie_after_delete after delete on _galerie for each row
execute function _galerie_after_delete ();