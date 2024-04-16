#!/bin/sh
echo "### RSYNC ###"
rsync  -e 'ssh -X' -S -av ./ tetetoto@172.16.128.5:/var/www/retarrosoir  --include="public/.htaccess" --include=".env"  --include=".env.vm.local" --exclude-from=".gitignore" --exclude=".*"
echo "### CONNECTION SSH ###"
ssh tetetoto@172.16.128.5 -o "StrictHostKeyChecking=no" << 'eof'
echo "### CD cd /var/www/jonchere ###"
cd /var/www/retarrosoir
echo "### RENOMMAGE .env.local ###"
mv .env.vm.local .env.local
echo "### COMPOSER INSTALL ###"
composer install
echo "### DOCTRINE MIGRATION ###"
symfony console --no-interaction doctrine:database:create --if-not-exists
symfony console --no-interaction doctrine:migrations:migrate