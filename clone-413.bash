#!/usr/bin/env bash

is_root() {
    return "$(id -u)"
}

has_sudo() {
    local prompt

    prompt=$(sudo -nv 2>&1)
    # shellcheck disable=SC2181
    if [ $? -eq 0 ]; then
        echo has_sudo__pass_set
    elif echo "$prompt" | grep -q '^sudo:'; then
        echo has_sudo__needs_pass
    else
        echo no_sudo
    fi
}

set -eu

readonly TEAM='413/'

echo "Ce script va créer le dossier '$TEAM' dans le dossier courant ($(pwd))."

if [[ -e $TEAM ]]; then
    >&2 echo "erreur: le dossier '$TEAM' exite déjà."
    >&2 echo "indice: pour le supprimer: rm -rf '$TEAM'"
    exit 1
fi

if is_root || [[ $(has_sudo) != no_sudo ]]; then
    >&2 echo 'Installation des packages PDO pgsql'
    apt install php-pgsql php-pdo-pgsql
else 
    >&2 echo "Packages PDO pgsql non insallés (pas d'accès sudo)"
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

cat >.vscode/launch.json <<'EOF'
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

cat >main/include/.env <<'EOF'
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

read -p "C'est bon. Ouvrir VSCode dans le dossier? (o/n) " -rn 1
echo
set -x
if [[ $REPLY =~ ^[Oo]$ ]]; then
    code .
fi
