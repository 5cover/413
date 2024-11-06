#!/bin/env bash

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

if [[ $# -ge 1 && $1 = '-f' ]]; then
    echo 'whatif'
    whatif=true
else
    whatif=false
fi

readonly outdir="../../main/html/images_utilisateur"
mkdir -p $outdir

echo 'insert into'
echo '    _image(taille, mime_subtype, legende)'
echo 'values'

cd sample_imgs

i=0
for f in *; do
    if [[ $i -gt 0 ]]; then
        echo ", -- $i"
    fi
    (( ++i ))

    ext="$(file -b --mime-type -- "$f" | cut -d/ -f2)"
    name="${f%.*}"
    echo -n "    ($(stat -c%s -- "$f"), '$ext', '${name/\'/\'\'}')"

    if [[ $whatif != true ]]; then
        cp "$f" "../$outdir/$i.$ext"
    fi
done

echo "; -- $i"
