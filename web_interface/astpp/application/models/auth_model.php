<?php 
class Auth_model extends CI_Model
{
	
	function Auth_model()
	{
		parent::__construct();
		
	}
	function verify_login($username,$password)
	{		
		$q = "SELECT COUNT(*) as cnt FROM accounts WHERE number = '";
		$q .= $this->db->escape_str($username);
		$q .= "' AND password = '" . $this->db->escape_str($password);
		$q .= "' AND status = 1 AND type IN (1,2,3,4,5,0)";
		
		$query = $this->db->query($q);
		//echo $this->db->last_query();

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			if( $row->cnt > 0)
			{
				$this->session->set_userdata('user_name', $username);
				return 1;
			}else{
				return 0;
			}
		}
		
		return 0;
	}
	
	  
	
	
	/*function get_account_cc($accountdata)
	{
		$q = "SELECT * FROM accounts WHERE cc = '".$this->db->escape_str($accountdata)."' AND status = 1";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}
		return NULL;
	}*/
	
	
	
	/*function get_did($number)
	{
		$q = "SELECT * FROM dids WHERE number = '".$this->db->escape_str($number)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}
		return NULL;
	}*/
		
	/*function get_did_list()
	{
		$q = "SELECT * FROM dids order by number asc";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			//$row = $query->row();
			return $query;			
		}
		return NULL;
	}*/	
	
			
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
	}
			
	function list_providers_select($default)
	{
		$ret_html = '';
		
		$providers = $this->list_providers();
		foreach ($providers as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}
		
		return  $ret_html;
	}*/
	
	
	/*function list_accounts($reseller)
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
	}*/
			
	/*function list_accounts_select($default)
	{
		$ret_html = '';
		
		$providers = $this->list_providers();
		foreach ($providers as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}
		
		return  $ret_html;
	}*/
	
	/*function list_accounts_info_table()
	{
		$item_arr = array();
		$q = "SELECT * FROM accounts";
		$query = $this->db->query($q);		
		$ret_html = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				//$item_arr[] = $row['name'];
				$ret_html .= '<TR>';
				$ret_html .= '<TD>'.$row['cc'].'</td>';
				$ret_html .= '<TD>'.$row['number'].'</td>';
				$ret_html .= '<TD>'.$row['pricelist'].'</td>';
				$ret_html .= '<TD>'.'0'.'</td>';
				$ret_html .= '<TD>'.$row['credit_limit'].'</td>';
				$ret_html .= '<TD>'.$row['sweep'].'</td>';
				if($row['posttoexternal'] == 1)
					$ret_html .= '<TD>'.'Yes'.'</td>';
				else 	
					$ret_html .= '<TD>'.'No'.'</td>';
				$ret_html .= '<TD>'.$row['reseller'].'</td>';
	            $ret_html .= '</TR>';            				
			}
		}
		return $ret_html;
	}*/
	
	
	
	/*function sweep_list($default)
	{
		$ret_html = '';
		$index = 0;
		$sweeplist = array('daily','weekly','monthly','quarterly','semi-annually','annually');
		foreach ($sweeplist as $elem)
		{
			$ret_html .= '<option value="'.$index.'"';
			if($index == $default)
				$ret_html .= ' selected="selected"';
			$ret_html .= ">$elem</option>";
			$index = $index + 1;
		}
		return $ret_html;
	}*/
	
	
	/*function sweep_get_name($value)
	{
		$sweeplist = array('daily','weekly','monthly','quarterly','semi-annually','annually');
		//$sweeplist = array('daily','weekly','monthly','quarterly','semi-annually','annually');
		$index = 0;
		foreach ($sweeplist as $elem)
		{
			if($index == $value)
				return $elem;
			$index = $index + 1;			
		}
		return '';
	}*/
	

	/*function list_callshops()
	{		
		$item_arr = array();
		$q = "SELECT number FROM accounts WHERE status < 2 AND type = 5 ORDER BY number";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['number'];
			}
		}
		return $item_arr;
	}*/
			
	/*function list_callshops_reseller($reseller)
	{		
		$item_arr = array();
		$q = "SELECT number FROM accounts WHERE status = 1 AND type = 5 AND reseller='".$this->db->escape_str($reseller)."'  ORDER BY number";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['number'];
			}
		}
		return $item_arr;
	}	*/
	
	/*function list_callshops_select($reseller,$default)
	{
		$ret_html = '';
		if($reseller == '')
			$callshops = $this->list_callshops();
		else 
			$callshops = $this->list_callshops_reseller($reseller);
			
		foreach ($callshops as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}
		
		return  $ret_html;
	}*/
	
	/*function list_reseller()
	{		
		$item_arr = array();
		$q = "SELECT name FROM resellers WHERE status = 1";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['name'];
			}
		}
		return $item_arr;
	}	*/
	
	/*function list_reseller_select($default)
	{
		$ret_html = '';
		
	//	if($reseller == '')
	//		$callshops = $this->list_callshops();
	//	else 
	//		$callshops = $this->list_callshops_reseller($reseller);

		$resellers = $this->list_reseller();
		foreach ($resellers as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= 'selected="selected"';
			$ret_html .= ">$elem</option>";
		}
		
		return  $ret_html;
	}*/
	
	/*function list_pricelists_select($reseller,$default)
	{
		$ret_html = '';
		$options = $this->list_pricelists($reseller);
		foreach ($options as $elem)
		{
			$ret_html .= '<option value="'.$elem.'"';
			if($elem == $default)
				$ret_html .= ' selected="selected"';
			$ret_html .= ">$elem</option>";
			
		}
		return $ret_html;
	}
    function list_pricelists($reseller)
    {	
    	//SELECT name FROM pricelists WHERE status < 2 AND reseller IS NULL ORDER BY name
    	//SELECT name FROM pricelists WHERE status < 2 AND reseller = ". $astpp_db->quote($reseller). " ORDER BY name
        	//SELECT id, name FROM country
            $this->db->select('name');
            $query = $this->db->get('pricelists');
            
            $options = array();
            
			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$options[] = $row['name'];
				}
			}
           	return $options;               
    }*/
	
	
	
	/*function account_type_select()
	{
		$index = 0;
		$ret_html = '';
		$default = '';
		$typelist = array('User','Reseller','Administrator','Provider','Customer Service','CallShop'); //0-5
		foreach ($typelist as $elem)
		{
			$ret_html .= '<option value="'.$index.'"';
			if($elem == $default)
				$ret_html .= ' selected="selected"';
			$ret_html .= ">$elem</option>";
			
			$index = $index+1;
		}
		return $ret_html;		
	}*/
	
	/*function account_type_name($type)
	{
		$index = 0;
		$ret_html = '';
		$default = '';
		$typelist = array('User','Reseller','Administrator','Provider','Customer Service','CallShop'); //0-5
		foreach ($typelist as $elem)
		{
			if($type == $index)
				return $elem;
			$index = $index+1;
		}
		return '';		
	}*/
	
	/*function prettytimestamp() 
	{
		$date = new DateTime();
		return $date->format('Y-m-d H:i:s');		
	}	*/
	
	// Refill and ASTPP account.
	
	
	/*function refill_account($account, $amount) 
	{
		$data = array(
   			'uniqueid' => 'N/A' ,
   			'cardnum' => $this->db->escape_str($account) ,
   			'callednum' => 'Refill Account',
   			'credit' => $amount,
   			'callstart' => $this->prettytimestamp()
		);

		$this->db->insert('cdrs', $data);		 
   }*/
   	
	// Refill and ASTPP account.
	/*function remove_account($account, $reseller='') 
	{
		$data = array();
		$data['status'] = 2;
		if($reseller == '')
			$this->db->update('accounts', $data, array('number' => $account));
		else 
			$this->db->update('accounts', $data, array('number' => $account,'reseller' => $reseller));
   }*/	

   /*	function list_account_charges_table($number)
	{
		$item_arr = array();
		$q = "SELECT * FROM charge_to_account WHERE status < 2 AND cardnum ='".$this->db->escape_str($number)."'";
		$query = $this->db->query($q);		
		$ret_html = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				//$item_arr[] = $row['name'];
				$ret_html .= '<TR>';
				$ret_html .= '<TD><a href="/accounts/chrage_remove/'.$row['id'].'">remove</a></td>';
				$ret_html .= '<TD>'.$row['id'].'</td>';
				$ret_html .= '<TD>'.$row['description'].'</td>';
				$ret_html .= '<TD>'.$row['sweep'].'</td>';
				$ret_html .= '<TD>'.$row['cost'].'</td>';
	            $ret_html .= '</TR>';            				
			}
		}
		return $ret_html;
	}*/
	
	/*function list_account_charges($number)
	{		
		$item_arr = array();
		$q = "SELECT * FROM charge_to_account WHERE status < 2 AND cardnum = ".$this->db->escape_str($number);
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$item_arr[] = $row['name'];
			}
		}
		return $item_arr;
	}*/		
	
	   
}//end class

?>
