#!/usr/bin/env bash

# $1 filename of the script to run with root permisions

set -xeuo pipefail

mkdir -p ~/.ssh
echo "$ARTIFACT_SSH_KEY" >~/.ssh/id_rsa
chmod 600 ~/.ssh/id_rsa
ssh-keyscan -p 22 "$ARTIFACT_HOST" >>~/.ssh/known_hosts

readonly host="root@$ARTIFACT_HOST" script="$1"
shift

# shellcheck disable=SC2029
ssh "$host" 'sudo bash -s' < "$script" "$@"
