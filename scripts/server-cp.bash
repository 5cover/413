#!/usr/bin/env bash

# $1 source
# $1 target

set -xeuo pipefail

mkdir -p ~/.ssh
echo "$ARTIFACT_SSH_KEY" >~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
ssh-keyscan -p 22 "$ARTIFACT_HOST" >>~/.ssh/known_hosts

readonly host="root@$ARTIFACT_HOST"

scp "$1" "$host:$2"
