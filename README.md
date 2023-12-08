# safety-gear

Read YML file 
Change ports for web and mysql if needed

For now its running in 80 http://localhost

After pulling this repo, do following steps

run docker
`docker-compose up -d`

stop docker
`docker compose down`

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