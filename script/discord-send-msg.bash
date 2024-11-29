#!/usr/bin/env bash

# Send a discord message
# $1: Message content

# Env:
# DISCORD_WEBHOOK_URL

set -xeuo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

. lib/discord.bash

discord_send_msg <<< "$1"