# Tchatator413

Un protocole d'échange de tchatator, JSON-based.

- [Rôles](#rôles)
  - [Client](#client)
  - [Professionnel](#professionnel)
  - [Administrateur](#administrateur)
- [Actions](#actions)
  - [S'identifier](#sidentifier)
    - [Forme](#forme)
    - [Retours](#retours)
      - [200 ok](#200-ok)
      - [403 access denied](#403-access-denied)
  - [Envoyer un message](#envoyer-un-message)
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

#### Forme

`login CLÉ_API`

#### Retours

##### 200 ok

```json
{ "token": "SESSION_TOKEN" }
```

##### 403 access denied

### Envoyer un message

**Rôles permis** : client, professionnel

### Obtenir les messages non lus

### Obtenir l'historique de messages (lus et émis)

### Modifier un message

### Supprimer d'un message

### Obtenir le status d'un client

### Bloquer un client

### Bannir un client

**Rôles permis** : administrateur
