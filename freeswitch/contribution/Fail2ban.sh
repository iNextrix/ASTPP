#!/bin/bash

####################################################
# Script written by Arysh 29-Jul-2013
# arysh83@gmail.com
####################################################

echo ""
echo "################################################################"
echo "What is your personal email address for notification ?"
read -e EMAIL
echo "################################################################"

echo "Downloading sources"
        cd /usr/src
        service iptables stop
        wget -T 10 -t 1 http://sourceforge.net/projects/fail2ban/files/fail2ban-stable/fail2ban-0.8.4/fail2ban-0.8.4.tar.bz2/download
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
echo "Auto Configuration in progress"
echo "-- Writing /etc/fail2ban/filter.d/freeswitch.conf file"
	touch  /etc/fail2ban/filter.d/freeswitch.conf
	cp  /etc/fail2ban/filter.d/freeswitch.conf  /etc/fail2ban/filter.d/freeswitch.bak

################################# FREESWITCH.CONF FILE WRITING #################

echo "
# Fail2Ban configuration file

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag '<HOST>' can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>
            \[WARNING\] sofia_reg.c:\d+ SIP auth failure \(INVITE\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/freeswitch.conf


echo "
# Fail2Ban configuration file

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
enabled  = true
port     = 5060,5061,5080,5081
filter   = freeswitch
logpath  = /usr/local/freeswitch/log/freeswitch.log
maxretry = 10
bantime = 10000000
findtime = 480
action   = iptables-allports[name=freeswitch, protocol=all]
           sendmail-whois[name=FreeSwitch, dest=$EMAIL, sender=fail2ban@example.org]
" >> /etc/fail2ban/jail.conf


echo "
[freeswitch-dos]
enabled = true
port = 5060,5061,5080,5081
filter = freeswitch-dos
logpath = /usr/local/freeswitch/log/freeswitch.log
action = iptables-allports[name=freeswitch-dos, protocol=all]
maxretry = 50
findtime = 30
bantime  = 6000
" >> /etc/fail2ban/jail.local


################################# JAIL.CONF FILE READY ######################

echo "################################################################"
echo "Auto Configuration Completed"

echo "Restarting IPtables"
/etc/init.d/iptables start

echo "Starting Fail2Ban Integration"
/etc/init.d/fail2ban start

echo "Restarting IPtables"
/etc/init.d/iptables restart

echo "Starting Fail2Ban Integration"
/etc/init.d/fail2ban restart

echo "################################################################"
echo "Configuring IPtables & Fail2Ban as service"
chkconfig iptables on
chkconfig fail2ban on

echo "################################################################"
echo "Fail2Ban for FreeSwitch & IPtables Integration completed"
