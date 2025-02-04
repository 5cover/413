# Rendu

Lien vidéo présentation : <https://youtu.be/OO3e9Fy0BSw>

Code source : <https://github.com/5cover/413> (brache main, dossier "chattator")

<!-- omit from toc -->
## Sommaire

- [Compilation \& exécution](#compilation--exécution)
  - [1. Clonage du dépôt GitHub](#1-clonage-du-dépôt-github)
  - [2. Serveur (`tct413`)](#2-serveur-tct413)
  - [3. Client (`tct`)](#3-client-tct)
- [Répartition du travail](#répartition-du-travail)

## Compilation & exécution

### 1. Clonage du dépôt GitHub

Note: une copie du code du Tchatator est disponible dans l'archive `chattator.tar.gz` ci-jointe.

```sh
git clone https://github.com/5cover/413.git
cd 413
```

### 2. Serveur (`tct413`)

```sh
cd chattator/tct413
```

#### Compilation

```sh
make
```

#### Exécution

```sh
bin/tct413
```

Note&nbsp;: une interface en ligne de commande est dispnible, essayez `bin/tct413 --help`

#### Tests

```sh
make test
```

### 3. Client (`tct`)

```sh
cd chattator/tct
```

#### Compilation

```sh
make
```

#### Exécution

```sh
bin/tct
```

#### Tests

*À venir...*

## Répartition du travail

| Tâche                                                   | Raphaël BARDINI | Romain GRANDCHAMP |
| ------------------------------------------------------- | --------------- | ----------------- |
| Écriture de la spécification du protocole Tchatator413 | 100&nbsp;%      | 0&nbsp;%          |
| Mise en place sockets serveur                           | 50&nbsp;%       | 50&nbsp;%         |
| Mise en place sockets client                            | 50&nbsp;%       | 50&nbsp;%         |
| Suite de tests automatisée                              | 100&nbsp;%      | 0&nbsp;%          |
| Makefiles                                               | 100&nbsp;%      | 0&nbsp;%          |
| Requêtes SQL avec libpq                                 | 100&nbsp;%      | 0&nbsp;%          |
| Lecture et écriture de JSON avec json-c                 | 100&nbsp;%      | 0&nbsp;%          |
| Documentation Doxygen                                   | 100&nbsp;%      | 0&nbsp;%          |
| Implémentation du protocole                             | 80&nbsp;%       | 20&nbsp;%         |
| Interface client                                        | 25&nbsp;%       | 75&nbsp;%         |
