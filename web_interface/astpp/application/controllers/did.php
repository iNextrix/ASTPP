<?php

class Did extends CI_Controller
{
	function  Did()
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
		
		$this->load->model('did_model');
		$this->load->model('Astpp_common');
		
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
		$access_control = validate_access($logintype,$method, "did");
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
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'DID';	
				
		$this->load->view('view_did',$data);
	}
	
	/**
	 * -------Here we write code for controller did functions manage------
	 * @action: Add, Edit, Delete, List DID
	 * @id: DID number
	 */
	function manage($action=false,$id=false)
	{
		
		$data['cur_menu_no'] = 5;
		
		if ($action === false)
		{
		    $action = 'list';
		}
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | '.ucwords($action) . " DIDs";

		if($action == 'list')
		{
			$data['page_title'] = 'Manage DID';	
			$this->load->view('view_did_manage',$data);
		}
		if($action == 'add')
		{	
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['number']) == "" || !is_numeric($_POST['number']))
				$errors .= "Number is Invalid<br />";
				
				if ($errors == "")
				{		
					$_POST['setup']=$this->common_model->add_calculate_currency($_POST['setup'],'','',false,false);					
					$_POST['disconnectionfee']=$this->common_model->add_calculate_currency($_POST['disconnectionfee'],'','',false,false);  
					$_POST['monthlycost']=$this->common_model->add_calculate_currency($_POST['monthlycost'],'','',false,false);  
					$_POST['connectcost']=$this->common_model->add_calculate_currency($_POST['connectcost'],'','',false,false);  
					$_POST['cost']=$this->common_model->add_calculate_currency($_POST['cost'],'','',false,false); 
					$this->did_model->add_did($_REQUEST);
					$this->session->set_userdata('astpp_notification', 'DID added successfully!');
					redirect(base_url().'did/manage/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);				
					redirect(base_url().'did/manage/');
				}			
			}			
			$data['providers'] = $this->common_model->list_providers_select('');
		    $data['accounts'] = $this->common_model->list_accounts_select('');
			$this->load->view('view_did_manage_add',$data);
		}
		if($action == 'edit')
		{	
			
				if(!empty($_POST))
				{
// 				    echo "<pre>";print_r($_REQUEST);exit;
					$errors = "";
					if(trim($_POST['number']) == "" || !is_numeric($_POST['number']))
					$errors .= "Number is Invalid<br />";
					
					if ($errors == "")
					{	$_POST['setup']=$this->common_model->add_calculate_currency($_POST['setup'],'','',false,false);					
					$_POST['disconnectionfee']=$this->common_model->add_calculate_currency($_POST['disconnectionfee'],'','',false,false);  
					$_POST['monthlycost']=$this->common_model->add_calculate_currency($_POST['monthlycost'],'','',false,false);  
					$_POST['connectcost']=$this->common_model->add_calculate_currency($_POST['connectcost'],'','',false,false);  
					$_POST['cost']=$this->common_model->add_calculate_currency($_POST['cost'],'','',false,false);			
						$this->did_model->edit_did($_REQUEST);
						$this->session->set_userdata('astpp_notification', 'DID updated successfully!');
						redirect(base_url().'did/manage/');				
					}
					else 
					{
						$this->session->set_userdata('astpp_errormsg', $errors);				
						redirect(base_url().'did/manage/');
					}
				}	
				else
				{	
					if($this->session->userdata('logintype')==1)
					{
						$reseller_didinfo = $this->Astpp_common->get_did_reseller_new($id,$this->session->userdata('username'));
						$accountinfo = $this->Astpp_common->get_account($this->session->userdata('username'));
							
						if ( $accountinfo['reseller']  != "" ) {
							$didinfo = $this->Astpp_common->get_did_reseller_new( $did['number'], $accountinfo['reseller'] );	
							
						}
						else {
							 $didinfo = $this->did_model->get_did_by_number($id);						
						}
						
						$data['did'] = $id;
						$data['reseller_didinfo'] = $reseller_didinfo;	
						$data['didinfo'] = $didinfo;
						$data['accountinfo'] = $accountinfo;					  
						$this->load->view('view_did_manage_reseller_add',$data);
						
					}
					else
					{
						  if($did = $this->did_model->get_did_by_number($id))
						  {
							  $data['did'] = $did;
							  $data['providers'] = $this->common_model->list_providers_select($did['provider']);
							  $data['accounts'] = $this->common_model->list_accounts_select($did['account']);
						  }
						  else
						  {
							  echo "This DID is not available.";
							  return;
						  }	
						  $this->load->view('view_did_manage_add',$data);		
					}
				}
			
		}
		if($action == 'delete')
		{	
			if($did = $this->did_model->get_did_by_number($id))
			{
				$this->did_model->remove_did($did);
				$this->session->set_userdata('astpp_notification', 'DID deleted successfully!');
				redirect(base_url().'did/manage/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', "Invalid card number.");	
				redirect(base_url().'did/manage/');			
			}	
		}
		
	}
	
	function get_action_buttons($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$import_style = 'style="text-decoration:none;background-image:url(/images/import.png);"';
		$url = '';
		$ret_url = '';

		$ret_url = '<a href="/did/manage/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/did/manage/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';

		return $ret_url;
	}
	
	
	/**
	 * -------Here we write code for controller did functions build_dids_reseller------
	 * Listing of DID reseller data through php function json_encode
	 */
	function build_dids_reseller()
	{
		$json_data = array();	
		
		$count_all = $this->did_model->getdidCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page'];
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->did_model->getdidList($start, $perpage);
		
		if($query->num_rows() > 0)
		{
			if($this->session->userdata('logintype')==1){
				foreach($query->result_array() as $did)
				{
					 $record = array();
					 $didinfo = array();
					 $didinfo = $this->did_model->get_did_by_number($did['number'] );
					
					 $success=0;
					if ( $didinfo['account'] != "" ) {
						
						$accountinfo = $this->Astpp_common->get_account($didinfo['account']);
						
						if ( $accountinfo['reseller'] ==$this->session->userdata('username') || $didinfo['account'] == $this->session->userdata('username') )
						{
							$record = $this->Astpp_common->get_did_reseller_new( $did['number'], $this->session->userdata('username') );
							$success = 1;
						}
					}
					else {
						$record = $this->Astpp_common->get_did_reseller_new($did['number'], $this->session->userdata('username') );
						$success = 1;
					}
					
					
					
					if($success >0){
						
						if($did['limittime']==0){
							$limittime= 'No';
						}
						else{
							$limittime = 'Yes';
						}
						
						if($did['chargeonallocation']==0){
						$chargeonallocation = 'No';
						}
						else{
							$chargeonallocation = 'Yes';
						}	
               
						
								  	
						$json_data['rows'][] = array('cell'=>array(
							$didinfo['number'],
							$didinfo['country'],
							//$didinfo['province'],
							//$didinfo['city'],
							$didinfo['provider'],
							@$record['account'],
							$limittime,
							@$record['extensions'],
							$this->common_model->calculate_currency($record['setup']),
							$this->common_model->calculate_currency($record['disconnectionfee']),
							$this->common_model->calculate_currency($record['monthlycost']),
							$this->common_model->calculate_currency($record['connectcost']),
							@$record['includedseconds'],
							$this->common_model->calculate_currency($record['cost']),
							@$record['inc'],
							@$record['prorate'],
							//@$did['variables'],
							$chargeonallocation,
							@$record['maxchannels'],
							$this->get_action_buttons($did['number'])
						));
				  
					}
				
				}
			}
		}
		echo json_encode($json_data);
	}
	
	
	/**
	 * -------Here we write code for controller did functions build_dids------
	 * Listing of DID data through php function json_encode
	 */
	function build_dids()
	{
		$json_data = array();		
		
		$count_all = $this->did_model->getdidCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;						
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->did_model->getdidList($start, $perpage);
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
				{
							
					if($row['limittime']==0){
						$limittime= 'No';
					}
					else{
						$limittime = 'Yes';
					}
					
					if($row['chargeonallocation']==0){
						$chargeonallocation = 'No';
					}
					else{
						$chargeonallocation = 'Yes';
					}
					
					$json_data['rows'][] = array('cell'=>array(
						$row['number'],
						$row['country'],
// 						$row['province'],
// 						$row['city'],
						$row['provider'],
						$row['account'],
						$limittime,
						$row['extensions'],
						$this->common_model->calculate_currency($row['setup']),
						$this->common_model->calculate_currency($row['disconnectionfee']),
						$this->common_model->calculate_currency($row['monthlycost']),
						$this->common_model->calculate_currency($row['connectcost']),
						$row['includedseconds'],
						$this->common_model->calculate_currency($row['cost']),
						$row['inc'],
						$row['prorate'],
// 						$row['variables'],
						$chargeonallocation,
						$row['maxchannels'],
						$this->get_action_buttons($row['number'])
					));
			   }	
		}
		
		echo json_encode($json_data);
	}
	
	/**
	 * -------Here we write code for controller did functions did_search------
	 * We post an array of did field to CI database session variable did_search
	 */
	function did_search()
	{		
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('did_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'did/manage/');
		}
	}
	
	/**
	 * -------Here we write code for controller did functions clearsearchfilter_did------
	 * Empty CI database session variable did_search for normal listing
	 */
	function clearsearchfilter_did()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('did_search', "");
		redirect(base_url().'did/manage/');		
	}
	
	
	/**
	 * -------Here we write code for controller did functions manage_json------
	 * If login typ is reseller then redirect to did reseller
	 */
	function manage_json()
	{
		if($this->session->userdata('logintype')==1)
		{
			$this->build_dids_reseller();
		}
		else
		{
			$this->build_dids();	
		}
	}
	
	/*function manage_json2()
	{
		if($this->session->userdata('logintype')==1)
		{
			$this->build_dids_reseller();
		}
		else
		{
			$this->build_dids();	
		}
		
		if($query->num_rows() > 0)
		{
			if($this->session->userdata('logintype')==1){
				foreach($query->result_array() as $did)
				{
					 $record = array();
					 $didinfo = array();
					 $didinfo = $this->did_model->get_did_by_number($did['number'] );
					
					 $success=0;
					if ( $didinfo['account'] != "" ) {
						
						$accountinfo = $this->Astpp_common->get_account($didinfo['account']);
						
						if ( $accountinfo['reseller'] ==$this->session->userdata('username') || $didinfo['account'] == $this->session->userdata('username') )
						{
							$record = $this->Astpp_common->get_did_reseller( $did['number'], $this->session->userdata('username') );
							$success = 1;
						}
					}
					else {
						$record = $this->Astpp_common->get_did_reseller($did['number'], $this->session->userdata('username') );
						$success = 1;
					}
					
					if($success >0){
						
						if($did['limittime']==0){
							$limittime= 'No';
						}
						else{
							$limittime = 'Yes';
						}
						
						if($did['chargeonallocation']==0){
						$chargeonallocation = 'No';
						}
						else{
							$chargeonallocation = 'Yes';
						}
						
								  	
						$json_data['rows'][] = array('cell'=>array(
							$didinfo['number'],
							$didinfo['country'],
							$didinfo['province'],
							$didinfo['city'],
							$didinfo['provider'],
							$didinfo['account'],
							$limittime,
							@$record['extensions'],
							@$record['setup'],
							@$record['disconnectionfee'],
							@$record['monthlycost'],
							@$record['prorate'],
							@$record['connectcost'],
							@$record['includedseconds'],
							@$record['cost'],
							@$record['inc'],
							@$did['variables'],
							$chargeonallocation,
							@$record['maxchannels'],
							$this->get_action_buttons($did['number'])
						));
				  
					}
				
				}
			}
			else{
				foreach ($query->result_array() as $row)
				{
							
					if($row['limittime']==0){
						$limittime= 'No';
					}
					else{
						$limittime = 'Yes';
					}
					
					if($row['chargeonallocation']==0){
						$chargeonallocation = 'No';
					}
					else{
						$chargeonallocation = 'Yes';
					}
					
					$json_data['rows'][] = array('cell'=>array(
						$row['number'],
						$row['country'],
						$row['province'],
						$row['city'],
						$row['provider'],
						$row['account'],
						$limittime,
						$row['extensions'],
						$row['setup'],
						$row['disconnectionfee'],
						$row['monthlycost'],
						$row['prorate'],
						$row['connectcost'],
						$row['includedseconds'],
						$row['cost'],
						$row['inc'],
						$row['variables'],
						$chargeonallocation,
						$row['maxchannels'],
						$this->get_action_buttons($row['number'])
					));
			   }
			}
 		}	
		echo json_encode($json_data);
		
	}*/
	
	/**
	 * -------Here we write code for controller did functions import------
	 * Import all did data
	 */
	function import()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | DID | Import';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Import DID';	
		$data['cur_menu_no'] = 5;
		$this->load->view('view_did_import',$data);		
	}

}


?>
