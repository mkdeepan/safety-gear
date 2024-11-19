# safety-gear

Read YML file 
Change ports for web and mysql if needed

For now its running in 80 http://localhost

After pulling this repo, do following steps

run docker
`docker-compose up -d`

stop docker
`docker compose down`

Few other docker commands
# Stop and remove all running containers
docker stop $(docker ps -aq)
docker rm $(docker ps -aq)

# Remove all images, volumes, and networks
docker system prune -af --volumes

# Destroy the docker image
docker rmi <image_name>

# Restart the containers
docker-compose up -d

# Some useful docker commands
docker cp ab36dcff17aa:/etc/ssl/certs/greenko_wildcard.crt ./
docker cp ./default-ssl.conf ed4ab270c8c5:/etc/apache2/sites-available/default-ssl.conf
docker exec -it

# Moved ssl config within docker, changes required in container config as below
ports:
  - "443:443"
volumes:
  - ../public_html:/var/www/html
  - ./certs/greenko_wildcard.crt:/etc/ssl/certs/greenko_wildcard.crt
  - ./certs/greenko_wildcard.key:/etc/ssl/private/greenko_wildcard.key
  - ./certs/greenko_wildcard_bundle.crt:/etc/ssl/certs/greenko_wildcard_bundle.crt
environment:
  - APACHE_SSL=enabled
entrypoint: /bin/bash -c "a2enmod ssl && a2ensite default-ssl && apache2ctl -D FOREGROUND"

# Once ssl enabled in docker, it requires following ssl apache2 config as below
default ssl config:

<IfModule mod_ssl.c>
<VirtualHost *:443>
    ServerName prqronline.greenkogroup.com
    ServerAdmin admin@greenkogroup.com
    DocumentRoot /var/www/html
    ErrorLog /var/log/httpd/prqronline.log
    CustomLog /var/log/httpd/prqronline-cus.log combined

    DirectoryIndex index.php index.html

    <Directory "/var/www/html">
        Options -Indexes +FollowSymLinks
        AllowOverride all
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/greenko_wildcard.crt
    SSLCertificateKeyFile /etc/ssl/private/greenko_wildcard.key
    SSLCertificateChainFile /etc/ssl/certs/greenko_wildcard_bundle.crt
</VirtualHost>
</IfModule>


# If Reverse proxy needed:
once docker up
do reverse proxy in default web server (http://127.0.0.1:8081)

# Ensure following tools is enabled: 
getenforce
sudo setsebool -P httpd_can_network_connect 1

connect mysql inside docker with below credentials 

`mysql -u user -p`

connect from outside docker

`mysql -h localhost -P 3306 --protocol=TCP -u user -p`

db_name: safety_gear_db
user: user
password: user
host: mysql

update following tables in db by below queries

UPDATE wp_options SET option_value = replace(option_value, 'https://safetygear.online', 'http://localhost') WHERE option_name = 'home' OR option_name = 'siteurl';

Result : Only 2 rows updated
  
UPDATE wp_posts SET post_content = replace(post_content, 'https://safetygear.online', 'http://localhost');

Result :  216 rows updated
  
UPDATE wp_postmeta SET meta_value = replace(meta_value,'https://safetygear.online', 'http://localhost');

Result : 1065 updated

# Then finally run http://localhost