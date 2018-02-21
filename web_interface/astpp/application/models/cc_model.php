<?php
class Cc_model extends CI_Model 
{
    function Cc_model()
    {     
        parent::__construct();      
    }

	function add_callingcard($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Add Cards";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function add_brand($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "CC Brands";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function refill_card($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Refill Card";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_card($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Delete Card";
		$data['action'] = "Delete";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function reset_card($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Reset InUse";
		$data['action'] = "Reset";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function update_status_card($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Update Card(s) Status";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}			
	
	function get_brand_by_name($name)
	{
		$this->db->where("name",$name);
		$query = $this->db->get("callingcardbrands");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	function get_card_by_number($number)
	{
		$this->db->where("cardnumber",$number);
		$query = $this->db->get("callingcards");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	function get_callingcard_cdrs($card_number=false)
	{
		if($card_number != false)
		$this->db->where("cardnumber",$card_number);
		$query = $this->db->get("callingcardcdrs");

		if($query->num_rows() > 0)
		return $query->result_array();
		else 
		return false;
	}
	
	function get_cc_brands()
	{ 
		if($this->session->userdata('logintype')==1 || $this->session->userdata('logintype')== 5){
			
			 //$this->db->where("status >","2");
			 $this->db->where("status","1");
			 $this->db->where("reseller",$this->session->userdata('username'));
			 $this->db->order_by("name","ASC");
			 $query = $this->db->get("callingcardbrands");			 
		}
		else
		{
			$where = ' (reseller IS NULL OR reseller = "") ';
			//SELECT name FROM callingcardbrands WHERE status = 1 AND (reseller IS NULL OR reseller = '')
			//$this->db->where("status <","2");
			$this->db->where("status","1");
			$this->db->where($where , NULL, FALSE);
			$this->db->order_by("name","ASC");
			$query = $this->db->get("callingcardbrands");			
		}
		
		$brands = array();
		foreach ($query->result() as $row)
		{
			$brands[$row->name] = $row->name; 
		}
		
		return $brands;
	}
	
	function account_process_payment($data)
	{
		$this->load->library("curl");	
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Process Payment";		
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function edit_brand($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "CC Brands";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_brand($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "CC Brands";
		$data['action'] = "Delete...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function getUserCountCC()
	{
		$sql =$this->db->query("SELECT cardnumber FROM callingcards WHERE (account = '".$this->session->userdata('username')."'  OR account IN (SELECT cc FROM accounts where number='".$this->session->userdata('username')."' )) AND status < 2  ");
		if($sql->num_rows()==0)
		return 0;
		else 
		return $sql->num_rows();
	}
	
	function getUserCCList($start, $limit)
	{
		$query = $this->db->query("SELECT * FROM callingcards WHERE ( account = '" . $this->session->userdata('username')."' OR account IN (SELECT cc FROM accounts where number='".$this->session->userdata('username')."' )) AND status < 2 ORDER BY id limit $start , $limit");
		return $query;
	}
	
	function getUserCountViewCard()
	{
		$query = $this->db->query("SELECT * FROM callingcards WHERE cardnumber = '" .$this->session->userdata('username') . "' AND ( account = '".$this->session->userdata('username')."' OR account IN (SELECT cc FROM accounts where number='".$this->session->userdata('username')."'))");
		
		if($query->num_rows()>0){
			$cardinfo = $query->row_array();
					
			$this->db->where('cardnumber',$cardinfo['cardnumber']);
			$this->db->from('callingcardcdrs');
			
			return  $this->db->count_all_results();
		}
		else{  
			return 0;
		}
	}
	
	function getUserViewCardList($start, $limit)
	{
		$query = $this->db->query("SELECT * FROM callingcards WHERE cardnumber = '" .$this->session->userdata('username') . "' AND ( account = '".$this->session->userdata('username')."' OR account IN (SELECT cc FROM accounts where number='".$this->session->userdata('username')."'))");
		
		
		$cardinfo = $query->row_array();		
		$this->db->where('cardnumber',@$cardinfo['cardnumber']);
		$this->db->limit($limit,$start);
		$this->db->from('callingcardcdrs');	
		$query2 = $this->db->get();	
		//echo $this->db->last_query();		
		return $query2;			
		
	}
	
	
	function getcdrs($start, $limit,$flag=true)
	{
		if($this->session->userdata('advance_search')==1){
			
			$brands_cdrs_search =  $this->session->userdata('brands_cdrs_search');
			
			$card_number_operator = $brands_cdrs_search['card_number_operator'];
			
			if(!empty($brands_cdrs_search['card_number'])) {
				switch($card_number_operator){
					case "1":
					$this->db->like('cardnumber', $brands_cdrs_search['card_number']); 
					break;
					case "2":
					$this->db->not_like('cardnumber', $brands_cdrs_search['card_number']);
					break;
					case "3":
					$this->db->where('cardnumber', $brands_cdrs_search['card_number']);
					break;
					case "4":
					$this->db->where('cardnumber <>', $brands_cdrs_search['card_number']);
					break;
				}
			}	
			
			if(!empty($brands_cdrs_search['start_date'])) {
				$this->db->where('callstart >= ', $brands_cdrs_search['start_date'].':00');
			}
			if(!empty($brands_cdrs_search['end_date'])) {
				$this->db->where('callstart <= ', $brands_cdrs_search['end_date'].':59');
			}
			
			
			$caller_id_operator = $brands_cdrs_search['caller_id_operator'];
			
			if(!empty($brands_cdrs_search['caller_id'])) {
				switch($caller_id_operator){
					case "1":
					$this->db->like('clid', $brands_cdrs_search['caller_id']); 
					break;
					case "2":
					$this->db->not_like('clid', $brands_cdrs_search['caller_id']);
					break;
					case "3":
					$this->db->where('clid', $brands_cdrs_search['caller_id']);
					break;
					case "4":
					$this->db->where('clid <>', $brands_cdrs_search['caller_id']);
					break;
				}
			}
			
			$dest_operator = $brands_cdrs_search['dest_operator'];
			
			if(!empty($brands_cdrs_search['dest'])) {
				switch($dest_operator){
					case "1":
					$this->db->like('destination', $brands_cdrs_search['dest']); 
					break;
					case "2":
					$this->db->not_like('destination', $brands_cdrs_search['dest']);
					break;
					case "3":
					$this->db->where('destination', $brands_cdrs_search['dest']);
					break;
					case "4":
					$this->db->where('destination <>', $brands_cdrs_search['dest']);
					break;
				}
			}
			
			$bill_sec_operator = $brands_cdrs_search['bill_sec_operator'];
			if(!empty($brands_cdrs_search['bill_sec'])) {
				switch($bill_sec_operator){
					case "1":
					$this->db->where('seconds ', $brands_cdrs_search['bill_sec']);
					break;
					case "2":
					$this->db->where('seconds <>', $brands_cdrs_search['bill_sec']);					
					break;
					case "3":
					$this->db->where('seconds > ', $brands_cdrs_search['bill_sec']); 					
					break;
					case "4":
					$this->db->where('seconds < ', $brands_cdrs_search['bill_sec']); 	
					break;
					case "5":
					$this->db->where('seconds >= ', $brands_cdrs_search['bill_sec']);
					break;
					case "6":
					$this->db->where('seconds <= ', $brands_cdrs_search['bill_sec']);
					break;
				}
			}	
			
			if($brands_cdrs_search['disposition']!='')
			  $this->db->where('disposition', $brands_cdrs_search['disposition']);
			
			$debit_operator = $brands_cdrs_search['debit_operator'];
			if(!empty($brands_cdrs_search['debit'])) {
				switch($debit_operator){
					case "1":
					$this->db->where('debit ', $brands_cdrs_search['debit']);
					break;
					case "2":
					$this->db->where('debit <>', $brands_cdrs_search['debit']);					
					break;
					case "3":
					$this->db->where('debit > ', $brands_cdrs_search['debit']); 					
					break;
					case "4":
					$this->db->where('debit < ', $brands_cdrs_search['debit']); 	
					break;
					case "5":
					$this->db->where('debit >= ', $brands_cdrs_search['debit']);
					break;
					case "6":
					$this->db->where('debit <= ', $brands_cdrs_search['debit']);
					break;
				}
			}	
			
// 			$credit_operator = $brands_cdrs_search['credit_operator'];
// 			if(!empty($brands_cdrs_search['credit'])) {
// 				switch($credit_operator){
// 					case "1":
// 					$this->db->where('credit ', $brands_cdrs_search['credit']);
// 					break;
// 					case "2":
// 					$this->db->where('credit <>', $brands_cdrs_search['credit']);					
// 					break;
// 					case "3":
// 					$this->db->where('credit > ', $brands_cdrs_search['credit']); 					
// 					break;
// 					case "4":
// 					$this->db->where('credit < ', $brands_cdrs_search['credit']); 	
// 					break;
// 					case "5":
// 					$this->db->where('credit >= ', $brands_cdrs_search['credit']);
// 					break;
// 					case "6":
// 					$this->db->where('credit <= ', $brands_cdrs_search['credit']);
// 					break;
// 				}
// 			}	
			
			if($brands_cdrs_search['pricelist']!='')
			{
			  $this->db->where('pricelist', $brands_cdrs_search['pricelist']);
			}
			
			$pattern_operator = $brands_cdrs_search['pattern_operator'];
			
			if(!empty($brands_cdrs_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $brands_cdrs_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $brands_cdrs_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $brands_cdrs_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $brands_cdrs_search['pattern']);
					break;
				}
			}		
			
		}
		if($this->session->userdata['logintype']=='2')
		{
		    $this->db->where('reseller', "");
		}else{
		    $this->db->where('reseller', $this->session->userdata['accountinfo']['number']);
		}
		if($flag)
		  $this->db->limit($limit,$start);
		$this->db->from('callingcard_cdrs');
		$this->db->order_by("callstart desc"); 
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	function  getcdrsCount()
	{
		if($this->session->userdata('advance_search')==1){
			
			$brands_cdrs_search =  $this->session->userdata('brands_cdrs_search');
			
			$card_number_operator = $brands_cdrs_search['card_number_operator'];
			
			if(!empty($brands_cdrs_search['card_number'])) {
				switch($card_number_operator){
					case "1":
					$this->db->like('cardnumber', $brands_cdrs_search['card_number']); 
					break;
					case "2":
					$this->db->not_like('cardnumber', $brands_cdrs_search['card_number']);
					break;
					case "3":
					$this->db->where('cardnumber', $brands_cdrs_search['card_number']);
					break;
					case "4":
					$this->db->where('cardnumber <>', $brands_cdrs_search['card_number']);
					break;
				}
			}	
			
			if(!empty($brands_cdrs_search['start_date'])) {
				$this->db->where('callstart >= ', $brands_cdrs_search['start_date'].':00');
			}
			if(!empty($brands_cdrs_search['end_date'])) {
				$this->db->where('callstart <= ', $brands_cdrs_search['end_date'].':00');
			}
			
			
			$caller_id_operator = $brands_cdrs_search['caller_id_operator'];
			
			if(!empty($brands_cdrs_search['caller_id'])) {
				switch($caller_id_operator){
					case "1":
					$this->db->like('clid', $brands_cdrs_search['caller_id']); 
					break;
					case "2":
					$this->db->not_like('clid', $brands_cdrs_search['caller_id']);
					break;
					case "3":
					$this->db->where('clid', $brands_cdrs_search['caller_id']);
					break;
					case "4":
					$this->db->where('clid <>', $brands_cdrs_search['caller_id']);
					break;
				}
			}
			
			$dest_operator = $brands_cdrs_search['dest_operator'];
			
			if(!empty($brands_cdrs_search['dest'])) {
				switch($dest_operator){
					case "1":
					$this->db->like('destination', $brands_cdrs_search['dest']); 
					break;
					case "2":
					$this->db->not_like('destination', $brands_cdrs_search['dest']);
					break;
					case "3":
					$this->db->where('destination', $brands_cdrs_search['dest']);
					break;
					case "4":
					$this->db->where('destination <>', $brands_cdrs_search['dest']);
					break;
				}
			}
			
			$bill_sec_operator = $brands_cdrs_search['bill_sec_operator'];
			if(!empty($brands_cdrs_search['bill_sec'])) {
				switch($bill_sec_operator){
					case "1":
					$this->db->where('seconds ', $brands_cdrs_search['bill_sec']);
					break;
					case "2":
					$this->db->where('seconds <>', $brands_cdrs_search['bill_sec']);					
					break;
					case "3":
					$this->db->where('seconds > ', $brands_cdrs_search['bill_sec']); 					
					break;
					case "4":
					$this->db->where('seconds < ', $brands_cdrs_search['bill_sec']); 	
					break;
					case "5":
					$this->db->where('seconds >= ', $brands_cdrs_search['bill_sec']);
					break;
					case "6":
					$this->db->where('seconds <= ', $brands_cdrs_search['bill_sec']);
					break;
				}
			}	
			
			if($brands_cdrs_search['disposition']!='')
			  $this->db->where('disposition', $brands_cdrs_search['disposition']);
			
			$debit_operator = $brands_cdrs_search['debit_operator'];
			if(!empty($brands_cdrs_search['debit'])) {
				switch($debit_operator){
					case "1":
					$this->db->where('debit ', $brands_cdrs_search['debit']);
					break;
					case "2":
					$this->db->where('debit <>', $brands_cdrs_search['debit']);					
					break;
					case "3":
					$this->db->where('debit > ', $brands_cdrs_search['debit']); 					
					break;
					case "4":
					$this->db->where('debit < ', $brands_cdrs_search['debit']); 	
					break;
					case "5":
					$this->db->where('debit >= ', $brands_cdrs_search['debit']);
					break;
					case "6":
					$this->db->where('debit <= ', $brands_cdrs_search['debit']);
					break;
				}
			}	
			
// 			$credit_operator = $brands_cdrs_search['credit_operator'];
// 			if(!empty($brands_cdrs_search['credit'])) {
// 				switch($credit_operator){
// 					case "1":
// 					$this->db->where('credit ', $brands_cdrs_search['credit']);
// 					break;
// 					case "2":
// 					$this->db->where('credit <>', $brands_cdrs_search['credit']);					
// 					break;
// 					case "3":
// 					$this->db->where('credit > ', $brands_cdrs_search['credit']); 					
// 					break;
// 					case "4":
// 					$this->db->where('credit < ', $brands_cdrs_search['credit']); 	
// 					break;
// 					case "5":
// 					$this->db->where('credit >= ', $brands_cdrs_search['credit']);
// 					break;
// 					case "6":
// 					$this->db->where('credit <= ', $brands_cdrs_search['credit']);
// 					break;
// 				}
// 			}
			
			if($brands_cdrs_search['pricelist']!='')
			{
			  $this->db->where('pricelist', $brands_cdrs_search['pricelist']);
			}
			
			$pattern_operator = $brands_cdrs_search['pattern_operator'];
			
			if(!empty($brands_cdrs_search['pattern'])) {
				switch($pattern_operator){
					case "1":
					$this->db->like('pattern', $brands_cdrs_search['pattern']); 
					break;
					case "2":
					$this->db->not_like('pattern', $brands_cdrs_search['pattern']);
					break;
					case "3":
					$this->db->where('pattern', $brands_cdrs_search['pattern']);
					break;
					case "4":
					$this->db->where('pattern <>', $brands_cdrs_search['pattern']);
					break;
				}
			}
					
			
		}
		if($this->session->userdata['logintype']=='2')
		{
		    $this->db->where('reseller', "");
		}else{
		    $this->db->where('reseller', $this->session->userdata['accountinfo']['number']);
		}
		$this->db->from('callingcard_cdrs');
		$cnt = $this->db->count_all_results();
		return $cnt;
	}
	
	function getCCCount($brands)
	{
		if($this->session->userdata('advance_search')==1){
						
			$cards_search =  $this->session->userdata('cards_search');
			
			if(!empty($cards_search['account_nummber'])) {
				$this->db->where('account', $cards_search['account_nummber']);
			}
			
			$card_number_operator = $cards_search['card_number_operator'];
			
			if(!empty($cards_search['card_number'])) {
				switch($card_number_operator){
					case "1":
					$this->db->like('cardnumber', $cards_search['card_number']); 
					break;
					case "2":
					$this->db->not_like('cardnumber', $cards_search['card_number']);
					break;
					case "3":
					$this->db->where('cardnumber', $cards_search['card_number']);
					break;
					case "4":
					$this->db->where('cardnumber <>', $cards_search['card_number']);
					break;
				}
			}
			
			if(!empty($cards_search['brand'])) {
				$this->db->where('brand', $cards_search['brand']);
			}
			
			$balance_operator = $cards_search['balance_operator'];
			if(!empty($cards_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('value ', $cards_search['balance']);
					break;
					case "2":
					$this->db->where('value <>', $cards_search['balance']);					
					break;
					case "3":
					$this->db->where('value > ', $cards_search['balance']); 					
					break;
					case "4":
					$this->db->where('value < ', $cards_search['balance']); 	
					break;
					case "5":
					$this->db->where('value >= ', $cards_search['balance']);
					break;
					case "6":
					$this->db->where('value <= ', $cards_search['balance']);
					break;
				}
			}	
			$balance_used_operator = $cards_search['balance_used_operator'];
			if(!empty($cards_search['balance_used'])) {
				switch($balance_used_operator){
					case "1":
					$this->db->where('used ', $cards_search['balance_used']);
					break;
					case "2":
					$this->db->where('used <>', $cards_search['balance_used']);					
					break;
					case "3":
					$this->db->where('used > ', $cards_search['balance_used']); 					
					break;
					case "4":
					$this->db->where('used < ', $cards_search['balance_used']); 	
					break;
					case "5":
					$this->db->where('used >= ', $cards_search['balance_used']);
					break;
					case "6":
					$this->db->where('used <= ', $cards_search['balance_used']);
					break;
				}
			}
			if(!empty($cards_search['creation_start_date'])) {
				$this->db->where('created >= ', $cards_search['creation_start_date'].':00');
			}
			if(!empty($cards_search['creation_end_date'])) {
				$this->db->where('created <= ', $cards_search['creation_end_date'].':59');
			}
			
			if(!empty($cards_search['first_used_start_date'])) {
				$this->db->where('firstused >= ', $cards_search['first_used_start_date'].':00');
			}
			
			if(!empty($cards_search['first_used_end_date'])) {
				$this->db->where('firstused <= ', $cards_search['first_used_end_date'].':59');
			}
			$this->db->where('inuse', $cards_search['inuse']);
			
		}
		
		if(count($brands)==0){
			$brands = array('');
		}
		$this->db->where('status <', '2');
		$this->db->where_in('brand',$brands);
		$this->db->from('callingcards');
		$cnt = $this->db->count_all_results();
		return $cnt;
	}
	
	function getCCList($brandsql, $start,$limit)
	{
		if($this->session->userdata('advance_search')==1){
						
			$cards_search =  $this->session->userdata('cards_search');
			
			if(!empty($cards_search['account_nummber'])) {
				$this->db->where('account', $cards_search['account_nummber']);
			}
			
			$card_nummber_operator = $cards_search['card_number_operator'];
			
			if(!empty($cards_search['card_number'])) {
				switch($card_nummber_operator){
					case "1":
					$this->db->like('cardnumber', $cards_search['card_number']); 
					break;
					case "2":
					$this->db->not_like('cardnumber', $cards_search['card_number']);
					break;
					case "3":
					$this->db->where('cardnumber', $cards_search['card_number']);
					break;
					case "4":
					$this->db->where('cardnumber <>', $cards_search['card_number']);
					break;
				}
			}
			
			if(!empty($cards_search['brand'])) {
				$this->db->where('brand', $cards_search['brand']);
			}
			
			$balance_operator = $cards_search['balance_operator'];
			if(!empty($cards_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('value ', $cards_search['balance']);
					break;
					case "2":
					$this->db->where('value <>', $cards_search['balance']);					
					break;
					case "3":
					$this->db->where('value > ', $cards_search['balance']); 					
					break;
					case "4":
					$this->db->where('value < ', $cards_search['balance']); 	
					break;
					case "5":
					$this->db->where('value >= ', $cards_search['balance']);
					break;
					case "6":
					$this->db->where('value <= ', $cards_search['balance']);
					break;
				}
			}	
			$balance_used_operator = $cards_search['balance_used_operator'];
			if(!empty($cards_search['balance_used'])) {
				switch($balance_used_operator){
					case "1":
					$this->db->where('used ', $cards_search['balance_used']);
					break;
					case "2":
					$this->db->where('used <>', $cards_search['balance_used']);					
					break;
					case "3":
					$this->db->where('used > ', $cards_search['balance_used']); 					
					break;
					case "4":
					$this->db->where('used < ', $cards_search['balance_used']); 	
					break;
					case "5":
					$this->db->where('used >= ', $cards_search['balance_used']);
					break;
					case "6":
					$this->db->where('used <= ', $cards_search['balance_used']);
					break;
				}
			}
			if(!empty($cards_search['creation_start_date'])) {
				$this->db->where('created >= ', $cards_search['creation_start_date'].':00');
			}
			if(!empty($cards_search['creation_end_date'])) {
				$this->db->where('created <= ', $cards_search['creation_end_date'].':59');
			}
			
			if(!empty($cards_search['first_used_start_date'])) {
				$this->db->where('firstused >= ', $cards_search['first_used_start_date'].':00');
			}
			
			if(!empty($cards_search['first_used_end_date'])) {
				$this->db->where('firstused <= ', $cards_search['first_used_end_date'].':59');
			}
			$this->db->where('inuse', $cards_search['inuse']);
			
		}
		
		if(count($brandsql)==0){
			$brandsql = array('');
		}
		$this->db->where('status < ', '2');
		$this->db->where_in('brand' , $brandsql);
		$this->db->limit($limit,$start);
		$this->db->from('callingcards');	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;		
	}
	
	function getCCBrands($start,$limit)
	{
		
		if($this->session->userdata('advance_search')==1){
			
			$brand_search =  $this->session->userdata('callingcards_brand_search');
			
			$cc_brand_operator = $brand_search['cc_brand_operator'];
			
			if(!empty($brand_search['cc_brand'])) {
				switch($cc_brand_operator){
					case "1":
					$this->db->like('name', $brand_search['cc_brand']); 
					break;
					case "2":
					$this->db->not_like('name', $brand_search['cc_brand']);
					break;
					case "3":
					$this->db->where('name', $brand_search['cc_brand']);
					break;
					case "4":
					$this->db->where('name <>', $brand_search['cc_brand']);
					break;
				}
			}
			
			$this->db->where('pricelist', $brand_search['pricelist']);
			
			$days_validate_for_operator = $brand_search['days_validate_for_operator'];
			if(!empty($brand_search['days_validate_for'])) {
				switch($days_validate_for_operator){
					case "1":
					$this->db->like('validfordays', $brand_search['days_validate_for']); 
					break;
					case "2":
					$this->db->not_like('validfordays', $brand_search['days_validate_for']);
					break;
					case "3":
					$this->db->where('validfordays', $brand_search['days_validate_for']);
					break;
					case "4":
					$this->db->where('validfordays <>', $brand_search['days_validate_for']);
					break;
				}
			}
			
			$this->db->where('status', $brand_search['status']);			
		}
		
		if($this->session->userdata('logintype')==1 || $this->session->userdata('logintype')==5){
			$this->db->where('status < ', '2');
			$this->db->where('reseller', $this->session->userdata('username'));
			$this->db->order_by("name", "ASC"); 
			$this->db->limit($limit,$start);
			$this->db->from('callingcardbrands');	
			// $tmp ="SELECT * FROM callingcardbrands WHERE status < 2 AND reseller = '".$this->session->userdata('username'). "' ORDER BY name limit $start , $limit";
		}
		else{
			$this->db->where('status < ', '2');			
			$where = ' (reseller IS NULL OR reseller = "") ';
			$this->db->where($where , NULL, FALSE);			
			$this->db->order_by("name", "ASC"); 
			$this->db->limit($limit,$start);
			$this->db->from('callingcardbrands');	
			 //$tmp ="SELECT * FROM callingcardbrands WHERE status < 2 AND (reseller IS NULL OR reseller = '') ORDER BY name limit $start , $limit";
		}	   
		//$query = $this->db->query($tmp);
		$query =  $this->db->get();
		return $query;
	}
	
	function list_cc_brands()
	{		
		$item_arr = array();
		$q = "SELECT name FROM callingcardbrands WHERE status < 2 AND (reseller IS NULL OR reseller = '')";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['name'];
			}
		}
		return $item_arr;
	}	
		
	function list_cc_brands_reseller($reseller)
	{		
		$item_arr = array();
		$q = "SELECT name FROM callingcardbrands WHERE status < 2 AND reseller='".$this->db->escape_str($reseller)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['name'];
			}
		}
		return $item_arr;
	}	
	
	function list_cc_brands_select($reseller,$default)
	{
		$ret_html = '';
		if($reseller == '')
			$providers = $this->list_cc_brands();
		else 
			$providers = $this->list_cc_brands_reseller($reseller);
			
		foreach ($providers as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}
		
		return  $ret_html;
	}
	
	function cc_add_callerid($data)
        {
                $this->load->library("curl");	
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Add CC CallerID";		
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$this->curl->sendRequestToPerlScript($url,$data);
            
        }
        function cc_get_callerid($card_number)
        {
            $this->db->where("cardnumber ",$card_number);
            $this->db->from("callingcards_callerid");
            $query = $this->db->get();
            return $query;
            
        }
}