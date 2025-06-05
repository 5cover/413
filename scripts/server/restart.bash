#!/usr/bin/env bash

# $1: env file contents

set -eua
cd /docker/413.ventsdouest.dev/sae/data
#shellcheck source=/dev/null
. .env
set +a

git config --global --add safe.directory /docker/413.ventsdouest.dev/sae/data
sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge

sudo docker compose down
sudo docker compose up -d
