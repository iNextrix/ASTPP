<?php
class Userdid_model extends CI_Model 
{
    function Userdid_model()
    {     
        parent::__construct();      
    }
	
	function get_did_by_number($number)
	{
		$this->db->where("number",$number);
		$query = $this->db->get("dids");
	
		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	function getUserdidCount()
	{
		$accountinfo = $this->session->userdata('accountinfo');		
		$this->db->where("account", $accountinfo['number']);
		$this->db->where("status", '1');
		$this->db->from('dids');
		$userdidcnt = $this->db->count_all_results();
		return $userdidcnt;		
	}
	
	function getUserdidList($start, $limit)
	{
		$accountinfo = $this->session->userdata('accountinfo');		
		$this->db->where("account", $accountinfo['number']);
		$this->db->where('status','1');
		$this->db->order_by("number", "desc"); 
		$this->db->limit($limit,$start);	
	  	$this->db->from('dids');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;			  	  
	}
	
	function remove_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "DIDs";
		$data['action'] = "Remove...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	function edit_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "DIDs";
		$data['action'] = "Edit...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		echo $this->curl->sendRequestToPerlScript($url,$data);	
	}
}
?>