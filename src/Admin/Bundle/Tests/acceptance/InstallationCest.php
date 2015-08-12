<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\User;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class EmailsCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class InstallationCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function tryInstallation(AcceptanceTester $I)
    {
        $I->amOnUrl('https://aws.amazon.com/');
        $I->wait(1);
        $I->moveMouseOver('div[data-dropdown="aws-nav-dropdown-account"]');
        $I->click('AWS Management Console');
        $I->fillField('input[name="email"]', 'admin@studysauce.com');
        $I->fillField('input[name="password"]', '2StudyBetter!');
        $I->click('#signInSubmit-input');
        $I->wait(1);
        $I->click('a[data-service-id="ec2"]');
        $I->wait(1);
        $I->click('Launch Instance');
        $I->wait(1);
        $I->click('button[id*="selectButton"]');
        $I->wait(1);
        $I->click('Configure Instance Details');
        $I->wait(1);
        $I->click('//span[contains(.,"Advanced Details")]');
        $I->wait(1);
        $bash = <<<EOSH
#!/bin/bash

mkdir /var/www
cd /var/www
yum update -y
yum install -y mysql-server httpd24 php55 php55-mysqlnd php55-pdo mod24_ssl openssl php55-mbstring php55-mcrypt php55-common php55-gd php55-xml libjpeg libpng git
git clone https://bjcullinan:Da1ddy23@bitbucket.org/StudySauce/studysauce2.git
rm -R /var/www/html
ln -s /var/www/studysauce2/web /var/www/html
chmod o+x /var/www/studysauce2/update_test.sh
killall mysqld
chown -R mysql /var/lib/mysql
chgrp -R mysql /var/lib/mysql
echo "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('MyNewPass');" > /tmp/mysql-init
service mysqld start
service mysqld stop
mysqld_safe --init-file=/tmp/mysql-init &
echo "CREATE DATABASE studysauce; GRANT ALL ON studysauce.* TO 'study'@'localhost' IDENTIFIED BY 'itekIO^#(';" | mysql -u root --password=MyNewPass -h localhost
mysqldump -u study -h studysauce2.cjucxx5pvknl.us-west-2.rds.amazonaws.com --password=itekIO^#\( studysauce | mysql -u study --password=itekIO^#\( -h localhost studysauce
sed -i "s/^;date.timezone =$/date.timezone = \"US\/Arizona\"/" /etc/php.ini |grep "^timezone" /etc/php.ini
cd /var/www/studysauce2
./update_test.sh
echo "
<Directory \"/var/www/html\">
    AllowOverride All
</Directory>
" >> /etc/httpd/conf/httpd.conf
service httpd restart
chkconfig httpd on
EOSH;

        $I->fillField('textarea', $bash);
        $I->click('Add Storage');
        $I->wait(1);
        $I->click('Tag Instance');
        $I->wait(1);
        $I->click('Configure Security Group');
        $I->wait(1);
        $I->click('//label[contains(.,"existing")]');
        $I->wait(1);
        $I->click('//tr[contains(.,"sg-a416bfc1")]');
        $I->wait(1);
        // add security group
        // $I->click('//tr[contains(.,"sg-a416bfc1")]//a[contains(.,"Copy to new")]');
        // $I->wait(1);
        $I->click('Review and Launch');
        $I->wait(1);
        $I->click('Launch');
        $I->wait(1);
        $I->click('.dialogMiddle input[type="checkbox"]');
        $I->click('Launch Instances');
        $I->wait(3);
        $instanceId = $I->grabTextFrom('a[href*="Instances:search"]');
        $I->click('View Instances');
        $I->wait(20);
        // change public IP
        $I->click('Elastic IPs');
        $I->wait(1);
        $I->click('//tr[contains(.,"52.24.94.177")]');
        $I->wait(1);
        $I->click('//button[contains(.,"Actions")]');
        $I->wait(1);
        $I->click('//div[contains(.,"Associate Address")]');
        $I->wait(1);
        $I->fillField('.dialogMiddle input[placeholder*="Search"]', $instanceId);
        $I->wait(1);
        $I->click('.dialogMiddle input[placeholder*="Search"]');
        $I->wait(1);
        $I->click('//div[contains(.,"' . $instanceId . '")]');
        $I->wait(1);
        $I->click('Associate');
        $I->wait(1);
        $I->click('Instances');
    }
}