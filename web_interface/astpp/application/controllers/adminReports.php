<?php 

class AdminReports extends CI_Controller
{
	function  AdminReports()
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
		$this->load->model('callshop_model');
		$this->load->model('accounts_model');
		
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
		$access_control = validate_access($logintype,$method, "adminreports");
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
		$this->resellerReport();
	}
	
	/**
	 * -------Here we write code for controller adminReports functions user_search------
	 * We post array of user field to CI database session variable user_search
	 */
	function user_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
			
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('user_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'adminReports/userReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_user()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('user_search', "");
		redirect(base_url().'adminReports/userReport/');
		
	}
	/**
	 * -------Here we write code for controller adminReports functions user_list------
	 * Listing of User for selection of account number in quick search
	 */
	function user_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List User';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_user_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_user_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'0');	
			
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
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'0');
			
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
	 * -------Here we write code for controller adminReports functions userReport------
	 * User report with call record info from start date to end date with IDD code and destination
	*/
	function userReport($grid=NULL, $start_date=NULL, $end_date=NULL, $reseller=NULL, $destination=NULL, $pattern=NULL , $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL)
	{
		
		$name = "User";
		$type = "0";
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | User Report';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'User Report';	
		
		$data['cur_menu_no'] = 2;
		
		//For Reseller
		
		//$Reseller_post = $this->input->post('Reseller',0);
		if($this->session->userdata('advance_search')==1){
			
			$user_search =  $this->session->userdata('user_search');
			
			if(!empty($user_search['reseller'])) {
				$reseller  = $user_search['reseller'];
			}
			
			if(!empty($user_search['Destination'])) {
				$destination  = $user_search['Destination'];
			}
			
			
			if(!empty($user_search['Pattern'])) {
				$pattern  = $reseller_search['Pattern'];
			}
			
			if(!empty($user_search['start_date'])) {
				$start_date_before  = $user_search['start_date'];
				
				$start_date_before = explode(" ", $start_date_before);
				$start_date = @$start_date_before[0];
				$time = explode(":", @$start_date_before[1]);
				
				$start_hour = $time[0];
				$start_minute = $time[1];
				$start_second = "00";
			}
			
			if(!empty($user_search['end_date'])) {
				$end_date_before  = $user_search['end_date'];
				
				$end_date_before = explode(" ", $end_date_before);
				$end_date = @$end_date_before[0];
				$time = explode(":", @$end_date_before[1]);
				
				$end_hour = $time[0];
				$end_minute = $time[1];
				$end_second = "59";
			}		
			
		}	
		 
		if($reseller == NULL)
		{
			 $reseller = "ALL";
		}
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			 $sth_reseller = $this->accounts_model->getReseller("".$this->session->userdata('username')."", $type);
			}
		else {
			$sth_reseller = $this->accounts_model->getReseller("", $type);			
		}
		
		$data['Reseller'] = $reseller;
		$data['reseller'] = $sth_reseller;
		
		//For Destination
		//$Destination_post = $this->input->post('destination',0);
		if($destination == NULL)
		{
			 $destination = "ALL";
		}
		if($pattern == NULL)
		{
			 $pattern = "ALL";
		}
				
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$sth_destination = $this->callshop_model->getDestination("".$this->session->userdata('username')."");
		}
		else{
			$sth_destination = $this->callshop_model->getDestination();
		}
		
		
		$destination_list = $sth_destination[1];
		$pattern_list = $sth_destination[2];
		
		$data['Destination'] = $destination;
		$data['destination'] = $destination_list;
		
		$data['Pattern'] = urldecode($pattern);
		$data['pattern'] = $pattern_list;
		
		$sd =  $start_date;		   
		$ed =  $end_date;
		
		$data['start_date'] = $sd;
		$data['end_date'] = $ed;
		
		$data['start_hour'] = $start_hour;
		$data['start_minute'] = $start_minute;
		$data['start_second'] = $start_second;
		
		$data['end_hour'] = $end_hour;
		$data['end_minute'] = $end_minute;
		$data['end_second'] = $end_second;
		   
	   if($sd==NULL || $ed==NULL || $sd=='NULL' || $ed=='NULL'){
			$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
			$ed = date('Y-m-d 23:59:59');
			
			$data['start_date'] = date("Y-m-d", strtotime(date('m').'/01/'.date('Y')));
			$data['end_date'] = date('Y-m-d');
			
			$data['start_hour'] = '00';
			$data['start_minute'] = '00';
			$data['start_second'] = '00';
			
			$data['end_hour'] = '23';
			$data['end_minute'] = '59';
			$data['end_second'] = '59';
		}
		else{
			
			$sd =  $start_date." ".$start_hour.":".$start_minute.":".$start_second;		   
			$ed =  $end_date." ".$end_hour.":".$end_minute.":".$end_minute;
		}
		
		if (!empty($_POST))// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			// put your processing code here... we show what we do for emailing. You will need to add a correct email address
			if ($this->_process_create($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('.');
			}
		}

		if ($this->uri->segment(3) === FALSE)
		{				
			$this->load->view('view_adminReports_userReport',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_adminReports_userReport',$data);
		}
		else 
		{			
		//Filter		 
		  /* $sd =  $start_date;		   
		   $ed =  $end_date;
		   
		   if($sd==NULL || $ed==NULL || $sd=='NULL' || $ed=='NULL'){
				$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
				$ed = date('Y-m-d 23:59:59');
			}*/
			
		   $where = ""; 
		   
			if($sd!='NULL' && $ed!='NULL' && $sd!="" && $ed!="") {	
				$where = " AND callstart BETWEEN '". $sd . "' AND '". $ed . "' ";
			}
			 
			 
			if ( $reseller == 'ALL' ) {
			   if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
					$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE reseller = '". $reseller. "' AND type IN (".$type.")) ";
				}
				else {
			//            if ( strpos( $this->session->userdata('logintype'), "1" ) != -1 ) {
						$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE type IN (".$type.")) ";
			//           }
			//            elsif ( strpos( $this->session->userdata('logintype'), "3" ) != -1 ) {
			//				if ($Reseller_post != 'ALL') {
			//	                $where .= "AND cardnum = " . $Reseller_post . " ";
			//		}
			//           }
				}
			}
			else {
			if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
				$where .="AND cardnum = '" . $reseller . "' ";
			}
			else {
					if ( strpos(  $type, "1" ) != -1 ) {
						$where .= "AND cardnum IN (SELECT `number` FROM accounts WHERE `number` = '". $reseller. "' AND type IN (".$type.")) ";
					}
					elseif ( strpos(  $type, "3" ) != -1 ) {
					if ($reseller != 'ALL') {
							$where .= "AND cardnum = '" . $reseller . "' ";
					}
				}
			}
		}
		
		if ( $destination == 'ALL' ) {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= "";
			}
			else {
				$where .= "AND notes LIKE '" . "%|" . urldecode($pattern)  . "' ";
			}
		}
		else {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= ""; 
			}
			else {
				$where .= "AND (notes LIKE '". "%|" . $destination . "|%" . "' "
				  . "OR notes LIKE '" .  "%|" . urldecode($pattern) . "' ";
			}
		}
		
		$table = "tmp_" . time();
		//$drop_view = @mysql_query("DROP VIEW $table");
		//$query ="CREATE TEMPORARY TABLE $table SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		$query = "CREATE TEMPORARY TABLE  $table AS SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		//CREATE VIEW prodsupp AS
	
		$rs_create = $this->db->query($query);
		
		if($rs_create) {			
		
			
			$sql =$this->db->query("SELECT DISTINCT cardnum AS '".$name."' FROM $table");
			$count_all =  $sql->num_rows();
			
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
			
			//$sql =mysql_query("SELECT DISTINCT cardnum AS ".$reseller." FROM $table"); 
			
			$admin_reseller_report =  $this->callshop_model->getCardNum($reseller, $table, $start,$perpage, $name);
			
			
			if(count($admin_reseller_report) > 0)
			{
				//$json_data['page'] = $page_no;				
				//$json_data['total'] = $config['total_rows'];	
				
				foreach ($admin_reseller_report as $key => $value)
				{
					$json_data['rows'][] = array('cell'=>array($value['bth'],
					  $value['dst'],
					  $value['idd'],
					  $value['atmpt'],
					  $value['cmplt'],
					  $value['asr'],
					  $value['mcd'],
					  $value['act'],
					  $value['bill'],
					  $this->common_model->calculate_currency($value['price']),
					  $this->common_model->calculate_currency($value['cost'])));							   
				}
			}
			//grid json data	
			//$json_data = array();$json_data['page'] = 0;$json_data['total'] = 0;
			//$json_data['rows'][] = array('cell'=>array("asdfa","sdfasf","sdfasf","adfsds","fsdf","gfhf","hgfhf","fghf","gfhgf","fghgf","ghjf"));
			echo json_encode($json_data);					
		}
		
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions reseller_search------
	 * We post array of reseller field to CI database session variable reseller_search
	 */
	function reseller_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
			
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('reseller_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'adminReports/resellerReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_reseller------
	 * Empty CI database session variable reseller_search for normal listing
	 */
	function clearsearchfilter_reseller()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('reseller_search', "");
		redirect(base_url().'adminReports/resellerReport/');
		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions reseller_list------
	 * Listing of Reseller for selection of account number in quick search
	 */
	function reseller_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Reseller';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_reseller_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_reseller_list',$data);
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
	 * -------Here we write code for controller adminReports functions resellerReport------
	 * Reseller report with call record info from start date to end date with IDD code and destination
	*/
	function resellerReport($grid=NULL, $start_date=NULL, $end_date=NULL, $reseller=NULL, $destination=NULL, $pattern=NULL, $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL )
	{
		
		$name = "Reseller";
		$type = "1";
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Reseller Report';
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = 'Reseller Report';	
		
		$data['cur_menu_no'] = 2;
		
		//For Reseller
		
		//$Reseller_post = $this->input->post('Reseller',0);
		
		if($this->session->userdata('advance_search')==1){
			
			$reseller_search =  $this->session->userdata('reseller_search');
			
			if(!empty($reseller_search['reseller'])) {
				$reseller  = $reseller_search['reseller'];
			}
			
			if(!empty($reseller_search['Destination'])) {
				$destination  = $reseller_search['Destination'];
			}
			
			
			if(!empty($reseller_search['Pattern'])) {
				$pattern  = $reseller_search['Pattern'];
			}
			
			if(!empty($reseller_search['start_date'])) {
				$start_date_before  = $reseller_search['start_date'];
				
				$start_date_before = explode(" ", $start_date_before);
				$start_date = @$start_date_before[0];
				$time = explode(":", @$start_date_before[1]);
				
				$start_hour = $time[0];
				$start_minute = $time[1];
				$start_second = "00";
			}
			
			if(!empty($reseller_search['end_date'])) {
				$end_date_before  = $reseller_search['end_date'];
				
				$end_date_before = explode(" ", $end_date_before);
				$end_date = @$end_date_before[0];
				$time = explode(":", @$end_date_before[1]);
				
				$end_hour = $time[0];
				$end_minute = $time[1];
				$end_second = "59";
			}		
			
		}	
		
		 
		if($reseller == NULL)
		{
			 $reseller = "ALL";
		}
		
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			 $sth_reseller = $this->accounts_model->getReseller("".$this->session->userdata('username')."", $type);
			}
		else {
			$sth_reseller = $this->accounts_model->getReseller("", $type);			
		}
		
		$data['Reseller'] = $reseller;
		$data['reseller'] = $sth_reseller;
		
		//For Destination
		//$Destination_post = $this->input->post('destination',0);
		if($destination == NULL)
		{
			 $destination = "ALL";
		}
		if(urldecode($pattern) == NULL)
		{
			 $pattern = "ALL";
		}			
		
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$sth_destination = $this->callshop_model->getDestination("".$this->session->userdata('username')."");
		}
		else{
			$sth_destination = $this->callshop_model->getDestination();
		}
		
		
		$destination_list = $sth_destination[1];
		$pattern_list = $sth_destination[2];
		
		$data['Destination'] = $destination;
		$data['destination'] = $destination_list;
		
		$data['Pattern'] = urldecode($pattern);
		$data['pattern'] = $pattern_list;
		
		$sd =  $start_date;		   
		$ed =  $end_date;
		 
		$data['start_date'] = $sd;
		$data['end_date'] = $ed;
		
		$data['start_hour'] = $start_hour;
		$data['start_minute'] = $start_minute;
		$data['start_second'] = $start_second;
		
		$data['end_hour'] = $end_hour;
		$data['end_minute'] = $end_minute;
		$data['end_second'] = $end_minute;
		
		if($sd==NULL || $ed==NULL || $sd=='NULL' || $ed=='NULL'){
			$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
			$sd = $sd." 00:00:00";
			$ed = date('Y-m-d 23:59:59');
			
			$data['start_date'] = date("Y-m-d", strtotime(date('m').'/01/'.date('Y')));
			$data['end_date'] = date('Y-m-d');
			
			$data['start_hour'] = '00';
			$data['start_minute'] = '00';
			$data['start_second'] = '00';
			
			$data['end_hour'] = '23';
			$data['end_minute'] = '59';
			$data['end_second'] = '59';
		}
		else{
			$sd =  $start_date." ".$start_hour.":".$start_minute.":".$start_second;		   
			$ed =  $end_date." ".$end_hour.":".$end_minute.":".$end_minute;
		}
		
		
		
		
		if (!empty($_POST))// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			// put your processing code here... we show what we do for emailing. You will need to add a correct email address
			if ($this->_process_create($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('.');
			}
		}

		if ($this->uri->segment(3) === FALSE)
		{				
			$this->load->view('view_adminReports_resellerReport',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_adminReports_resellerReport',$data);
		}
		else 
		{			
		//Filter		 
		  
			
		   $where = ""; 
		   
			if($sd!='NULL' && $ed!='NULL' && $sd!="" && $ed!="") {	
				$where = " AND callstart BETWEEN '". $sd . "' AND '". $ed . "' ";
			}
			 
			 
			if ( $reseller == 'ALL' ) {
			   if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
					$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE reseller = '". $reseller. "' AND type IN (".$type.")) ";
				}
				else {
			//            if ( strpos( $this->session->userdata('logintype'), "1" ) != -1 ) {
						$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE type IN (".$type.")) ";
			//           }
			//            elsif ( strpos( $this->session->userdata('logintype'), "3" ) != -1 ) {
			//				if ($Reseller_post != 'ALL') {
			//	                $where .= "AND cardnum = " . $Reseller_post . " ";
			//		}
			//           }
				}
			}
			else {
			if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
				$where .="AND cardnum = '" . $reseller . "' ";
			}
			else {
					if ( strpos(  $type, "1" ) != -1 ) {
						$where .= "AND cardnum IN (SELECT `number` FROM accounts WHERE `number` = '". $reseller. "' AND type IN (".$type.")) ";
					}
					elseif ( strpos(  $type, "3" ) != -1 ) {
					if ($reseller != 'ALL') {
							$where .= "AND cardnum = '" . $reseller . "' ";
					}
				}
			}
		}
		
	
		if ( $destination == 'ALL' ) {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= "";
			}
			else {
				$where .= "AND notes LIKE '" . "%|" . urldecode($pattern)  . "' ";
			}
		}
		else {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= ""; 
			}
			else {
				
				$where .= "AND (notes LIKE '". "%|" . $destination . "|%" . "' "
				  . "OR notes LIKE '" .  "%|" . urldecode($pattern) . "') ";
			}
		}

		
		$table = "tmp_" . time();
// 		$drop_view = @mysql_query("DROP VIEW $table");
		//$query ="CREATE TEMPORARY TABLE $table SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		$query = "CREATE TEMPORARY TABLE  $table AS SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
// 		echo $query;exit;
		//CREATE VIEW prodsupp AS
		
		$rs_create = $this->db->query($query);
		 
		if($rs_create) {
			$sql =$this->db->query("SELECT DISTINCT cardnum AS '".$name."' FROM $table");
			$count_all =  $sql->num_rows();
			
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
			
			//$sql =mysql_query("SELECT DISTINCT cardnum AS ".$reseller." FROM $table"); 
			
			$admin_reseller_report =  $this->callshop_model->getCardNum($reseller, $table, $start,$perpage, $name);
			
			
			if(count($admin_reseller_report) > 0)
			{
				//$json_data['page'] = $page_no;				
				//$json_data['total'] = $config['total_rows'];	
				
				foreach ($admin_reseller_report as $key => $value)
				{
					
					$json_data['rows'][] = array('cell'=>array($value['bth'],
					$value['dst'],
					$value['idd'],
					$value['atmpt'],
					$value['cmplt'],
					$value['asr'],
					$value['acd'],
					$value['mcd'],
					$value['act'],
					$value['bill'],
					$this->common_model->calculate_currency($value['price']),
					$this->common_model->calculate_currency($value['cost'])));
															   
				}
			}
			
			
		
		
			//grid json data	
			//$json_data = array();$json_data['page'] = 0;$json_data['total'] = 0;
			//$json_data['rows'][] = array('cell'=>array("asdfa","sdfasf","sdfasf","adfsds","fsdf","gfhf","hgfhf","fghf","gfhgf","fghgf","ghjf"));
			echo json_encode($json_data);					
		}
		
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions provider_search------
	 * We post array of provider field to CI database session variable provider_search
	 */
	function provider_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
			
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('provider_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
			redirect(base_url().'adminReports/providerReport/');
		}
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_provider------
	 * Empty CI database session variable provider_search for normal listing
	 */
	function clearsearchfilter_provider()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('provider_search', "");
		redirect(base_url().'adminReports/providerReport/');
		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions provider_list------
	 * Listing of Provider for selection of account number in quick search
	 */
	function provider_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
		
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List Provider';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_provider_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_provider_list',$data);
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
	 * -------Here we write code for controller adminReports functions providerReport------
	 * Provider report with call record info from start date to end date with IDD code and destination
	*/
	function providerReport($grid=NULL, $start_date=NULL, $end_date=NULL, $reseller=NULL, $destination=NULL, $pattern=NULL, $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL)
	{
		$name = "Provider";
		$type = "3";
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Provider Report';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Provider Report';	
		
		$data['cur_menu_no'] = 2;
		
			
		//For Reseller		
		//$Reseller_post = $this->input->post('Reseller',0);
		
		if($this->session->userdata('advance_search')==1){
			
			$provider_search =  $this->session->userdata('provider_search');
			
			if(!empty($provider_search['reseller'])) {
				$reseller  = $provider_search['reseller'];
			}
			
			if(!empty($provider_search['Destination'])) {
				$destination  = $provider_search['Destination'];
			}
			
			
			if(!empty($provider_search['Pattern'])) {
				$pattern  = $provider_search['Pattern'];
			}
			
			if(!empty($provider_search['start_date'])) {
				$start_date_before  = $provider_search['start_date'];
				
				$start_date_before = explode(" ", $start_date_before);
				$start_date = @$start_date_before[0];
				$time = explode(":", @$start_date_before[1]);
				
				$start_hour = $time[0];
				$start_minute = $time[1];
				$start_second = "00";
			}
			
			if(!empty($provider_search['end_date'])) {
				$end_date_before  = $provider_search['end_date'];
				
				$end_date_before = explode(" ", $end_date_before);
				$end_date = @$end_date_before[0];
				$time = explode(":", @$end_date_before[1]);
				
				$end_hour = $time[0];
				$end_minute = $time[1];
				$end_second = "59";
			}		
			
		}
		
		 
		if($reseller == NULL)
		{
			 $reseller = "ALL";
		}
		
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			 $sth_reseller = $this->accounts_model->getReseller("".$this->session->userdata('username')."", $type);
			}
		else {
			$sth_reseller = $this->accounts_model->getReseller("", $type);			
		}
		
		$data['Reseller'] = $reseller;
		$data['reseller'] = $sth_reseller;
		
		//For Destination
		//$Destination_post = $this->input->post('destination',0);
		if($destination == NULL)
		{
			 $destination = "ALL";
		}
		if($pattern == NULL)
		{
			 $pattern = "ALL";
		}
				
		
		if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
			$sth_destination = $this->callshop_model->getDestination("".$this->session->userdata('username')."");
		}
		else{
			$sth_destination = $this->callshop_model->getDestination();
		}
		
		
		$destination_list = $sth_destination[1];
		$pattern_list = $sth_destination[2];
		
		$data['Destination'] = $destination;
		$data['destination'] = $destination_list;
		
		$data['Pattern'] = urldecode($pattern);
		$data['pattern'] = $pattern_list;
		
		$sd =  $start_date;		   
		$ed =  $end_date;
		
		$data['start_date'] = $sd;
		$data['end_date'] = $ed;
		
		$data['start_hour'] = $start_hour;
		$data['start_minute'] = $start_minute;
		$data['start_second'] = $start_second;
		
		$data['end_hour'] = $end_hour;
		$data['end_minute'] = $end_minute;
		$data['end_second'] = $end_second;
		   
	   if($sd==NULL || $ed==NULL || $sd=='NULL' || $ed=='NULL'){
			$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
			$ed = date('Y-m-d 23:59:59');
			
			$data['start_date'] = date("Y-m-d", strtotime(date('m').'/01/'.date('Y')));
			$data['end_date'] = date('Y-m-d');
			
			$data['start_hour'] = '00';
			$data['start_minute'] = '00';
			$data['start_second'] = '00';
			
			$data['end_hour'] = '23';
			$data['end_minute'] = '59';
			$data['end_second'] = '59';
		}else{
			
			$sd =  $start_date." ".$start_hour.":".$start_minute.":".$start_second;		   
			$ed =  $end_date." ".$end_hour.":".$end_minute.":".$end_minute;
		}
		
			
		if (!empty($_POST))// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			// put your processing code here... we show what we do for emailing. You will need to add a correct email address
			if ($this->_process_create($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('.');
			}
		}

		if ($this->uri->segment(3) === FALSE)
		{				
			$this->load->view('view_adminReports_providerReport',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_adminReports_providerReport',$data);
		}
		else 
		{
						
		//Filter		 
		  /* $sd =  $start_date;		   
		   $ed =  $end_date;
		   
		   if($sd==NULL || $ed==NULL || $sd=='NULL' || $ed=='NULL'){
				$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
				$ed = date('Y-m-d 23:59:59');
			}*/
		   
		   $where = ""; 
		   
			if($sd!='NULL' && $ed!='NULL' && $sd!="" && $ed!="") {	
				$where = " AND callstart BETWEEN '". $sd . "' AND '". $ed . "' ";
			}
			 
			 
			if ( $reseller == 'ALL' ) {
			   if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
					$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE reseller = '". $reseller. "' AND type IN (".$type.")) ";
				}
				else {
			//            if ( strpos( $this->session->userdata('logintype'), "1" ) != -1 ) {
						$where .="AND cardnum IN (SELECT `number` FROM accounts WHERE type IN (".$type.")) ";
			//           }
			//            elsif ( strpos( $this->session->userdata('logintype'), "3" ) != -1 ) {
			//				if ($Reseller_post != 'ALL') {
			//	                $where .= "AND cardnum = " . $Reseller_post . " ";
			//		}
			//           }
				}
			}
			else {
			if ( $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ) {
				$where .="AND cardnum = '" . $reseller . "' ";
			}
			else {
					if ( strpos(  $type, "1" ) != -1 ) {
						$where .= "AND cardnum IN (SELECT `number` FROM accounts WHERE `number` = '". $reseller. "' AND type IN (".$type.")) ";
					}
					elseif ( strpos(  $type, "3" ) != -1 ) {
					if ($reseller != 'ALL') {
							$where .= "AND cardnum = '" . $reseller . "' ";
					}
				}
			}
		}
		
		if ( $destination == 'ALL' ) {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= "";
			}
			else {
				$where .= "AND notes LIKE '" . "%|" . urldecode($pattern)  . "' ";
			}
		}
		else {
			if ( urldecode($pattern) == 'ALL' || urldecode($pattern) == "") {
				$where .= ""; 
			}
			else {
				$where .= "AND (notes LIKE '". "%|" . $destination . "|%" . "' "
				  . "OR notes LIKE '" .  "%|" . urldecode($pattern) . "'  ) ";
			}
		}
		
		$table = "tmp_" . time();
		
		//$drop_view = @mysql_query("DROP TEMPORARY TABLE $table");
		
		//$query ="CREATE TEMPORARY TABLE $table SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		$query = "CREATE TEMPORARY TABLE  $table AS SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		//CREATE VIEW prodsupp AS
		
		$rs_create = $this->db->query($query);
		
		 
		 
		if($rs_create) {			
		
			
			$sql =$this->db->query("SELECT DISTINCT cardnum AS '".$name."' FROM $table");
			$count_all =  $sql->num_rows();
			
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
			
			//$sql =mysql_query("SELECT DISTINCT cardnum AS ".$reseller." FROM $table"); 
			
			$admin_reseller_report =  $this->callshop_model->getCardNum($reseller, $table, $start,$perpage, $name);
			
			if(count($admin_reseller_report) > 0)
			{
				$json_data['page'] = $page_no;				
				$json_data['total'] = $config['total_rows'];	
				
				foreach ($admin_reseller_report as $key => $value)
				{
					
					$json_data['rows'][] = array('cell'=>array($value['bth'],
															   $value['dst'],
															   $value['idd'],
															   $value['atmpt'],
															   $value['cmplt'],
															   $value['asr'],
															   $value['acd'],
															   $value['mcd'],
															   $value['act'],
															   $value['bill'],
															   $this->common_model->calculate_currency($value['price']),
															   $this->common_model->calculate_currency($value['cost'])));
															   
				}
			}
			
			
		
		
			//grid json data	
			//$json_data = array();$json_data['page'] = 0;$json_data['total'] = 0;
			//$json_data['rows'][] = array('cell'=>array("asdfa","sdfasf","sdfasf","adfsds","fsdf","gfhf","hgfhf","fghf","gfhgf","fghgf","ghjf"));
			echo json_encode($json_data);					
		}
		
							
		}
		
		
	}	
	
}


?>