Tech Leaders
================

## Usage

Women in Technology - Tech Leaders Program

# Installation and running

## Requirements

Make sure you have installed the requirements for running symfony [requirements][1].

PHP >=7.2

Install [composer][2] to manage dependencies.
To install dependencies run command in project root dir:

    composer install


## Run app

Run the following commands in project root dir:

### Files and directories permissions

    HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

 * OS X:

        sudo chmod -R +a "group:_www allow read,write,delete,append,readattr,writeattr,readextattr,writeextattr,file_inherit,directory_inherit" var web/media web/uploads
        sudo chmod -R +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" var web/media web/uploads

 * Linux:

        sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media web/uploads
        sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var web/media web/uploads

### Database

Create database:

    ./bin/console doctrine:database:create

Create/update tables:

    ./bin/console doctrine:schema:update --force
or
    ./bin/console doctrine:migrations:migrate

### Frontend resources

Generate assets:

    ./bin/console assets:install
    ./bin/console assetic:dump

### Fixtures

Load data:

    ./bin/console doctrine:fixtures:load

### Server

Run server: [https://symfony.com/doc/current/setup/built_in_web_server.html]

    ./bin/console server:start

Check [http://localhost:8000][3].

### Static translations

Prepare database
    ./bin/console doctrine:database:create --connection=translations && ./bin/console doctrine:schema:update --force --em=translations

Reset database
    ./bin/console doctrine:database:drop --connection=translations --force && ./bin/console doctrine:database:create --connection=translations && ./bin/console doctrine:schema:update --force --em=translations

Import translations from files to db (to edit)
    ./bin/console lexik:translations:import

Grid view:
    ./bin/console assets:install

Export translations from db to files (to use)
    ./bin/console lexik:translations:export

# Testing

Tests library included in composer ./vendor/bin/phpunit
TDD Tests PHPUnit 5.7.x

## Functional Tests

### PhpUnit

    ./vendor/bin/phpunit
    ./vendor/bin/phpunit --testdox

    ./vendor/bin/phpunit --list-groups
    ./vendor/bin/phpunit --group edition

  - . - Printed when the test succeeds.

  - F - Printed when an assertion fails while running the test method. A failure is a violated PHPUnit assertion such as a failing assertEquals() call.

  - E - Printed when an error occurs while running the test method. An error is an unexpected exception or a PHP error.

  - R - Printed when the test has been marked as risky

  - S - Printed when the test has been skipped

  - I - Printed when the test is marked as being incomplete or not yet implemented

  - W - Warning

### PhpUnit

  LiipFunctionalTestBundle with HautelookAliceBundle


#### HautelookAliceBundle

  ./bin/console hautelook:fixtures:load --no-interaction

##### Writing fixtures

  Randomized entries using Faker:

  * formatters - https://github.com/fzaninotto/Faker#formatters

#### LiipFunctionalTestBundle


### Tests all urls

    sudo rm -R var/cache/* && ./vendor/bin/urltest tests
    ./vendor/bin/urltest --comparator=console -r=true -vvvv tests

# Dev

### Roadmap

###What's inside?

####Integration with:

  * friendsofsymfony/user-bundle - Users management system
  * doctrine/doctrine-migrations-bundle - Safe migrations system
  * knplabs/knp-paginator-bundle - Pagination
  * stephanecollot/datetimepicker-bundle - DateTime ui picker
  * [bootstrap 3][6]
  * [flat ui design][7] Free css based on bootstrap 3
  * vich/uploader-bundle - Images upload
  * liip/imagine-bundle - Resizing images, cache images
    Media dir must exist beforehand with permissions
    Remove cache
      ./bin/console liip:imagine:cache:remove --filters=homepage_partners
  * knplabs/knp-snappy-bundle Pdf export
  * ivopetkov/video-embed Video embedded
  * adesigns/calendar-bundle Full callendar js
  * doctrine/doctrine-fixtures-bundle Fixtures
  * stof/doctrine-extensions-bundle Symfony database extensions (sortable, timestampable, softdeleteable in use)
  * knplabs/doctrine-behaviors Symfony database extensions (translatable in use)
  * isometriks/spam-bundle spam prevention (form for not logged in)
  * PetkoparaMultiSearchBundle - search with many entity fields with Doctrine in Symfony

### Current development

 Using PHP_CodeSniffer

    ./vendor/bin/phpcs src
    ./vendor/bin/phpcbf src

### Deploy

### TODO

post-install-cmd: ./bin/console presta:sitemaps:dump

##### Performance
  - https://github.com/liip/LiipFunctionalTestBundle

License
---------------

All libraries and bundles included in the Symfony Standard Edition are
released under the MIT license.

The MIT License

Copyright © 2015-2018, Natalia Stanko [nataliastanko.com][5]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the “Software”), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

[1]:  http://symfony.com/doc/current/reference/requirements.html
[2]:  http://getcomposer.org/
[3]:  http://localhost:8000/
[4]:  https://phpunit.de/manual/current/en/installation.html
[5]:  http://nataliastanko.com/
[6]:  http://getbootstrap.com
[7]:  http://designmodo.github.io/Flat-UI/
