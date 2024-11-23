set schema 'pact';

set plpgsql.extra_errors to 'all';

-- utils

create function rmod(x numeric, y numeric) returns numeric as $$
    select (y + x % y) % y;
$$ language sql strict immutable;
comment on function rmod is
'Retourne le modulo de deux entiers.
@param x La première opérande
@param y La seconde opérande
@return @p x % @p y, mais du signe de @p y au lieu de l''opérateur % ou la fonction mod, qui returnent @p x mod @p y du signe de @p x.';

create function bounds(multirange anymultirange) returns table (
    bound anyelement,
    inclusive bool
) as $$
    with ranges as (select unnest(multirange) m)
    select lower(m) bound, lower_inc(m) inclusive from ranges
    union all
    select upper(m), upper_inc(m) from ranges
$$ language sql strict immutable;

-- Membre

create function id_membre(p_pseudo pseudonyme) returns int as $$
    select id from _membre where pseudo = p_pseudo;
$$ language sql strict stable;
comment on function id_membre (pseudonyme) is
'Retourne l''ID d''un membre à partir de son pseudo.
@param p_pseudo le pseudo du membre
@returns L''ID du membre, ou `null` si il n''existe pas de membre ayant le pseudo donné.

Comme le pseudo est `unique`, on peut garantir qu''il n''existe qu''un seul membre pour un pseudo donné.';

-- Offres

create function offre_categorie (p_id_offre int) returns categorie_offre as $$
    select case
        when p_id_offre in (select id from pact._restaurant) then categorie_offre 'restaurant'
        when p_id_offre in (select id from pact._activite) then categorie_offre 'activité'
        when p_id_offre in (select id from pact._visite) then categorie_offre 'visite'
        when p_id_offre in (select id from pact._spectacle) then categorie_offre 'spectacle'
        when p_id_offre in (select id from pact._parc_attractions) then categorie_offre 'parc d''attractions'
    end
$$ language sql strict stable;
comment on function offre_categorie (int) is
'Retourne la catégorie d''une offre.
@param p_id_offre l''ID de l''offre
@returns La catégorie de l''offre d''ID @p p_id_offre.';

create function offre_changement_ouverture_suivant_le (
    p_id_offre int,
    p_apres_le timestamp,
    p_offre_periodes_ouverture tsmultirange
) returns timestamp as $$
    select least(
        (
            select date_trunc('day', p_apres_le)
                 + dans_jours * interval '1 day'
                 + heure
            from (
                select bound heure, inclusive, rmod(dow - extract(dow from p_apres_le), 7) dans_jours
                  from _ouverture_hebdomadaire o, bounds(o.horaires)
                 where id_offre = p_id_offre
            ) b
            where dans_jours <> 0 or (b.inclusive and heure > p_apres_le::time
                               or not b.inclusive and heure >= p_apres_le::time)
            order by dans_jours
            limit 1
        ),
        (
            select bound from bounds(p_offre_periodes_ouverture) b
            where b.inclusive and bound > p_apres_le
            or not b.inclusive and bound >= p_apres_le
            order by bound
            limit 1
        )
    );
$$ language sql strict stable;
comment on function offre_changement_ouverture_suivant_le (int, timestamp, tsmultirange) is
'Retourne un timestamp indiquant quand a lieu le prochain changement d''ouverture d''une offre après une date.
@param p_id_offre l''ID de l''offre
@param p_apres_le le timpestamp auquel la valeur de retour doit être postérieur.
@param p_offre_periodes_ouverture_ouverture l''a valeur de l''attribut periodes_ouverture de l''offre d''ID  @p id_offre. Évite d''avoir faire une nouvelle requête.
@returns Le premier changement d''ouverture de l''offre d''ID @p p_id_offre postérieur à @p p_apres_le, ou `null` si aucun changement d''ouverture n''a lieu après le timsetamp spécifé

Prend uniquement en compte les changement d''ouverture strictement postérieurs à @p p_apres_le.
Ainsi, `offre_changement_ouverture_suivant_le(5, ''2024-11-20'') < offre_changement_ouverture_suivant_le(5, offre_changement_ouverture_suivant_le(5, ''2024-11-20''))`
On ne peut pas utiliser une vue pour les changements d''ouverture car il y en ait une infinité (puisque les horaires se répentent chaque semaine)';


create function offre_en_ligne_pendant (
    p_id_offre int,
    p_debut timestamp,
    p_duree interval
) returns interval as $$
declare
    fait_le timestamp;
    derniere_mise_en_ligne timestamp;
    en_ligne bool not null = false;
    en_ligne_pendant interval not null = '0';
    fin constant timestamp not null = p_debut + p_duree;
    -- le premier changement d'état représente toujours la création.
    creee_le constant timestamp not null = c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le limit 1;
begin
    if p_duree <= interval '0' then
        raise 'La durée doit être positive';
    end if;

    for fait_le in
        select c.fait_le from _changement_etat c where id_offre = p_id_offre order by fait_le offset 1
    loop
        en_ligne = not en_ligne;

        if fin <= fait_le then
            exit;
        elseif p_debut <= fait_le then
            if en_ligne then -- mise en ligne
                -- donc l'offre a été en hors-ligne depuis p_dpebut
                derniere_mise_en_ligne = fait_le;
            else -- mise hors-ligne
                -- donc l'offre a été en ligne depuis p_debut
                en_ligne_pendant = en_ligne_pendant + (fait_le - coalesce(derniere_mise_en_ligne, greatest(p_debut, creee_le)));
            end if;
        end if;
    end loop;

    if en_ligne then
        en_ligne_pendant = en_ligne_pendant + (fin - coalesce(derniere_mise_en_ligne, greatest(p_debut, creee_le)));
    end if;

    return en_ligne_pendant;
end
$$ language plpgsql strict stable;
comment on function offre_en_ligne_pendant (int, timestamp, interval) is
'Retourne la durée pendant laquelle un offre a été en ligne sur une période donnée.
@param p_id_offre l''ID de l''offre
@param p_debut début de la période d''observation
@param p_duree durée de la période d''observation
@returns La valeur de retour est inférieure ou égale à @p p_duree';
