# Problèmes

la todolist de rache

## Project Trenzalore

- Use null for empty strings
- Entities
- Storybook
- for head, header, footer: make single script and use functions for all

## Fait

Assigné|Page|Problème
-|-|-
||connexion|C'est dégueulasse et le bouton connexion est mal placé quand il y a une erreur
|||Le header est mal fait (le logo devrait être à gauche, bouton connexion à droite)
|||Quand l'utilisateur est connexté, il faudrait affiché "déconnexion" au lieu de "connexion"
||detail-offre|white-space: pre-wrap pour la description détailéle
||connexion|return url quand on exige de l'utilisateur qu'il se connecte
||carte offre|afficher le pris le plus bas de la grille tarifaire (utiliser attr calculé vue offres)
||detail offre > creation avis|ajouter "autre" en contexte

## Page modifer

modifier offre

## BDD

- [ ] FACTURATION preciser prestations pour options et abonnement
- [x] interruption option sans effet sur la facturation (bool actif dans souscription option)
- [x] contrainte exclude periodes overtures et horaires_ouverture, non-overlapping
- [x] anonymisation des avis ok avec la clé primaire de _avis actuelle?
- [x] la date d'experience de l'avis doit être postérieure ou égale à la date de création de l'offre

## Dataset

- [x] avis
- [x] \+ de membres
- [x] grilles tarifaires
- [x] changements d'état
- [x] offres.modifiee_le explicite (pour le sorting et tester offre_en_ligne_pendant), reutilise dans l'insertion dans changement_etat
- [x] avis restaurant

## web design

- [ ] Mode daltonien pour benoit
- [x] rediriger vers accpro si on est sur acceuil et connecte pro
- [x] accueil cartes cliquables

## js

- [ ] proper put image tri_recherche.js

## Todo

- [x] Use args array technique everywhere `$_GET`, `$_POST` or `$_FILES` are used.
- [ ] fix php performance problems
- [x] utiliser un fichier offers.json pour recherche.php qui sera fetch en JS et les images
- [ ] encapsuler les multiples insertions dans une transaction en php
- [x] traduire les commentaires et le code en français
- [ ] passer adresse dans un seul sous-tableau au lieu d'utiliser un préfixe

- [ ] verif gratuit bdd
- [ ] maelan liste bugs dans des issues
- [ ] update serrer workflow display committer name and message
- [ ] propagate null in sql parse methods. Clearly document that the output is destined to be SQL.
- [ ] Do not use getarg to get from non-argument arrarys. we don't need it anymore sicne we propagate null  

## PHP

- [x] global replace: `<?=`
- [x] global replace: no semicolons before `?>` on same line
- [x] encapsulate functions in namespaces, use `use`? to avoid naming conflicts and make it clearer where a function is from.

### Creation offre

- [ ] custom tags (no need for + button, a non-empty tag input implies adding a new one)
- [ ] options
- [ ] grille tarifaire: mise en exergue du plus bas tarif
- [ ] abonnement demander un choix avec une carte affichant une description, une liste des avantages, et un prix

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

## Octocat

ON fix : mit scheme exit phrase, ave caesar moritarus te saliti

on break : je suis pas venu ici pour souffrir ok, voir voc
  