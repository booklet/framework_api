FROM mysql:5.7
MAINTAINER Booklet Group S.C. <b.mikolajczak@booklet.pl>

ENV MYSQL_ROOT_PASSWORD docker

COPY sql-scripts.sql /docker-entrypoint-initdb.d/
