#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

cat_struct=true
cat_data=true

case "${1-}" in
struct) cat_data=false ;;
data) cat_struct=false ;;
esac

{
    echo 'begin;'
    if [[ "$cat_struct" = true ]]; then
        cat schemas.sql \
            pact/types.sql pact/crea.sql pact/fonctions.sql pact/vues.sql pact/triggers.util.sql pact/triggers/*.sql \
            tchattator/crea.sql
    fi
    if [[ "$cat_data" = true ]]; then
        cat pact/bigdata.sql pact/data.sql pact/images.sql pact/offre/*.sql
    fi
    echo 'commit;'
} | xsel -ib
