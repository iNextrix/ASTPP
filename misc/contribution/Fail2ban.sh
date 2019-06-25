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

echo "
# Fail2Ban configuration file
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
#2014-12-01 00:47:54.331821 [WARNING] sofia_reg.c:2752 Can't find user [1000@xxx.xxx.xxx.xxx] from 62.210.151.162
failregex = \[WARNING\] sofia_reg.c:\d+ Can't find user \[.*@\d+.\d+.\d+.\d+\] from <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/freeswitch-ip.conf

echo "
# Fail2Ban configuration file
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
#[WARNING] sofia_reg.c:1792 SIP auth challenge (INVITE) on sofia profile 'internal' for [+972592277524@xxx.xxx.xxx.xxx] from ip 209.160.120.12 
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \((INVITE|REGISTER)\) on sofia profile \'\w+\' for \[.*@\d+.\d+.\d+.\d+\] from ip <HOST>


# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/auth-challenge-ip.conf

echo "
# Fail2Ban configuration file
#
[Definition]
failregex = <HOST> - - \[.*\] "(GET|POST).*HTTP[^ ]* 404
ignoreregex =" > /etc/fail2ban/filter.d/nginx-404.conf

echo "
# Fail2Ban configuration file
 
[Definition]
# Option: failregex
# Notes.: Regexp to catch a generic call from an IP address.
# Values: TEXT
#
failregex = ^<HOST> -.*"(GET|POST).*HTTP.*"$
 
# Option: ignoreregex
# Notes.: regex to ignore. If this regex matches, the line is ignored.
# Values: TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/nginx-dos.conf

echo "
# Fail2Ban configuration file
#
# Author: soapee01
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/sip-auth-challenge.conf

echo "
# Fail2Ban configuration file
#
# Author: soapee01
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth failure \(REGISTER\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =" > /etc/fail2ban/filter.d/sip-auth-failure.conf


################################# FREESWITCH.CONF FILE READY ##################

echo "-- Modifying /etc/fail2ban/jail.conf file"

################################# JAIL.CONF FILE WRITING ####################
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.bak

echo "
[ssh]
enabled  = true
port     = 22
protocol = ssh
filter   = sshd
logpath  = /var/log/auth.log
action   = iptables-allports[name=sshd, protocol=all]
maxretry = 3
findtime = 7200
bantime  = 1000000

[freeswitch]
enabled  = true
port     = 5060:5091
protocol = all
filter   = freeswitch
logpath  = /var/log/freeswitch/freeswitch.log
#logpath  = /usr/local/freeswitch/log/freeswitch.log
action   = iptables-allports[name=freeswitch, protocol=all]
maxretry = 3
findtime = 600
bantime  = 1000000
#          sendmail-whois[name=FreeSwitch, dest=root, sender=fail2ban@example.org] #no smtp server installed

[freeswitch-ip]
enabled  = true
port     = 5060:5091
protocol = all
filter   = freeswitch-ip
logpath  = /var/log/freeswitch/freeswitch.log
#logpath  = /usr/local/freeswitch/log/freeswitch.log
action   = iptables-allports[name=freeswitch-ip, protocol=all]
maxretry = 1
findtime = 30
bantime  = 1000000

[auth-challenge-ip]
enabled  = true
port     = 5060:5091
protocol = all
filter   = auth-challenge-ip
logpath  = /var/log/freeswitch/freeswitch.log
#logpath  = /usr/local/freeswitch/log/freeswitch.log
action   = iptables-allports[name=auth-challenge-ip, protocol=all]
maxretry = 1
findtime = 30
bantime  = 1000000

[sip-auth-challenge]
enabled  = true
port     = 5060:5091
protocol = all
filter   = sip-auth-challenge
logpath  = /var/log/freeswitch/freeswitch.log
#logpath  = /usr/local/freeswitch/log/freeswitch.log
action   = iptables-allports[name=sip-auth-challenge, protocol=all]
maxretry = 15
findtime = 180
bantime  = 1000000

[sip-auth-failure]
enabled  = true
port     = 5060:5091
protocol = all
filter   = sip-auth-failure
logpath  = /var/log/freeswitch/freeswitch.log
#logpath  = /usr/local/freeswitch/log/freeswitch.log
action   = iptables-allports[name=sip-auth-failure, protocol=all]
maxretry = 3
findtime = 180
bantime  = 1000000

[nginx-404]
enabled  = true
port     = 80,443
protocol = tcp
filter   = nginx-404
logpath  = /var/log/nginx/access*.log
action   = iptables-allports[name=nginx-404, protocol=all]
bantime  = 1000000
findtime = 60
maxretry = 120

[nginx-dos]
# Based on apache-badbots but a simple IP check (any IP requesting more than
# 300 pages in 60 seconds, or 5p/s average, is suspicious)
enabled  = true
port     = 80,443
protocol = tcp
filter   = nginx-dos
logpath  = /var/log/nginx/access*.log
action   = iptables-allports[name=nginx-dos, protocol=all]
findtime = 60
bantime  = 1000000
maxretry = 300
" >> /etc/fail2ban/jail.local

################################# JAIL.CONF FILE READY ######################
mkdir /var/run/fail2ban

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
