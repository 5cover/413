# Problèmes

Assigné|Page|Problème
-|-|-
Marius|détail offre|La page détail offre est dégueulasse et il manque des informations
||connexion|C'est dégueulasse et le bouton connexion est mal placé quand il y a une erreur
|||Le header est mal fait (le logo devrait être à gauche, bouton connexion à droite)
|||Quand l'utilisateur est connexté, il faudrait affiché "déconnexion" au lieu de "connexion"
||detail-offre|white-space: pre-wrap pour la description détailéle
||connexion|return url quand on exige de l'utilisateur qu'il se connecte
||carte offre|afficher le pris le plus bas de la grille tarifaire (utiliser attr calculé vue offres)

## BDD

- preciser prestations pour options et abonnement
- enlever attr remise
- interruption optien sans effet sur la facturation (bool actif dans souscription option)
- attr manquant dans avis : contexte de la visite (affaires, couple, famille, amis, solo, autre...)
- renommer option.prix en prix_hebdomadaire, abonnement.prix en prix_mensuel
  
## Todo

- Use args array technique everywhere `$_GET`, `$_POST` or `$_FILES` are used.
- fix php performance problems

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
