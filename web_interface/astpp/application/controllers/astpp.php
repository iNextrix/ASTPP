<?php 

class Astpp extends CI_Controller
{
	
	function Astpp()
	{
		parent::__construct();
		
		$this->load->helper('template_inheritance');
		$this->load->model('Auth_model');
		$this->load->helper('form');		
	}
	
	function index()
	{
		if($this->session->userdata('user_login')== FALSE)
		{
			redirect(base_url().'astpp/login');
		}
		else 
		{
			redirect(base_url().'astpp/dashboard');
		}
	}
	function login2()
	{
		$this->load->view('view_login2');
	}
	function login()
	{
		if (!empty($_POST))// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			$this->load->model('system_model');
			
			$config = $this->system_model->getAuthInfo();
			$config_info = @$config[0];
		
			$user_valid = $this->Auth_model->verify_login($_POST['username'],$_POST['password']);
			
			if($user_valid == 1)
			{
				$this->session->set_userdata('user_login', TRUE);
				
				$this->load->model('accounts_model');		
				$result = $this->accounts_model->get_account($_POST['username']);
				//$this->session->set_userdata('logintype', $result->{'type'});
				$this->session->set_userdata('logintype', $result['type']);
				$this->session->set_userdata('userlevel_logintype', $result['type']);
				$this->session->set_userdata('username', $_POST['username']);
				$this->session->set_userdata('accountinfo', $result);
				if($result['type']==0) {
					$this->session->set_userdata('mode_cur', 'user');		
					redirect(base_url().'user/dashboard');
				}
				else{
					$this->session->set_userdata('mode_cur', 'admin');		
					redirect(base_url().'astpp/dashboard');
				}
			}
			else 
			{
				if($_POST['username']=="" && $config_info->value==$_POST['password']) {
					$this->session->set_userdata('user_login', TRUE);	
					$this->session->set_userdata('logintype', 2);
					$this->session->set_userdata('userlevel_logintype', -1);
					$this->session->set_userdata('mode_cur', 'admin');		
					redirect(base_url().'astpp/dashboard');	
				
				}
				else{
					$data['astpp_errormsg'] = "Login Failed! Try Again..";
				}
			}
		}

		$this->session->set_userdata('user_login', FALSE);
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		
		$this->load->view('view_login',$data);
		
	}
	
	function dashboard()
	{
		if($this->session->userdata('user_login')== FALSE)
		redirect(base_url().'astpp/login');
					
		$data['username'] = $this->session->userdata('user_name');				
		//$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['app_name'] = '';
		$data['cur_menu_no'] = 0;
		//$data['astpp_sidebar'] = 1;
		
		$this->load->model('Astpp_common');		
		
		  if ( $this->session->userdata('logintype') == 1  || $this->session->userdata('logintype') == 5  )
		  {
			  $accountlist = $this->Astpp_common->list_accounts_selective($this->session->userdata('username'), "-1");
			
			 $accounts = "";
        	 $tot_count =  count($accountlist);
        	 $count = 0;
				foreach ($accountlist as $key => $value) {
				$count++;
				$accounts .= "'" . $value . "',";
				}
			 
			$data['customer_count'] = $this->Astpp_common->count_accounts("WHERE type = 0 AND reseller = '".$this->session->userdata('username')."'");
			$data['reseller_count'] = $this->Astpp_common->count_accounts("WHERE type = 1 AND reseller = '".$this->session->userdata('username')."'");
			$data['vendor_count']= $this->Astpp_common->count_accounts("WHERE type = 3 AND reseller = '".$this->session->userdata('username')."'" );
			$data['admin_count']= $this->Astpp_common->count_accounts("WHERE type = 2 AND reseller = '".$this->session->userdata('username')."'");
			$data['callshop_count'] = $this->Astpp_common->count_accounts("WHERE type = 5  AND reseller = '".$this->session->userdata('username')."'");
			$data['total_owing'] = $this->common_model->calculate_currency($this->Astpp_common->accounts_total_balance( $this->session->userdata('username') ),'',$this->session->userdata['accountinfo']['currency'],true);
			$data['total_due'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance( $this->session->userdata('username')),'',$this->session->userdata['accountinfo']['currency'],true);
			$data['dids'] = $this->Astpp_common->count_dids("");
			$data['unbilled_cdrs'] = $this->Astpp_common->count_unbilled_cdrs("". $accounts."" );    
			
			$brands = $this->common_model->list_cc_brands_reseller( $this->session->userdata('username') );
			
			$brandsql = " IN (";		
			$list = implode("','",$brands);
			$list = "'".$list."'";
			$brandsql .= $list;		
			$brandsql .= ")";
			
			$data['calling_cards_in_use']  = $this->Astpp_common->count_callingcards("WHERE inuse = 1 AND status = 1 AND brand $brandsql");
			$data['calling_cards_active'] = $this->Astpp_common->count_callingcards(" WHERE status = 1 AND brand $brandsql");
			
			$data['calling_cards_unused'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1 AND brand $brandsql","SUM(value-used)"));
			
			$data['calling_cards_used'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1 AND brand $brandsql","SUM(used)"));
			
		  }
		   elseif ( $this->session->userdata('logintype') == 2) {
			   	
			   $data['customer_count'] = $this->Astpp_common->count_accounts(" WHERE type = 0 ");
			   $data['reseller_count'] = $this->Astpp_common->count_accounts(" WHERE type = 1 ");
			   $data['vendor_count'] = $this->Astpp_common->count_accounts(" WHERE type = 3 ");
			   $data['admin_count'] = $this->Astpp_common->count_accounts(" WHERE type = 2 ");
			   $data['callshop_count'] = $this->Astpp_common->count_accounts(" WHERE type = 5 ");
			   $data['calling_cards_in_use'] = $this->Astpp_common->count_callingcards(" WHERE inuse = 1 AND status = 1");
			   $data['total_owing'] = $this->common_model->calculate_currency($this->Astpp_common->accounts_total_balance("") ); 
			   $data['total_due'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance(""));
			   $data['dids'] = $this->Astpp_common->count_dids("");
			   $data['unbilled_cdrs'] = $this->Astpp_common->count_unbilled_cdrs("NULL,'',");    
			   
			   $data['calling_cards_active'] = $this->Astpp_common->count_callingcards(" WHERE status = 1");
			   
			   $data['calling_cards_unused'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1","SUM(value-used)"));
			   
			   $data['calling_cards_used'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1","SUM(used)"));
			
		   }
		$db_error = "";  
		if(Common_model::$global_config['system_config']['users_dids_freeswitch']=='1'){
		    try
		    {
			$db_fs = Common_model::$global_config['fs_db'];
			if($db_fs->conn_id=='')
			{
			    $db_error .= "Freeswitch database connection error. ";
			}
		    } catch(Exception $e) {		    
		    }
		}
		try
		{
		    $dbcdr_fs = Common_model::$global_config['fscdr_db'];		    
		    if($dbcdr_fs->conn_id=='')
		    {
			  $db_error .= "Freeswitch CDR database connection error.";
		    }
		    $query = $dbcdr_fs->query('SHOW TABLES');
		    if($query=='')
		    {		      
		      $db_error .= "Freeswitch CDR database connection error.";
		    }else{
			if(!$dbcdr_fs->table_exists(Common_model::$global_config['system_config']['freeswitch_cdr_table']))
			{
			  $db_error .= "Freeswitch CDR table not configured properly.";  
			}
		    }
		    
		} catch(Exception $e) {		    
		}
		if($db_error!='')
		{
		    $db_error .= "<br/> <a href='/systems/configuration'>Click here</a> to change settings.
		    <br/>Important : Please correct the error otherwise system may not work properly!!!";
		}
		$this->session->set_userdata('astpp_errormsg', $db_error);
		$this->load->view('view_dashboard',$data);
		
	}
	function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url().'astpp/');
	}
	
	function search()
	{
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		$this->load->model('common_model');
		$data['sweeplist'] = $this->common_model->get_sweep_list();
		$data['user_types'] = $this->common_model->get_user_levels_list();
		$data['currency_list'] = $this->common_model->get_currency_list();
		
		$this->load->model('callshop_model');
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$sth_destination = $this->callshop_model->getDestination("".$this->session->userdata('username')."");
		}
		else{
			$sth_destination = $this->callshop_model->getDestination();
		}		
		
		$destination_list = $sth_destination[1];		
		$data['destination'] = $destination_list;	
		
		$pattern_list = $sth_destination[2];	
		$data['pattern'] = $pattern_list;	
		
		$this->load->model('cc_model');
		$data['brands'] =  $this->cc_model->get_cc_brands();
		
		$data['trunks'] = $this->common_model->list_trunks_select('');
		
		$this->load->model('rates_model');
		$query_pkg = $this->rates_model->getPackages();
		$packages = array();
		if($query_pkg->num_rows()>0){
			foreach ($query_pkg->result_array() as $row)
				{
					$packages[] = $row['name'];
				}
		}
		
		$data['packages'] = $packages;
		
		$this->load->view('view_search', $data);
	}

	
}



?>
