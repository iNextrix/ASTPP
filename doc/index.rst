
<h2>Install base packages</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="1e137620-380a-49d2-ae85-b94f81bdb7ce"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[apt-get -o Acquire::Check-Valid-Until=false update
apt-get install -y git wget curl]]></ac:plain-text-body></ac:structured-macro>
<h2>Install Freeswitch</h2>
<h4>1. Install Freeswitch pre-requisite packages</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="720b9eda-c2f0-42c8-ab77-5d670fdcc57d"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[#Add freeswitch source list
curl https://files.freeswitch.org/repo/deb/debian/freeswitch_archive_g0.pub | apt-key add -
echo "deb http://files.freeswitch.org/repo/deb/freeswitch-1.6/ jessie main" > /etc/apt/sources.list.d/freeswitch.list

#Install dependencies
apt-get -o Acquire::Check-Valid-Until=false update && apt-get install -y --force-yes freeswitch-video-deps-most
apt-get install -y autoconf automake devscripts gawk chkconfig dnsutils sendmail-bin sensible-mda ntpdate ntp g++ git-core curl libjpeg62-turbo-dev libncurses5-dev make python-dev pkg-config libgdbm-dev libyuv-dev libdb-dev libvpx2-dev gettext sudo lua5.1 php5 php5-dev php5-common php5-cli php5-gd php-pear php5-cli php-apc php5-curl libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc libldns-dev libpcre3-dev build-essential libssl-dev libspeex-dev libspeexdsp-dev libsqlite3-dev libedit-dev libldns-dev libpq-dev bc
#Install mysql server
apt-get install -y mysql-server php5-mysql
]]></ac:plain-text-body></ac:structured-macro>
<h4>2. Download latest freeswitch version</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="03baefd9-5dee-4f8a-a9eb-3293797173a2"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[cd /usr/local/src
git config --global pull.rebase true

#Clone freeswitch version 1.6 from git 
git clone -b v1.6.19 https://freeswitch.org/stash/scm/fs/freeswitch.git
cd freeswitch
./bootstrap.sh -j
]]></ac:plain-text-body></ac:structured-macro>
<h4>3. Edit modules.conf</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="1b72f0c3-6159-4f20-b336-cf6b78abe0de"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[#Enabling mod_xml_curl, mod_json_cdr, mod_db
sed -i "s#\#xml_int/mod_xml_curl#xml_int/mod_xml_curl#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#mod_db#mod_db#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#applications/mod_voicemail#applications/mod_voicemail#g" /usr/local/src/freeswitch/modules.conf
sed -i "s#\#event_handlers/mod_json_cdr#event_handlers/mod_json_cdr#g" /usr/local/src/freeswitch/modules.conf





]]></ac:plain-text-body></ac:structured-macro>
<p class="auto-cursor-target"><br /></p><ac:structured-macro ac:name="info" ac:schema-version="1" ac:macro-id="61c44dd7-36e4-4051-bb58-84b6a5bdce53"><ac:parameter ac:name="title">Note</ac:parameter><ac:rich-text-body>
<pre># add a module by removing '#' comment character at the beginning of the line
# remove a module by inserting the '#' comment character at the beginning of the line containing the name of the module<br />&nbsp; to be skipped</pre></ac:rich-text-body></ac:structured-macro>
<h4>4. Compile the Source</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="9a13ebe9-9b84-4e52-91ef-efceb90044a5"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[./configure -C
]]></ac:plain-text-body></ac:structured-macro>
<h4>5. Install Freeswitch with sound files</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="d0d03698-7844-4da2-9833-bdca6eb38f80"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[make all install cd-sounds-install cd-moh-install
make && make install ]]></ac:plain-text-body></ac:structured-macro>
<h4>6. Set right time in server</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="04c25c25-f927-4c9b-ba8e-b73344af9290"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[ntpdate pool.ntp.org
systemctl restart ntp
chkconfig ntp on]]></ac:plain-text-body></ac:structured-macro>
<h4>7. Create symbolic links for Freeswitch executables</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="6db9b92e-d169-4998-9ec1-3e12f632fe9f"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[ln -s /usr/local/freeswitch/bin/freeswitch /usr/local/bin/freeswitch
ln -s /usr/local/freeswitch/bin/fs_cli /usr/local/bin/fs_cli]]></ac:plain-text-body></ac:structured-macro>
<h2>ASTPP Install</h2>
<h4>1.&nbsp;Download ASTPP</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="538426d8-3ee1-47cd-a45a-25dd62d6fb20"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[# Download ASTPP 3.5 source from git
cd /usr/src
git clone https://github.com/iNextrix/ASTPP]]></ac:plain-text-body></ac:structured-macro>
<h4 style="line-height: 20.0px;">2. Change Apache working scenario</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="bcd68e32-1ca8-4c5a-8a07-e7da624892ea"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[As we are using Nginx from now onwards in ASTPP 3.0, if you are using apache for any applicaion then-
either have to move it to Nginx and/or remove apache. You can also change default port for apache if want to use-
it continue and troubleshoot some installation issue if arise.]]></ac:plain-text-body></ac:structured-macro>
<h4>3. Install ASTPP pre-requisite packages</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="200c5fc3-481a-4f6d-968e-1b960d8ef51d"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[apt-get -o Acquire::Check-Valid-Until=false update

apt-get install -y curl libyuv-dev libvpx2-dev nginx php5-fpm php5 php5-mcrypt libmyodbc unixodbc-bin php5-dev php5-common php5-cli php5-gd php-pear php5-cli php-apc php5-curl libxml2 libxml2-dev openssl libcurl4-openssl-dev gettext gcc g++]]></ac:plain-text-body></ac:structured-macro>
<h4>4. Normalize ASTPP</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="e5af34a6-fd1c-4609-a65b-5837b128d7ba"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[#Create access & error log files.
touch /var/log/nginx/astpp_access_log
touch /var/log/nginx/astpp_error_log
touch /var/log/nginx/fs_access_log
touch /var/log/nginx/fs_error_log			
php5enmod mcrypt
systemctl restart php5-fpm
service nginx reload]]></ac:plain-text-body></ac:structured-macro>
<h2>ASTPP using FreeSWITCH (if you want to use ASTPP with FreeSWITCH)</h2>
<h4>1. Configure freeswitch startup script</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="6051854e-2ddd-4cfb-b762-0daae9b15010"><ac:parameter ac:name="language">xml</ac:parameter><ac:plain-text-body><![CDATA[cp /usr/src/ASTPP/freeswitch/init/freeswitch.debian.init /etc/init.d/freeswitch

chmod 755 /etc/init.d/freeswitch
chmod +x /etc/init.d/freeswitch
update-rc.d freeswitch defaults
chkconfig --add freeswitch
chkconfig --level 345 freeswitch on]]></ac:plain-text-body></ac:structured-macro>
<h4>2. Configure ASTPP with freeswitch</h4><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="e6c53622-5114-4df5-9fc0-7bc7914c5e07"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[#Create directory structure for ASTPP
mkdir -p /var/lib/astpp/
mkdir -p /var/log/astpp/
mkdir -p /usr/local/astpp/
mkdir -p /var/www/

#Setting permisssion
chown -Rf root.root /var/lib/astpp/
chown -Rf www-data.www-data /var/log/astpp/
chown -Rf root.root /usr/local/astpp/
chown -Rf www-data.www-data /var/www/
 
#Setting up Scripts and Sounds for fs
cp -rf /usr/src/ASTPP/freeswitch/scripts/* /usr/local/freeswitch/scripts/
cp -rf /usr/src/ASTPP/freeswitch/fs /var/www/html/
cp -rf /usr/src/ASTPP/freeswitch/sounds/*.wav /usr/local/freeswitch/sounds/en/us/callie/
chmod -Rf 777 /usr/local/freeswitch/sounds/en/us/callie/
rm -rf  /usr/local/freeswitch/conf/dialplan/*
touch /usr/local/freeswitch/conf/dialplan/astpp.xml
rm -rf  /usr/local/freeswitch/conf/directory/*
touch /usr/local/freeswitch/conf/directory/astpp.xml
rm -rf  /usr/local/freeswitch/conf/sip_profiles/*
touch /usr/local/freeswitch/conf/sip_profiles/astpp.xml]]></ac:plain-text-body></ac:structured-macro>
<h2>Install ASTPP web interface</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="297a5349-4489-4da4-80bc-59c7da44478c"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[mkdir -p /var/lib/astpp
cp /usr/src/ASTPP/config/* /var/lib/astpp/
 
#Setup web interface for ASTPP
mkdir -p /var/www/html/astpp
cp -rf /usr/src/ASTPP/web_interface/astpp/* /var/www/html/astpp/
chown -Rf www-data.www-data /var/www/html/astpp
cp /usr/src/ASTPP/web_interface/nginx/deb_* /etc/nginx/conf.d/

chmod -Rf 755 /var/www/html/astpp
touch /var/log/astpp/astpp.log
chown -Rf www-data.www-data /var/log/astpp/astpp.log]]></ac:plain-text-body></ac:structured-macro>
<h2>Install ASTPP Database</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="e8e0c964-e7cf-43f9-9638-796f71d97e29"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[#Restart mysql service
systemctl restart mysql
mysql -uroot -e "UPDATE mysql.user SET password=PASSWORD('<MYSQL_ROOT_PASSWORD>') WHERE user='root'; FLUSH PRIVILEGES;"

#Create database astpp
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "create database astpp;"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "CREATE USER 'astppuser'@'localhost' IDENTIFIED BY '<ASTPP_USER_PASSWORD>';"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> -e "GRANT ALL PRIVILEGES ON \`astpp\` . * TO 'astppuser'@'localhost' WITH GRANT OPTION;FLUSH PRIVILEGES;"
mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-3.0.sql
mysql -uroot -p<MYSQL_ROOT_PASSWORD> astpp < /usr/src/ASTPP/database/astpp-upgrade-3.5.sql

 
#Setup ODBC Connection for mysql
cp /usr/src/ASTPP/misc/odbc/deb_odbc.ini /etc/odbc.ini
cp /usr/src/ASTPP/misc/odbc/deb_odbcinst.ini /etc/odbcinst.ini
 
#Update your mysql login information in odbc file
sed -i "s#PASSWORD = <PASSWORD>#PASSWORD = <MYSQL_ROOT_PASSWORD>#g" /etc/odbc.ini
 
Note:- Replace "<MYSQL_ROOT_PASSWORD>" with your mysql root login password and "<ASTPP_USER_PASSWORD>" is as per your choice.]]></ac:plain-text-body></ac:structured-macro>
<h2>ASTPP Freeswitch Configuration</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="1096e4ec-7934-43f9-9919-482b9dcd99a3"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[cp /usr/src/ASTPP/freeswitch/conf/autoload_configs/* /usr/local/freeswitch/conf/autoload_configs/
 
#Edit db password in autoload config files.
sed -i "s#dbpass = <PASSSWORD>#dbpass = <MYSQL_ROOT_PASSWORD>#g" /var/lib/astpp/astpp-config.conf
sed -i "s#DB_PASSWD=\"<PASSSWORD>\"#DB_PASSWD = \"<MYSQL_ROOT_PASSWORD>\"#g" /var/lib/astpp/astpp.lua
 
#Edit base URL in astpp-config
sed -i "s#base_url=http://localhost:8081/#base_url=http://<SERVER FQDN / IP ADDRESS>:8089/#g" /var/lib/astpp/astpp-config.conf
 
Note:- Replace "<SERVER FQDN / IP ADDRESS>" with your server domain name or IPaddress]]></ac:plain-text-body></ac:structured-macro>
<h2>Finalize Installation &amp; Start Services</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="3600713c-8730-4b54-9954-f0dc919dc768"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[#Open php short tag
sed -i "s#short_open_tag = Off#short_open_tag = On#g" /etc/php.ini
 
#Configure services for startup
systemctl disable apache2   #If you are using it then change the port or update your configuration for nginx otherwise your gui will not up
systemctl enable nginx
systemctl enable php5-fpm			
systemctl start mysql
systemctl start freeswitch
chkconfig --levels 345 mariadb on
chkconfig --levels 345 freeswitch on
 
Note:- If you want to use iptables then configure it to allow all port used in fs and ASTPP.	]]></ac:plain-text-body></ac:structured-macro>
<h2>Setup cron</h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="4e490368-3629-481a-bcae-4daa75c986bf"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[# Generate Invoice   
0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php GenerateInvoice

# Low balance notification
0 1 * * * cd /var/www/html/astpp/cron/ && php cron.php UpdateBalance
          
# Low balance notification
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php LowBalance
          
# Update currency rate
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php CurrencyUpdate


# Email Broadcasting
0 0 * * * cd /var/www/html/astpp/cron/ && php cron.php BroadcastEmail

]]></ac:plain-text-body></ac:structured-macro>
<h2><span style="line-height: 1.5;">Finally Reboot it.</span></h2><ac:structured-macro ac:name="code" ac:schema-version="1" ac:macro-id="81f38863-bc66-4735-ba4e-3a57c4d63b33"><ac:parameter ac:name="language">css</ac:parameter><ac:plain-text-body><![CDATA[#You are almost done with your configuration so just reboot it and make sure everything is working fine.
 
reboot now
 
#Once server up and running again, check below service status.
systemctl status nginx
systemctl status mysql
systemctl status freeswitch
systemctl status php5-fpm]]></ac:plain-text-body></ac:structured-macro>
<p class="auto-cursor-target"><br /></p><ac:structured-macro ac:name="info" ac:schema-version="1" ac:macro-id="68909318-2108-4d64-a0d1-6cc2714c5025"><ac:parameter ac:name="title">Note</ac:parameter><ac:rich-text-body>
<pre style="white-space: pre-wrap;">You are done with GUI installation. Enjoy :)
Visit the astpp admin page in your web browser. It can be found here: http://server_ip:8089/ Please change the ip address depending upon your box. The default username and password is &ldquo;admin&rdquo;. </pre>
<pre style="white-space: pre-wrap;">Note : In case of any issue please refer apache error log.</pre></ac:rich-text-body></ac:structured-macro>
<p class="auto-cursor-target"><br /></p><ac:structured-macro ac:name="note" ac:schema-version="1" ac:macro-id="ca8ec06b-358a-4bb8-a46f-c5db76017d03"><ac:parameter ac:name="title">Note</ac:parameter><ac:rich-text-body>
<p><span style="color: rgb(64,64,64);">If you have any other question(s) then please contact us on&nbsp;</span><a href="mailto:sales@inextrix.com"><span style="color: rgb(17,85,204);text-decoration: underline;">sales@inextrix.com</span></a><span style="color: rgb(64,64,64);">&nbsp;or post your questions(s) in</span><a href="https://groups.google.com/forum/#%21forum/astpp"><span style="color: rgb(64,64,64);">&nbsp;</span><span style="color: rgb(17,85,204);text-decoration: underline;">https://groups.google.com/forum/#!forum/astpp</span></a><span style="color: rgb(64,64,64);">.</span></p>
<div><span style="color: rgb(64,64,64);"><br /></span></div></ac:rich-text-body></ac:structured-macro>
<p class="auto-cursor-target"><br /></p>
