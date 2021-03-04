Getting started with Symfony Link Shortner
==========================================

Symfony Link Shortner provides an easy way to shorten URLs

## Requirements

* php 7.4
* [Composer](https://getcomposer.org/)
* [Yarn](https://yarnpkg.com/)  
* [Symfony CLI](https://symfony.com/download) 
    * This is optional for running the solution

## Installation

After cloning the Git repository, please run the following commands:

  * composer install
  * composer dump-env prod
    * inside the .env.local.php please setup the DATABASE_URL similar to this mysql://username:password@127.0.0.1:3306/database_name?serverVersion=5.7  
    * replace the username, password & database_name with your values
  * php bin/console doctrine:database:create
    * if you want to create the database from console
  * php bin/console doctrine:migration:migrate
  * yarn install
  * yarn build
  * symfony server:start -d (If using Symfony CLI)
  

&copy; 2021 Charbel Al Batal, All rights reserved.