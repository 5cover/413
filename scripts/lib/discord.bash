# Discord message flag: do not include any embeds when serializing this message
#readonly DMF_SUPPRESS_EMBEDS=4

# Send a message to the Discord webhook.
# $1: integer: message flags bitfield (default 0)
# stdin: string: the message to send
discord_send_msg() {
    jq --slurp --raw-input --compact-output --arg flags "${1-0}" '{content:.,$flags}' |
        curl --data @- "$DISCORD_WEBHOOK_URL" \
            --header "Accept: application/json" \
            --header "Content-Type: application/json"
}