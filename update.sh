#! /bin/bash

cd /var/www/Symfony/;
sudo php app/console cache:clear --env=prod;
sudo php app/console doctrine:generate:entities StudySauceBundle;
sudo php app/console doctrine:generate:entities Course1;
sudo php app/console doctrine:generate:entities Course2;
sudo php app/console doctrine:generate:entities Course3;
sudo php app/console doctrine:schema:update --force --env=prod;
sudo php app/console assets:install --env=prod --symlink;
sudo php app/console assetic:dump --env=prod;
sudo chown www-data:www-data -R app/cache/prod/
sudo chown www-data:www-data -R app/logs/
sudo chown www-data:www-data -R src/Admin/Bundle/Tests