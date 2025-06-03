# Commandes

## Installer json-c

```sh
apt install libjson-c-dev
```

## Connexion au serveur (PC perso)

```bash
ssh -i ~/.ssh/sae.rsa.pub debian@dbadmin-413.ventsdouest.dev
# ou
ssh -i ~/.ssh/sae.rsa.pub debian@413.ventsdouest.dev
```

## Clonage du dépôt sur le serveur

```bash
cd /docker/sae/
sudo git clone -b main --depth 1 https://github.com/5cover/413.git data
cd data
```

## Restart server
  
```bash
cd /docker/sae
cd data && git pull && cd .. && docker compose down && docker compose up -d
```

## PHPinfo

```bash
echo '<?php phpinfo();' > info.php
```

## See apache2 logs

```bash
docker log web
```
