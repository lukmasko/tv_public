# TV Online
_My Mini-Youtube version script_

# Working - in brief
1. Upload process:

* select the video file
* the browser divides it into parts and sends it one by one to the server
* the server saves these files and updates / writes status information
* after uploading all parts of the video it is converted and saved to the database

2. Playing process:

* the browser gets the metadata from the server
* on their basis, the server sends subsequent parts (the video is buffered)

# Technology stack
 - PHP 
 - JavaScript 
 - MySQL 
 - HTML/CSS 
 - MP4Box

 # Install
  - localhost server (e.g. WAMP) with PHP ver 8+
  - MP4Box

# Simply configuration
 - add new virtualhost (e.g. www.tv.test)
 - login to mysql database and import tv.sql file
 - in PHP configuration file php.ini set upload_max_filesize = 8M and post_max_size = 8M
 - in MySQL configuration file my.ini change max_allowed_packet = 8M
 - restart your localhost server and go to www.tv.test

# Authors
-   Łukasz Maśko (lukmasko@gmail.com)

# License
...

# External libraries used
- MP4Box - https://github.com/gpac/gpac/wiki/MP4Box
- Bootstrap - https://getbootstrap.com/
- jQuery - https://jquery.com/