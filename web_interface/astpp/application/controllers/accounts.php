<?php 

class Accounts extends CI_Controller
{
	function  Accounts()
	{
		parent::__construct();
		$this->load->helper('template_inheritance');
		$this->load->helper('authorization');
		$this->load->helper('form');
		$this->load->helper('romon');
		
		$this->load->library('astpp');	

		$this->load->library('session');
		$this->load->library('form_builder');
		
		$this->load->library('fpdf');
		$this->load->library('pdf');
		
		$this->load->model('Pricelists_model');
		$this->load->model('accounts_model');
		$this->load->model('accounting_model');
		
		$this->load->model('Astpp_common');	
		$this->load->model('rates_model');	
		$this->load->model('switch_config_model');
		
		$this->protected_pages = array('account_list');
		
		if($this->session->userdata('user_login')== FALSE)
	    redirect(base_url().'/astpp/login');				
	}
	
	
	/*
	 * CI has a built in method named _remap which allows
	 * you to overwrite the behavior of calling your controller methods over URI
	*/
	public function _remap($method, $params = array())
	{
		$logintype = $this->session->userdata('logintype');
		$access_control = validate_access($logintype,$method, "accounts");
		if ($access_control){
			return call_user_func_array(array($this, $method), $params);			 
	        //$this->$method();
		}
		else{
			$errors =  "Permission Access denied";
			$this->session->set_userdata('astpp_errormsg', $errors);
			if($logintype!=0){
				redirect(base_url().'astpp/dashboard');
			}
			else{
				redirect(base_url().'user/dashboard');
			}			
		}
	}
	
	function index()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts';
		$data['username'] = 'admin';
		redirect(base_url().'accounts/account_list/');
		//$this->load->view('view_accounts',$data);
	}
	
	function validation_create($data)
	{
		$errors = "";
		if(trim($data['firstname']) == "")
		$errors .= "First Name is required.<br />";
		
		if(trim($data['customnum'])== "")
		$errors .= "Account Number is required.<br />";
		
		if(trim($data['context'])== "")
		$errors .= "Context is required.<br />";
		
		if(trim($data['credit_limit'])== "")
		$errors .= "Credit Limit is required.<br />";
		
		if(trim($data['accountpassword'])=="")
		$errors .= "Password is required.<br />";
		
		if(trim($data['email'])== "")
		$errors .= "Email is required.<br />";

		if ($errors == "")
		{
			return true;
		}
		else
		return  $errors;
		
	}
	
	/**
	 * -------Here we write code for controller accounts functions remove------
	 * this function check if account number exist or not then remove from database.
	 * @account_number: Account Number
	 */
	function remove($account_number="")
	{		
		if (!($account = $this->accounts_model->get_account_by_number(urldecode($account_number))))
		{				
			$this->session->set_userdata('astpp_errormsg', 'Account not found!');
			redirect(base_url().'/accounts/account_list/');
		}
		
		$logintype = $this->session->userdata('logintype');
		$username = $this->session->userdata('username');
		
		$this->accounts_model->remove_account($account);		
		$this->redirect_notification('Account,'.$id.' Removed successfully...','/accounts/account_list/');
	}
	
	function redirect_errormsg($errormsg,$redirect)
	{
		$this->session->set_userdata('astpp_errormsg', $errormsg);
		redirect(base_url().$redirect);		
	}
	
	function redirect_notification($notificationmsg,$redirect)
	{
		$this->session->set_userdata('astpp_notification', $notificationmsg);
		redirect(base_url().$redirect);		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions payment_process------
	 * Payment process can only be done by Reseller or CallShop user.
	 * Against Account Number with specific price. 
	 * @account_number: Account Number
	 */	
	function payment_process($account_number="")
	{
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Process Payment';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Process Payment';	
		$data['cur_menu_no'] = 1;
		
		if (!empty($_POST))
		{
			$number = $_POST['number'];
			$refilldollars = $_POST['refilldollars'];
			$account_currency = $_POST['account_currency'];
			
			
			$errors = "";
			if(trim($_POST['number']) == "" || strlen($_POST['number']) < 3)
			$errors .= "Invalid account number specified.<br />";
			if(trim($_POST['refilldollars']) == "" || !is_numeric($_POST['refilldollars']))
			$errors .= "Invalid amount to process.<br />";
			
			if (trim($errors) == "")
			{
				$_POST['refilldollars'] = $this->common_model->add_calculate_currency($_POST['refilldollars'],$_POST['account_currency'],'',false,false);
				$logintype = $this->session->userdata('logintype');
				$username = $this->session->userdata('username');
				if ( $logintype == 1 || $logintype == 5 ) 
				{
					$accountinfo = $this->accounts_model->get_account_by_number($number);
					if ( $accountinfo['reseller'] == $username ) 
					{
					    $this->accounts_model->account_process_payment($_POST);
					    $this->redirect_notification('Account refilled successfully...','/accounts/account_list/');
					}
					else
					{
						$this->session->set_userdata('astpp_errormsg', "You are not allowed to add amount to this account.");
					}
			    }
			    else
			    {
					    $this->accounts_model->account_process_payment($_POST);		            	
				$this->redirect_notification('Account refilled successfully...','/accounts/account_list/');									
			    }
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', $errors);
			}			
		}
		else
		{
		  			  
		  if ($account = $this->accounts_model->get_account_by_number(urldecode($account_number)))
		  {			
			  $data['account'] = $account;			  
		  	  $data['default_currency'] = $account['currency'];
		  	  $this->load->view('view_accounts_process_payment',$data);
		  }
		  else
		  {
		  	 $this->session->set_userdata('astpp_errormsg', 'Account not found!');
			 redirect(base_url().'/accounts/account_list/');
		  }
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions create------
	 * If Account Type is Provider(3) message title is Create Provider otheriwse Create New Account
	 * Add an Account: This applys to user accounts as well as reseller accounts
	 * @type: Account Type
	 */
	function create($type="")
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['flag'] = 'create';
		if($type==3) {
		$data['page_title'] = 'Create Provider';
		$data['account'] = array('type' => 3);
		}
		else{
		$data['page_title'] = 'Create New Account';	
		}
		$data['cur_menu_no'] = 1;		
		
		
		$data['pricelist'] = $this->rates_model->get_price_list();
		$data['sweeplist'] = $this->common_model->get_sweep_list();
		$data['currency_list'] = $this->common_model->get_currency_list();
		$data['user_types'] = $this->common_model->get_user_levels_list();
		$data['config'] = $this->common_model->get_system_config();
		
		if (!empty($_POST))
		{
			$validation_result = $this->validation_create($_POST);
			if ($validation_result === true)
			{	
				$_POST['credit_limit'] = $this->common_model->add_calculate_currency($_POST['credit_limit'],'','',false,false);
				$this->accounts_model->add_account($_POST);
				$this->session->set_userdata('astpp_notification', 'Account Setup Completed!');
				if($this->input->post('accounttype')==3)
				{
					redirect(base_url().'lcr/providers/');
				}
				else
				{
					redirect(base_url().'accounts/account_list/');
				}
			}
			else 
			{
				$data['account'] = $_POST;
				$this->session->set_userdata('astpp_errormsg', $validation_result);				
			}
		}		
		
		$this->load->view('view_accounts_create',$data);
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions edit------
	 * Edit Account detail info of user against Account Number
	 * @account_number: Account Number
	 */
	function edit($account_number = "")
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Edit Account';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Edit Account';
		$data['flag'] = 'edit';	
		$data['cur_menu_no'] = 1;
		if (!empty($_POST))
		{			
			$validate_data = $_POST;
			$validate_data['customnum'] = $validate_data['item'];
			$validation_result = $this->validation_create($validate_data);
			if ($validation_result === true)
			{	
				$_POST['credit_limit'] = $this->common_model->add_calculate_currency($_POST['credit_limit'],'','',false,false);
				$this->accounts_model->edit_account($_POST);
				$this->session->set_userdata('astpp_notification', 'Account Edited Sucessfully!');
				if($this->input->post('accounttype')==3)
				{
					redirect(base_url().'lcr/providers/');
				}
				else
				{
					redirect(base_url().'accounts/account_list/');
				}				
			}
			else 
			{
				$data['account'] = $_POST;
				$this->session->set_userdata('astpp_errormsg', $validation_result);				
			}
		}
		else
		{		
			
			
			if($account = $this->accounts_model->get_account_by_number(urldecode($account_number)))
			{				
				$data['account'] = $account;				
				$data['pricelist'] = $this->rates_model->get_price_list();
				$data['resellers'] = $this->common_model->list_resellers_select($account['reseller']);
				
				$data['sweeplist'] = $this->common_model->get_sweep_list();
				$data['currency_list'] = $this->common_model->get_currency_list();
				$data['user_types'] = $this->common_model->get_user_levels_list();
				$data['config'] = $this->common_model->get_system_config();
				$this->load->view('view_accounts_create',$data);
			}
			else
			{				
				$data['astpp_errormsg'] = 'Account not found!';
				$this->session->set_userdata('astpp_errormsg', 'Account not found!');
				redirect(base_url().'accounts/account_list/');
			}
		}
	}
	
	
	function account_detail_add($acc_number=false)
	{
		$this->accounts_model->add_account_details($_POST);
		redirect(base_url().'accounts/account_detail/'.$_POST['accountnum']);
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions remove_account_details------
	 
	 * @type: Type: Remove ANI
	 * @id: ANI
	 * @accoutnum: accountnum
	 
	 * @type: Type: Remove IP
	 * @id: ip
	 * @accoutnum: accountnum
	 
	 * @type: Type: Remove Charge
	 * @id: chargeid
	 * @accoutnum: accountnum
	 
	 * @type: Type: Remove DID
	 * @id: DID
	 * @accoutnum: accountnum
	 */	 
	function remove_account_details($type, $id=false, $accountnum=false)
	{
		if(urldecode($type) == "Remove ANI"){
			$data['ANI'] = $id;
			$data['accountnum'] = $accountnum;
			$this->accounts_model->remove_ani_mapping($data);
		}
		elseif(urldecode($type) == "Remove IP") {
			$data['prefix']=$this->accounts_model->get_prefix_by_ip($id);
			$data['ip'] =$id;
			$data['accountnum'] = $accountnum;
				
			$this->accounts_model->remove_ip_mapping($data);
		}
		elseif(urldecode($type) == "Remove Charge") {
			$data['chargeid'] = $id;
			$data['accountnum'] = $accountnum;			
			$this->accounts_model->remove_charge($data);			
		}
		elseif(urldecode($type) == "Remove DID"){
			$data['DID'] = $id;
			$data['accountnum'] = $accountnum;
			$this->accounts_model->remove_dids($data);
		}
		
		
		redirect(base_url().'accounts/account_detail/'.$accountnum);
	}	
	
	
	/**
	 * -------Here we write code for controller accounts functions search------
	 * We post array of accounts field to CI database session variable account_search
	 */
	function search()
	{		
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('account_search', $_POST);		
		}
		if(@$ajax_search!=1) {
			redirect(base_url().'accounts/account_list');
		}
	}
	
	/**
	 * -------Here we write code for controller accounts functions clearsearchfilter------
	 * Empty CI database session variable account_search for normal listing
	 */
	function clearsearchfilter()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('account_search', "");
		redirect(base_url().'accounts/account_list');		
	}	
	
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		$this->load->model('Accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Accounts';
		
		$data['cur_menu_no'] = 1;
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		$this->load->model('common_model');
		$data['sweeplist'] = $this->common_model->get_sweep_list();
		$data['user_types'] = $this->common_model->get_user_levels_list();
		$data['currency_list'] = $this->common_model->get_currency_list();
		
		
		$data['typelist'] = $this->accounts_model->select_account_type();
		
		
		if ($this->uri->segment(3) === FALSE)
		{
					
			$this->load->view('view_accounts_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_accounts_list',$data);
		}
		else 
		{			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);			
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];		
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			$json_data = array();

			$sweeplist = $this->common_model->get_sweep_list();
							
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
// 				echo "<pre>";print_r(Common_model::$global_config['userlevel']);echo "<pre>";
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account_including_closed($row['number']);
// 				echo "<pre>";print_r($accountinfo);echo "<pre>";					
									
					if($accountinfo['posttoexternal'] == 1)
						$posttoexternal = 'Yes';
					else 	
						$posttoexternal = 'No';
// 					echo $accountinfo['type']."<br/>";	
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['pricelist'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$this->common_model->calculate_currency($this->Astpp_common->accountbalance($row['number'])),
						$this->common_model->calculate_currency($accountinfo['credit_limit']),
						$sweeplist[$row['sweep']],						
						$posttoexternal,
// 						$accountinfo['reseller'],
						Common_model::$global_config['userlevel'][$accountinfo['type']],
						($accountinfo['status']==1)?'Active':'Inactive',
						$this->get_action_buttons($row['number'],$row['accountid'])
					));
// 					echo Common_model::$global_config['userlevel'][$accountinfo['type']];
				}
			}
// 			echo "<pre>";print_r($json_data);
			echo json_encode($json_data);			
		}
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_callingcard_account_list------
	 * Listing of Calling card for selection of account number in quick search
	 */
	function search_callingcard_account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_account_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_account_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	/**
	 * -------Here we write code for controller accounts functions search_did_account_list------
	 * Listing of DIDs for selection of account number in quick search
	 */
	function search_did_account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_did_account_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_did_account_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	
	/**
	 * -------Here we write code for controller accounts functions search_did_provider_list------
	 * Listing of Provider for selection of account number in quick search
	 */
	function search_did_provider_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{	
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Provider';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_did_provider_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_did_provider_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'3');	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'3');
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	/**
	 * -------Here we write code for controller accounts functions search_trunks_provider_list------
	 * Listing of Trunks Provider for selection of account number in quick search
	 */
	function search_trunks_provider_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Provider';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_trunks_provider_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_trunks_provider_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'3');	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'3');
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_trunks_reseller_list------
	 * Listing of Trunks Reseller for selection of account number in quick search
	 */
	function search_trunks_reseller_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_trunk_reseller_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_trunk_reseller_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'1');	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'1');
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_outbound_reseller_list------
	 * Listing of Outbound Reseller for selection of account number in quick search
	 */
	function search_outbound_reseller_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_outbound_reseller_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_outbound_reseller_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'1');	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'1');
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_counters_account_list------
	 * Listing of Counters Reseller for selection of account number in quick search
	 */
	function search_counters_account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Account';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_counters_account_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_counters_account_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_configuration_reseller_list------
	 * Listing of Configuration Reseller for selection of account number in quick search
	 */
	function search_configuration_reseller_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_configuration_reseller_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_configuration_reseller_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'1');	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'1');
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions search_fssipdevices_account_list------
	 * Listing of FSSIP devices account for selection of account number in quick search
	 */
	function search_fssipdevices_account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_fssipdevices_account_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_fssipdevices_account_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	/**
	 * -------Here we write code for controller accounts functions search_fscdrs_account_list------
	 * Listing of FSCDRS account for selection of account number in quick search
	 */
	function search_fscdrs_account_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL, $accounttype=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_search_fscdrs_account_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_search_fscdrs_account_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,$accounttype);	
			
			$config['total_rows'] = $count_all;			
			
			$config['per_page'] = $_GET['rp'];

			$page_no = $_GET['page']; 
			
			$json_data = array();
			$json_data['page'] = $page_no;
			
			$json_data['total'] = $config['total_rows'];	
			
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,$accounttype);
			
			if($query->num_rows() > 0)
			{
				$json_data['page'] = $page_no;
				
				$json_data['total'] = $config['total_rows'];
				foreach ($query->result_array() as $row)
				{
					
					$accountinfo = $this->accounts_model->get_account($row['number']);
													
					$json_data['rows'][] = array('cell'=>array(
						$accountinfo['cc'],
						$row['number'],
						$accountinfo['first_name'],
						$accountinfo['last_name'],
						$accountinfo['company_name'],
						$accountinfo['country'],
						"<a href=\"javascript:sendValue('".$row['number']."', '".$row['number']."');\">select</a>"
					));
				}
			}		
			echo json_encode($json_data);		
			
		}
		
	}
	
	
	function get_action_buttons($id,$accountid='')
	{
		//Process Payment,Remove Account,Edit Account,View Details
		$payment_style = 'style="text-decoration:none;background-image:url(/images/payment.png);"';
		$viewdetails_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
		$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		
		$add_ac_tax = 'style="text-decoration:none;background-image:url(/images/tax.png);"';
                $add_caller_id = 'style="text-decoration:none;background-image:url(/images/callerid.png);"';
		
		$ret_url = '';		
		$ret_url .= '<a href="'.base_url().'accounts/payment_process/'.$id.'/" class="icon" '.$payment_style.' rel="facebox" title="Process Payment">&nbsp;</a>';
		$ret_url .= '<a href="'.base_url().'accounts/account_detail/'.$id.'/" class="icon" '.$viewdetails_style.' title="View Details">&nbsp;</a>';
		$ret_url .= '<a href="'.base_url().'accounts/edit/'.$id.'/" class="icon" '.$update_style.' title="Edit Account">&nbsp;</a>';
		
		$ret_url .= '<a href="'.base_url().'accounting/account_taxes/edit/'.$accountid.'/" class="icon" '.$add_ac_tax.' title="Add Account Taxes" rel="facebox" >&nbsp;</a>';                
                $ret_url .= '<a href="'.base_url().'accounts/add_callerid/'.$accountid.'/" class="icon" '.$add_caller_id.' title="Add CallerID" rel="facebox" >&nbsp;</a>';
		
		$ret_url .= '<a href="'.base_url().'accounts/remove/'.$id.'/" class="icon" '.$delete_style.' title="Remove Account" onClick="return get_alert_msg();">&nbsp;</a>';
		
		return $ret_url;
	}	
	
	
	/**
	 * -------Here we write code for controller accounts functions account_detail------
	 * Account detail info through account number with checking account no exit or not.
	 */
	function account_detail($account_number) //build_account_info
	{
		//$account_number =  $this->uri->segment(3,0);
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Account Details';	
		
		if (!empty($_POST))// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			// put your processing code here... we show what we do for emailing. You will need to add a correct email address
			if ($this->_process_create($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('contact');
			}
		}
		
		if ($account = $this->accounts_model->get_account_by_number(urldecode($account_number)))
		{	
			$data['balance'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance($account_number));
			$data['credit_limit'] = $this->common_model->calculate_currency($account['credit_limit']);
			$data['account'] = $account;
						
			$this->load->model('Rates_model');			
			$data['pricelist'] = $this->Rates_model->list_pricelists_select($account['pricelist']);
			
			//$data['sweeplist'] = $this->Astpp_model->sweep_list($account['sweep']);
			$data['sweeplist'] = $this->common_model->get_sweep_list();
			
			
			//$data['account_type'] = $this->Astpp_model->account_type_name($account['type']);
			$data['user_type'] = $this->common_model->get_user_levels_list();
			
			$data['chargelist'] =  $this->Astpp_common->list_applyable_charges();
							
			$data['account_did_list'] = $this->Astpp_common->list_dids_account($account_number);
						
			$data['account_ani_list'] = $this->Astpp_common->list_ani_account($account_number);	
			
			$data['account_ip_list'] = $this->Astpp_common->list_ip_map($account_number);	
			
			$accountinfo = $this->session->userdata('accountinfo');
			
			$data['account_invoice_list'] = $this->accounts_model->invoice_list_internal($account['accountid']);
				
			//$data['chargelist'] = $this->Astpp_model->list_account_charges_table($account_number);
			$data['account_number'] =$account_number;
			
			$data['availabledids'] = $this->Astpp_common->list_available_dids($account_number);
		
						
			$this->load->model('common_model');
			$this->load->view('view_accounts_details',$data);						
		}
		else
		{
			$this->session->set_userdata('astpp_errormsg', 'Account not found!');
			redirect(base_url().'/accounts/account_list/');
		}		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions accountDetailsPopup------
	 * Account detail info through account number with checking account no exit or not.
	 */
	function accountDetailsPopup($account_number=NULL)
	{
		if ($account = $this->accounts_model->get_account_by_number(urldecode($account_number)))
		{	
			
			$data['account'] = $account;
			$data['balance'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance($account['number']));
			$data['credit_limit'] = $this->common_model->calculate_currency($account['credit_limit']);
			
			//$data['account_type'] = $this->Astpp_model->account_type_name($account['type']);
			$data['user_type'] = $this->common_model->get_user_levels_list();
			$this->load->view('view_accounts_details_popup',$data);
		}
		else
		{
			$this->session->set_userdata('astpp_errormsg', 'Account not found!');
			redirect(base_url().'/accounts/account_list/');
		}
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions view_invoice------
	 * We fetch invoice detail from CDRS table through Invoice ID
	 * @invoiceid: Invoice ID
	 */
	function view_invoice($invoiceid=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Invoice Detail';	
		$data['cur_menu_no'] = 1;
		
		$cdrs_query = $this->accounts_model->getCdrs_invoice($invoiceid);
		$invoice_cdr_list = array();
		$cdr_list = array();
		if($cdrs_query->num_rows()>0)
		{
			foreach($cdrs_query->result_array() as $cdr)
			{
				$cdr['charge'] = $this->common_model->calculate_currency($cdr['debit'] - $cdr['credit']);
// 				$cdr['charge'] = money_format( "%.6n", $cdr['charge'] );
				array_push( $cdr_list, $cdr );
			}
		}
		$data['invoice_cdr_list'] = $cdr_list;
		
		$invoice_total_query = $this->Astpp_common->get_invoice_total($invoiceid);
		$total_list = array();
		$invoice_total_list = array();
		
		if($invoice_total_query->num_rows()>0){
			foreach($invoice_total_query->result_array() as $total) {
				array_push( $total_list, $total );
			}
		}		
		$data['invoice_total_list'] = $total_list;	
			
		$invoicedata  = $this->Astpp_common->get_invoice($invoiceid);	
		
		$data['invoiceid'] = @$invoicedata[0]['invoiceid'];
		$data['invoicedate'] = @$invoicedata[0]['date'];
		$data['accountid'] = @$invoicedata[0]['accountid'];
		
		
		$accountinfo = $this->accounts_model->get_account_including_closed( @$invoicedata[0]['accountid'] );				      
		$data['accountinfo'] = $accountinfo;
		
		//Get invoice header information
		$invoiceconf = $this->accounts_model->get_invoiceconf( $accountinfo['reseller'] );
		$data['invoiceconf'] = $invoiceconf;
		$this->load->view('view_account_invoice_detail',$data);
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions invoice_json------
	 * Listing of invoice detail through account no
	 * @account_number: Account Number
	 */
	function invoice_json($account_number=NULL)
	{
	
		$accountinfo = $this->accounts_model->get_account_by_number(urldecode($account_number));		
		//$accountinfo = $this->session->userdata('accountinfo');
		
		$json_data = array();		
		$count_all = $this->accounts_model->list_invoice_count($accountinfo['accountid']);	
		
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_invoice($accountinfo['accountid'], $start, $perpage);	
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			
				$json_data['rows'][] = array('cell'=>array($row['invoiceid'],
						    $row['date'],
						    $this->common_model->calculate_currency($row['value']),
						    $this->get_action_buttons_invoice($row['invoiceid']),
						    $this->get_action_buttons_invoice_download($row['invoiceid'])
						    ));
			}
		}
		echo json_encode($json_data);
	}
	
	/**
	 * -------Here we write code for controller accounts functions download_invoice------
	 * Invoice detail in pdf format
	 * @invoiceid: Invoice ID
	 */
	function download_invoice($invoiceid=false)
	{
// 		if($this->session->userdata('logintype')==2) {
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Invoice Detail';	
		$data['cur_menu_no'] = 1;
		
		$cdrs_query = $this->accounts_model->getCdrs_invoice($invoiceid);
		$invoice_cdr_list = array();
		$cdr_list = array();
		if($cdrs_query->num_rows()>0)
		{
			foreach($cdrs_query->result_array() as $cdr)
			{
				$cdr['charge'] = $this->common_model->calculate_currency($cdr['debit'] - $cdr['credit']);
				array_push( $cdr_list, $cdr );
			}
		}
		$data['invoice_cdr_list'] = $cdr_list;
		
		$invoice_total_query = $this->Astpp_common->get_invoice_total($invoiceid);
		$total_list = array();
		$invoice_total_list = array();
		
		if($invoice_total_query->num_rows()>0){
			foreach($invoice_total_query->result_array() as $total) {
				array_push( $total_list, $total );
			}
		}		
		$data['invoice_total_list'] = $total_list;	
			
		$invoicedata  = $this->Astpp_common->get_invoice($invoiceid);	
		
		$data['invoiceid'] = @$invoicedata[0]['invoiceid'];
		$data['invoicedate'] = @$invoicedata[0]['date'];
		$data['accountid'] = @$invoicedata[0]['accountid'];
		
		
		$accountinfo = $this->accounts_model->get_account_including_closed( @$invoicedata[0]['accountid'] );

		$data['accountinfo'] = $accountinfo;
				
		//Get invoice header information
		$invoiceconf = $this->accounts_model->get_invoiceconf( $accountinfo['reseller'] );
		$data['invoiceconf'] = $invoiceconf;

		//FOR the header company information
		$result_company[0]['value'] = $data['invoiceconf']['company_name'];						//Company Name
		$result_company[1]['value'] = $data['invoiceconf']['address'];							//Address
		$result_company[2]['value'] = $data['invoiceconf']['city'] . " - " . $data['invoiceconf']['zipcode']; 		//City - Zip
		$result_company[3]['value'] = $data['invoiceconf']['country'];							//Country
		$result_company[4]['value'] = "Phone: " . $data['invoiceconf']['telephone']; 					//Phone
		$result_company[5]['value'] = "Email: " . $data['invoiceconf']['emailaddress']; 				//Fax
		$result_company[6]['value'] = "Web Site: " . $data['invoiceconf']['website']; 					//Website
		
		//FOR the Customer Address
		$customer_address = "";
		if ( $data['accountinfo']['first_name'] != "")
		    $customer_address .= $data['accountinfo']['first_name'] . " ";

		if ( $data['accountinfo']['last_name'] != "")
		    $customer_address .= $data['accountinfo']['last_name'] . "\n";
		else
		    $customer_address .= "\n";

		if ( $data['accountinfo']['address_1'] != "")
		    $customer_address .= $data['accountinfo']['address_1'] . "," .$data['accountinfo']['address_2'] ."," . $data['accountinfo']['address_3'] . "\n";

		if ( $data['accountinfo']['city'] != "")
		    $customer_address .= $data['accountinfo']['city'] . "\n";

		if ( $data['accountinfo']['province'] != "")
		    $customer_address .= $data['accountinfo']['province'];

		if ( $data['accountinfo']['country'] != "")
		    $customer_address .= $data['accountinfo']['country'];
		
		if ( $data['accountinfo']['postal_code'] != "")
		    $customer_address .= " - " . $data['accountinfo']['postal_code'] . "\n";
		else
		    $customer_address .= "\n";

		if ( $data['accountinfo']['telephone_1'] != "")
		    $customer_address .= "Phone: " . $data['accountinfo']['telephone_1'] . "," . $data['accountinfo']['telephone_2']. "\n";

		if ( $data['accountinfo']['email'] != "")
		    $customer_address .= "Email: " . $data['accountinfo']['email'] . "\n";
		
		
		$this->fpdf = new PDF('P','pt');
		$this->fpdf->initialize('P','mm','A4');
		$this->fpdf->AliasNbPages();
		$this->fpdf->AddPage();
		$this->fpdf->SetFont('Arial','',12);
		
		$y_axis = 14;
		//Loop For Company Address.
		for ($i = 0; $i < count($result_company); $i++) {
		    if ($i == 1) {
			$this->fpdf->SetFont('Arial', '', 8);
		    }
		    $this->fpdf->Cell(40, 5, $result_company[$i]['value']);
		    $this->fpdf->SetXY(10, $y_axis);
		    $y_axis +=4;
		}
		
		//Right header part
		$this->fpdf->SetFont('Arial', '', 18);
		$this->fpdf->SetXY(170, 10);
		$this->fpdf->Cell(40, 10, "INVOICE");

		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(166, 15);
		$this->fpdf->Cell(40, 10, "Invoice Date");

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetXY(185, 15);
		$this->fpdf->Cell(40, 10, $data['invoicedate']);

		//Customer Address.
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(10, 50);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->Cell(80, 5, "Bill To:", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 9);
		$this->fpdf->SetXY(10, 55);
		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->Multicell(80, 4, $customer_address, 1, 1, 'L', true);
		
		//Middle portion.
		
		//Card Number
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(20, 90);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->Cell(45, 6, "Card Number", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetXY(20, 96);
		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->Cell(45, 5, $data['accountinfo']['cc'], 1, 1, 'L', true);

		//Account Number
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(65, 90);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->Cell(40, 6, "Account Number", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetXY(65, 96);
		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->Cell(40, 5, $data['accountinfo']['number'], 1, 1, 'L', true);

		//Invoice Number
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(105, 90);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->Cell(40, 6, "Invoice Number", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetXY(105, 96);
		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->Cell(40, 5, $invoiceid, 1, 1, 'L', true);


		//Invoice Date
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetXY(145, 90);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->Cell(40, 6,"Invoice Date", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetXY(145, 96);
		$this->fpdf->SetFillColor(255, 255, 255);
		$this->fpdf->Cell(40, 5, $data['invoicedate'], 1, 1, 'L', true);
		
		
		//Header for the detailed table.
		$this->fpdf->SetFont('Arial', 'B', 8);
		$this->fpdf->SetFillColor(231, 231, 231);
		$this->fpdf->SetXY(10, 110);
		$this->fpdf->Cell(30, 5, "Date & Time", 1, 1, 'L', true);

		$this->fpdf->SetXY(40, 110);
		$this->fpdf->Cell(30, 5, "Caller*ID", 1, 1, 'L', true);
		
		$this->fpdf->SetXY(70, 110);
		$this->fpdf->Cell(30, 5, "Called Number", 1, 1, 'L', true);

		$this->fpdf->SetXY(100, 110);
		$this->fpdf->Cell(50, 5, "Disposition", 1, 1, 'L', true);

		$this->fpdf->SetXY(150, 110);
		$this->fpdf->Cell(25, 5, "Duration", 1, 1, 'L', true);

		$this->fpdf->SetXY(175, 110);
		$this->fpdf->Cell(25, 5, "Charge", 1, 1, 'L', true);

		$this->fpdf->SetFont('Arial', '', 8);
		$this->fpdf->SetFillColor(255, 255, 255);
		
		$this->fpdf->tablewidths = array(30, 30, 30, 50, 25, 25);

		foreach ($data['invoice_cdr_list'] as $key => $value) {
		    $data_final[$key][0] = $value['callstart'];
		    $data_final[$key][1] = $value['callerid'];
		    $data_final[$key][2] = $value['callednum'];
		    $data_final[$key][3] = $value['disposition'];
		    $data_final[$key][4] = $value['billseconds'];
		    $data_final[$key][5] = $value['charge'];
		}
		
// 		echo "<pre>";print_r($data_final);echo "</pre>";
		
		//Generating the table of the invoice entures.
		$dimensions = $this->fpdf->morepagestable($data_final, "5");
		
		$currency = $data['accountinfo']['currency'];
		  
		foreach($data['invoice_total_list'] as $key => $values)
		{
		    $data_to_total[$key] = $values;
		}
// 		print_r($data['invoice_total_list']);
		foreach ($data_to_total as $key => $value) {
		    $data_final_total[$key][0] = "";
		    $data_final_total[$key][1] = "";
		    $data_final_total[$key][2] = "";
		    $data_final_total[$key][3] = $value['title'];
		    $data_final_total[$key][4] = $value['text'];
		    $data_final_total[$key][5] = substr($value['value'],0,-2)." ".$currency;
		}
		
		//Total list
		$this->fpdf->tablewidths = array(30,30,30,50, 25, 25);
		$dimensions = $this->fpdf->table_total($data_final_total, "5");
		
		//To output the file to the folder.
		$this->fpdf->Output('invoice_'.date('dmY').'.pdf', "D");
		exit;
// 		}
	}
	
	function get_action_buttons_invoice_download($id)
	{
		$viewdetails_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
		$ret_url = '';		
		$ret_url .= '<a href="'.base_url().'accounts/download_invoice/'.$id.'/" class="icon" '.$viewdetails_style.' title="Download Invoice">&nbsp;</a>';
		return $ret_url;
	}
	
	
	function get_action_buttons_invoice($id)
	{
		$viewdetails_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
		$ret_url = '';		
		$ret_url .= '<a href="'.base_url().'accounts/view_invoice/'.$id.'/" class="icon" '.$viewdetails_style.' title="View Invoice">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller accounts functions ip_json------
	 * Listing of IP through account no
	 * @account_number: Account No
	 */
	function ip_json($account_number)
	{
		
		$json_data = array();
		$count_all = $this->accounts_model->list_ip_count($account_number);	
		
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_ip($account_number, $start, $perpage);	
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			
				$json_data['rows'][] = array('cell'=>array($row['ip'],
				  $row['prefix'],
				  $row['context'],
				  $row['created_date'],
				  //'<a href="'.base_url().'accounts/remove_account_details/Map IP/'.$id.'/" class="icon" style="text-decoration:none;background-image:url(/images/delete.png);" title="Remove" onClick="return get_alert_msg();">&nbsp;</a>'
				    $this->get_action_buttons_chargelist("Remove IP", $row['ip'], $account_number)
				  ));
			}
		}
		echo json_encode($json_data);
		
	
	}
	
	
	
	/**
	 * -------Here we write code for controller accounts functions ani_json------
	 * Listing of ANI through account no
	 * @account_number: Account No
	 */
	function ani_json($account_number)
	{
		
		$json_data = array();
		$count_all = $this->accounts_model->list_ani_count($account_number);	
		
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_ani($account_number, $start, $perpage);	
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			
				$json_data['rows'][] = array('cell'=>array($row['number'],
															$row['context'],
															 $this->get_action_buttons_chargelist("Remove ANI", $row['number'], $account_number)
															));
			}
		}
		echo json_encode($json_data);
		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions dids_json------
	 * Listing of DISs through account no in accounts details.
	 * @account_number: Account No
	 */
	function dids_json($account_number)
	{
		
		$json_data = array();
		$count_all = $this->accounts_model->list_dids_count($account_number);	
		
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_dids($account_number, $start, $perpage);	
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$cost = $this->common_model->calculate_currency($row['monthlycost']);
				$json_data['rows'][] = array('cell'=>array(@$row['number'],
				$cost,
				  $this->get_action_buttons_chargelist("Remove DID", @$row['number'], $account_number)
				));
			}
		}
		echo json_encode($json_data);
		
	}
	
	/*function chargelist_json($account_number)
	{
		$count_all = $this->accounts_model->list_chargelist_count();			
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_chargelist($start, $perpage);	
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array("",
															$row['id'],
															$row['description'],
															$row['sweep'],
															$row['charge']
															));
			}
		}
		echo json_encode($json_data);
		
	}*/
	
	/**
	 * -------Here we write code for controller accounts functions chargelist_json------
	 * Listing of Charge List through account no in accounts details.
	 * @account_number: Account No
	 */
	function chargelist_json($account_number)
	{
		
		$this->load->model('common_model');
		$sweeplist = $this->common_model->get_sweep_list();
		
		$this->load->model('Astpp_common');
		
		$accountinfo = $this->accounts_model->get_account($account_number);
		
		$account_charge_list = $this->Astpp_common->list_account_charges($accountinfo['number']);		
		
		$pricelist_charge_list = $this->Astpp_common->list_pricelist_charges($accountinfo['pricelist']);	
	
		$charge_list = array();		
		foreach($account_charge_list as $key => $value){
			
			$chargeinfo = $this->Astpp_common->get_charge($value['charge_id']);
			
			if(count($chargeinfo)>0) {
				$cost = $this->common_model->calculate_currency($chargeinfo[0]['charge']);
				$row['sweep'] = $sweeplist[$chargeinfo[0]['sweep']];
				$row['charge'] = $cost;
				$row['id'] = $value['id'];
				$row['description'] =  $chargeinfo[0]['description'];
				array_push( $charge_list, $row );
			}
		}
		
	
		foreach($pricelist_charge_list as $key => $value)
		{	
			$chargeinfo = $this->Astpp_common->get_charge($value['id']);
		
			if(count($chargeinfo)>0) {
				$cost = $this->common_model->calculate_currency($chargeinfo[0]['charge']);
				$row['sweep'] = $sweeplist[$chargeinfo[0]['sweep']];
				$row['charge'] = $cost;
				$row['id'] = $value['id'];
				$row['description'] =  $chargeinfo[0]['description'];
				array_push( $charge_list, $row );
			}
		}
		
	
		$count_all = count($charge_list);		
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		 for($i=$start;$i<=($config['per_page']+$start-1);$i++) { 
		 		 if (isset($charge_list[$i]['id'])) {
					$json_data['rows'][] = array('cell'=>array(	
															$this->get_action_buttons_chargelist("Remove Charge", $charge_list[$i]['id'], $account_number),
															$charge_list[$i]['id'],
															$charge_list[$i]['description'],
															$charge_list[$i]['sweep'],
															$charge_list[$i]['charge']
															)); 
				 }
		 	
		 }
		
		echo json_encode($json_data);	
	}
	
	
	
	function get_action_buttons_chargelist($action, $id, $username)
	{
		//$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		//$import_style = 'style="text-decoration:none;background-image:url(/images/import.png);"';
		$url = '';
		$ret_url = '';
	
		$ret_url .= '<a href="/accounts/remove_account_details/'.$action.'/'.$id.'/'.$username.'" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';

		return $ret_url;
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions account_detail_json------
	 * Account details through account no.
	 * @account_number: Account No
	 */
	function account_detail_json($account_number)
	{
		
		$count_all = $this->accounts_model->list_cdrs_count($account_number);			
		$config['total_rows'] = $count_all;			
		
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;
		
		$json_data['total'] = $config['total_rows'];	
		
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		$query = $this->accounts_model->list_cdrs($account_number, $start, $perpage);	
		
		$row_detail = array();
			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $record)
				{
					$row = array();
					if ( !$record['callerid'] ) {$row['callerid'] = "N/A";}
					else {$row['callerid'] = $record['callerid'];}
					
					 if ( !$record['uniqueid'] ) { $row['uniqueid'] = "N/A"; }
		             else { $row['uniqueid'] = $record['uniqueid']; }
					 
					 if ( !$record['disposition'] ) {$row['disposition'] = "N/A";}
					 else { $row['disposition'] = $record['disposition']; }
					 
					 if ( !$record['notes'] ) { $row['notes'] = ""; }
            		 else { $row['notes'] = $record['notes']; }
					 
					 if ( !$record['callstart'] ) { $row['callstart'] = ""; }
            		 else { $row['callstart'] = $record['callstart']; }
					 
					 if ( !$record['callednum'] ) { $row['callednum'] = ""; }
           			 else { $row['callednum'] = $record['callednum']; }
					 
					 if ( !$record['billseconds'] ) { $row['billseconds'] = ""; }
            		 else { $row['billseconds'] = $record['billseconds']; }
					 
					 if ( !$record['cost'] ) { $row['cost'] = ""; }
           			 else { $row['cost'] = $record['cost']; }
					 
					  $row['profit'] = $this->common_model->calculate_currency( $record['debit'] - $record['cost'] );
					  $row['cost'] = $this->common_model->calculate_currency($row['cost']);
					  
				    if ( $record['debit'] ) {
					$row['debit'] = $record['debit'];
					$row['debit'] = $this->common_model->calculate_currency($row['debit'] );
					}
					else {
						$row['debit'] = "-";
					}
					  
					if ( $record['credit'] ) {
						$row['credit'] = $record['credit'];
						$row['credit'] = $this->common_model->calculate_currency($row['credit']);
					}
					else {
						$row['credit'] = "-";
					} 
					
					array_push($row_detail, $row);					
				}
			}
						
			$cdrlist = $row_detail;
			
			if(count($cdrlist) > 0)
			{
				foreach ($cdrlist as $key => $row)
				{
					$json_data['rows'][] = array('cell'=>array($row['uniqueid'],
															   $row['callstart'],
															   $row['callerid'],
															   $row['callednum'],
															   $row['disposition'],
															   $row['billseconds'],
															   $row['debit'],
															   $row['credit'],
															   $row['notes'],
															   $row['cost'],
															   $row['profit']));
															   
				}
			}
				
			echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller accounts functions iax_sip_json------
	 * Listing of IAX SIP through account no in accounts details.
	 * @account_number: Account No
	 */
	function iax_sip_json($account_number=NULL)
	{
		
		$account_device_list = array();
		if ($account = $this->accounts_model->get_account_by_number(urldecode($account_number)))
		{	
			$data['account'] = $account;			
			$data['account_number'] =$account_number;
			$this->load->model('common_model');
			//$accountinfo = $this->session->userdata('accountinfo');
			
			$rt_db = 0;
			if ($rt_db) {
				
				$sip_names = $this->common_model->list_sip_account_rt($account['number'],$account['cc']);
				
				$iax_names = $this->common_model->list_iax_account_rt($account['number'],$account['cc']);
				
				
				foreach($sip_names as $key => $value) 
				{
					$deviceinfo = $this->common_model->get_sip_account_rt($value['name']);
					$row = array();
					$row['tech']     = "SIP";
					$row['type']     = $deviceinfo['type'];
					$row['username'] = $deviceinfo['username'];
					$row['secret']   = $deviceinfo['secret'];
					$row['context']  = $deviceinfo['context'];
					array_push( $account_device_list, $row );
					
				}
				
				foreach($iax_names as $key => $value)
				{
					$deviceinfo = $this->common_model->get_iax_account_rt($value['name']);
					$row = array();
					$row['tech']     = "IAX2";
					$row['type']     = $deviceinfo['type'];
					$row['username'] = $deviceinfo['username'];
					$row['secret']   = $deviceinfo['secret'];
					$row['context']  = $deviceinfo['context'];
					array_push( $account_device_list, $row );
					
				}
			
			}
			
			$fs_db=1;
			if ($fs_db) {			
			   $sip_devices = $this->common_model->fs_list_sip_usernames($account['cc'], $account['number']);
				if(count($sip_devices)>0) {
					foreach($sip_devices as $key => $record){
						
						$deviceinfo = $this->switch_config_model->fs_retrieve_sip_user($record['id']);
						$row = array();
						$row['tech']     = "SIP";
						$row['type']     = "user@" . $record['domain'];
						$row['username'] = $record['username'];
						$row['secret']   = $deviceinfo['password'];
						$row['context']  = $deviceinfo['context'];
						array_push($account_device_list, $row);
					}
				}
			}
			
			$freepbx_db = 0;
			if ($freepbx_db) {
				 $sip_names_freepbx =  $this->common_model->list_sip_account_freepbx( $account['number'],$account['cc'] );
				 $iax_names_freepbx =  $this->common_model->list_iax_account_freepbx( $account['number'],$account['cc']);
				 
				  foreach($sip_names_freepbx as $key => $value)
				  {
					$deviceinfo =        $this->common_model->get_sip_account_freepbx( $value['name'] );
					$row =array();
					$row['tech']     = "SIP";
					$row['type']     = $deviceinfo['type'];
					$row['username'] = $deviceinfo['username'];
					$row['secret']   = $deviceinfo['secret'];
					$row['context']  = $deviceinfo['context'];
					array_push( $account_device_list, $row );
				}
				foreach($iax_names_freepbx as $key => $value)
				{
					$deviceinfo =  $this->common_model->get_iax_account_freepbx( $value['name'] );
					$row =array();
					$row['tech']     = "IAX2";
					$row['type']     = $deviceinfo['type'];
					$row['username'] = $deviceinfo['username'];
					$row['secret']   = $deviceinfo['secret'];
					$row['context']  = $deviceinfo['context'];
					array_push( $account_device_list, $row );
				}
			}	
			
							
		}
		
		 $count_all = count($account_device_list);		
		 $config['total_rows'] = $count_all;			
		
		 $config['per_page'] = $_GET['rp'];

		 $page_no = $_GET['page']; 
		
		 $json_data['page'] = $page_no;		
		 $json_data['total'] = $config['total_rows'];	
		
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		 for($i=$start;$i<=($config['per_page']+$start-1);$i++) { 
		 	if (isset($account_device_list[$i]['tech']) && $account_device_list[$i]['tech']!="") {
				$json_data['rows'][] = array('cell'=>array(
						$account_device_list[$i]['tech'],
						$account_device_list[$i]['type'],
						$account_device_list[$i]['username'],
						$account_device_list[$i]['secret'],
						$account_device_list[$i]['context'],			
					));	
			}
		 }
		 
		 echo json_encode($json_data);
	}
	
	/**
	 * -------Here we write code for controller accounts functions add_callerid------
	 * Add caller ids against account no
	 * @account_number: Account No
	 */
	function add_callerid($account_number="")
    {
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Add Caller ID';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Caller ID';	
		$data['cur_menu_no'] = 1;  
                
                $result = $this->accounts_model->get_callerid($account_number);                
                if($result->num_rows() > 0 ){
                    foreach($result->result_array() as $values){
                        $data['account_id'] = $values['accountid'];
                        $data['callerid_name']=$values['callerid_name'];
                        $data['callerid_number'] = $values['callerid_number'];
                        $data['status'] = $values['status'];
                    }
                }
                else{
                    $data['account_id'] =  $account_number;
                }                
                $account_num = $this->accounting_model->get_account_number($account_number);                 
                $data['account'] = $account_num['number'];			  
                
                if(!empty($_POST))
                {
                        $this->accounts_model->add_callerid($_POST);		            	
                	$this->redirect_notification('CallerID Added successfully...','/accounts/account_list/');							
                }
              
                $this->load->view('view_accounts_add_callerid',$data);
        }
	
}


?>