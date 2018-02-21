<?php

class Rates extends CI_Controller
{
	function  Rates()
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
		
		$this->load->model('rates_model');
		
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
		$access_control = validate_access($logintype,$method, "rates");
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
		$data['page_title'] = 'Rates';	
				
		$this->load->view('view_rates',$data);
	}
	
	/**
	 * -------Here we write code for controller rates functions calccharge------
	 * Calculate Charges
	 */
	function calccharge()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Rates | Calculate Charge';
		$data['page_title'] = 'Calculate Charges';	
		$data['cur_menu_no'] = 7;
		if(!empty($_POST))
		{
			$errors = "";
			if(trim($_POST['length']) == "")
			$errors .= "Call length is required.<br />";
			if(trim($_POST['phonenumber']) == "")
			$errors .= "Phone number is required.<br />";	
			
			if($errors == "")
			{
				$cost = $this->rates_model->calculate_charges($_POST);
				$result = "Call to: ".$_POST['phonenumber']." will cost: ".$this->common_model->calculate_currency($cost)." for a call lasting ".$_POST['length']." minutes. ";
				$this->session->set_userdata('astpp_notification',$result);
			}
			else
			{
				$this->session->set_userdata('astpp_errormsg', $errors);
			}		
			
		}
		
		$data['pricelists'] = $this->rates_model->list_pricelists_select();				
		$this->load->view('view_rates_calccharge',$data);
		
	}
	
	
	/**
	 * -------Here we write code for controller rates functions counters------
	 * Counters
	 */
	function counters()
	{
		if($this->session->userdata('logintype') != 2 && $this->session->userdata('logintype') != 1){
			$this->session->set_userdata('astpp_errormsg', "Rates Counter is not accessible."); 
			redirect(base_url().'astpp/dashboard');
		}
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Rates | Counters';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Counters';	
		$data['cur_menu_no'] = 7;
		
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
		
		$this->load->view('view_rates_counters',$data);
		
	}
	
	/**
	 * -------Here we write code for controller rates functions counters_search------
	 * We post an array of counters field to CI database session variable counters_search
	 */
	function counters_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('counters_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'rates/counters/');
		}
	}
	
	/**
	 * -------Here we write code for controller rates functions clearsearchfilter_counters------
	 * Empty CI database session variable counters_search for normal listing
	 */
	function clearsearchfilter_counters()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('counters_search', "");
		redirect(base_url().'rates/counters/');
		
	}
	
	/**
	 * -------Here we write code for controller rates functions counters_grid------
	 * Listing of Counters data through php function json_encode
	 */
	function counters_grid()
	{
		$json_data = array();
		
		$count_all = $this->rates_model->getCountersCount();		
	
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;			
			
		$query = $this->rates_model->getCountersList($start, $perpage);	 
				
			
		if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['id'],
					$row['name'],
					$row['account'],
					$row['seconds']
				));
			}
		}
		
		
		echo json_encode($json_data);			
	}
	
	/**
	 * -------Here we write code for controller rates functions importroutes------
	 * Import routes
	 */
	function importroutes($action=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Import Origination Rates';	
		$data['cur_menu_no'] = 7;
		
		if($action == "done")
		{
			$this->session->set_userdata('astpp_notification', 'Origination Rates Imported Successfully');	
			redirect(base_url()."rates/routes");	
		}
		
		$this->load->view('view_rates_importroutes',$data);
		
	}
	
	
	/**
	 * -------Here we write code for controller rates functions packages------
	 * @action: Add, Edit, Delete and list packages
	 * @id: Package Id
	 */
	function packages($action=false,$id=false)
	{
		
		if($this->session->userdata('logintype') != '2' && $this->session->userdata('logintype') != '1'){
			$this->session->set_userdata('astpp_errormsg', "Rates Packages is not accessible."); 
			redirect(base_url().'astpp/dashboard');
		}
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Rates | Packages';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'packages';	
		$data['cur_menu_no'] = 7;
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		if($action == false)
		$action = "list";
				
		
		if($action == 'list')
		{
			$this->load->view('view_rates_packages',$data);
		}
		elseif($action == 'add')
		{			
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Name is required<br />";
				if(trim($_POST['pattern']) == "")
				$errors .= "Pattern is required<br />";
								
				if ($errors == "")
				{				
					$this->rates_model->add_package($_POST);
					$this->session->set_userdata('astpp_notification', 'Packages added successfully!');
					redirect(base_url().'rates/packages/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/packages/');				
				}			
			}
			$data['pricelists'] = $this->rates_model->list_pricelists_select();
			$this->load->view('view_rates_packages_add',$data);			
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Name is required<br />";
				if(trim($_POST['pattern']) == "")
				$errors .= "Pattern is required<br />";
							
				if ($errors == "")
				{				
					$this->rates_model->edit_package($_POST);
					$this->session->set_userdata('astpp_notification', 'Packages updated successfully!');
					redirect(base_url().'rates/packages/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/packages/');				
				}
			}	
			else
			{	
			  if($package = $this->rates_model->get_package_by_id($id))
			  {
				  $data['package'] = $package;	
			  }
			  else
			  {
				  echo "This Packages is not available.";
				  return;
			  }	
			  $data['pricelists'] = $this->rates_model->list_pricelists_select($package['pricelist']);
			  $this->load->view('view_rates_packages_add',$data);	
			}
		}
		elseif($action == 'delete')
		{
			if (!($package = $this->rates_model->get_package_by_id($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Packages not found!');
				redirect(base_url().'rates/packages/');
			}
			
			$this->rates_model->remove_package($package);		
			$this->session->set_userdata('astpp_notification', 'Packages removed successfully!');
			redirect(base_url().'rates/packages/');
		}		
		
	}
	
	/**
	 * -------Here we write code for controller rates functions packages_search------
	 * We post an array of packages field to CI database session variable packages_search
	 */
	function packages_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('packages_search', $_POST);		
		}
		
		if(@$ajax_search!=1) {		
			redirect(base_url().'rates/packages/');
		}
	}
	
	
	/**
	 * -------Here we write code for controller rates functions clearsearchfilter_packages------
	 * Empty CI database session variable packages_search for normal listing
	 */
	function clearsearchfilter_packages()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('packages_search', "");
		redirect(base_url().'rates/packages/');
		
	}
	
	
	/**
	 * -------Here we write code for controller rates functions packages_grid------
	 * Listing of Packages data through php function json_encode
	 */
	function packages_grid()
	{
		$json_data = array();
		$count_all = $this->rates_model->getPackagesCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->rates_model->getPackagesList($start, $perpage);
		
		if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
							$row['name'],
							$row['pricelist'],
							$row['pattern'],
							$row['includedseconds'],
							$this->get_action_buttons_packages($row['id'])
						));
			}
		}
		echo json_encode($json_data);			
	}
	
	function get_action_buttons_packages($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/rates/packages/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/rates/packages/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}		
	
	
	function get_action_buttons_periodiccharges($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/rates/periodiccharges/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/rates/periodiccharges/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	
	/**
	 * -------Here we write code for controller rates functions periodiccharges_search------
	 * We post an array of periodic charges field to CI database session variable periodiccharges_search
	 */
	function periodiccharges_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('periodiccharges_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'rates/periodiccharges/');
		}
	}
	
	/**
	 * -------Here we write code for controller rates functions clearsearchfilter_periodiccharges------
	 * Empty CI database session variable periodiccharges_search for normal listing
	 */
	function clearsearchfilter_periodiccharges()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('periodiccharges_search', "");
		redirect(base_url().'rates/periodiccharges/');
		
	}
	
	/**
	 * -------Here we write code for controller rates functions periodiccharges_grid------
	 * Listing of Periodic Charges data through php function json_encode
	 */			
	function periodiccharges_grid()
	{
		$json_data = array();		
		
		$count_all = $this->rates_model->getPeriodicChargesCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->rates_model->getPeriodicChargesList($start, $perpage);

		//$this->db->where('type','3');
		//$query = $this->db->get('charges');
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
	            if ( $row['status'] == 0 ) {
	                $chargestat = 'inactive';
	            }
	            elseif ( $row['status'] == 1 ) {
	                $chargestat = 'active';
	            }
	            elseif ( $row['status'] == 2 ) {
	                $chargestat = 'deleted';
	            }
				
	            if ( $row['sweep'] == 0 ) {
	                $row['sweep']  = "daily";
	            }
	            elseif ( $row['sweep'] == 1 ) {
	                $row['sweep'] ="weekly";
	            }
	            elseif ( $row['sweep'] == 2 ) {
	                $row['sweep'] ="monthly";
	            }
	            elseif ( $row['sweep'] == 3 ) {
	                $row['sweep'] ="quarterly";
	            }
	            elseif ( $row['sweep'] == 4 ) {
	                $row['sweep'] ="semi-annually";
	            }
	            elseif ( $row['sweep'] == 5 ) {
	                $row['sweep'] ="annually";
	            }
				
				$json_data['rows'][] = array('cell'=>array(
					$row['id'],
					$row['description'],
					$row['pricelist'],
					$this->common_model->calculate_currency($row['charge']),
					$row['sweep'],
					$chargestat,
					$this->get_action_buttons_periodiccharges($row['id'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller rates functions periodiccharges------
	 * @action: Add, Edit, Delete and list of periodic charges
	 * @id: Periodic charges Id
	 */
	function periodiccharges($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'periodic charges';	
		$data['cur_menu_no'] = 7;	
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		$this->load->model('common_model');
		$data['sweeplist'] = $this->common_model->get_sweep_list();
		
		if($action == false)
		$action = "list";
				
		
		if($action == 'list')
		{
			$this->load->view('view_rates_periodiccharges',$data);
		}
		elseif($action == 'add')
		{			
			if(!empty($_POST))
			{
				$errors = "";				
				if ($errors == "")
				{		
					$_POST['charge']=$this->common_model->add_calculate_currency($_POST['charge'],'','',false,false);
					$this->rates_model->add_charge($_POST);
					$this->session->set_userdata('astpp_notification', 'Periodic Charge added successfully!');
					redirect(base_url().'rates/periodiccharges/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/periodiccharges/');				
				}			
			}
			$data['pricelists'] = $this->rates_model->list_pricelists_select();
			$data['sweeplist'] = $this->common_model->get_sweep_list();
			$this->load->view('view_rates_periodiccharges_add',$data);			
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";			
				if ($errors == "")
				{		
					$_POST['charge']=$this->common_model->add_calculate_currency($_POST['charge'],'','',false,false);
					$this->rates_model->edit_charge($_POST);
					$this->session->set_userdata('astpp_notification', 'Periodic Charge updated successfully!');
					redirect(base_url().'rates/periodiccharges/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/periodiccharges/');				
				}
			}	
			else
			{	
			  if($periodicCharge = $this->rates_model->get_charge_by_id($id))
			  {
				  $data['periodicCharge'] = $periodicCharge;	
			  }
			  else
			  {
				  echo "This route is not available.";
				  return;
			  }	
			  $data['pricelists'] = $this->rates_model->list_pricelists_select($periodicCharge['pricelist']);
			  $data['sweeplist'] = $this->common_model->get_sweep_list();
			  $this->load->view('view_rates_periodiccharges_add',$data);	
			}
		}
		elseif($action == 'delete')
		{
			if (!($charge = $this->rates_model->get_charge_by_id($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Periodic Charge not found!');
				redirect(base_url().'rates/periodiccharges/');
			}
			
			$this->rates_model->remove_charge($charge);		
			$this->session->set_userdata('astpp_notification', 'Periodic Charge removed successfully!');
			redirect(base_url().'rates/periodiccharges/');
		}
	}
	
	/**
	 * -------Here we write code for controller rates functions pricelists------
	 * @action: Add, Edit, Delete and list of Pricelists
	 * @id: Periodic Pricelist Id
	 */
	function pricelists($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Price List';	
		$data['cur_menu_no'] = 7;
		
		if($action === false)
		$action = "list";
				
			
		if($action == 'list')
		{
			$this->load->view('view_rates_pricelists',$data);
		}
		elseif($action == 'add')
		{			
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Name is required<br />";
				if(trim($_POST['inc']) == "")
				$errors .= "Increment is required<br />";
				if(trim($_POST['markup']) == "")
				$errors .= "Markup is required<br />";
				
				if ($errors == "")
				{				
					$this->rates_model->add_pricelist($_POST);
					$this->session->set_userdata('astpp_notification', 'Pricelist added successfully!');
					redirect(base_url().'rates/pricelists/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/pricelists/');				
				}			
			}
			$this->load->view('view_rates_pricelists_add',$data);			
		}		
		elseif($action == 'edit')
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "")
				$errors .= "Name is required<br />";
				if(trim($_POST['inc']) == "")
				$errors .= "Increment is required<br />";
				if(trim($_POST['markup']) == "")
				$errors .= "Markup is required<br />";
				
				if ($errors == "")
				{				
					$this->rates_model->edit_pricelist($_POST);
					$this->session->set_userdata('astpp_notification', 'Pricelist updated successfully!');
					redirect(base_url().'rates/pricelists/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/pricelists/');				
				}
			}	
			else
			{	
			  if($pricelist = $this->rates_model->get_pricelist_by_name(urldecode($id)))
			  {
				  $data['pricelist'] = $pricelist;	
			  }
			  else
			  {
				  echo "This pricelist is not available.";
				  return;
			  }	
			  $this->load->view('view_rates_pricelists_add',$data);	
			}
		}
		elseif($action == 'delete')
		{
			$pricelist = $this->rates_model->get_pricelist_by_name(urldecode($id));
			if (count($pricelist)==0)
			{				
				$this->session->set_userdata('astpp_errormsg', 'Pricelist not found!');
				redirect(base_url().'rates/pricelists/');
			}
			
			$this->rates_model->remove_pricelist($pricelist);		
			$this->session->set_userdata('astpp_notification', 'Pricelist removed successfully!');
			redirect(base_url().'rates/pricelists/');
		}
			
	}
	
	
	function get_action_buttons_routes($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/rates/routes/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/rates/routes/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}	
	
	/**
	 * -------Here we write code for controller rates functions routes_search------
	 * We post an array of routes field to CI database session variable routes_search
	 */
	function routes_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);	
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('routes_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'rates/routes/');
		}
	}
	
	/**
	 * -------Here we write code for controller rates functions clearsearchfilter_routes------
	 * Empty CI database session variable routes_search for normal listing
	 */
	function clearsearchfilter_routes()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('routes_search', "");
		redirect(base_url().'rates/routes/');		
	}
	
	/**
	 * -------Here we write code for controller rates functions routes_grid------
	 * Listing of routes data through php function json_encode
	 */	
	function routes_grid()
	{
		$json_data = array();
	
		$count_all = $this->rates_model->getRoutesCount();	
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
	
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		$query = $this->rates_model->getRoutesList($start, $perpage);
         
		//$this->db->where('type','3');
		//$query = $this->db->get('routes');
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['id'],
					$row['pattern'],
					$row['comment'],
					$row['pricelist'],
					$this->common_model->calculate_currency($row['connectcost']),
					$row['includedseconds'],
					$this->common_model->calculate_currency($row['cost']),
					$row['inc'],
					$this->get_action_buttons_routes($row['id'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	/**
	 * -------Here we write code for controller rates functions routes------
	 * @action: Add, Edit, Delete and list of routes
	 * @id: Routes Id
	 */
	function routes($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Rates | Origination Rates';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Origination Rates';	
		$data['cur_menu_no'] = 7;
		
		if($action == false)
		$action = "list";
				
		
		if($action == 'list')
		{
			$this->load->view('view_rates_routes',$data);
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
					
					$this->rates_model->add_route($_POST);
					$this->session->set_userdata('astpp_notification', 'Route added successfully!');
					redirect(base_url().'rates/routes/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/routes/');				
				}			
			}
			$data['pricelists'] = $this->rates_model->list_pricelists_select();
			$this->load->view('view_rates_routes_add',$data);			
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
					
					$this->rates_model->edit_route($_POST);
					$this->session->set_userdata('astpp_notification', 'Route updated successfully!');
					redirect(base_url().'rates/routes/');				
				}
				else 
				{
					$this->session->set_userdata('astpp_errormsg', $errors);
					redirect(base_url().'rates/routes/');				
				}
			}	
			else
			{	
			  if($route = $this->rates_model->get_route_by_id($id))
			  {
				  $data['route'] = $route;	
			  }
			  else
			  {
				  echo "This route is not available.";
				  return;
			  }	
			  $data['pricelists'] = $this->rates_model->list_pricelists_select($route['pricelist']);
			  $this->load->view('view_rates_routes_add',$data);	
			}
		}
		elseif($action == 'delete')
		{
			if (!($pricelist = $this->rates_model->get_route_by_id(urldecode($id))))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Route not found!');
				redirect(base_url().'rates/routes/');
			}
			
			$this->rates_model->remove_route($pricelist);		
			$this->session->set_userdata('astpp_notification', 'Route removed successfully!');
			redirect(base_url().'rates/routes/');
		}		
	}
	
	function get_action_buttons_pricelists($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/rates/pricelists/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/rates/pricelists/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}	
	
	/**
	 * -------Here we write code for controller rates functions pricelist_search------
	 * We post an array of pricelist field to CI database session variable pricelist_search
	 */
	function pricelist_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);	
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('pricelist_search', $_POST);		
		}		
		if(@$ajax_search!=1) {
			redirect(base_url().'rates/pricelists/');
		}
	}
	
	/**
	 * -------Here we write code for controller rates functions clearsearchfilter------
	 * Empty CI database session variable pricelist_search for normal listing
	 */
	function clearsearchfilter()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('pricelist_search', "");
		redirect(base_url().'rates/pricelists/');
		
	}
	
	/**
	 * -------Here we write code for controller rates functions pricelists_grid------
	 * Listing of pricelist data through php function json_encode
	 */	
	function pricelists_grid()
	{
		$json_data = array();
		
		$count_all = $this->rates_model->getPriceCount();
			
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		 
		 $query = $this->rates_model->getPriceList($start, $perpage);
				
		//$this->db->where('type','3');
		//$query = $this->db->get('pricelists');
		if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['name'],
					$row['inc'],
					$row['markup'],
					$this->rates_model->getRouteByPricelistsCount($row['name']),
					$this->get_action_buttons_pricelists($row['name'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
}
?>