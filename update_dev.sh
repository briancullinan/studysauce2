#! /bin/bash

cd /var/www/studysauce2/;
sudo php app/console cache:clear --env=dev;
sudo php app/console doctrine:generate:entities StudySauceBundle;
sudo php app/console doctrine:generate:entities Course1;
sudo php app/console doctrine:generate:entities Course2;
sudo php app/console doctrine:generate:entities Course3;
sudo php app/console doctrine:schema:update --force --env=dev;
sudo php app/console assets:install --env=dev --symlink;
sudo php app/console assetic:dump --env=dev;
sudo chown www-data:www-data -R app/cache/
sudo chown www-data:www-data -R app/logs/
sudo chown www-data:www-data -R src/Admin/Bundle/Tests
sudo chown www-data:www-data -R app/logs/
sudo chown www-data:www-data -R src/Admin/Bundle/Tests/_output/
sudo chown www-data:www-data -R src/Admin/Bundle/Resources/public/results/