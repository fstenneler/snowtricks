# Snowtricks
## Community website about snowboard figures

Online version [here](http://snowtricks.orlinstreet.rocks).

## Certifications

### Symfony Insights
[![SymfonyInsight](https://insight.symfony.com/projects/af37613e-6fa3-4203-9ebd-ae9978c0b14d/big.svg)](https://insight.symfony.com/projects/af37613e-6fa3-4203-9ebd-ae9978c0b14d)

### Codacy
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e2c03f2f71994d3a9689143e48c8b17b)](https://www.codacy.com/manual/fstenneler/snowtricks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fstenneler/snowtricks&amp;utm_campaign=Badge_Grade)

## Setup instructions

### Download the repository

#### Either from the url
[Download repository using the web URL](https://github.com/fstenneler/snowtricks/archive/master.zip)

#### Or from Git
    git clone https://github.com/fstenneler/snowtricks.git

### Download and install Composer
[Download Composer](https://getcomposer.org/download/)

### Update dependencies

#### In command line from the project directory
    composer update

### Setup the .env files with your own parameters

#### Database
    DATABASE_URL=mysql://user:password@hostName:port/snowtricks

#### Email
    MAILER_URL="smtp://yourSmtpServer:yourSmtpPort?encryption=tls&auth_mode=login&username=yourMailUserName@yourWebsiteHostName&password=yourMailPassword"

### Create database and load data
In command line from the project directory

#### Create database
    php bin/console doctrine:database:create

#### Create tables and relations
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate

#### Load initial data
    php bin/console doctrine:fixtures:load

### Deploy the app

#### Change the APP_ENV and APP_DEBUG values in the .env file
    APP_ENV=prod
    APP_DEBUG=0

#### Empty cache
    php bin/console cache:clear

#### Upload all project files on your server

#### In case or errors, run the debug mode in the .env file
    APP_DEBUG=1
