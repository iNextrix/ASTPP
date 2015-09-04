#!/bin/bash
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd. - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################

#################################
##########  variables ###########
#################################
TEMP_USER_ANSWER="no"
INSTALL_ASTPP="no"
ASTPP_SOURCE_DIR="/usr/src/trunk"
ASTPP_HOST_DOMAIN_NAME="host.domain.tld"

#ASTPP Configuration
ASTPPDIR=/var/lib/astpp/
ASTPPEXECDIR=/usr/local/astpp/
ASTPPLOGDIR=/var/log/astpp/
LOCALE_DIR=/usr/local/share/locale

#Freeswich Configuration
FS_DIR=/usr/local/freeswitch
FS_SOUNDSDIR=${FS_DIR}/sounds/en/us/callie
FS_SCRIPTS=${FS_DIR}/scripts

CGIDIR=/var/www
WWWDIR=/var/www/html

ASTPP_USING_FREESWITCH="no"
ASTPP_USING_ASTERISK="no"
INSTALL_ASTPP_PERL_PACKAGES="no"
INSTALL_ASTPP_WEB_INTERFACE="no"

ASTPP_DATABASE_NAME="astpp"

ASTPP_DB_USER="astppuser"

MYSQL_ROOT_PASSWORD=""
ASTPPUSER_MYSQL_PASSWORD=""


#################################
####  general functions #########
#################################

# task of function: ask to user yes or no
# usage: ask_to_user_yes_or_no "your question"
# return TEMP_USER_ANSWER variable filled with "yes" or "no"
ask_to_user_yes_or_no () {
	# default answer = no
	TEMP_USER_ANSWER="no"
	clear
	echo ""
	echo -e ${1}
	read -n 1 -p "(y/n)? :"
	if [ ${REPLY} = "y" ]; then
		TEMP_USER_ANSWER="yes"
	else
		TEMP_USER_ANSWER="no"
	fi
}

# Determine the OS architecture
get_os_architecture () {
	if [ ${HOSTTYPE} == "x86_64" ]; then
		ARCH=x64
	else
		ARCH=x32
	fi
}
get_os_architecture

# Linux Distribution CentOS or Debian
get_linux_distribution () {
	if [ -f /etc/debian_version ]; then
		DIST="DEBIAN"
	elif  [ -f /etc/redhat-release ]; then
		DIST="CENTOS"
	else
		DIST="OTHER"
	fi
}
get_linux_distribution

# get ip of eth0
get_local_ip () {
LOCAL_IP=`ifconfig eth0 | head -n2 | tail -n1 | cut -d' ' -f12 | cut -c 6-`
}
get_local_ip

install_epel () {
# only on CentOS
	if [ ${ARCH} = "x64" ]; then
		rpm -Uvh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
	else
		rpm -Uvh http://download.fedoraproject.org/pub/epel/6/i386/epel-release-6-8.noarch.rpm
	fi
}

install_epel

remove_epel () {
# only on CentOS
yum remove epel-release
}

# Generate random password (for MySQL)
genpasswd() {
	length=$1
	[ "$length" == "" ] && length=16
	tr -dc A-Za-z0-9_ < /dev/urandom | head -c ${length} | xargs
}

MYSQL_ROOT_PASSWORD=$(genpasswd)
ASTPPUSER_MYSQL_PASSWORD=$(genpasswd)

#################################
########  ASK SCRIPTS ###########
#################################

# Ask to install astpp
ask_to_install_astpp () {

	# License acceptance
	yum -y install wget
	clear
	echo "********************"
	echo "License acceptance"
	echo "********************"
	
	if [ -f LICENSE ]; then
		more LICENSE
	else
		wget -q -O GNU-GPLv2.0.txt https://raw.githubusercontent.com/ASTPP/ASTPP/master/LICENSE
        	more GNU-GPLv2.0.txt	
	fi

	echo "***"
	echo "*** I agree to be bound by the terms of the license - [YES/NO]"
	echo "*** " 
	read ACCEPT

	while [ "$ACCEPT" != "yes" ] && [ "$ACCEPT" != "Yes" ] && [ "$ACCEPT" != "YES" ] && [ "$ACCEPT" != "no" ] && [ "$ACCEPT" != "No" ] && [ "$ACCEPT" != "NO" ]; do
	echo "I agree to be bound by the terms of the license - [YES/NO]"
	read ACCEPT
	done

	if [ "$ACCEPT" != "yes" ] && [ "$ACCEPT" != "Yes" ] && [ "$ACCEPT" != "YES" ]; then
		echo "License rejected !"
		exit 0
	else
		echo "Licence accepted !"
	fi


	ask_to_user_yes_or_no "Do you want to install ASTPP?"
	if [ ${TEMP_USER_ANSWER} = "yes" ]; then
		  INSTALL_ASTPP="yes"
		  echo ""
		  read -p "Enter FQDN example (i.e ${ASTPP_HOST_DOMAIN_NAME}): "
		  ASTPP_HOST_DOMAIN_NAME=${REPLY}
		  echo "Your entered FQDN is : ${ASTPP_HOST_DOMAIN_NAME} "
		  echo ""

		  read -p "Enter your email address: ${EMAIL}"
		  EMAIL=${REPLY}

		  read -n 1 -p "Press any key to continue ... "
		  
		  ask_to_user_yes_or_no "Do you want use FreeSwitch on ASTPP?"
		  if 	[ ${TEMP_USER_ANSWER} = "yes" ]; then
  			    ASTPP_USING_FREESWITCH="yes"			  
		  fi

		  ask_to_user_yes_or_no "Do you want to install ASTPP PERL PACKAGES?"
			  if [ ${TEMP_USER_ANSWER} = "yes" ]; then
				  INSTALL_ASTPP_PERL_PACKAGES="yes"
			  fi
		  
		  ask_to_user_yes_or_no "Do you want to install ASTPP web interface?"
			  if [ ${TEMP_USER_ANSWER} = "yes" ]; then
				  INSTALL_ASTPP_WEB_INTERFACE="yes"
			  fi	 
	fi
}
ask_to_install_astpp


#################################
####  INSTALL SCRIPTS ###########
#################################

clear

if [ "${UPGRADE_ASTPP}" = "yes" ]; then
    echo "Starting upgradation !"
else
    echo "Starting installation !"
fi 
   
echo -e "Are you ready?"
read -n 1 -p "Press any key to continue ... "
clear


# install freeswitch for astpp
install_freeswitch_for_astpp () {  
  
    if [ ${DIST} = "DEBIAN" ]; then
      
	apt-get update

	# Install Freeswitch pre-requisite packages using apt-get
	apt-get install -y autoconf automake devscripts gawk g++ git-core libjpeg62-turbo-dev libncurses5-dev libtool make python-dev pkg-config libperl-dev libgdbm-dev libdb-dev gettext sudo lua5.1 apache2 apache2-threaded-dev php5 php5-dev php5-common php5-cli php5-gd php-pear php5-cli php-apc php5-curl libapache2-mod-php5 perl libapache2-mod-perl2 libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc libldns-dev libpcre3-dev build-essential libssl-dev libspeex-dev libspeexdsp-dev libsqlite3-dev libedit-dev libldns-dev libpq-dev bc
	
	echo mysql-server mysql-server/root_password password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
	echo mysql-server mysql-server/root_password_again password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
	
    apt-get install -y mysql-server php5-mysql chkconfig ntpdate ntp
	
    elif  [ ${DIST} = "CENTOS" ]; then

	yum install -y git
	
	# Install Freeswitch pre-requisite packages using yum
	yum install -y autoconf automake  expat-devel gnutls-devel libtiff-devel libX11-devel unixODBC-devel python-devel zlib-devel alsa-lib-devel libogg-devel libvorbis-devel perl perl-libs uuid-devel @development-tools gdbm-devel db4-devel libjpeg libjpeg-devel compat-libtermcap ncurses ncurses-devel ntp screen sendmail sendmail-cf gcc-c++ libtool cpan @development-tools bison bzip2 curl curl-devel dmidecode git make mysql-connector-odbc openssl-devel unixODBC zlib pcre-devel speex-devel sqlite-devel ldns-devel libedit-devel perl-ExtUtils-Embed bc
	
	/etc/init.d/iptables stop
	setenforce 0

    fi  

    curl --data "email=$EMAIL" --data "type=script" http://demo.astpp.org/lib/
 
    echo "Lets first make sure that time is correct before we continue ... "
    # set right time
    set_right_time () {
	echo "Setting up correct time ..."
	ntpdate pool.ntp.org
	if [ ${DIST} = "DEBIAN" ]; then
	  /etc/init.d/ntp restart
	  chkconfig ntp on
	else [ -f /etc/redhat-release ]
	  /etc/init.d/ntpd restart
	  chkconfig ntpd on
	fi
    }
    set_right_time
    
    # Download latest freeswitch version
    cd /usr/local/src
    git clone -b v1.4 https://freeswitch.org/stash/scm/fs/freeswitch.git
    cd freeswitch
    ./bootstrap.sh -j

    # Edit modules.conf
    echo "Enabling mod_xml_curl"

    sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
    sed -i "s#\#mod_xml_cdr#mod_xml_cdr#g" /usr/local/src/freeswitch/modules.conf

    # Compile the Source
    ./configure
        
    # Install Freeswitch with sound files
    make all install cd-sounds-install cd-moh-install
    make && make install

    # Create symbolic links for Freeswitch executables
    ln -s /usr/local/freeswitch/bin/freeswitch /usr/local/bin/freeswitch
    ln -s /usr/local/freeswitch/bin/fs_cli /usr/local/bin/fs_cli

    echo ""
    read -p "Do you want to configure and install mod_perl (for Calling Cards) for FreeSWITCH (y/n)? " YESNO
    if [ $YESNO == "y" ]; then
	sed -i "s#\#languages/mod_perl#languages/mod_perl#g" /usr/local/src/freeswitch/modules.conf
	./configure
	make mod_perl-install
    else
	echo "Not installing mod_perl for FreeSWITCH !"
	# Comment mod_perl, so it will not load on Freeswitch startup
	sed -i '/<load module="mod_perl"\/>/s/^/<!--/;//s/$/-->/' /usr/local/freeswitch/conf/autoload_configs/modules.conf.xml
    fi
    
}

#SUB Configure astpp Freeswitch Startup Script
astpp_freeswitch_startup_script () {

    if [ ! -d ${ASTPP_SOURCE_DIR} ]; then
        echo "ASTPP source doesn't exists, downloading it from git !"
	cd /usr/src/
        git clone https://github.com/ASTPP/trunk.git
    fi 
    
    if [ ${DIST} = "DEBIAN" ]; then
	  adduser --disabled-password  --quiet --system --home ${FS_DIR} --gecos "FreeSWITCH Voice Platform" --ingroup daemon freeswitch
	  chown -R freeswitch:daemon ${FS_DIR}/
	  chmod -R o-rwx ${FS_DIR}/
	  chmod -R u=rwx,g=rx ${FS_DIR}/bin/*
	  cp ${ASTPP_SOURCE_DIR}/freeswitch/init/freeswitch.debian.init /etc/init.d/freeswitch

    elif  [ ${DIST} = "CENTOS" ]; then
	  cp ${ASTPP_SOURCE_DIR}/freeswitch/init/freeswitch.centos.init /etc/init.d/freeswitch
    fi
  
    chmod 755 /etc/init.d/freeswitch
    chkconfig --add freeswitch
    chkconfig --level 345 freeswitch on
}

startup_services() {
# Startup Services
    if [ ${DIST} = "DEBIAN" ]; then
	chkconfig --add apache2
	chkconfig --level 345 apache2 on
	chkconfig --add mysql
	chkconfig --level 345 mysql on	
		
	/etc/init.d/mysql restart
	/usr/sbin/a2ensite astpp
	/etc/init.d/apache2 restart
	/etc/init.d/freeswitch restart
	
    elif  [ ${DIST} = "CENTOS" ]; then
	chkconfig --add httpd
	chkconfig --levels 35 httpd on
	chkconfig --add mysqld
	chkconfig --levels 35 mysqld on
	
	/etc/init.d/mysqld restart
	/etc/init.d/httpd restart
	/etc/init.d/freeswitch restart
    fi
}

# Setup MySQL For ASTPP
mySQL_for_astpp () {
	# Start MySQL server
	if [ ${DIST} = "DEBIAN" ]; then
	    /etc/init.d/mysql restart
	else [ -f /etc/redhat-release ]
	    /etc/init.d/mysqld restart
	fi

# Configure MySQL server
sleep 5
#MYSQL_ROOT_PASSWORD=$(genpasswd)
#ASTPPUSER_MYSQL_PASSWORD=$(genpasswd)
mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('${MYSQL_ROOT_PASSWORD}') WHERE user='root'; FLUSH PRIVILEGES;"
    
# Save MySQL root password to a text file in /root
echo ""
echo "MySQL password set to '${MYSQL_ROOT_PASSWORD}'. Remember to delete ~/.mysql_passwd" | tee ~/.mysql_passwd
echo "" >>  ~/.mysql_passwd
echo "MySQL astppuser password:  ${ASTPPUSER_MYSQL_PASSWORD} " >>  ~/.mysql_passwd
chmod 400 ~/.mysql_passwd
read -n 1 -p "*** Press any key to continue ..."


# Create astpp database
mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "create database ${ASTPP_DATABASE_NAME};"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '${ASTPPUSER_MYSQL_PASSWORD}';"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON \`${ASTPP_DATABASE_NAME}\` . * TO 'astppuser'@'localhost' WITH GRANT OPTION;FLUSH PRIVILEGES;"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/sql/astpp-2.0.sql
mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/sql/astpp-upgrade-2.1.sql
mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/sql/astpp-upgrade-2.2.sql
}


install_astpp () {

	# Download ASTPP
	if [ ! -d ${ASTPP_SOURCE_DIR} ]; then
              echo "ASTPP source doesn't exists, downloading it from git !"
              cd /usr/src/
              git clone https://github.com/ASTPP/trunk.git
    	fi

	if [ ${DIST} = "DEBIAN" ]; then
	      # Install ASTPP pre-requisite packages using apt-get
	      apt-get install -y apache2 apache2-threaded-dev php5 php5-dev php5-common php5-cli php5-gd php-pear php5-cli php-apc php5-curl libapache2-mod-php5 perl libapache2-mod-perl2 libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext libtool gcc g++
	      
	      echo "MySQL root password is set to : ${MYSQL_ROOT_PASSWORD}" 
	      echo "astppuser password is set to : ${ASTPPUSER_MYSQL_PASSWORD}"
	      echo mysql-server mysql-server/root_password password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
	      echo mysql-server mysql-server/root_password_again password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
	      
          apt-get install -y mysql-server php5-mysql


	elif  [ ${DIST} = "CENTOS" ]; then
	      # Install ASTPP pre-requisite packages using YUM
	      yum install -y cpan autoconf automake bzip2 cpio curl curl-devel php php-devel php-common php-cli php-gd php-pear php-mysql php-pdo php-pecl-json mysql mysql-server mysql-devel libxml2 libxml2-devel openssl openssl-devel gettext-devel libtool fileutils gcc-c++ httpd httpd-devel perl-YAML cpan perl
	fi	
	
#	cd ${ASTPP_SOURCE_DIR}	

	if [ ${DIST} = "DEBIAN" ]; then
	  echo "Normalize ASTPP for Debian"
	  #sed -i "s#APACHE=/etc/httpd#APACHE=/etc/apache2#g" Makefile
	  sed -i "s#/var/log/httpd/astpp_access_log#/var/log/apache2/astpp_access_log#g" ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf
	  sed -i "s#/var/log/httpd/astpp_error_log#/var/log/apache2/astpp_error_log#g" ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf
	  touch /var/log/apache2/astpp_access_log
	  touch /var/log/apache2/astpp_error_log	  
	fi
	# make
	
	if [ ${INSTALL_ASTPP_PERL_PACKAGES} = "yes" ]; then
		perl -MCPAN -e 'my $c = "CPAN::HandleConfig"; $c->load(doit => 1, autoconfig => 1); $c->edit(prerequisites_policy => "follow"); $c->edit(build_requires_install_policy => "yes"); $c->commit'

		perl -MCPAN -e "install Data::Dumper,URI::Escape,JSON,POSIX,DBI,Time::HiRes,DateTime::Format::Strptime,XML::Simple,CGI";
		
	fi
	
	if [ ${ASTPP_USING_FREESWITCH} = "yes" ]; then
				
		#Folder creation and permission
		mkdir -p ${ASTPPDIR}		
		mkdir -p ${ASTPPLOGDIR}		
		mkdir -p ${ASTPPEXECDIR}
		
		if [ ${DIST} = "DEBIAN" ]; then
		    chown -Rf www-data.www-data ${ASTPPDIR}
		    chown -Rf www-data.www-data ${ASTPPLOGDIR}
		    chown -Rf www-data.www-data ${ASTPPEXECDIR}
		    CGIDIR=/usr/lib
		    chown -Rf www-data.www-data ${CGIDIR}/cgi-bin/
		elif [ ${DIST} = "CENTOS" ]; then
		    chown -Rf apache.apache ${ASTPPDIR}
		    chown -Rf apache.apache ${ASTPPLOGDIR}
		    chown -Rf apache.apache ${ASTPPEXECDIR}
		    CGIDIR=/var/www
		    chown -Rf apache.apache ${CGIDIR}/cgi-bin/
		fi
		
		cp -rf ${ASTPP_SOURCE_DIR}/scripts/*.pl ${ASTPPEXECDIR}/
		
		#Copy cgi scripts to cgi-bin
		cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/astpp ${CGIDIR}/cgi-bin/
	        chmod -Rf 777 ${CGIDIR}/cgi-bin/astpp
		
		#copy calling card script to freeswitch script folder
		cp ${ASTPP_SOURCE_DIR}/freeswitch/astpp-callingcards.pl ${FS_SCRIPTS}/astpp-callingcards.pl
				
		cp -rf ${ASTPP_SOURCE_DIR}/sounds/*.wav ${FS_SOUNDSDIR}/
		chmod -Rf 777 ${FS_SOUNDSDIR}
	fi

	if [ ${INSTALL_ASTPP_WEB_INTERFACE} = "yes" ]; then
		
		echo "installing ASTPP web interface"
		
		mkdir -p ${ASTPPDIR}		
		#Copy configuration file
		cp ${ASTPP_SOURCE_DIR}/astpp_confs/sample.astpp-config.conf ${ASTPPDIR}astpp-config.conf

		#Install GUI of ATSPP
		mkdir -p ${WWWDIR}/astpp
		
		echo "Directory created ${WWWDIR}/astpp"
		
		cp -rf ${ASTPP_SOURCE_DIR}/web_interface/astpp/* ${WWWDIR}/astpp/
		cp ${ASTPP_SOURCE_DIR}/web_interface/astpp/htaccess ${WWWDIR}/astpp/.htaccess
		
		if [ ${DIST} = "DEBIAN" ]; then
			chown -Rf www-data.www-data ${WWWDIR}/astpp
			cp ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf /etc/apache2/sites-available/astpp.conf
			a2ensite astpp
			a2ensite astpp.conf
			/etc/init.d/apache2 restart
		elif  [ ${DIST} = "CENTOS" ]; then
			chown -Rf apache.apache ${WWWDIR}/astpp
			cp ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf /etc/httpd/conf.d/astpp.conf
		fi
		chmod -Rf 777 ${WWWDIR}/astpp
	fi	
	touch /var/log/astpp/astpp.log
}



finalize_astpp_installation () {

    # /etc/php.ini short_open_tag = On 
    # short_open_tag = Off   to short_open_tag = On        
    
    echo "Make sure Short Open Tag is switched On"    
    if [ ${DIST} = "DEBIAN" ]; then
	    sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php5/apache2/php.ini
	    a2enmod rewrite
    else [ -f /etc/redhat-release ]
	    sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php.ini
    fi
    
    /bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/conf/autoload_configs/* /usr/local/freeswitch/conf/autoload_configs/

    
    # edit ASTPP Database Connection Information
    # /var/lib/astpp/astpp-config.conf
    sed -i "s#dbpass = <PASSSWORD>#dbpass = ${MYSQL_ROOT_PASSWORD}#g" ${ASTPPDIR}astpp-config.conf
    sed -i "s#base_url=http://localhost:8081/#base_url=http://${ASTPP_HOST_DOMAIN_NAME}:8081/#g" ${ASTPPDIR}/astpp-config.conf
}

setup_cron(){

   if [ ${DIST} = "DEBIAN" ]; then
  	CRONPATH='/var/spool/cron/crontabs/astpp'
   elif [ ${DIST} = "CENTOS" ]; then
	CRONPATH='/var/spool/cron/astpp'
   fi

echo "# Generate Invoice   
0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice

# Low balance notification
0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php UpdateBalance
          
# Low balance notification
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance

# Low credit notification
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowCredit
          
# Update currency rate
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php CurrencyUpdate
" > $CRONPATH

   chmod 600 $CRONPATH
   crontab $CRONPATH
}

install_perl_packages(){
     echo "Installing missing cpan packages ..."     
     cpan -fi Data::Dumper URI::Escape JSON POSIX,DBI Time::HiRes DateTime::Format::Strptime XML::Simple CGI

}


install_fail2ban(){

	read -n 1 -p "Do you want to install and configure Fail2ban ? (y/n) "
	if [ "$REPLY"   = "y" ]; then
		
		if [ -f /etc/debian_version ] ; then
			DIST="DEBIAN"
			apt-get -y install fail2ban

		elif [ -f /etc/redhat-release ] ; then
			DIST="CENTOS"
			echo ""
			echo "Downloading sources"
				cd /usr/src
				service iptables stop
				wget -T 10 -t 1 http://sourceforge.net/projects/fail2ban/files/fail2ban-stable/fail2ban-0.8.4/fail2ban-0.8.4.tar.bz2
				echo "/!\IF FILE COULD BE DOWNLOADED, MAKE SURE TO UPLOAD SOURCE ARCHIVE [fail2ban-0.8.4.tar.bz2] MANUALLY IN [/usr/src/] DIRECTORY/!\"
				echo "/!\PRESS [CTRL-C] TO ABORT OR [ENTER] WHEN SOURCE ARCHIVE IS UPLOADED OR DOWNLOADED/!\"
				read -e OK

				if [ ! -f /usr/src/fail2ban-0.8.4.tar.bz2 ] ; #File that you are looking for isn't there
				then
				    echo "/!\ STOP /!\ FILE fail2ban-0.8.4.tar.bz2 NOT AVAILABLE IN /USR/SRC/"
				    echo "Aborting Installation"
						exit
					fi

			echo "################################################################"
			echo "File OK, unarchiving in progress"
				tar -jxf fail2ban-0.8.4.tar.bz2
				cd fail2ban-0.8.4

			echo "################################################################"
			echo "Fail2Ban installation in progress"
				python setup.py install
				cp /usr/src/fail2ban-0.8.4/files/redhat-initd /etc/init.d/fail2ban
				chmod 755 /etc/init.d/fail2ban
			echo "Installation done"

			echo "################################################################"

			echo "################################################################"
			echo "Auto Configuration in progress"
			echo "-- Writing /etc/fail2ban/filter.d/freeswitch.conf file"
			touch /etc/fail2ban/filter.d/freeswitch.conf
			cp /etc/fail2ban/filter.d/freeswitch.conf /etc/fail2ban/filter.d/freeswitch.bak
		else
			echo "***"
			echo "*** This Installer should be run only on CentOS 6.x or Debian based system"
			echo "***"
			exit 1
		fi

		echo "# Fail2Ban configuration file
[Definition]
# Option: failregex
# Notes.: regex to match the password failures messages in the logfile. The
# host must be matched by a group named "host". The tag '<HOST>' can
# be used for standard IP/hostname matching and is only an alias for
# (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values: TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>
\[WARNING\] sofia_reg.c:\d+ SIP auth failure \(INVITE\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>
# Option: ignoreregex
# Notes.: regex to ignore. If this regex matches, the line is ignored.
# Values: TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/freeswitch.conf
		echo "# Fail2Ban configuration file
[Definition]
# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag '<HOST>' can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>
# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/freeswitch-dos.conf
		################################# FREESWITCH.CONF FILE READY ##################
		echo "-- Modifying /etc/fail2ban/jail.conf file"
		################################# JAIL.CONF FILE WRITING ####################
		cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.bak
		echo "
[freeswitch]
enabled = true
port = 5060,5061,5080,5081
filter = freeswitch
logpath = /usr/local/freeswitch/log/freeswitch.log
maxretry = 10
bantime = 10000000
findtime = 480
action = iptables-allports[name=freeswitch, protocol=all]
sendmail-whois[name=FreeSwitch, dest=$EMAIL, sender=fail2ban@${ASTPP_HOST_DOMAIN_NAME}]
" >> /etc/fail2ban/jail.local
		echo "
[freeswitch-dos]
enabled = true
port = 5060,5061,5080,5081
filter = freeswitch-dos
logpath = /usr/local/freeswitch/log/freeswitch.log
action = iptables-allports[name=freeswitch-dos, protocol=all]
maxretry = 50
findtime = 30
bantime = 6000
" >> /etc/fail2ban/jail.local
		################################# JAIL.CONF FILE READY ######################

		echo "################################################################"
		echo "Auto Configuration Completed"

		if [ -f /etc/redhat-release ] ; then
			echo "Restarting IPtables"
			/etc/init.d/iptables start
		fi

		echo "Starting Fail2Ban Integration"
		/etc/init.d/fail2ban start
		
		if [ -f /etc/redhat-release ] ; then
			echo "Restarting IPtables"
			/etc/init.d/iptables restart
		fi

		/etc/init.d/fail2ban restart

		if [ -f /etc/redhat-release ] ; then
			chkconfig iptables on
		fi
		chkconfig fail2ban on

		echo "################################################################"
		echo "Fail2Ban for FreeSwitch & IPtables Integration completed"
				
	else
		echo ""
		echo "Fail2ban installation is aborted !"
	fi   
}


astpp_install () {

	if [ ${ASTPP_USING_FREESWITCH} = "yes" ]; then
		install_freeswitch_for_astpp
                astpp_freeswitch_startup_script
		echo ""
		echo "FreeSWITCH is Installed"
	fi
	
	install_astpp
	mySQL_for_astpp
	finalize_astpp_installation
	install_perl_packages
	setup_cron
	startup_services	

	clear
	echo "---------------------"
	echo "| Login information |"
	echo "---------------------"
	echo "http://${ASTPP_HOST_DOMAIN_NAME}:8081 "
	echo "Username= admin "
	echo "Password= admin "
	echo ""

	sleep 5
	echo ""	
	install_fail2ban
}


# Install astpp
start_install_astpp () {
	if [ ${DIST} = "CENTOS" ]; then
		astpp_install
	elif [ ${DIST} = "DEBIAN" ]; then
		astpp_install
	else
		echo "Can't install with this script on your OS"
	fi
}
if [ ${INSTALL_ASTPP} = "yes" ]; then
	start_install_astpp
fi

