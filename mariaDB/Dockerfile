FROM mariadb:10.6.4

COPY table.sql /docker-entrypoint-initdb.d/
COPY my.cnf /etc/mysql/conf.d/

RUN apt-get update && apt-get install -y \
    vim

CMD ["mysqld"]