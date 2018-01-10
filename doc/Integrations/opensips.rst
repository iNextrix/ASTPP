===============================
OpenSIPs
===============================


Opensips Install guide for CentOS 6.0


**1. Install Dependencies**
::

 yum install -y gcc gcc-c++ bison flex zlib-devel openssl-devel mysql-devel subversion pcre-devel ncurses-devel ncurses
 

**2. Download opensips sources for version 1.7.1**
::
  
 cd /usr/src 
 wget http://opensips.org/pub/opensips/1.7.1/src/opensips-1.7.1_src.tar.gz
 
 
**3. Unpack sources, compile and install opensips with mysql support**
::
  
 tar -zxvf opensips-1.7.1_src.tar.gz 
 cd /usr/src/opensips-1.7.1-tls 
 
 make include_modules="db_mysql" prefix="/" all 
 make include_modules="db_mysql" prefix="/" install
 
 
**4. Add opensips user**
::

 adduser opensips
 
 
**5  Copy init file add correct path and permissions**
::

 cp /usr/src/opensips-1.7.1-tls/packaging/fedora/opensips.init /etc/init.d/opensips
 chmod a+x /etc/init.d/opensips
 
 
**5.1 Locate opensips executable and edit init file with correct path**
::

  $which opensips 
  /sbin/opensips 
  nano /etc/init.d/opensips
     
 
**5.2  Find line 24 and change:**
::

  oser=/usr/local/sbin/$prog for: oser=/sbin/$prog
 
 
**6. Verify opensips default install.**
::

 $ service opensips start
 
 $ netstat -pln | grep opensips
 
 $ netstat -pln | grep opensips 
 
 tcp        0      0 localhost:5060         0.0.0.0:*                   LISTEN      26302/opensips
 tcp        0      0 localhost:7000         0.0.0.0:*                   LISTEN      26302/opensips
 udp        0      0 localhost:5060         0.0.0.0:*                               26302/opensips
 udp        0      0 localhost:7000         0.0.0.0:*                               26302/opensips
 
 At this point you should not have any problem to start the process, in case you have it, 
 Please verify the steps and the logs file to correct possible errors.
 
 tailf /var/log/messages | grep opensips
 
 Oct 26 19:16:56 astpp opensips[26302]: INFO:registrar:mod_init: initializing...
 Oct 26 19:16:56 astpp opensips[26302]: INFO:textops:hname_fixup: using hdr type name <X-AUTH-IP>
 Oct 26 19:16:56 astpp opensips[26302]: INFO:textops:hname_fixup: using hdr type name <P-Accountcode>
 Oct 26 19:16:56 astpp opensips[26302]: INFO:textops:hname_fixup: using hdr type name <P-Pricelist_id>
 Oct 26 19:16:56 astpp opensips[26302]: INFO:textops:hname_fixup: using hdr type name <X-Redirect-Server>
 Oct 26 19:16:56 astpp opensips[26302]: INFO:core:probe_max_sock_buff: using rcv buffer of 244 kb
 Oct 26 19:16:56 astpp opensips[26302]: INFO:core:probe_max_sock_buff: using rcv buffer of 244 kb
 Oct 26 19:16:56 astpp opensips[26302]: INFO:core:probe_max_sock_buff: using snd buffer of 244 kb
 Oct 26 19:16:56 astpp opensips[26302]: INFO:core:probe_max_sock_buff: using snd buffer of 244 kb
 Oct 26 19:16:56 astpp opensips: INFO:core:daemonize: pre-daemon process exiting with 0


**7. Setup MySql db and user**
::

 CREATE DATABASE `opensips`;
 CREATE USER 'opensips'@'%' IDENTIFIED BY '<password>';
 GRANT ALL PRIVILEGES ON `opensips`.* TO 'opensips'@'%';
 FLUSH PRIVILEGES;
 DROP DATABASE `opensips`;

 
**7.1  Edit opensipsctlrc file for database information:**
::

 $ nano /etc/opensips/opensipsctlrc

 uncomment and edit following lines:
 
 DBENGINE = MYSQL
 DBHOST = localhost
 DBNAME = opensips
 DBRWUSER = opensips
 DBRWPW = ""
 DBROOTUSER = "root"
 USERCOL = "username"
 ETCDIR = etc/
 
 
**7.2  Run $ opensipsdbctl create and follow the step to create opensips db**
::

 MySQL password for root:
 
 INFO: test server charset
 INFO: creating database opensips ...
 INFO: Core OpenSIPS tables succesfully created.
 Install presence related tables? (y/n): y
 INFO: creating presence tables into opensips ...
 INFO: Presence tables succesfully created.
 Install tables for imc cpl siptrace domainpolicy carrierroute userblacklist? (y/n): y
 INFO: creating extra tables into opensips ...
 INFO: Extra tables succesfully created.


 
**7.3  Verify db tables**
::

 $ mysql -u opensips -pPassword
 
 Welcome to the MySQL monitor.  Commands end with ; or \g.
 Your MySQL connection id is 35
 Server version: 5.1.73 Source distribution
 Copyright (c) 2000, 2013, Oracle and/or its affiliates. All rights reserved.
 Oracle is a registered trademark of Oracle Corporation and/or its
 affiliates. Other names may be trademarks of their respective
 owners.
 
 Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
 
 $ mysql> use opensips;
 
 Reading table information for completion of table and column names
 You can turn off this feature to get a quicker startup with -A
 
 Database changed
 
 $ mysql> show tables;
       +---------------------+
       | Tables_in_opensips  |
       +---------------------+
       | acc                 |    
       |                     |
       | active_watchers     |
       | address             |
       | aliases             |
       | carrierfailureroute |
       | carrierroute        |
       | cpl                 |
       | dbaliases           |
       | dialog              |
       | dialplan            |
       | dispatcher          |
       | domain              |
       | domainpolicy        |
       | dr_gateways         |
       | dr_groups           |
       | dr_gw_lists         |
       | dr_rules            |
       | globalblacklist     |
       | grp                 |
       | imc_members         |
       | imc_rooms           |
       | load_balancer       |
       | location            |
       | missed_calls        |
       | nh_sockets          |
       | pdt                 |
       | presentity          |
       | pua                 |
       | re_grp              |
       | rls_presentity      |
       | rls_watchers        |
       | route_tree          |
       | silo                |
       | sip_trace           |
       | speed_dial          |
       | subscriber          |
       | uri                 |
       | userblacklist       |
       | usr_preferences     |
       | version             |
       | watchers            |
       | xcap                |
       +---------------------+
      42 rows in set (0.00 sec)
 
 
**7.4  Modify subscriber table of opensips:**
::

 ALTER TABLE `subscriber` ADD `accountcode` VARCHAR( 20 ) NOT NULL;
 
 
**8. Enable opensips support in ASTPP**
::

 1. Goto System -> Configuration on the left panel select Opensips
 2. Enable opensips options
 3. Configure opensips database and domain variables from same page (Variables : opensips_dbname, opensips_dbuser, opensips_dbhost,opensips_dbpass, opensips_domain)

 Note : If you are running opensips on separate server then please make sure ASTPP server should have grant to access opensips database.


**9. Copy opensip.cfg file from the ASTPP source to opensips server folder.**
::

 for this step we got many options to move or copy the file from ASTPP source to the Opensips server, i choose to copy it with external file manager WinSCP

 
**9.1  Locate and open the file**
::

 locate and open the file with any remote file manager tool like: WinSCP or FileZilla, then copy the content of the file.
 
 
**9.2  In opensips server:**
::

 $ cp /etc/opensips/opensips.cfg /etc/opensips/opensips.cfg.bk
 $ rm -rf /etc/opensips/opensips.cfg
 $ nano /etc/opensips/opensips.cfg
 
 and we paste the content of the ASTPP source opensips.cfg there and modify the lines that have the comment: "# CUSTOMIZE ME" at the end of the line.
 
 when done just: ctrl+x then press "y" and enter to save
 

**9.3  We restart system process:**
::

 $ service opensips restart

 Stopping opensips:                                         [  OK  ]
 Starting opensips:                                         [  OK  ]

 and make the verification on step 6 again.
 
 
**9.4  Final steps to enable from astpp**
::

 1. Login into admin portal of ASTPP
 2. Go to Configuration and click on Settings
 3. Then click on Opensips 
 4. Now configure your details and click on Save it.
 Now you done everything :)

 
 
 
 
 
 
 
