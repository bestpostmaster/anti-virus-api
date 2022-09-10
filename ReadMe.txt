This API provides a complete system for authenticating via JWT and uploading files.
An admin exists, and he has endpoints that allow him to manage users
After the upload, an anti-virus scan is performed asynchronously by ClamAV. It is therefore important to install ClamAV and configure it correctly

Installation
-------------------------------------------------------------------
composer install
<!on debian 10!> sudo composer require ext-dom

!!VAULT!! Check user name and database name in .env file and run this command to choose the password : 
APP_RUNTIME_ENV=dev php bin/console secrets:set DATABASE_PASSWORD
APP_RUNTIME_ENV=dev php bin/console secrets:set ADMIN_PASSWORD
APP_RUNTIME_ENV=dev php bin/console secrets:set DEFAULT_USER_PASSWORD

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
php bin/console messenger:consume async -vv

-------------------------------------------------------------------
https://www.postman.com/collections/880b957ed4b9cdded6bf

Run PHPUnit Tests
-------------------------------------------------------------------
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:drop --force --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test
php bin/phpunit

After every entity modification use this command
-------------------------------------------------------------------
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

Run php-cs-fixer
-------------------------------------------------------------------
bin/php-cs-fixer fix tests && bin/php-cs-fixer fix src

