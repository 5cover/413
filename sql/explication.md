# Explication du nouveau modèle

Auteur : Raphaël

J'ai besoin d'une base de donnée de test pour le chattator.

Donc je pars sur une réorganisation de l'arborescence pour le SQL, afin de montrer qu'on a plusieurs instances de la base de données

- schemas
  - pact
    - crea.sql
    - shared_data.sql : peuplement partagé (données identiques pour toutes les instances)
    - ...
  - tchatator
    - crea.sql
    - ...
- instances
  - main: BDD de production
    - data.sql
    - offre
      - *.sql
  - test: BDD de test
    - data.sql
    - ...
