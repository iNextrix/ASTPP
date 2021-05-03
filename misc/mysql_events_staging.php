<?php
#Purpose of this file: We have used this file inplace of mysql EVENT. When we have multiple master server that time we faced issue in EVENT.

//Here we can define minutes which will remove cdr records from cdrs_staging table.
$minutes = "180";

//Get parameters of database connection from this file.
$astpp_config = parse_ini_file ( "/var/lib/astpp/astpp-config.conf" );
$dbname = $astpp_config['dbname'];
$dbuser = $astpp_config['dbuser'];
$dbpass = $astpp_config['dbpass'];
$dbhost = $astpp_config['dbhost'];

//Database connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname)or die("cannot connect");

//We call master procedure. In this procedure we have called sub procedure. We get records from cdrs_staging table and doing operation on it and then insert records into cdrs_day_by_summary table.
$query = "CALL master_pro()";
mysqli_query($conn,$query) ;

//It will Remove cdrs records from cdrs_staging table as per above specified interval. For example if you specify 180 then it will delete records current time - 180 minutes.
$query = "DELETE FROM cdrs_staging where end_stamp <= (NOW()- INTERVAL $minutes MINUTE)";
mysqli_query($conn,$query) ;
?>
