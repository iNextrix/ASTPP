========================
Opensips Integration Old
========================


.. note:: OpenSIPs script is for version 1.7.1. 

          **Please help us in converting opensips script to latest opensips LTS version.**



+------------------------------------------------------------------+
|**1. Modify subscriber table of opensips:**                       |
+------------------------------------------------------------------+                       
|ALTER TABLE `subscriber` ADD `accountcode` VARCHAR( 20 ) NOT NULL |
+------------------------------------------------------------------+



+--------------------------------------------------------------------------------------------------------------------+
|**2. Enable opensips support in ASTPP:**                                                                            |
+--------------------------------------------------------------------------------------------------------------------+       
|1. Goto System -> Configuration                                                                                     |
|2. Enable opensips options                                                                                          |
|3. Configure opensips database and domain variables from same page (Variables : opensips_dbname, opensips_dbuser,   |
|   opensips_dbhost,opensips_dbpass, opensips_domain)                                                                |    
| **Note** : If you are running opensips on separate server then please make sure ASTPP server should have grant to  | 
|             access opensips database.                                                                              |
+--------------------------------------------------------------------------------------------------------------------+




+---------------------------------------------------------------------------------+
|**3. Copy opensip.cfg file to appropriate opensips folder:**                     |
+---------------------------------------------------------------------------------+                                           
|cp <ASTPP SOURCE DIR>/opensips/opensips.cfg /usr/local/etc/opensips/opensips.cfg |
+---------------------------------------------------------------------------------+

You are done.












