#!/bin/env bash 
set -eu
cd "$(dirname "${BASH_SOURCE[0]}")"

# Concateneates the SQL files in the correct order to standard output.

if [[ "$#" -ne 1 ]]; then
    echo "usage: $0 INSTANCE" >&2
    exit 1
fi
readonly subunite="$PWD/instances/$1/unite.bash"
if ! [[ -f "$subunite" ]]; then
    echo "error: no such instance: '$1'" >&2
    exit 2
fi

echo 'begin;'

cd schemas
cat schemas.sql
cd pact
cat types.sql crea.sql fonctions.sql vues.sql triggers.util.sql triggers/*.sql shared_data.sql
cd ../tchatator
cat crea.sql fonctions.sql vues.sql
# shellcheck source=/dev/null
. "$subunite"

echo 'commit;'
