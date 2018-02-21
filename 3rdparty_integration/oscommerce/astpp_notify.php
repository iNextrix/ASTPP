<?
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
######################################################################################################

 function fsockPost($url,$data) { 
	//Parse url 
	$web=parse_url($url); 
	//build post string 
	foreach($data as $i=>$v) {
		$postdata.= $i . "=" . urlencode($v) . "&"; 
	}
	$nocache=rand();
	$postdata.="nocache=$nocache";

	//Set the port number
	if($web[scheme] == "https") {
		$web[port]="443";
		$ssl="ssl://";
	} else {
		$web[port]="80";
	}  

	//Create socket connection
	$fp=@fsockopen($ssl . $web[host],$web[port],$errnum,$errstr,30); 
	//Error checking
	if(!$fp) {
		echo "Can't open socket to remote addr ($errnum): $errstr - please check your webserver configuration!<br>Connect str: $ssl . $web[host],$web[port],30<br>URL: $url<br>";
	} else { //Posting Data
		fputs($fp, "POST $web[path] HTTP/1.1\r\n"); 
		fputs($fp, "Host: $web[host]\r\n"); 
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
		fputs($fp, "Content-length: ".strlen($postdata)."\r\n"); 
		fputs($fp, "Connection: close\r\n\r\n"); 
		fputs($fp, $postdata . "\r\n\r\n"); 

		//loop through the response from the server 
		while(!feof($fp)) {
			$info[]=@fgets($fp, 1024);
		} 

		//close fp - we are done with it
		fclose($fp);

		//break up results into a string
		$info=implode(",",$info); 

	}
	return $info; 
 }

 ?>