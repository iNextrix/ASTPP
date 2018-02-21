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

class Feedback extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		 if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/login/login');

	}
	
	function index(){
		$data['account_info'] = $this->session->userdata['accountinfo'];
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = "Feedback";
		$this->load->view('view_feedback',$data);
	}
	function customer_feedback_result(){
	       
		$account_info = $this->session->userdata['accountinfo'];
		$name=$_REQUEST['name'];
		$email=$_REQUEST['email'];
		$feedback=$_REQUEST['feedback'];
		$first_name=$account_info['first_name'];
		$last_name=$account_info['last_name'];
		$city=$account_info['city'];
		$telephone_1=$account_info['telephone_1'];
		$account_email=$account_info['email'];
		$company_name=$account_info['company_name'];
		$address_1=$account_info['address_1'];
		$address_2=$account_info['address_2'];
		$telephone_2=$account_info['telephone_2'];
		$province=$account_info['province'];

		$data=array("name"=>$name,"email"=>$email,"feedback"=>$feedback,"first_name"=>$first_name,"last_name"=>$last_name,"city"=>$city,"telephone_1"=>$telephone_1,"account_email"=>$account_email,"company_name"=>$company_name,"address_1"=>$address_1,"address_2"=>$address_2,"telephone_2"=>$telephone_2,"province"=>$province,"serverip"=>$_SERVER['SERVER_ADDR']);
	        $data_new= json_encode($data);
	        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://65.111.177.99/feedback/feedback.php');  
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
		curl_setopt($ch, CURLOPT_HEADER, 1);  
		curl_setopt($ch, CURLOPT_POST, 1);  
		curl_setopt($ch, CURLOPT_VERBOSE, 1);  
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	        $response = curl_exec($ch);
	//print_r( $response); exit;
	     redirect(base_url() . 'feedback/thanks');
	    

	}
	function thanks(){

            $this->load->view('view_feedback_response');

}		
}
?>
