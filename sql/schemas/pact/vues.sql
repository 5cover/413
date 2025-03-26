set schema 'pact';

create view
    tarif as table _tarif;

create view
    avis as
select
    *,
    id in (
        select
            b.id
        from
            _blacklist b
    ) blackliste
from
    _avis;

create view
    offres as
select
    o.id,
    o.id_adresse,
    o.id_image_principale,
    o.id_professionnel,
    o.libelle_abonnement,
    o.titre,
    o.resume,
    o.description_detaillee,
    o.modifiee_le,
    o.url_site_web,
    o.periodes_ouverture,
    offre_en_ligne (o.id) en_ligne,
    offre_note_moyenne (o.id) note_moyenne,
    offre_prix_min (o.id) prix_min,
    offre_nb_avis (o.id) nb_avis,
    offre_creee_le (o.id) creee_le,
    offre_categorie (o.id) categorie,
    offre_en_ligne_pendant (o.id, date_trunc('month', localtimestamp), localtimestamp) en_ligne_ce_mois_pendant,
    offre_changement_ouverture_suivant_le (o.id, localtimestamp, o.periodes_ouverture) changement_ouverture_suivant_le,
    -- Considérer une offre sans période ou horaire comme ouverte tout le temps
    offre_est_ouverte (o.id, o.periodes_ouverture) est_ouverte,
    case
        when so.actif is null then null
        else json_build_array(so.actif, so.nom_option, so.lancee_le, so.nb_semaines, opt.prix_hebdomadaire)
    end option
from
    _offre o
    left join _souscription_option so on so.id_offre = o.id
    left join _option opt on opt.nom = so.nom_option;

comment on column offres.est_ouverte is 'Un booléen indiquant si cette offre est actuellement ouverte';

comment on column offres.changement_ouverture_suivant_le is 'Un timestamp indiquant quand aura lieu le prochain changement d''ouverture.
Si l''offre est fermée, c''est la prochaine ouverture, ou infinity si l''offre sera fermée pour toujours.
Si l''offre est ouverte, c''est la prochaine fermeture, ou infinity si l''offre sera ouverte pour toujours.';

comment on column offres.en_ligne_ce_mois_pendant is 'La durée pendant laquelle cette offre a été en ligne pour le mois courant. La valeur est inférieure ou égale à 1 mois.';

create view
    offre_json as
select
    o.*,
    a.lat lat,
    a.long long,
    coalesce(
        (
            select
                json_agg(t.tag)
            from
                _tags t
            where
                t.id_offre = o.id
        ),
        json '[]'
    ) tags
from
    offres o
    join _adresse a on a.id = o.id_adresse;

create view
    activite as
select
    *
from
    _activite
    join offres using (id);

create view
    spectacle as
select
    *
from
    _spectacle
    join offres using (id);

create view
    visite as
select
    *
from
    _visite
    join offres using (id);

create view
    parc_attractions as
select
    *
from
    _parc_attractions
    join offres using (id);

create view
    restaurant as
select
    *
from
    _restaurant
    join offres using (id);

create view
    membre as
select
    *
from
    _membre
    join _compte using (id);

create view
    professionnel as
select
    *,
    professionnel_secteur (id) secteur
from
    _professionnel
    join _compte using (id);

create view
    pro_prive as
select
    *
from
    _prive
    join professionnel using (id);

create view
    pro_public as
select
    *
from
    _public
    join professionnel using (id);

create view
    avis_restaurant as
select
    commentaire,
    note,
    publie_le,
    date_experience,
    contexte,
    lu,
    id_membre_auteur,
    id_offre id_restaurant,
    likes,
    dislikes,
    blackliste,
    _avis_restaurant.*
from
    _avis_restaurant
    join avis using (id)
    join membre on id_membre_auteur = membre.id;

create view
    reponse as
select
    *
from
    _reponse;


create view
    notifications as
select
    *
from
    _avis WHERE lu = false;

create view
    blacklists_effectives as
select
    *
from
    _blacklist
where
    localtimestamp between date_blacklist and fin_blacklist;