## Horse Game Simulator

### Application Preview

![demo-horseGame](https://user-images.githubusercontent.com/1470750/59915216-bbd90f00-93e1-11e9-90b1-653c494071e7.gif)

### How to install and run the application

Clone the git repository

    git clone https://github.com/edisnake/horseGame.git


* For non-Docker version

    * Make sure you have enabled the pdo_pgsql extension in `php.ini` file
    
    * Update the Postgres Database string connection `DATABASE_URL` in `.env` file

    * In your CLI go to horseGame folder and run following commands:
    
            composer install
            php bin/console doctrine:database:create
            php bin/console doctrine:migrations:migrate
            php bin/console server:run

        Now the App can be found in **http://127.0.0.1:8000/**

* Docker version

    * In your CLI go to horseGame/docker folder
    * Start docker, running 

            docker-compose up

        Now the App can be found in your docker IP address i.e. **http://192.168.99.100/**


**Running Tests**

Go to the horseGame folder and run this command

    php bin/phpunit tests/

There are 15 tests and 62 assertions including functional and unit tests.


**Database**

* The Database tables can be found inside horseGame folder `database_tables.sql`

**What has been done**

* Provide Docker Containers

* Create Race feature which generates random horses

* Progress horse feature which calculates and records the horse covered distance according to its skills 

* Implement Symfony 4.3 project with Dependency Injection, Annotations, Single Responsibility and other Design Patterns

* Use Bootstrap

* Unit and Functional tests


## Author

Edwuin Gutierrez
edwinguti86@gmail.com


## License

Copyright(c) 2019
MIT License
