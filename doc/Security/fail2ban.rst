=========
Fail2ban
=========

Fail2Ban is an intrusion prevention system that works by scanning log files and then taking action based on the entries 
in those logs.

You can configure Fail2Ban in a way that will update iptables firewall rules, when an authentication failure threshold 
is reached which helps in preventing SIP brute force attacks against FS instances.

Fail2Ban scans your freeswitch log file and bans IP that makes too many password failures. It updates firewall rules to 
reject the IP address.

Fail2Ban is available at fail2ban.org as well as more documentation.


**Installtion :**
::  For CentOS
cd /usr/src
service iptables stop
wget -T 10 -t 1 http://sourceforge.net/projects/fail2ban/files/fail2ban-stable/fail2ban-0.8.4/fail2ban-0.8.4.tar.bz2
tar -jxf fail2ban-0.8.4.tar.bz2
cd fail2ban-0.8.4


python setup.py install
cp /usr/src/fail2ban-0.8.4/files/redhat-initd /etc/init.d/fail2ban
chmod 755 /etc/init.d/fail2ban


For Debian
apt-get -y install fail2ban
    
**Configurations:**
::  touch /etc/fail2ban/filter.d/freeswitch.conf
cp /etc/fail2ban/filter.d/freeswitch.conf /etc/fail2ban/filter.d/freeswitch.bak


# vim /etc/fail2ban/filter.d/freeswitch.conf

[Definition]
# Option: failregex
# Notes.: regex to match the password failures messages in the logfile. The
# host must be matched by a group named host. The tag '<HOST>' can
# be used for standard IP/hostname matching and is only an alias for
# (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values: TEXT
#
failregex
= \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia 
profile \'[^']+\' for \[.*\] from ip <HOST>
\[WARNING\] sofia_reg.c:\d+ SIP auth failure \(INVITE\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>
# Option: ignoreregex
# Notes.: regex to ignore. If this regex matches, the line is ignored.
# Values: TEXT
#
ignoreregex =


# vim /etc/fail2ban/filter.d/freeswitch-dos.conf


[Definition]
# Option: failregex
# Notes.: regex to match the password failures messages in the logfile. The
# host must be matched by a group named host. The tag '<HOST>' can
# be used for standard IP/hostname matching and is only an alias for
# (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values: TEXT
#
failregex
= \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia 
profile \'[^']+\' for \[.*\] from ip <HOST>
# Option: ignoreregex
# Notes.: regex to ignore. If this regex matches, the line is ignored.
# Values: TEXT
#
ignoreregex =


cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.bak

# vim /etc/fail2ban/jail.local

[freeswitch]
enabled = true
port = 5060,5061,5080,5081
filter = freeswitch
logpath = /usr/local/freeswitch/log/freeswitch.log
maxretry = 10
bantime = 10000000
findtime = 480
action = iptables-allports[name=freeswitch, protocol=all]
sendmail-whois[name=FreeSwitch, dest=, sender=fail2ban@]


[freeswitch-dos]
enabled = true
port = 5060,5061,5080,5081
filter = freeswitch-dos
logpath = /usr/local/freeswitch/log/freeswitch.log
action = iptables-allports[name=freeswitch-dos, protocol=all]
maxretry = 50
findtime = 30
bantime = 6000


/etc/init.d/iptables start

/etc/init.d/fail2ban start

chkconfig fail2ban on
