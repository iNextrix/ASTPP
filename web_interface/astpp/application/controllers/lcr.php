<?php

class Lcr extends CI_Controller
{
	function  Lcr()
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
		
		$this->load->model('lcr_model');
		
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
		$access_control = validate_access($logintype,$method, "lcr");
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
		$data['page_title'] = 'Providers';	
		$data['cur_menu_no'] = 6;
		
		$this->load->view('view_lcr',$data);
	}
	
	
	function providers()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Provider Details';	
		$data['cur_menu_no'] = 6;	
		$this->load->view('view_lcr_providers',$data);
		
	}
	
	function get_action_buttons_providers($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/accounts/edit/'.$id.'/" class="icon" '.$update_style.' title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/accounts/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}	
	
	/**
	 * -------Here we write code for controller lcr functions provider_search------
	 * We post an array of provider field to CI database session variable providers_search
	 */
	function provider_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);	
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('providers_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'lcr/providers/');
		}
	}
	
	/**
	 * -------Here we write code for controller lcr functions clearsearchfilter------
	 * Empty CI database session variable providers_search for normal listing
	 */
	function clearsearchfilter()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('providers_search', "");
		redirect(base_url().'lcr/providers/');
		
	}
	
	/**
	 * -------Here we write code for controller lcr functions providers_grid------
	 * Listing of Provider info with credit limit, balance and number
	 */
	function providers_grid()
	{
		$json_data = array();
		
		$count_all = $this->lcr_model->getProviderCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->lcr_model->getProviderList($start, $perpage);
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
		        if ( $row['pin'] == 0 ) {
		            $pins = 'NO';
		        }
		        elseif( $row['pin'] == 1  ) {
		            $pins = 'YES';
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
				$json_data['rows'][] = array('cell'=>array(
					$row['number'],
					$this->common_model->calculate_currency($row['credit_limit']),
					$this->common_model->calculate_currency($row['balance']),
					$this->get_action_buttons_providers($row['number'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller lcr functions trunks------
	 * @action: Add, Edit, Delete and list trunks
	 * @id: Trunk Id
	 */
	function trunks($action=false,$id="")
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Trunks';	
		$data['cur_menu_no'] = 6;

		if ($action === false)
		{
		    $action = 'list';
		}

		if($action == 'list')
		{
			$data['page_mode'] = 'list';
			$this->load->view('view_lcr_trunks',$data);
		}
		elseif($action == 'add')
		{
			
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Trunk Name is required<br />";
				if(trim($_POST['path']) == "")
				$errors .= "Peer name is required<br />";
				
				if ($errors == "")
				{				
					$this->lcr_model->add_trunk($_POST);
					$this->session->set_userdata('astpp_notification', 'Trunk added successfully!');
					redirect(base_url().'lcr/trunks/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'lcr/trunks/');				
				}			
			}
			
			$data['providers'] = $this->common_model->list_providers_select('');
			$data['sellersList'] = $this->common_model->list_resellers();			
			$this->load->view('view_lcr_trunks_add',$data);			
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Trunk Name is required<br />";
				if(trim($_POST['path']) == "")
				$errors .= "Peer name is required<br />";
				
				if ($errors == "")
				{				
					$this->lcr_model->edit_trunk($_POST);
					$this->session->set_userdata('astpp_notification', 'Trunk updated successfully!');
					redirect(base_url().'lcr/trunks/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'lcr/trunks/');				
				}
			}	
			else
			{	
			  if($trunk = $this->lcr_model->get_trunk_by_name(urldecode($id)))
			  {
				  $data['trunk'] = $trunk;
				  $data['providers'] = $this->common_model->list_providers_select($trunk['provider']);
				  $data['sellersList'] = $this->common_model->list_resellers();	
			  }
			  else
			  {
				  echo "This trunk is not available.";
				  return;
			  }	
			  $this->load->view('view_lcr_trunks_add',$data);		
			}
		}
		elseif($action == 'delete')
		{
			if (!($trunk= $this->lcr_model->get_trunk_by_name(urldecode($id))))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Trunk not found!');
				redirect(base_url().'lcr/trunks/');
			}
			
			$this->lcr_model->remove_trunk($trunk);		
			$this->session->set_userdata('astpp_notification', 'Trunk removed successfully!');
			redirect(base_url().'lcr/trunks/');
		}
		
	}
	
	function get_action_buttons_trunks($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/lcr/trunks/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/lcr/trunks/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}	
	
	/**
	 * -------Here we write code for controller lcr functions trunks_search------
	 * We post an array of trunks field to CI database session variable trunks_search
	 */
	function trunks_search()
	{		
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('trunks_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'lcr/trunks/');
		}
	}
	
	
	/**
	 * -------Here we write code for controller lcr functions clearsearchfilter_trunks------
	 * Empty CI database session variable trunks_search for normal listing
	 */
	function clearsearchfilter_trunks()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('trunks_search', "");
		redirect(base_url().'lcr/trunks/');		
	}
	
	/**
	 * -------Here we write code for controller lcr functions trunks_grid------
	 * Listing of trunks table data through php function json_encode
	 */
	function trunks_grid()
	{
		$json_data = array();		
			
		$count_all = $this->lcr_model->getTrunkCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->lcr_model->getTrunkList($start, $perpage);

		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
		        if ( $row['status'] == 0 ) {
		            $cardstat = 'inactive';
		        }
		        elseif ( $row['status'] == 1) {
		            $cardstat = 'active';
		        }
		        elseif ( $row['status'] == 2 ) {
		            $cardstat = 'deleted';
		        }
				$json_data['rows'][] = array('cell'=>array(
					$row['name'],
					$row['tech'],
					$row['path'],
					$row['provider'],
					$row['maxchannels'],
					$row['dialed_modify'],
					$row['precedence'],
					$row['resellers'],
					$this->get_action_buttons_trunks($row['name'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller lcr functions outbound_search------
	 * We post an array of outbound field to CI database session variable outbound_search
	 */
	function outbound_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
			
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('outbound_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'lcr/outbound/');
		}
	}
	
	/**
	 * -------Here we write code for controller lcr functions clearsearchfilter_outbound------
	 * Empty CI database session variable outbound_search for normal listing
	 */
	function clearsearchfilter_outbound()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('outbound_search', "");
		redirect(base_url().'lcr/outbound/');		
	}
	
	/**
	 * -------Here we write code for controller lcr functions outbound------
	 * @action: Add, Edit, Delete and list outbound
	 * @id: Outbound Id
	 */
	function outbound($action=false,$id="")
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Termination Rates';	
		$data['cur_menu_no'] = 6;
		
		if ($action === false)
		{
		    $action = 'list';
		}

		if($action == 'list')
		{
			$this->load->view('view_lcr_outbound',$data);
		}
		elseif($action == 'add')
		{			
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['pattern']) == "")
				$errors .= "Pattern is required<br />";
				
				if ($errors == "")
				{	
					$_POST['connectcost']=$this->common_model->add_calculate_currency($_POST['connectcost'],'','',false,false);		
					$_POST['cost']=$this->common_model->add_calculate_currency($_POST['cost'],'','',false,false);		
					$this->lcr_model->add_outbound($_POST);
					$this->session->set_userdata('astpp_notification', 'Outbound added successfully!');
					redirect(base_url().'lcr/outbound/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'lcr/outbound/');				
				}			
			}
			
			$data['trunks'] = $this->common_model->list_trunks_select('');
			$data['sellersList'] = $this->common_model->list_sellers();
			$this->load->view('view_lcr_outbound_add',$data);			
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['pattern']) == "")
				$errors .= "Pattern is required<br />";
				
				if ($errors == "")
				{	
					$_POST['connectcost']=$this->common_model->add_calculate_currency($_POST['connectcost'],'','',false,false);		
					$_POST['cost']=$this->common_model->add_calculate_currency($_POST['cost'],'','',false,false);		
					$this->lcr_model->edit_outbound($_POST);
					$this->session->set_userdata('astpp_notification', 'Outbound updated successfully!');
					redirect(base_url().'lcr/outbound/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'lcr/outbound/');				
				}
			}	
			else
			{	
			  if($outbound = $this->lcr_model->get_outbound_by_id($id))
			  {
				  $data['outbound'] = $outbound;
				  $data['trunks'] = $this->common_model->list_trunks_select($outbound['trunk']);
				  $data['sellersList'] = $this->common_model->list_sellers();	
			  }
			  else
			  {
				  echo "This trunk is not available.";
				  return;
			  }	
			  $this->load->view('view_lcr_outbound_add',$data);		
			}
		}
		elseif($action == 'delete')
		{
			if (!($outbound = $this->lcr_model->get_outbound_by_id($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Outbound not found!');
				redirect(base_url().'lcr/outbound/');
			}
			
			$this->lcr_model->remove_outbound($outbound);		
			$this->session->set_userdata('astpp_notification', 'Outbound removed successfully!');
			redirect(base_url().'lcr/outbound/');
		}		
		
	}
	
	
	function get_action_buttons_outbound($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/lcr/outbound/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/lcr/outbound/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller lcr functions outbound_grid------
	 * Listing of Outbound data through php function json_encode
	 */	
	function outbound_grid()
	{
		$json_data = array();
		$count_all = $this->lcr_model->getOutBoundCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->lcr_model->getOutBoundList($start, $perpage);
		//$this->db->where('type','3');
		//$query = $this->db->get('outbound_routes');
		if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['id'],
					$row['pattern'],
					$row['prepend'],
					$row['comment'],
					$row['trunk'],
					$row['inc'],
					$this->common_model->calculate_currency($row['connectcost']),
					$row['includedseconds'],
					$this->common_model->calculate_currency($row['cost']),
					$row['precedence'],
					$row['resellers'],
					$this->get_action_buttons_outbound($row['id'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	/**
	 * -------Here we write code for controller lcr functions import_outbound------
	 * Import Outbound data
	 */
	function import_outbound()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'LCR - Import Outbound';	
		$data['cur_menu_no'] = 6;
		
		$this->load->view('view_lcr_import_outbound',$data);
		
	}
	
}


?>