#/bin/bash
now=$(date +"%Y%m%d-%H%M")
mysqldump -h localhost -u[dbuser] -p[dbpsw] --opt [dbname] > [path]/filename_$now.sql
