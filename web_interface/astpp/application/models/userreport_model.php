<?php
class Userreport_model extends CI_Model 
{
    function Userreport_model()
    {     
        parent::__construct();      
    }
            
    //**************************************** My Call Detail Report **************************************************//
    //*********************************************** Reseller Call Detail Report ***********************************************************//
    
    function build_mycdrs_search()
    {
	  if($this->session->userdata('advance_search')==1){
		    
	      $mycdrs_search =  $this->session->userdata('usercdr_search');
		    
		    if(!empty($mycdrs_search['start_date'])) {
			    $this->db->where('callstart >= ', $mycdrs_search['start_date'].':00');
		    }
		    if(!empty($mycdrs_search['end_date'])) {
			    $this->db->where('callstart <= ', $mycdrs_search['end_date'].':00');
		    }
	  
		    
	      $caller_id_operator = $mycdrs_search['caller_id_operator'];
	      
	      if(!empty($mycdrs_search['caller_id'])) {
		      switch($caller_id_operator){
			      case "1":
			      $this->db->like('callerid', $mycdrs_search['caller_id']); 
			      break;
			      case "2":
			      $this->db->not_like('callerid', $mycdrs_search['caller_id']);
			      break;
			      case "3":
			      $this->db->where('callerid', $mycdrs_search['caller_id']);
			      break;
			      case "4":
			      $this->db->where('callerid <>', $mycdrs_search['caller_id']);
			      break;
		      }
	      }
	      
	      $dest_operator = $mycdrs_search['dest_operator'];
		    
	      if(!empty($mycdrs_search['dest'])) {
			    switch($dest_operator){
				    case "1":
				    $this->db->like('callednum', $mycdrs_search['dest']); 
				    break;
				    case "2":
				    $this->db->not_like('callednum', $mycdrs_search['dest']);
				    break;
				    case "3":
				    $this->db->where('callednum', $mycdrs_search['dest']);
				    break;
				    case "4":
				    $this->db->where('callednum <>', $mycdrs_search['dest']);
				    break;
			    }
		    }
		    
	      $bill_sec_operator = $mycdrs_search['bill_sec_operator'];
		    if(!empty($mycdrs_search['bill_sec'])) {
			    switch($bill_sec_operator){
				    case "1":
				    $this->db->where('billseconds ', $mycdrs_search['bill_sec']);
				    break;
				    case "2":
				    $this->db->where('billseconds <>', $mycdrs_search['bill_sec']);					
				    break;
				    case "3":
				    $this->db->where('billseconds > ', $mycdrs_search['bill_sec']); 					
				    break;
				    case "4":
				    $this->db->where('billseconds < ', $mycdrs_search['bill_sec']); 	
				    break;
				    case "5":
				    $this->db->where('billseconds >= ', $mycdrs_search['bill_sec']);
				    break;
				    case "6":
				    $this->db->where('billseconds <= ', $mycdrs_search['bill_sec']);
				    break;
			    }
		    }	
		
		if($mycdrs_search['disposition']!='')
		{
		  $this->db->where('disposition', $mycdrs_search['disposition']);
		}
		    
		$debit_operator = $mycdrs_search['debit_operator'];
		if(!empty($mycdrs_search['debit'])) {
			switch($debit_operator){
				case "1":
				$this->db->where('debit ', $mycdrs_search['debit']);
				break;
				case "2":
				$this->db->where('debit <>', $mycdrs_search['debit']);					
				break;
				case "3":
				$this->db->where('debit > ', $mycdrs_search['debit']); 					
				break;
				case "4":
				$this->db->where('debit < ', $mycdrs_search['debit']); 	
				break;
				case "5":
				$this->db->where('debit >= ', $mycdrs_search['debit']);
				break;
				case "6":
				$this->db->where('debit <= ', $mycdrs_search['debit']);
				break;
			}
		}     
	      
	      if($mycdrs_search['calltype']!='')
	      {
		$this->db->where('calltype', $mycdrs_search['calltype']);
	      }
	  }    	  
	  $this->db->where('number', $this->session->userdata['accountinfo']['number']);
    }
    
    function getmycdrs($flag, $start=0, $limit=0)
    {
	$this->build_mycdrs_search();
	$this->db->from('customer_cdrs');
	$this->db->order_by("callstart desc"); 
	if($flag)
	{
	  if($start !='0' && $limit!='0')
	    {
		$this->db->limit($limit,$start);
	    }
	  $result = $this->db->get();
	}else{
	  $result = $this->db->count_all_results();
	}	
	return $result;
    }
    
    
    /*************************** Calling card CDR Report *********************************/
    
	function build_cc_cdr_search()
	{
	    if($this->session->userdata('advance_search')==1){
			
			$brands_cdrs_search =  $this->session->userdata('brands_cc_cdrs_search');
			
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
			
		}				
		$this->db->where('account', $this->session->userdata['accountinfo']['number']);		
	}
    	function get_cc_cdrs($flag,$start=0, $limit=0,$export=true)
	{
		$this->build_cc_cdr_search();
		$this->db->from('callingcard_cdrs');
		$this->db->order_by("callstart desc"); 
		if($flag)
		{
		    if($export)
		      $this->db->limit($limit,$start);
		    $result = $this->db->get();    		    
		}else{
		    $result = $this->db->count_all_results();
		}			
		return $result;
	}    
}