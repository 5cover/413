# Implémentation Tchatator

## BDD

Schéma séparé (orgnanisation)

Table message, table discussion?

Blocage, banissement

Protocole d'échange : JSON

Message:

- émetteur
- destinataire
- existe?
- lu?
- contenu
- envoyé le

## Paramètres

Nom|Description|Valeur par défaut
-|-|-
messages_par_page|Nombre maximal de messages par page dans l'historique|20
longeur_max_message|Longueur max. d'un message (caractères)|1000
minute_rate_limit|Nombre max. de requêtes par minute|12
hour_rate_limit|Nombre max. de requêtes par heure|90
...

## Erreurs

?: à rechercher

Message|Status
Rate limit dépassée. Prochaine requête dans {temps_restant}|?

## Clés d'API

Forme: UUID V4.

(client = membre)

Chaque utilisateur à 0 ou 1 clé d'API qu'il peut supprimer ou regénerer.

UUID spécial : administrateur : `ed33c143-5752-4543-a821-00a187955a28`

## Fonctionnement

Séparer l'interprétation de la requête de la présentation.

Toutes les réponses se font en JSON.

Représentation du temps : UNIX timestamps (secondes).

## Documentation

Markdowns convertis en PDF.

## Base de données

### Contraintes trigger

Émetteur est client XOR Récépteur est client.

L'émetteur et le client ne peuvent pas être de la même classe
