======================
Apache Authentication
======================

Apache authentication can be configured to require web site visitors to login with a user and password.

We protect cgi-bin apache directory which contains important perl scripts for dialplan, configurations and directory. 

**Step # 1: Make sure Apache is configured to use .htaccess file**
::

    You need to have "AllowOverride AuthConfig" directive in apache configuration file in order for directives to have 
    any effect.

    For CentOS
    vim /etc/httpd/conf/httpd.conf
    <Directory "/var/www/cgi-bin">
    AllowOverride AuthConfig
    Options None
    Order allow,deny
    Allow from all
    </Directory>

    Save the file and restart Apache
    # service httpd restart

    For Debian
    vim /etc/apache2/sites-available/default
    ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
    <Directory "/usr/lib/cgi-bin">
    AllowOverride AuthConfig
    Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
    Order allow,deny
    Allow from all
    </Directory>


    Save the file and restart Apache
    # service apache2 restart


**Step # 2: Create a password file with htpasswd**
::
   
    htpasswd command is used to create and update the flat-files (text file) used to store usernames and password for
    basic authentication of Apache users.General syntax: htpasswd -c password-file username
    Where,
                 -c : Create the password-file. If password-file already exists, it is rewritten and truncated.
                 username : The username to create or update in password-file. If username does not 
                 exist in this file, an entry is added. If it does exist, the password is changed.

    Create directory outside apache document root, so that only Apache can access password file. The password-file should 
    be placed somewhere not accessible from the web. This is so that people cannot download the password file:

    # mkdir -p /home/secure/
    Add new user called astpp
    # htpasswd -c /home/secure/apasswords astpp
     New password:
    Re-type new password:
    allow apache user apache to read password file:

    For CentOS
    # chown apache:apache /home/secure/apasswords
    # chmod 0660 /home/secure/apasswords

    For Debian
    # chown www-data:www-data /home/secure/apasswords
    # chmod 0660 /home/secure/apasswords

    Now user astpp is added but you need to configure the Apache web server to request a password and tell the server 
    which users are allowed access.We have directory /var/www/cgi-bin and we would like to protect it with a password.
    For CentOS
     # cd /var/www/cgi-bin
     # vim .htaccess
    For Debian
     # cd /usr/lib/cgi-bin
     # vim .htaccess
     Add following text:
                         AuthType Basic
                         AuthName "Restricted Access"
                         AuthUserFile /home/secure/apasswords
                         Require user astpp

    Now add username and password to following files:
    # vim /usr/local/freeswitch/conf/autoload_configs/xml_curl.conf.xml
    <!-- set this to provide authentication credentials to the server →
    <param name="gateway-credentials" value="astpp:your_password"/>

    # vim /usr/local/freeswitch/conf/autoload_configs/xml_cdr.conf.xm
    <!-- optional: credentials to send to web server -->
     <param name="cred" value="astpp:your_paasword"/>

    Now restart freeswitch
    # service freeswitch restart
      
  
You can test it by running below url in browser

http://localhost/cgi-bin/astpp/astpp-fs-xml.cgi

You will be asked for username and password for authentication.  
    
