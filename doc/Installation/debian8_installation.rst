=======================
Debian 8 Installation 
=======================

**Install base packages**
::  apt-get -o Acquire::Check-Valid-Until=false update
apt-get install -y git wget curl
    
**Install Freeswitch**

**1. Install Freeswitch pre-requisite packages**
::  #Add freeswitch source list
curl https://files.freeswitch.org/repo/deb/debian/freeswitch_archive_g0.pub | apt-key add -
echo "deb http://files.freeswitch.org/repo/deb/freeswitch-1.6/ jessie main" > /etc/apt/sources.list.d/freeswitch.list
    
#Add php7.0  source list
echo "deb http://packages.dotdeb.org jessie all
deb-src http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/php7.list
curl https://www.dotdeb.org/dotdeb.gpg | apt-key add -
    
#Install dependencies
apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y --force-yes freeswitch-video-deps-most
apt-get install -y autoconf automake devscripts gawk chkconfig dnsutils sendmail-bin sensible-mda ntpdate ntp g++ \
git-core curl libjpeg62-turbo-dev libncurses5-dev make python-dev pkg-config libgdbm-dev libyuv-dev libdb-\
dev libvpx2-dev gettext sudo lua5.1 php7.0 php7.0-dev php7.0-common php7.0-cli php7.0-gd php-pear \
php7.0-apc php7.0-curl libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc libldns-dev \
libpcre3-dev build-essential libssl-dev libspeex-dev libspeexdsp-dev libsqlite3-dev libedit-dev libldns-dev libpq-dev bc
    
#Install mysql server
apt-get install -y mysql-server php7.0-mysql

**2. Download latest freeswitch version**
::  cd /usr/local/src
git config --global pull.rebase true

#Clone freeswitch version 1.6 from git 
git clone -b v1.6.19 https://freeswitch.org/stash/scm/fs/freeswitch.git
cd freeswitch
./bootstrap.sh -j

**3. Edit modules.conf**
::  #Enabling mod_xml_curl, mod_json_cdr, mod_db
sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#mod_db#mod_db#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#applications/mod_voicemail#applications/mod_voicemail#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#event_handlers/mod_json_cdr#event_handlers/mod_json_cdr#g" /usr/local/src/freeswitch/modules.conf

.. note:: # add a module by removing '#' comment character at the beginning of the line 
          # remove a module by inserting the '#' comment character at the beginning of the line containing the name of 
          the module to be skipped
            
**4. Compile the Source**
::  ./configure -C

**5. Install Freeswitch with sound files**
::  make all install cd-sounds-install cd-moh-install
make && make install 
    
**6. Set right time in server**
::  ntpdate pool.ntp.org
systemctl restart ntp
chkconfig ntp on

**7. Create symbolic links for Freeswitch executables**
::  ln -s /usr/local/freeswitch/bin/freeswitch /usr/local/bin/freeswitch
ln -s /usr/local/freeswitch/bin/fs_cli /usr/local/bin/fs_cli

**ASTPP Install**

**1. Download ASTPP**
::  # Download ASTPP 3.6 source from git
cd /usr/src
git clone -b v3.6 https://github.com/iNextrix/ASTPP

**2.  Change Apache working scenario**
::	As we are using Nginx from now onwards in ASTPP 3.0, if you are using apache for any applicaion then-
either have to move it to Nginx and/or remove apache. You can also change default port for apache if want to use-
it continue and troubleshoot some installation issue if arise.


**3. Install ASTPP pre-requisite packages**
::  apt-get -o Acquire::Check-Valid-Until=false update
    
apt-get install -y curl libyuv-dev libvpx2-dev nginx php7.0-fpm php7.0 php7.0-mcrypt libmyodbc unixodbc-bin php7.0-dev \
php7.0-common php7.0-cli php7.0-gd php-pear php7.0-cli php7.0-apc php7.0-curl libxml2 libxml2-dev openssl libcurl4-openssl-\
dev gettext gcc g++



**4. Normalize ASTPP**
::  #Create access & error log files.
touch /var/log/nginx/astpp_access_log
touch /var/log/nginx/astpp_error_log
touch /var/log/nginx/fs_access_log
touch /var/log/nginx/fs_error_log			
php5enmod mcrypt
systemctl restart php7.0-fpm
service nginx reload
  
  
**ASTPP using FreeSWITCH (if you want to use ASTPP with FreeSWITCH)**

**1. Configure freeswitch startup script**
::  cp /usr/src/ASTPP/freeswitch/init/freeswitch.debian.init /etc/init.d/freeswitch

chmod 755 /etc/init.d/freeswitch
chmod +x /etc/init.d/freeswitch
update-rc.d freeswitch defaults
chkconfig --add freeswitch
chkconfig --level 345 freeswitch on

**2. Configure ASTPP with freeswitch**
::  #Create directory structure for ASTPP
mkdir -p /var/lib/astpp/
mkdir -p /var/log/astpp/
mkdir -p /usr/local/astpp/
mkdir -p /var/www/

#Setting permisssion
chown -Rf root.root /var/lib/astpp/
chown -Rf www-data.www-data /var/log/astpp/
chown -Rf root.root /usr/local/astpp/
chown -Rf www-data.www-data /var/www/

#Setting up Scripts and Sounds for fs
cp -rf /usr/src/ASTPP/freeswitch/scripts/* /usr/local/freeswitch/scripts/
cp -rf /usr/src/ASTPP/freeswitch/fs /var/www/html/
cp -rf /usr/src/ASTPP/freeswitch/sounds/*.wav /usr/local/freeswitch/sounds/en/us/callie/
chmod -Rf 777 /usr/local/freeswitch/sounds/en/us/callie/
rm -rf  /usr/local/freeswitch/conf/dialplan/*
touch /usr/local/freeswitch/conf/dialplan/astpp.xml
rm -rf  /usr/local/freeswitch/conf/directory/*
touch /usr/local/freeswitch/conf/directory/astpp.xml
rm -rf  /usr/local/freeswitch/conf/sip_profiles/*
touch /usr/local/freeswitch/conf/sip_profiles/astpp.xml

**Install ASTPP web interface**
::  mkdir -p /var/lib/astpp
cp /usr/src/ASTPP/config/* /var/lib/astpp/

#Setup web interface for ASTPP
mkdir -p /var/www/html/astpp
cp -rf /usr/src/ASTPP/web_interface/astpp/* /var/www/html/astpp/
chown -Rf www-data.www-data /var/www/html/astpp
cp /usr/src/ASTPP/web_interface/nginx/deb_* /etc/nginx/conf.d/

chmod -Rf 755 /var/www/html/astpp
touch /var/log/astpp/astpp.log
chown -Rf www-data.www-data /var/log/astpp/astpp.log
    
**Install ASTPP Database**
::  #Restart mysql service
systemctl restart mysql
mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('<MYSQL_ROOT_PASSWORD>') WHERE user='root'; FLUSH PRIVILEGES;"

#Create database astpp
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "create database astpp;"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '<ASTPP_USER_PASSWORD>';"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "GRANT ALL PRIVILEGES ON \`astpp\` . * TO 'astppuser'@'localhost' WITH 
GRANT OPTION;FLUSH PRIVILEGES;"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-3.0.sql
mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-upgrade-3.5.sql
mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-upgrade-3.6.sql

#Setup ODBC Connection for mysql
cp /usr/src/ASTPP/misc/odbc/deb_odbc.ini /etc/odbc.ini
cp /usr/src/ASTPP/misc/odbc/deb_odbcinst.ini /etc/odbcinst.ini

#Update your mysql login information in odbc file
sed -i "s#PASSWORD = <PASSWORD>#PASSWORD = <MYSQL_ROOT_PASSWORD>#g" /etc/odbc.ini

Note:- Replace "<MYSQL_ROOT_PASSWORD>" with your mysql root login password and "<ASTPP_USER_PASSWORD>" is as per 
your choice.

**ASTPP Freeswitch Configuration**
::  cp /usr/src/ASTPP/freeswitch/conf/autoload_configs/* /usr/local/freeswitch/conf/autoload_configs/
 
#Edit db password in autoload config files.
sed -i "s#dbpass = <PASSSWORD>#dbpass = <MYSQL_ROOT_PASSWORD>#g" /var/lib/astpp/astpp-config.conf
sed -i "s#DB_PASSWD=\"<PASSSWORD>\"#DB_PASSWD = \"<MYSQL_ROOT_PASSWORD>\"#g" /var/lib/astpp/astpp.lua

#Edit base URL in astpp-config
sed -i "s#base_url=http://localhost:8089/#base_url=http://<SERVER FQDN / IP ADDRESS>:8089/#g" /var/lib/astpp/
astpp-config.conf

Note:- Replace "<SERVER FQDN / IP ADDRESS>" with your server domain name or IPaddress

**Finalize Installation & Start Services**
::  #Open php short tag
sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php/7.0/fpm/php.ini
sed -i "s#;cgi.fix_pathinfo=1#cgi.fix_pathinfo=1#g" /etc/php/7.0/fpm/php.ini
sed -i "s/max_execution_time = 30/max_execution_time = 3000/" /etc/php/7.0/fpm/php.ini
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 20M/" /etc/php/7.0/fpm/php.ini
sed -i "s/post_max_size = 8M/post_max_size = 20M/" /etc/php/7.0/fpm/php.ini
sed -i "s/memory_limit = 128M/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini
systemctl restart php7.0-fpm
systemctl restart nginx

#Configure services for startup
systemctl disable apache2   #If you are using it then change the port or update your configuration for nginx 
otherwise your gui will not up
systemctl enable nginx
systemctl enable php7.0-fpm			
systemctl start mysql
systemctl start freeswitch
chkconfig --levels 345 mariadb on
chkconfig --levels 345 freeswitch on

    Note:- If you want to use iptables then configure it to allow all port used in fs and ASTPP.
    
**Setup cron**
::  # Generate Invoice   
0 12 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice

# Update balance notification
0 12 * * * cd /var/www/html/astpp/cron/ && php cron.php UpdateBalance

# Low balance notification
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance

# Update currency rate
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php CurrencyUpdate


# Email Broadcasting
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php BroadcastEmail

**Finally Reboot it.**
::  #You are almost done with your configuration so just reboot it and make sure everything is working fine.
 
reboot now

#Once server up and running again, check below service status.
systemctl status nginx
systemctl status mysql
systemctl status freeswitch
systemctl status php7.0-fpm


.. note:: You are done with GUI installation. Enjoy :)
          Visit the astpp admin page in your web browser. It can be found here: http://server_ip:8089/ Please change the ip 
          address depending upon your box. The default username and password is “admin”. 

          Note : In case of any issue please refer apache error log.

.. note:: If you have any other question(s) then please contact us on sales@inextrix.com or post your questions(s) 
          in https://groups.google.com/forum/#!forum/astpp.



