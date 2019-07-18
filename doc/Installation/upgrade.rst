===================
Upgrade
===================

**Upgrade Steps from 4.0 to 4.0.1**
::

 Pull new source from git repository
 cd /opt/ASTPP/
 git pull origin v4.0.1

 Update database changes
 mysql -u<DB_USER> -p astpp < database/astpp-4.0.1.sql

 Open config file /var/lib/astpp/astpp-config.conf and set below lines in that,

 PRIVATE_KEY = 8YSDaBtDHAB3EQkxPAyTz2I5DttzA9uR
 ENCRYPTION_KEY = r)fddEw232f

|

Looking for Cloud Hosting? : `Click here
<https://m.do.co/c/2000afbc6cda>`_

Freeswitch G729 License? : `Click here
<https://billing.freeswitch.com/aff.php?pid=3&aff=014>`_

