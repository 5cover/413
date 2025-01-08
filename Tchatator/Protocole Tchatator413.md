# Tchatator413

Un protocole d'échange de tchatator, JSON-based.

- [Rôles](#rôles)
  - [Client](#client)
  - [Professionnel](#professionnel)
  - [Administrateur](#administrateur)
- [Actions](#actions)
  - [S'identifier](#sidentifier)
  - [Rechercher un compte](#rechercher-un-compte)
  - [Envoyer un message](#envoyer-un-message)
    - [Restrictions](#restrictions)
  - [Obtenir les messages non lus](#obtenir-les-messages-non-lus)
  - [Obtenir l'historique de messages (lus et émis)](#obtenir-lhistorique-de-messages-lus-et-émis)
  - [Modifier un message](#modifier-un-message)
  - [Supprimer d'un message](#supprimer-dun-message)
  - [Obtenir le status d'un client](#obtenir-le-status-dun-client)
  - [Bloquer un client](#bloquer-un-client)
  - [Bannir un client](#bannir-un-client)

## Rôles

### Client

### Professionnel

### Administrateur

Il en existe qu'un seul. Clé d'API : `ed33c143-5752-4543-a821-00a187955a28`

## Actions

### S'identifier

**Rôles permis** : *tous*

**Syntaxe** : `login CLÉ_API`

Code retour|Corps
-|-
200 ok|application/json: `{ "token": SESSION_TOKEN }`
403 forbidden|

### Rechercher un compte

**Rôles permis** : *tous*

**Syntaxe** : `whois ID_OR_EMAIL_OR_PSEUDO`

Code retour|Corps
200 ok|application/json: `{ "id": number, "email": string, "pseudo": string, "nom": string, "prenom": string, "kind": "membre" | "pro" }`
404|

### Envoyer un message

**Rôles permis** : *tous*

**Syntaxe** : `send SESSION_TOKEN DEST CONTENT`

Argument|Description
-|-
SESSION_TOKEN|Token de session
DEST|ID de compte destinataire ou email ou pseudo
CONTENT|Contenu du message

#### Restrictions

- Si le SESSION_TOKEN appartient à un client, le destinataire doit être un professionnel
- Si le SESSION_TOKEN appartient à un professionnel, le destinataire doit être un client lui ayant déja envoyé un message

### Obtenir les messages non lus

`inbox`

### Obtenir l'historique de messages (lus et émis)

### Modifier un message

### Supprimer d'un message

### Obtenir le status d'un client

### Bloquer un client

### Bannir un client

**Rôles permis** : administrateur
