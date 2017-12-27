==========
Security
==========


Fail2Ban is an intrusion prevention system that works by scanning log files and then taking action based on the entries in those logs.

You can configure Fail2Ban in a way that will update iptables firewall rules, when an authentication failure threshold is reached which helps in preventing SIP brute force attacks against FS instances.

Fail2Ban scans your freeswitch log file and bans IP that makes too many password failures. It updates firewall rules to reject the IP address.

Fail2Ban is available at fail2ban.org as well as more documentation.

**Related pages**

.. toctree::
   :maxdepth: 2

   apache_authentication.rst
   secure_freeswitch.rst
   secure_portal.rst
   fail2ban.rst


