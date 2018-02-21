<?php

class Astpp_common extends CI_Model 
{
	
	// ------------------------------------------------------------------------
	/**
	 * initialises the class inheriting the methods of the class Model 
	 *
	 * @return Usermodel
	 */
	function Astpp_common()
	{     
	    parent::__construct();
	}
	function gettext($str)
	{
	    return $str;
	}
	function quote($inp)
	{
	    return "'".$this->db->escape_str($inp)."'";
	}
	function db_do($q)
	{
	    $this->db->query($q);
	    
	    return 1;
	}
	function db_get_item($q,$colname)
	{		
		$item_arr = array();
		$query = $this->db->query($q);	
		//echo $this->db->last_query();	
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row[$colname];
		}
		return '';
	}	
    
	function db_get_array($q,$colname)
	{		
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = @$row[$colname];
			}
		}
		return $item_arr;
	}
	function db_get_array1($q,$colname,$colvalue)
	{		
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[$row[$colname]] = $row[$colvalue];
			}
		}
		return $item_arr;
	}			
	function db_get_arrays($q)
	{		
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row;
			}
		}
		return $item_arr;
	}	
	function config_data($name)
	{
		$q = "select * from system where name=".$this->quote($name);
		return $this->db_get_item($q,'value');
	}
	function get_config()
	{
		$q = "select * from system ";
		return $this->db_get_array1($q,'name','value');
	}
	
    // Return the balance for a specific ASTPP account.
	function accountbalance($account) 
	{		
		$debit = 0;
		$q = "SELECT SUM(debit) as val1 FROM cdrs WHERE cardnum=".$this->quote($account)." AND status NOT IN (1, 2)";
		$query = $this->db->query($q);				
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$debit= $row['val1'];
		}
		$credit = 0;
    	$q ="SELECT SUM(credit) as val1  FROM cdrs WHERE cardnum= ". $this->quote($account). " AND status NOT IN (1, 2)";
		$query = $this->db->query($q);				
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$credit= $row['val1'];
		}
		$posted_balance = 0;
    	$q ="SELECT * FROM accounts WHERE number = ". $this->quote($account);
		$query = $this->db->query($q);				
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$posted_balance= $row['balance'];
		}
	   	$balance = ( $debit - $credit + $posted_balance );
    	return $balance;		
	} 
	   
	function list_pricelist_accounts($pricelist)
	{
		$q = "SELECT number FROM accounts WHERE status < 2 AND pricelist = " . $this->quote($pricelist);	
		return $this->db_get_array($q,'number');
	}
	
	function list_dids_account($account) 
	{
		$q = "SELECT * FROM dids WHERE status = 1 AND (account = '".$account."' OR account = '')";		
		return $this->db_get_arrays($q);
	}
	
	function list_dids_number_account($account) 
	{
		$q = "SELECT number FROM dids WHERE status = 1 AND (account = '".$account."' OR account = '')";
		return $this->db_get_array($q,'number');
		
	}	
	
	function list_ani_account($account)
	{
		$q = "SELECT * FROM ani_map WHERE account = ". $this->quote($account). "";
		return $this->db_get_array($q, 'number');
	}
	
	function list_ip_map($account)
	{
		$q = "SELECT * FROM ip_map WHERE account = ".$this->quote($account). " ORDER BY ip";
		return $this->db_get_array($q, 'number');
	}
	
	//Update the available credit on the calling card.
	function callingcard_update_balance($cardinfo, $charge) 
	{  
		$data = array();
		$data['used'] = $this->quote( $charge + $cardinfo['used'] );
		$this->db->update('callingcards', $data, "cardnumber = ".$this->quote($cardinfo['cardnumber']) );
	}
	
	// Set the "inuse" flag on the calling cards.  This prevents multiple people from
	function callingcard_set_in_use($cardinfo,$status) 
	{  
		$data = array();
		$data['inuse'] = $status;
		$this->db->update('callingcards', $data, "cardnumber = ".$this->quote($cardinfo['cardnumber']) );
	}
		
	function finduniquecallingcard()
	{
		return ''.time() . rand(0,9);
	}
		
	function add_callingcard($config,$branddata, $status, $pennies,$account, $pins)
	{
		$cc = $this->finduniquecallingcard();
		$pin = '';
	    if ($pins) {
	        $pin =
	            int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 )
	          . int( rand() * 9000 + 1000 );
			$pin = $config->{pin_cc_prepend} . $pin;
	        $pin = substr( $pin, 0, $config[pinlength] );
	    }
    	$sql =
			"INSERT INTO callingcards (cardnumber,brand,status,value,account,pin,validfordays,created,firstused,expiry,maint_fee_pennies,"
      		. "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies,min_length_minutes,min_length_pennies) VALUES ("
      	. $this->quote($cc) . ","
      	. $this->quote( $branddata['name'] ) . ","
      	. $this->quote($status) . ","
      	. $this->quote($pennies) . ","
      	. $this->quote($account) . ","
      	. $this->quote($pin) . ","
      	. $this->quote( $branddata['validfordays'] )
      	. ", NOW(), '0000-00-00 00:00:00', '0000-00-00 00:00:00', "
      	. $this->quote( $branddata['maint_fee_pennies'] ) . ","
      	. $this->quote( $branddata['maint_fee_days'] ) . ","
      	. $this->quote( $branddata['disconnect_fee_pennies'] ) . ","
      	. $this->quote( $branddata['minute_fee_minutes'] ) . ","
      	. $this->quote( $branddata['minute_fee_pennies'] ) . ","
      	. $this->quote( $branddata['min_length_minutes'] ) . ","
      	. $this->quote( $branddata['min_length_pennies'] ) . ")";
		
      	$this->db->query($sql);
      	
      	return array($cc,$pin);
	}
	
	function add_pricelist($name, $inc, $markup, $reseller)
	{
		$q = "SELECT name FROM pricelists WHERE name = " . $this->quote($name);
		$pricelist = $this->db_get_item($q,'name');
		
		if($pricelist == '')
		{
			$rs = 'NULL';
			if($reseller != '') $rs = $reseller;
			$tmp = "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
          		. $this->quote($name) . ", "
          		. $this->quote($inc) . ", "
          		. $this->quote($markup) . ", 1, ".$rs.")";
          	
          	$this->db->query($tmp);
          	return 1;
		}
		else 
		{
			$tmp = "UPDATE pricelists SET status = 1 WHERE name = ". $this->quote($name);
			$this->db->query($tmp);
			return 1;
		}
		return 0;
	}
	
	// Add an account.  This applys to user accounts as well as reseller accounts.
	function addaccount($config,$accountinfo) 
	{
		$logintype = $this->session->userdata('logintype');
		$username = $this->session->userdata('username');
    	
		if ($logintype == 1 || $logintype == 5) {
			$accountinfo['reseller'] = $accountinfo['username'];
    	} else {
			$accountinfo['reseller'] = "";
    	}
    	if(! isset($accountinfo['posttoexternal'])) $accountinfo['posttoexternal'] = 0;
    	if(! isset($accountinfo['firstname'])) $accountinfo['firstname'] = "";
    	if(! isset($accountinfo['lastname'])) $accountinfo['lastname'] = "";
    	if(! isset($accountinfo['middlename'])) $accountinfo['middlename'] = "";
    	if(! isset($accountinfo['reseller'])) $accountinfo['reseller'] = "";
    	if(! isset($accountinfo['company'])) $accountinfo['company'] = "";
    	if(! isset($accountinfo['address1'])) $accountinfo['address1'] = "";
    	if(! isset($accountinfo['address2'])) $accountinfo['address2'] = "";
    	if(! isset($accountinfo['postal_code'])) $accountinfo['postal_code'] = "";
    	if(! isset($accountinfo['province'])) $accountinfo['province'] = "";
    	if(! isset($accountinfo['city'])) $accountinfo['city'] = "";
    	if(! isset($accountinfo['country'])) $accountinfo['country'] = "";
    	if(! isset($accountinfo['telephone1'])) $accountinfo['telephone1'] = "";
    	if(! isset($accountinfo['telephone2'])) $accountinfo['telephone2'] = "";
    	if(! isset($accountinfo['facsimile'])) $accountinfo['facsimile'] = "";
    	if(! isset($accountinfo['email'])) $accountinfo['email'] = "";
    	if(! isset($accountinfo['currency'])) $accountinfo['currency'] = "";
    	if(! isset($accountinfo['maxchannels'])) $accountinfo['maxchannels'] = "";
    	if(! isset($accountinfo['timezone'])) $accountinfo['timezone'] = "";
    	if(! isset($accountinfo['language'])) $accountinfo['language'] = "";

    	$cc = $this->finduniquecc(  $config );
    	$pin = $this->finduniquepin(  $config );
   		$tmp =
		"INSERT INTO accounts (cc,pin,number,pricelist,status,sweep,credit_limit,posttoexternal,password,"
      	. "first_name, middle_name, last_name, company_name, address_1, address_2,"
      	. "postal_code, province, city, country, telephone_1, telephone_2, fascimile,"
      	. "email, language, currency, reseller, tz, maxchannels, type"
      	. ") VALUES ("
      	. $this->quote($cc) . ","
      	. $this->quote($pin) . ","
      	. $this->quote( $accountinfo['number'] ) . ","
      	. $this->quote( $accountinfo['pricelist'] ) . ", 1,"
      	. $this->quote( $accountinfo['sweep'] ) . ","
      	. $this->quote( $accountinfo['credit_limit'] ) . ","
      	. $this->quote( $accountinfo['posttoexternal'] ) . ","
      	. $this->quote( $accountinfo['accountpassword'] ) . ","
      	. $this->quote( $accountinfo['firstname'] ) . ","
      	. $this->quote( $accountinfo['middlename'] ) . ","
      	. $this->quote( $accountinfo['lastname'] ) . ","
      	. $this->quote( $accountinfo['company'] ) . ","
      	. $this->quote( $accountinfo['address1'] ) . ","
      	. $this->quote( $accountinfo['address2'] ) . ","
      	. $this->quote( $accountinfo['postal_code'] ) . ","
      	. $this->quote( $accountinfo['province'] ) . ","
      	. $this->quote( $accountinfo['city'] ) . ","
      	. $this->quote( $accountinfo['country'] ) . ","
      	. $this->quote( $accountinfo['telephone1'] ) . ","
      	. $this->quote( $accountinfo['telephone2'] ) . ","
      	. $this->quote( $accountinfo['facsimile'] ) . ","
      	. $this->quote( $accountinfo['email'] ) . ","
      	. $this->quote( $accountinfo['language'] ) . ","
      	. $this->quote( $accountinfo['currency'] ) . ","
      	. $this->quote( $accountinfo['reseller'] ) . ","
      	. $this->quote( $accountinfo['timezone'] ) . ","
      	. $this->quote( $accountinfo['maxchannels'] ) . ","
      	. $this->quote( $accountinfo['accounttype'] ) . ")";
      	
      $this->db->query($tmp);

      return 1;
	}	

	function add_reseller($name,$posttoexternal)
	{
		
		$configdir = $this->config_data('astpp_dir');
		$configfile = $configdir. "/astpp-" . $name . "-config.conf";
		
		$q = "SELECT name FROM resellers WHERE name = " . $this->quote($name);
		$item_data = $this->db_get_item($q,'name');
		
		if($item_data == '')
		{
			 $tmp = "INSERT INTO resellers (name,status,config_file,posttoexternal) VALUES ("
          			. $this->quote($name) . ", 1,"
          			. $this->quote($configfile) . ","
          			. $this->quote($posttoexternal) . ")";
          	
          	$this->db->query($tmp);
          	
          	system("cp ".$configdir."/sample.reseller-config.conf ".$configfile);
          	return 1;
		}
		else 
		{
			$tmp = "UPDATE resellers SET status = 1 WHERE name = ". $this->quote($name);
			$this->db->query($tmp);
			system("mv ".$configdir."/".$name."-config.conf.old ".$configfile);
			return 1;
		}
		return 0;
		
	}
	
	function get_trunk($trunk)
	{
		$q = "SELECT * FROM trunks WHERE name = " . $this->quote($trunk);
		return $this->db_get_arrays($q);
	}
	
	function get_invoice($invoicedata)
	{
		$q = "SELECT * FROM invoice_list_view WHERE invoiceid = " . $this->quote($invoicedata);
		return $this->db_get_arrays($q);
	}
	
	function get_invoice_total($invoiceid)
	{
		$this->db->where('invoices_id',$invoiceid);
		$this->db->order_by('sort_order','ASC');
		$this->db->from('invoices_total');
		$query = $this->db->get();
		return $query;			
	}
	
	//TODO: incomplete
	function get_dial_string($route, $phone)
	{
		$dialstring = '';
		$q  = "SELECT * FROM trunks WHERE name = ". $this->quote( $route['trunk'] );
		$trunkdata = $this->db_get_array($q);
		
		return $dialstring;
	}
	//Return the list of outbound routes you should use based either on cost or precedence.
	function get_outbound_routes($number, $accountinfo,$routeinfo, $reseller_list)
	{
		if ($accountinfo['routing_technique'] && $accountinfo['routing_technique'] != 0) 
		{
			$sql = "SELECT * FROM outbound_routes WHERE "
					. $this->quote($number)
					. " RLIKE pattern AND status = 1 AND precedence <= "
					. $this->quote($accountinfo['routing_technique']) 
					. "GROUP BY trunk ORDER BY cost";
					# . "ORDER by LENGTH(pattern) DESC, precedence, cost"
			
			$routelist = $this->db_get_array($sql); 
		}
		elseif ($routeinfo->{precedence} && $routeinfo->{precedence} != 0 )
		{
			$sql ="SELECT * FROM outbound_routes WHERE "
				. $this->quote($number)
				. " RLIKE pattern AND status = 1 AND precedence <= " 
				. $this->quote($routeinfo['precedence']) 
				. "GROUP BY trunk ORDER BY cost";
				//# . "ORDER by LENGTH(pattern) DESC, precedence, cost"
			$routelist = $this->db_get_array($sql); 
		}
		else 
		{
			$sql = "SELECT * FROM outbound_routes WHERE "
				. $this->quote($number)
				. " RLIKE pattern AND status = 1 "
				. "GROUP BY trunk ORDER BY cost";
				#."ORDER by LENGTH(pattern) DESC, cost"
			
			$routelist = $this->db_get_array($sql); 
		}
		//-----
		$outbound_route_list = array();
		if ($reseller_list) 
		{
			//print STDERR "CHECKING LIST OF RESELLERS AGAINST TRUNKS\n";
			foreach ($routelist as $route)
			{
				//print STDERR "CHECKING ROUTE: $route->{name}\t";
				$trunkdata = $this->get_trunk($route['trunk']);
				if ($trunkdata['resellers'] != "") 
				{
					//print STDERR "ROUTE RESELLER DATA = $trunkdata->{resellers}\t";
					foreach($reseller_list as $reseller) 
					{
						//print STDERR "Checking Reseller: $reseller against trunk: $route->{name}\t";
						if ($trunkdata['resellers'] == '$reseller') 
						{
							//push @outbound_route_list, $route;
							$outbound_route_list[] = $route;
						}
					}
				} 
				else 
				{
					//print STDERR "ROUTE RESELLER DATA = $trunkdata->{resellers}\n";
					//push @outbound_route_list, $route;
					$outbound_route_list[] = $route;
				}
			}
		} 
		else 
		{
			//print STDERR "WE DID NOT RECEIVE A LIST OF RESELLERS TO CHECK AGAINST.\n";
			//@outbound_route_list = @routelist;
			$outbound_route_list = $routelist;			
		}
		
		return $outbound_route_list;
	}
	
// Load configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
// those settings with settings from the database.	
	function load_config_db()
	{		
		$q = "SELECT name,value FROM system WHERE reseller IS NULL";
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr['name'] = $row['value'];
			}
		}
		return $item_arr;
	}
		
// Load reseller configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
// those settings with settings from the database.
	function load_config_db_reseller($reseller)
	{		
		$q = "SELECT name,value FROM system WHERE reseller = " . $this->quote($reseller);
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr['name'] = $row['value'];
			}
		}
		return $item_arr;
	}
		
// Load brand specific configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
// those settings with settings from the database.
	function load_config_db_brand($brand)
	{		
		$q = "SELECT name,value FROM system WHERE brand = " . $this->quote($brand);
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr['name'] = $row['value'];
			}
		}
		return $item_arr;
	}	

	/*function list_trunks()
	{
		$q = "SELECT name FROM trunks WHERE status = 1";
		return $this->db_get_array($q,'name');
	}
	
	function list_provider_trunks()
	{
		$colname = 'name';
		$trunk_arr = array();
		$this->db->where("provider", $this->session->userdata('username'));
		$this->db->from('trunks');	
		$query = $this->db->get();	
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$trunk_arr[] = $row[$colname];
			}
		}
		return $trunk_arr;
		
	}*/
	
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
	
	function prettytimestamp() 
	{
		$date = new DateTime();
		return $date->format('Y-m-d H:i:s');		
	}	
		
	function get_did_reseller($did,$reseller="")
	{
		$sql = "SELECT dids.number AS number, "
			. "reseller_pricing.monthlycost AS monthlycost, "
			. "reseller_pricing.prorate AS prorate, "
			. "reseller_pricing.setup AS setup, "
			. "reseller_pricing.cost AS cost, "
			. "reseller_pricing.connectcost AS connectcost, "
			. "reseller_pricing.includedseconds AS includedseconds, "
			. "reseller_pricing.inc AS inc, "
			. "reseller_pricing.disconnectionfee AS disconnectionfee, "
			. "dids.provider AS provider, "
			. "dids.country AS country, "
			. "dids.city AS city, "
			. "dids.province AS province, "
			. "dids.extensions AS extensions, "
			. "dids.account AS account, "
			. "dids.variables AS variables, "
			. "dids.options AS options, "
			. "dids.maxchannels AS maxchannels, "
			. "dids.chargeonallocation AS chargeonallocation, "
			. "dids.allocation_bill_status AS allocation_bill_status, "
			. "dids.limittime AS limittime, "
			. "dids.dial_as AS dial_as, "
			. "dids.status AS status "
			. "FROM dids, reseller_pricing "
			. "WHERE dids.number = " . $this->quote($did)
			. " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
			. $this->quote($reseller) . " AND reseller_pricing.note = "
			. $this->quote($did);
		
		return $this->db_get_arrays($sql);
	}
	
	
			
	function get_did_reseller_new($did,$reseller="")
	{
		$sql = "SELECT dids.number AS number, "
			. "reseller_pricing.monthlycost AS monthlycost, "
			. "reseller_pricing.prorate AS prorate, "
			. "reseller_pricing.setup AS setup, "
			. "reseller_pricing.cost AS cost, "
			. "reseller_pricing.connectcost AS connectcost, "
			. "reseller_pricing.includedseconds AS includedseconds, "
			. "reseller_pricing.inc AS inc, "
			. "reseller_pricing.disconnectionfee AS disconnectionfee, "
			. "dids.provider AS provider, "
			. "dids.country AS country, "
			. "dids.city AS city, "
			. "dids.province AS province, "
			. "dids.extensions AS extensions, "
			. "dids.account AS account, "
			. "dids.variables AS variables, "
			. "dids.options AS options, "
			. "dids.maxchannels AS maxchannels, "
			. "dids.chargeonallocation AS chargeonallocation, "
			. "dids.allocation_bill_status AS allocation_bill_status, "
			. "dids.limittime AS limittime, "
			. "dids.dial_as AS dial_as, "
			. "dids.status AS status "
			. "FROM dids, reseller_pricing "
			. "WHERE dids.number = " . $this->quote($did)
			. " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
			. $this->quote($reseller) . " AND reseller_pricing.note = "
			. $this->quote($did);
		
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			return $query->row_array();
		}
		//return $this->db_get_arrays($sql);
	}
	
	//Write ASTPP cdr.  I think this one is mostly deprecated but should probably be completely removed.
	function post_cdr($uniqueid,$account,$clid,$dest,$disp,$seconds,$cost,$callstart,$postexternal,$trunk,$notes,$pricelist,$pattern)
	{
		if ( !$trunk ) $trunk    = "N/A"; 
		if ( !$uniqueid ) $uniqueid = gettext("N/A") ;
		if ( !$pricelist ) $pricelist = gettext("N/A") ;
		if ( !$pattern ) $pattern = gettext("N/A") ;
		$status   = 0;
		
		$q  = 
		"INSERT INTO cdrs(uniqueid,cardnum,callerid,callednum,trunk,disposition,billseconds,"
		. "debit,callstart,status,notes,pricelist,pattern) VALUES ("
		. $this->quote($uniqueid) . ", "
		. $this->quote($account) . ", "
		. $this->quote($clid) . ", "
		. $this->quote($dest) . ", "
		. $this->quote($trunk) . ", "
		. $this->quote($disp) . ", "
		. $this->quote($seconds) . ", "
		. $this->quote($cost) . ", "
		. $this->quote($callstart) . ", "
		. $this->quote($status) . ", "
		. $this->quote($notes) . ","
		. $this->quote($pricelist) . ","
		. $this->quote($pattern) . ")";		
		
		$this->db->query($q);
	}
	
	function get_did($number)
	{
		$q = "SELECT * FROM dids WHERE number = '".$this->db->escape_str($number)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}
		return NULL;
		
	}		
	function remove_did($did,$account)
	{
		$callstart = $this->prettytimestamp();
		$accountdata = $this->get_account($account);
		$notes = '';
		$dest = gettext("DID:") . $did . gettext(" disconnection fee");
		
		if ($accountdata['reseller'] != '') 
		{
			while ($accountdata['reseller'] != '') 
			{
				$didinfo = $this->get_did_reseller_new($did,$accountdata['reseller']);
	        	if ($didinfo['disconnectionfee'] != 0) 
	        	{
					$dest = gettext("DID:") . $did . gettext(" disconnection fee");
					$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['disconnectionfee'],$callstart,$accountdata['postexternal'],'',$notes);
				}
				$accountdata = $this->get_account($accountdata['reseller']);
			}
			$didinfo = $this->get_did($did,$account);
	        if ($didinfo['disconnectionfee'] != 0) 
	        {
				$dest = gettext("DID:") . $did . gettext(" disconnection fee");
				$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['disconnectionfee'],$callstart,$accountdata['postexternal'],'',$notes);
			}
			$accountdata = $this->get_account($account);
		} 
		else 
		{
			$didinfo = $this->get_did($did,$account);
	        if ($didinfo['disconnectionfee'] != 0)
	        {
				$dest = gettext("DID:") . $did . gettext(" disconnection fee");
				$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['disconnectionfee'],$callstart,$accountdata['postexternal'],'',$notes);
			}
		}		
	}
	//Find the appropriate charge based on the day of the month.  This is mostly used for DID setup fees.
	// days = current date to end date of month
	function prorate($amount)
	{
		$curdate = getdate();
		$days = cal_days_in_month(CAL_GREGORIAN, $curdate['month'], $curdate['year']); // 31
		$start_date = $curdate['year'] . "-" . $curdate['month'] . "-" . $curdate['mday'];
		$end_date = $curdate['year'] . "-" . $curdate['month'] . "-" . $days;
		$daily_charge = $amount / $days;
		$prorated = $daily_charge * ($days - $curdate['mday']);

		return array($prorated,$start_date,$end_date);		
	}
	//This subroutine is used for DIDs that are set to only start the monthly billing after the first ANSWERED call.
	function apply_did_activated_charges($did,$account)
	{
		$didinfo = $this->get_did($did);
		$accountinfo = $this->get_account($account);
		
		if ($accountinfo['reseller'] && $accountinfo['reseller'] != "") 
		{
			$accountdata = $this->get_account($account);
			while ($accountdata['reseller'] != "") 
			{
				$didinfo = $this->get_did_reseller_new($did,$accountdata['reseller']);
				if ($didinfo['prorate'] == 1) 
				{
					//($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
					$retarr = $this->prorate($didinfo['monthlycost']);
					$cost = $retarr[0]; 
					$start_date = $retarr[1]; 
					$end_date = $retarr[2]; 
				} 
				else 
				{
					//($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
					$retarr = $this->prorate($didinfo['monthlycost']);
					$cost = 0; 
					$start_date = $retarr[1]; 
					$end_date = $retarr[2]; 
				}
				$callstart = $this->prettytimestamp();
				$notes = '';
				if ($didinfo['chargeonallocation'] == 0) 
				{
					$dest = gettext("DID:") . $did . gettext(" from ")
							. $start_date . gettext(" to ") . $end_date;
	        		$this->post_cdr('',$accountdata['number'],'',$dest,'','',$cost,$callstart,$accountinfo['postexternal'],'',$notes);
				}
				$accountdata = $this->get_account($astpp_db,$accountdata['reseller']);
			}
			$didinfo = $this->get_did($did);
			if ($didinfo['prorate'] == 1) 
			{
				//($cost,$start_date,$end_date) = &prorate($didinfo['monthlycost']);
				$retarr = $this->prorate($didinfo['monthlycost']);
				$cost = $retarr[0]; 
				$start_date = $retarr[1]; 
				$end_date = $retarr[2]; 				
			} 
			else 
			{
				//($null,$start_date,$end_date) = &prorate($didinfo['monthlycost']);
				$retarr = $this->prorate($didinfo['monthlycost']);
				$cost = 0; 
				$start_date = $retarr[1]; 
				$end_date = $retarr[2]; 				
			}
			$callstart = $this->prettytimestamp();
			$notes = '';
			if ($didinfo['chargeonallocation'] == 0) 
			{
				$dest = gettext("DID:") . $did . gettext(" from ") . $start_date . gettext(" to ") . $end_date;
	       		$this->post_cdr('',$accountdata['number'],'',$dest,'','',$cost,$callstart,$accountdata['postexternal'],'',$notes);
			}
		} 
		else 
		{
			//($cost,$start_date,$end_date) = &prorate($didinfo['monthlycost']);
			$retarr = $this->prorate($didinfo['monthlycost']);
			$cost = $retarr[0]; 
			$start_date = $retarr[1]; 
			$end_date = $retarr[2]; 				
			
			$callstart = $this->prettytimestamp();
			$notes = '';
			if ($didinfo['chargeonallocation'] == 0) 
			{
				$dest = gettext("DID:") . $did . gettext(" from ") . $start_date . gettext(" to ") . $end_date;
	        	$this->post_cdr('',$accountinfo['number'],'',$dest,'','',$cost,$callstart,$accountinfo['postexternal'],'',$notes);
			}
		}
		$this->db->query("UPDATE dids SET allocation_bill_status = 1 WHERE number = " . $this->quote($did));		
		
	}
	
	//Apply DID to customers account and charge them and the resellers as appropriate.
	function purchase_did($did,$account)
	{
		$didinfo = $this->get_did($did);
		$accountinfo = $this->get_account($account);
		if ($didinfo['account'] != $accountinfo['reseller'] && $didinfo['account'] != "") 
		{
			return array('error',gettext("This DID is owned by another customer already!"));
		}
		if ($accountinfo['reseller'] != "") 
		{
			$accountdata = $this->get_account($account);
			while ($accountdata['reseller']) 
			{
				$didinfo = $this->get_did_reseller_new($did,$accountdata['reseller']);
				$did_min_available = $didinfo['monthlycost'] + $didinfo['setup'] + $didinfo['disconnectionfee'];
				$credit = $this->accountbalance( $accountdata['number'] ); //# Find the available credit to the customer.
				//print STDERR "Account Balance: " . $credit . "\n"  if $config->{debug} == 1;
				$credit = ($credit * -1) + ($accountdata['credit_limit']);//  # Add on the accounts credit limit.
				if ($credit < $did_min_available) 
				{
					return  array('error', gettext ("Account: " . $accountdata->{number} . " does not have enough funds available."));
				} 
				else 
				{
					$accountdata = $this->get_account($accountdata['reseller']);
				}
			}
			$accountdata = $this->get_account($account);
			while ($accountdata['reseller'] != "") 
			{
				$didinfo = $this->get_did_reseller_new($did,$accountdata['reseller']);
				if ($didinfo['prorate'] == 1) 
				{
					//($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
					$retarr = $this->prorate($didinfo['monthlycost']);
					$cost = $retarr[0]; 
					$start_date = $retarr[1]; 
					$end_date = $retarr[2]; 									
				} 
				else 
				{
//					($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
					$retarr = $this->prorate($didinfo['monthlycost']);
					$cost = 0;
					$start_date = $retarr[1]; 
					$end_date = $retarr[2]; 														
				}
				$callstart = $this->prettytimestamp();
				$notes = '';
				if ($didinfo['chargeonallocation'] == 1) 
				{
					$dest = gettext("DID:") . $did . gettext(" from ")
						. $start_date . gettext(" to ") . $end_date;
	        		$this->post_cdr('',$accountdata['number'],'',$dest,'','',$cost,$callstart,$accountinfo['postexternal'],'',$notes);
				}
	        	if ($didinfo['setup'] != 0) 
	        	{
					$dest = gettext("DID:") . $did . gettext(" setup fee");
					$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['setup'],$callstart,$accountinfo['postexternal'],'',$notes);
				}
				$accountdata = $this->get_account($accountdata['reseller']);
			}
			$didinfo = $this->get_did($did);
			if ($didinfo['prorate'] == 1)
			{ 
				//($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
				$retarr = $this->prorate($didinfo['monthlycost']);
				$cost = $retarr[0]; 
				$start_date = $retarr[1]; 
				$end_date = $retarr[2]; 									
			} 
			else 
			{
				//($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
				$retarr = $this->prorate($didinfo['monthlycost']);
				$cost = 0;
				$start_date = $retarr[1]; 
				$end_date = $retarr[2]; 														
			}
			$callstart = $this->prettytimestamp();
			$notes = '';
			if ($didinfo['chargeonallocation'] == 1) 
			{
				$dest = gettext("DID:") . $did . gettext(" from ")
					. $start_date . gettext(" to ") . $end_date;
	        	//&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$cost,$callstart,$accountdata->{postexternal},'',$notes);
	        	$this->post_cdr('',$accountdata['number'],'',$dest,'','',$cost,$callstart,$accountinfo['postexternal'],'',$notes);
			}
        	if ($didinfo->{setup} != 0) 
        	{
				$dest = gettext("DID:") . $did . gettext(" setup fee");
				//&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{setup},$callstart,$accountdata->{postexternal},'',$notes);
				$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['setup'],$callstart,$accountinfo['postexternal'],'',$notes);
			}
		} 
		else 
		{
			$did_min_available = $didinfo['monthlycost'] + $didinfo['setup'] + $didinfo['disconnectionfee'];
			$credit = $this->accountbalance( $accountinfo['number'] ); //# Find the available credit to the customer.
			//print STDERR "Account Balance: " . $credit . "\n" if $config->{debug} == 1;
			$credit = ($credit * -1) + ($accountinfo['credit_limit']);  # Add on the accounts credit limit.
			if ($credit < $did_min_available) 
			{
				return array('error', gettext ("Account: " . $accountinfo->{number} . " does not have enough funds available.") );
			} 
			else 
			{
				//my ($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
				$retarr = $this->prorate($didinfo['monthlycost']);
				$cost = 0;
				$start_date = $retarr[1]; 
				$end_date = $retarr[2]; 														
				
				$callstart = $this->prettytimestamp();
				$notes = '';
				if ($didinfo['chargeonallocation'] == 1) 
				{
					$dest = gettext("DID:") . $did . gettext(" from ")
						. $start_date . gettext(" to ") . $end_date;
	        		//&post_cdr($astpp_db,$config,'',$accountinfo->{number},'',$dest,'','',$cost,$callstart,$accountinfo->{postexternal},'',$notes);
	        		$this->post_cdr('',$accountdata['number'],'',$dest,'','',$cost,$callstart,$accountinfo['postexternal'],'',$notes);
				}
	        	if ($didinfo['setup'] != 0) 
	        	{
					$dest = gettext("DID:") . $did . gettext(" setup fee");
					//&post_cdr($astpp_db,$config,'',$accountinfo->{number},'',$dest,'','',$didinfo->{setup},$callstart,$accountinfo->{postexternal},'',$notes);
					$this->post_cdr('',$accountdata['number'],'',$dest,'','',$didinfo['setup'],$callstart,$accountinfo['postexternal'],'',$notes);
				}
			}
		}
		# If we got this far the cdrs have been posted and we're ready 
		$tmp = 	 "UPDATE dids SET extensions = "
		         . $this->quote($accountinfo['extension'])
		         . " WHERE number = "
		         . $this->quote($did);
		
		$this->db->query($q);
		
		$status = array('success','DID mapped to extension successfully!');
/*		
		if ($this->do($tmp)) {
			$status .= gettext("DID mapped to extension successfully!");
		} else {
			$status .= gettext("DID failed to map to extension!");
		}	
*/		
		$tmp =
		           "UPDATE dids SET account = "
		         . $this->quote($account)
		         . " WHERE number = "
		         . $this->quote($did);
		         
		$status[1] .= "DID Assigned Successfully!";
		 /*
		if ($this->do($tmp)) {
			$status .= gettext("DID Assigned Successfully!");
		} else {
			$status .= gettext("DID Failed to Assign!");
		}	
		*/
		return $status;
		
	}	
	
	
	/*function list_providers()
	{
		$providers = array();
		$q = "SELECT number FROM accounts WHERE status = 1";// AND type = 3
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$providers[] = $row['number'];
			}
		}
		return $providers;
	}*/		
	
	//Return a list of accounts.  This is currently only(I think) used by the "List Accounts" page.
	function list_accounts_selective($reseller, $type)
	{
	    if ( $type == -1 ) 
	    {
	        $tmp =
	            "SELECT number FROM accounts WHERE status < 2 AND reseller = "
	          . $this->quote($reseller)
	          . " ORDER BY number";
	        //print STDERR "$tmp\n";
	    }
	    elseif ( $type == 0 || !$type ) 
	    {
	        $tmp =
				"SELECT number FROM accounts WHERE status < 2 AND type = 0 AND reseller = "
	          . $this->quote($reseller)
	          . " ORDER BY number";
	        //print STDERR "$tmp\n";
	    }
	    elseif ( $type > 0 ) 
	    {
	        $tmp =
			"SELECT number FROM accounts WHERE status < 2 AND type = '$type' AND reseller = "
	          . $this->quote($reseller)
	          . " ORDER BY number";
	        //print STDERR "$tmp\n";
    	}

    	return $this->db_get_array($tmp,'number');
	}	
	
	function list_accounts($reseller)
	{		
		$accounts = array();
		$q = "SELECT number FROM accounts WHERE status < 2 AND reseller = '".$this->db->escape_str($reseller)."'  ORDER BY number";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$accounts[] = $row['number'];
			}
		}
		return $accounts;
	}
			
	function list_all_accounts()
	{		
		$accounts = array();
		$q = "SELECT number FROM accounts WHERE status < 2 ORDER BY number";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$accounts[] = $row['number'];
			}
		}
		return $accounts;
	}
			
	function list_pricelists($reseller)
	{
		$q = "SELECT name FROM trunks WHERE status = 1";
		if ( !$reseller ) 
		{
	        $tmp = "SELECT name FROM pricelists WHERE status < 2 AND reseller IS NULL ORDER BY name";
	    }
	    else 
	    {
	        $tmp = "SELECT name FROM pricelists WHERE status < 2 AND reseller = "
	          	. $this->quote($reseller) . " ORDER BY name";
    	}		
		return $this->db_get_array($tmp,'name');
	}
		
	function get_astpp_cdr($id)
	{
		$q = "SELECT * FROM cdrs WHERE id = " . $this->quote($id);
		return $this->db_get_arrays($q);
	}
		
	function update_astpp_balance($account, $balance)
	{
		$q = "UPDATE accounts SET balance = "
          . $this->quote($balance)
          . " WHERE number = "
          . $this->quote($account);
          
        $this->db->query($q);
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
		
	function get_pricelist($pricelist)
	{
		$q = "SELECT * FROM pricelists WHERE name =" . $this->quote($pricelist);
		return $this->db_get_arrays($q);
	}
		
	function get_cc_brand($brand)
	{
		$q = "SELECT * FROM callingcardbrands WHERE name = " . $this->quote($brand). " AND status = 1";
		return $this->db_get_arrays($q);
	}
		
	function search_for_route($destination,$pricelist)
	{
    	$tmp = "SELECT * FROM routes WHERE "
          . $this->quote($destination)
          . " RLIKE pattern AND status = 1 AND pricelist = "
          . $this->quote($pricelist)
          . " ORDER BY LENGTH(pattern) DESC LIMIT 1";
		
    	return $this->db_get_arrays($tmp);
	}
		
	function get_charge($chargeid)
	{
    	$tmp = "SELECT * FROM charges WHERE id = ". $this->quote($chargeid). " AND status < 2 LIMIT 1";
		
    	return $this->db_get_arrays($tmp);
	}
		
	function list_account_charges($number)
	{
    	$tmp = "SELECT * FROM charge_to_account WHERE status < 2 AND cardnum = "
          . $this->quote($number);
		
    	return $this->db_get_arrays($tmp);
	}	
	
	function list_applyable_charges()
	{		
		$q = "SELECT * FROM charges WHERE status < 2 AND pricelist = ''";
		$item_arr = array();
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				//$item_arr[] = $row[$colname];
				if ( $row['charge'] > 0 ) 
				{
            		$row['charge'] = $this->common_model->calculate_currency($row['charge']);
            		//$row->{charge} = sprintf( "%.4f", $row->{charge} );
        		}
        		$item_arr[$row['id']] = $row['description'].' - '.$row['charge'];
 			}
		}
		return $item_arr;
	}
		
	function list_pricelist_charges($pricelist)
	{
		$q = "SELECT id FROM charges WHERE status < 2 AND pricelist = " . $this->quote($pricelist);
		return $this->db_get_arrays($q,'id');
	}
	
	// Refill and ASTPP account.
	function refill_account($account, $amount) 
	{
		$data = array(
   			'uniqueid' => 'N/A' ,
   			'cardnum' => $this->db->escape_str($account) ,
   			'callednum' => 'Refill Account',
   			'credit' => $amount,
   			'callstart' => $this->prettytimestamp()
		);

		$this->db->insert('cdrs', $data);		 
   	}
	
   	function write_callingcard_cdr($cardinfo,  $clid,   $destination, $status, $callstart, $charge, $answeredtime)
   	{
        if (!$status) $status = gettext("N/A"); 
        
        $sql =
			"INSERT INTO callingcardcdrs (cardnumber,clid,destination,disposition,callstart,seconds,"
          . "debit) VALUES ("
	  	  . $this->quote( $cardinfo->{cardnumber} ) . ", "
          . $this->quote($clid) . ", "
          . $this->quote($destination) . ", "
          . $this->quote($status) . ", "
          . $this->quote($callstart) . ", "
          . $this->quote($answeredtime) . ", "
          . $this->quote($charge) . ")";
          
        $this->db->query($sql);   		
   	}
	
   	function write_account_cdr($account, $amount, $description, $timestamp, $answeredtime, $uniqueid, $clid, $pricelist, $pattern)
   	{
	    if(!$answeredtime) $answeredtime = "0" ;
	    if (!$uniqueid) $uniqueid = "N/A" ;
	    if (!$clid) $clid = "N/A" ;
	    if ($timestamp == '') $timestamp = $this->prettytimestamp();        

    	$tmp =
			"INSERT INTO cdrs (uniqueid, cardnum, callednum, debit, billseconds, callstart,callerid,pricelist,pattern) VALUES ("
	      . $this->quote($uniqueid) . ", "
	      . $this->quote($account) . ","
	      . $this->quote($description) . ", "
	      . $this->quote($amount) . ", "
	      . $this->quote($answeredtime) . ", "
		  . $this->quote($timestamp) . ", "
	      . $this->quote($clid) . ","
	      . $this->quote($pricelist) . ","
	      . $this->quote($pattern) . ")";
          
        $this->db->query($tmp);   		
   	}
   	
	function accounts_total_balance($reseller) 
	{		
		$debit = 0;
		$credit = 0;
		if ($reseller == "") 
		{
			$q = "SELECT SUM(debit) as val1 FROM cdrs WHERE status NOT IN (1, 2)";
			$debit = $this->db_get_item($q,'val1');
		
			$q = "SELECT SUM(credit)  as val1 FROM cdrs WHERE status NOT IN (1, 2)";
			$credit = $this->db_get_item($q,'val1');
			
			$tmp   = "SELECT SUM(balance) as val1 FROM accounts WHERE reseller = ''";			
		}
		else 
		{
			$tmp   = "SELECT SUM(balance) as val1 FROM accounts WHERE reseller = " . $this->quote($reseller);
		}
		$posted_balance = $this->db_get_item($tmp,"val1");
	
		$balance = ( $debit - $credit + $posted_balance );
    	return $balance;		
	}    
   	
	function list_available_dids($account)
	{
		$accountinfo = $this->get_account( $account);
		$didlist_ist = $this->list_dids_number_account("");
		$resellerdidlist_second = $this->list_dids_number_account($accountinfo['reseller']);
		//$didlist  += $resellerdidlist;
		
		$didlist = array_merge($didlist_ist, $resellerdidlist_second);
		
		$availabledids = array();
		if ($accountinfo['reseller']  != "") 
		{
			//my (@availabledids,$sql);
			foreach($didlist as $did)
			{
	      		$tmp =
		        "SELECT dids.number AS number, "
		        . "reseller_pricing.monthlycost AS monthlycost, "
		        . "reseller_pricing.prorate AS prorate, "
		        . "reseller_pricing.setup AS setup, "
		        . "reseller_pricing.cost AS cost, "
		        . "reseller_pricing.disconnectionfee AS disconnectionfee "
		        . "FROM dids,reseller_pricing "
		        . "WHERE dids.number = " . $this->quote($did)
		        . " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
		        . $this->quote($accountinfo['reseller']) . " AND reseller_pricing.note = "
		        . $this->quote($did);			       		        
				//print STDERR "$tmp\n";
				
		        //$availabledids += $this->db_get_array($tmp,'number');
				$availabledids[]= $this->db_get_array($tmp,'number');
				//array_push($availabledids, $this->db_get_array($tmp,'number'));
			}
			return $availabledids;
		}
	    else 
	    {
			$didlist[] = array_merge($didlist_ist, $resellerdidlist_second);
			return $didlist;
		}		
	}
	
	function get_reseller($reseller)
	{
    	$tmp = "SELECT * FROM resellers WHERE name= "
      		. $this->quote($reseller)
      		. " AND status = 1";
		
    	return $this->db_get_arrays($tmp);
	}
	function get_reseller_including_closed($reseller)
	{
    	$tmp = "SELECT * FROM resellers WHERE name= "
      		. $this->quote($reseller);
		
    	return $this->db_get_arrays($tmp);
	}
	function get_provider($provider)
	{
    	$tmp = "SELECT * FROM providers WHERE  status = 1 AND name= "
      		. $this->quote($provider);
		
    	return $this->db_get_arrays($tmp);
	}
		
	function get_counter($package, $cardnum)
	{
    	$tmp = "SELECT * FROM counters WHERE package = "
          . $this->quote($package)
          . " AND account = "
          . $this->quote($cardnum) 
	  	  . " AND status = 1";
		
    	return $this->db_get_arrays($tmp);
	}
		
	function get_package($carddata, $number)
	{
    	$tmp = "SELECT * FROM packages WHERE "
          . $this->quote($number)
          . " RLIKE pattern AND pricelist = "
          . $this->quote( $carddata['pricelist'] )
          . " ORDER BY LENGTH(pattern) DESC LIMIT 1";
		
    	return $this->db_get_arrays($tmp);
	}	
	function count_dids($test)
	{
    	$tmp = "SELECT COUNT(*) as val1 FROM dids ".$test;		
    	return $this->db_get_item($tmp,'val1');
	}	
	function count_callingcards($where,$field='COUNT(*)')
	{
		$tmp = "SELECT $field as val FROM callingcards ".$where;		
		return $this->db_get_item($tmp,'val');
	}
	function count_accounts($test)
	{
	      $tmp = "SELECT COUNT(*) as val1 FROM accounts ".$test;	
		return $this->db_get_item($tmp,'val1');	
	}		
	function list_callingcards()
	{
    	$tmp = "SELECT cardnumber FROM callingcards WHERE status < 2";		
    	return $this->db_get_array($tmp,'cardnumber');
	}		
	function list_callingcards_account($account)
	{
    	$tmp = "SELECT cardnumber FROM callingcards WHERE status < 2 AND account = ". $this->quote($account);		
    	return $this->db_get_array($tmp,'cardnumber');
	}		
	function list_cc_brands()
	{
    	$tmp = "SELECT name FROM callingcardbrands WHERE status = 1 AND (reseller IS NULL OR reseller = '')";		
    	return $this->db_get_array($tmp,'name');
	}		
	function list_cc_brands_reseller($reseller)
	{
    	$tmp = "SELECT name FROM callingcardbrands WHERE status = 1 AND (reseller IS NULL OR reseller = ". $this->quote($reseller).")";		
	echo $tmp;
    	return $this->db_get_array($tmp,'name');
	}		
	function list_resellers()
	{
    	$tmp = "SELECT name FROM resellers WHERE status = 1";		
    	return $this->db_get_array($tmp,'name');
	}		
	function list_booths($callshop)
	{
    	$tmp = "SELECT * FROM booths WHERE status = 1";		
    	return $this->db_get_array($tmp,'name');
	}	
	
	function get_callingcard($cardno)
	{
    	$tmp = "SELECT * FROM callingcards WHERE cardnumber = "
          . $this->quote($cardno);
    	return $this->db_get_arrays($tmp);
	}		
	//Transfer funds from one callingcard to another
	function transfer_funds($sourcecard, $sourcecardpin, $destcard, $destcardpin)
	{
		$sourcecardinfo = $this->get_callingcard( $sourcecard, $config );
		$sourcecardstatus = $this->check_card_status( $sourcecardinfo );
		# This subroutine returns the status of the card:
		if ( $sourcecardstatus != 0 ) 
		{
			return 1;
		} 
		elseif ( $sourcecardinfo['pin'] != $sourcecardpin) 
		{
			return 1;
		}
		$destcardinfo = $this->get_callingcard( $destcard, $config );
		$destcardstatus = $this->check_card_status( $destcardinfo );
	
		# If we get this far that means that both the source and the destination card are ok.
		if ( $destcardstatus != 0 ) 
		{
			return 1;
		} 
		elseif ( $destcardinfo['pin'] != $destcardpin) 
		{
			return 1;
		}
		$tmp = "UPDATE callingcards SET used = ". $this->quote( $sourcecardinfo['value'] )
	          . " WHERE cardnumber = ". $this->quote( $sourcecardinfo['cardnumber'] );
		$this->db->query($tmp);  
		 		
		$tmp = "UPDATE callingcards SET status = '2'"
	          . " WHERE cardnumber = ". $this->quote( $sourcecardinfo['cardnumber'] );
		$this->db->query($tmp);   		

		$tmp = "UPDATE callingcards SET value = "
			  . $this->quote( ($sourcecardinfo['value'] - $sourcecardinfo['used']) + $destcardinfo['value'] )
	          . " WHERE cardnumber = "
	          . $this->quote( $destcardinfo['cardnumber'] );
		$this->db->query($tmp);   		
		return 0;	
	}
	
	//Check to see if a calling card is ok to use.
	//Check a few things before saying the card is ok.
	// This subroutine returns the status of the card:
	// Status 0 means the card is ok,
	// Status 1 means the card is in use.
	// Status 2 means the card has expired.
	// Status 3 means the card is empty.
	function check_card_status($cardinfo)
	{
		$now = $this->db_get_item("SELECT NOW() as val1",'val1');
		//print STDERR "Present Time: $now\n";
		//print STDERR "Expiration Date: $cardinfo->{expiry}\n";
		//print STDERR "Valid for Days: $cardinfo->{validfordays}\n";
		//print STDERR "First Use: $cardinfo->{firstused}\n";
		if ( $cardinfo['inuse'] != 0 )
		{                
			return 1; #Status 1 means card is in use.
		}
		if ( $cardinfo['validfordays'] > 0 ) 
		{
			$now = $this->db_get_item("SELECT NOW() as val1",'val1');
			if ( $now > $cardinfo['expiry'] && $cardinfo['expiry'] != "0000-00-00 00:00:00" ) 
			{
				$sql =
				  "UPDATE callingcards SET status = 2 WHERE cardnumber = "
				  . $this->quote( $cardinfo['cardnumber'] );
				$this->db->query($sql);   	
				$sql =
				  "DELETE FROM ani_map WHERE account = "
				  . $this->quote( $cardinfo['cardnumber'] );
				$this->db->query($sql);  
				return 2; #Status 2 means card has expired
			}
		}
		if ( $cardinfo['value'] - $cardinfo['used'] < 100 )
		{    # don't allow this if the card is down to the last penny.
			return 3; #Status 3 means card is empty
		}
		return 0;		
	}
//################### CallShop Support Begins ######################
	function list_callshops()
	{
    	$tmp = "SELECT number FROM accounts WHERE status < 2 AND type = 5 ORDER BY number";		
    	return $this->db_get_array($tmp,'number');
	}	
	function list_callshops_reseller($reseller)
	{
    	$tmp = "SELECT number FROM accounts WHERE status = 1 AND type = 5 AND reseller = " . $this->quote($reseller) . " ORDER BY number";		
    	return $this->db_get_array($tmp,'number');
	}	
	function list_booths_callshop($reseller)
	{
    	$tmp = "SELECT number FROM accounts WHERE type = 6 AND reseller = " . $this->quote($reseller) . " AND status < 2 ORDER by number";		
    	return $this->db_get_array($tmp,'number');
	}	
	function get_callshop($accountno)
	{
    	$sql ="SELECT * FROM callshops WHERE name = "
          . $this->quote($accountno)
          . " AND status = 1" ;		
    	return $this->db_get_arrays($sql);
	}	
	function get_charges($account,$params)
	{
	    if ($params['startdate'] && $params['enddate']) 
	    {
	    	$tmp =
	        "SELECT * FROM cdrs WHERE cardnum = "
	      	. $this->quote($account)
	      	. " AND status = 0"
	      	. " AND callstart >= DATE(" . $this->quote($params['startdate']) . ")"
	      	. " AND callstart <= DATE(" . $this->quote($params['enddate']) . ")"
	      	. " ORDER BY callstart";
	    } 
	    elseif ($params['startdate']) 
	    {
	    	$tmp = "SELECT * FROM cdrs WHERE cardnum = "
	      		. $this->quote($account)
	      		. " AND status = 0"
	      		. " AND callstart >= DATE(" . $this->quote($params['startdate']) . ")"
	      		. " ORDER BY callstart";
	    } 
	    elseif ($params['enddate']) 
	    {
		    $tmp =
		        "SELECT * FROM cdrs WHERE cardnum = "
		      . $this->quote($account)
		      . " AND status = 0"
		      . " AND callstart <= DATE(" . $this->quote($params['enddate']) . ")"
		      . " ORDER BY callstart";
	    } 
	    else 
	    {
		    $tmp =
		        "SELECT * FROM cdrs WHERE cardnum = "
		      . $this->quote($account)
		      . " AND status = 0"
		      . " ORDER BY callstart";
	    }
    	return $this->db_get_array($tmp,'id');
	}	
	
//#######  Freeswitch Integration Starts ###############

	function get_sip_account_freeswitch($directory_id)
	{
		
	}
   	function list_sip_account_freeswitch($name, $cc)
   	{
   		
   	}
	function finduniquesip_freeswitch($name)
	{
		
	}
	function add_sip_user_freeswitch($name, $secret, $username,$params,$cc)
	{
		
	}
//#######  Freeswitch Integration ends ###############

	
// astpp-admin.cgi
//
	function generate_accounts($params)
	{
		$config = get_config();
		
// 	    $description = gettext("Account Setup");
// 	    $cardlist = $this->get_account_including_closed($params['number'] );
// 	    if ( !$cardlist['number'] ) 
// 	    {
// 		    $this->addaccount( $config, $params );
// 		    
// 		    if ( $params['accounttype'] == 1 ) 
// 		    {
// 		    $this->add_pricelist( $params['number'], 6, 0,$params['number'] );
// 		    $this->add_reseller($params['number'],$params['posttoexternal'] );
// 		    }
// 		    if ( $params['accounttype'] == 5 ) 
// 		    {
// 		    $this->add_pricelist( $params['number'], 6, 0,$params['number'] );
// 			    $this->add_reseller($params['number'],$params['posttoexternal'] );
// 		    }
// 		    if ( $config['email'] == 1 && $params['accounttype'] == 0 ) 
// 		    {
// 			    $params->{extension} = $params->{number};
// 			    $params->{secret} = $params->{accountpassword};
// 		    //&email_add_user( $astpp_db, '', $config, $params );
// 		    }
// 		    $timestamp = $this->prettytimestamp();
// 		    $sql = "INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
// 		  . $this->quote( $params->{number} ) . ","
// 		  . $this->quote($description) . ","
// 		  . $this->quote( $params->{pennies} * 100 ) . ","
// 		  . $this->quote($timestamp)
// 		  . ")";
// 		
// 		  $this->db->query($sql);
// 		    $cardlist =$this->get_account_including_closed($params['number'] );
// 		    if ( $cardlist['number'] ) 
// 		    {
// 		    $status .= "Account ".$params['number']." added successfully" . "<br>";
// 		    }
// 		    else 
// 		    {
// 		    $status .= "Account ".$params['number']." Failed to Add!" . "<br>";
// 		    }
// 	    }
// 	    elseif ( $cardlist['status'] != 1 ) 
// 	    {
// 		    if (
// 			$this->db_do(
// 			    "UPDATE accounts SET status = 1 WHERE number ="
// 			      . $this->quote( $params->{number} )
// 			    )
// 		      )
// 		    {
// 			$status .=
// 			    gettext("Account:")
// 			  . " $params->{number} "
// 			  . gettext("has been (re)activated")
// 			  . "<br>\n";
// 		    }
// 		    else {
// 			$status .=
// 			    gettext("Account:")
// 			  . " $params->{number} "
// 			  . gettext("failed to (re)activate!")
// 			  . "<br>\n";
// 		    }
// 		    if ( $cardlist['type'] == 1 ) {
// 			$this->db_do( "UPDATE resellers SET status = 1 WHERE name ="
// 			      . $this->quote( $params->{number} ) );
// 		    }
// 		    if ( $config['email'] == 1 ) {
// 			//$this->email_reactivate_account( $astpp_db, '', $config, $params, );
// 		    }
// 	    }
// 	    else 
// 	    {
// 		    $status .=
// 		gettext("Account:")
// 	      . " $params->{number} "
// 	      . gettext("exists already!!")
// 	      . "<br>\n";
// 	    }
// 	    return $status;
// 		    
	    }	
		
		function count_unbilled_cdrs($account) {
			try
			{
			    $this->db_fscdr = Common_model::$global_config['fscdr_db'];
// 			    echo "<pre>";print_r($this->db_fscdr);
			    if($this->db_fscdr->conn_id!='')
			    {							    
				$query = $this->db_fscdr->query("SELECT COUNT(*) as val1 FROM ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." WHERE cost = 'error' OR accountcode IN (" . $account . " 0) AND cost ='none'" );
				if($query!='')
				{
				    if($query->num_rows() > 0)
				    {
					    $row = $query->row_array();
					    return $row['val1'];
				    }
				}					
			    }
			} catch(Exception $e) {		    
			}
		}
	
	function get_call_count($name)
	{
		  $query = $this->db->query("SELECT COUNT(*) as val1 FROM cdrs WHERE cardnum = '".$name."' AND status = 0");
		  if($query->num_rows() > 0)
			{
				$row = $query->row_array();
				return $row['val1'];
			}
		  
	}
}//end class
?>
