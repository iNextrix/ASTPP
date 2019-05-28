<?php
session_start();
include "config.php";
 $filename = $licence_file;
if(filesize($filename) > 0){
	echo file_exists($filename);
}
else{
	echo 0;
}
?>
