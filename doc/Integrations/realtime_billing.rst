==================================
Realtime Billing (Experimental)
==================================

.. note:: Contributor of the feature: Rokib

	**We strongly recommend you to test the feature first in a LAB environment before applying it in production.**

.. note:: Feature includes : 

   - Code has been written for only outbound calls.  
   - No package is taken under calculation.
   - No free minutes are taken under calculation.
   - Max depth account is set to 10
   - Heartbeat set to 30 seconds per channel


Follow below steps to enable real-time billing in ASTPP.


**1. Enable nibble billing in modules.conf of FreeSwitch source**
::	vim /usr/local/src/freeswitch/modules.conf
Uncomment #applications/mod_nibblebill
Save and Close the file


**2. Replace nibblebill source code file**
::	cp -rf /usr/src/ASTPP/freeswitch/mod/mod_nibblebill.c <FreeSwitch Src>/freeswitch/src/mod/applications/mod_nibblebill/mod_nibblebill.c

**3. Copy & Change nibblebill.conf accordingly with your database user and password**
::  cp -rf  /usr/src/ASTPP/freeswitch/conf/autoload_configs/nibblebill.conf.xml /usr/local/freeswitch/conf/autoload_configs/nibblebill.conf.xml
vim /usr/local/freeswitch/conf/autoload_configs/nibblebill.conf.xml
Change here : <param name="odbc-dsn" value="dbname:user:password"/>

**4. Compile and install FreeSWITCH**
::	Go to FreeSwitch source folder 
./configure
make
make install   			

**5. Enable mod_nibblebill in FreeSwitch autoload**
::  vim /usr/local/freeswitch/conf/autoload_configs/modules.conf.xml
Uncomment Or Add <load module="mod_nibblebill"/>
Save and Close the file

**6. Restart FreeSwitch OR load mod_nibblebill manually from fs_cli console**
