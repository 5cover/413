# Implémentation Tchatator

## Interface

Mini shell

1 seule connexion possible

## Tests

Fundanmally : formulate request -> call server -> retrieve output -> assert based on protocol specificiation

should we test steps (parsing actions, running actions, formulating response) separately or together?

## Clés d'API

Forme: UUID V4.

(client = membre)

Chaque utilisateur à 0 ou 1 clé d'API qu'il peut supprimer ou regénerer.

UUID spécial : administrateur : `ed33c143-5752-4543-a821-00a187955a28`

Pour l'administrateur, le mot de passe est `314317` (bcrypt `$2y$10$YiDc/A/8DR9YSVohn7Dh9u5rb7DaiKvG/2iMRF3Xo8byNkOPEY0Sq`). Il n'est pas modifiable. Si la clé d'API admin est leak on change la clé d'API. C'est une protection temporaire si la clé d'API est leak. Le mot de passe est stocké dans le programme C statiquement sous une forme hachée.

## Fonctionnement

Séparer l'interprétation de la requête de la présentation.

Toutes les réponses se font en JSON.

Représentation du temps : UNIX timestamps (secondes).

Parsing de la requête : lex et yacc

Session token : un nombre basé sur le timestamp et la clé d'api. Stocké dans la BDD de manière à éviter les collisions. Supprimé à la déconnexion.

Table Sessions:

- Clé API
- Token
- Dernière requête
- Nombre de requêtes depuis 1 minute (default 0)
- Nombre de requêtes depuis 1 heure (default 0)

Tokens et rate limits stockés dans la mémoire du serveur dans des hashtable (voir stb_ds)

Pour la rate limit: une qté max de requêtes par minute et par heure.

Quand une requête est faite :

- Si la dernière requête était il y a plus d'1 minute, réinitializer le nombre de requêtes depuis 1 minute
- Vérifier qu'on est pas au max et l'incrémenter
- Si la dernière requête était il y a plus d'1 heure, réinitializer le nombre de requêtes depuis 1 heure
- Vérifier qu'on est pas au max et l'incrémenter

### C

- Utiliser des arena allocators

## Documentation

Markdowns convertis en PDF.

## Base de données

### Contraintes trigger

Émetteur est client XOR Récépteur est client.

L'émetteur et le client ne peuvent pas être de la même classe

## Conventions

### Doxygen multiple returns

Start with successful returns.
