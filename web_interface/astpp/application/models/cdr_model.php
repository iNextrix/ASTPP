<?php
class Cdr_model extends CI_Model 
{
    function Cdr_model()
    {     
        parent::__construct();      
    }
    
    function build_customercdrs_search()
    {
	  if($this->session->userdata('advance_search')==1){
		    
	      $customercdrs_search =  $this->session->userdata('customercdr_search');
		    
		    if(!empty($customercdrs_search['start_date'])) {
			    $this->db->where('callstart >= ', $customercdrs_search['start_date'].':00');
		    }
		    if(!empty($customercdrs_search['end_date'])) {
			    $this->db->where('callstart <= ', $customercdrs_search['end_date'].':00');
		    }
	  
		    
	      $caller_id_operator = $customercdrs_search['caller_id_operator'];
	      
	      if(!empty($customercdrs_search['caller_id'])) {
		      switch($caller_id_operator){
			      case "1":
			      $this->db->like('callerid', $customercdrs_search['caller_id']); 
			      break;
			      case "2":
			      $this->db->not_like('callerid', $customercdrs_search['caller_id']);
			      break;
			      case "3":
			      $this->db->where('callerid', $customercdrs_search['caller_id']);
			      break;
			      case "4":
			      $this->db->where('callerid <>', $customercdrs_search['caller_id']);
			      break;
		      }
	      }
	      
	      $dest_operator = $customercdrs_search['dest_operator'];
		    
	      if(!empty($customercdrs_search['dest'])) {
			    switch($dest_operator){
				    case "1":
				    $this->db->like('callednum', $customercdrs_search['dest']); 
				    break;
				    case "2":
				    $this->db->not_like('callednum', $customercdrs_search['dest']);
				    break;
				    case "3":
				    $this->db->where('callednum', $customercdrs_search['dest']);
				    break;
				    case "4":
				    $this->db->where('callednum <>', $customercdrs_search['dest']);
				    break;
			    }
		    }
		    
	      $bill_sec_operator = $customercdrs_search['bill_sec_operator'];
		    if(!empty($customercdrs_search['bill_sec'])) {
			    switch($bill_sec_operator){
				    case "1":
				    $this->db->where('billseconds ', $customercdrs_search['bill_sec']);
				    break;
				    case "2":
				    $this->db->where('billseconds <>', $customercdrs_search['bill_sec']);					
				    break;
				    case "3":
				    $this->db->where('billseconds > ', $customercdrs_search['bill_sec']); 					
				    break;
				    case "4":
				    $this->db->where('billseconds < ', $customercdrs_search['bill_sec']); 	
				    break;
				    case "5":
				    $this->db->where('billseconds >= ', $customercdrs_search['bill_sec']);
				    break;
				    case "6":
				    $this->db->where('billseconds <= ', $customercdrs_search['bill_sec']);
				    break;
			    }
		    }	
		
		if($customercdrs_search['disposition']!='')
		{
		  $this->db->where('disposition', $customercdrs_search['disposition']);
		}
		
		if($customercdrs_search['customer']!='')
		{
		  $this->db->where('number', $customercdrs_search['customer']);
		}
		
		if(isset($customercdrs_search['trunk']) && $customercdrs_search['trunk']!='')
		{
		  $this->db->where('trunk', $customercdrs_search['trunk']);
		}
		
		if(isset($customercdrs_search['provider']) && $customercdrs_search['provider']!='')
		{
		  $this->db->where('provider', $customercdrs_search['provider']);
		}
		    
		$debit_operator = $customercdrs_search['debit_operator'];
		if(!empty($customercdrs_search['debit'])) {
			switch($debit_operator){
				case "1":
				$this->db->where('debit ', $customercdrs_search['debit']);
				break;
				case "2":
				$this->db->where('debit <>', $customercdrs_search['debit']);					
				break;
				case "3":
				$this->db->where('debit > ', $customercdrs_search['debit']); 					
				break;
				case "4":
				$this->db->where('debit < ', $customercdrs_search['debit']); 	
				break;
				case "5":
				$this->db->where('debit >= ', $customercdrs_search['debit']);
				break;
				case "6":
				$this->db->where('debit <= ', $customercdrs_search['debit']);
				break;
			}
		}      
		
		$cost_operator = $customercdrs_search['cost_operator'];
		if(!empty($customercdrs_search['cost'])) {
			    switch($cost_operator){
				    case "1":
				    $this->db->where('cost ', $customercdrs_search['cost']);
				    break;
				    case "2":
				    $this->db->where('cost <>', $customercdrs_search['cost']);					
				    break;
				    case "3":
				    $this->db->where('cost > ', $customercdrs_search['cost']); 					
				    break;
				    case "4":
				    $this->db->where('cost < ', $customercdrs_search['cost']); 	
				    break;
				    case "5":
				    $this->db->where('cost >= ', $customercdrs_search['cost']);
				    break;
				    case "6":
				    $this->db->where('cost <= ', $customercdrs_search['cost']);
				    break;
			    }
	      }
	      
	      if($customercdrs_search['pricelist']!='')
	      {
		$this->db->where('pricelist', $customercdrs_search['pricelist']);  		
	      }
	      
	      $pattern_operator = $customercdrs_search['pattern_operator'];
		    
	      if(!empty($customercdrs_search['pattern'])) {
		    switch($pattern_operator){
			    case "1":
			    $this->db->like('pattern', $customercdrs_search['pattern']); 
			    break;
			    case "2":
			    $this->db->not_like('pattern', $customercdrs_search['pattern']);
			    break;
			    case "3":
			    $this->db->where('pattern', $customercdrs_search['pattern']);
			    break;
			    case "4":
			    $this->db->where('pattern <>', $customercdrs_search['pattern']);
			    break;
		    }
	      }
	      
	      $notes_operator = $customercdrs_search['notes_operator'];
		    
	      if(!empty($customercdrs_search['notes'])) {
		    switch($notes_operator){
			    case "1":
			    $this->db->like('notes', $customercdrs_search['notes']); 
			    break;
			    case "2":
			    $this->db->not_like('notes', $customercdrs_search['notes']);
			    break;
			    case "3":
			    $this->db->where('notes', $customercdrs_search['notes']);
			    break;
			    case "4":
			    $this->db->where('notes <>', $customercdrs_search['notes']);
			    break;
		    }
	      }
	      
	      if($customercdrs_search['calltype']!='')
	      {
		$this->db->where('calltype', $customercdrs_search['calltype']);
	      }
	  }    
// 	  echo "<pre>";print_r($this->session->userdata);
	  if($this->session->userdata['logintype']=='2')
	  {
	      $this->db->where('reseller', "");
	  }else{
	      $this->db->where('reseller', $this->session->userdata['accountinfo']['number']);
	  }
    }
    
    function getcustomercdrs($flag, $start=0, $limit=0,$export=true)
    {
	$this->build_customercdrs_search();
	$this->db->from('customer_cdrs');
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
    
    
    //*********************************************** Reseller Call Detail Report ***********************************************************//
    
    function build_resellercdrs_search()
    {
	  if($this->session->userdata('advance_search')==1){
		    
	      $resellercdrs_search =  $this->session->userdata('resellercdr_search');
		    
		    if(!empty($resellercdrs_search['start_date'])) {
			    $this->db->where('callstart >= ', $resellercdrs_search['start_date'].':00');
		    }
		    if(!empty($resellercdrs_search['end_date'])) {
			    $this->db->where('callstart <= ', $resellercdrs_search['end_date'].':00');
		    }
	  
		    
	      $caller_id_operator = $resellercdrs_search['caller_id_operator'];
	      
	      if(!empty($resellercdrs_search['caller_id'])) {
		      switch($caller_id_operator){
			      case "1":
			      $this->db->like('callerid', $resellercdrs_search['caller_id']); 
			      break;
			      case "2":
			      $this->db->not_like('callerid', $resellercdrs_search['caller_id']);
			      break;
			      case "3":
			      $this->db->where('callerid', $resellercdrs_search['caller_id']);
			      break;
			      case "4":
			      $this->db->where('callerid <>', $resellercdrs_search['caller_id']);
			      break;
		      }
	      }
	      
	      $dest_operator = $resellercdrs_search['dest_operator'];
		    
	      if(!empty($resellercdrs_search['dest'])) {
			    switch($dest_operator){
				    case "1":
				    $this->db->like('callednum', $resellercdrs_search['dest']); 
				    break;
				    case "2":
				    $this->db->not_like('callednum', $resellercdrs_search['dest']);
				    break;
				    case "3":
				    $this->db->where('callednum', $resellercdrs_search['dest']);
				    break;
				    case "4":
				    $this->db->where('callednum <>', $resellercdrs_search['dest']);
				    break;
			    }
		    }
		    
	      $bill_sec_operator = $resellercdrs_search['bill_sec_operator'];
		    if(!empty($resellercdrs_search['bill_sec'])) {
			    switch($bill_sec_operator){
				    case "1":
				    $this->db->where('billseconds ', $resellercdrs_search['bill_sec']);
				    break;
				    case "2":
				    $this->db->where('billseconds <>', $resellercdrs_search['bill_sec']);					
				    break;
				    case "3":
				    $this->db->where('billseconds > ', $resellercdrs_search['bill_sec']); 					
				    break;
				    case "4":
				    $this->db->where('billseconds < ', $resellercdrs_search['bill_sec']); 	
				    break;
				    case "5":
				    $this->db->where('billseconds >= ', $resellercdrs_search['bill_sec']);
				    break;
				    case "6":
				    $this->db->where('billseconds <= ', $resellercdrs_search['bill_sec']);
				    break;
			    }
		    }	
		
		if($resellercdrs_search['disposition']!='')
		{
		  $this->db->where('disposition', $resellercdrs_search['disposition']);
		}
		
		if($resellercdrs_search['reseller']!='')
		{
		  $this->db->where('number', $resellercdrs_search['reseller']);
		}
		
		if(isset($resellercdrs_search['trunk']) && $resellercdrs_search['trunk']!='')
		{
		  $this->db->where('trunk', $resellercdrs_search['trunk']);
		}
		
		if(isset($resellercdrs_search['provider']) && $resellercdrs_search['provider']!='')
		{
		  $this->db->where('provider', $resellercdrs_search['provider']);
		}
		    
		$debit_operator = $resellercdrs_search['debit_operator'];
		if(!empty($resellercdrs_search['debit'])) {
			switch($debit_operator){
				case "1":
				$this->db->where('debit ', $resellercdrs_search['debit']);
				break;
				case "2":
				$this->db->where('debit <>', $resellercdrs_search['debit']);					
				break;
				case "3":
				$this->db->where('debit > ', $resellercdrs_search['debit']); 					
				break;
				case "4":
				$this->db->where('debit < ', $resellercdrs_search['debit']); 	
				break;
				case "5":
				$this->db->where('debit >= ', $resellercdrs_search['debit']);
				break;
				case "6":
				$this->db->where('debit <= ', $resellercdrs_search['debit']);
				break;
			}
		}      
		
		$cost_operator = $resellercdrs_search['cost_operator'];
		if(!empty($resellercdrs_search['cost'])) {
			    switch($cost_operator){
				    case "1":
				    $this->db->where('cost ', $resellercdrs_search['cost']);
				    break;
				    case "2":
				    $this->db->where('cost <>', $resellercdrs_search['cost']);					
				    break;
				    case "3":
				    $this->db->where('cost > ', $resellercdrs_search['cost']); 					
				    break;
				    case "4":
				    $this->db->where('cost < ', $resellercdrs_search['cost']); 	
				    break;
				    case "5":
				    $this->db->where('cost >= ', $resellercdrs_search['cost']);
				    break;
				    case "6":
				    $this->db->where('cost <= ', $resellercdrs_search['cost']);
				    break;
			    }
	      }
	      
	      if($resellercdrs_search['pricelist']!='')
	      {
		$this->db->where('pricelist', $resellercdrs_search['pricelist']);  		
	      }
	      
	      $pattern_operator = $resellercdrs_search['pattern_operator'];
		    
	      if(!empty($resellercdrs_search['pattern'])) {
		    switch($pattern_operator){
			    case "1":
			    $this->db->like('pattern', $resellercdrs_search['pattern']); 
			    break;
			    case "2":
			    $this->db->not_like('pattern', $resellercdrs_search['pattern']);
			    break;
			    case "3":
			    $this->db->where('pattern', $resellercdrs_search['pattern']);
			    break;
			    case "4":
			    $this->db->where('pattern <>', $resellercdrs_search['pattern']);
			    break;
		    }
	      }
	      
	      $notes_operator = $resellercdrs_search['notes_operator'];
		    
	      if(!empty($resellercdrs_search['notes'])) {
		    switch($notes_operator){
			    case "1":
			    $this->db->like('notes', $resellercdrs_search['notes']); 
			    break;
			    case "2":
			    $this->db->not_like('notes', $resellercdrs_search['notes']);
			    break;
			    case "3":
			    $this->db->where('notes', $resellercdrs_search['notes']);
			    break;
			    case "4":
			    $this->db->where('notes <>', $resellercdrs_search['notes']);
			    break;
		    }
	      }
	      
	      if($resellercdrs_search['calltype']!='')
	      {
		$this->db->where('calltype', $resellercdrs_search['calltype']);
	      }
	  }    
	  if($this->session->userdata['logintype']=='2')
	  {
	      $this->db->where('reseller', "");
	  }else{
	      $this->db->where('reseller', $this->session->userdata['accountinfo']['number']);
	  }
    }
    
    function getresellercdrs($flag, $start=0, $limit=0,$export=true)
    {
	$this->build_resellercdrs_search();
	$this->db->from('reseller_cdrs');
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
    
    
    
    //*********************************************** Provider Call Detail Report ***********************************************************//
    
    function build_providercdrs_search()
    {
	  if($this->session->userdata('advance_search')==1){
		    
	      $providercdrs_search =  $this->session->userdata('providercdr_search');
		    
		    if(!empty($providercdrs_search['start_date'])) {
			    $this->db->where('callstart >= ', $providercdrs_search['start_date'].':00');
		    }
		    if(!empty($providercdrs_search['end_date'])) {
			    $this->db->where('callstart <= ', $providercdrs_search['end_date'].':00');
		    }
	  
		    
	      $caller_id_operator = $providercdrs_search['caller_id_operator'];
	      
	      if(!empty($providercdrs_search['caller_id'])) {
		      switch($caller_id_operator){
			      case "1":
			      $this->db->like('callerid', $providercdrs_search['caller_id']); 
			      break;
			      case "2":
			      $this->db->not_like('callerid', $providercdrs_search['caller_id']);
			      break;
			      case "3":
			      $this->db->where('callerid', $providercdrs_search['caller_id']);
			      break;
			      case "4":
			      $this->db->where('callerid <>', $providercdrs_search['caller_id']);
			      break;
		      }
	      }
	      
	      $dest_operator = $providercdrs_search['dest_operator'];
		    
	      if(!empty($providercdrs_search['dest'])) {
			    switch($dest_operator){
				    case "1":
				    $this->db->like('callednum', $providercdrs_search['dest']); 
				    break;
				    case "2":
				    $this->db->not_like('callednum', $providercdrs_search['dest']);
				    break;
				    case "3":
				    $this->db->where('callednum', $providercdrs_search['dest']);
				    break;
				    case "4":
				    $this->db->where('callednum <>', $providercdrs_search['dest']);
				    break;
			    }
		    }
		    
	      $bill_sec_operator = $providercdrs_search['bill_sec_operator'];
		    if(!empty($providercdrs_search['bill_sec'])) {
			    switch($bill_sec_operator){
				    case "1":
				    $this->db->where('billseconds ', $providercdrs_search['bill_sec']);
				    break;
				    case "2":
				    $this->db->where('billseconds <>', $providercdrs_search['bill_sec']);					
				    break;
				    case "3":
				    $this->db->where('billseconds > ', $providercdrs_search['bill_sec']); 					
				    break;
				    case "4":
				    $this->db->where('billseconds < ', $providercdrs_search['bill_sec']); 	
				    break;
				    case "5":
				    $this->db->where('billseconds >= ', $providercdrs_search['bill_sec']);
				    break;
				    case "6":
				    $this->db->where('billseconds <= ', $providercdrs_search['bill_sec']);
				    break;
			    }
		    }	
		
		if($providercdrs_search['disposition']!='')
		{
		  $this->db->where('disposition', $providercdrs_search['disposition']);
		}
		
		if($providercdrs_search['provider']!='')
		{
		  $this->db->where('number', $providercdrs_search['provider']);
		}
		
		if(isset($providercdrs_search['trunk']) && $providercdrs_search['trunk']!='')
		{
		  $this->db->where('trunk', $providercdrs_search['trunk']);
		}
		    
		$debit_operator = $providercdrs_search['debit_operator'];
		if(!empty($providercdrs_search['debit'])) {
			switch($debit_operator){
				case "1":
				$this->db->where('debit ', $providercdrs_search['debit']);
				break;
				case "2":
				$this->db->where('debit <>', $providercdrs_search['debit']);					
				break;
				case "3":
				$this->db->where('debit > ', $providercdrs_search['debit']); 					
				break;
				case "4":
				$this->db->where('debit < ', $providercdrs_search['debit']); 	
				break;
				case "5":
				$this->db->where('debit >= ', $providercdrs_search['debit']);
				break;
				case "6":
				$this->db->where('debit <= ', $providercdrs_search['debit']);
				break;
			}
		}      		
	      
	      $pattern_operator = $providercdrs_search['pattern_operator'];
		    
	      if(!empty($providercdrs_search['pattern'])) {
		    switch($pattern_operator){
			    case "1":
			    $this->db->like('pattern', $providercdrs_search['pattern']); 
			    break;
			    case "2":
			    $this->db->not_like('pattern', $providercdrs_search['pattern']);
			    break;
			    case "3":
			    $this->db->where('pattern', $providercdrs_search['pattern']);
			    break;
			    case "4":
			    $this->db->where('pattern <>', $providercdrs_search['pattern']);
			    break;
		    }
	      }
	      
	      $notes_operator = $providercdrs_search['notes_operator'];
		    
	      if(!empty($providercdrs_search['notes'])) {
		    switch($notes_operator){
			    case "1":
			    $this->db->like('notes', $providercdrs_search['notes']); 
			    break;
			    case "2":
			    $this->db->not_like('notes', $providercdrs_search['notes']);
			    break;
			    case "3":
			    $this->db->where('notes', $providercdrs_search['notes']);
			    break;
			    case "4":
			    $this->db->where('notes <>', $providercdrs_search['notes']);
			    break;
		    }
	      }
	      
	      if($providercdrs_search['calltype']!='')
	      {
		$this->db->where('calltype', $providercdrs_search['calltype']);
	      }
	  }    
// 	  if($this->session->userdata['logintype']=='2')
// 	  {
// 	      $this->db->where('provider', "");
// 	  }else{
// 	      $this->db->where('provider', $this->session->userdata['number']);
// 	  }
    }
    
    function getprovidercdrs($flag, $start=0, $limit=0,$export=true)
    {
	$this->build_providercdrs_search();
	$this->db->from('provider_cdrs');
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
    
    //**************************************** My Call Detail Report **************************************************//
    //*********************************************** Reseller Call Detail Report ***********************************************************//
    
    function build_mycdrs_search()
    {
	  if($this->session->userdata('advance_search')==1){
		    
	      $mycdrs_search =  $this->session->userdata('resellercdr_search');
		    
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
    
    function getmycdrs($flag, $start=0, $limit=0,$export=true)
    {
	$this->build_mycdrs_search();
	$this->db->from('reseller_cdrs');
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