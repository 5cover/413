# Tchatator413

Un protocole d'échange de tchatator, JSON-based.

- [Rôles](#rôles)
  - [Client](#client)
  - [Professionnel](#professionnel)
  - [Administrateur](#administrateur)
- [Erreurs](#erreurs)
- [Actions](#actions)
  - [S'authentifier](#sauthentifier)
  - [Se déconnecter](#se-déconnecter)
  - [Rechercher un compte](#rechercher-un-compte)
  - [Envoyer un message](#envoyer-un-message)
    - [Invariants](#invariants)
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

## Erreurs

Code|Raison
-|-
401 (unauthorized)|le SESSION_TOKEN est invalide
413 (request too large)|Un des arguments est trop long
429 (too many requests)|rate limit atteinte

## Actions

Si la requête n'est pas reconnue, 400 (bad request) est renvoyé.

### S'authentifier

**Rôles permis** : *tous*

**Syntaxe** : `login CLÉ_API MDP_HASH`

Code retour|Corps|Raison
-|-|-
200|application/json: `{ "token": SESSION_TOKEN }`
403||Mot de passe incorrect
404||Clé d'API invalide

### Se déconnecter

**Rôles permis** : *tous*

**Syntaxe** : `logout SESSION_TOKEN`

Code retour|Corps|Raison
-|-|-
200|

### Rechercher un compte

**Rôles permis** : *tous*

**Syntaxe** : `whois ID_OR_EMAIL_OR_PSEUDO`

Code retour|Corps|Raison
-|-|-
200|application/json: `{ "id": number, "email": string, "pseudo": string, "nom": string, "prenom": string, "kind": "membre" | "pro" }`
404||Compte introuvable

### Envoyer un message

**Rôles permis** : *tous*

**Syntaxe** : `send SESSION_TOKEN DEST CONTENT`

Argument|Description
-|-
SESSION_TOKEN|Token de session
DEST|ID de compte destinataire ou email ou pseudo
CONTENT|Contenu du message

Code retour|Corps|Raison
-|-|-
200|
404||Compte destinataire introuvable
422||Invariant enfreint

#### Invariants

- Si le SESSION_TOKEN appartient à un client, le destinataire est un professionnel
- Si le SESSION_TOKEN appartient à un professionnel, le destinataire est un client lui ayant déja envoyé un message

### Obtenir les messages non lus

**Syntax** `inbox SESSION_TOKEN`

### Obtenir l'historique de messages (lus et émis)

### Modifier un message

### Supprimer d'un message

### Obtenir le status d'un client

### Bloquer un client

### Bannir un client

**Rôles permis** : administrateur
