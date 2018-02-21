<?php
class Useranimapping_model extends CI_Model 
{
    function Useranimapping_model()
    {     
        parent::__construct();      
    }
	
	function get_ANI_by_number($number)
	{
		$account = $this->session->userdata('username');	
		$this->db->where("account", $account);
		$this->db->where("number",$number);
		$query = $this->db->get("ani_map");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	function getUseranimappingCount()
	{
		$account = $this->session->userdata('username');	
		//$accountinfo = $this->session->userdata('accountinfo');		
		$this->db->where("account", $account);
		$this->db->from('ani_map');
		$userdidcnt = $this->db->count_all_results();
		return $userdidcnt;		
	}
	
	function getUseranimappingList($start, $limit)
	{
		$account = $this->session->userdata('username');	
		//$accountinfo = $this->session->userdata('accountinfo');		
		$this->db->where("account", $account);
		$this->db->order_by("number", "desc"); 
		$this->db->limit($limit,$start);	
	  	$this->db->from('ani_map');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;			  	  
	}
	
	function add_Map_ANI($data)
	{
		$this->load->library("curl");	
		$url = "astpp-wraper.cgi";
		$data['mode'] = "ANI Mapping";		
		$data['action'] = "Map ANI";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');	
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_Map_ANI($data)
	{
		$this->load->library("curl");	
		$url = "astpp-wraper.cgi";
		$data['mode'] = "ANI Mapping";		
		$data['action'] = "Remove ANI";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');	
		$this->curl->sendRequestToPerlScript($url,$data);
	}
}
?>