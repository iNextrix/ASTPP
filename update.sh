#!/bin/bash

###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# ASTPP Version 3.0
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
git pull
NOW=$(date +"%m-%d-%Y-%H-%M-%S")
BACKUPDIR="/opt/ASTPP3_Backup"
TEMPDIR="/opt/ASTPP3_patch"
if [ -d "${BACKUPDIR}" ]
then
        echo "Backup directory  exists!"
else
        echo "Backup directory not found!!!"."Creating Backup location . . . . ."
        mkdir ${BACKUPDIR}
fi
echo "date = "${NOW}
cd ${BACKUPDIR}
echo "Creating Backups files . . . . ."
tar czf ASTPP3_html_astpp_${NOW}.tar.gz /var/www/html/astpp
tar czf ASTPP3_html_fs_${NOW}.tar.gz /var/www/html/fs
tar czf ASTPP3_local_fsscripts_${NOW}.tar.gz /usr/local/freeswitch/scripts
echo "Backups files created at location "${BACKUPDIR}

mkdir ${TEMPDIR}
cd ${TEMPDIR}
git clone -v -b v3.0 https://github.com/countrdd/ASTPP
cd ASTPP
echo "Updating your current source with latest one . . . . . "
rm -rf /var/www/html/astpp/*
cp -rf web_interface/astpp/* /var/www/html/astpp/.
chown -Rf root.root /var/www/html/astpp
chmod -Rf 755 /var/www/html/astpp
chmod -Rf 777/var/www/html/astpp/assets/Rates_File/uploaded_files
rm -rf /var/www/html/fs/*
cp -rf freeswitch/fs/* /var/www/html/fs/.
chown -Rf root.root /var/www/html/fs
rm -rf /usr/local/freeswitch/scripts/*
cp -rf freeswitch/scripts/* /usr/local/freeswitch/scripts/.
echo "Update successfully Completed !!!!!!"

rm -rf ${TEMPDIR}
