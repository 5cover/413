set schema 'pact';

insert into _abonnement(libelle, prix)
    values ('gratuit', 0),
('standard', 5), -- ébauche
('premium', 10);

-- ébauche
insert into _compte(email, mdp_hash, nom, prenom, telephone)
    values ('a.gmail', 'hashça', 'Abraham', 'Lincoln', '0123456789');

with signalable as (
insert into _signalable default
        values
        returning
            id_signalable
), identite as (
insert into _identite default
        values
        returning
            id_identite
), adresse as (
insert into _adresse(nom_voie, commune_code_insee, commune_code_postal)
        values ('aaaaaaaaaaaaaaaaaaa', '1001', '1400')
    returning
        id_adresse
), professionnel as (
insert into _professionnel(denomination, email)
        values ('MERTREM Solutions', 'a.gmail')
    returning
        id_professionnel
), image as (
insert into _image(legende, taille, mime_type)
        values ('legende', 100, 'image/jpeg')
    returning
        id_image)
    insert into _offre(titre, resume, description_detaille, url_site_web, adresse, id_signalable, id_professionnel, photoprincipale)
        values ('barraque à frites', 'aaaaaaaaaaa', 'cest une barraque à frite', 'blabla.fr',(table adresse),
(table signalable),
(table professionnel),
(table image));

