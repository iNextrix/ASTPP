<?php 

class User extends CI_Controller
{
	
	function User()
	{
		parent::__construct();
		
		$this->load->helper('template_inheritance');
		$this->load->helper('authorization');
		$this->load->helper('form');
		$this->load->helper('romon');
		$this->load->library('astpp');	

		$this->load->library('session');
		$this->load->library('form_builder');
		
		$this->load->model('Pricelists_model');
                $this->load->model('accounts_model');
                $this->load->model('common_model');
		
		$this->load->model('cc_model');
		$this->load->model('Astpp_common');
		$this->load->model('userdid_model');
$this->load->model('usermodel');
		
		if($this->session->userdata('user_login')== FALSE)
			redirect(base_url().'astpp/login');
		
	}
	
	/*
	 * CI has a built in method named _remap which allows
	 * you to overwrite the behavior of calling your controller methods over URI
	*/
	public function _remap($method, $params = array())
	{
		$logintype = $this->session->userdata('logintype');
		$access_control = validate_access($logintype,$method, "user");
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
		if($this->session->userdata('user_login')== FALSE)
		{
			redirect(base_url().'astpp/login');
		}
		else 
		{
			redirect(base_url().'user/dashboard');
		}
	}
	
	/**
	 * -------Here we write code for controller user functions dashboard------
	 * User Dashboard
	 */
	function dashboard()
	{
		if($this->session->userdata('user_login')== FALSE)
			redirect(base_url().'astpp/login');
					
		$data['username'] = $this->session->userdata('username');				
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['cur_menu_no'] = 1;
		//$data['astpp_sidebar'] = 1;
		
		$this->load->view('view_user_dashboard',$data);
		
	}
	
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url().'astpp/');
	}
	
	/**
	 * -------Here we write code for controller user functions cclist------
	 * List Calling Cards
	 */
	function cclist()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'List Calling Cards';	
		$data['cur_menu_no'] = 3;
		
		$this->load->view('view_user_cc_list',$data);		
	}
	
	/**
	 * -------Here we write code for controller user functions viewcard_json------
	 * View User Card List
	 */
	function viewcard_json()
	{
		
		$json_data = array();
		$json_data['page'] = 1;	
		$json_data['total'] = 0;	
		
		$this->load->model('cc_model');		
		
		$count_all = $this->cc_model->getUserCountViewCard();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];
  
		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
  		
		//$query = $this->Astpp_model->getCCList($brands, $start,$perpage);
		$query = $this->cc_model->getUserViewCardList($start, $perpage);
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{	
					$json_data['rows'][] = array('cell'=>array(
						$row['destination'],
						$row['disposition'],
						$row['clid'],
						$row['callstart'],
						$row['seconds'],
						$this->common_model->calculate_currency($row['debit'])	
					));
			}
		}
		echo json_encode($json_data);
		
	
	}
	
	/**
	 * -------Here we write code for controller user functions manage_json------
	 * User Count Calling Card
	 */
	function manage_json()
	{
		$json_data = array();
		$json_data['page'] = 0;	
		$json_data['total'] = 0;	
		
		$this->load->model('cc_model');
		
		$count_all = $this->cc_model->getUserCountCC();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];
  
		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
  
		//$query = $this->Astpp_model->getCCList($brands, $start,$perpage);
		$query = $this->cc_model->getUserCCList($start, $perpage);
	
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				if ( $row['inuse'] == 0 ) {
					$inuse = 'no';
				}
				elseif( $row['inuse'] == 1  ) {
					$inuse = 'yes';
				}
				
				if ( $row['status'] == 0 ) {
					$cardstat = 'inactive';
				}
				elseif ( $row['status'] == 1) {
					$cardstat = 'active';
				}
				elseif ( $row['status'] == 2 ) {
					$cardstat = 'deleted';
				}  
				if($row['inuse'] == 0)
				{
					$inuse = 'no';
				}
				else 
				{
					$inuse = 'yes';
				}
					$json_data['rows'][] = array('cell'=>array(
						'<a href="/callingcards/view/'.$row['cardnumber'].'/" rel="facebox" >'.$row['cardnumber'].'</a>',
						$row['pin'],
						$row['brand'],
						$this->common_model->calculate_currency($row['value']),
						$this->common_model->calculate_currency($row['used']),
						$row['validfordays'],
						$row['created'],
						$row['firstused'],
						$row['expiry'],
						$inuse,
						$cardstat
					));
			}
		}
		echo json_encode($json_data);
		
	}
	
	/**
	 * -------Here we write code for controller user functions didslist------
	 * List of Available Dids
	 * @action: Delete
	 */
	function didslist($action=false,$id=false)
	{
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		//$data['username'] = $this->session->userdata('user_name');	
		$data['username'] = $this->session->userdata('username');
		$data['page_title'] = 'DIDs';	
		$data['cur_menu_no'] = 5;
		
		$data['availabledids'] = $this->Astpp_common->list_available_dids($data['username']);
		
		if ($action === false)
		{
		    $action = 'list';
		}
		
		if($action == 'delete')
		{	
			if($did = $this->userdid_model->get_did_by_number($id))
			{
				$data['did'] = $did;
				$this->userdid_model->remove_did($data);
				$this->session->set_userdata('astpp_notification', 'DID deleted successfully!');
				redirect(base_url().'user/didslist/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', "Invalid card number.");	
				redirect(base_url().'user/didslist/');			
			}	
		}
		
		if($action == 'list')
		{
			$this->load->view('view_user_did_manage',$data);		
		}
	}
	
	/**
	 * -------Here we write code for controller user functions dids_json------
	 * List of Dids
	 */
	function dids_json($did_list=NULL)
	{	
		$json_data = array();
		$json_data['page'] = 1;	
		$json_data['total'] = 0;	
		
		$this->load->model('did_model');
		
		if($did_list!=NULL && $did_list!='null')
		{
			$data['did_list'] = $did_list;
			$msg = $this->did_model->purchase_did($data);
			//$this->session->set_userdata('astpp_notification', 'DID mapped to extension successfully! DID Assigned Successfully!');			
			$this->session->set_userdata('astpp_notification', $msg);			
			redirect(base_url().'user/didslist');
		}
		
		$this->load->model('accounts_model');
		$accountinfo = $this->accounts_model->get_account($this->session->userdata('username'));
		
		$count_all = $this->did_model->getUserCountDIDS($accountinfo['number']);
	
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];
  
		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
  
		//$query = $this->Astpp_model->getCCList($brands, $start,$perpage);
		$query = $this->did_model->getUserDIDSList($start, $perpage, @$accountinfo['number']);
		
		$this->load->model('astpp_common');
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$record = $this->astpp_common->get_did_reseller_new($row['number'],@$accountinfo['reseller']);
				
					$json_data['rows'][] = array('cell'=>array(
						@$record['number'],
						$this->common_model->calculate_currency($record['connectcost']),
						@$record['includedseconds'],
						$this->common_model->calculate_currency($record['cost']),
						$this->common_model->calculate_currency($record['monthlycost']),
						@$record['country'],
						@$record['province'],
						@$record['city'],
						@$record['extensions'],
						$this->get_action_buttons(@$record['number'])				
					));
			}
		}
		echo json_encode($json_data);
		
	
	}
	
	/**
	 * -------Here we write code for controller user functions remove_dids------
	 * Remove DIDs.
	 * @did: DID Id
	 */
	function remove_dids($did="")
	{
		$data['did'] = @$did;
		
		$msg = $this->userdid_model->remove_did($data);		
		$this->session->set_userdata('astpp_notification', $msg);			
		redirect(base_url().'user/didslist');	
	}
	
	/**
	 * -------Here we write code for controller user functions edit_did------
	 * Edit Dids
	 * @did: DID Id
	 */
	function edit_did($did="")
	{
	
		$didinfo = $this->userdid_model->get_did_by_number($did);	
		
		$this->load->model('accounts_model');					
		$accountinfo = $this->accounts_model->get_account($this->session->userdata('user_name'));
		$data['didinfo'] = $didinfo;
		$data['accountinfo'] = $accountinfo;	
		$data['number'] = @$did;	
				
		$this->load->view('view_user_edit_did', $data);
	}
	
	
	function editdid()
	{
		$msg = $this->userdid_model->edit_did($_POST);
		$this->session->set_userdata('astpp_notification', $msg);
		redirect(base_url().'user/didslist');
	}
	
		
	function get_action_buttons($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		
		$ret_url = '<a href="#" onclick="edit_did_confirm(\''.$id.'\')" class="icon" '.$update_style.' title="Update">&nbsp;</a>';
		//$ret_url = '<a href="/user/edit_did/'.$id.'/"  class="icon" '.$update_style.' title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/user/remove_dids/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller user functions accountsdetail------
	 * Get Account Detail
	 */
	function accountsdetail()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'Account Details';	
		
		$data['cur_menu_no'] = 2;
		
		$this->load->model('accounts_model');
		
		if ($account = $this->accounts_model->get_account_by_number($this->session->userdata('username')))
		{			  
// 			$data['balance'] = $this->common_model->calculate_currency($account['balance']);
			$data['balance'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance($data['username']));
			$data['credit_limit'] = $this->common_model->calculate_currency($account['credit_limit']);
			$data['account'] = $account;
		}
		
		$this->load->view('view_user_accounts_details',$data);
	}
	
	/**
	 * -------Here we write code for controller user functions account_detail_json------
	 * List of CDRS
	 */
	function account_detail_json()
	{
		$this->load->model('accounts_model');
		$account_number = $this->session->userdata('username');		
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
					 
					  $row['profit'] = ( $record['debit'] - $record['cost'] );
// 					  $row['cost'] = money_format('%.6n', $row['cost'] );
					  
				    if ( $record['debit'] ) {
					$row['debit'] = $record['debit'];
// 					$row['debit'] = money_format('%.6n', $row['debit'] );
					}
					else {
						$row['debit'] = "-";
					}
					  
					if ( $record['credit'] ) {
// 						$row['credit'] = $record['credit'] / 10000;
						$row['credit'] = $record['credit'];
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
						$this->common_model->calculate_currency($row['debit']),
						$this->common_model->calculate_currency($row['credit']),
						$row['notes']/*,
						$this->common_model->calculate_currency($row['cost']),
						$row['profit']*/));
															   
				}
			}
				
			echo json_encode($json_data);		
	
	}
	
	/**
	 * -------Here we write code for controller user functions chargelist_json------
	 * List of Charge List
	 */
	function chargelist_json()
	{
		
		$this->load->model('common_model');
		$sweeplist = $this->common_model->get_sweep_list();
		
		$this->load->model('accounts_model');	
		$accountinfo = $this->accounts_model->get_account($this->session->userdata('username'));
		
		$this->load->model('Astpp_common');
		$account_charge_list = $this->Astpp_common->list_account_charges($accountinfo['number']);		
		
		$pricelist_charge_list = $this->Astpp_common->list_pricelist_charges($accountinfo['pricelist']);	
		
		$charge_list = array();
		
		foreach($account_charge_list as $key => $value){
			
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
					$json_data['rows'][] = array('cell'=>array($charge_list[$i]['id'],
								      $charge_list[$i]['description'],
								      $charge_list[$i]['sweep'],
								      $this->common_model->calculate_currency($charge_list[$i]['charge'])
								      )); 
				 }
		 	
		 }
		
		echo json_encode($json_data);		
	
	}

	/**
	 * -------Here we write code for controller user functions userdids_json------
	 * List of User DIDs List
	 */
	function userdids_json()
	{
		$json_data = array();
		$json_data['page'] = 1;	
		$json_data['total'] = 0;	
		
		$this->load->model('accounts_model');	
		$accountinfo = $this->accounts_model->get_account($this->session->userdata('username'));
		
		$this->load->model('did_model');
		$count_all = $this->did_model->getUserCountDIDS($accountinfo['number']);
	
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];
  
		$page_no = $_GET['page']; 
		
		$json_data = array();
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
  
		//$query = $this->Astpp_model->getCCList($brands, $start,$perpage);
		$query = $this->did_model->getUserDIDSList($start, $perpage, @$accountinfo['number']);
		
		$this->load->model('astpp_common');
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$record = $this->astpp_common->get_did_reseller_new($row['number'],@$accountinfo['reseller']);								
				$monthlycost = $this->common_model->calculate_currency($record['monthlycost']);	
												
				$json_data['rows'][] = array('cell'=>array(
						@$row['number'],
						$monthlycost,
						$this->get_dids_action_buttons(@$row['number'])				
					));
			}
		}
		echo json_encode($json_data);
	}
	
	function get_dids_action_buttons($id)
	{
		$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url .= '<a href="/user/dids/remove/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
			
        function user_invoice_list($action=false,$id=false)
        {
             $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
             $data['username'] = $this->session->userdata('user_name');	
             $data['page_title'] = 'Account Invoice List';
             $data['cur_menu_no'] = 6;
             
             
             if($action == false)
             $action = "list";
		
             if($action == 'list')
              {
                    $this->load->view('view_user_invoice_list',$data);
              }
            
            
        }

        
        function userinvoice_json()
        {
        	$accountinfo = $this->session->userdata['accountinfo'];
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
		$json_data['rows'] = array();
		$query = $this->accounts_model->list_invoice($accountinfo['accountid'], $start, $perpage);	
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
                            
                           $json_data['rows'][] = array('cell'=>array(
                                                $row['invoiceid'],
                                                $row['date'],
                                                $this->common_model->calculate_currency($row['value']),
                                                $this->get_action_buttons_invoice($row['invoiceid'])
                                                ));
			}
		}
		echo json_encode($json_data);

        }
        
        function get_action_buttons_invoice($invoiceid)
	{
        	$details_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
                $pdf_style = 'style="text-decoration:none;background-image:url(/images/pdf.png);"';
		$ret_url = '';
		$ret_url .= '<a href="'.base_url().'accounts/view_invoice/'.$invoiceid.'/" class="icon" '.$details_style.' title="Details">&nbsp;</a>';
                $ret_url .= '<a href="'.base_url().'accounts/download_invoice/'.$invoiceid.'/" class="icon" '.$pdf_style.' title="Details">&nbsp;</a>';
		return $ret_url;
	}
                        
    	function add_callerid($card_number="")
        {
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | CallingCards | Add CC Caller ID';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Caller ID';	
		$data['cur_menu_no'] = 3;  
                
                $result = $this->cc_model->cc_get_callerid($card_number);
                if($result->num_rows() > 0 ){
                    foreach($result->result_array() as $values){
                        $data['cardnumber'] = $values['cardnumber'];
                        $data['callerid_name']=$values['callerid_name'];
                        $data['callerid_number'] = $values['callerid_number'];
                        $data['status'] = $values['status'];
                    }
                }
                else{
                    $data['cardnumber'] =  $card_number;
                }
                
                if(!empty($_POST))
                {
                        $this->cc_model->cc_add_callerid($_POST);
                	$this->redirect_notification('CallerID Added successfully...','/user/cclist/');									
                }
              
                $this->load->view('view_user_add_callerid',$data);
        }
        
        function redirect_notification($notificationmsg,$redirect)
	{
		$this->session->set_userdata('astpp_notification', $notificationmsg);
		redirect(base_url().$redirect);		
	}
        
        
         function search()
	{		
		$ajax_search = $this->input->post('ajax_search',0);

                if($this->input->post('advance_search', TRUE)==1) {		
                       $this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('user_invoice_search', $_POST);	
		}
		if(@$ajax_search!=1) {
			redirect(base_url().'user/user_invoice_list');
		}
	}
        
        function clearsearchfilter()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('user_invoice_search', "");
		redirect(base_url().'user/user_invoice_list');		
	}
	
 function edit_account($name)
        {
            
            $data['name']="Edit User Account";
            $detail=$this->usermodel->get_account($name);
            $data['record']=$detail->result();
            $this->load->view('view_user_edit_account',$data);
        }
	function update($id)
        {
           $this->usermodel->edit_account($_POST);
           redirect("user/accountsdetail");
        }
        /*===================================================================================================*/

        
/*=====================================================================================================================================================*/
	

	
}



?>