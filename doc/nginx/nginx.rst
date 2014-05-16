eZ Publish nginx configuration
==============================

For information on if nginx is supported with your version of eZ Publish, consult with the online documentation on http://doc.ez.no.


Prerequisite
------------
- A working FPM setup (either using network or unix socket).
- nginx must be installed.


Configuration
------------
- Copy the provided etc/nginx/ez_params.d folder in your /etc/nginx/ folder (or symlink it if you are in a development environment).
- Copy the provided example file etc/nginx/sites-available/mysite.com into /etc/nginx/sites-available/yoursite.com
- Edit it and adapt the configuration to suit your needs.
- Create a symlink of /etc/nginx/sites-available/yoursite.com into /etc/nginx/sites-enabled/yoursite.com
- restart nginx


