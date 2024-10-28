#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

readonly outdir="../../main/images_utilisateur"
mkdir -p $outdir

echo 'insert into'
echo '    _image(taille, mime_type, legende)'
echo 'values'

cd sample_imgs

i=0
for f in *; do
    if [[ "$((i++))" -gt 0 ]]; then
        echo ,
    fi
    ext="$(file -b --mime-type -- "$f" | cut -d/ -f2)"
    name="${f%.*}"
    echo -n "    ($(stat -c%s -- "$f"), '$ext', '${name/\'/\'\'}')"
    cp "$f" "../$outdir/$i.$ext"
done

echo ';'
