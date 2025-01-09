# Tchatator413

Un protocole d'échange de tchatator, JSON-based.

<!-- omit from toc -->
## Sommaire

- [Fondamentaux](#fondamentaux)
- [Rôles](#rôles)
  - [Client](#client)
  - [Professionnel](#professionnel)
  - [Administrateur](#administrateur)
- [Erreurs communes](#erreurs-communes)
- [Actions](#actions)
  - [`login` : s'authentifier](#login--sauthentifier)
  - [`logout` : se déconnecter](#logout--se-déconnecter)
  - [`whois` : rechercher un compte](#whois--rechercher-un-compte)
  - [`send` : envoyer un message](#send--envoyer-un-message)
    - [Invariants](#invariants)
  - [`motd` : obtenir les messages reçus non lus](#motd--obtenir-les-messages-reçus-non-lus)
    - [Exemple de réponse](#exemple-de-réponse)
    - [Problèmes possibles](#problèmes-possibles)
  - [`inbox` : obtenir les messages reçus](#inbox--obtenir-les-messages-reçus)
    - [Exemple de réponse](#exemple-de-réponse-1)
    - [Problèmes possibles](#problèmes-possibles-1)
  - [`outbox` : obtenir les messages envoyés](#outbox--obtenir-les-messages-envoyés)
    - [Exemple de réponse](#exemple-de-réponse-2)
    - [Problèmes possibles](#problèmes-possibles-2)
  - [`edit` : modifier un message](#edit--modifier-un-message)
    - [Invariants](#invariants-1)
  - [`rm` : supprimer un message](#rm--supprimer-un-message)
    - [Invariants](#invariants-2)
  - [`block` : bloquer un client](#block--bloquer-un-client)
    - [Invariants](#invariants-3)
  - [`unblock`: débloquer un client](#unblock-débloquer-un-client)
    - [Invariants](#invariants-4)
  - [`ban` : bannir un client](#ban--bannir-un-client)
    - [Invariants](#invariants-5)
  - [`unban` : débannir un client](#unban--débannir-un-client)
    - [Invariants](#invariants-6)

## Fondamentaux

Le serveur reçoit une requête sous la forme d'une liste JSON:

```json
[
  {
    "do": "login",
    "with": {
      "api_key": "...",
      "mdp_hash": "..."
    }
  }
]
```

La liste peut contenir plusieurs actions.

Une forme raccourcie est possible pour les requêtes d'1 action :

```json
{
  "do": "login",
  "with": {
    "api_key": "...",
    "mdp_hash": "..."
  }
}
```

(l'objet est implicitement englobé dans une liste d'1 élément)

À la récéption de la liste vide `[]`, rien ne se passe et `[]` est renvoyé.

Il renvoie une liste JSON comme tel, transformant effectivement chaque action dans la liste en entrée par son résultat.

```json
[
  {
    "status": 200,
    "has_next_page": false,
    "body": {
      "token": "token"
    }
  }
]
```

La propriété "has_next_page" indique que le résultat est paginé et que le prochain numéro de page est valide (et donc que la page actuelle n'est pas la dernière et elle contient le nombre maximum d'éléments).

## Rôles

### Client

Aussi appelé membre.

### Professionnel

### Administrateur

Il en existe qu'un seul. Clé d'API : `ed33c143-5752-4543-a821-00a187955a28` (secret)

## Erreurs communes

Code|Raison
-|-
403 (forbidden)|L'utilisateur actuel n'est pas autorisé à faire cette action
413 (request too large)|Un des arguments est trop long
422 (unprocessable content)|Invariant enfreint
429 (too many requests)|Rate limit atteinte

## Actions

Si la requête n'est pas reconnue, 400 (bad request) est renvoyé.

### `login` : s'authentifier

**Rôles** : *tous*

Argument|Type|Description
-|-|-
api_key|UUID V4|Clé d'API
mdp_hash|string|Mot de passe haché

Crée une session.

Code retour|Corps|Raison
-|-|-
200|`{ "token": string }`
403||Mot de passe incorrect
404||Clé d'API invalide

### `logout` : se déconnecter

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session

Supprime une session.

Code retour|Corps|Raison
-|-|-
200|
401||le *token* est invalide

### `whois` : rechercher un compte

**Rôles** : *tous*

Obtient les informations d'un compte à partir d'unee de ses clés candidates (ID, pseudo, e-mail).

Permet également de savoir si un compte est en ligne sur le service.

Argument|Type|Description
-|-|-
user|Clé de compte (ID, pseudo, e-mail)|Identifie l'utilisateur à rechercher

Code retour|Corps|Raison
-|-|-
200|`{ "id": integer, "email": string, "pseudo": string, "nom": string, "prenom": string, "kind": "membre" | "pro", "online": bool }`
404||Compte introuvable

### `send` : envoyer un message

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session
dest|Clé de compte (ID, pseudo, e-mail)|Identifie le compte destinataire
content|string|contenu du message

Envoie un message.

Code retour|Corps|Raison
-|-|-
200|
401||le *token* est invalide
403||utilisateur actuel bloqué ou banni
404||Compte *dest* introuvable

#### Invariants

- Le destinataire est différent de l'émetteur
- Si le token appartient à un client, le destinataire est un professionnel
- Si le token appartient à un professionnel, le destinataire est un client lui ayant déja envoyé un message
- Le contenu du message ne doit pas être plus long que la limite.

### `motd` : obtenir les messages reçus non lus

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session

Obtient la liste des messages non lus, ordonnées par date d'envoi (plus ancien au plus récent).

Code retour|Corps|Raison
-|-|-
200|Liste de messages

#### Exemple de réponse

```json
{
  "status": 200,
  "body": [
    {
      "id": 55,
      "sent_at": "1736355387",
      "edited_at": "1736355387",
      "read": false,
      "deleted": false,
      "content": "Bonjour. j'ai une question.",
      "sender": 17,
      "recipient": 3
    },
    {
      "id": 56,
      "sent_at": "1736355397",
      "edited_at": "1736355397",
      "read": false,
      "deleted": false,
      "content": "Bonjour. j'ai une question aussi (je suis pas la même personne).",
      "sender": 16,
      "recipient": 3
    }
  ]
}
```

#### Problèmes possibles

- Si il y a des milliers de messsages non lus, la réponse pourrait être énorme. Implémenter une pagination.
- Il y a des informations redondantes (read, recipient). Pour l'instant on les garde par souci de simplicité mais si il y a des problèmes de performance on les enlèvera.

### `inbox` : obtenir les messages reçus

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session
page|integer|Numéro de page (1-based)

Obtient l'historique des messages reçus, avec pagination.

Code retour|Corps|Raison
-|-|-
200|Liste de messages
404||Numéro de page invalide

#### Exemple de réponse

```json
{
  "status": 200,
  "has_next_page": true,
  "body": [
    {
      "id": 55,
      "sent_at": "1736355387",
      "edited_at": "1736355387",
      "read": true,
      "deleted": false,
      "content": "Bonjour. j'ai une question.",
      "sender": 17,
      "recipient": 3
    },
    {
      "id": 56,
      "sent_at": "1736355397",
      "edited_at": "1736355397",
      "read": false,
      "deleted": false,
      "content": "Bonjour. j'ai une question aussi (je suis pas la même personne).",
      "sender": 16,
      "recipient": 3
    }
    // more...
  ]
}
```

#### Problèmes possibles

- Information redondante : recipient

### `outbox` : obtenir les messages envoyés

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session
page|integer|Numéro de page (1-based)

Obtient l'historique des messages envoyés, avec pagination.

Code retour|Corps|Raison
-|-|-
200|Liste de messages
404||Numéro de page invalide

#### Exemple de réponse

```json
{
  "status": 200,
  "has_next_page": true,
  "body": [
    {
      "id": 57,
      "sent_at": "1736355487",
      "edited_at": "1736355487",
      "read": false,
      "deleted": false,
      "content": "Bonjour, quelle est votre question?",
      "sender": 3,
      "recipient": 17
    },
    {
      "id": 58,
      "sent_at": "1736355497",
      "edited_at": "1736355497",
      "read": true,
      "deleted": false,
      "content": "Bonjour, quelle est votre question? (je vous crois)",
      "sender": 3,
      "recipient": 16
    }
    // more...
  ]
}
```

#### Problèmes possibles

- Information redondante : sender

### `edit` : modifier un message

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session
msg_id|integer|ID du message à modifier
content|string|Nouveau contenu du message

Modifie un message.

Code retour|Corps|Raison
-|-|-
200|
403||utilisateur actuel bloqué ou banni
404||Message introuvable

#### Invariants

- L'utilisateur actuel est soit l'administrateur, soit l'émetteur du message.
- Le nouveau contenu du message ne doit pas être plus long que la limite.

### `rm` : supprimer un message

**Rôles** : *tous*

Argument|Type|Description
-|-|-
token|string|Token de session
msg_id|integer|ID du message à modifier

Supprime un message.

Code retour|Corps|Raison
-|-|-
200|
404||Message introuvable

#### Invariants

- L'utilisateur actuel est soit l'administrateur, soit l'émetteur du message.

### `block` : bloquer un client

**Rôles** : professionnel, administrateur

Argument|Type|Description
-|-|-
token|string|Token de session
user|Clé de compte utilisateur (ID, pseudo, e-mail)|Identifie le client à bloquer (la cible)

Bloque un client pendant une durée limitée.

Si l'utilisateur actuel est un professionnel, empêche la cible d'envoyer ou de modifier tout message s'adressant à celui-ci.

Si l'utilisateur actuel est l'administrateur, empêche la cible d'envoyer ou de modifier tout message.

Code retour|Corps|Raison
-|-|-
200|
404||Utilisateur introuvable

#### Invariants

- La cible est un client
- La cible n'est pas déjà bloquée par cet utilisateur

### `unblock`: débloquer un client

**Rôles** : professionnel, administrateur

Argument|Type|Description
-|-|-
token|string|Token de session
user|Clé de compte utilisateur (ID, pseudo, e-mail)|Identifie le client à débloquer (la cible)

Débloque un client avant l'expiration de son blocage.

Code retour|Corps|Raison
-|-|-
200|
404||Utilisateur introuvable

#### Invariants

- Si l'utilisateur actuel est un professionnel, la cible a été bloquée par celui-ci. Cela signifie que l'administrateur peut intervenir sur les blocages d'un professionnel, mais pas les autres professionnels.

### `ban` : bannir un client

**Rôles** : professionnel, administrateur

Argument|Type|Description
-|-|-
token|string|Token de session
user|Clé de compte utilisateur (ID, pseudo, e-mail)|Identifie le client à bannir (la cible)

Bannit un client.

Si l'utilisateur actuel est un professionnel, empêche la cible d'envoyer ou de modifier tout message s'adressant à celui-ci.

Si l'utilisateur actuel est l'administrateur, empêche la cible d'envoyer ou de modifier tout message.

Code retour|Corps|Raison
-|-|-
200|
404||Utilisateur introuvable

#### Invariants

- La cible est un client
- La cible n'a pas déjà été bannie par cet utilisateur

### `unban` : débannir un client

**Rôles** : profesionnel, administrateur

Argument|Type|Description
-|-|-
token|string|Token de session
user|Clé de compte utilisateur (ID, pseudo, e-mail)|Identifie le client à débannir (la cible)

Débannit un client.

Code retour|Corps|Raison
-|-|-
200|
404||Utilisateur introuvable

#### Invariants

- Si l'utilisateur actuel est un professionnel, la cible a été bannie par celui-ci. Cela signifie que l'administrateur peut intervenir sur les blocages d'un professionnel, mais pas les autres professionnels.
