#!/usr/bin/env bash

#1 Filename of the script to run with root permisions

set -euo pipefail

mkdir -p ~/.ssh
echo "$ARTIFACT_SSH_KEY" >~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
ssh-keyscan -p 22 "$ARTIFACT_HOST" >>~/.ssh/known_hosts
ssh "debian@$ARTIFACT_HOST" sudo bash <"$1"
