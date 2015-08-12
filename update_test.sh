#! /bin/bash

cd /var/www/studysauce2/;
sudo php app/console cache:clear --env=test;
sudo php app/console doctrine:generate:entities StudySauceBundle;
sudo php app/console doctrine:generate:entities Course1;
sudo php app/console doctrine:generate:entities Course2;
sudo php app/console doctrine:generate:entities Course3;
sudo php app/console doctrine:schema:update --force --env=test;
sudo php app/console assets:install --env=test --symlink;
sudo php app/console assetic:dump --env=test;
sudo chown apache:apache -R app/cache/
sudo chown apache:apache -R app/logs/
sudo chown apache:apache -R src/Admin/Bundle/Tests
sudo chown apache:apache -R app/logs/
sudo chown apache:apache -R src/Admin/Bundle/Tests/_output/
sudo chown apache:apache -R src/Admin/Bundle/Resources/public/results/
