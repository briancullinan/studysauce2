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
        $update = file_get_contents(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'update_test.sh');
        $cert = "-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCxeZa5BvDqXoze
z/H7utzlkP5V//2oowU3OtGRuusG+01EgvF2uvY6r2hQ6K3VJ7Ay91LRaN4xHLxq
RG9GOPRvbZNPYUXMz4x8cYy0lhuoRmt2Rh/4UcN4mN/JlCqjoehw3K09l73apNaU
UJJlcdEG71Zi/XzOqrkVQH0v6yH5INrz2xrrVA39PY6vnkY0A0nbXOwjaUeO23cA
+uwWw0QDj2dhM4fd+ESepmzRBlXFEFt5/R2BChCSlDX9mNVgu7itdIVnvekOpalu
5kmtjnEnvvkvyiJpGzZeLF97vwQABZ0xFVTvSDDzNNCVaqlvGx74eQXx+flFyrH4
Dvv8FInTAgMBAAECggEAbiPnRizolorXIfArXO466L1zGiwkX+PL+Nqn9Kjr8wlC
iqI4+FZPOVxigNPmDcLztq2G2C8ctZ3/0HNoG0x9Zim6oijcCJ9FsHVHvUrrHyOU
zXH3pUQY6170g0VQsPxqJKDPYsopKOfjw2e9cvePoZ4nHofyTM/mzSmYh4SFMPYK
WyNRDh7QPKAjsp3Buvje/kUgysY12BPE7CQh5MbbydY5xhc/CtAueeGYOI3TbDwQ
IAzvx0yd8F8NxzfgbbWydTTaiXrr/dyRzwsuYIXnJsasuisIXuOSkKcDwYMNrMJe
SaQWiRL0wQG/3lMY9RIlofxtv72X7MdH41f0CZMeAQKBgQDVLxBzkRfAK+yOjfP9
q/QJK0Lu3krEjU2PeH7rvbaGl0nLVPz+liGnHwg2PYvHLCELl+7mZFEpCzze7Oyo
OaeIxfFG1iQEp7XImWeJmP2ZZVle4tFLDZ51+BKe6vb/e9566qyrX3lRjH9Egc+p
+jFdFwZqbkmUbyCwfEhsJ5XxaQKBgQDVHomFAIFma0v2jAD3NIF+YIQkPqE6Gqu7
n/46s9VYz6h7cQPyz0uuJxhb3/qVFIHDuMighuQtCUYHquv6KMCZLSga7bb2X6HU
4JeKbhF/3LIEpKPT3u/aDYSTZQf475uvMNekPfkvCU/TlDDoCn7Ws4MGSgM6edDz
UUcen+E92wKBgGsqgpml2YuoSP2MjJf7xeyKC9aqdmmCIvg5eYwmTUgxa05Exf1x
GS+64NTrcYXJQD9kvBqjWU1JserarUkP2pFd+CFE6sxQRoi13R+Fgg6HrTqOyOly
yjLBZxLuSQY7jaa6q9xmXUVKXHviybLH6+LA/V+pW2G7z3IIuBtudujpAoGAOJsO
zbvPouN23rpa+03/4xBBqNrVufghiwk43mCrlxY42uiussxqfow1xRldlkFHIjQv
XavWaTvgVOMKIHy4gzbiQxzGjvPhqQgqWANaNIrq4Z7VQM4jCNi0UO3xyyXhRQwF
CsPHLr4bbMgcoVVAUUiG0aHQ77QxUp9Q84plqicCgYEAhJbg4RexY6u+anKcCj42
n9d0Qd2MnZCdSaFoEdlpeiWPTAGDDSVLxJ9avJfHwn5LTdVt4Pi1bbFI4FFJV27M
yf5oMok9jc+vBFCQ7hE3Lo4xguhEdqw4+HDN03eTBTJfwIVadukLjp0ySW4bQ8DE
Zne1CAi8RtxMqtZquIsjxSg=
-----END PRIVATE KEY-----
-----BEGIN CERTIFICATE-----
MIIFTDCCBDSgAwIBAgIHJ4FrBUoJvjANBgkqhkiG9w0BAQUFADCByjELMAkGA1UE
BhMCVVMxEDAOBgNVBAgTB0FyaXpvbmExEzARBgNVBAcTClNjb3R0c2RhbGUxGjAY
BgNVBAoTEUdvRGFkZHkuY29tLCBJbmMuMTMwMQYDVQQLEypodHRwOi8vY2VydGlm
aWNhdGVzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkxMDAuBgNVBAMTJ0dvIERhZGR5
IFNlY3VyZSBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTERMA8GA1UEBRMIMDc5Njky
ODcwHhcNMTQwOTExMTU1MDE4WhcNMTUwOTE2MTg0MTAzWjBAMSEwHwYDVQQLExhE
b21haW4gQ29udHJvbCBWYWxpZGF0ZWQxGzAZBgNVBAMTEnd3dy5zdHVkeXNhdWNl
LmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALF5lrkG8OpejN7P
8fu63OWQ/lX//aijBTc60ZG66wb7TUSC8Xa69jqvaFDordUnsDL3UtFo3jEcvGpE
b0Y49G9tk09hRczPjHxxjLSWG6hGa3ZGH/hRw3iY38mUKqOh6HDcrT2Xvdqk1pRQ
kmVx0QbvVmL9fM6quRVAfS/rIfkg2vPbGutUDf09jq+eRjQDSdtc7CNpR47bdwD6
7BbDRAOPZ2Ezh934RJ6mbNEGVcUQW3n9HYEKEJKUNf2Y1WC7uK10hWe96Q6lqW7m
Sa2OcSe++S/KImkbNl4sX3u/BAAFnTEVVO9IMPM00JVqqW8bHvh5BfH5+UXKsfgO
+/wUidMCAwEAAaOCAb4wggG6MAwGA1UdEwEB/wQCMAAwHQYDVR0lBBYwFAYIKwYB
BQUHAwEGCCsGAQUFBwMCMA4GA1UdDwEB/wQEAwIFoDA0BgNVHR8ELTArMCmgJ6Al
hiNodHRwOi8vY3JsLmdvZGFkZHkuY29tL2dkczEtMTEyLmNybDBTBgNVHSAETDBK
MEgGC2CGSAGG/W0BBxcBMDkwNwYIKwYBBQUHAgEWK2h0dHA6Ly9jZXJ0aWZpY2F0
ZXMuZ29kYWRkeS5jb20vcmVwb3NpdG9yeS8wgYAGCCsGAQUFBwEBBHQwcjAkBggr
BgEFBQcwAYYYaHR0cDovL29jc3AuZ29kYWRkeS5jb20vMEoGCCsGAQUFBzAChj5o
dHRwOi8vY2VydGlmaWNhdGVzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvZ2RfaW50
ZXJtZWRpYXRlLmNydDAfBgNVHSMEGDAWgBT9rGEyk2xF1uLuhV+auud2mWjM5zAt
BgNVHREEJjAkghJ3d3cuc3R1ZHlzYXVjZS5jb22CDnN0dWR5c2F1Y2UuY29tMB0G
A1UdDgQWBBQqmflf9UgnkNXTLSVzyiTAC0G2KjANBgkqhkiG9w0BAQUFAAOCAQEA
o9NSv8aCmGzfc9EuMVrO4I3lLNGciJorJ4p0VRXSd5x/4C/VM0jdOjO4Oz6zpZx2
HgChm2+3NyfGKVnfM0V7iyQ3vVsKS7/KlgbSwUwf5D6TDu86hsl9+NsRH5IcG5IJ
6rsjaBrhIbzXAhZ7n6G/TeJO4pNS2UHrRQM4TLg2IIiJly1xDUHL3dwaz4K8VvQh
j/Lbg1Odo7YyVHIPZaV/fGmtH5B9Ojxb3AAVl1nO/vl7KUBu3dlu+9ifFItwITJA
uw7kYAZq1SQiSDVFIumg8D4UItSmie+LDbzt8aBIhHHMvAG8SwwtfUUXiFiqFoFi
9krPntAPL62+n7RXi94xOA==
-----END CERTIFICATE-----
";
        $bundle = "-----BEGIN CERTIFICATE-----
MIIE0DCCA7igAwIBAgIBBzANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMCVVMx
EDAOBgNVBAgTB0FyaXpvbmExEzARBgNVBAcTClNjb3R0c2RhbGUxGjAYBgNVBAoT
EUdvRGFkZHkuY29tLCBJbmMuMTEwLwYDVQQDEyhHbyBEYWRkeSBSb290IENlcnRp
ZmljYXRlIEF1dGhvcml0eSAtIEcyMB4XDTExMDUwMzA3MDAwMFoXDTMxMDUwMzA3
MDAwMFowgbQxCzAJBgNVBAYTAlVTMRAwDgYDVQQIEwdBcml6b25hMRMwEQYDVQQH
EwpTY290dHNkYWxlMRowGAYDVQQKExFHb0RhZGR5LmNvbSwgSW5jLjEtMCsGA1UE
CxMkaHR0cDovL2NlcnRzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvMTMwMQYDVQQD
EypHbyBEYWRkeSBTZWN1cmUgQ2VydGlmaWNhdGUgQXV0aG9yaXR5IC0gRzIwggEi
MA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC54MsQ1K92vdSTYuswZLiBCGzD
BNliF44v/z5lz4/OYuY8UhzaFkVLVat4a2ODYpDOD2lsmcgaFItMzEUz6ojcnqOv
K/6AYZ15V8TPLvQ/MDxdR/yaFrzDN5ZBUY4RS1T4KL7QjL7wMDge87Am+GZHY23e
cSZHjzhHU9FGHbTj3ADqRay9vHHZqm8A29vNMDp5T19MR/gd71vCxJ1gO7GyQ5HY
pDNO6rPWJ0+tJYqlxvTV0KaudAVkV4i1RFXULSo6Pvi4vekyCgKUZMQWOlDxSq7n
eTOvDCAHf+jfBDnCaQJsY1L6d8EbyHSHyLmTGFBUNUtpTrw700kuH9zB0lL7AgMB
AAGjggEaMIIBFjAPBgNVHRMBAf8EBTADAQH/MA4GA1UdDwEB/wQEAwIBBjAdBgNV
HQ4EFgQUQMK9J47MNIMwojPX+2yz8LQsgM4wHwYDVR0jBBgwFoAUOpqFBxBnKLbv
9r0FQW4gwZTaD94wNAYIKwYBBQUHAQEEKDAmMCQGCCsGAQUFBzABhhhodHRwOi8v
b2NzcC5nb2RhZGR5LmNvbS8wNQYDVR0fBC4wLDAqoCigJoYkaHR0cDovL2NybC5n
b2RhZGR5LmNvbS9nZHJvb3QtZzIuY3JsMEYGA1UdIAQ/MD0wOwYEVR0gADAzMDEG
CCsGAQUFBwIBFiVodHRwczovL2NlcnRzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkv
MA0GCSqGSIb3DQEBCwUAA4IBAQAIfmyTEMg4uJapkEv/oV9PBO9sPpyIBslQj6Zz
91cxG7685C/b+LrTW+C05+Z5Yg4MotdqY3MxtfWoSKQ7CC2iXZDXtHwlTxFWMMS2
RJ17LJ3lXubvDGGqv+QqG+6EnriDfcFDzkSnE3ANkR/0yBOtg2DZ2HKocyQetawi
DsoXiWJYRBuriSUBAA/NxBti21G00w9RKpv0vHP8ds42pM3Z2Czqrpv1KrKQ0U11
GIo/ikGQI31bS/6kA1ibRrLDYGCD+H1QQc7CoZDDu+8CL9IVVO5EFdkKrqeKM+2x
LXY2JtwE65/3YR8V3Idv7kaWKK2hJn0KCacuBKONvPi8BDAB
-----END CERTIFICATE-----
-----BEGIN CERTIFICATE-----
MIIEfTCCA2WgAwIBAgIDG+cVMA0GCSqGSIb3DQEBCwUAMGMxCzAJBgNVBAYTAlVT
MSEwHwYDVQQKExhUaGUgR28gRGFkZHkgR3JvdXAsIEluYy4xMTAvBgNVBAsTKEdv
IERhZGR5IENsYXNzIDIgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwHhcNMTQwMTAx
MDcwMDAwWhcNMzEwNTMwMDcwMDAwWjCBgzELMAkGA1UEBhMCVVMxEDAOBgNVBAgT
B0FyaXpvbmExEzARBgNVBAcTClNjb3R0c2RhbGUxGjAYBgNVBAoTEUdvRGFkZHku
Y29tLCBJbmMuMTEwLwYDVQQDEyhHbyBEYWRkeSBSb290IENlcnRpZmljYXRlIEF1
dGhvcml0eSAtIEcyMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAv3Fi
CPH6WTT3G8kYo/eASVjpIoMTpsUgQwE7hPHmhUmfJ+r2hBtOoLTbcJjHMgGxBT4H
Tu70+k8vWTAi56sZVmvigAf88xZ1gDlRe+X5NbZ0TqmNghPktj+pA4P6or6KFWp/
3gvDthkUBcrqw6gElDtGfDIN8wBmIsiNaW02jBEYt9OyHGC0OPoCjM7T3UYH3go+
6118yHz7sCtTpJJiaVElBWEaRIGMLKlDliPfrDqBmg4pxRyp6V0etp6eMAo5zvGI
gPtLXcwy7IViQyU0AlYnAZG0O3AqP26x6JyIAX2f1PnbU21gnb8s51iruF9G/M7E
GwM8CetJMVxpRrPgRwIDAQABo4IBFzCCARMwDwYDVR0TAQH/BAUwAwEB/zAOBgNV
HQ8BAf8EBAMCAQYwHQYDVR0OBBYEFDqahQcQZyi27/a9BUFuIMGU2g/eMB8GA1Ud
IwQYMBaAFNLEsNKR1EwRcbNhyz2h/t2oatTjMDQGCCsGAQUFBwEBBCgwJjAkBggr
BgEFBQcwAYYYaHR0cDovL29jc3AuZ29kYWRkeS5jb20vMDIGA1UdHwQrMCkwJ6Al
oCOGIWh0dHA6Ly9jcmwuZ29kYWRkeS5jb20vZ2Ryb290LmNybDBGBgNVHSAEPzA9
MDsGBFUdIAAwMzAxBggrBgEFBQcCARYlaHR0cHM6Ly9jZXJ0cy5nb2RhZGR5LmNv
bS9yZXBvc2l0b3J5LzANBgkqhkiG9w0BAQsFAAOCAQEAWQtTvZKGEacke+1bMc8d
H2xwxbhuvk679r6XUOEwf7ooXGKUwuN+M/f7QnaF25UcjCJYdQkMiGVnOQoWCcWg
OJekxSOTP7QYpgEGRJHjp2kntFolfzq3Ms3dhP8qOCkzpN1nsoX+oYggHFCJyNwq
9kIDN0zmiN/VryTyscPfzLXs4Jlet0lUIDyUGAzHHFIYSaRt4bNYC8nY7NmuHDKO
KHAN4v6mF56ED71XcLNa6R+ghlO773z/aQvgSMO3kwvIClTErF0UZzdsyqUvMQg3
qm5vjLyb4lddJIGvl5echK1srDdMZvNhkREg5L4wn3qkKQmw4TRfZHcYQFHfjDCm
rw==
-----END CERTIFICATE-----
-----BEGIN CERTIFICATE-----
MIIEADCCAuigAwIBAgIBADANBgkqhkiG9w0BAQUFADBjMQswCQYDVQQGEwJVUzEh
MB8GA1UEChMYVGhlIEdvIERhZGR5IEdyb3VwLCBJbmMuMTEwLwYDVQQLEyhHbyBE
YWRkeSBDbGFzcyAyIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MB4XDTA0MDYyOTE3
MDYyMFoXDTM0MDYyOTE3MDYyMFowYzELMAkGA1UEBhMCVVMxITAfBgNVBAoTGFRo
ZSBHbyBEYWRkeSBHcm91cCwgSW5jLjExMC8GA1UECxMoR28gRGFkZHkgQ2xhc3Mg
MiBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTCCASAwDQYJKoZIhvcNAQEBBQADggEN
ADCCAQgCggEBAN6d1+pXGEmhW+vXX0iG6r7d/+TvZxz0ZWizV3GgXne77ZtJ6XCA
PVYYYwhv2vLM0D9/AlQiVBDYsoHUwHU9S3/Hd8M+eKsaA7Ugay9qK7HFiH7Eux6w
wdhFJ2+qN1j3hybX2C32qRe3H3I2TqYXP2WYktsqbl2i/ojgC95/5Y0V4evLOtXi
EqITLdiOr18SPaAIBQi2XKVlOARFmR6jYGB0xUGlcmIbYsUfb18aQr4CUWWoriMY
avx4A6lNf4DD+qta/KFApMoZFv6yyO9ecw3ud72a9nmYvLEHZ6IVDd2gWMZEewo+
YihfukEHU1jPEX44dMX4/7VpkI+EdOqXG68CAQOjgcAwgb0wHQYDVR0OBBYEFNLE
sNKR1EwRcbNhyz2h/t2oatTjMIGNBgNVHSMEgYUwgYKAFNLEsNKR1EwRcbNhyz2h
/t2oatTjoWekZTBjMQswCQYDVQQGEwJVUzEhMB8GA1UEChMYVGhlIEdvIERhZGR5
IEdyb3VwLCBJbmMuMTEwLwYDVQQLEyhHbyBEYWRkeSBDbGFzcyAyIENlcnRpZmlj
YXRpb24gQXV0aG9yaXR5ggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQAD
ggEBADJL87LKPpH8EsahB4yOd6AzBhRckB4Y9wimPQoZ+YeAEW5p5JYXMP80kWNy
OO7MHAGjHZQopDH2esRU1/blMVgDoszOYtuURXO1v0XJJLXVggKtI3lpjbi2Tc7P
TMozI+gciKqdi0FuFskg5YmezTvacPd+mSYgFFQlq25zheabIZ0KbIIOqPjCDPoQ
HmyW74cNxA9hi63ugyuV+I6ShHI56yDqg+2DzZduCLzrTia2cyvk0/ZM/iZx4mER
dEr/VxqHD3VILs9RaRegAhJhldXRQLIQTO7ErBBDpqWeCtWVYpoNz4iCxTIM5Cuf
ReYNnyicsbkqWletNw+vHX/bvZ8=
-----END CERTIFICATE-----
";
        $bash = <<<EOSH
#!/bin/bash

mkdir /var/www
cd /var/www
yum update -y
yum install -y mysql-server httpd24 php55 php55-mysqlnd php55-pdo mod24_ssl openssl php55-mbstring php55-mcrypt php55-common php55-gd php55-xml libjpeg libpng git
git clone https://bjcullinan:Da1ddy23@bitbucket.org/StudySauce/studysauce2.git

chown -R mysql /var/lib/mysql
chgrp -R mysql /var/lib/mysql
service mysqld start
/usr/bin/mysqladmin -u root password 'MyNewPass'
echo "CREATE DATABASE studysauce; GRANT ALL ON studysauce.* TO 'study'@'localhost' IDENTIFIED BY 'itekIO^#(';" | mysql -u root --password=MyNewPass -h localhost
mysqldump -u study -h studysauce2.cjucxx5pvknl.us-west-2.rds.amazonaws.com --password=itekIO^#\( studysauce | mysql -u study --password=itekIO^#\( -h localhost studysauce

echo "
<Directory \"/var/www/html\">
    AllowOverride All
</Directory>
" >> /etc/httpd/conf/httpd.conf
sed -i "s/^;date.timezone =$/date.timezone = \"US\/Arizona\"/" /etc/php.ini |grep "^timezone" /etc/php.ini
sed -i "s/^#SSLCACertificateFile/SSLCACertificateFile/" /etc/httpd/conf.d/ssl.conf |grep "SSLCACertificateFile" /etc/httpd/conf.d/ssl.conf
sed -i "s/^SSLCertificateKeyFile/#SSLCertificateKeyFile/" /etc/httpd/conf.d/ssl.conf |grep "SSLCertificateKeyFile" /etc/httpd/conf.d/ssl.conf
echo "$cert" > /etc/pki/tls/certs/localhost.crt
echo "$bundle" > /etc/pki/tls/certs/ca-bundle.crt
rm -R /var/www/html
ln -s /var/www/studysauce2/web /var/www/html

service httpd restart
chkconfig httpd on

$update
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
        $I->wait(30);
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
        $I->checkOption('.dialogMiddle input[type="checkbox"]'); // reassociate IP even if its already assigned
        $I->wait(1);
        $I->click('Associate');
        $I->wait(1);
        $I->click('Instances');
    }
}