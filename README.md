# WTAB-framework
Basic login / PHP pages wrapper, someway / somehow compatible with a famous forgotten framework.

# README
1) always implement the "apache http authentication" on the project folder because the login section is not 100% secure
2) create a database and adapt the sqldump.sql structure
3) change -change-here.php and .htaccess file to set the domain and db details

Some personal notes:
I kinda forked an old project I wrote years ago, before adopt Zend Framework for every PHP application I made in the latest years. Zend Framework has became a different project in the meanwhile. I'm happy I always used a custom class for the CRUD operations, so I can migrate everything with less effort.

# WTAB assistant
other typical needs show up when developing a software with the framework, like a smart system to read the software's logs (very handy in debugging) or a backup tool; fancy things fall down here in this section.

# How to Create new module
I suggest to copy/paste the "OapDashboard" folder in /module. Change the directory name to the module name (for example "OapDatamng") and find/replace "OapDashboard" with "OapDatamng", "dashboard" to "datamng", "Dashboard" to "Datamng". Just do the same in /public/css and /public/js but also change dashboard_index.css in datamng_index.css and dashboard_index.js in datamng_index.js, change module/view/oap-dashboard in module/view/oap-datamng and finally add the occurrence in $application_configs['parameters_whitelist'] /wtabassistant/-application-config.php (more information will be available on the topic soon)

# Code generator
The Code generator is still in super-alpha version, but there are the basics to create a "View" table based on a mysql table (automatically create the LEFT JOIN statement for the connected tables) and the Insert/Edit forms used for change the row data and the data into the external table. Formal checks must be implemented before use it even in a dev environment, but this work is very useful in my day work, because I can have a great part of code, written almost automatically and in fast way. A basics I use to do my job.
