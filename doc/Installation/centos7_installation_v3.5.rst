============================
CentOs 7 Installation V3.5
============================

**Install base packages**
::

 yum update
 yum groupinstall "Development tools" -y
 
 #Enable epel and freeswitch repository
 yum install epel-release
 rpm -Uvh http://files.freeswitch.org/freeswitch-release-1-6.noarch.rpm
 yum update

**Install Freeswitch**

**1. Install Freeswitch pre-requisite packages**
::

 #Install dependencies for freeswitch
 yum install -y wget git autoconf automake expat-devel yasm gnutls-devel libtiff-devel libX11-devel unixODBC-devel 
 python-devel zlib-devel alsa-lib-devel libogg-devel libvorbis-devel uuid-devel @development-tools gdbm-devel 
 db4-devel libjpeg libjpeg-deve compat-libtermcap ncurses ncurses-devel ntp screen sendmail sendmail-cf gcc-c++
 @development-tools bison bzip2 curl curl-devel dmidecode git make mysql-connector-odbc openssl-devel unixODBC 
 zlib pcre-devel speex-devel sqlite-devel ldns-devel libedit-devel bc e2fsprogs-devel libcurl-devel libxml2-devel 
 libyuv-devel opus-devel libvpx-devel libvpx2* libdb4* libidn-devel unbou-nd devel libuuid-devel lua-devel libsndfile-devel


**2. Download latest freeswitch version**
::
  
  cd /usr/local/src
  git config --global pull.rebase true

  #Clone freeswitch version 1.6.8 from git 
  git clone -b v1.6.19 https://freeswitch.org/stash/scm/fs/freeswitch.git
  cd freeswitch
  ./bootstrap.sh -j


**3. Edit modules.conf**
::

   #Enabling mod_xml_curl, mod_json_cdr, mod_db
    sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
    sed -i "s#\#mod_db#mod_db#g" /usr/local/src/freeswitch/modules.conf
    sed -i "s#\#event_handlers/mod_json_cdr#event_handlers/mod_json_cdr#g" /usr/local/src/freeswitch/modules.conf
    sed -i "s#\#applications/mod_voicemail#applications/mod_voicemail#g" /usr/local/src/freeswitch/modules.conf


.. note:: # add a module by removing '#' comment character at the beginning of the line 
          # remove a module by inserting the '#' comment character at the beginning of the line containing the name of 
          the module to be skipped
          

**4. Compile the Source** 
::

  ./configure -C
          
          
**5. Install Freeswitch with sound files** 
::

   make all install cd-sounds-install cd-moh-install
   make && make install
  

**6. Set right time in server** 
::

   ntpdate pool.ntp.org
   systemctl restart ntp
   chkconfig ntp on


**7. Create symbolic links for Freeswitch executables** 
::

   ln -s /usr/local/freeswitch/bin/freeswitch /usr/local/bin/freeswitch
   ln -s /usr/local/freeswitch/bin/fs_cli /usr/local/bin/fs_cli


**ASTPP Install**

**1. Download ASTPP** 
::

   # Download ASTPP 3.5 source from git
     cd /usr/src
     git clone https://github.com/iNextrix/ASTPP

**2. Change Apache working scenario** 
::
  
    As we are using Nginx from now onwards from ASTPP 3.0, if you are using apache for any applicaion then-
    either have to move it to Nginx and/or remove apache. You can also change default port for apache if want to use-
    it continue and troubleshoot some installation issue if arise.

**3. Install ASTPP pre-requisite packages** 
::
  
    yum install -y autoconf automake bzip2 cpio curl nginx php-fpm php-mcrypt* unixODBC mysql-connector-odbc curl-devel php 
    php-devel php-common php-cli php-gd php-pear php-mysql php-mbstring sendmail sendmail-cf php-pdo php-pecl-json mysql
    mariadb-server mysql-devel libxml2 libxml2-devel openssl openssl-devel gettext-devel fileutils gcc-c++


**4. Normalize ASTPP** 
::
  
   #Create access & error log files.
   touch /var/log/nginx/astpp_access_log
   touch /var/log/nginx/astpp_error_log
   touch /var/log/nginx/fs_access_log
   touch /var/log/nginx/fs_error_log			
   systemctl restart php-fpm
   service nginx reload


**ASTPP using FreeSWITCH (if you want to use ASTPP with FreeSWITCH)**

**1. Configure freeswitch startup script** 
::

  cp /usr/src/latest/freeswitch/init/freeswitch.centos.init /etc/init.d/freeswitch
  chmod 755 /etc/init.d/freeswitch
  chmod +x /etc/init.d/freeswitch
  chkconfig --add freeswitch
  chkconfig --level 345 freeswitch on
  mkdir /var/run/freeswitch


**2. Configure ASTPP with freeswitch** 
::

    #Create directory structure for ASTPP
    mkdir -p /var/lib/astpp/
    mkdir -p /var/log/astpp/
    mkdir -p /usr/local/astpp/
    mkdir -p /var/www/

    #Setting permisssion
    chown -Rf root.root /var/lib/astpp/
    chown -Rf root.root /var/log/astpp/
    chown -Rf root.root /usr/local/astpp/
    chown -Rf root.root /var/www//

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
::

   mkdir -p /var/lib/astpp
   cp /usr/src/ASTPP/config/* /var/lib/astpp/

   #Setup web interface for ASTPP
   mkdir -p /var/www/html/astpp
   cp -rf /usr/src/ASTPP/web_interface/astpp/* /var/www/html/astpp/
   chown -Rf root.root /var/www/html/astpp
   cp /usr/src/ASTPP/web_interface/nginx/cent_* /etc/nginx/conf.d/

   #apply security policy 
   sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/sysconfig/selinux
   sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/selinux/config
   /etc/init.d/iptables stop
   chkconfig iptables off
   setenforce 0

   chmod -Rf 755 /var/www/html/astpp
   touch /var/log/astpp/astpp.log
 
 

**Install ASTPP Database**
::

   #Restart mysql service
   systemctl start mariadb
   mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('<MYSQL_ROOT_PASSWORD>') WHERE user='root'; FLUSH PRIVILEGES;"

   #Create database astpp
   mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "create database astpp;"
   mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '<ASTPP_USER_PASSWORD>';"
   mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "GRANT ALL PRIVILEGES ON \`astpp\` . * TO 'astppuser'@'localhost' WITH 
   GRANT OPTION;FLUSH PRIVILEGES;"
   mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-3.0.sql
   mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-upgrade-3.5.sql

**ASTPP Freeswitch Configuration**
::

   cp /usr/src/ASTPP/freeswitch/conf/autoload_configs/* /usr/local/freeswitch/conf/autoload_configs/
 
   #Edit db password in autoload config files.
   sed -i "s#dbpass = <PASSSWORD>#dbpass = <MYSQL_ROOT_PASSWORD>#g" /var/lib/astpp/astpp-config.conf
   sed -i "s#DB_PASSWD=\"<PASSSWORD>\"#DB_PASSWD = \"<MYSQL_ROOT_PASSWORD>\"#g" /var/lib/astpp/astpp.lua

   #Edit base URL in astpp-config
   sed -i "s#base_url=http://localhost:8089/#base_url=http://<SERVER FQDN / IP ADDRESS>:8089/#g" /var/lib/astpp/
   astpp-config.conf

   Note:- Replace "<SERVER FQDN / IP ADDRESS>" with your server domain name or IPaddress
   


**Finalize Installation & Start Services**
::
  
   #Open php short tag
   sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php.ini

   #Configure services for startup
   systemctl disable httpd   #If you are using it then change the port or update your configuration for nginx otherwise 
   your gui will not up
   systemctl enable nginx
   systemctl enable php-fpm			
   systemctl start mariadb
   systemctl start freeswitch
   systemctl stop firewalld			
   chkconfig --levels 345 mariadb on
   chkconfig --levels 345 freeswitch on
   chkconfig --levels 123456 firewalld off

   Note:- If you want to use firewall then configure it to allow all port used in fs and ASTPP.

**Setup cron**
::
 
    # Generate Invoice   
    0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice

    # Low balance notification
    0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php UpdateBalance

    # Low balance notification
    0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance

    # Update currency rate
    0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php CurrencyUpdate


    # Email Broadcasting
    0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php BroadcastEmail
    
    
**Finally Reboot it.**
::

     #You are almost done with your configuration so just reboot it and make sure everything is working fine.
 
     reboot now

     #Once server up and running again, check below service status.
     systemctl status nginx
     systemctl status mariadb
     systemctl status freeswitch
     systemctl status php-fpm


.. note:: 
     You are done with GUI installation. Enjoy :)
     Visit the astpp admin page in your web browser. It can be found here: http://server_ip:8089/ Please change the 
     ip address depending upon your box. The default username and password is “admin”. 

     Note : In case of any issue please refer apache error log.

.. note:: 
     If you have any other question(s) then please contact us on sales@inextrix.com or post your questions(s) 
     in https://groups.google.com/forum/#!forum/astpp.

