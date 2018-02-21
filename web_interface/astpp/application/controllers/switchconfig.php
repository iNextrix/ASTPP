<?php

class Switchconfig extends CI_Controller
{
	function  Switchconfig()
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
		
		$this->load->model('switch_config_model');
		$this->load->model('Astpp_common');
        $this->load->model('accounts_model');
		
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
		$access_control = validate_access($logintype,$method, "switchconfig");
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
		$this->fssipdevices();
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions fssipdevices_search------
	 * We post an array of fssip devices field to CI database session variable fssipdevices_search
	 */
	function fssipdevices_search()
	{
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('fssipdevices_search', $_POST);		
		}		
		redirect(base_url().'switchconfig/fssipdevices/');
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions clearsearchfilter_fssipdevices------
	 * Empty CI database session variable fssipdevices_search for normal listing
	 */
	function clearsearchfilter_fssipdevices()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('fssipdevices_search', "");
		redirect(base_url().'switchconfig/fssipdevices/');
		
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions fssipdevices------
	 * @action: Add, Edit, Delete fssip devices
	*/
	function fssipdevices($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Switch Config | FSS-IP Devices';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'FREESWITCH(TM) SIP DEVICES';	
		$data['cur_menu_no'] = 9;
		
		if($action === false)
		$action = "list";
		
		
		if($action == 'list')
		{
			$this->load->view('view_switchconfig_fssipdevices',$data);
		}
		elseif($action == 'add')
		{			
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['fs_username']) == "")
				$errors .= "Username is required<br />";
				if(trim($_POST['fs_password']) == "")
				$errors .= "Password is required<br />";
				if(trim($_POST['vm_password']) == "")
				$errors .= "VM Password is required<br />";							
								
				if ($errors == "")
				{				
					$this->switch_config_model->add_switch($_POST);
					$this->session->set_userdata('astpp_notification', 'Switch Configuration added successfully!');
					redirect(base_url().'switchconfig/fssipdevices');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'switchconfig/fssipdevices');				
				}			
			}
			$this->load->view('view_switchconfig_fssipdevices_add',$data);		
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['fs_username']) == "")
				$errors .= "Username is required<br />";
				if(trim($_POST['fs_password']) == "")
				$errors .= "Password is required<br />";
				if(trim($_POST['vm_password']) == "")
				$errors .= "VM Password is required<br />";
							
				if ($errors == "")
				{				
					$this->switch_config_model->edit_switch($_POST);
					$this->session->set_userdata('astpp_notification', 'Switch Configuration updated successfully!');
					redirect(base_url().'switchconfig/fssipdevices');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'switchconfig/fssipdevices');				
				}
			}	
			else
			{	
			  if($switch = $this->switch_config_model->get_switch_by_id($id))
			  {
				  $data['switch'] = $switch;	
			  }
			  else
			  {
				  echo "This Switch Configuration is not available.";
				  return;
			  }	
			  $this->load->view('view_switchconfig_fssipdevices_add',$data);	
			}
		}
		elseif($action == 'delete')
		{
			if (!($switch = $this->switch_config_model->get_switch_by_id($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Switch Configuration not found!');
				redirect(base_url().'switchconfig/fssipdevices');
			}
			
			$this->switch_config_model->remove_switch($switch);		
			$this->session->set_userdata('astpp_notification', 'Switch Configuration removed successfully!');
			redirect(base_url().'switchconfig/fssipdevices');
		}			
		
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions fssipdevices_grid------
	 * Listing of fssip devices data through php function json_encode
	 */
	function fssipdevices_grid()
	{
		$json_data = array();
		$json_data['page'] = 1;	
		$json_data['total'] = 1;	
		$domain = "";
		$accountinfo = $this->session->userdata('accountinfo');
		$accountcode = $accountinfo['number'];
		$cc = $accountinfo['cc'];
		
		
		/*if ($accountcode || $cc) {
			$tmp = "SELECT directory.id AS id, directory.username AS username, directory.domain AS domain FROM " 
			. "directory,directory_vars WHERE directory.id = directory_vars.directory_id "
			. "AND directory_vars.var_name = 'accountcode' "
			. "AND directory_vars.var_value IN (".$accountcode. ",".$cc.")";	
		}
		else{*/
			$tmp = "SELECT id,username,domain FROM directory ";
			if($this->session->userdata('logintype') == 1)
			{
				$tmp .= " WHERE username = '".$this->session->userdata('user_name')."'"; 
				if ($domain) {
				$tmp .= " AND domain IN( '".$domain."','$${local_ip_v4}')";
				} 
			}
		/*}*/
		
		$this->db_fs = Common_model::$global_config['fs_db'];
		
		$query1 = $this->db_fs->query($tmp);
		$count_all = $query1->num_rows();		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = (!isset($_GET['rp']) && $_GET['rp']!='')?20:$_GET['rp'];

		$page_no = (!isset($_GET['page']) && $_GET['page']!='')?1:$_GET['page'];
		
		$json_data = array();
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		
		$query = $this->db_fs->query($tmp. " limit $start, $perpage");		 
// 		echo "<pre>";print_r($query);
		$device_list = array();
// 		echo "<pre>";print_r($query);
		if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $record)
			{
				
				$deviceinfo = $this->switch_config_model->fs_retrieve_sip_user($record['id']);
				$row = array();
				$row['directory_id'] = $record['id'];
				$row['tech']         = "SIP";
				$row['type']         = "user@" . $record['domain'];
				$row['fs_username']     = $record['username'];
				$row['fs_password']     = $deviceinfo['password'];
				$row['vm_password']   = $deviceinfo['vm_password'];
				$row['accountcode']  = $deviceinfo['accountcode'];
				$row['context']      = $deviceinfo['context'];
				
				array_push($device_list, $row);				
			}
			
			
		}
		
		foreach($device_list as $key => $value)	{
	
		$json_data['rows'][] = array('cell'=>array(
					$value['directory_id'],
					$value['fs_username'],
					$value['fs_password'],
					$value['vm_password'],
					$value['accountcode'],
					$value['context'],
					$this->get_action_buttons_fssipdevices($value['directory_id'])
				));
		}
			
		echo json_encode($json_data);			
	}
	
	function get_action_buttons_fssipdevices($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/switchconfig/fssipdevices/edit/'.$id.'/" class="icon" rel="facebox" '.$update_style.' title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/switchconfig/fssipdevices/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions acl_list------
	 * @action: Delete ACL
	 */
	function acl_list($action=false,$id=false)
    {
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Switch Config | Access Control List (ACL)';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Access Control List';	
		$data['cur_menu_no'] = 9;
		//$action = "list";
		
		
		if($action == 'list')
		{
			$this->load->view('view_switchconfig_acl_list',$data);
		}

                elseif($action == 'delete')
		{
			if (!($ip_config = $this->switch_config_model->get_ip_map($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'ACL not found!');
				redirect(base_url().'switchconfig/acl_list');
			}
                        $this->accounts_model->remove_ip_mapping($ip_config);		
			$this->session->set_userdata('astpp_notification', 'ACL removed successfully!');
			redirect(base_url().'switchconfig/acl_list');
		}
                $this->load->view('view_switchconfig_acl_list',$data);		
        }
        
	function get_action_buttons_acl_list($id, $account)
	{
		$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url .= '<a href="/switchconfig/acl_list/delete/'.$id.'/'.$account.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
				return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller switchconfig functions acl_grid------
	 * Listing of ACL data through php function json_encode
	 */
	function acl_grid()
	{
	$json_data = array();
	$json_data['page'] = 1;	
	$json_data['total'] = 1;
	
	$domain = "";

			$ip_list = "SELECT * FROM ip_map ";
			$this->db_astpp = $this->load->database('default',TRUE);
			$query1 = $this->db_astpp->query($ip_list);
	$count_all = $query1->num_rows();
	
	$config['total_rows'] = $count_all;			
	$config['per_page'] = $_GET['rp']=10;

	$page_no = $_GET['page']=1; 
	
	$json_data = array();
	$json_data['page'] = $page_no;			
	$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
				
	$perpage = $config['per_page'];
	$start = ($page_no-1) * $perpage;
	if($start < 0 )
	$start = 0;
	
	
	$query = $this->db_astpp->query($ip_list. " limit $start, $perpage");		 
	
	$acl_list = array();
	
	if($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $record)
		{
			//$deviceinfo = $this->switch_config_model->fs_retrieve_sip_user($record['id']);
			$row = array();
			$row['account']         = $record['account'];
			$row['ip'] = $record['ip'];
			$row['prefix']         = $record['prefix'];
			$row['context']     = $record['context'];
			$row['created_date']     = $record['created_date'];
			
			array_push($acl_list, $row);
		}
	}
	
	foreach($acl_list as $key => $value)	{
				$json_data['rows'][] = array('cell'=>array(
									$value['account'],		
									$value['ip'],
				$value['prefix'],
				$value['context'],
									$value['created_date'],    
									$this->get_action_buttons_acl_list($value['ip'],$value['account'])
			));
	}
	echo json_encode($json_data);			
}
}
?>