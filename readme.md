#Wordpress vehicles project

This is the vehicles project developed with WordPress 5.4.2, 
on Ubuntu 18.04 using PHP 7.2.24 and MySQL database version 5.7

The first thing you should do is to login to MySQL using the command line. 
You should login as a non-root user, 
if you don't have non-root user created login as a root and then 
create the non-root user and its password. 
After that create the database ex. "wpdb" 
and make sure to set up your db credentials inside the .env file.
About the naming of ENV variables please take a look at the wp-config.php file.

I'm using the "vlucas/phpdotenv" package so you would need to execute 
composer install.

For me the next step was configuring NGINX. I have NGINX locally installed so I'm
providing some instructions about its configuration. Before you start with this
please make sure that you have php-fpm installed also. So inside /etc/nginx you
must have these files: fastcgi.conf, mime.types and nginx.conf. Also, inside
/var/log/nginx dir the files access.log and error.log. Inside nginx.conf 
make sure you have this contents.

user www-data;

worker_processes auto;

events {}

http {

  include /etc/nginx/mime.types;


  server {

   listen 8081;
   server_name #your_ip_address;

   root /home/bojan/Desktop/wordpress;

   index index.php;

   location / {

     try_files $uri $uri/ /index.php?q=$uri&$args;

   }

   location ~\.php$ {
     include fastcgi.conf;
     fastcgi_pass unix:/run/php/php7.2-fpm.sock;
   }

   location ~*\.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
     expires max;
     log_not_found off;
   }

  }

}

now execute this command - systemctl restart nginx

then execute - systemctl status nginx or nginx -t to check whether everything is running 
as it should be.
After this you should install Wordpress and its database by going to its domain
which in my case is my ip address.
After the installation access the wp-admin, go to settings->permalinks 
and choose the option postname for you to be able to use slug based urls.
Now you should create two pages vehicles and vehicle. To the first assign the template
vehicles, while to the second assign the template vehicle. 
I haven't started on the second page as I run out of time. It is a single page which should display
a detailed info about each vehicle, while the first vehicles page is more like landing page
where you are able to see a list of all the vehicles and you can filter the by make, model, year and location.
The template names are defined in the files located inside the directory 
wp-content/themes/vehicles/templates. Also you should visit the Vehicle Settings admin panel
which I've created and which is a custom theme options admin panel that serves for adding and updating
vehicles data in the db.