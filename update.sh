#!/bin/bash
###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# ASTPP Version 3.5
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################

clear;
echo "**************     Script to update ASTPP latest version  *******************"
echo " "
read -n 1 -p "Press any key to continue ..."

TIME=`date +"%Y%m%d-%T"`
BACKUP_DIR="astpp_$TIME"
ASTPP_SOURCE_DIR=/usr/src/ASTPP
FS=/usr/local/freeswitch
WWWDIR=/var/www
EMAIL=""
dbhost=$(cat /var/lib/astpp/astpp-config.conf | grep dbhost | cut -d " " -f 3)
dbname=$(cat /var/lib/astpp/astpp-config.conf | grep dbname | cut -d " " -f 3)
dbuser=$(cat /var/lib/astpp/astpp-config.conf | grep dbuser | cut -d " " -f 3)
dbpass=$(cat /var/lib/astpp/astpp-config.conf | grep dbpass | cut -d " " -f 3)
echo "database host     : "$dbhost
echo "database name     : "$dbname
echo "database user     : "$dbuser
echo "database password : "$dbpass

VERSION=$(echo "SELECT value FROM system where name='version'" | mysql $dbname -h $dbhost -u $dbuser -p$dbpass -ss -N)

if [ "$VERSION" != "3.5" || "$VERSION" != "3.6" ]; then 
	echo 'This upgrade script only supporting ASTPP v3.5 or v3.6'
	exit 1
fi

get_linux_distribution () {
        if [ -f /etc/debian_version ]; then
                DIST="DEBIAN"
		 apt-get update
		 apt-get install -y dnsutils
        elif  [ -f /etc/redhat-release ]; then
                DIST="CENTOS"
		yum install bind-utils
        else
                DIST="OTHER"
        fi
}
get_linux_distribution


###############################################################
################# Save backup of all files  ###################
###############################################################

mkdir /mnt/${BACKUP_DIR}
mv ${WWWDIR}/html/astpp /mnt/${BACKUP_DIR}/astpp_web
mv ${WWWDIR}/html/fs /mnt/${BACKUP_DIR}/fs_web
mv ${FS}/scripts /mnt/${BACKUP_DIR}/fs_astpp_scripts

###############################################################
###### Remove an old source and download new one from git #####
###############################################################

rm -rf /usr/src/latest/
rm -rf /usr/src/trunk/
rm -rf /usr/src/ASTPP/
rm -rf /usr/src/Enterprise/
cd /usr/src/
echo ""
read -p "Enter your email address: ${EMAIL}"
EMAIL=${REPLY}
git clone -b v3.6-dev https://github.com/iNextrix/ASTPP.git
NAT1=$(dig +short myip.opendns.com @resolver1.opendns.com)
NAT2=$(curl http://ip-api.com/json/)
INTF=$(ifconfig $1|sed -n 2p|awk '{ print $2 }'|awk -F : '{ print $2 }')
if [ "${NAT1}" != "${INTF}" ]; then
			echo "Server is behind NAT";
fi

mkdir -p ${WWWDIR}/html/astpp
mkdir -p ${WWWDIR}/html/fs
mkdir -p ${FS}/scripts

cp -rf ${ASTPP_SOURCE_DIR}/web_interface/astpp/* ${WWWDIR}/html/astpp/.
cp ${ASTPP_SOURCE_DIR}/web_interface/astpp/htaccess ${WWWDIR}/html/astpp/.htaccess
cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/fs/* ${WWWDIR}/html/fs/.
cp -rf ${ASTPP_SOURCE_DIR}/freeswitch/scripts/* ${FS}/scripts/.
cp -rf /mnt/${BACKUP_DIR}/astpp_web/assets/images/* ${WWWDIR}/html/astpp/assets/images/.
cp -rf /mnt/${BACKUP_DIR}/astpp_web/upload/* ${WWWDIR}/html/astpp/upload/.
cp -rf /mnt/${BACKUP_DIR}/astpp_web/attachments/* ${WWWDIR}/html/astpp/attachments/.
cp -rf /mnt/${BACKUP_DIR}/astpp_web/database_backup/* ${WWWDIR}/html/astpp/database_backup/.
cp -rf /mnt/${BACKUP_DIR}/astpp_web/invoices/* ${WWWDIR}/html/astpp/invoices/.

chmod -Rf 755 ${WWWDIR}/html/astpp
chmod -Rf 755 ${WWWDIR}/html/fs

if [ ${DIST} = "DEBIAN" ]; then
    chown -Rf www-data.www-data ${WWWDIR}/html/astpp
    chown -Rf root.root ${WWWDIR}/html/fs
elif [ ${DIST} = "CENTOS" ]; then
  	chown -Rf apache.apache ${WWWDIR}/html/astpp
  	chown -Rf root.root ${WWWDIR}/html/fs
fi

###############################################################
################## update ASTPP database ######################
###############################################################

echo "Current ASTPP Version : "$VERSION;
SQLFILE=$(echo $VERSION + 0.1 | bc);

if [ ${VERSION} = "3.5" ]; then
echo "New Updated Version : "$SQLFILE;
filename_sql="astpp-upgrade-"$SQLFILE".sql";
VERSION=3.6
echo "New SQL File Name : "$filename_sql;
[ -f $ASTPP_SOURCE_DIR/database/$filename_sql ] && mysql -h${dbhost} -u${dbuser} -p${dbpass} ${dbname} < $ASTPP_SOURCE_DIR/database/$filename_sql || echo "Database update not succeed !!!"
fi
curl --data "email=$EMAIL" --data "data=$NAT2" --data "type=Update" http://astppbilling.org/lib/
echo "******************************************************************************************"
echo "******************************************************************************************"
echo "******************************************************************************************"
echo "**********                                                                      **********"
echo "**********       Your ASTPP Version Successfully Updated to: "${VERSION}"                **********"
echo "**********       Old data backup location: "/mnt/${BACKUP_DIR}          "        **********"
echo "**********                                                                      **********"
echo "******************************************************************************************"
echo "******************************************************************************************"
echo "******************************************************************************************"
