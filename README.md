# zabbix-dashboard
PHP dashboard for Zabbix.
The code are quite old and a bit quick and dirty. Tested with Zabbix 3.x and 4.x
## Installation
Just copy the HTML folder to a PHP enabled webserver.
For config the are 3 environment variables
- ZABBIX_SERVER
- ZABBIX_USER
- ZABBIX_PASSWORD
- TZ for timezone [list](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones)
## Docker
There is a simple Dockerfile and the image is on DockerHub as well.
```bash
docker run --name zabbix-dashboard --restart unless-stopped -d -p 8080:80 -e TZ=Europe/Copenhagen -e ZABBIX_SERVER='zabbix-server.example.com' -e ZABBIX_USER='zabbixusername' -e ZABBIX_PASSWORD='zabbixpassword' styrup/zabbix-dashboard
```