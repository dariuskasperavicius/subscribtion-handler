FROM mysql:8.0.21 as user_auth_mysql

RUN mysqld_safe & until mysqladmin ping; do sleep 1; done && \
    mysql -uroot -e "CREATE DATABASE testdb;" && \
    mysql -uroot -e "CREATE USER 'test'@'localhost' IDENTIFIED BY 'test';" && \
    mysql -uroot -e "GRANT ALL PRIVILEGES ON testdb.* TO 'test'@'localhost';"