===================
Service Monitoring
===================

Monit is a small Open Source utility for managing and monitoring systems. Monit conducts automatic maintenance and repair and can execute meaningful causal actions in error situations.  

For ASTPP we can configure apache,freeswitch and mysql services to monitor.

**Installation**
::  For CentOS
yum install monit

For Debian
apt-get install monit


**Configurations:**

**Enable Web Interface in Monit**
::  Monit also provided an web interface to view services and processes status. To enable monit web interface, 
edit configuration file ( For CentOS /etc/monit.conf & For Debian System /etc/monit/monitrc ) and modify following 
lines as per your server information's

set httpd port 2812 and
use address localhost
allow localhost
allow admin:monit
allow @monit
allow @users readonly


**Configure Monit To Monitor Services**
::  1) Nginx:
check process nginx with pidfile /var/run/nginx.pid
group www-data
start program = "/etc/init.d/nginx start" with timeout 30 seconds
stop program  = "/etc/init.d/nginx stop"
    
    2) MySQL
        check process mysqld with pidfile /var/run/mysqld/mysqld.pid
        start program = "/etc/init.d/mysql start"
        stop program = "/etc/init.d/mysql stop"
        group resources
        if cpu > 60% for 2 cycles then alert
        if cpu > 80% for 5 cycles then restart


    3) Freeswitch
        check process freeswitch with pidfile /usr/local/freeswitch/run/freeswitch.pid
        start program = "/etc/init.d/freeswitch start"
        stop program = "/etc/init.d/freeswitch stop"
        if 5 restarts within 5 cycles then timeout
        if cpu > 60% for 2 cycles then alert
        if cpu > 80% for 5 cycles then alert
        if totalmem > 2000.0 MB for 5 cycles then restart
        if children > 2500 then restart


    
**Configuration for email notification**
::  # set mailserver mail.bar.baz, # primary mailserver
# backup.bar.baz port 10025, # backup mailserver on port 10025
# localhost # fallback relay

set mailserver localhost

# set alert sysadm@foo.bar # receive all alerts
# set alert manager@foo.bar only on { timeout } # receive just service-
# # timeout alert

set alert your@email.com

It will notify the status of services in email which are configured in configuration file.
    
  
  
**Start service**
::  Now start the monit service
# service monit start
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    




