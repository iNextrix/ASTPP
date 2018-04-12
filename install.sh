#!/bin/bash
###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# ASTPP Version 3.5
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################


#################################
##########  variables ###########
#################################
TEMP_USER_ANSWER="no"
INSTALL_ASTPP="no"
CURRENT_DIR="${PWD}"
DOWNLOAD_DIR="/usr/src"
ASTPP_SOURCE_DIR=/usr/src/latest
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
WWWDIR=/var/www/html

ASTPP_USING_FREESWITCH="no"
ASTPP_USING_ASTERISK="no"
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
ask_to_user_yes_or_no () 
{
		# default answer = no
		TEMP_USER_ANSWER="no"
		clear
		echo ""
		echo -e ${1}
		read -n 1 -p "(y/n)? :"
		if [ "${REPLY}" = "y" ]; then
			TEMP_USER_ANSWER="yes"
		else
			TEMP_USER_ANSWER="no"
		fi
}

# Determine the OS architecture
get_os_architecture () 
{
		if [ ${HOSTTYPE} == "x86_64" ]; then
			ARCH=x64
		else
			ARCH=x32
		fi
}
get_os_architecture

# Linux Distribution CentOS or Debian
get_linux_distribution ()
{ 
	V1=`cat /etc/*release | head -n1 | tail -n1 | cut -c 14- | cut -c1-18`
	V2=`cat /etc/*release | head -n7 | tail -n1 | cut -c 14- | cut -c1-14`
	PHPV=`php -v |sed -n 1p|awk '{ print $1 $2 }'|cut -c 1-4`
	if [ "$V1" = "Debian GNU/Linux 8" ]; then
		DIST="DEBIAN"
		else if [ "$V2" = "CentOS Linux 7" ]; then
			DIST="CENTOS"
		else
			DIST="OTHER"
			echo 'Opps!! Quick Installation does not support your distribution'
			exit 1
		fi
	fi
}
get_linux_distribution
install_php7 ()
{
	if [ "$DIST" = "DEBIAN" ]; then
		echo "
deb http://packages.dotdeb.org jessie all
deb-src http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/php7.list
		curl https://www.dotdeb.org/dotdeb.gpg | apt-key add -
		apt-get update
		else if [ "$DIST" = "CENTOS" ]; then
			yum install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
			yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
			yum install yum-utils
			yum-config-manager --enable remi-php70
		fi
	fi 
}
# Generate random password (for MySQL)
genpasswd() 
{
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
ask_to_install_astpp () 
{        
        if [ ${DIST} = "CENTOS" ]; then
            yum install -y wget
        fi
		# License acceptance		
		clear		
		echo "********************"
		echo "License acceptance"
		echo "********************"		
		if [ -f LICENSE ]; then
			more LICENSE
		else
			wget --no-check-certificate -q -O GNU-AGPLv3.6.txt https://raw.githubusercontent.com/iNextrix/ASTPP/master/LICENSE
			more GNU-AGPLv3.6.txt	
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
			echo "License rejected!"
			exit 0
		else
			echo "Licence accepted!"
			install_php7
			echo "============checking your working directory=================="			
			git clone -b v3.6 https://github.com/iNextrix/ASTPP.git
			cp -rf ASTPP latest			
			if [ ${CURRENT_DIR} == ${DOWNLOAD_DIR} ]; then
				echo "dir is '$CURRENT_DIR' and it's matched!!!"			
			else			
				echo "dir is '$CURRENT_DIR' and not matched!!!"
				mv -f ${CURRENT_DIR}/latest ${DOWNLOAD_DIR}/.			
				clear
				echo "====================Starting installation again======================"
				sleep 10
				cd ${ASTPP_SOURCE_DIR} && chmod +x install.sh && ./install.sh			
				clear
			fi
		fi
		ask_to_user_yes_or_no "Do you want to install ASTPP?"
		if [ "${TEMP_USER_ANSWER}" = "yes" ]; then
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
			ask_to_user_yes_or_no "Do you want to install ASTPP web interface?"
			if [ ${TEMP_USER_ANSWER} = "yes" ]; then
				INSTALL_ASTPP_WEB_INTERFACE="yes"
			fi	 
		fi
		echo "Installation Done"
}
ask_to_install_astpp


#################################
####  INSTALL SCRIPTS ###########
#################################

clear
echo -e "Are you ready?"
read -n 1 -p "Press any key to continue ... "
clear

# install freeswitch for astpp
install_freeswitch_for_astpp () 
{  
		if [ ${DIST} = "DEBIAN" ]; then
			apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y curl
			curl https://files.freeswitch.org/repo/deb/debian/freeswitch_archive_g0.pub | apt-key add -
			echo "deb http://files.freeswitch.org/repo/deb/freeswitch-1.6/ jessie main" > /etc/apt/sources.list.d/freeswitch.list
			apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y --force-yes freeswitch-video-deps-most
			# Install Freeswitch pre-requisite packages using apt-get
			apt-get install -y autoconf automake devscripts gawk chkconfig dnsutils sendmail-bin sensible-mda ntpdate ntp g++ git-core curl libjpeg62-turbo-dev libncurses5-dev make python-dev pkg-config libgdbm-dev libyuv-dev libdb-dev libvpx2-dev gettext sudo lua5.1 php7.0 php7.0-dev php7.0-common php7.0-cli php7.0-gd php-pear php7.0-cli php-apc php7.0-curl libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc libldns-dev libpcre3-dev build-essential libssl-dev libspeex-dev libspeexdsp-dev libsqlite3-dev libedit-dev libldns-dev libpq-dev bc
			
			#-------------------MySQL setup in for freeswitch Start ------------------------
			clear
			echo "======================Mysql installation start======================="
			sleep 20
			echo "MySQL root password is set to : ${MYSQL_ROOT_PASSWORD}" 
			echo "astppuser password is set to : ${ASTPPUSER_MYSQL_PASSWORD}"
			echo mysql-server mysql-server/root_password password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
			echo mysql-server mysql-server/root_password_again password ${MYSQL_ROOT_PASSWORD} | debconf-set-selections
			apt-get install -y mysql-server php7.0-mysql
			echo "======================Mysql installation end======================="
			sleep 20
			#-------------------MySQL setup in for freeswitch End ------------------------
			
	    elif  [ ${DIST} = "CENTOS" ]; then
			# Install Freeswitch pre-requisite packages using yum
			yum groupinstall "Development tools" -y
			rpm -Uvh http://files.freeswitch.org/freeswitch-release-1-6.noarch.rpm
			yum install -y wget git autoconf automake expat-devel yasm nasm gnutls-devel libtiff-devel libX11-devel unixODBC-devel python-devel zlib-devel alsa-lib-devel libogg-devel libvorbis-devel uuid-devel @development-tools gdbm-devel db4-devel libjpeg libjpeg-devel compat-libtermcap ncurses ncurses-devel ntp screen sendmail sendmail-cf gcc-c++ @development-tools bison bzip2 curl curl-devel dmidecode git make mysql-connector-odbc openssl-devel unixODBC zlib pcre-devel speex-devel sqlite-devel ldns-devel libedit-devel bc e2fsprogs-devel libcurl-devel libxml2-devel libyuv-devel opus-devel libvpx-devel libvpx2* libdb4* libidn-devel unbound-devel libuuid-devel lua-devel libsndfile-devel
		fi
		NAT1=$(dig +short myip.opendns.com @resolver1.opendns.com)
		NAT2=$(curl http://ip-api.com/json/)
		INTF=$(ifconfig $1|sed -n 2p|awk '{ print $2 }'|awk -F : '{ print $2 }')
		if [ "${NAT1}" != "${INTF}" ]; then
			echo "Server is behind NAT";
		fi
		curl --data "email=$EMAIL" --data "data=$NAT2" --data "type=Install" http://astppbilling.org/lib/
		echo "Lets first make sure that time is correct before we continue ... "
    
		# set right time
		set_right_time () 
		{
			echo "Setting up correct time ..."
			ntpdate pool.ntp.org
			if [ ${DIST} = "DEBIAN" ]; then
				systemctl restart ntp
				chkconfig ntp on
			else [ -f /etc/redhat-release ]
				systemctl restart ntpd
				chkconfig ntpd on
			fi
		}
		set_right_time
		
		#-----------------Freeswitch Installation Start------------------------------
		# Download latest freeswitch version
		cd /usr/local/src		
		git config --global pull.rebase true
		git clone -b v1.6.19 https://freeswitch.org/stash/scm/fs/freeswitch.git
		cd freeswitch
		./bootstrap.sh -j
		# Edit modules.conf
		
		sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
		sed -i "s#\#applications/mod_curl#applications/mod_curl#g" /usr/local/src/freeswitch/modules.conf
		sed -i "s#\#event_handlers/mod_json_cdr#event_handlers/mod_json_cdr#g" /usr/local/src/freeswitch/modules.conf
		sed -i "s#\#applications/mod_voicemail#applications/mod_voicemail#g" /usr/local/src/freeswitch/modules.conf
		
		# Compile the Source
		./configure -C
		# Install Freeswitch with sound files		
		make all install cd-sounds-install cd-moh-install
		make && make install
		# Create symbolic links for Freeswitch executables
		ln -s /usr/local/freeswitch/bin/freeswitch /usr/local/bin/freeswitch
		ln -s /usr/local/freeswitch/bin/fs_cli /usr/local/bin/fs_cli		
		#-----------------Freeswitch Installation End------------------------------
		if [ ${DIST} = "DEBIAN" ]; then
			systemctl stop apache2
			systemctl disable apache2			
		elif  [ ${DIST} = "CENTOS" ]; then
			systemctl stop httpd
			systemctl disable httpd			
		fi		

}

#SUB Configure astpp Freeswitch Startup Script
astpp_freeswitch_startup_script ()
{
		if [ ! -d ${ASTPP_SOURCE_DIR} ]; then
			echo "ASTPP source doesn't exists, downloading it..."
			cd /usr/src/			
			git clone -b v3.6 https://github.com/iNextrix/ASTPP.git
			cp -rf ASTPP latest			
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
	  	chmod +x /etc/init.d/freeswitch
		update-rc.d freeswitch defaults
		chkconfig --add freeswitch
		chkconfig --level 345 freeswitch on
		mkdir /var/run/freeswitch
		chown -R freeswitch:daemon  /var/run/freeswitch
}

startup_services() 
{
	# Startup Services
    if [ ${DIST} = "DEBIAN" ]; then
		chkconfig --add nginx
		chkconfig --level 345 nginx on
		chkconfig --add mysql
		chkconfig --level 345 mysql on			
		systemctl restart mysql
		systemctl restart nginx
		systemctl restart freeswitch
	elif  [ ${DIST} = "CENTOS" ]; then
		chkconfig --add nginx
		chkconfig --levels 35 nginx on
		chkconfig --add mysqld
		chkconfig --levels 35 mysqld on
		systemctl restart mariadb
		systemctl restart nginx
		systemctl restart freeswitch		
	fi
}

# Setup MySQL For ASTPP
mySQL_for_astpp () 
{
		# Start MySQL server
		if [ ${DIST} = "DEBIAN" ]; then
			systemctl restart mysql
		else [ -f /etc/redhat-release ]
		#	/etc/init.d/mysqld restart
			systemctl restart mariadb
		fi
		# Configure MySQL server
		sleep 5
		
		# Save MySQL root password to a text file in /root
		echo ""
		echo "MySQL password set to '${MYSQL_ROOT_PASSWORD}'. Remember to delete ~/.mysql_passwd" | tee ~/.mysql_passwd
		echo "" >>  ~/.mysql_passwd
		echo "MySQL astppuser password:  ${ASTPPUSER_MYSQL_PASSWORD} " >>  ~/.mysql_passwd
		chmod 400 ~/.mysql_passwd
		read -n 1 -p "*** Press any key to continue ..."

        if  [ ${DIST} = "CENTOS" ]; then
            mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('${MYSQL_ROOT_PASSWORD}') WHERE user='root'; FLUSH PRIVILEGES;"		
        fi 

		# Create astpp database
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "create database ${ASTPP_DATABASE_NAME};"
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '${ASTPPUSER_MYSQL_PASSWORD}';"
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON \`${ASTPP_DATABASE_NAME}\` . * TO 'astppuser'@'localhost' WITH GRANT OPTION;FLUSH PRIVILEGES;"		
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/database/astpp-3.0.sql
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/database/astpp-upgrade-3.5.sql
		mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/database/astpp-upgrade-3.6.sql
		if [ ${DIST} = "DEBIAN" ]; then
			apt-get install libmyodbc unixodbc-bin
			cp ${ASTPP_SOURCE_DIR}/misc/odbc/deb_odbc.ini /etc/odbc.ini
			cp ${ASTPP_SOURCE_DIR}/misc/odbc/deb_odbcinst.ini /etc/odbcinst.ini
		fi
		if  [ ${DIST} = "CENTOS" ]; then
			yum install unixODBC mysql-connector-odbc
			cp ${ASTPP_SOURCE_DIR}/misc/odbc/cent_odbc.ini /etc/odbc.ini
			cp ${ASTPP_SOURCE_DIR}/misc/odbc/cent_odbcinst.ini /etc/odbcinst.ini
		fi
		sed -i "s#PASSWORD = <PASSWORD>#PASSWORD = ${MYSQL_ROOT_PASSWORD}#g" /etc/odbc.ini
}

install_astpp () 
{
		# Download ASTPP
		if [ ! -d ${ASTPP_SOURCE_DIR} ]; then
			echo "ASTPP source doesn't exists, downloading it..."
			cd /usr/src/
			git clone -b v3.6 https://github.com/iNextrix/ASTPP.git
			cp -rf ASTPP latest			
    	fi
    	if [ ${DIST} = "DEBIAN" ]; then
			# Install ASTPP pre-requisite packages using apt-get
			systemctl stop apache2
			systemctl disable apache2
			apt-get -o Acquire::Check-Valid-Until=false update
			apt-get install -y curl libyuv-dev libvpx2-dev nginx php7.0-fpm php7.0 php7.0-mcrypt libmyodbc unixodbc-bin php7.0-dev php7.0-common php7.0-cli php7.0-gd php-pear php7.0-cli php-apc php7.0-curl libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc g++
		elif  [ ${DIST} = "CENTOS" ]; then
			# Install ASTPP pre-requisite packages using YUM
			yum install -y autoconf automake bzip2 cpio curl nginx php-fpm php-mcrypt* unixODBC mysql-connector-odbc curl-devel php php-devel php-common php-cli php-gd php-pear php-mysql php-pdo php-pecl-json mysql mariadb-server mysql-devel libxml2 libxml2-devel openssl openssl-devel gettext-devel fileutils gcc-c++ httpd httpd-devel
		fi	
		#	cd ${ASTPP_SOURCE_DIR}	
		if [ ${DIST} = "DEBIAN" ]; then
			echo "Normalize ASTPP for Debian"			
			touch /var/log/nginx/astpp_access_log
			touch /var/log/nginx/astpp_error_log
			touch /var/log/nginx/fs_access_log
			touch /var/log/nginx/fs_error_log			
			php5enmod mcrypt
			systemctl restart php7.0-fpm
			service nginx reload
		fi
		if [ ${DIST} = "CENTOS" ]; then
			systemctl stop httpd
			systemctl disable httpd
			systemctl start php-fpm			
		fi
		if [ ${ASTPP_USING_FREESWITCH} = "yes" ]; then
			#Folder creation and permission
			mkdir -p ${ASTPPDIR}		
			mkdir -p ${ASTPPLOGDIR}		
			mkdir -p ${ASTPPEXECDIR}
			if [ ${DIST} = "DEBIAN" ]; then
				chown -Rf root.root ${ASTPPDIR}
				chown -Rf www-data.www-data ${ASTPPLOGDIR}
				chown -Rf root.root ${ASTPPEXECDIR}				
			elif [ ${DIST} = "CENTOS" ]; then
				chown -Rf root.root ${ASTPPDIR}
				chown -Rf root.root ${ASTPPLOGDIR}
				chown -Rf root.root ${ASTPPEXECDIR}				
			fi
			
			#Setup FS-Scripts
			/bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/scripts/* ${FS_SCRIPTS}/
			/bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/fs /var/www/html/
						
			/bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/sounds/*.wav ${FS_SOUNDSDIR}/
			chmod -Rf 755 ${FS_SOUNDSDIR}
			rm -rf  /usr/local/freeswitch/conf/dialplan/*
			touch /usr/local/freeswitch/conf/dialplan/astpp.xml
			rm -rf  /usr/local/freeswitch/conf/directory/*
			touch /usr/local/freeswitch/conf/directory/astpp.xml
			rm -rf  /usr/local/freeswitch/conf/sip_profiles/*
			touch /usr/local/freeswitch/conf/sip_profiles/astpp.xml
		fi
		if [ ${INSTALL_ASTPP_WEB_INTERFACE} = "yes" ]; then
			echo "Installing ASTPP web interface"
			mkdir -p ${ASTPPDIR}		
			#Copy configuration file
			cp ${ASTPP_SOURCE_DIR}/config/astpp-config.conf ${ASTPPDIR}astpp-config.conf
			cp ${ASTPP_SOURCE_DIR}/config/astpp.lua ${ASTPPDIR}astpp.lua			
			#Install GUI of ATSPP
			mkdir -p ${WWWDIR}/astpp
			echo "Directory created ${WWWDIR}/astpp"
			cp -rf ${ASTPP_SOURCE_DIR}/web_interface/astpp/* ${WWWDIR}/astpp/			
			if [ ${DIST} = "DEBIAN" ]; then
				chown -Rf root.root ${WWWDIR}/astpp
				cp ${ASTPP_SOURCE_DIR}/web_interface/nginx/deb_astpp.conf /etc/nginx/sites-enabled/astpp.conf
				cp ${ASTPP_SOURCE_DIR}/web_interface/nginx/deb_fs.conf /etc/nginx/sites-enabled/fs.conf		
				sed -i "s/;request_terminate_timeout = 0/request_terminate_timeout = 300/" /etc/php/7.0/fpm/pool.d/www.conf
				sed -i "s/client_max_body_size 8M/client_max_body_size 20M/" /etc/nginx/sites-enabled/astpp.conf
				sed -i '35i fastcgi_read_timeout 300;' /etc/nginx/sites-enabled/astpp.conf
				systemctl restart nginx
			elif  [ ${DIST} = "CENTOS" ]; then
				chown -Rf root.root ${WWWDIR}/astpp
				cp ${ASTPP_SOURCE_DIR}/web_interface/nginx/cent_astpp.conf /etc/nginx/conf.d/astpp.conf
				cp ${ASTPP_SOURCE_DIR}/web_interface/nginx/cent_fs.conf /etc/nginx/conf.d/fs.conf
				sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/sysconfig/selinux
				sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/selinux/config
				sed -i "s/;request_terminate_timeout = 0/request_terminate_timeout = 300/" /etc/php-fpm.d/www.conf
				sed -i "s/client_max_body_size 8M/client_max_body_size 20M/" /etc/nginx/conf.d/astpp.conf
				sed -i '35i fastcgi_read_timeout 300;' /etc/nginx/conf.d/astpp.conf
				/etc/init.d/iptables stop
				chkconfig iptables off
				setenforce 0
			fi
			chmod -Rf 755 ${WWWDIR}/astpp
			chmod -Rf 755 ${WWWDIR}/fs
			if [ ${DIST} = "DEBIAN" ]; then
				chown -Rf www-data.www-data ${WWWDIR}/astpp
                chown -Rf www-data.www-data ${ASTPPLOGDIR}
				chown -Rf root.root ${WWWDIR}/fs
			elif [ ${DIST} = "CENTOS" ]; then
				chown -Rf apache.apache ${WWWDIR}/astpp
                chown -Rf apache.apache ${ASTPPLOGDIR}
				chown -Rf root.root ${WWWDIR}/fs
			fi
		fi	
		touch /var/log/astpp/astpp.log
}

finalize_astpp_installation () 
{
		# /etc/php.ini short_open_tag = On
		# short_open_tag = Off   to short_open_tag = On        
		echo "Make sure Short Open Tag is switched On"    
		if [ ${DIST} = "DEBIAN" ]; then
			sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php/7.0/fpm/php.ini
			sed -i "s#;cgi.fix_pathinfo=1#cgi.fix_pathinfo=1#g" /etc/php/7.0/fpm/php.ini
			sed -i "s/max_execution_time = 30/max_execution_time = 3000/" /etc/php/7.0/fpm/php.ini
			sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 20M/" /etc/php/7.0/fpm/php.ini
			sed -i "s/post_max_size = 8M/post_max_size = 20M/" /etc/php/7.0/fpm/php.ini
			sed -i "s/memory_limit = 128M/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini
			systemctl restart php7.0-fpm
			systemctl restart nginx
		elif [ ${DIST} = "CENTOS" ]; then
			sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php.ini
			sed -i "s#;cgi.fix_pathinfo=1#cgi.fix_pathinfo=1#g" /etc/php.ini
			sed -i "s/max_execution_time = 30/max_execution_time = 3000/" /etc/php.ini
			sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 20M/" /etc/php.ini
			sed -i "s/post_max_size = 8M/post_max_size = 20M/" /etc/php.ini
			sed -i "s/memory_limit = 128M/memory_limit = 512M/" /etc/php.ini
			
			#######   Some more steps for CentOS 7  #########
			yum update					
			sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/sysconfig/selinux
			sed -i "s/SELINUX=enforcing/SELINUX=disabled/" /etc/selinux/config
			setenforce 0			
			systemctl disable httpd
			systemctl enable nginx
			systemctl enable php-fpm			
			systemctl start mariadb
			systemctl start freeswitch
			systemctl stop firewalld			
			chkconfig --levels 345 mariadb on
			chkconfig --levels 345 freeswitch on
			chkconfig --levels 123456 firewalld off
		fi		
		/bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/conf/autoload_configs/* /usr/local/freeswitch/conf/autoload_configs/
			
		# edit ASTPP Database Connection Information
		# /var/lib/astpp/astpp-config.conf
		sed -i "s#dbpass = <PASSSWORD>#dbpass = ${MYSQL_ROOT_PASSWORD}#g" ${ASTPPDIR}astpp-config.conf
		sed -i "s#DB_PASSWD=\"<PASSSWORD>\"#DB_PASSWD = \"${MYSQL_ROOT_PASSWORD}\"#g" ${ASTPPDIR}astpp.lua
		sed -i "s#base_url=http://localhost:8081/#base_url=http://${ASTPP_HOST_DOMAIN_NAME}:8089/#g" ${ASTPPDIR}/astpp-config.conf
}

setup_cron()
{
		if [ ${DIST} = "DEBIAN" ]; then
			CRONPATH='/var/spool/cron/crontabs/astpp'
		elif [ ${DIST} = "CENTOS" ]; then
			CRONPATH='/var/spool/cron/astpp'
		fi
		echo "# Generate Invoice   
		0 12 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice
		# Update Balance notification
		0 12 * * * cd /var/www/html/astpp/cron/ && php cron.php UpdateBalance
		# Low balance notification
		0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance		
		# Update currency rate
		0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php CurrencyUpdate
		# Email Broadcasting
		* * * * * cd /var/www/html/astpp/cron/ && php cron.php BroadcastEmail
		" > $CRONPATH
		chmod 600 $CRONPATH
		crontab $CRONPATH
}

install_fail2ban()
{
		read -n 1 -p "Do you want to install and configure Fail2ban ? (y/n) "
		if [ "$REPLY"   = "y" ]; then
			if [ -f /etc/debian_version ] ; then
				DIST="DEBIAN"
				apt-get -y install fail2ban
				echo '[DEFAULT]
# Ban hosts for one hour:
bantime = -1
ignoreip = 127.0.0.1
# Override /etc/fail2ban/jail.d/00-firewalld.conf:
banaction = iptables-multiport
[ssh]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=ssh, port="ssh", protocol=tcp]
[ssh-ddos]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=ssh-ddos, port="ssh", protocol=tcp]
[nginx-http-auth]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=nginx, port="http,https,8089,8735", protocol=tcp]
[freeswitch]
enabled = true
logpath = /usr/local/freeswitch/log/freeswitch.log
maxretry = 5
bantime = 10000000
port = 5060
action   = %(banaction)s[name=%(__name__)s-tcp, port="%(port)s", protocol="tcp", chain="%(chain)s", actname=%(banaction)s-tcp]
           %(banaction)s[name=%(__name__)s-udp, port="%(port)s", protocol="udp", chain="%(chain)s", actname=%(banaction)s-udp]
#findtime = 10' >> /etc/fail2ban/jail.local			
			elif [ -f /etc/redhat-release ] ; then
				DIST="CENTOS"
				yum install fail2ban	
				echo '[DEFAULT]
# Ban hosts for one hour:
bantime = -1
ignoreip = 127.0.0.1
# Override /etc/fail2ban/jail.d/00-firewalld.conf:
banaction = iptables-multiport
[sshd]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=ssh, port="ssh", protocol=tcp]
[sshd-ddos]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=ssh-ddos, port="ssh", protocol=tcp]
[nginx-http-auth]
enabled = true
maxretry = 3
bantime = 10000000
action   = iptables-multiport[name=nginx, port="http,https,'${FSPORT}','${UIPORT}'", protocol=tcp]
[freeswitch]
enabled = true
logpath = /usr/local/freeswitch/log/freeswitch.log
maxretry = 5
bantime = 10000000
port = 5060
action   = %(banaction)s[name=%(__name__)s-tcp, port="%(port)s", protocol="tcp", chain="%(chain)s", actname=%(banaction)s-tcp]
           %(banaction)s[name=%(__name__)s-udp, port="%(port)s", protocol="udp", chain="%(chain)s", actname=%(banaction)s-udp]
#findtime = 10' >> /etc/fail2ban/jail.local					
			fi		
			################################# JAIL.CONF FILE READY ######################
			echo "################################################################"
			mkdir /var/run/fail2ban			
			chkconfig fail2ban on
			systemctl restart fail2ban
			systemctl enable fail2ban
			echo "################################################################"
			echo "Fail2Ban for FreeSwitch & IPtables Integration completed"
			else
			echo ""
			echo "Fail2ban installation is aborted !"
		fi   
}

astpp_install () 
{
		if [ ${ASTPP_USING_FREESWITCH} = "yes" ]; then
			install_freeswitch_for_astpp
			astpp_freeswitch_startup_script
			echo ""
			echo "FreeSWITCH is Installed"
		fi
		install_astpp
		mySQL_for_astpp
		finalize_astpp_installation		
		setup_cron
		startup_services	
		install_fail2ban		
		clear
		echo "******************************************************************************************"
		echo "******************************************************************************************"
		echo "******************************************************************************************"
		echo "**********                                                                      **********"
		echo "**********           Your ASTPP is installed successfully                       **********"
		echo "                     Browse URL: http://${ASTPP_HOST_DOMAIN_NAME}:8089"
		echo "                     Username: admin"     
		echo "                     Password: admin"                                       
		echo "**********           IMPORTANT NOTE: Please reboot your server once.            **********"
		echo "**********                                                                      **********"
		echo "******************************************************************************************"
		echo "******************************************************************************************"
		echo "******************************************************************************************"
}

# Install astpp
start_install_astpp () 
{
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
