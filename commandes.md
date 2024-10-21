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

## Impémentation BDD

```bash
cd /docker/sae/data/sql
for f in *.sql; do
    sudo docker cp "$f" postgresdb:/"$f"
done
# Atteindre le shell du conteneur postgres
sudo docker exec -w / postgresdb bash -c 'for f in /creaBDD.sql /vuesBDD.sql /fonctions_triggers.sql
  do echo run "$f"
  psql -U sae -d postgres -f "$f"
done'

```

## Restart server (when commits to pull)
  
```bash
cd /docker/sae
cd data && git pull && cd .. && docker compose down && docker compose up -d
```
