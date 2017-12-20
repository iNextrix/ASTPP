======================
Secure Freeswitch
======================


+-------------------------------------------------------------------------+
| **Change Event Socket credential**                                      |
+-------------------------------------------------------------------------+
| # vim /usr/local/freeswitch/conf/autoload_configs/event_socket.conf.xml |
|                                                                         |
| <param name="password" value="your_password”/>                          |
|                                                                         |
| Restart freeswitch service                                              |
| #service freeswitch restart                                             |
+-------------------------------------------------------------------------+

+-------------------------------------------------------------------------+
| **Set FreeSwitch Event Socket credential in UI**                        |
+-------------------------------------------------------------------------+
| 1. Login to ASTPP portal and Open Switch -> Freeswitch Server page.     |  
| 2. Edit configured FreeSwitch settings to new credential which          |
|    you just configured in event socket file.                            |                                               
+-------------------------------------------------------------------------+
