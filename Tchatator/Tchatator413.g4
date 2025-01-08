grammar Tchatator413;

request: req_login | req_whois;

req_login: 'login' API_KEY;

req_whois: 'whois' USER_ID;

API_KEY: UUID_V4;

USER_ID: DD+;

UUID_V4: HD4 HD4 '-' HD4 '-' HD4 '-' HD4 '-' HD4 HD4 HD4;

/** 4 hexadecimal digits */
HD4: HD HD HD HD;

/** Hexadecimal digit */
HD: [a-fA-F0-9];

/** Decimal digit */
DD: [0-9];

WS: [\p{White_Space}]+ -> skip;