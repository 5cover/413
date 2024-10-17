# Commandes

## Connexion au serveur (PC perso)

```bash
ssh -i .ssh/sae_rsa.pub debian@dbadmin-413.ventsdouest.dev
# ou
ssh -i .ssh/sae_rsa.pub debian@413.ventsdouest.dev
```

## Clonage du dépôt sur le serveur

```bash
cd /docker/sae/
git clone --separate-git-dir ./.git -b main --depth 1 https://github.com/5cover/413.git data
```

## Copier le script de création dans le conteneur

```bash
cd /docker/sae/data/sql
docker cp creaBDD.sql postgresdb:/docker-entrypoint-initdb.d/creaBDD.sql
```

## Atteindre le shell du conteneur postgres

```bash
docker exec -it postgresdb bash
```

## Implémentation BdD

```bash
psql -U sae -d postgres -f /creaBDD.sql
```

## Restart server (when commits to pull)
  
```bash
cd /docker/sae
cd data && git pull && cd .. && docker compose down && docker compose up -d
```
