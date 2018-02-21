<?php
class Rates_model extends CI_Model 
{
    function Rates_model()
    {     
        parent::__construct();      
    }
	
	function add_pricelist($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Pricelists";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_pricelist($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Pricelists";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_pricelist_by_name($name)
	{
		$this->db->where("name",$name);
		$query = $this->db->get("pricelists");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_pricelist($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Pricelists";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	
	function get_price_list()
	{
		//if($this->session->userdata('logintype') == 3)
		//$this->db->where("reseller",$this->session->userdata('username'));
		if($this->session->userdata('username') !="" && $this->session->userdata('logintype')!=2){
			$this->db->where('reseller', $this->session->userdata('username'));
		}
		else{
			$this->db->where(array( 'reseller' => NULL ));	
		}
		$this->db->where('status <', 2);
		$this->db->order_by('name','desc');
		$query = $this->db->get("pricelists");
		$price_list = array();

		$result =  $query->result_array();
		foreach($result as $row)
		{
			$price_list[$row['name']] = $row['name'];
		}
		
		return $price_list;
	}	
	
		
	function list_pricelists_select($default='')
	{
		$ret_html = '';
		$price_list = $this->get_price_list();
		foreach ($price_list as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}		
		return  $ret_html;
	}	
	
	
	function getPriceCount()
	{
		
		if($this->session->userdata('advance_search')==1){
			$pricelist_search =  $this->session->userdata('pricelist_search');
			
			$pricelist_name_operator = $pricelist_search['pricelist_name_operator'];
			
			if(!empty($pricelist_search['pricelist_name'])) {
				switch($pricelist_name_operator){
					case "1":
					$this->db->like('name', $pricelist_search['pricelist_name']); 
					break;
					case "2":
					$this->db->not_like('name', $pricelist_search['pricelist_name']);
					break;
					case "3":
					$this->db->where('name', $pricelist_search['pricelist_name']);
					break;
					case "4":
					$this->db->where('name <>', $pricelist_search['pricelist_name']);
					break;
				}
			}
			
			$default_increment_operator = $pricelist_search['default_increment_operator'];
			if(!empty($pricelist_search['default_increment'])) {
				switch($default_increment_operator){
					case "1":
					$this->db->where('inc ', $pricelist_search['default_increment']);
					break;
					case "2":
					$this->db->where('inc <>', $pricelist_search['default_increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $pricelist_search['default_increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $pricelist_search['default_increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $pricelist_search['default_increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $pricelist_search['default_increment']);
					break;
				}
			}
			
		}
		
		$this->db->where('status < ','2');
		if($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5)
		{
			if($this->session->userdata('username')!=""){
				$this->db->where('reseller',$this->session->userdata('username'));	
			}
		}
		else{			 
			$this->db->where(array( 'reseller' => NULL ));	
		}
		$this->db->from('pricelists');
		$trunkcnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $trunkcnt;
	}
				
	
	function getPriceList($start, $limit)
	{
		
		if($this->session->userdata('advance_search')==1){
			$pricelist_search =  $this->session->userdata('pricelist_search');
			
			$pricelist_name_operator = $pricelist_search['pricelist_name_operator'];
			
			if(!empty($pricelist_search['pricelist_name'])) {
				switch($pricelist_name_operator){
					case "1":
					$this->db->like('name', $pricelist_search['pricelist_name']); 
					break;
					case "2":
					$this->db->not_like('name', $pricelist_search['pricelist_name']);
					break;
					case "3":
					$this->db->where('name', $pricelist_search['pricelist_name']);
					break;
					case "4":
					$this->db->where('name <>', $pricelist_search['pricelist_name']);
					break;
				}
			}
			
			$default_increment_operator = $pricelist_search['default_increment_operator'];
			if(!empty($pricelist_search['default_increment'])) {
				switch($default_increment_operator){
					case "1":
					$this->db->where('inc ', $pricelist_search['default_increment']);
					break;
					case "2":
					$this->db->where('inc <>', $pricelist_search['default_increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $pricelist_search['default_increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $pricelist_search['default_increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $pricelist_search['default_increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $pricelist_search['default_increment']);
					break;
				}
			}
			
		}
		
		$this->db->where('status < ','2');		
		if($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) 
		{
			if($this->session->userdata('username')!=""){
			$this->db->where('reseller',$this->session->userdata('username'));	
			}
		}
		else{
			$this->db->where(array( 'reseller' => NULL ));	
		}
		$this->db->limit($limit,$start);
	  	$this->db->from('pricelists');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}	
	
	function add_route($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Routes";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_route($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Routes";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_route_by_id($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get("routes");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_route($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Routes";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function import_routes($data)
	{		
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Import Routes";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
		
	
	function add_charge($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Periodic Charges";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_charge($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Periodic Charges";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_charge_by_id($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get("charges");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_charge($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Periodic Charges";
		$data['action'] = "Delete...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function calculate_charges($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Calc Charge";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	
	function getPeriodicChargesCount()
	{
		if($this->session->userdata('advance_search')==1){
			
			$periodiccharges_search =  $this->session->userdata('periodiccharges_search');
			
			$description_operator = $periodiccharges_search['description_operator'];
			if(!empty($periodiccharges_search['description'])) {
				switch($description_operator){
					case "1":
					$this->db->where('description ', $periodiccharges_search['description']);
					break;
					case "2":
					$this->db->where('description <>', $periodiccharges_search['description']);					
					break;
					case "3":
					$this->db->where('description > ', $periodiccharges_search['description']); 					
					break;
					case "4":
					$this->db->where('description < ', $periodiccharges_search['description']); 	
					break;
					case "5":
					$this->db->where('description >= ', $periodiccharges_search['description']);
					break;
					case "6":
					$this->db->where('description <= ', $periodiccharges_search['description']);
					break;
				}
			}
			
			$this->db->where('pricelist', $periodiccharges_search['pricelist']);
			
			$charge_operator = $periodiccharges_search['charge_operator'];
			if(!empty($periodiccharges_search['charge'])) {
				switch($charge_operator){
					case "1":
					$this->db->like('charge', $periodiccharges_search['charge']); 
					break;
					case "2":
					$this->db->not_like('charge', $periodiccharges_search['charge']);
					break;
					case "3":
					$this->db->where('charge', $periodiccharges_search['charge']);
					break;
					case "4":
					$this->db->where('charge <>', $periodiccharges_search['charge']);
					break;
				}
			}
			$this->db->where('sweep', $periodiccharges_search['sweep']);
			$this->db->where('status', $periodiccharges_search['status']);		
		}
		
		$this->db->where('status < ','2');
		$this->db->from('charges');
		$trunkcnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $trunkcnt;
	}
	
	function getPeriodicChargesList($start, $limit)
	{
		if($this->session->userdata('advance_search')==1){
			
			$periodiccharges_search =  $this->session->userdata('periodiccharges_search');
			
			$description_operator = $periodiccharges_search['description_operator'];
			if(!empty($periodiccharges_search['description'])) {
				switch($description_operator){
					case "1":
					$this->db->where('description ', $periodiccharges_search['description']);
					break;
					case "2":
					$this->db->where('description <>', $periodiccharges_search['description']);					
					break;
					case "3":
					$this->db->where('description > ', $periodiccharges_search['description']); 					
					break;
					case "4":
					$this->db->where('description < ', $periodiccharges_search['description']); 	
					break;
					case "5":
					$this->db->where('description >= ', $periodiccharges_search['description']);
					break;
					case "6":
					$this->db->where('description <= ', $periodiccharges_search['description']);
					break;
				}
			}
			
			$this->db->where('pricelist', $periodiccharges_search['pricelist']);
			
			$charge_operator = $periodiccharges_search['charge_operator'];
			if(!empty($periodiccharges_search['charge'])) {
				switch($charge_operator){
					case "1":
					$this->db->like('charge', $periodiccharges_search['charge']); 
					break;
					case "2":
					$this->db->not_like('charge', $periodiccharges_search['charge']);
					break;
					case "3":
					$this->db->where('charge', $periodiccharges_search['charge']);
					break;
					case "4":
					$this->db->where('charge <>', $periodiccharges_search['charge']);
					break;
				}
			}
			$this->db->where('sweep', $periodiccharges_search['sweep']);
			$this->db->where('status', $periodiccharges_search['status']);		
		}
		$this->db->where('status < ','2');	
		$this->db->limit($limit,$start);
	  	$this->db->from('charges');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	
	function add_package($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Packages";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function edit_package($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Packages";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_package_by_id($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get("packages");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_package($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Packages";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	
	function getCountersCount()
	{
		$packages = "";
		$seconds = "";
		$account ="";
		if($this->session->userdata('advance_search')==1){
			
			$packages_search =  $this->session->userdata('packages_search');
			if(!empty($packages_search['packages'])) {
			$packages = " AND counter.package= '".$packages_search['packages']."' ";
			}
			
			if(!empty($packages_search['account_nummber'])) {
			$account = " AND counter.account= '".$packages_search['account_nummber']."' ";
			}
			
			$seconds_used_operator = $packages_search['seconds_used_operator'];
			
			if(!empty($packages_search['seconds_used'])) {
				switch($seconds_used_operator){
					case "1":
					$seconds = " AND seconds  = '" .$packages_search['seconds_used']."' ";
					break;
					case "2":
					$seconds = " AND seconds  <> '". $packages_search['seconds_used']."' ";					
					break;
					case "3":
					$seconds = " AND seconds > '". $packages_search['seconds_used']."' "; 					
					break;
					case "4":
					$seconds = " AND seconds < '". $packages_search['seconds_used']."' "; 	
					break;
					case "5":
					$seconds = " AND seconds >= '". $packages_search['seconds_used']."' ";
					break;
					case "6":
					$seconds = " AND seconds <= '". $packages_search['seconds_used']."' ";
					break;
				}
			}			
			
		}
		
		if($this->session->userdata('logintype')==2){
			 $sql_count ="SELECT COUNT(*) as numrows FROM counters, packages WHERE package IN(SELECT id FROM packages WHERE pricelist IN (SELECT name from pricelists WHERE reseller IS NULL))  AND counters.package = packages.id ".$packages."  ".$account." ".$seconds." ;";
		}
		elseif($this->session->userdata('logintype')==1){
			 $sql_count ="SELECT COUNT(*) as numrows FROM counters, packages WHERE package IN(SELECT id FROM packages WHERE pricelist IN(SELECT name FROM pricelists WHERE reseller = '".$this->session->userdata('username')."')) AND counters.package = packages.id  " .$packages. "  " .$account. " " .$seconds. " ;";
		}		
		$query = $this->db->query($sql_count);		
		//$trunkcnt = $this->db->count_all_results();
		$countercnt=  0 ;
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array(); 		
			$countercnt = $row['numrows'];
		}
		return $countercnt;
	}
	
	function getCountersList($start, $limit)
	{
		$packages = "";
		$seconds = "";
		$account ="";
		if($this->session->userdata('advance_search')==1){
			
			$packages_search =  $this->session->userdata('packages_search');
			if(!empty($packages_search['packages'])) {
			$packages = " AND counter.package= '".$packages_search['packages']."' ";
			}
			
			if(!empty($packages_search['account_nummber'])) {
			$account = " AND counter.account= '".$packages_search['account_nummber']."' ";
			}
			
			$seconds_used_operator = $packages_search['seconds_used_operator'];
			
			if(!empty($packages_search['seconds_used'])) {
				switch($seconds_used_operator){
					case "1":
					$seconds = " AND seconds  = '" .$packages_search['seconds_used']."' ";
					break;
					case "2":
					$seconds = " AND seconds  <> '". $packages_search['seconds_used']."' ";					
					break;
					case "3":
					$seconds = " AND seconds > '". $packages_search['seconds_used']."' "; 					
					break;
					case "4":
					$seconds = " AND seconds < '". $packages_search['seconds_used']."' "; 	
					break;
					case "5":
					$seconds = " AND seconds >= '". $packages_search['seconds_used']."' ";
					break;
					case "6":
					$seconds = " AND seconds <= '". $packages_search['seconds_used']."' ";
					break;
				}
			}		
		}
		
		if($this->session->userdata('logintype')==2){
			 $sql_select ="SELECT counters.id AS id, packages.name AS name,counters.account AS account, counters.seconds AS seconds, counters.status AS status FROM counters,packages WHERE package IN(SELECT id FROM packages WHERE pricelist IN (SELECT name from pricelists WHERE reseller IS NULL))  AND counters.package = packages.id " .$packages. "  " .$account. " " .$seconds. " limit $start, $limit";
		}
		elseif($this->session->userdata('logintype')==1){
			 $sql_select ="SELECT counters.id AS id, packages.name AS name,counters.account AS account, counters.seconds AS seconds, counters.status AS status FROM counters,packages WHERE package IN(SELECT id FROM packages WHERE pricelist IN(SELECT name FROM pricelists WHERE reseller = '".$this->session->userdata('username')."'))  AND counters.package = packages.id " .$packages. "  " .$account. " " .$seconds. " limit $start, $limit";
		}
		
		$query = $this->db->query($sql_select);
		//$this->db->limit($limit,$start);
	  	//$this->db->from('routes');	
		//$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	function getRoutesCount()
	{
		if($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) 
		{
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
		
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
		
			$this->db->where('status <', 2);			
			$where = ' (reseller IS NULL OR reseller = "") ';
			$this->db->where($where , NULL, FALSE);			
			$this->db->where('pricelist', $this->session->userdata('username'));
			$query1 = $this->db->get('routes');
			$join1 = $this->db->last_query();
			
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
		
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
			$this->db->where('status <', 2);
			$this->db->where('reseller', $this->session->userdata('username'));
			//$query2 = $this->db->get("routes");
                         $this->db->from('routes');
                         $this->db->get();
			$join2 = $this->db->last_query();
			$query = $this->db->query($join1 .' UNION '.$join2);

			//$query = " SELECT * FROM routes WHERE status < 2 AND (reseller IS NULL OR reseller = '') AND pricelist = '".$this->session->userdata('username')."' ";
			//$query .= " UNION SELECT * FROM routes WHERE status < 2 AND reseller = '".$this->session->userdata('username')."' ";
			
			
		}
		else
		{
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
			$this->db->where('status <', 2);
			$where = ' (reseller IS NULL OR reseller = "") ';
			$this->db->where($where , NULL, FALSE);
			$query2 = $this->db->get('routes');
			$join = $this->db->last_query();
			$query = $this->db->query($join);
			//$query = " SELECT * FROM routes WHERE status < 2 AND ( reseller IS NULL OR reseller = '' ) ";
		}
		
		
		/*$this->db->where('status < ','2');
		if($username!=""){
		$this->db->where('reseller',$username);	
		}
		else{			 
		$this->db->where(array( 'reseller' => NULL ));	
		}
		$this->db->or_where('reseller', ''); 
		$this->db->from('routes');*/
		//$query1 = $this->db->query($query);
	    return $query->num_rows();
	}
	
	function getRoutesList($start, $limit)
	{

		if($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) 
		{
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
					
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
		
			$this->db->where('status <', 2);
			$where = ' (reseller IS NULL OR reseller = "") ';
			$this->db->where($where , NULL, FALSE);			
			$this->db->where('pricelist', $this->session->userdata('username'));
			$query1 = $this->db->get('routes');
			$join1 = $this->db->last_query();
			
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
				
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
				
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
			$this->db->where('status <', 2);
			$this->db->where('reseller', $this->session->userdata('username'));
			$query2 = $this->db->get('routes');
			$join2 = $this->db->last_query();
			$query = $this->db->query($join1.' UNION '.$join2. ' ORDER BY comment limit '.$start.', '.$limit.' ');
			//$query  = "SELECT * FROM routes WHERE ( reseller IS NULL OR reseller = '') AND pricelist ='".$this->session->userdata('username')."'  ";
			//$query .= "UNION SELECT * FROM routes WHERE status < 2  AND reseller = '".$this->session->userdata('username')."' ORDER BY comment limit $start, $limit ";			
		}
		else
		{
			if($this->session->userdata('advance_search')==1){
			
			$routes_search =  $this->session->userdata('routes_search');
			
			$pattern_operator = $routes_search['pattern_operator'];
			
			if(!empty($routes_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $routes_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $routes_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $routes_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $routes_search['pattern']);
					break;
				}
			}
			
			$comment_operator = $routes_search['comment_operator'];
			
			if(!empty($routes_search['comment'])) {
				switch($comment_operator){
					case "1":
					$this->db->like('comment', $routes_search['comment']); 
					break;
					case "2":
					$this->db->not_like('comment', $routes_search['comment']);
					break;
					case "3":
					$this->db->where('comment', $routes_search['comment']);
					break;
					case "4":
					$this->db->where('comment <>', $routes_search['comment']);
					break;
				}
			}
			
				
			$increment_operator = $routes_search['increment_operator'];
			if(!empty($routes_search['increment'])) {
				switch($increment_operator){
					case "1":
					$this->db->where('inc ', $routes_search['increment']);
					break;
					case "2":
					$this->db->where('inc <>', $routes_search['increment']);					
					break;
					case "3":
					$this->db->where('inc > ', $routes_search['increment']); 					
					break;
					case "4":
					$this->db->where('inc < ', $routes_search['increment']); 	
					break;
					case "5":
					$this->db->where('inc >= ', $routes_search['increment']);
					break;
					case "6":
					$this->db->where('inc <= ', $routes_search['increment']);
					break;
				}
			}	
			
			$connect_charge_operator = $routes_search['connect_charge_operator'];
			if(!empty($routes_search['connect_charge'])) {
				switch($connect_charge_operator){
					case "1":
					$this->db->where('connectcost ', $routes_search['connect_charge']);
					break;
					case "2":
					$this->db->where('connectcost <>', $routes_search['connect_charge']);					
					break;
					case "3":
					$this->db->where('connectcost > ', $routes_search['connect_charge']); 					
					break;
					case "4":
					$this->db->where('connectcost < ', $routes_search['connect_charge']); 	
					break;
					case "5":
					$this->db->where('connectcost >= ', $routes_search['connect_charge']);
					break;
					case "6":
					$this->db->where('connectcost <= ', $routes_search['connect_charge']);
					break;
				}
			}	
			
			$included_seconds_operator = $routes_search['included_seconds_operator'];
			if(!empty($routes_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $routes_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $routes_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $routes_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $routes_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $routes_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $routes_search['included_seconds']);
					break;
				}
			}	
			
			$cost_per_add_minutes_operator = $routes_search['cost_per_add_minutes_operator'];
			if(!empty($routes_search['cost_per_add_minutes'])) {
				switch($cost_per_add_minutes_operator){
					case "1":
					$this->db->where('cost ', $routes_search['cost_per_add_minutes']);
					break;
					case "2":
					$this->db->where('cost <>', $routes_search['cost_per_add_minutes']);					
					break;
					case "3":
					$this->db->where('cost > ', $routes_search['cost_per_add_minutes']); 					
					break;
					case "4":
					$this->db->where('cost < ', $routes_search['cost_per_add_minutes']); 	
					break;
					case "5":
					$this->db->where('cost >= ', $routes_search['cost_per_add_minutes']);
					break;
					case "6":
					$this->db->where('cost <= ', $routes_search['cost_per_add_minutes']);
					break;
				}
			}			
						
		}
			$this->db->where('status <', 2);
			$where = ' (reseller IS NULL OR reseller = "") ';
			$this->db->where($where , NULL, FALSE);
			$query2 = $this->db->get('routes');
			$join = $this->db->last_query();
			$query = $this->db->query($join. ' ORDER BY comment limit '.$start.' , '.$limit.' ');
			//$query = "SELECT * FROM routes WHERE ( reseller IS NULL OR reseller = '') AND status < 2 ORDER BY comment limit $start , $limit ";
		}
		//$query1 = $this->db->query($query);
		/*$this->db->where('status < ','2');
		if($username!=""){
		$this->db->where('reseller',$username);	
		}
		else{
		$this->db->where(array( 'reseller' => NULL ));	
		}
		$this->db->or_where('reseller', '');
		$this->db->limit($limit,$start);
	  	$this->db->from('routes');	
		$query = $this->db->get();	*/
		//echo $this->db->last_query();	
		return $query;
	}
	
	function getPackagesCount()
	{
		if($this->session->userdata('advance_search')==1){
			
			$packages_search =  $this->session->userdata('packages_search');
			
			$package_name_operator = $packages_search['package_name_operator'];
			
			if(!empty($packages_search['package_name'])) {
				switch($package_name_operator){
					case "1":
					$this->db->like('name', $packages_search['package_name']); 
					break;
					case "2":
					$this->db->not_like('name', $packages_search['package_name']);
					break;
					case "3":
					$this->db->where('name', $packages_search['package_name']);
					break;
					case "4":
					$this->db->where('name <>', $packages_search['package_name']);
					break;
				}
			}
			$this->db->where('pricelist', $packages_search['pricelist']);
			
			$pattern_operator = $packages_search['pattern_operator'];
			
			if(!empty($packages_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $packages_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $packages_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $packages_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $packages_search['pattern']);
					break;
				}
			}
			
			$included_seconds_operator = $packages_search['included_seconds_operator'];
			if(!empty($packages_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $packages_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $packages_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $packages_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $packages_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $packages_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $packages_search['included_seconds']);
					break;
				}
			}		
			
		}
		
		 if ( $this->session->userdata('logintype')== 2 ) {
			 // $sql_count ="SELECT * FROM packages WHERE status < 2 AND pricelist IN(SELECT name FROM pricelists WHERE reseller IS NULL) ORDER BY id";
			  //$sql_count ="SELECT * FROM packages WHERE status < 2  ORDER BY id"; //final
			  $this->db->where('status < ', 2);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
		 }
		 elseif ( $this->session->userdata('logintype') == 1 ) {
			  //$sql_count ="SELECT * FROM packages WHERE status < 2 AND pricelist IN(SELECT name FROM pricelists WHERE reseller = '". $this->session->userdata('username'). "') ORDER BY id";
			  $this->db->where('reseller', $this->session->userdata('username'));
			  $query1 = $this->db->get('pricelists');
			  $result = $query1->result_array();
			  $pricelist = array();			   
			  foreach($result as $row)
			  {
				$pricelist[] = $row['name'];
			  }
			  
			  $this->db->where('status < ', 2);
			  $this->db->where_in('pricelist',$pricelist);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
			  
		 }
		 $count = $this->db->count_all_results();
		 return $count;
	}
	
	function getPackagesList($start, $limit)
	{
		if($this->session->userdata('advance_search')==1){
			
			$packages_search =  $this->session->userdata('packages_search');
			
			$package_name_operator = $packages_search['package_name_operator'];
			
			if(!empty($packages_search['package_name'])) {
				switch($package_name_operator){
					case "1":
					$this->db->like('name', $packages_search['package_name']); 
					break;
					case "2":
					$this->db->not_like('name', $packages_search['package_name']);
					break;
					case "3":
					$this->db->where('name', $packages_search['package_name']);
					break;
					case "4":
					$this->db->where('name <>', $packages_search['package_name']);
					break;
				}
			}
			$this->db->where('pricelist', $packages_search['pricelist']);
			
			$pattern_operator = $packages_search['pattern_operator'];
			
			if(!empty($packages_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $packages_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $packages_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $packages_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $packages_search['pattern']);
					break;
				}
			}
			
			$included_seconds_operator = $packages_search['included_seconds_operator'];
			if(!empty($packages_search['included_seconds'])) {
				switch($included_seconds_operator){
					case "1":
					$this->db->where('includedseconds ', $packages_search['included_seconds']);
					break;
					case "2":
					$this->db->where('includedseconds <>', $packages_search['included_seconds']);					
					break;
					case "3":
					$this->db->where('includedseconds > ', $packages_search['included_seconds']); 					
					break;
					case "4":
					$this->db->where('includedseconds < ', $packages_search['included_seconds']); 	
					break;
					case "5":
					$this->db->where('includedseconds >= ', $packages_search['included_seconds']);
					break;
					case "6":
					$this->db->where('includedseconds <= ', $packages_search['included_seconds']);
					break;
				}
			}		
			
		}
		
		 if ( $this->session->userdata('logintype')== 2 ) 
		 {
			// $sql_select ="SELECT * FROM packages WHERE status < 2 AND pricelist IN(SELECT name FROM pricelists WHERE reseller IS NULL) ORDER BY id limit $start, $limit";
			 //$sql_select ="SELECT * FROM packages WHERE status < 2  ORDER BY id limit $start, $limit";
			  $this->db->where('status < ', 2);
			  $this->db->limit($limit,$start);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
			  
		 }
		 elseif ( $this->session->userdata('logintype') == 1 ) 
		 {
			 // $sql_select ="SELECT * FROM packages WHERE status < 2 AND pricelist IN(SELECT name FROM pricelists WHERE reseller = '". $this->session->userdata('username'). "') ORDER BY id limit $start, $limit";
			  $this->db->where('reseller', $this->session->userdata('username'));
			  $query1 = $this->db->get('pricelists');
			  $result = $query1->result_array();
			  $pricelist = array();			   
			  foreach($result as $row)
			  {
				$pricelist[] = $row['name'];
			  }
			  
			  $this->db->where('status < ', 2);
			  $this->db->where_in('pricelist',$pricelist);
			   $this->db->limit($limit,$start);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
		 }
		 $query = $this->db->get();		 
		 //$query = $this->db->query($sql_select);
		 return $query;		 
	}
	
	function getPackages()
	{
		 if ( $this->session->userdata('logintype')== 2 ) 
		 {
			  $this->db->where('status < ', 2);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
		 }
		 elseif ( $this->session->userdata('logintype') == 1 ) 
		 {
			   $this->db->where('reseller', $this->session->userdata('username'));
			  $query1 = $this->db->get('pricelists');
			  $result = $query1->result_array();
			  $pricelist = array();			   
			  foreach($result as $row)
			  {
				$pricelist[] = $row['name'];
			  }
			  
			  $this->db->where('status < ', 2);
			  $this->db->where_in('pricelist',$pricelist);
			  $this->db->order_by('id','ASC');
			  $this->db->from('packages');
		 }
		  $query = $this->db->get();	
		
		 //$query = $this->db->query($sql_select);
		 return $query;	
		
	}
	
	function getRouteByPricelistsCount($pricelist)
	{
	    $this->db->where('pricelist = ',"$pricelist");
	    $this->db->from('routes');
	    $routecnt = $this->db->count_all_results();
	    return $routecnt;
	}
	
	
	
				
}
?>