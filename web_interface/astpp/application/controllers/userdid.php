<?php

class Userdid extends CI_Controller
{
	function  Userdid()
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
		
		$this->load->model('Userdid_model');
		
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
		$access_control = validate_access($logintype,$method, "userdid");
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
	 * -------Here we write code for controller userdid functions manage------
	 * @manage: 
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
			$this->load->view('view_userdid_manage',$data);
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
					$this->did_model->add_did($_POST);
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
				$errors = "";
				if(trim($_POST['number']) == "" || !is_numeric($_POST['number']))
				$errors .= "Number is Invalid<br />";
				
				if ($errors == "")
				{				
					$this->did_model->edit_did($_POST);
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

		$ret_url = '<a href="/userdid/manage/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/userdid/manage/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';

		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller userdid functions manage_json------
	 * Listing of User DID data through php function json_encode
	 */
	function manage_json()
	{
		$json_data = array();
		
		
		$count_all = $this->Userdid_model->getUserdidCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->Userdid_model->getUserdidList($start, $perpage);
		
		//$query = $this->db->get('callingcardcdrs');
		if($query->num_rows() > 0)
		{
		   	foreach ($query->result_array() as $row)
		   	{
				$accountinfo = $this->session->userdata('accountinfo');	
				$reseller = $accountinfo['reseller'];	
				$record = $this->Astpp_common->get_did_reseller_new($row['number'], $reseller);
				
				$monthlycost = number_format(($record['monthlycost'] / 10000), 2 );
				
				$json_data['rows'][] = array('cell'=>array(
					$record['number'],
					$record['connectcost'],
					$record['includedseconds'],
					$record['cost'],
					$monthlycost,
					$record['country'],
					$record['province'],
					$record['city'],
					$record['extensions'],
					$this->get_action_buttons($record['number'])
				));
		   }
 		}	
		echo json_encode($json_data);
		
	}
	
	/**
	 * -------Here we write code for controller userdid functions import------
	 * Import DID
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