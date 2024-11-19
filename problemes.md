# Problèmes

Assigné|Page|Problème
-|-|-
Marius|détail offre|La page détail offre est dégueulasse et il manque des informations
||connexion|C'est dégueulasse et le bouton connexion est mal placé quand il y a une erreur
|||Le header est mal fait (le logo devrait être à gauche, bouton connexion à droite)
|||Quand l'utilisateur est connexté, il faudrait affiché "déconnexion" au lieu de "connexion"
||detail-offre|white-space: pre-wrap pour la description détailéle
||connexion|return url quand on exige de l'utilisateur qu'il se connecte

## BDD

- preciser prestations pour options et abonnement
- enlever attr remise
- interruption optien sans effet sur la facturation (bool actif dans souscription option)
- attr manquant dans avis
- renommer option.prix en prix_hebdomadaire, abonnement.prix en prix_mensuel
  
## Todo

- Creation offre punctual horaires
- Creation offre custom tags (no need for + button, a non-empty tag input implies adding a new one)
- Creation offre handle horaires, tags and other missing data in PHP
- Craetion offre parse indication_duree_{jours,heures,minutes} accordingly
- Use args array technique everywhere `$_GET`, `$_POST` or `$_FILES` are used.
- fix php performance problems
  
## Components dynamic array

Export a JS class from a module

Constructor arguments:

- table
- template_tr
- validate

Expects:

- `<table>`
  - `<tbody>`
  - `<tfoot>`
    - `<input>`s for each column.
    - a `button`

Methods
