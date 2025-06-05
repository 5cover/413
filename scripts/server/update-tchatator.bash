#!/usr/bin/env bash

set -eu

cd /docker/413.ventsdouest.dev/sae/data
sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge

sudo killall tct413 || true

cd chattator/tct413

sudo apt-get install make -y

sudo make -B

sudo bin/tct413 -c ../../config.json
