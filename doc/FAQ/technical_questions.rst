====================
Technical Questions
====================

  1. What databases are supported in ASTPP ?
      It supports only MySQL (PostgreSQL support will be added in future release).
   
   
  2. What payment gateways are supported in ASTPP ?
      Paypal only for now.


  3. Does ASTPP support Multi-language ?
      No


  4. Does ASTPP current version support callshop?
      No. It will be included in future releases. To get more information you can contact us at sales@inextrix.com.


  5. How can I update my source from to keep it update to date?
      ASTPP provides update.sh script with its source just run that script to update your source.


  6. When I go to login page, rather WEBUI I get long list of php contents.
      Enable short_open_tag in php.ini and then restart apache.


  7. Why I am not able to register extensions after installation?
      Make sure you have configured your IP in Sip Profile and your registration request is reaching to server.


  8. How to verify if ASTPP and FreeSWITCH communicating properly?
      Default sip profile must be loaded in FreeSWITCH


  9. While login I am getting “Unable to connect to your database server using the provided settings” error.
      Please check your database credentials. ASTPP uses /var/lib/astpp/astpp-config.conf file for database connection.


  10. How can I do IP Authentication for my customers?
       Configure your customer IP under Customers -> IP Settings


  11. Does CDRs report will show all data ?
       No, CDRs report will only record of current day. If you want to see record of previous days record then you need 
       to search.
       
       
  12. How to connect freeswitch fs_cli console from linux server ?
       #fs_cli or fs_cli -p123456 (if you have set even_socket password)


  13. How to change event socket password in freeswitch ?
       Open below file and replace your password with “ClueCon”
       
       File path : /usr/local/freeswitch/conf/autoload_configs/event_socket.conf.xml
       
       Go to fs_cli console and reload it. freeswitch> reload mod_event_socket
       

  14. If i’m getting origination rates not found error in fs_cli console then what to do ?[WARNING] mod_dptools.c:1724   Accountcode 2457848300. Dialed number (3318555801802)  origination rates not found!!
       Add origination rate for prefix 33 and select rategroup which assigned to customer.
       

  15. If i’m getting origination rates not found error in fs_cli console then what to do ? [WARNING] mod_dptools.c:1724 Accountcode 2457848300. Dialed number (3318555801802) termination rates not found!!
       Add termination rate for prefix 33 and select trunk which selected in rate group
       Or select approprie trunk in rategroup which assigned to customer.
       
       
  16. How i can disable astpp.log ?
       Go to ASTPP Web-portal ---> Configuration ---> Setting ---> Global ---> Call Debug = Disable
     
     
  17. How i can increase CC/CPS in freeswitch ?
        Navigated to /usr/local/freeswitch/conf/autoload_configs/switch.conf.xml to increase Max handles.
        You can increase below variable as per your need and need to restart freeswitch for applied changes.
        
        <!-- Maximum number of simultaneous DB handles open -->
             <param name="max-db-handles" value="200"/>
        <!-- Maximum number of seconds to wait for a new DB handle before failing -->
             <param name="db-handle-timeout" value="10"/>
        <!-- Max number of sessions to allow at any given time -->
            <param name="max-sessions" value="10000"/>
        <!--Most channels to create per second -->
            <param name="sessions-per-second" value="100"/>

        Recommended ULIMIT settings
        The following are recommended ulimit settings for FreeSWITCH when you want maximum performance. Ulimit settings you can             add to initd script before do_start().

        1. ulimit -c unlimited # The maximum size of core files created.
        2. ulimit -d unlimited # The maximum size of a process's data segment.
        3. ulimit -f unlimited # The maximum size of files created by the shell (default option)
        4. ulimit -i unlimited # The maximum number of pending signals
        5. ulimit -n 999999    # The maximum number of open file descriptors.
        6. ulimit -q unlimited # The maximum POSIX message queue size
        7. ulimit -u unlimited # The maximum number of processes available to a single user.
        8. ulimit -v unlimited # The maximum amount of virtual memory available to the process.
        9. ulimit -x unlimited # ???
        10. ulimit -s 240         # The maximum stack size
        11. ulimit -l unlimited # The maximum size that may be locked into memory.
        12. ulimit -a           # All current limits are reported.

18. What is difference between Origination rates and Termination rates ?
      Origination rates : Customer rates or sell rates as these rates will be applicable on customers.
      
      Termination rates : Carrier/Provider rates or buy rates as these rates are charged from providers.

19. What is difference between Customer and Provider ?
      We consider customers as (Originator or client ) and providers as (Terminator + Originator).
      
20. How to set callerid in ASTPP ?
     1.If you want to configure callerid for customer then you can set force caller id.
     
     Navigate on ASTPP >> Accounts >> Customer >> List of customer      
     
     For every customer, you will get four action button.
     The second button is for Force caller id to set caller id

     2.If you want to set callerid for specific user of customer then you can set on sip device configuration.
     
     Navigate on ASTPP >> Switch >> SIP Devices >> Click on sip device edit >> Set Caller Number

21. How to take database backup?
     Navigate on ASTPP >> Configuration >> Database Restore >> Create backup
     
22. How to do number translation?
     You can do number translation in two places in ASTPP.
     
     1. Customer configuration
     
     If you wish to translate number with some defined number for a specific customer then use this feature.

     2. Trunk configuration 
     
     If you wish to translate number with some defined number for trunk then use this feature.

     Ex: “011/2222” (You can define multiple translations like “011/2222”,”02/33”)
     That means from called/dialed number 011 is replaced by 2222.

     Refer : https://www.youtube.com/watch?v=KjO2sIqvCBY

23. How to do callerid translation?
     You can do callerid number translation in two places in ASTPP.
     
     1. Customer configuration
     OUT Callerid Translation: This will apply to outbound call
     
     IN Callerid Translation: This will apply to inbound/DID call

     2. Trunk 
     Callerid Translation: This will apply to outbound call

     Ex: “011/2222” (You can define multiple translations like “011/2222”,”02/33”)
     That means from callerID number 011 is replaced by 2222.
     
24. How to create sip device?
     Navigate on ASTPP >> Switch  >> Sip Devices >> Create sip device
     
25. How to setup calling card access number?
     You can define CC(Calling card) access number as below.
     
     Navigate on ASTPP >> Configuration >> Settings >> Calling Cards >> CC Access Numbers
     
26. How to check registered device list?
     If you have configured FM addon. Refer : http://www.astppbilling.org/addons/freeswitch-monitoring-addon/ 
     
     Navigate on ASTPP >> Addons >> FS Monitor >> SIP Devices
     
     Or
     
     SSH on astpp server and connect fs_cli
     
     freeswitch>show registrations
     
     You will get the list of registered device list.
     
27. What is default sip port for registration?
     By default, sip port is 5060 for registration.
     
28. How to integrate FusionPBX with ASTPP?
     You can configure your ASTPP as trunk in FusionPBX outbound route.
     So your FusioPBX routes all calls to ASTPP and then to provider.

29. How to refill/recharge on customer account?
     Navigate on ASTPP >> Accounts >> Customer >> List of customer
     
     For every customer, you will get four action button.
     First button $ is for refill/recharge.

30. How to enable video call for sip accounts?
     Navigate on ASTPP >> Switch >> SIP Profiles >> Default
     
     You will get two params for codec.

     inbound-codec-prefs and outbound-codec-prefs
     
     You can add video codec H263,H264,H261 and rescan your profile.
     Also, you need load required module in FreeSWITCH.
     
31. How to configure SMTP server on ASTPP?
     Navigate on ASTPP >> Configuration >> Settings >> Email 

     In that page, you can configure your SMTP details

     Example if you want to configure your gmail account as SMTP:
     Email Notifications : Enable
     1.SMTP : Enable
     2.SMTP Host : ssl://smtp.gmail.com
     3.SMTP Port : 465
     4.SMTP User : yourgmailusername@gmail.com
     5.SMTP Pass : yourgmailpassword
     
32. Why calls disconnect after 1440 seconds or 24 minutes?
     Becuase by default 24 minutes configured on global setting you can change it as per your need.

     Navigate on ASTPP >> Configuration >> Settings >> Global >> Call Max Length(ms)
     
33. How to import origination rates sheet on ASTPP?
     Navigate on ASTPP >> Tariff >> Origination Rates >> Import >> Upload your CSV file

     You can download also sample file from there or
     File must be in the following format(.csv): Code,Destination,Connect Cost,Included Seconds,Per Minute Cost,Initial Increment,Increment.

34. How to import termination rates sheet on ASTPP?
     Navigate on ASTPP >> Carriers >> Termination Rates >> Import  >> Upload your CSV file

     You can download also sample file from there or
     The file must be in the following format(.csv): Code,Destination,Connect Cost,Included Seconds,Per Minute Cost,Initial Increment,Increment.

     Also, you import the file using filed map 
     
     Navigate on ASTPP >> Carriers >> Termination Rates >> Import Termination Rates using field mapper  >> Upload your CSV file

35. How to bill local calls?
     You can define local call charge on customer configuration.

     Navigate on ASTPP >> Accounts >> Customers >> Click on customer edit >> LC Charge / Min
     
36. How to enable call recording?
     Navigate on ASTPP >> Accounts >> Customers >> Click on customer edit >> Allow Recording =Yes
     
37. How to send multiple codecs or single codec to a provider(gateway)?
     You can configure codecs on specific trunk
     
     Navigate on ASTPP >> Carriers >> Trunks >> Click on trunk edit >> Codecs
     Ex : Codec = PCMA,PCMU,G729
