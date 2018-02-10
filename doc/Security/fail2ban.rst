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
::

    For Cent OS
    yum install fail2ban
    
    For Debian
    apt-get -y install fail2ban
    
**Configurations:**
::
    
    For Cent OS
    Replace content of jail.local file with below content.
    
    --------- /etc/fail2ban/jail.local ------------------
    [DEFAULT]
    # Ban hosts for one hour:
    bantime = -1
    ignoreip = 127.0.0.1
    banaction = iptables-multiport
    [sshd]
    enabled = true
    maxretry = 3
    bantime = 10000000
    [sshd-ddos]
    enabled = true
    maxretry = 3
    bantime = 10000000
    [freeswitch]
    enabled = true
    logpath = /usr/local/freeswitch/log/freeswitch.log
    maxretry = 5
    bantime = 10000000
    -----------------------END OF File------------------
    
    For Debian 8 OS
    Replace content of jail.local file with below content.
    
    --------- /etc/fail2ban/jail.local ------------------
    [DEFAULT]
    # Ban hosts for one hour:
    bantime = -1
    ignoreip = 127.0.0.1
    banaction = iptables-multiport
    [ssh]
    enabled = true
    maxretry = 3
    bantime = 10000000
    [ssh-ddos]
    enabled = true
    maxretry = 3
    bantime = 10000000
    [freeswitch]
    enabled = true
    logpath = /usr/local/freeswitch/log/freeswitch.log
    maxretry = 5
    bantime = 10000000
    -----------------------END OF File------------------
    
    mkdir /var/run/fail2ban
    chkconfig iptables on  [If installed otherwise optional]
    chkconfig fail2ban on
    systemctl start fail2ban
    
    Note:
    You can whitelist your own ip address by adding it in ignoreip with space seperation. Also you can set maxretry and logpath as per your need.
