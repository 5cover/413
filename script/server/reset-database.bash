#!/usr/bin/env bash

set -xeu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main

cd sql
sudo docker cp . postgresdb:

fargs=()
for f in schemas.sql \
    pact/types.sql pact/crea.sql pact/fonctions.sql pact/vues.sql pact/triggers.util.sql pact/triggers/*.sql pact/bigdata.sql pact/data.sql pact/images.sql pact/offre/*.sql \
    tchattator/crea.sql; do
    fargs+=(-f "$f")
done

sudo docker exec -w / postgresdb psql -v ON_ERROR_STOP=on -U sae -d postgres --single-transaction "${fargs[@]}"
