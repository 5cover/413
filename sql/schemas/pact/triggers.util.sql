-- Fonctions utilitaires pour les triggers
-- Non destinées à êtres appelées en dehors de triggers.sql

set schema 'pact';

set plpgsql.extra_errors to 'all';

create function insert_avis (inout new record, p_id_offre int) as $$
declare
    -- On ne prend pas en compte les heures pour cette vérification.
    -- Cela signifie qu'on autorise la publication d'un avis le même jour que la création de l'offre mais à une heure antérieure.
    -- C'est soit ça soit interdire la publication à heure postérieure car date_experience n'a pas d'heure.
    -- Mieut vaut autoriser un cas potentiellement invalide plutôt qu'interdire un cas potentiellement valide.
    date_creation_offre constant date not null = offre_creee_le(p_id_offre)::date;
begin
    if new.date_experience < date_creation_offre then
        raise 'La date d''expérience de l''avis (%) ne peut pas être antérieure à la date création de de l''offre (%)', new.date_experience, date_creation_offre;
    end if;

    insert into _signalable default values returning id into new.id;
    insert into _avis (
        id,
        id_offre,
        id_membre_auteur,
        commentaire,
        date_experience,
        contexte,
        note
    ) values (
        new.id,
        p_id_offre,
        new.id_membre_auteur,
        new.commentaire,
        new.date_experience,
        new.contexte,
        new.note
    ) returning
        publie_le,
        lu
    into
        new.publie_le,
        new.lu;
end
$$ language plpgsql strict;

comment on function insert_avis (record, int) is
'Insère un avis.
@param new contient les valeurs de l''avis.';

create function insert_offre (inout new record) as $$
begin
    if (new.libelle_abonnement = 'gratuit') = new.id_professionnel in (select id from _prive) then
        raise 'Cette offre (%) d''abonnement % ne peut pas être administrée par un professionnel du secteur %',
            new.titre, new.libelle_abonnement, professionnel_secteur(new.id_professionnel);
    end if;

    new.modifiee_le = coalesce(new.modifiee_le, localtimestamp);
    new.periodes_ouverture = coalesce(new.periodes_ouverture, '{}');

    insert into pact._signalable default values returning id into new.id;
    insert into pact._offre (
        id,
        id_adresse,
        id_image_principale,
        id_professionnel,
        libelle_abonnement,
        titre,
        resume,
        description_detaillee,
        url_site_web,
        modifiee_le,
        periodes_ouverture
    ) values (
        new.id,
        new.id_adresse,
        new.id_image_principale,
        new.id_professionnel,
        new.libelle_abonnement,
        new.titre,
        new.resume,
        new.description_detaillee,
        new.url_site_web,
        new.modifiee_le,
        new.periodes_ouverture
    );
    
    insert into pact._changement_etat (id_offre, fait_le) values (new.id, new.modifiee_le);

    new.en_ligne = offre_en_ligne(new.id);
    new.note_moyenne = offre_note_moyenne(new.id);
    new.nb_avis = offre_nb_avis(new.id);
    new.prix_min = offre_prix_min(new.id);
    new.creee_le = offre_creee_le(new.id);
    new.en_ligne_ce_mois_pendant = offre_en_ligne_pendant(new.id, date_trunc('month', localtimestamp), localtimestamp);
    new.changement_ouverture_suivant_le = offre_changement_ouverture_suivant_le(new.id, localtimestamp, new.periodes_ouverture);
    new.est_ouverte = offre_est_ouverte(new.id, new.periodes_ouverture);
    -- new.option devrait tjrs etre null
end
$$ language plpgsql strict;
comment on function insert_offre (record) is
'Insère une offre.
@param new contient les valeurs de l''offre.';

create function insert_compte (inout new record) as $$
begin
    new.email = lower(new.email);

    insert into pact._signalable default values returning id into new.id;
    insert into pact._compte (
        id,
        email,
        mdp_hash,
        nom,
        prenom,
        telephone,
        adresse,
        api_key
    ) values (
        new.id,
        new.email,
        new.mdp_hash,
        new.nom,
        new.prenom,
        new.telephone,
        new.adresse,
        new.api_key
    ) returning id into new.id;
end
$$ language plpgsql strict;
comment on function insert_compte (record) is
'Insère un compte.
`new` contient les valeurs du compte.';

create function update_offre (old record, inout new record) as $$
begin
    if old.id <> new.id then
        raise 'Ne peut pas update id.';
    end if;

    new.modifiee_le = localtimestamp;

    update _offre
    set
        id_adresse = new.id_adresse,
        id_image_principale = new.id_image_principale,
        id_professionnel = new.id_professionnel,
        libelle_abonnement = new.libelle_abonnement,
        titre = new.titre,
        resume = new.resume,
        description_detaillee = new.description_detaillee,
        modifiee_le = new.modifiee_le,
        url_site_web = new.url_site_web,
        periodes_ouverture = new.periodes_ouverture
    where
        id = new.id;
end
$$ language plpgsql;

create procedure update_professionnel(old record, new record) as $$
begin
    call update_compte(old, new);
    update _professionnel
    set
        denomination = new.denomination
    where
        id = new.id;
end
$$ language plpgsql;

create procedure update_compte(old record, new record) as $$
begin
    if old.id <> new.id then
        raise 'Ne peut pas update id.';
    end if;

    update _compte
    set
        email = new.email,
        mdp_hash = new.mdp_hash,
        nom = new.nom,
        prenom = new.prenom,
        telephone = new.telephone,
        adresse = new.adresse,
        api_key = new.api_key
    where
        id = new.id;
end
$$ language plpgsql;

create function _compte_delete() returns trigger as $$
begin
    -- fixme do nothing
    return old;
end
$$ language plpgsql;

create procedure delete_unused_adress(p_id_adresse int) as $$
-- We need to do this because _adresse rows can be shared, but we still want to avoid having unused adresses around.
begin
    delete from _adresse where id = p_id_adresse;
exception
    when foreign_key_violation then
        -- do nothing
end
$$ language plpgsql;

create function update_avis(old record, inout new record) as $$
begin
    if old.id <> new.id or old.id_membre_auteur <> new.id_membre_auteur then
        raise 'Ne peut pas update id ou id_membre_auteur';
    end if;

    update _avis
    set
        commentaire = new.commentaire,
        note = new.note,
        publie_le = new.publie_le,
        date_experience = new.date_experience,
        contexte = new.contexte,
        lu = new.lu,
        likes = new.likes,
        dislikes = new.dislikes
    where
        id = new.id;
end
$$ language plpgsql;