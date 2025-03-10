set schema 'pact';

set
    plpgsql.extra_errors to 'all';

-- Create
create function tarif_insert () returns trigger as $$
begin
    if 'gratuit' = libelle_abonnement from pact._offre o where o.id = new.id_offre then
        raise 'Seulement les offres payantes (standard ou premium) peuvent avoir une grille tarifaire.';
    end if;

    insert into pact._tarif (
        id_offre,
        nom,
        montant
    ) values (
        new.id_offre,
        new.nom,
        new.montant
    );
    return new;
end
$$ language plpgsql;

create trigger tg_tarif_insert instead of insert on tarif for each row
execute function tarif_insert ();
