Install
---------
Run `docker-compose up -d`

Create Database: login into docker:
`docker-compose exec php /bin/sh` 
and run:
`bin/console d:s:u --force`

Code Testing
--------
- Run `composer test`

Code Style
--------
- Run `composer style`


