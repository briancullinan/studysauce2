#! /bin/bash

cd /var/www/Symfony/;
sudo php app/console cache:clear --env=test;
sudo php app/console doctrine:generate:entities StudySauceBundle;
sudo php app/console doctrine:schema:update --force;
sudo php app/console assets:install --env=test --symlink;
sudo php app/console assetic:dump --env=test;
sudo chown www-data:www-data -R app/cache/test/
