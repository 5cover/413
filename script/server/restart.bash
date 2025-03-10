#!/usr/bin/env bash

set -xeu

cd /docker/sae
docker compose down
docker compose up -d
