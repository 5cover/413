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

    select
        en_ligne,
        note_moyenne,
        prix_min,
        creee_le,
        en_ligne_ce_mois_pendant,
        changement_ouverture_suivant_le,
        est_ouverte
    from
        offres
    where
        id = new.id
    into
        new.en_ligne,
        new.note_moyenne,
        new.prix_min,
        new.creee_le,
        new.en_ligne_ce_mois_pendant,
        new.changement_ouverture_suivant_le,
        new.est_ouverte;
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
        id_adresse
    ) values (
        new.id,
        new.email,
        new.mdp_hash,
        new.nom,
        new.prenom,
        new.telephone,
        new.id_adresse
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

create function _compte_delete() returns trigger as $$
begin
    delete from _signalable where id = old.id;
    delete from _adresse where id = old.id_adresse;
    return old;
end
$$ language plpgsql;

create function update_avis(old record, inout new record) as $$
begin
    if old.id <> new.id or old.id_offre <> new.id_offre or old.id_membre_auteur <> new.id_membre_auteur then
        raise 'Ne peut pas update id ou id_offre ou id_membre_auteur';
    end if;

    update _avis
    set
        commentaire = new.commentaire,
        note = new.note,
        publie = new.publie,
        date = new.date,
        contexte = new.contexte,
        lu = new.lu,
<<<<<<< HEAD:sql/schemas/pact/triggers.util.sql
        likes = new.likes,
        dislikes = new.dislikes
=======
        blackliste = new.blackliste
>>>>>>> 8c459db373f74adcc295a31a8c7a7fa64844222d:sql/pact/triggers.util.sql
    where
        id = new.id;
end
$$ language plpgsql;