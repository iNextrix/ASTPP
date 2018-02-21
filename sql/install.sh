#!/bin/bash
clear
echo ""
echo "Install ASTPP Database"
echo "****************** WARNING WARNING  WARNING *****************"
echo " "
echo "      	   Please use MySQL Root Credencial          	  "     
echo "     Appropriate database should exist in database.        "     
echo ""
echo "*************************************************************"
echo ""

echo "Enter MySQL Hostname : "
read astpp_hostname

echo "Enter MySQL root UserName : "
read astpp_username

echo "Enter MySQL Password : "
read astpp_password

echo "Enter ASTPP Database Name : "
read astpp_dbname

mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname < astpp-current.sql

echo "";
read -n 1 -p "Press 0 For Freeswitch & Press 1 For Asterisk [Default:0]: ";
echo 
if [ "$REPLY" = "1" ]; then
      
    mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "
    update system set value=0 where name='softswitch';
    update system set value='0' where name='users_dids_rt';
    update system set value='0' where name='users_dids_freeswitch';";

#   AsteriskCDRDB installation  
    REPLY="y";
    read -n 1 -p "Do You Want To Install AsteriskCDR? (y/n) [Default:y] : ";
    if [ "$REPLY" = "n" ]; then
	echo "";
    else
	echo "";
	REPLY="y";
	read -n 1 -p "Do You Want To Use Same Database Credencial As ASTPP ? (y/n) [Default:y] : ";
	echo 
	echo "";
	if [ "$REPLY" = "n" ]; then
	    
	    echo "Enter Asterisk CDR Hostname : "
	    read asteriskcdrdb_hostname

	    echo "Enter Asterisk CDR UserName : "
	    read asteriskcdrdb_username

	    echo "Enter Asterisk CDR Password : "
	    read asteriskcdrdb_password
	    
	    echo "Enter Asterisk CDR Database Name : "
	    read asteriskcdrdb_dbname

	else
	    echo "Enter Asterisk CDR Database Name : "
	    read asteriskcdrdb_dbname
	    
	    asteriskcdrdb_hostname=$astpp_hostname;
	    asteriskcdrdb_username=$astpp_username;
	    asteriskcdrdb_password=$astpp_password;
	fi
	mysql --user=$asteriskcdrdb_username --password=$asteriskcdrdb_password --host=$asteriskcdrdb_hostname $asteriskcdrdb_dbname < asteriskcdrdb-current.sql
	
	mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "
	update system set value='$asteriskcdrdb_hostname' where name='cdr_dbhost';update system set value='$asteriskcdrdb_dbname' where name='cdr_dbname'; 
	update system set value='$asteriskcdrdb_username' where name='cdr_dbuser';update system set value='$asteriskcdrdb_password' where name='cdr_dbpass';";
    fi
#   Asterisk Realtime database installation  
  echo "";
  REPLY="y";
  read -n 1 -p "Do You Want To Install Realtime Asterisk Database? (y/n) [Default:y] : ";
  echo 
  
    if [ "$REPLY" = "n" ]; then
	echo "";
    else
# 	REPLY="y";
	read -n 1 -p "Do You Want To Use Same Database Credencial As ASTPP ? (y/n) [Default:y] : ";
	echo 
	if [ "$REPLY" = "n" ]; then
	  
	    echo "Enter Asterisk Realtime Hostname : "
	    read realtime_hostname

	    echo "Enter Asterisk Realtime UserName : "
	    read realtime_username

	    echo "Enter Asterisk Realtime Password : "
	    read realtime_password
	    
	    echo "Enter Asterisk Realtime Database Name : "
	    read realtime_dbname

	else
	    echo "Enter Asterisk Realtime Database Name : "
	    read realtime_dbname
	    
	    realtime_hostname=$astpp_hostname;
	    realtime_username=$astpp_username;
	    realtime_password=$astpp_password;
	fi
	mysql --user=$realtime_username --password=$realtime_password --host=$realtime_hostname $realtime_dbname < asterisk-realtime-current.sql	
	mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "update system set value='1' where name='users_dids_rt';
	update system set value='$realtime_hostname' where name='rt_host';
	update system set value='$realtime_dbname' where name='rt_db';update system set value='$realtime_username' where name='rt_user';
	update system set value='$realtime_password' where name='rt_pass';";
    fi    
else  
    mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "update system set value=1 where name='softswitch';";
#   Freeswitch CDR installation  
    echo "";
#     REPLY="y";  
    read -n 1 -p "Do You Want To Install FreeswitchCDR? (y/n) [Default:y] : ";
    echo 
    if [ "$REPLY" = "n" ]; then
	echo "";
    else
# 	REPLY="y";
	echo "";  
	read -n 1 -p "Do You Want To Use Same Database Credencial As ASTPP ? (y/n) [Default:y] : ";
	echo 
	echo "";
	if [ "$REPLY" = "n" ]; then
	  
	    echo "Enter Freeswitch CDR Hostname : "
	    read fscdrdb_hostname

	    echo "Enter Freeswitch CDR UserName : "
	    read fscdrdb_username

	    echo "Enter Freeswitch CDR Password : "
	    read fscdrdb_password
	    
	    echo "Enter Freeswitch CDR Database Name : "
	    read fscdrdb_dbname

	else
	    echo "Enter Freeswitch CDR Database Name : "
	    read fscdrdb_dbname
	    
	    fscdrdb_hostname=$astpp_hostname;
	    fscdrdb_username=$astpp_username;
	    fscdrdb_password=$astpp_password;
	fi
	mysql --user=$fscdrdb_username --password=$fscdrdb_password --host=$fscdrdb_hostname $fscdrdb_dbname < freeswitchcdrdb-current.sql
	
	mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "
	update system set value='$fscdrdb_hostname' where name='cdr_dbhost';update system set value='$fscdrdb_dbname' where name='cdr_dbname'; 
	update system set value='$fscdrdb_username' where name='cdr_dbuser';update system set value='$fscdrdb_password' where name='cdr_dbpass';";
    fi
#   Freeswitch database installation  
  echo "";
#   REPLY="y";
  read -n 1 -p "Do You Want To Install Freeswitch Database? (y/n) [Default:y] : ";
  echo   
    if [ "$REPLY" = "n" ]; then
	echo "";
    else
	echo "";
# 	REPLY="y";
	read -n 1 -p "Do You Want To Use Same Database Credencial As ASTPP ? (y/n) [Default:y] : ";
	echo 
	echo "";
	if [ "$REPLY" = "n" ]; then
	  
	    echo "Enter Freeswitch Hostname : "
	    read fs_hostname

	    echo "Enter Freeswitch UserName : "
	    read fs_username

	    echo "Enter Freeswitch Password : "
	    read fs_password
	    
	    echo "Enter Freeswitch Database Name : "
	    read fs_dbname

	else
	    echo "Enter Freeswitch Database Name : "
	    read fs_dbname
	    
	    fs_hostname=$astpp_hostname;
	    fs_username=$astpp_username;
	    fs_password=$astpp_password;
	fi
	mysql --user=$fs_username --password=$fs_password --host=$fs_hostname $fs_dbname < freeswitch-current.sql
	mysql --user=$astpp_username --password=$astpp_password --host=$astpp_hostname $astpp_dbname -e "update system set value='1' where name='users_dids_freeswitch';update system set value='$fs_hostname' where name='freeswitch_dbhost';update system set value='$fs_dbname' where name='freeswitch_dbname';update system set value='$fs_username' where name='freeswitch_dbuser';update system set value='$fs_password' where name='freeswitch_dbpass';";
    fi        
fi

echo "";
echo "-----------------------------";
echo "Database Installation Done";
echo "-----------------------------";
echo "";
# All done, exit ok
exit 0