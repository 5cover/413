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

## docker-compose.yml

```yml
services:
   postgresdb:
      container_name: postgresdb
      image: postgres:16.0
      volumes:
         - ./data/postgres:/var/lib/postgresql/data
      ports:
         - 0.0.0.0:${PGDB_PORT}:5432
      environment:
         POSTGRES_PASSWORD: ${DB_ROOT_PASSWORD}
         POSTGRES_USER: ${DB_USER}
         POSTGRES_DB: ${DB_NAME}
         PGDATA: /var/lib/postgresql/data/pgdata
      networks:
         - sae

   web:
      container_name: web
      build: .
      #ports:
      #   - ${PORT_WEB}:80
      volumes:
         - ./data/html:/var/www/html
         - ./data/include:/var/www/include
         - ./data/cfg:/usr/local/etc/php
      environment:
         VIRTUAL_HOST: ${EQUIPE}.ventsdouest.dev
         LETSENCRYPT_HOST: ${EQUIPE}.ventsdouest.dev
         LETSENCRYPT_EMAIL: gildas@bigpapoo.com
      networks:
         - sae
         - webproxy_back
      depends_on:
         - postgresdb

   pgadmin:
      image: dpage/pgadmin4
      restart: always
      environment:
         VIRTUAL_HOST: dbadmin-${EQUIPE}.ventsdouest.dev
         LETSENCRYPT_HOST: dbadmin-${EQUIPE}.ventsdouest.dev
         LETSENCRYPT_EMAIL: gildas@bigpapoo.com
         PGADMIN_DEFAULT_EMAIL: ${EQUIPE}@dbadmin-sae.com
         PGADMIN_DEFAULT_PASSWORD: ${PGADMIN_PASSWORD}
         PGADMIN_DISABLE_POSTFIX: true
      networks:
         - sae
         - webproxy_back
      depends_on:
         - postgresdb

networks:
   sae:
   webproxy_back:
      external: true
```

## PHPinfo

```bash
echo '<?php phpinfo();' > info.php
```
