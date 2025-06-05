#!/usr/bin/env bash

set -eu

cd /docker/413.ventsdouest.dev/sae/data
git config --global --add safe.directory /docker/413.ventsdouest.dev/sae/data
sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge
