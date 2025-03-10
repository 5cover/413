#!/usr/bin/env bash

set -eua
cd /docker/sae/data
#shellcheck source=/dev/null
. .env
set +a

sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge

for instance in main test; do
    db="sae413_$instance"
    bash sql/unite.bash $instance | sudo docker exec -iw / postgresdb psql -d postgres -v ON_ERROR_STOP=on -h localhost -wU "$DB_USER" \
        -c "drop database if exists $db" \
        -c "create database $db" \
        -c "\c $db" \
        -f -
done