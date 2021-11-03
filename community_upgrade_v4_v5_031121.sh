#!/bin/bash
 
FILE=astpp-`date +"%m-%d-%Y-%H_%M_%S"`.sql
DBSERVER="127.0.0.1"
DATABASE="astpp"
USER="root"
ROOT_PASS=`cat /root/.mysql_passwd | head -n1 | cut -c24-43`
PASS=$1
NOW=`date '+%Y_%m_%d_%T'`                                                                                                                                                                                                                                                                            
CHMOD=`which chmod`
CHOWN=`which chown`
MKDIR=`which mkdir`
SLEEP=`which sleep`
SYSTEMCTL=`which systemctl`
MYSQLDUMP=`which mysqldump`
MYSQL=`which mysql`
WGET=`which wget`
PHP=`which php`
USERPASS=`cat /var/lib/astpp/astpp-config.conf | grep dbpass | cut -c10-29`

cd /mnt/
$MYSQLDUMP --opt --user=${USER} --password=${PASS} --host=${DBSERVER} ${DATABASE} >  ${FILE}

exist=`ls  | grep ${FILE}`
size=`du -sh ${FILE} | cut -c1-1`
size=$(( $size + 0 ))

get_linux_distribution ()
{
       echo -e "===get_linux_distribution==="
       $SLEEP 2s
       V1=`cat /etc/*release | head -n1 | tail -n1 | cut -c 14- | cut -c1-18`
       V2=`cat /etc/*release | head -n7 | tail -n1 | cut -c 14- | cut -c1-14`
       V3=`cat /etc/*release | grep Deb | head -n1 | tail -n1 | cut -c 14- | cut -c1-19`
       if [[ $V1 = "Debian GNU/Linux 9" ]]; then
               DIST="DEBIAN"
       else if [[ $V2 = "CentOS Linux 7" ]]; then
               DIST="CENTOS"
       else if [[ $V3 = "Debian GNU/Linux 10" ]]; then
               DIST="DEBIAN10"
       else
               DIST="OTHER"
               echo -e 'Ooops!!! This script does not support your distribution \nPlease use manual steps or contact ASTPP Sales Team \nat sal
es@astpp.com.'
               exit 1
                
                
       fi
       fi
       fi
       echo -e "========Your OS is $DIST========="
       $SLEEP 4s
}

step1(){
       cd /opt/
       cp -r ASTPP/ ASTPP-bkp/
       $MKDIR /mnt/upload
       mv /var/www/html/astpp/upload/* /mnt/upload/
       $SLEEP 2
       tar -czf ASTPP_bkp.tar.gz ASTPP-bkp/
       $SLEEP 1
       rm -rf ASTPP-bkp/
       echo -e "Your source backup is in /opt/ASTPP_bkp.tar.gz and database backup is in /mnt/$FILE"
       cd /mnt/
}

step2(){
       cd /mnt/
       git clone -b v5.0 https://github.com/iNextrix/ASTPP.git
}

step3(){
       $MYSQL --user=${USER} --password=${PASS} --host=${DBSERVER} ${DATABASE} < /mnt/ASTPP/database/upgrade_v4_to_v5_community.sql
       rm -rf /opt/ASTPP/freeswitch/scripts/*
       $SLEEP 1
       rm -rf /opt/ASTPP 
       $SLEEP 5
       cp -r /mnt/ASTPP /opt/
       $CHMOD -Rf 777 /opt/ASTPP/
       rm -rf /mnt/ASTPP
       cp -r /mnt/upload/* /opt/ASTPP/web_interface/astpp/upload/
       $SLEEP 2
       $CHMOD -Rf 777 /var/www/html/astpp/upload/*
}

step4()
{
       cp /opt/ASTPP/misc/upgrade_v4_to_v5_community.php /opt/ASTPP/web_interface/astpp/application/controllers/
}

step5()
{
       cd /opt/ASTPP/web_interface/astpp
       $PHP index.php upgrade_v4_to_v5_community update_timezone
       $SLEEP 2
}


step6()
{      
       $CHMOD -Rf 777 /opt/ASTPP/
       $CHMOD -Rf 777 /opt/ASTPP/web_interface/astpp/addons/*
       sed -i "s#<\!--    <load module=\"mod_nibblebill\"/> -->#    <load module=\"mod_nibblebill\"/>#g" /etc/freeswitch/autoload_configs/modules.conf.xml
       rm -Rf /usr/share/freeswitch/scripts
       ln -s /opt/ASTPP/freeswitch/scripts /usr/share/freeswitch
       $SLEEP 1
       systemctl restart freeswitch
       echo "*********           IMPORTANT NOTE: Please check for new updates of addons and if its available update them            *********"
       echo "*********           IMPORTANT NOTE: Please check for new updates of addons and if its available update them            *********"
       echo "*********           IMPORTANT NOTE: Please check for new updates of addons and if its available update them            *********"

}

if  [ $exist == ${FILE}  ]; then
       if [[ $size != 0 ]]; then
               get_linux_distribution
               step1
               step2
               step3
               step4
               step5
               step6
       else
                echo -e 'Ooops!!! entered root user password is wrong.'
       fi
fi
