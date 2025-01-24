#!/usr/bin/env bash

set -xeu

cd /docker/sae/data

# shellcheck disable=SC1091
. include/.env

sudo git fetch --all
sudo git reset --hard origin/main

cd sql
sudo docker cp . postgresdb:

fargs=()
for f in schemas.sql \
    pact/types.sql pact/crea.sql pact/fonctions.sql pact/vues.sql pact/triggers.util.sql pact/triggers/*.sql pact/bigdata.sql pact/data.sql pact/images.sql pact/offre/*.sql \
    tchattator/crea.sql tchattator/vues.sql; do
    fargs+=(-f "$f")
done

sudo docker exec -w / postgresdb psql -v ON_ERROR_STOP=on -U "$DB_USER" -d "$DB_NAME" --single-transaction "${fargs[@]}"
