#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

if [[ "$#" -ne 1 ]]; then
    echo "usage: $0 INSTANCE" >&2
    exit 1
fi

readonly db="sae413_$1"

bash ./unite.bash "$1" | psql -v ON_ERROR_STOP=on -h localhost -wU postgres \
    -c "drop database if exists $db" \
    -c "create database $db" \
    -c "\c $db" \
    -f -
