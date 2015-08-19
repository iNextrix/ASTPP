#! /bin/bash
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd. - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################

echo "************** Script to update ASTPP Version 2.0 to 2.1 *******************"
read -n 1 -p "Press any key to continue ..."

TIME=`date +"%Y%m%d-%T"`
BACKUP_DIR="astpp_$TIME"
ASTPP_SOURCE_DIR=/usr/src/trunk
ASTPPEXECDIR=/usr/local/astpp
CGIDIR=/var/www
WWWDIR=/var/www


# Linux Distribution CentOS or Debian
get_linux_distribution () {
        if [ -f /etc/debian_version ]; then
                DIST="DEBIAN"
                CGIDIR=/usr/lib
        elif  [ -f /etc/redhat-release ]; then
                DIST="CENTOS"
                CGIDIR=/var/www
        else
                DIST="OTHER"
        fi
}
get_linux_distribution

###############################################################
################# Save backup of all files  ###################
###############################################################

mkdir /mnt/$BACKUP_DIR
mv ${WWWDIR}/html/astpp /mnt/$BACKUP_DIR/web
mv ${ASTPPEXECDIR} /mnt/$BACKUP_DIR/scripts
mv ${CGIDIR}/cgi-bin /mnt/$BACKUP_DIR/

###############################################################
###### Remove an old source and download new one from git #####
###############################################################

rm -rf /usr/src/trunk/

cd /usr/src/
git clone https://github.com/ASTPP/trunk.git

mkdir -p ${WWWDIR}/html/astpp
mkdir -p ${ASTPPEXECDIR}
mkdir -p ${CGIDIR}/cgi-bin

cp -rf $ASTPP_SOURCE_DIR/web_interface/astpp/* ${WWWDIR}/html/astpp
cp ${ASTPP_SOURCE_DIR}/web_interface/astpp/htaccess ${WWWDIR}/html/astpp/.htaccess
cp -rf $ASTPP_SOURCE_DIR/scripts/*.pl ${ASTPPEXECDIR}
cp -rf $ASTPP_SOURCE_DIR/freeswitch/astpp ${CGIDIR}/cgi-bin

chmod -Rf 777 ${CGIDIR}/cgi-bin/astpp
chmod -Rf 777 ${WWWDIR}/html/astpp

if [ ${DIST} = "DEBIAN" ]; then
    chown -Rf www-data.www-data ${CGIDIR}/cgi-bin/
elif [ ${DIST} = "CENTOS" ]; then
  	chown -Rf apache.apache ${CGIDIR}/cgi-bin/
fi
###############################################################
################## update ASTPP database ######################
###############################################################

dbname=$(cat /var/lib/astpp/astpp-config.conf | grep dbname | cut -d " " -f 3)
dbuser=$(cat /var/lib/astpp/astpp-config.conf | grep dbuser | cut -d " " -f 3)
dbpass=$(cat /var/lib/astpp/astpp-config.conf | grep dbpass | cut -d " " -f 3)

echo "database name     : "$dbname
echo "database user     : "$dbuser
echo "database password : "$dbpass

VERSION=$(echo "SELECT value FROM system where name='version'" | mysql $dbname -u $dbuser -p$dbpass -ss -N)

echo "Current ASTPP Version : "$VERSION;
SQLFILE=$(echo $VERSION + 0.1 | bc);

echo "New Updated Version : "$SQLFILE;
filename_sql="astpp-upgrade-"$SQLFILE".sql";

echo "New SQL File Name : "$filename_sql;
[ -f $ASTPP_SOURCE_DIR/sql/$filename_sql ] && mysql -u${dbuser} -p${dbpass} ${dbname} < $ASTPP_SOURCE_DIR/sql/$filename_sql || echo "SQL File Not found"

echo "**************** Your ASTPP Version Successfully Updated ******************"
