# Problèmes

Assigné|Page|Problème
-|-|-
||connexion|C'est dégueulasse et le bouton connexion est mal placé quand il y a une erreur
|||Le header est mal fait (le logo devrait être à gauche, bouton connexion à droite)
|||Quand l'utilisateur est connexté, il faudrait affiché "déconnexion" au lieu de "connexion"
||detail-offre|white-space: pre-wrap pour la description détailéle
||connexion|return url quand on exige de l'utilisateur qu'il se connecte
||carte offre|afficher le pris le plus bas de la grille tarifaire (utiliser attr calculé vue offres)
||detail offre > creation avis|ajouter "autre" en contexte

## BDD

- preciser prestations pour options et abonnement
- interruption option sans effet sur la facturation (bool actif dans souscription option)
- renommer option.prix en prix_hebdomadaire, abonnement.prix en prix_mensuel
- avis_resto with computed attr id_restaurant (based on )
- insert into tarif: assert that `'gratuit' <> (select libelle_abonnement from _offre o where o.id = id_offre)`
- trigger timestamp offre modifiee_le
- non-instanciation classes abstraite
- contrainte exclude periodes overtures et horaires_ouverture, non-overlapping
- normalization periodes ouvertures (contrainte pour ne pas avoir de range overlapping -- agrandir les ranges existants dans un trigger) ce sera intéréssant à coder

- anonymisation des avis ok avec la clé primaire de _avis actuelle?

- ajouter convention s1, s1, s3
- la date d'experience de l'avis doit être postérieure ou égale à la date de création de l'offre

## Dataset

- avis
- \+ de membres
- grilles tarifaires
- changements d'état
- offres.modifiee_le explicite (pour le sorting et tester offre_en_ligne_pendant), reutilise dans l'insertion dans changement_etat

## web design

- Mode daltonien pour benoit

## Todo

- Use args array technique everywhere `$_GET`, `$_POST` or `$_FILES` are used.
- fix php performance problems

- utiliser un fichier offers.json pour recherche.php qui sera fetch en JS et les images
- encapsuler les multiples insertions dans une transaction en php

- traduire les commentaires et le code en français

- passer adresse dans un seul sous-tableau au lieu d'utiliser un préfixe

## PHP

- global replace: `<?=`
- global replace: no semicolons before `?>` on same line
- encapsulate functions in namespaces, use `use`? to avoid naming conflicts and make it clearer where a function is from.

### Creation offre

- custom tags (no need for + button, a non-empty tag input implies adding a new one)
- options
- grille tarifaire: mise en exergue du plus bas tarif
- abonnement demander un choix avec une carte affichant une description, une liste des avantages, et un prix

#### Abonnements

##### Gratuit

Uniquement pour pro public

##### Payant

Uniquement pour pro privé

Facturation

###### Standard

- Grille tarifaire
- Options

###### Premium

Uniquement pour pro privé

- Grille tarifaire
- Options
- Blacklist