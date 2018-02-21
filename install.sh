#!/bin/bash
#
# ASTPP - Open Source VoIP Billing
#
# Copyright (C) 2004/2013 www.astpp.org
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# Terms of the GNU General Public License version 2.
############################################################

#################################
####  variables #################
#################################
#################################
TEMP_USER_ANSWER="no"
INSTALL_ASTPP="no"
UPGRADE_ASTPP="no"
ASTPP_SOURCE_DIR="/usr/src/ASTPP/"
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

WWWDIR=/var/www

ASTPP_USING_FREESWITCH="no"
ASTPP_USING_ASTERISK="no"
INSTALL_ASTPP_PERL_PACKAGES="no"
INSTALL_ASTPP_WEB_INTERFACE="no"

ASTPP_DATABASE_NAME="astpp"

ASTPP_DB_USER="astppuser"

ASTPP_SERVER_IP="8.8.8.8"

MYSQL_ROOT_PASSWORD=""
ASTPPUSER_MYSQL_PASSWORD=""


#################################
#################################
####  general functions #########
#################################
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

#################################
#################################
####  ASK SCRIPTS ###############
#################################
#################################





# Ask to install astpp
ask_to_install_astpp () {
  
	ask_to_user_yes_or_no "Do you want to upgrade ASTPP?"
	echo -e "";
	if [ ${TEMP_USER_ANSWER} = "yes" ]; then
	    read -n 1 -p "Press any key to continue ... "
	    clear
	    
	    NOW=$(date +"%d%m%Y%H%M%S")
	    
	    echo -e "Backup Directory : /usr/src/ASTPP_$NOW"
	    
	    mkdir /usr/src/ASTPP_$NOW
	    mkdir /usr/src/ASTPP_$NOW/sql	    
	    mv /usr/src/ASTPP /usr/src/ASTPP_$NOW/
	    mv /var/www/html/astpp /usr/src/ASTPP_$NOW/astpp_gui
	    mv /var/www/cgi-bin/* /usr/src/ASTPP_$NOW/cgi-bin
	    mv /usr/local/astpp /usr/src/ASTPP_$NOW/usr_local_astpp
	    
	    read -p "Enter your MySQL root password: "
	    MYSQL_ROOT_PASSWORD=${REPLY}
	    read -n 1 -p "Press any key to continue ... "
	    clear
	    
	    /usr/bin/mysqldump -u root --password=${MYSQL_ROOT_PASSWORD} astpp > /usr/src/ASTPP_$NOW/sql/astpp.sql
	    
	    echo -e "Backup process completed!!!"
	    read -n 1 -p "Press any key to continue ... "
	    	    
	    UPGRADE_ASTPP="yes"
	    
            clear
            
	else 
	  ask_to_user_yes_or_no "Do you want to install ASTPP?"
	  if 	[ ${TEMP_USER_ANSWER} = "yes" ]; then
		  INSTALL_ASTPP="yes"
		  echo ""
		  read -p "Enter fqdn example: ${ASTPP_HOST_DOMAIN_NAME}: "
		  ASTPP_HOST_DOMAIN_NAME=${REPLY}
		  echo "Your entered data as fqdm : ${ASTPP_HOST_DOMAIN_NAME}"
		  read -n 1 -p "Press any key to continue ... "
		  
		  ask_to_user_yes_or_no "Do you want use FreeSwitch on ASTPP?"
		  if 	[ ${TEMP_USER_ANSWER} = "yes" ]; then
			  ASTPP_USING_FREESWITCH="yes"	
		  else 
			  ask_to_user_yes_or_no "Do you want use Asterisk on ASTPP?"
				  if 	[ ${TEMP_USER_ANSWER} = "yes" ]; then
				  ASTPP_USING_ASTERISK="yes"
				  fi
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
	fi
}
ask_to_install_astpp


#################################
#################################
####  INSTALL SCRIPTS ###########
#################################
#################################

clear
if [ ${UPGRADE_ASTPP} = "yes" ]; then
    echo -e "upgradation starting"
else
    echo -e "installation starting"
fi    
echo -e "are you ready?"
read -n 1 -p "Press any key to continue ... "
clear


# install freeswitch for astpp
install_freeswitch_for_astpp () {  
  
    if [ ${DIST} = "DEBIAN" ]; then
      
	aptitude update && aptitude safe-upgrade && aptitude clean && aptitude autoclean

	# Install Freeswitch pre-requisite packages using APTITUDE
	aptitude -y install autoconf automake devscripts gawk g++ git-core libjpeg62-dev libncurses5-dev libtool make python-dev gawk pkg-config libtiff4-dev libperl-dev libgdbm-dev libdb-dev gettext sudo lua5.1 chkconfig mysql-server apache2 apache2-threaded-dev php5 php5-dev php5-common php5-cli php5-gd php-pear php5-mysql php5-cli php-apc php5-curl libapache2-mod-php5 perl libapache2-mod-perl2 libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext libtool gcc g++ ntp ntpdate
	
    elif  [ ${DIST} = "CENTOS" ]; then
	install_epel

	yum install -y git
	
	yum clean all

	# Install Freeswitch pre-requisite packages using YUM
	yum install -y autoconf automake  expat-devel gnutls-devel libtiff-devel libX11-devel unixODBC-devel python-devel zlib-devel alsa-lib-devel libogg-devel libvorbis-devel perl perl-libs uuid-devel @development-tools gdbm-devel db4-devel libjpeg libjpeg-devel compat-libtermcap ncurses ncurses-devel ntp screen sendmail sendmail-cf gcc-c++ libtool cpan
	# i think i need to install also next packages
	yum install -y bison bzip2 curl curl-devel dmidecode git make mysql-connector-odbc openssl-devel unixODBC zlib
    fi  
    
    echo "Lets first make sure that time is correct before we continue ... "
    # set right time
    set_right_time () {
	echo "get the Time Right"
	ntpdate pool.ntp.org
	service ntpd start
	chkconfig ntpd on
    }
    set_right_time
    
    # Download latest freeswitch version
    cd /usr/local/src
    git clone https://stash.freeswitch.org/scm/fs/freeswitch.git
    cd freeswitch
    ./bootstrap.sh

    read -n 1 -p "Press any key to continue ... "

    # Edit modules.conf
    echo "Enable mod_xml_curl, mod_xml_cdr, mod_perl (If you want to use calling card features)"

    sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
    #sed -i "s#\#languages/mod_perl#languages/mod_perl#g" /usr/local/src/freeswitch-1.2.8/modules.conf
    sed -i "s#\#mod_xml_cdr#mod_xml_cdr#g" /usr/local/src/freeswitch/modules.conf

    read -n 1 -p "Press any key to continue ... "

    # Compile the Source
    ./configure
        
    # Install Freeswitch with sound files
    make all install cd-sounds-install cd-moh-install

    make && make install

    # Create symbolic links for Freeswitch executables
    ln -s ${FS_DIR}/bin/freeswitch /usr/local/bin/freeswitch
    ln -s ${FS_DIR}/bin/fs_cli /usr/local/bin/fs_cli
}

#SUB Configure astpp Freeswitch Startup Script
astpp_freeswitch_startup_script () {
    
    if [ ${DIST} = "DEBIAN" ]; then
	  adduser --disabled-password  --quiet --system --home ${FS_DIR} --gecos "FreeSWITCH Voice Platform" --ingroup daemon freeswitch
	  chown -R freeswitch:daemon ${FS_DIR}/
	  chmod -R o-rwx ${FS_DIR}/

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
if [ -n "$MYSQL_ROOT_PASSWORD" ]; then
    echo "Using your entered root password!!!"
    
    read -p "Enter your MySQL astppuser password: "
    ASTPPUSER_MYSQL_PASSWORD=${REPLY}
    read -n 1 -p "Press any key to continue ... "    
    clear	
else
    MYSQL_ROOT_PASSWORD=$(genpasswd)
    ASTPPUSER_MYSQL_PASSWORD=$(genpasswd)
    mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('${MYSQL_ROOT_PASSWORD}') WHERE user='root'; FLUSH PRIVILEGES;"
    
    # Save MySQL root password to a text file in /root
    echo ""
    echo "MySQL password set to '${MYSQL_ROOT_PASSWORD}'. Remember to delete ~/.mysql_passwd" | tee ~/.mysql_passwd
    echo "" >>  ~/.mysql_passwd
    echo "MySQL astppuser password:  ${ASTPPUSER_MYSQL_PASSWORD} " >>  ~/.mysql_passwd
    chmod 400 ~/.mysql_passwd
    read -n 1 -p "*** Press any key to continue ..."

fi

# Create astpp database
mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "create database ${ASTPP_DATABASE_NAME};"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '${ASTPPUSER_MYSQL_PASSWORD}';"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON \`${ASTPP_DATABASE_NAME}\` . * TO 'astppuser'@'localhost' WITH GRANT OPTION;FLUSH PRIVILEGES;"

mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/sql/astpp-1.7.3.sql
mysql -uroot -p${MYSQL_ROOT_PASSWORD} astpp < ${ASTPP_SOURCE_DIR}/sql/astpp-upgrade-1.7.3.sql
}


install_astpp () {
	# Download ASTPP
	cd /usr/src/	
	git clone https://github.com/ASTPP/ASTPP.git

	if [ ${DIST} = "DEBIAN" ]; then
	      # Install ASTPP pre-requisite packages using APTITUDE
	      aptitude install -y mysql-server apache2 apache2-threaded-dev php5 php5-dev php5-common php5-cli php5-gd php-pear php5-mysql php5-cli php-apc php5-curl libapache2-mod-php5 perl libapache2-mod-perl2 libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext libtool gcc g++
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
		perl -MCPAN -e "install Bundle::CPAN,ExtUtils::CBuilder,DBI,DBD::mysql,YAML,Params::Validate,CGI,URI::Escape,Time::DaysInMonth,DateTime,DateTime::TimeZone,DateTime::Locale,XML::Simple,Data::Dumper,Module::Build,Storable,Time::Zone,Date::Parse,Curses,POE,Sys::Syslog,FCGI,DateTime::Set,DateTime::Event::Recurrence,DateTime::Incomplete,Date::Language,DateTime::Format::Strptime,DBI::Shell,JSON,CGI::Fast,Locale::gettext_pp,Text::Template,Mail::Sendmail,XML::Simple";
		cd ${ASTPP_SOURCE_DIR}/modules/ASTPP && perl Makefile.PL && make && make install && cd ../../
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
		    WWWDIR=/usr/lib
		    chown -Rf www-data.www-data ${WWWDIR}/cgi-bin/
		elif [ ${DIST} = "CENTOS" ]; then
		    chown -Rf apache.apache ${ASTPPDIR}
		    chown -Rf apache.apache ${ASTPPLOGDIR}
		    chown -Rf apache.apache ${ASTPPEXECDIR}
		    WWWDIR=/var/www
		    chown -Rf apache.apache ${WWWDIR}/cgi-bin/
		fi
		
		cp -rf ${ASTPP_SOURCE_DIR}/scripts/*.pl ${ASTPPEXECDIR}/
		
		#Copy cgi scripts to cgi-bin
		cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/astpp ${WWWDIR}/cgi-bin/
	        chmod -Rf 777 ${WWWDIR}/cgi-bin/astpp
		
		#copy calling card script to freeswitch script folder
		cp ${ASTPP_SOURCE_DIR}/freeswitch/astpp-callingcards.pl ${FS_SCRIPTS}/astpp-callingcards.pl
				
		cp -rf ${ASTPP_SOURCE_DIR}/sounds/*.wav ${FS_SOUNDSDIR}/
		chmod -Rf 777 ${FS_SOUNDSDIR}
	fi

	if [ ${INSTALL_ASTPP_WEB_INTERFACE} = "yes" ]; then
		
		#Copy configuration file
		cp ${ASTPP_SOURCE_DIR}/astpp_confs/sample.astpp-config.conf ${ASTPPDIR}/astpp-config.conf
		cp ${ASTPP_SOURCE_DIR}/astpp_confs/sample.reseller-config.conf ${ASTPPDIR}/sample.reseller-config.conf
		
		#Install GUI of ATSPP
		mkdir -p ${WWWDIR}/html/astpp
		
		cp -rf ${ASTPP_SOURCE_DIR}/web_interface/astpp/* ${WWWDIR}/html/astpp/
		cp ${ASTPP_SOURCE_DIR}/web_interface/astpp/htaccess ${WWWDIR}/html/astpp/.htaccess
		
		if [ ${DIST} = "DEBIAN" ]; then
			chown -Rf www-data.www-data ${WWWDIR}/html/astpp
			cp ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf /etc/apache2/conf.d/astpp.conf
		elif  [ ${DIST} = "CENTOS" ]; then
			chown -Rf apache.apache ${WWWDIR}/html/astpp
			cp ${ASTPP_SOURCE_DIR}/web_interface/apache/astpp.conf /etc/httpd/conf.d/astpp.conf
		fi
		chmod -Rf 777 ${WWWDIR}/html/astpp
	fi	
}



upgrade_astpp () {
  
	# Download ASTPP
	cd /usr/src/	
	git clone https://github.com/ASTPP/ASTPP.git

	cd ${ASTPP_SOURCE_DIR}/modules/ASTPP && perl Makefile.PL && make && make install && cd ../../	
	
	mkdir -p ${ASTPPDIR}		
	mkdir -p ${ASTPPLOGDIR}		
	mkdir -p ${ASTPPEXECDIR}
	mkdir -p ${WWWDIR}/html/astpp
	
	#setup scripts and other configuration files
	if [ ${DIST} = "DEBIAN" ]; then
	    chown -Rf www-data.www-data ${ASTPPDIR}
	    chown -Rf www-data.www-data ${ASTPPLOGDIR}
	    chown -Rf www-data.www-data ${ASTPPEXECDIR}
	    chown -Rf www-data.www-data ${WWWDIR}/cgi-bin/
	elif [ ${DIST} = "CENTOS" ]; then
	    chown -Rf apache.apache ${ASTPPDIR}
	    chown -Rf apache.apache ${ASTPPLOGDIR}
	    chown -Rf apache.apache ${ASTPPEXECDIR}
	    chown -Rf apache.apache ${WWWDIR}/cgi-bin/
	fi
	
	cp -rf ${ASTPP_SOURCE_DIR}/scripts/*.pl ${ASTPPEXECDIR}/
	
	#Copy cgi scripts to cgi-bin
	cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/astpp ${WWWDIR}/cgi-bin/
	chmod -Rf 777 ${WWWDIR}/cgi-bin/astpp
	
	#copy calling card script to freeswitch script folder
	cp ${ASTPP_SOURCE_DIR}/freeswitch/astpp-callingcards.pl ${FS_SCRIPTS}/astpp-callingcards.pl
			
	cp -rf ${ASTPP_SOURCE_DIR}/sounds/*.wav ${FS_SOUNDSDIR}/
	chmod -Rf 777 ${FS_SOUNDSDIR}
	
	
	#Install GUI of ATSPP
	cp -rf ${ASTPP_SOURCE_DIR}/web_interface/astpp/* ${WWWDIR}/html/astpp/
	cp ${ASTPP_SOURCE_DIR}/web_interface/astpp/htaccess ${WWWDIR}/html/astpp/.htaccess
	
	chmod -Rf 777 ${WWWDIR}/html/astpp
	
	
	dbname=$(cat /var/lib/astpp/astpp-config.conf | grep dbname | cut -d " " -f 3)
	dbuser=$(cat /var/lib/astpp/astpp-config.conf | grep dbuser | cut -d " " -f 3)
	dbpass=$(cat /var/lib/astpp/astpp-config.conf | grep dbpass | cut -d " " -f 3)

	mysql -u root -f --password=${MYSQL_ROOT_PASSWORD} astpp < $ASTPP_SOURCE_DIR/sql/astpp-upgrade-1.7.3.sql
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
    
    /bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/conf/autoload_configs/* ${FS_DIR}/conf/autoload_configs/
    #### Edit xml_curl.conf.xml file and change localhost to your ip or domain name.
    #### Edit xml_cdr.conf.xml file and change localhost to your ip or domain name.
    sed -i "s#localhost#${ASTPP_HOST_DOMAIN_NAME}#g" ${FS_DIR}/conf/autoload_configs/xml_curl.conf.xml
    sed -i "s#localhost#${ASTPP_HOST_DOMAIN_NAME}#g" ${FS_DIR}/conf/autoload_configs/xml_cdr.conf.xml


    /bin/cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/conf/dialplan/default/astpp_callingcards.xml ${FS_DIR}/conf/dialplan/default/
    #### Edit astpp_callingcards.xml file to change acccess number for calling card.
    # TODO IF NEEDED

    # Enable mod_xml_curl, mod_xml_cdr, mod_cdr_csv, mod_perl in /usr/local/freeswitch/conf/autoload_configs/modules.conf.xml
    # <!-- <load module="mod_xml_curl"/> -->
    # <!-- <load module="mod_xml_cdr"/> -->
    sed -i "s#<!-- <load module=\"mod_xml_curl\"/> -->#load module=\"mod_xml_curl\"/>#g" ${FS_DIR}/conf/autoload_configs/modules.conf.xml
    sed -i "s#<!-- <load module=\"mod_xml_cdr\"/> -->#<load module=\"mod_xml_cdr\"/>#g" ${FS_DIR}/conf/autoload_configs/modules.conf.xml


    # edit ASTPP Database Connection Information
    # /var/lib/astpp/astpp-config.conf
    sed -i "s#dbpass = <PASSSWORD>#dbpass = ${MYSQL_ROOT_PASSWORD}#g" ${ASTPPDIR}/astpp-config.conf
    sed -i "s#base_url=http://localhost:8081/#base_url=http://${ASTPP_HOST_DOMAIN_NAME}:8081/#g" ${ASTPPDIR}/astpp-config.conf
}

setup_cron()
{

if [ ${DIST} = "DEBIAN" ]; then
CRONPATH='/var/spool/cron/crontabs/astpp'
elif [ ${DIST} = "CENTOS" ]; then
CRONPATH='/var/spool/cron/astpp'
fi

echo "# Generate Invoice   
0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice
          
# Low balance notification
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance
          
# Update currency rate
@hourly /usr/local/astpp/astpp-currency-update.pl
" > $CRONPATH

chmod 600 $CRONPATH
}

astpp_install () {

	if [ ${ASTPP_USING_FREESWITCH} = "yes" ]; then
		install_freeswitch_for_astpp
                astpp_freeswitch_startup_script
	fi
	
	install_astpp
	mySQL_for_astpp
	finalize_astpp_installation
	setup_cron
	startup_services
	
	clear
	echo " you can login on "
	echo "http://${ASTPP_HOST_DOMAIN_NAME}:8081 "
	echo "Username= Leave empty "
	echo "Password= Passw0rd! "
}

astpp_upgrade () {

	upgrade_astpp	
	setup_cron
	startup_services	
	clear
	echo " ASTPP upgrade completed!!! "
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

if [ ${UPGRADE_ASTPP} = "yes" ]; then
	astpp_upgrade
fi
