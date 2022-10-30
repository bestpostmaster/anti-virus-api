This API provides a complete system for authenticating via JWT and uploading files.
An admin exists, and he has endpoints that allow him to manage users
After the upload, an anti-virus scan is performed asynchronously by ClamAV. It is therefore important to install ClamAV and configure it correctly

Installation
-------------------------------------------------------------------
composer install
<!on debian 10!> sudo composer require ext-dom

!!VAULT!! Check username and database name in .env file and run this command to choose the password :
php bin/console secrets:generate-keys
php bin/console secrets:set DATABASE_PASSWORD
php bin/console secrets:set ADMIN_PASSWORD
php bin/console secrets:set DEFAULT_USER_PASSWORD

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

Put the correct path in .env , this folder will be used to host files :
HOSTING_DIRECTORY=path/to/hosting/directory/

php bin/console doctrine:fixtures:load
php bin/console lexik:jwt:generate-keypair
symfony server:start

Install ClamAV :
https://www.rosehosting.com/blog/how-to-install-clamav-on-debian-9-and-scan-for-vulnerabilities/

Test
-------------------------------------------------------------------
Query :

*GET user Token(valid for 24 hours) :

curl --location --request POST 'localhost:8000/api/login_check' --header 'Content-Type: application/json' --data-raw '{"username":"user","password":"f56f5h4f6g5h4f56df5gh4"}'

*GET admin Token(valid for 24 hours) :

curl --location --request POST 'localhost:8000/api/login_check' --header 'Content-Type: application/json' --data-raw '{"username":"admin","password":"f56f5h4f6g5h4f56df5gh4_admin"}'

Notes
-------------------------------------------------------------------
After every entity creation or modification, use this command :
symfony console make:migration & bin/console doctrine:migrations:migrate & symfony console doctrine:schema:update --force

After update fixtures, use this command :
php bin/console doctrine:fixtures:load

JSON Collection for Postman

Run Worker to consume messages :
php bin/console messenger:consume commandRunner -vv

-------------------------------------------------------------------
https://www.postman.com/collections/880b957ed4b9cdded6bf

Run PHPUnit Tests
-------------------------------------------------------------------
rm -f var/data.db
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:drop --force --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load -n --env=test
php bin/phpunit
ls

After every entity modification use this command
-------------------------------------------------------------------
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

Run php-cs-fixer
-------------------------------------------------------------------
bin/php-cs-fixer fix tests --allow-risky=yes && bin/php-cs-fixer fix src --allow-risky=yes

Run php-stan
-------------------------------------------------------------------
vendor/bin/phpstan analyse src tests

Simulate ClamAV On Windows :
-------------------------------------------------------------------
#C:\scripts\clamscan.bat
touch %4%

and add C:\scripts\ to ENV variables

Check supervisor configuration :
------------------------------------------------------------------
cat /etc/supervisor/conf.d/messenger-worker.conf


AWS EC2 rights :
------------------------------------------------------------------
sudo chown -R www-data:www-data SYMFONY_DIRECTORY
sudo usermod -a -G www-data admin
sudo chmod -R 770 SYMFONY_DIRECTORY

AWS EC2 Firewall Configuration (IN)
------------------------------------------------------------------
–
sgr-018a3a647a6fa0384	IPv4	LDAP	TCP	389	0.0.0.0/0	–

–
sgr-0d4f7b854b05508d4	IPv4	SMTP	TCP	25	0.0.0.0/0	–

–
sgr-022ed2f4ea0464521	IPv4	SSH	TCP	22	0.0.0.0/0	–

–
sgr-0eb458371c351ee75	IPv4	HTTP	TCP	80	0.0.0.0/0	–

–
sgr-0477e1c69354b92c3	IPv4	HTTPS	TCP	443	0.0.0.0/0	–

AWS EC2 Firewall Configuration (OUT)
------------------------------------------------------------------
–
sgr-075d4cb7158770eef	IPv4	SMTP	TCP	25	0.0.0.0/0	–

–
sgr-03581b0ec0290f23c	IPv4	SMTPS	TCP	465	0.0.0.0/0	–

–
sgr-012d45eb7c1d16f70	IPv4	HTTP	TCP	80	0.0.0.0/0	–

–
sgr-0b7776abb76b98679	IPv4	HTTPS	TCP	443	0.0.0.0/0	–

Disable clamav-daemon
------------------------------------------------------------------
sudo systemctl disable clamav-daemon

Update ClamAV database :
------------------------------------------------------------------
sudo systemctl stop clamav-freshclam.service
sudo freshclam
sudo systemctl start clamav-freshclam.service

Install Imagick :
------------------------------------------------------------------
sudo apt install imagemagick imagemagick-doc
sudo apt install php8.0-imagick