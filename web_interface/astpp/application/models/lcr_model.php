<?php
class Lcr_model extends CI_Model 
{
    function Lcr_model()
    {     
        parent::__construct();      
    }
	
	function add_trunk($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Trunks";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_trunk($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Trunks";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_trunk_by_name($name)
	{
		$this->db->where("name",$name);
		$query = $this->db->get("trunks");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_trunk($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Trunks";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function getTrunkCount()
	{
		if($this->session->userdata('advance_search')==1){
					
		$trunks_search =  $this->session->userdata('trunks_search');
		
		$trunk_name_operator = $trunks_search['trunk_name_operator'];
			
			if(!empty($trunks_search['trunk_name'])) {
				switch($trunk_name_operator){
					case "1":
					$this->db->like('name', $trunks_search['trunk_name']); 
					break;
					case "2":
					$this->db->not_like('name', $trunks_search['trunk_name']);
					break;
					case "3":
					$this->db->where('name', $trunks_search['trunk_name']);
					break;
					case "4":
					$this->db->where('name <>', $trunks_search['trunk_name']);
					break;
				}
			}
			
			if(!empty($trunks_search['tech'])) {
				$this->db->where('tech', $trunks_search['tech']);
			}
			
			if(!empty($trunks_search['provider'])) {
				$this->db->where('provider', $trunks_search['provider']);
			}
			if(!empty($trunks_search['reseller'])) {
				$this->db->where('reseller', $trunks_search['reseller']);
			}
		
		}
		
		$this->db->where('status','1');
		$this->db->from('trunks');
		$trunkcnt = $this->db->count_all_results();
		return $trunkcnt;
	}
	
	function getTrunkList($start, $limit)
	{
		if($this->session->userdata('advance_search')==1){
					
		$trunks_search =  $this->session->userdata('trunks_search');
		
		$trunk_name_operator = $trunks_search['trunk_name_operator'];
			
			if(!empty($trunks_search['trunk_name'])) {
				switch($trunk_name_operator){
					case "1":
					$this->db->like('name', $trunks_search['trunk_name']); 
					break;
					case "2":
					$this->db->not_like('name', $trunks_search['trunk_name']);
					break;
					case "3":
					$this->db->where('name', $trunks_search['trunk_name']);
					break;
					case "4":
					$this->db->where('name <>', $trunks_search['trunk_name']);
					break;
				}
			}
			
			if(!empty($trunks_search['tech'])) {
				$this->db->where('tech', $trunks_search['tech']);
			}
			
			if(!empty($trunks_search['provider'])) {
				$this->db->where('provider', $trunks_search['provider']);
			}
			if(!empty($trunks_search['reseller'])) {
				$this->db->where('reseller', $trunks_search['reseller']);
			}
		
		}
		
		$this->db->where('status','1');
		$this->db->limit($limit,$start);	
	  	$this->db->from('trunks');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	function add_outbound($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Outbound Routes";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_outbound($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Outbound Routes";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_outbound_by_id($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get("outbound_routes");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_outbound($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Outbound Routes";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}	
	
	
	function getOutBoundCount()
	{
		$trunks = array('');
		
		if($this->session->userdata('advance_search')==1){
			
			$outbound_search =  $this->session->userdata('outbound_search');
			
			$pattern_operator = $outbound_search['pattern_operator'];
			
			if(!empty($outbound_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $outbound_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $outbound_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $outbound_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $outbound_search['pattern']);
					break;
				}
			}
			
			$prepend_operator = $outbound_search['prepend_operator'];
			
			if(!empty($outbound_search['prepend'])) {
				switch($prepend_operator){
					case "1":
					$this->db->like('prepend', $outbound_search['prepend']); 
					break;
					case "2":
					$this->db->not_like('prepend', $outbound_search['prepend']);
					break;
					case "3":
					$this->db->where('prepend', $outbound_search['prepend']);
					break;
					case "4":
					$this->db->where('prepend <>', $outbound_search['prepend']);
					break;
				}
			}
			
			$comment_operator = $outbound_search['comment_operator'];
			
			if(!empty($outbound_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $outbound_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $outbound_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $outbound_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $outbound_search['comment']);
					break;
				}
			}
			
			if(!empty($outbound_search['trunk'])) {
			$this->db->where('trunk', $outbound_search['trunk']);
			}
			
			$increment_operator = $outbound_search['increment_operator'];
			if(!empty($outbound_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $outbound_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $outbound_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $outbound_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $outbound_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $outbound_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $outbound_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $outbound_search['connect_charge_operator'];
			if(!empty($outbound_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $outbound_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $outbound_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $outbound_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $outbound_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $outbound_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $outbound_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $outbound_search['included_seconds_operator'];
			if(!empty($outbound_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $outbound_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $outbound_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $outbound_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $outbound_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $outbound_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $outbound_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $outbound_search['cost_per_add_minutes_operator'];
			if(!empty($outbound_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $outbound_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $outbound_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $outbound_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $outbound_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $outbound_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $outbound_search['cost_per_add_minutes']);
					break;
				}
			}	
			
			if(!empty($outbound_search['reseller'])) {
				$this->db->where('resellers ', $outbound_search['reseller']);
			}
			
				
		}
		
		if($this->session->userdata('logintype') == 3)
		{
			$this->db->where("provider",$this->session->userdata('username'));
			$query = $this->db->get("trunks");
			$result = $query->result_array();
			foreach($result as $row)
			{
				$trunks[] = $row['name'];
			}
			
			$this->db->where_in("trunk",$trunks);
		}	
		
		$this->db->where('status','1');
		$this->db->from('outbound_routes');
		$outboundcnt = $this->db->count_all_results();
		return $outboundcnt;
	}
	
	function getOutBoundList($start, $limit)
	{
		
		if($this->session->userdata('advance_search')==1){
			
			$outbound_search =  $this->session->userdata('outbound_search');
			
			$pattern_operator = $outbound_search['pattern_operator'];
			
			if(!empty($outbound_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $outbound_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $outbound_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $outbound_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $outbound_search['pattern']);
					break;
				}
			}
			
			$prepend_operator = $outbound_search['prepend_operator'];
			
			if(!empty($outbound_search['prepend'])) {
				switch($prepend_operator){
					case "1":
					$this->db->like('prepend', $outbound_search['prepend']); 
					break;
					case "2":
					$this->db->not_like('prepend', $outbound_search['prepend']);
					break;
					case "3":
					$this->db->where('prepend', $outbound_search['prepend']);
					break;
					case "4":
					$this->db->where('prepend <>', $outbound_search['prepend']);
					break;
				}
			}
			
			$comment_operator = $outbound_search['comment_operator'];
			
			if(!empty($outbound_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $outbound_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $outbound_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $outbound_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $outbound_search['comment']);
					break;
				}
			}
			
			if(!empty($outbound_search['trunk'])) {
			$this->db->where('trunk', $outbound_search['trunk']);
			}
			
			$increment_operator = $outbound_search['increment_operator'];
			if(!empty($outbound_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $outbound_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $outbound_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $outbound_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $outbound_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $outbound_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $outbound_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $outbound_search['connect_charge_operator'];
			if(!empty($outbound_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $outbound_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $outbound_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $outbound_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $outbound_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $outbound_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $outbound_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $outbound_search['included_seconds_operator'];
			if(!empty($outbound_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $outbound_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $outbound_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $outbound_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $outbound_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $outbound_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $outbound_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $outbound_search['cost_per_add_minutes_operator'];
			if(!empty($outbound_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $outbound_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $outbound_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $outbound_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $outbound_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $outbound_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $outbound_search['cost_per_add_minutes']);
					break;
				}
			}	
			
			if(!empty($outbound_search['reseller'])) {
				$this->db->where('resellers ', $outbound_search['reseller']);
			}
			
				
		}
		
		$this->db->where('status','1');
		$this->db->limit($limit,$start);	
	  	$this->db->from('outbound_routes');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	
	
	function getProviderCount()
	{
		if($this->session->userdata('advance_search')==1){
			$providers_search =  $this->session->userdata('providers_search');
			
			$provider_name_operator = $providers_search['provider_name_operator'];
			
			if(!empty($providers_search['provider_name'])) {
				switch($provider_name_operator){
					case "1":
					$this->db->like('number', $providers_search['provider_name']); 
					break;
					case "2":
					$this->db->not_like('number', $providers_search['provider_name']);
					break;
					case "3":
					$this->db->where('number', $providers_search['provider_name']);
					break;
					case "4":
					$this->db->where('number <>', $providers_search['provider_name']);
					break;
				}
			}
			
			$first_name_operator = $providers_search['first_name_operator'];
			if(!empty($providers_search['first_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('first_name', $providers_search['first_name']); 
					break;
					case "2":
					$this->db->not_like('first_name', $providers_search['first_name']);
					break;
					case "3":
					$this->db->where('first_name', $providers_search['first_name']);
					break;
					case "4":
					$this->db->where('first_name <>', $providers_search['first_name']);
					break;
				}
			}
			
			$last_name_operator = $providers_search['last_name_operator'];
			if(!empty($providers_search['last_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('last_name', $providers_search['last_name']); 
					break;
					case "2":
					$this->db->not_like('last_name', $providers_search['last_name']);
					break;
					case "3":
					$this->db->where('last_name', $providers_search['last_name']);
					break;
					case "4":
					$this->db->where('last_name <>', $providers_search['last_name']);
					break;
				}
			}
			
			$company_operator = $providers_search['company_operator'];
			if(!empty($providers_search['company'])) {
				switch($company_operator){
					case "1":
					$this->db->like('company_name', $providers_search['company']); 
					break;
					case "2":
					$this->db->not_like('company_name', $providers_search['company']);
					break;
					case "3":
					$this->db->where('company_name', $providers_search['company']);
					break;
					case "4":
					$this->db->where('company_name <>', $providers_search['company']);
					break;
				}
			}
			
			$balance_operator = $providers_search['balance_operator'];
			if(!empty($providers_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('balance ', $providers_search['balance']);
					break;
					case "2":
					$this->db->where('balance <>', $providers_search['balance']);					
					break;
					case "3":
					$this->db->where('balance > ', $providers_search['balance']); 					
					break;
					case "4":
					$this->db->where('balance < ', $providers_search['balance']); 	
					break;
					case "5":
					$this->db->where('balance >= ', $providers_search['balance']);
					break;
					case "6":
					$this->db->where('balance <= ', $providers_search['balance']);
					break;
				}
			}
			
			$creditlimit_operator = $providers_search['creditlimit_operator'];
			if(!empty($providers_search['creditlimit'])) {
				switch($creditlimit_operator){
					case "1":
					$this->db->where('credit_limit ', $providers_search['creditlimit']);
					break;
					case "2":
					$this->db->where('credit_limit <>', $providers_search['creditlimit']);					
					break;
					case "3":
					$this->db->where('credit_limit > ', $providers_search['creditlimit']); 					
					break;
					case "4":
					$this->db->where('credit_limit < ', $providers_search['creditlimit']); 	
					break;
					case "5":
					$this->db->where('credit_limit >= ', $providers_search['creditlimit']);
					break;
					case "6":
					$this->db->where('credit_limit <= ', $providers_search['creditlimit']);
					break;
				}
			}

			
		}
		
		$this->db->where('type','3');
		$this->db->where('status','1');
		$this->db->from('accounts');
		$providercnt = $this->db->count_all_results();
		return $providercnt;
	}
	
	function getProviderList($start, $limit)
	{
		if($this->session->userdata('advance_search')==1){
			$providers_search =  $this->session->userdata('providers_search');
			
			$provider_name_operator = $providers_search['provider_name_operator'];
			
			if(!empty($providers_search['provider_name'])) {
				switch($provider_name_operator){
					case "1":
					$this->db->like('number', $providers_search['provider_name']); 
					break;
					case "2":
					$this->db->not_like('number', $providers_search['provider_name']);
					break;
					case "3":
					$this->db->where('number', $providers_search['provider_name']);
					break;
					case "4":
					$this->db->where('number <>', $providers_search['provider_name']);
					break;
				}
			}
			
			$first_name_operator = $providers_search['first_name_operator'];
			if(!empty($providers_search['first_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('first_name', $providers_search['first_name']); 
					break;
					case "2":
					$this->db->not_like('first_name', $providers_search['first_name']);
					break;
					case "3":
					$this->db->where('first_name', $providers_search['first_name']);
					break;
					case "4":
					$this->db->where('first_name <>', $providers_search['first_name']);
					break;
				}
			}
			
			$last_name_operator = $providers_search['last_name_operator'];
			if(!empty($providers_search['last_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('last_name', $providers_search['last_name']); 
					break;
					case "2":
					$this->db->not_like('last_name', $providers_search['last_name']);
					break;
					case "3":
					$this->db->where('last_name', $providers_search['last_name']);
					break;
					case "4":
					$this->db->where('last_name <>', $providers_search['last_name']);
					break;
				}
			}
			
			$company_operator = $providers_search['company_operator'];
			if(!empty($providers_search['company'])) {
				switch($company_operator){
					case "1":
					$this->db->like('company_name', $providers_search['company']); 
					break;
					case "2":
					$this->db->not_like('company_name', $providers_search['company']);
					break;
					case "3":
					$this->db->where('company_name', $providers_search['company']);
					break;
					case "4":
					$this->db->where('company_name <>', $providers_search['company']);
					break;
				}
			}
			
			$balance_operator = $providers_search['balance_operator'];
			if(!empty($providers_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('balance ', $providers_search['balance']);
					break;
					case "2":
					$this->db->where('balance <>', $providers_search['balance']);					
					break;
					case "3":
					$this->db->where('balance > ', $providers_search['balance']); 					
					break;
					case "4":
					$this->db->where('balance < ', $providers_search['balance']); 	
					break;
					case "5":
					$this->db->where('balance >= ', $providers_search['balance']);
					break;
					case "6":
					$this->db->where('balance <= ', $providers_search['balance']);
					break;
				}
			}
			
			$creditlimit_operator = $providers_search['creditlimit_operator'];
			if(!empty($providers_search['creditlimit'])) {
				switch($creditlimit_operator){
					case "1":
					$this->db->where('credit_limit ', $providers_search['creditlimit']);
					break;
					case "2":
					$this->db->where('credit_limit <>', $providers_search['creditlimit']);					
					break;
					case "3":
					$this->db->where('credit_limit > ', $providers_search['creditlimit']); 					
					break;
					case "4":
					$this->db->where('credit_limit < ', $providers_search['creditlimit']); 	
					break;
					case "5":
					$this->db->where('credit_limit >= ', $providers_search['creditlimit']);
					break;
					case "6":
					$this->db->where('credit_limit <= ', $providers_search['creditlimit']);
					break;
				}
			}

			
		}
		
		$this->db->where('type','3');
		$this->db->where('status','1');
		$this->db->limit($limit,$start);
		$this->db->order_by("number", "asc"); 
	  	$this->db->from('accounts');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}		
}
?>