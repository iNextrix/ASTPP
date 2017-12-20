===================
Homer
===================

Follow below steps to integrate ASTPP with Homer.


**Open file**
::
    /var/www/html/fs/lib/astpp.xml.php

**Edit file**

::
Goto Line number : 113 (It should look like : $xml .= "   <configuration name=\"sofia.conf\" description=\"SIP Profile\">\n";)

Add below code just after above line : 

$xml .= " <global_settings>\n";
$xml .= " <param name=\"sip-capture\" value=\"yes\"/>\n";
$xml .= " <param name=\"capture-server\" value=\"udp:192.168.1.200:9060\"/>\n";
$xml .= " </global_settings>\n";

Change 192.168.1.20:9060 according to your configuration.

**Save the file and restart freeswitch.**
 


