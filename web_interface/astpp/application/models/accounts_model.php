<?php
class Accounts_model extends CI_Model 
{
    function Accounts_model()
    {     
        parent::__construct();
    }
	
	function getAccount_Count($account, $company,$fname,$lname,$accounttype)
	{
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$reseller = $this->session->userdata('username');
		}
		else {
			$reseller = "";
		}
		
		if($this->session->userdata('advance_search')==1){
			
			$account_search =  $this->session->userdata('account_search');
		
			$account_number_operator = $account_search['account_number_operator'];
			
			if(!empty($account_search['account_number'])) {
				switch($account_number_operator){
					case "1":
					$this->db->like('number', $account_search['account_number']); 
					break;
					case "2":
					$this->db->not_like('number', $account_search['account_number']);
					break;
					case "3":
					$this->db->where('number', $account_search['account_number']);
					break;
					case "4":
					$this->db->where('number <>', $account_search['account_number']);
					break;
				}
			}
			
			if(!empty($account_search['pricelist'])) {	
			$this->db->where('pricelist', $account_search['pricelist']);
			}
			
			$first_name_operator = $account_search['first_name_operator'];
			if(!empty($account_search['first_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('first_name', $account_search['first_name']); 
					break;
					case "2":
					$this->db->not_like('first_name', $account_search['first_name']);
					break;
					case "3":
					$this->db->where('first_name', $account_search['first_name']);
					break;
					case "4":
					$this->db->where('first_name <>', $account_search['first_name']);
					break;
				}
			}
			
			$last_name_operator = $account_search['last_name_operator'];
			if(!empty($account_search['last_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('last_name', $account_search['last_name']); 
					break;
					case "2":
					$this->db->not_like('last_name', $account_search['last_name']);
					break;
					case "3":
					$this->db->where('last_name', $account_search['last_name']);
					break;
					case "4":
					$this->db->where('last_name <>', $account_search['last_name']);
					break;
				}
			}
			
			$company_operator = $account_search['company_operator'];
			if(!empty($account_search['company'])) {
				switch($company_operator){
					case "1":
					$this->db->like('company_name', $account_search['company']); 
					break;
					case "2":
					$this->db->not_like('company_name', $account_search['company']);
					break;
					case "3":
					$this->db->where('company_name', $account_search['company']);
					break;
					case "4":
					$this->db->where('company_name <>', $account_search['company']);
					break;
				}
			}
			
			$balance_operator = $account_search['balance_operator'];
			if(!empty($account_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('balance ', $account_search['balance']);
					break;
					case "2":
					$this->db->where('balance <>', $account_search['balance']);					
					break;
					case "3":
					$this->db->where('balance > ', $account_search['balance']); 					
					break;
					case "4":
					$this->db->where('balance < ', $account_search['balance']); 	
					break;
					case "5":
					$this->db->where('balance >= ', $account_search['balance']);
					break;
					case "6":
					$this->db->where('balance <= ', $account_search['balance']);
					break;
				}
			}
			
			if(!empty($account_search['sweep'])) {
			$this->db->where('sweep', $account_search['sweep']);
			}
			if(!empty($account_search['posttoexternal'])) {
			$this->db->where('posttoexternal', $account_search['posttoexternal']);
			}
			
			$accounttype = $account_search['accounttype'];
			
			if(!empty($account_search['country'])) {
			$this->db->where('country', $account_search['country']);
			}
			if(!empty($account_search['currency'])) {
			$this->db->where('currency', $account_search['currency']);			
			}
			
		}
		else{
			if($account!='NULL' && $account!=NULL)
			{
				$this->db->where('number', $account);
			}
			if($company!='NULL' && $company!=NULL)
			{
				$this->db->where('company_name', $company);
			}
			
			if($fname!='NULL' && $fname!=NULL)
			{
				$this->db->where('first_name', $fname);
			}
			
			if($lname!='NULL' && $lname!=NULL)
			{
				$this->db->where('last_name', $lname);
			}
		}
		//echo $account_search['account_number'];
				
		if(!isset($accounttype)){
			$accounttype = -1;
		}
		
		if($accounttype!='NULL' && $accounttype!=NULL)
		{
			if ( $accounttype == -1 ) {
// 				$this->db->where('status < ', 2);
				$this->db->where('reseller', $reseller);
			 }
			elseif ( $accounttype == 0 || !$accounttype ) {
// 				$this->db->where('status < ', 2);
				$this->db->where('type', 0);
				$this->db->where('reseller', $reseller);
			}
			elseif ( $accounttype > 0 ) {
// 				$this->db->where('status < ',2);
				$this->db->where('type', $accounttype); 
				$this->db->where('reseller', $reseller);     
				
			}
		}
		
		//$this->db->where('status < ', '2');
		//$this->db->where('status', '1');
		$this->db->from('accounts');		
		$count_all =  $this->db->count_all_results();
		//echo $this->db->last_query();
		return $count_all;
	}
	
	function getAccount_list($start, $limit, $account, $company,$fname,$lname,$accounttype) 
	{
		 if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$reseller = $this->session->userdata('username');
		}
		else {
			$reseller = "";
		}
		
		if($this->session->userdata('advance_search')==1){
			
			$account_search =  $this->session->userdata('account_search');
			
			$account_number_operator = $account_search['account_number_operator'];
			
			if(!empty($account_search['account_number'])) {
				switch($account_number_operator){
					case "1":
					$this->db->like('number', $account_search['account_number']); 
					break;
					case "2":
					$this->db->not_like('number', $account_search['account_number']);
					break;
					case "3":
					$this->db->where('number', $account_search['account_number']);
					break;
					case "4":
					$this->db->where('number <>', $account_search['account_number']);
					break;
				}
			}
			
			if(!empty($account_search['pricelist'])) {
			$this->db->where('pricelist', $account_search['pricelist']);
			}
			
			$first_name_operator = $account_search['first_name_operator'];
			if(!empty($account_search['first_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('first_name', $account_search['first_name']); 
					break;
					case "2":
					$this->db->not_like('first_name', $account_search['first_name']);
					break;
					case "3":
					$this->db->where('first_name', $account_search['first_name']);
					break;
					case "4":
					$this->db->where('first_name <>', $account_search['first_name']);
					break;
				}
			}
			
			$last_name_operator = $account_search['last_name_operator'];
			if(!empty($account_search['last_name'])) {
				switch($first_name_operator){
					case "1":
					$this->db->like('last_name', $account_search['last_name']); 
					break;
					case "2":
					$this->db->not_like('last_name', $account_search['last_name']);
					break;
					case "3":
					$this->db->where('last_name', $account_search['last_name']);
					break;
					case "4":
					$this->db->where('last_name <>', $account_search['last_name']);
					break;
				}
			}
			
			$company_operator = $account_search['company_operator'];
			if(!empty($account_search['company'])) {
				switch($company_operator){
					case "1":
					$this->db->like('company_name', $account_search['company']); 
					break;
					case "2":
					$this->db->not_like('company_name', $account_search['company']);
					break;
					case "3":
					$this->db->where('company_name', $account_search['company']);
					break;
					case "4":
					$this->db->where('company_name <>', $account_search['company']);
					break;
				}
			}
			
			$balance_operator = $account_search['balance_operator'];
			if(!empty($account_search['balance'])) {
				switch($balance_operator){
					case "1":
					$this->db->where('balance ', $account_search['balance']);
					break;
					case "2":
					$this->db->where('balance <>', $account_search['balance']);					
					break;
					case "3":
					$this->db->where('balance > ', $account_search['balance']); 					
					break;
					case "4":
					$this->db->where('balance < ', $account_search['balance']); 	
					break;
					case "5":
					$this->db->where('balance >= ', $account_search['balance']);
					break;
					case "6":
					$this->db->where('balance <= ', $account_search['balance']);
					break;
				}
			}
			
			$creditlimit_operator = $account_search['creditlimit_operator'];
			if(!empty($account_search['creditlimit'])) {
				switch($creditlimit_operator){
					case "1":
					$this->db->where('credit_limit ', $account_search['creditlimit']);
					break;
					case "2":
					$this->db->where('credit_limit <>', $account_search['creditlimit']);					
					break;
					case "3":
					$this->db->where('credit_limit > ', $account_search['creditlimit']); 					
					break;
					case "4":
					$this->db->where('credit_limit < ', $account_search['creditlimit']); 	
					break;
					case "5":
					$this->db->where('credit_limit >= ', $account_search['creditlimit']);
					break;
					case "6":
					$this->db->where('credit_limit <= ', $account_search['creditlimit']);
					break;
				}
			}
			
			if(!empty($account_search['sweep'])) {
			$this->db->where('sweep', $account_search['sweep']);
			}
			if(!empty($account_search['posttoexternal'])) {
			$this->db->where('posttoexternal', $account_search['posttoexternal']);
			}
			
			$accounttype = $account_search['accounttype'];
			if(!empty($account_search['country'])) {
			$this->db->where('country', $account_search['country']);
			}
			if(!empty($account_search['currency'])) {
			$this->db->where('currency', $account_search['currency']);			
			}
			
		}
		else{
			if($account!='NULL' && $account!=NULL)
			{
				$this->db->where('number', $account);
			}
			if($company!='NULL' && $company!=NULL)
			{
				$this->db->where('company_name', $company);
			}
			
			if($fname!='NULL' && $fname!=NULL)
			{
				$this->db->where('first_name', $fname);
			}
			
			if($lname!='NULL' && $lname!=NULL)
			{
				$this->db->where('last_name', $lname);
			}
		}
		
		if(!isset($accounttype)){
			$accounttype = -1;
		}		
		
		if($accounttype!='NULL' && $accounttype!=NULL)
		{
			if ( $accounttype == -1 ) {
// 				$this->db->where('status < ', 2);
				$this->db->where('reseller', $reseller);
			 }
			elseif ( $accounttype == 0 || !$accounttype ) {
// 				$this->db->where('status < ', 2);
				$this->db->where('type', 0);
				$this->db->where('reseller', $reseller);
			}
			elseif ( $accounttype > 0 ) {
// 				$this->db->where('status < ',2);
				$this->db->where('type', $accounttype); 
				$this->db->where('reseller', $reseller);   
				
			}
		}
		
	
	 //$this->db->where('status < ', '2');
	 //$this->db->where('status', '1');
	  $this->db->order_by("number", "desc"); 
	  $this->db->limit($limit,$start);
	  $this->db->from('accounts');	
	  $query = $this->db->get();	
	  //echo $this->db->last_query();
	  return $query;
  }
  
	function add_account($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		
		if($data['accounttype'] == 3)
		$data['mode'] = "Providers";
		else
		$data['mode'] = "Create Account";
		
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		 $this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function get_account_by_number($account_number)
	{
		$this->db->where("number",$account_number);
		$query = $this->db->get("accounts");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
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
	
	function edit_account($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Edit Account";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_account($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Remove Account";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}  
	
	
	function list_cdrs_count($account_number)
	{
		$this->db->where('cardnum', $account_number);
		//$this->db->where(array( 'status' => NULL ));
		//$status_arr = array(0,1,'');
		//$this->db->or_where_in('status', $status_arr);
		$where = "( status IS NULL OR status IN (0,1,''))";
		$this->db->where($where);
		$this->db->from('cdrs');
		$cdrscnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $cdrscnt;		
	}
	
	function list_cdrs($account_number, $start, $limit)
	{
		//"SELECT * FROM cdrs WHERE cardnum =". $astpp_db->quote( $accountinfo->{number} ). "and ( status IS NULL OR status IN (0,1,''))". " ORDER BY callstart DESC "
		$this->db->where('cardnum', $account_number);
		//$this->db->where(array( 'status' => NULL ));
		//$status_arr = array(0,1,'');
		$where = "( status IS NULL OR status IN (0,1,''))";
		$this->db->where($where);
		$this->db->order_by('callstart', 'DESC');
		$this->db->limit($limit,$start);
		$this->db->from('cdrs');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query;		
	}


	function invoice_list_internal($accountid)
	{
		$item_arr = array();
		if ($accountid) {
		$this->db->where('accountid', $accountid);	
		}
		$this->db->from('invoice_list_view');
		$query =$this->db->get();
		
		$ret_html = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$ret_html .= '<TR>';
				$ret_html .= '<TD>'.$row['invoiceid'].'</td>';
				$ret_html .= '<TD>'.$row['date'].'</td>';
				$ret_html .= '<TD>'.$row['value'].'</td>';
				$ret_html .= '<TD>View</td>';
				$ret_html .= '<TD>View</td>';
				$ret_html .= '</TR>';         				        				
			}
		}
		return $ret_html;
	}
	
	function list_applyable_charges()
	{
		$this->db->where('status < ', '2');
		$this->db->where('pricelist', '');
		$this->db->from('charges');
		$query = $this->db->get();
		$ret_html = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				//$item_arr[] = $row['name'];
				$ret_html .= '<TR>';
				$ret_html .= '<TD><a href="/accounts/charge_remove/'.$row['id'].'">remove</a></td>';
				$ret_html .= '<TD>'.$row['id'].'</td>';
				$ret_html .= '<TD>'.$row['description'].'</td>';
				$ret_html .= '<TD>'.$row['sweep'].'</td>';
				$ret_html .= '<TD>'.$row['charge'].'</td>';
	            $ret_html .= '</TR>';            				
			}
		}
		return $ret_html;
		
	}
	
	function list_chargelist_count()
	{
		$this->db->where('status < ', '2');
		$this->db->where('pricelist', '');
		$this->db->from('charges');
		$chargescnt = $this->db->count_all_results();
		return $chargescnt;
	}
	
	function list_chargelist($start, $limit)
	{
		$this->db->where('status < ', '2');
		$this->db->where('pricelist', '');
		$this->db->limit($limit,$start);
		$this->db->from('charges');
		$query = $this->db->get();
		return $query;
	}
	
	function list_dids_count($account){
		$this->db->where('status ', '1');
		$this->db->where('account',  $account);
		$this->db->from('dids');
		$didscnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $didscnt;		
		
	}
	
	function list_dids($account, $start, $limit){
		$this->db->where('status ', '1');
		$this->db->where('account',  $account);
		$this->db->limit($limit,$start);
		$this->db->from('dids');
		$query = $this->db->get();
		return $query;
	}
	
	function list_ani_count($account){
		$this->db->where('account',  $account);
		$this->db->from('ani_map');
		$didscnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $didscnt;
	}
	
	function list_ani($account, $start, $limit)
	{
		$this->db->where('account',  $account);
		$this->db->limit($limit,$start);
		$this->db->from('ani_map');
		$query = $this->db->get();
		return $query;
	}
	
	function list_ip_count($account){
		$this->db->where('account',  $account);
		$this->db->from('ip_map');
		$didscnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $didscnt;
	}
	
	function list_ip($account, $start, $limit)
	{
		$this->db->where('account',  $account);
		$this->db->limit($limit,$start);
		$this->db->from('ip_map');
		$query = $this->db->get();
		return $query;
	}
	
	
	function get_prefix_by_ip($ip)
	{
		$this->db->where('ip',  $ip);
		$this->db->from('ip_map');
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$row = $query->row_array();
			return $row['prefix'];
		}
		else 
		return "";
	}
	
	function list_invoice_count($accountid){
	if ($accountid) {
	$this->db->where('accountid', $accountid);	
	}
	$this->db->from('invoice_list_view');	
	$didscnt = $this->db->count_all_results();
	//echo $this->db->last_query();
	return $didscnt;
	}
	
	function list_invoice($accountid, $start, $limit)
	{
           if($this->session->userdata('advance_search')==1)
           {
                $invoice_search =  $this->session->userdata('user_invoice_search');
                $number_operator = $invoice_search['inumber_operator'];
                if(!empty($invoice_search['invoice_number'])) 
                {
                        switch($number_operator){
                                case "1":
                                $this->db->where('invoice_list_view.invoiceid', $invoice_search['invoice_number']);
                                break;
                                case "2":
                                $this->db->where('invoice_list_view.invoiceid <>', $invoice_search['invoice_number']);					
                                break;
                                case "3":
                                $this->db->where('invoice_list_view.invoiceid > ', $invoice_search['invoice_number']); 					
                                break;
                                case "4":
                                $this->db->where('invoice_list_view.invoiceid < ', $invoice_search['invoice_number']); 	
                                break;
                                case "5":
                                $this->db->where('invoice_list_view.invoiceid >= ', $invoice_search['invoice_number']);
                                break;
                                case "6":
                                $this->db->where('invoice_list_view.invoiceid <= ', $invoice_search['invoice_number']);
                                break;
                        }
                }
                
                if(!empty($invoice_search['invoice_date'])) 
                    {
                        $this->db->where('invoice_list_view.date ', $invoice_search['invoice_date']);
		    }
             
                $creditlimit_operator = $invoice_search['creditlimit_operator'];
                if(!empty($invoice_search['creditlimit'])) 
                {
                        switch($creditlimit_operator){
                                case "1":
                                $this->db->where('invoice_list_view.value', $invoice_search['creditlimit']);
                                break;
                                case "2":
                                $this->db->where('invoice_list_view.value <>', $invoice_search['creditlimit']);					
                                break;
                                case "3":
                                $this->db->where('invoice_list_view.value > ', $invoice_search['creditlimit']); 					
                                break;
                                case "4":
                                $this->db->where('invoice_list_view.value < ', $invoice_search['creditlimit']); 	
                                break;
                                case "5":
                                $this->db->where('invoice_list_view.value >= ', $invoice_search['creditlimit']);
                                break;
                                case "6":
                                $this->db->where('invoice_list_view.value <= ', $invoice_search['creditlimit']);
                                break;
                        }
                }

           }
 
		if ($accountid) {
		$this->db->where('accountid', $accountid);	
		}
	
		$this->db->limit($limit,$start);
		$this->db->from('invoice_list_view');
		$query = $this->db->get();
		return $query;
	}

	
	function add_account_details($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Details";
		$data['amount'] = $this->common_model->add_calculate_currency($data['amount'],'','',false,false);
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_ani_mapping($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Details";
		$data['action'] = "Remove ANI";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);
	}	
	
	
	function remove_charge($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Details";
		$data['action'] = "Remove Charge...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_dids($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Details";
		$data['action'] = "Remove DID";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function remove_ip_mapping($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Details";
		$data['action'] = "Remove IP";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$this->curl->sendRequestToPerlScript($url,$data);
	}
	
	function getCdrs_invoice($invoiceid)
	{
		$this->db->where('invoiceid', $invoiceid);
		$this->db->from('cdrs');
		$query = $this->db->get();
		return $query;
	}
	
	
	function select_account_type()
	{
		$index = 0;
		$ret_html = '';
		$default = '';
	
		 if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$typelist = array('0' => 'User','1' => 'Reseller','5' => 'CallShop'); //0-5
		 }
		 else{
			$typelist = array('User','Reseller','Administrator','Provider','Customer Service','CallShop'); //0-5	 
		 }
		foreach ($typelist as $key=> $elem)
		{
			$ret_html .= '<option value="'.$key.'"';
			if($elem == $default)
				$ret_html .= ' selected="selected"';
			$ret_html .= ">$elem</option>";
			
			$index = $index+1;
		}
		return $ret_html;		
	}
	
	function get_account($accountdata)
	{
		$q = "SELECT * FROM accounts WHERE number = '".$this->db->escape_str($accountdata)."' AND status = 1";
		$query = $this->db->query($q);		
		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		
		$q = "SELECT * FROM accounts WHERE cc = '".$this->db->escape_str($accountdata)."' AND status = 1";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}
		
		$q = "SELECT * FROM accounts WHERE accountid = '".$this->db->escape_str($accountdata)."' AND status = 1";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}

		return NULL;
	}	
	
	
	function get_account_including_closed($accountdata)
	{
		$q = "SELECT * FROM accounts WHERE number = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		
		$q = "SELECT * FROM accounts WHERE cc = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}
		
		$q = "SELECT * FROM accounts WHERE accountid = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}

		return NULL;
	}
	
	function getReseller($username="", $type)
	{
		$reseller = "";
		if($username!=""){
			$reseller = "reseller = '".$username. "' AND";
		}
		$q = "SELECT * FROM accounts WHERE  ".$reseller." type IN ('".$type."')";
		
		$query = $this->db->query($q);	
		
		$options = array();
        if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$options[] = $row['number'];
			}
		}
		return $options;  		  
	}
	
	function get_invoiceconf($reseller)
	{
	    $accountdata = $this->get_account_by_number($reseller);
	    if($accountdata['accountid']=='')
	      $accountdata['accountid'] = '-1';
	    $q = "SELECT * FROM invoice_conf where accountid='".$accountdata['accountid']."'";	    
	    $query = $this->db->query($q);
	    $row = $query->row_array();
	    return $row;	
	}

	function get_account_number($accountid) {
	  $this->db->select("number");
	  $this->db->where("accountid", $accountid);
	  $query = $this->db->get("accounts");

	  if ($query->num_rows() > 0)
	      return $query->row_array();
	  else
	      return false;
	}
	
        function add_callerid($data)
        {
                $this->load->library("curl");	
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Add CallerID";		
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');
		$this->curl->sendRequestToPerlScript($url,$data);
            
        }
        function get_callerid($account_id)
        {
            $this->db->where("accountid",$account_id);
            $this->db->from("accounts_callerid");
            $query = $this->db->get();
            return $query;
            
        }



}
