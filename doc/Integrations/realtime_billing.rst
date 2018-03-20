==================================
Realtime Billing (Experimental)
==================================

.. note:: Contributor of the feature: Rokib

.. note:: We strongly recommend you to test the feature first in a LAB environment before applying it in production. 

.. note:: Feature includes : 
   1. Code has been written for only outbound calls. 
   2. No package is taken under calculation.
   3. No free minutes are taken under calculation.
   4. Max depth account is set to 10
   5. Heartbeat set to 30 seconds per channel


Follow below steps to enable real-time billing in ASTPP.


**Enable nibble billing in modules.conf of FreeSwitch source**
::
vim /usr/local/src/freeswitch/modules.conf

Uncomment #applications/mod_nibblebill

Save and Close the file


**Replace nibblebill source code file**
::
cp -rf /usr/src/ASTPP/freeswitch/mod/mod_nibblebill.c <FreeSwitch Src>/freeswitch/src/mod/applications/mod_nibblebill/mod_nibblebill.c

**Copy & Change nibblebill.conf accordingly with your database user and password**
::
cp -rf  /usr/src/ASTPP/freeswitch/conf/autoload_configs/nibblebill.conf.xml /usr/local/freeswitch/conf/autoload_configs/nibblebill.conf.xml
vim /usr/local/freeswitch/conf/autoload_configs/nibblebill.conf.xml
Change here : <param name="odbc-dsn" value="dbname:user:password"/>

Note: Only change dbname, user, and password.

**Compile and install FreeSWITCH**
::
Go to FreeSwitch source. 
./configure
make
make install   			

**Enable mod_nibblebill in FreeSwitch autoload**
::
vim /usr/local/freeswitch/conf/autoload_configs/modules.conf.xml
Uncomment Or Add <load module="mod_nibblebill"/>
Save and Close the file


**Restart FreeSwitch OR load mod_nibblebill manually from fs_cli console**