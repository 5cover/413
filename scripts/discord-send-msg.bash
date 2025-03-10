#!/usr/bin/env bash

# Send a discord message
# $1: Message content

# Env:
# DISCORD_WEBHOOK_URL

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

. lib/discord.bash

# shellcheck disable=SC2119
discord_send_msg <<< "$1"