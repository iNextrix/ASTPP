<?php
class Switch_config_model extends CI_Model 
{
    function Switch_config_model()
    {     
        parent::__construct();      
    }
	
	function add_switch($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Freeswitch(TM) SIP Devices";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_switch($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Freeswitch(TM) SIP Devices";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_switch_by_id($id)
	{
		$data = array("directory_id"=>$id);
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Freeswitch(TM) SIP Devices";
		$data['action'] = "Edit...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$return = $this->curl->sendRequestToPerlScript($url,$data);
		
		$return_array = explode("###",trim($return));
		$switch = array();
		$switch['directory_id'] = $return_array[0];
	    $switch['accountcode'] = $return_array[1];
	    $switch['context'] = $return_array[2];
	    $switch['fs_password'] = $return_array[3];
	    $switch['vm_password'] = $return_array[4];
	    $switch['fs_username'] = $return_array[5];;
		
		return $switch;
	}
	
	
	function remove_switch($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Freeswitch(TM) SIP Devices";
		$data['action'] = "Delete...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}

    function get_ip_map($ip)
	{
		$q = "SELECT ip,account as accountnum, prefix,context,created_date FROM ip_map WHERE ip ='$ip'";
                $astpp_comon = new Astpp_common();
                $result = $this->db->query($q);
                $ip_data = $result->row_array();
			
                return $ip_data;
	}
	
	function fs_retrieve_sip_user($directory_id) {
	
	$this->db_fs =  Common_model::$global_config['fs_db'];
	
	$deviceinfo = array();
	
	$tmp1 = "SELECT username FROM directory WHERE id = '".$directory_id. "'";
	$query1  = $this->db_fs->query($tmp1);
	$deviceinfo['username'] = "";
	if($query1->num_rows()>0) {
		$row1 = $query1->row_array();
		$deviceinfo['username'] = $row1['username'];
	}
	
	
	$tmp22 = "SELECT var_value FROM directory_vars WHERE directory_id = '".$directory_id."' AND var_name = 'user_context'";
	$query22  = $this->db_fs->query($tmp22);
	$deviceinfo['context'] = "";
	if($query22->num_rows()>0) {
	$row22 = $query22->row_array();
	$deviceinfo['context'] = $row22['var_value'];
	}
	
	$tmp2 = "SELECT param_value FROM directory_params WHERE directory_id = '".$directory_id."'  AND param_name = 'password' LIMIT 1";
	$query2  = $this->db_fs->query($tmp2);
	$deviceinfo['password'] = "";
	if($query2->num_rows()>0) {
	$row2 = $query2->row_array();
	$deviceinfo['password'] = $row2['param_value'];
	}
	
	$tmp3 = "SELECT param_value FROM directory_params WHERE directory_id = '".$directory_id."' AND param_name = 'vm-password' LIMIT 1";
	$query3  = $this->db_fs->query($tmp3);
	$deviceinfo['vm_password'] = "";
	if($query3->num_rows()>0) {
	$row3 = $query3->row_array();
	$deviceinfo['vm_password'] = $row3['param_value'];
	}
	
	$tmp4 = "SELECT var_value FROM directory_vars WHERE directory_id = '".$directory_id."' AND var_name = 'accountcode' LIMIT 1";
	$query4  = $this->db_fs->query($tmp4);
	$deviceinfo['accountcode'] = "";
	if($query4->num_rows()>0) {
	$row4 = $query4->row_array();
	$deviceinfo['accountcode'] = $row4['var_value'];
	}
	
	return $deviceinfo;
	}
	
}
?>