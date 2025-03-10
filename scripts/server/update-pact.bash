#!/usr/bin/env bash

set -eu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main
sudo git submodule init
sudo git submodule update --remote --merge
