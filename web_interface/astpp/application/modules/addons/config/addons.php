<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

//Set mode to read the addons information. (development / production)
$config ['module_mode'] = "development";

//If development define url to read modules xml file
$config ['module_xml'] = 'http://localhost/ADDONS/';

//If development define url to download module .zip
$config ['module_download_url'] = "http://localhost/ADDONS/";

//~ $config['astpp_path'] = "/opt/ITPLATP/web_interface/";
$config['addons_path'] = FCPATH."addons/";

$config['fs_path'] = "/var/www/html/fs/";
$config['fs_usr_path'] = "/usr/share/freeswitch/";
