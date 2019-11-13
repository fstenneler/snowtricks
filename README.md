# Snowtricks
## Community website about snowboard figures

Online version [here](http://snowtricks.orlinstreet.rocks).

## Certifications

### Symfony Insights
[![SymfonyInsight](https://insight.symfony.com/projects/af37613e-6fa3-4203-9ebd-ae9978c0b14d/big.svg)](https://insight.symfony.com/projects/af37613e-6fa3-4203-9ebd-ae9978c0b14d)

### Codacy
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/498a5166102d4352bd25f41ed6e12260)](https://www.codacy.com/app/fstenneler/webdev-blog?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fstenneler/webdev-blog&amp;utm_campaign=Badge_Grade)

## Setup instructions

### Download the repository

#### Either from the url
[https://github.com/fstenneler/snowtricks/archive/master.zip]https://github.com/fstenneler/snowtricks/archive/master.zip

#### Or from Git
    git clone https://github.com/fstenneler/snowtricks.git

### Download and install Composer
https://getcomposer.org/download/

### Update dependencies from the project directory
    composer update

### Create a new MySQL database on your server

### Setup the .env files with your own parameters

#### Database
    DATABASE_URL=mysql://user:password@hostName:port/dbName

#### Email
    MAILER_URL="smtp://yourSmtpServer:yourSmtpPort?encryption=tls&auth_mode=login&username=yourMailUserName@yourWebsiteHostName&password=yourMailPassword"
    
### Load initial fixtures
    php bin/console doctrine:fixtures:load 

### Deploy the app

#### Change the APP_ENV and APP_DEBUG values in the .env file
    APP_ENV=prod
    APP_DEBUG=0

#### Empty chache
    php bin/console cache:clear

#### Upload all project files on your server

#### In case or errors, run the debug mode in the .env file
    APP_DEBUG=1
