<?php
class Did_model extends CI_Model 
{
    function Did_model()
    {     
        parent::__construct();      
    }
	
	
	function add_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Manage DIDs";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$data['extension'] = str_replace ("%","###",$data['extension']);
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Manage DIDs";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$data['extension'] = str_replace ("%","###",$data['extension']);
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function remove_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Manage DIDs";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	function purchase_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "DIDs";
		$data['action'] = "Purchase DID";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);		
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
	
	function getUserCountDIDS($number)
	{
		
		$this->db->select('number');
		$this->db->where('account', $number);
		$this->db->where('status',1);
		$this->db->from('dids');
		
		return  $this->db->count_all_results();
	}
	
	function getUserDIDSList($start, $limit,$number)
	{
	 	$this->db->select('number');
	 	$this->db->where('account', $number);
		$this->db->where('status',1);
		$this->db->order_by('number', 'DESC');
		$this->db->limit($limit,$start);
		$this->db->from('dids');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;		
	}
	
	function remove_user_did($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "DIDs";
		$data['action'] = "Remove...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
			
		return $this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	function getdidCount()
	{
		if($this->session->userdata('advance_search')==1){
					
			$did_search =  $this->session->userdata('did_search');
			
			$number_operator = $did_search['number_operator'];
			
			if(!empty($did_search['number'])) {
				switch($number_operator){
					case "1":
					$this->db->like('number', $did_search['number_operator']); 
					break;
					case "2":
					$this->db->not_like('number', $did_search['number_operator']);
					break;
					case "3":
					$this->db->where('number', $did_search['number_operator']);
					break;
					case "4":
					$this->db->where('number <>', $did_search['number_operator']);
					break;
				}
			}
			
			$this->db->where('country', $did_search['country']);
			
			if(!empty($did_search['reseller'])) {
				$this->db->where('provider', $did_search['reseller']);
			}
			
			if(!empty($did_search['account_nummber'])) {
				$this->db->where('account', $did_search['account_nummber']);
			}
			
		}
		
		$this->db->where('status <', '2');
		$this->db->from('dids');
		$didcnt = $this->db->count_all_results();
		return $didcnt;
	}
	
	function getdidList($start, $limit) 
	{	
		if($this->session->userdata('advance_search')==1){
					
			$did_search =  $this->session->userdata('did_search');
			
			$number_operator = $did_search['number_operator'];
			
			if(!empty($did_search['number'])) {
				switch($number_operator){
					case "1":
					$this->db->like('number', $did_search['number_operator']); 
					break;
					case "2":
					$this->db->not_like('number', $did_search['number_operator']);
					break;
					case "3":
					$this->db->where('number', $did_search['number_operator']);
					break;
					case "4":
					$this->db->where('number <>', $did_search['number_operator']);
					break;
				}
			}
			
			$this->db->where('country', $did_search['country']);
			
			if(!empty($did_search['reseller'])) {
				$this->db->where('provider', $did_search['reseller']);
			}
			
			if(!empty($did_search['account_nummber'])) {
				$this->db->where('account', $did_search['account_nummber']);
			}
			
		}		
		
		$this->db->where('status <', '2');
		$this->db->limit($limit,$start);
		$this->db->order_by("number", "asc"); 
	  	$this->db->from('dids');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
}
?>