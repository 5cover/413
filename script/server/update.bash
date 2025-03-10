#!/usr/bin/env bash

set -xeu

cd /docker/sae/data
sudo git fetch --all
sudo git reset --hard origin/main
