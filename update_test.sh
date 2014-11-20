#! /bin/bash

cd /var/www/studysauce2/;
sudo php app/console cache:clear --env=test;
sudo php app/console doctrine:generate:entities StudySauceBundle;
sudo php app/console doctrine:schema:update --force --env=test;
sudo php app/console assets:install --env=test --symlink;
sudo php app/console assetic:dump --env=test;
sudo chown www-data:www-data -R app/cache/
sudo chown www-data:www-data -R app/logs/