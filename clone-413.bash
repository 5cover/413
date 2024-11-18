#!/usr/bin/env bash

set -eu

readonly TEAM='413/'

echo "Ce script va créer le dossier '$TEAM' dans le dossier courant ($(pwd))."

if [[ -e $TEAM ]]; then
    >&2 echo "erreur: le dossier '$TEAM' exite déjà."
    >&2 echo "indice: pour le supprimer: rm -rf '$TEAM'"
    exit 1
fi

read -p "Continuer? (o/n) " -rn 1
echo
if ! [[ $REPLY =~ ^[Oo]$ ]]; then
    exit
fi

mkdir $TEAM
cd $TEAM

git clone https://github.com/5cover/413.git --depth 1 -b main main

git clone https://github.com/5cover/413.git --depth 1 -b support support

mkdir .vscode

> .vscode/launch.json cat <<'EOF'
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Launch Built-in web server",
      "type": "php",
      "request": "launch",
      "runtimeArgs": [
        "-dxdebug.mode=debug",
        "-dxdebug.start_with_request=yes",
        "-c",
        "${workspaceFolder}/main/cfg/php.ini",
        "-S",
        "localhost:8888",
        "-t",
        "${workspaceFolder}/main/html",
        "-d include_path=${workspaceFolder}/main/include",
        "-d zend.assertions=1"
      ],
      "program": "",
      "cwd": "${workspaceFolder}/main/",
      "port": 9003,
      "serverReadyAction": {
        "pattern": "Development Server \\(http://localhost:([0-9]+)\\) started",
        "uriFormat": "http://localhost:%s",
        "action": "openExternally"
      }
    }
  ]
}
EOF

> main/include/.env cat <<'EOF'
EQUIPE=413
PGDB_PORT=5432
MARIADB_PORT=3306
DB_USER=sae
DB_NAME=sae
DB_ROOT_PASSWORD=bib3loTs-CRues-rdv
MARIADB_PASSWORD=enterrer-couLo1rs-sound
PGADMIN_PASSWORD=parfois-appel0ns-streSSer
EOF

code --install-extension DEVSENSE.profiler-php-vscode

sudo apt install php-pgsql php-pdo-pgsql

read -p "C'est bon. Ouvrir VSCode dans le dossier? (o/n) " -rn 1
echo
set -x
if [[ $REPLY =~ ^[Oo]$ ]]; then
    code .
fi
