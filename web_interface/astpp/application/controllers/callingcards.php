<?php
class Callingcards extends CI_Controller
{
	function  Callingcards()
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
		$this->load->model('cc_model');
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
		$access_control = validate_access($logintype,$method, "callingcards");
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
		$data['page_title'] = 'Calling Cards';	
				
		$this->load->view('view_cc',$data);
	}
	
	/**
	 * -------Here we write code for controller callingcards functions add------
	 * Add Calling Card
	*/
	function add()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Calling Card | Add';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Add Calling Cards';	
		$data['cur_menu_no'] = 4;
		
		if(!empty($_POST))
		{
			$errors = "";
			/*if(trim($_POST['account']) == "" || strlen($_POST['account']) < 5)
			$errors .= "Invalid account number provided <br />";*/
			if(trim($_POST['brand']) == "" || $_POST['brand'] =="0")
			$errors .= "Please Select Brands <br />";
			if(trim($_POST['value']) == "" || !is_numeric($_POST['value']))
			$errors .= "Invalid value, must be a number <br />";
			if(trim($_POST['count']) == "" || !is_numeric($_POST['count']))
			$errors .= "Invalid quantity, must be a number <br />";
			
			if ($errors == "")
			{			
				$_POST['value']=$this->common_model->add_calculate_currency($_POST['value'],'','',false,false);
				$this->cc_model->add_callingcard($_POST);
				$this->session->set_userdata('astpp_notification', 'Callingcard added successfully!');
				redirect(base_url().'callingcards/cclist/');				
			}
			else 
			{
				$data['cc'] = $_POST;
				$this->session->set_userdata('astpp_errormsg', $errors);
				redirect(base_url().'callingcards/cclist/');				
			}			
		}
				
		$data['brands'] =  $this->cc_model->get_cc_brands();
		$this->load->view('view_cc_add',$data);		
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions brands------
	 * Edit or delete brands
	 * @$action: Edit, Delete
	*/
	function brands($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | CallingCards | Brands';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Calling Cards Brand';	
		$data['cur_menu_no'] = 4;
		
		$data['page_type'] = 'list';
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		if($action == "edit")
		{
			if(!empty($_POST))
			{
				$errors = "";
				if(trim($_POST['name']) == "" )
				$errors .= "Brand Name is required <br />";
				if(trim($_POST['validdays']) == "" || !is_numeric($_POST['validdays']))
				$errors .= "Invalid validaty duration. <br />";
												
				if ($errors == "")
				{	
					$_POST['maint_fee_pennies']=$this->common_model->add_calculate_currency($_POST['maint_fee_pennies'],'','',false,false);
					$_POST['disconnect_fee_pennies']=$this->common_model->add_calculate_currency($_POST['disconnect_fee_pennies'],'','',false,false);
					$_POST['minute_fee_pennies']=$this->common_model->add_calculate_currency($_POST['minute_fee_pennies'],'','',false,false);
					$_POST['min_length_pennies']=$this->common_model->add_calculate_currency($_POST['min_length_pennies'],'','',false,false);
					$this->cc_model->edit_brand($_POST);
					$this->session->set_userdata('astpp_notification', 'Brand updated successfully!');
					redirect(base_url().'callingcards/brands/');				
				}
				else 
				{
					$data['brand'] = $_POST;
					$this->session->set_userdata('astpp_errormsg', $errors);				
				}
			}
			else
			{														
				if($brand = $this->cc_model->get_brand_by_name($id))
				{				
					$data['brand'] = $brand;					
					$data['pricelist'] = $this->rates_model->get_price_list();				
					$this->load->view('view_cc_brands_add',$data);
				}
				else
				{				
					echo 'Brand not found';
				}
			}
							
		}
		elseif($action == "delete")
		{
			if (!($brand= $this->cc_model->get_brand_by_name($id)))
			{				
				$this->session->set_userdata('astpp_errormsg', 'Brand not found!');
				redirect(base_url().'callingcards/brands/');
			}
			
			$this->cc_model->remove_brand($brand);
			$this->session->set_userdata('astpp_notification', 'Brand removed successfully!');		
			redirect(base_url().'callingcards/brands/');
		}
		else
		$this->load->view('view_cc_brands',$data);				
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions brands_add------
	 * Add Brands info
	*/
	function brands_add()
	{
		$data['username'] = $this->session->userdata('user_name');	
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		if(!empty($_POST))
		{
			$errors = "";
			if(trim($_POST['brandname']) == "" )
			$errors .= "Brand Name is required <br />";
			if(trim($_POST['validdays']) == "" || !is_numeric($_POST['validdays']))
			$errors .= "Invalid validaty duration. <br />";
			
			if ($errors == "")
			{	
				$_POST['maint_fee_pennies']=$this->common_model->add_calculate_currency($_POST['maint_fee_pennies'],'','',false,false);
				$_POST['disconnect_fee_pennies']=$this->common_model->add_calculate_currency($_POST['disconnect_fee_pennies'],'','',false,false);
				$_POST['minute_fee_pennies']=$this->common_model->add_calculate_currency($_POST['minute_fee_pennies'],'','',false,false);
				$_POST['min_length_pennies']=$this->common_model->add_calculate_currency($_POST['min_length_pennies'],'','',false,false);

				
				$this->cc_model->add_brand($_POST);
				$this->session->set_userdata('astpp_notification', 'Brand added successfully!');
				redirect(base_url().'callingcards/brands/');				
			}
			else 
			{
				$data['brand'] = $_POST;
				$this->session->set_userdata('astpp_errormsg', $errors);				
			}			
		}						
		$this->load->view('view_cc_brands_add',$data);				
	}			
	
	
	function get_action_buttons_brands($id)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';
		$ret_url = '<a href="/callingcards/brands/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
		$ret_url .= '<a href="/callingcards/brands/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller callingcards functions brands_search------
	 * We post array of brands field to CI database session variable callingcards_brand_search
	 */
	function brands_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('callingcards_brand_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'callingcards/brands');
		}
	}
	
	/**
	 * -------Here we write code for controller callingcards functions clearsearchfilter------
	 * Empty CI database session variable callingcards_brand_search for normal listing
	 */
	function clearsearchfilter()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('callingcards_brand_search', "");
		redirect(base_url().'callingcards/brands');
		
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions brands_grid------
	 * Listing of Brands
	 * @account_number: Account Number
	 */
	function brands_grid()
	{
		$json_data = array();
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$brands = $this->cc_model->list_cc_brands_reseller( $this->session->userdata('username') );
		}
		else {
			$brands = $this->cc_model->list_cc_brands();
		}
		
				
		$count_all = count($brands);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;		 
		
		$query = $this->cc_model->getCCBrands($start,$perpage);
		
	
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
					$row['name'],
					$pins,
					$row['pricelist'],
					$row['validfordays'],
					$this->common_model->calculate_currency($row['maint_fee_pennies']),
					$row['maint_fee_days'],
					$this->common_model->calculate_currency($row['disconnect_fee_pennies']),
					$this->common_model->calculate_currency($row['minute_fee_pennies']),
					$row['minute_fee_minutes'],
					$row['min_length_minutes'],
					$this->common_model->calculate_currency($row['min_length_pennies']),
					$cardstat,
					$this->get_action_buttons_brands($row['name'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	
	function cdrs()
	{
		//@build_callingcard_cdrs		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Calling Cards';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Calling Cards CDRs';	
		$data['cur_menu_no'] = 4;
		
		$this->load->model('cc_model');
		$data['brands'] =  $this->cc_model->get_cc_brands();
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		$this->load->view('view_cc_cdrs',$data);		
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_search------
	 * We post array of brands cdrs field to CI database session variable brands_cdrs_search
	 */
	function brands_cdrs_search()
	{		
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('brands_cdrs_search', $_POST);		
		}				
		if(@$ajax_search!=1) {		
			redirect(base_url().'callingcards/cdrs/');
		}
	}
	
	
	
	/**
	 * -------Here we write code for controller callingcards functions clearsearchfilter_brands_cdrs------
	 * Empty CI database session variable brands_cdrs_search for normal listing
	 */
	function clearsearchfilter_brands_cdrs()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('brands_cdrs_search', "");
		redirect(base_url().'callingcards/cdrs/');		
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_grid------
	 * Listing of Brands Cdrs
	*/
	function brands_cdrs_grid()
	{
		$json_data = array();		
		
		$count_all = $this->cc_model->getcdrsCount();
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
/*		
		$did_list = $this->Astpp_model->list_cc_brands();
		if(count($did_list)>0)
		{
			$json_data['page'] = 1;	
			$json_data['total'] = count($did_list);
		}
*/		
		//$this->db->where('status <', 2);
		$query = $this->cc_model->getcdrs($start, $perpage);
		
		//$query = $this->db->get('callingcardcdrs');
		if($query->num_rows() > 0)
		{
			
			
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['clid'],
					$row['destination'],
					$row['cardnumber'],
					$row['seconds'],
					$row['disposition'],
					$this->common_model->calculate_currency($row['debit']),
					$row['notes'],
					$row['pricelist'],
					$row['pattern']
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	function cclist()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Calling Cards';	
		$data['cur_menu_no'] = 4;
		
		$this->load->model('cc_model');
		$data['brands'] =  $this->cc_model->get_cc_brands();
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		$this->load->view('view_cc_list',$data);		
	}
	
	/**
	 * -------Here we write code for controller callingcards functions refill------
	 * Refill Card no 
	 * @$card_number: Card Number
	 */
	function refill($card_number=false)
	{
		if(!empty($_POST))
		{
			$errors = "";
			if(trim($_POST['penies']) == "" || !is_numeric($_POST['penies']))
			$errors .= "Invalid value.<br />";
			
			if($errors != "")
			{
				$_POST['pennies']=$this->common_model->add_calculate_currency($_POST['pennies'],'','',false,false);
				$this->cc_model->refill_card($_POST);
				$this->session->set_userdata('astpp_notification', 'Card refilled successfully!');
				redirect(base_url().'callingcards/cclist/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', $errors);	
				redirect(base_url().'callingcards/cclist/');			
			}
		}	
		else
		{	
		  if($cc = $this->cc_model->get_card_by_number($card_number))
		  {
			  $data['cc'] = $cc;
		  }
		  else
		  {
			  echo "This card is not available.";
			  return;
		  }
		  $this->load->view('view_cc_refill',$data);		
		}
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions remove------
	 * this function check if card number exist or not then remove from database.
	 * @card_number: Card Number
	 */
	function remove($card_number="")
	{
		if($cc = $this->cc_model->get_card_by_number($card_number))
		{
			$this->cc_model->remove_card($cc);
			$this->session->set_userdata('astpp_notification', 'Card deleted successfully!');
			redirect(base_url().'callingcards/cclist/');				
		}
		else 
		{
			$this->session->set_userdata('astpp_errormsg', "Invalid card number.");	
			redirect(base_url().'callingcards/cclist/');			
		}			

	}
	
	/**
	 * -------Here we write code for controller callingcards functions reset_card------
	 * Card reset against card number if exist
	 * @card_number: Card Number
	 */
	function reset_card($card_number = "")
	{
		if($cc = $this->cc_model->get_card_by_number($card_number))
		{
			$this->cc_model->reset_card($cc);
			$this->session->set_userdata('astpp_notification', 'Card reset successfully!');
			redirect(base_url().'callingcards/cclist/');				
		}
		else 
		{
			$this->session->set_userdata('astpp_errormsg', "Invalid card number.");	
			redirect(base_url().'callingcards/cclist/');			
		}		
	}
	
	/**
	 * -------Here we write code for controller callingcards functions update_status------
	 * update status of card
	 */
	function update_status()
	{
		if(!empty($_POST))
		{
			$errors = "";
			if(trim($_POST['starting']) == "" || !is_numeric($_POST['starting']))
			$errors .= "Invalid starting sequence.<br />";
			if(trim($_POST['ending']) == "" || !is_numeric($_POST['ending']))
			$errors .= "Invalid ending sequence.<br />";			
			
			if($errors == "")
			{
				$this->cc_model->update_status_card($_POST);
				$this->session->set_userdata('astpp_notification', 'Card status updated successfully!');
				redirect(base_url().'callingcards/cclist/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', $errors);	
				redirect(base_url().'callingcards/cclist/');			
			}
		}	
		else
		{	
		  $this->load->view('view_cc_update_status');		
		}	
	}
	
	/**
	 * -------Here we write code for controller callingcards functions view------
	 * View calling cards info
	 * @card_number: Card Number
	 */
	function view($card_number = "")
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | CallingCards | View';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'View - Calling Cards';	
		$data['cur_menu_no'] = 4;
		
		if($cc = $this->cc_model->get_card_by_number($card_number))
		{
			$data['cc'] = $cc;
			$data['cdrs'] = $this->cc_model->get_callingcard_cdrs($card_number);
		}
		else
		{
			echo "This card is not available."	;
			return;
		}
		$this->load->view('view_cc_add',$data);
			
	}
	
	function get_action_buttons($page,$id,$action)
	{
		$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$url = '';
		$ret_url = '';
		if($page=='did')
		{
			$url = '/did/manage/'.$id.'/';			
		}
		if($action == 'update')
		{
			$ret_url = '<a href="'.$url.'" class="icon" '.$update_style.' title="Update">&nbsp;</a>';
		}
		if($action == 'delete')
		{
			$ret_url = '<a href="'.$url.'1/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		}
		if($action == 'manage')
		{
			$ret_url = '<a href="/did/manage/edit/'.$id.'/" class="icon" '.$update_style.' rel="facebox" title="Update">&nbsp;</a>';
			$ret_url .= '<a href="/did/manage/del/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		}
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller callingcards functions cards_search------
	 * We post an array of cards field to CI database session variable cards_search
	 */
	function cards_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('cards_search', $_POST);		
		}
		
		if(@$ajax_search!=1) {		
			redirect(base_url().'callingcards/cclist/');
		}
	}
	
	/**
	 * -------Here we write code for controller callingcards functions clearsearchfilter_cards------
	 * Empty CI database session variable cards_search for normal listing
	 */
	function clearsearchfilter_cards()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('cards_search', "");
		redirect(base_url().'callingcards/cclist/');		
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions manage_json------
	 * Listing of Calling Cards
	*/
	function manage_json()
	{
		$json_data = array();
		$json_data['page'] = 0;	
		$json_data['total'] = 0;	
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$brands = $this->cc_model->list_cc_brands_reseller( $this->session->userdata('username') );
		}
		else {
			$brands = $this->cc_model->get_cc_brands();
		}
				
		$brandsql = " IN (";		
		$list = implode("','",$brands);
		$list = "'".$list."'";
		$brandsql .= $list;		
		$brandsql .= ")";
		
		//$sql =$this->db->query("SELECT * FROM callingcards WHERE status < 2 AND brand $brandsql");
		//$count_all =  $sql->num_rows();
		$count_all = $this->cc_model->getCCCount($brands);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];
  
		$page_no = $_GET['page']; 
		
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
  
		$query = $this->cc_model->getCCList($brands, $start,$perpage);
	
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
				$value = $row['value'];
				
				$used  = $row['used'];
  
				if($row['inuse'] == 0)
				{
					$inuse = 'no';
				}
				else 
				{
					$inuse = '<a href="/callingcards/reset/'.$row['cardnumber'].'" title="Reset" onClick="return get_alert_msg(\'reset\');">yes</a>';
				}
					$json_data['rows'][] = array('cell'=>array(
						$row['account'],
						$row['id'],
						'<a href="/callingcards/view/'.$row['cardnumber'].'/" rel="facebox" >'.$row['cardnumber'].'</a>',
						$row['pin'],
						$row['brand'],
						$this->common_model->calculate_currency($value),
						$this->common_model->calculate_currency($used),
						$row['validfordays'],
						$row['created'],
						$row['firstused'],
						$row['expiry'],
						$inuse,
						$cardstat,
						$this->get_callingcards_action_buttons($row['cardnumber'],$row['inuse'])
					));
			}
		}
		echo json_encode($json_data);
		
	}
	
	function get_callingcards_action_buttons($id,$inuse)
	{
		$refill_style = 'style="text-decoration:none;background-image:url(/images/payment.png);"';
		$details_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
		$status_style = 'style="text-decoration:none;background-image:url(/images/status.png);"';
		$add_caller_id = 'style="text-decoration:none;background-image:url(/images/callerid.png);"';
		$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';		
		$reset_style = 'style="text-decoration:none;background-image:url(/images/reset_icon.png);"';
		$url = '';
		
		$ret_url = '<a href="/callingcards/refill/'.$id.'/" class="icon" '.$refill_style.' rel="facebox" title="Refill">&nbsp;</a>';
		$ret_url .= '<a href="/callingcards/view/'.$id.'/" class="icon" '.$details_style.' rel="facebox" title="View Details">&nbsp;</a>';
		//$ret_url .= '<a href="/callingcards/update_status/'.$id.'/" class="icon" '.$status_style.' rel="facebox" title="Update Status">&nbsp;</a>';
		$ret_url .= '<a href="'.base_url().'callingcards/add_callerid/'.$id.'/" class="icon" '.$add_caller_id.' title="Add CallerID" rel="facebox" >&nbsp;</a>';
		$ret_url .= '<a href="/callingcards/remove/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg(\'delete\');">&nbsp;</a>';
		if($inuse == 1)
		$ret_url .= '<a href="/callingcards/reset_card/'.$id.'/" class="icon" '.$reset_style.' title="Reset" onClick="return get_alert_msg(\'reset\');">&nbsp;</a>';
		
		return $ret_url;
	}
	
	
	/**
	 * -------Here we write code for controller callingcards functions add_callerid------
	 * Add CC Caller ID
	 * @card_number : Card Number
	*/
	function add_callerid($card_number="")
    {
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | CallingCards | Add CC Caller ID';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Caller ID';	
		$data['cur_menu_no'] = 4;  
                
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
                	$this->redirect_notification('CallerID Added successfully...','/callingcards/cclist/');									
                }
              
                $this->load->view('view_cc_add_callerid',$data);
        }
        
    
	function redirect_notification($notificationmsg,$redirect)
	{
		$this->session->set_userdata('astpp_notification', $notificationmsg);
		redirect(base_url().$redirect);		
	}
	
	
	function export_cc_cdr_xls()
	{
	    $query = $this->cc_model->getcdrs('','',false);
	    $cc_array = array();    	    
	    $cc_array[] = array("Date","CallerID","Called Number","Card Number","Bill Seconds","Disposition","Debit","Destination","Pricelist","Code");
	   
	    if($query->num_rows() > 0)
	    {		
		foreach ($query->result_array() as $row)
		{
		      $cc_array[] = array(
			  $row['callstart'],
			  $row['clid'],
			  $row['destination'],
			  $row['cardnumber'],
			  $row['seconds'],
			  $row['disposition'],
			  $this->common_model->calculate_currency($row['debit']),
			  $row['notes'],
			  $row['pricelist'],
			  $row['pattern']
		      );
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($cc_array,'CallingCard_CDR_'.date("Y-m-d").'.xls');
	}
	
	function export_cc_cdr_pdf()
	{
	    $query = $this->cc_model->getcdrs('','',false);
	    $cc_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    	    
	    $this->fpdf->tablewidths = array(25, 25, 21, 18, 10, 30,16, 20, 14, 13);
	    $cc_array[] = array("Date","CallerID","Called Number","Card Number","Bill Seconds","Disposition","Debit","Destination","Pricelist","Code");
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      $cc_array[] = array(
			  $row['callstart'],
			  $row['clid'],
			  $row['destination'],
			  $row['cardnumber'],
			  $row['seconds'],
			  $row['disposition'],
			  $this->common_model->calculate_currency($row['debit']),
			  $row['notes'],
			  $row['pricelist'],
			  $row['pattern']
		      );
		}
	    }
	    
	    $this->fpdf->AliasNbPages();
	    $this->fpdf->AddPage();    
	    
	    $this->fpdf->SetFont('Arial', '', 15);
	    $this->fpdf->SetXY(60, 5);
	    $this->fpdf->Cell(100, 10, "CallingCard CDR Report ".date('Y-m-d'));
	    	    
	    $this->fpdf->SetY(20);
	    $this->fpdf->SetFont('Arial', '', 7);
	    $this->fpdf->SetFillColor(255, 255, 255);
	    $this->fpdf->lMargin=2;
	    
	    $dimensions = $this->fpdf->export_pdf($cc_array, "5");
	    $this->fpdf->Output('CallingCard_CDR_'.date("Y-m-d").'.pdf',"D");
	}
	
}
?>