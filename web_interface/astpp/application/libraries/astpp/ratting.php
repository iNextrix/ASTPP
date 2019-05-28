<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ratting {

    protected $CI; 
    protected $fields = array();  
    protected $form_title = 'Form';
    protected $form_id = 'form';
    protected $form_action = '';
    protected $form_class = '';
    protected $hidden = array();
    protected $multipart = FALSE; 
    protected $submit_button = 'Submit';
    protected $after_button = '';
    protected $rules = array(); 

    function __construct($library_name = '') {

        $this->CI = & get_instance();
        $this->CI->load->library("timezone");
        $this->CI->load->model('db_model');
        $this->CI->load->library('email');
        $this->CI->load->library('session');
    }

    function ratting_list($data,$account_data){
		$account_id =$data['accountid'];
		$fax_num =$data['number'];
		$file_type=$data['type'];
		$file_name=$data['text'];
		$account_details = $this->CI->db_model->getSelect("*", "accounts", array("id" => $account_id));
		$account_details = $account_details->result_array();
		if(!$account_details[0]['pricelist_id'] == ''){
			$rate_group =$account_details[0]['pricelist_id'];
			$balance =$account_details[0]['balance'];
		if($account_data['type']==1 && $balance > $account_data['balance']){
			$this->CI->session->set_flashdata('astpp_notification', 'Reseller balance is low!');
			redirect(base_url() . 'web2fax/web2fax_list/');
			exit;
		}	
		if($balance <= 0){
			$this->CI->session->set_flashdata('astpp_notification', 'Insuficient belance!');
			redirect(base_url() . 'web2fax/web2fax_list/');
			exit;
		}

		$origination_details = $this->CI->db_model->getSelect("*", "routes", array("pricelist_id" => $rate_group));
		$origination_details = $origination_details->result_array();
		$origination_cost = $origination_details[0]['cost'];
		$origination_trunk = $origination_details[0]['trunk_id'];
		$termination_details = $this->CI->db_model->getSelect("*", "outbound_routes", array("trunk_id" => $origination_trunk));
		$termination_details = $termination_details->result_array();
		$termination_cost = $termination_details[0]['cost'];
		
		if($termination_cost >= $origination_cost){
			$this->CI->session->set_flashdata('astpp_notification', 'Cost not correct!');
			redirect(base_url() . 'web2fax/web2fax_list/');
			exit;
		}
		echo "Fax number :"; print_r($fax_num); echo "<br>"; 	
		echo "Balance :"; print_r($balance); echo "<br>"; 	
		echo "Origination Charge:"; print_r($origination_cost); echo "<br>"; 	
		echo "termination Charge:"; print_r($termination_cost); echo "<br>"; 	
		echo "Rate group :"; print_r($rate_group); echo "<br>"; 	
		exit;
	}
	else{
		$this->CI->session->set_flashdata('astpp_notification', 'Rategroup not found!');
		redirect(base_url() . 'web2fax/web2fax_list/');
		exit;
	}
	return true;
  }
   
     function get_prefix_destination($destination){
	$field = "pattern";
    	$max_len_prefix  = strlen($destination);
	$number_prefix = '(';
	while ($max_len_prefix  > 0){
		$number_prefix .= "$field='^".substr($destination,0,$max_len_prefix).".*' OR ";
		$max_len_prefix--;
	}
    	$number_prefix .= "$field='^defaultprefix.*')";
	return $number_prefix;
     }
   
   
   function ratting_list_customer($data,$account_arr){
       $account_id =$account_arr['pricelist_id'];
       $fax_num =$data['number'];
       $file_type=$data['type'];
       $file_name=$data['text'];
 	$rate_group =$account_arr['pricelist_id'];
	$balance =$account_arr['balance'];
	if($balance <= 0)
	{

	 $this->CI->session->set_flashdata('astpp_notification', 'Insuficient belance!');
        $logintype = $this->CI->session->userdata('logintype');	 
		if($logintype == 0){
			redirect(base_url() . 'user/user_web2fax_list/');

		}else{
			redirect(base_url() . 'web2fax/web2fax_list/');
		}	
	}
	$number_prefix = $this->get_prefix_destination($data['number']);
	$this->CI->db->where($number_prefix);
	$origination_details = $this->CI->db_model->getSelect("*", "routes", array("pricelist_id" => $rate_group));
        $origination_details = $origination_details->result_array();


	return true;
   }
   function license_check($module_name){
	$filename = FCPATH."license_fax.txt";
	if (file_exists($filename)) {
		$localkey = file_get_contents($filename);
	} else {
		$localkey = '';
	}
	$results = $this->authentication_check_license($licensekey = "", $localkey);
	$status = $results['status'];
	if ($results['status'] == 'Invalid') {
		$logintype = $this->CI->session->userdata('logintype');	 
		if($logintype == 0 && $module_name == 'web2fax' ){
			redirect(base_url() . 'web2fax/web2fax_list_authentication/');
		}else{
			if($logintype == 0 && $module_name == 'authemail' ){
				redirect(base_url() . 'authemail/user_authemail_list_authentication/');
			}else{
				redirect(base_url() . 'authemail/authemail_list_authentication/');
			}
		}	
	}
	return true;
   }
   function license_key_check($postarr){
	$filename = FCPATH."license_fax.txt";
	$log_type = $postarr['log_type'];
	$licensekey = $postarr['akey'];
	$localkey = "";
 	$results = $this->authentication_check_license($licensekey, $localkey);
	if($results['status'] == 'Active'){
		$localkey = $results['localkey']; 
	}
	$FileContent = $localkey;
	if(file_exists($filename)){	
		$myfile = fopen($filename, "w") or die("Unable to open file license_fax.txt file!");
		$txt = $localkey;
		fwrite($myfile, $txt);
		fclose($myfile);
	}
	return $results['status'];
    }
    function authentication_check_license($licensekey, $localkey,$whmcs_url="") {
	$whmcsurl = $whmcs_url;
	$whmcsurl = 'http://portal.inextrix.com/';
	$licensing_secret_key = 'Aja$v@2A#a9^F';
	$localkeydays = 1000;
	$allowcheckfaildays = 0;
	$check_token = time() . md5(mt_rand(1000000000, 9999999999) . $licensekey);
	$checkdate = date("Ymd");
	$domain = $_SERVER['SERVER_NAME'];
	$usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
	$dirpath = $_SERVER['DOCUMENT_ROOT'];
	$verifyfilepath = 'modules/servers/licensing/verify.php';
	$localkeyvalid = false;
	if ($localkey) {
		$localkey = str_replace("\n", '', $localkey); # Remove the line breaks
		$localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
		$md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
		if ($md5hash == md5($localdata . $licensing_secret_key)) {

		$localdata = strrev($localdata); # Reverse the string
		$md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
		$localdata = substr($localdata, 32); # Extract License Data
		$localdata = base64_decode($localdata);
		$localkeyresults = unserialize($localdata);
		$originalcheckdate = $localkeyresults['checkdate'];
		if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {

		$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
		if ($originalcheckdate > $localexpiry) {

		$localkeyvalid = true;
		$results = $localkeyresults;
		$validdomains = explode(',', $results['validdomain']);


		if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {

		$localkeyvalid = false;
		$localkeyresults['status'] = "Invalid";
		$results = array();
		}
		$validips = explode(',', $results['validip']);
		if (!in_array($usersip, $validips)) {

		$localkeyvalid = false;
		$localkeyresults['status'] = "Invalid";
		$results = array();
		}

		$validdirs = explode(',', $results['validdirectory']);


		if (!in_array($dirpath, $validdirs)) {
		$localkeyvalid = false;
		$localkeyresults['status'] = "Invalid";
		$results = array();
		}
		}
		}
		}
	}
	if (!$localkeyvalid) {
		$postfields = array(
			'licensekey' => $licensekey,
			'domain' => $domain,
			'ip' => $usersip,
			'dir' => $dirpath,
		);
		if ($check_token)
			$postfields['check_token'] = $check_token;
		$query_string = '';
		foreach ($postfields AS $k => $v) {
			$query_string .= $k . '=' . urlencode($v) . '&';
		}
		if (function_exists('curl_exec')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
		} else {
			$fp = fsockopen($whmcsurl, 80, $errno, $errstr, 5);
			if ($fp) {
				$newlinefeed = "\r\n";
				$header = "POST " . $whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
				$header .= "Host: " . $whmcsurl . $newlinefeed;
				$header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
				$header .= "Content-length: " . @strlen($query_string) . $newlinefeed;
				$header .= "Connection: close" . $newlinefeed . $newlinefeed;
				$header .= $query_string;
				$data = '';
				@stream_set_timeout($fp, 20);
				@fputs($fp, $header);
				$status = @socket_get_status($fp);
				while (!@feof($fp) && $status) {
				$data .= @fgets($fp, 1024);
				$status = @socket_get_status($fp);
				}
				@fclose($fp);
			}
		}
		if (!$data) {
			$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
			if ($originalcheckdate > $localexpiry) {
				$results = $localkeyresults;
			} else {
				$results = array();
				$results['status'] = "Invalid";
				$results['description'] = "Remote Check Failed";
				return $results;
			}
		} else {
			preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
			$results = array();
			foreach ($matches[1] AS $k => $v) {
				$results[$v] = $matches[2][$k];
			}
		}
		if (!is_array($results)) {
			die("Invalid License Server Response");
		}
		if ($results['status'] == "Active") {
			$results['checkdate'] = $checkdate;
			$data_encoded = serialize($results);
			$data_encoded = base64_encode($data_encoded);
			$data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
			$data_encoded = strrev($data_encoded);
			$data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
			$data_encoded = wordwrap($data_encoded, 80, "\n", true);
			$results['localkey'] = $data_encoded;
		}
		$results['remotecheck'] = true;
	}
	unset($postfields, $data, $matches, $whmcsurl, $licensing_secret_key, $checkdate, $usersip, $localkeydays, $allowcheckfaildays, $md5hash);
	return $results;
    }
}
?>
