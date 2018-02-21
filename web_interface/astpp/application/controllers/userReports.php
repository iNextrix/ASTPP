<?php 

class UserReports extends CI_Controller
{
	function UserReports()
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
		$this->load->model('rates_model');
		$this->load->model('userreport_model');
		
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
		$access_control = validate_access($logintype,$method, "userreports");
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
		$this->myReport();
	}
	
	
	//******************************* My Call Detail Report ***************************************************//
		
	function myReport()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | My Call Detail Report';
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = 'My Call Detail Report';
		$data['cur_menu_no'] = 4;
		$this->load->view('view_cdr_user',$data);
	}
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_grid------
	 * Listing of Brands Cdrs
	*/
	function myReport_grid()
	{
		$json_data = array();		
		
		$count_all = $this->userreport_model->getmycdrs(false);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
		 
		$perpage = $config['per_page'];
		$start = ($page_no-1) * $perpage;
		if($start < 0 )
		  $start = 0;
		$query = $this->userreport_model->getmycdrs(true, $start, $perpage);
		$json_data['rows'] = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			    $json_data['rows'][] = array('cell'=>array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $row['calltype'],
			  ));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions user_search------
	 * We post array of user field to CI database session variable user_search
	 */
	function myReport_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('usercdr_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		  redirect(base_url().'userReports/myReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_myReports()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('mycdr_search', "");
		redirect(base_url().'userReports/myReport/');
		
	}
	
	function export_cdr_user_xls()
	{
	    $query = $this->userreport_model->getmycdrs(true);
	    $user_array = array();    	    
	    $user_array[] = array("Date","CallerID","Called Number","Bill Seconds","Disposition","Debit","Call Type");
	   
	    if($query->num_rows() > 0)
	    {		
		foreach ($query->result_array() as $row)
		{
		      $user_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $row['calltype'],
			  );
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($user_array,'CDR_'.date("Y-m-d").'.xls');
	}
	
	function export_cdr_user_pdf()
	{
	    $query = $this->userreport_model->getmycdrs(true);
	    $provider_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    	    
	    $this->fpdf->tablewidths = array(30, 30, 30, 20, 32, 22,22);
	    $user_array[] = array("Date","CallerID","Called Number","Bill Seconds","Disposition","Debit","Call Type");	    
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      $user_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $row['calltype'],
			  );
		}
	    }
	    
	    $this->fpdf->AliasNbPages();
	    $this->fpdf->AddPage();    
	    
	    $this->fpdf->SetFont('Arial', '', 15);
	    $this->fpdf->SetXY(60, 5);
	    $this->fpdf->Cell(100, 10, "Provider CDR Report ".date('Y-m-d'));
	    	    
	    $this->fpdf->SetY(20);
	    $this->fpdf->SetFont('Arial', '', 7);
	    $this->fpdf->SetFillColor(255, 255, 255);
	    $this->fpdf->lMargin=10;
	    
	    $dimensions = $this->fpdf->export_pdf($user_array, "5");
	    $this->fpdf->Output('CDR_'.date("Y-m-d").'.pdf',"D");
	}
	
	
	//******************************* My Callingcard Call Detail Report ***************************************************//
		
	function myccReport()
	{
		//@build_callingcard_cdrs		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Calling Cards';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Calling Cards CDRs';	
		$data['cur_menu_no'] = 4;
		
		$this->load->view('view_cc_cdr_user',$data);
	}
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_grid------
	 * Listing of Brands Cdrs
	*/
	function myccReport_grid()
	{
		$json_data = array();		
		
		$count_all = $this->userreport_model->get_cc_cdrs(false);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;

		$query = $this->userreport_model->get_cc_cdrs(true ,$start, $perpage);
		
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
					$this->common_model->calculate_currency($row['debit'])
				));
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions user_search------
	 * We post array of user field to CI database session variable user_search
	 */
	function myccReport_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('brands_cc_cdrs_search', $_POST);		
		}				
		if(@$ajax_search!=1) {		
			redirect(base_url().'callingcards/myccReport_grid/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_myccReports()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('brands_cc_cdrs_search', "");
		redirect(base_url().'callingcards/myccReport_grid/');	
		
	}
	
	function export_cc_cdr_xls()
	{
	    $query = $this->userreport_model->get_cc_cdrs(true,'','',false);
	    $user_array = array();    	    
	    $user_array[] = array("Date","CallerID","Called Number","Card Number","Bill Seconds","Disposition","Debit");
	   
	    if($query->num_rows() > 0)
	    {		
		foreach ($query->result_array() as $row)
		{
		      $user_array[] = array(
				  $row['callstart'],
				  $row['clid'],
				  $row['destination'],
				  $row['cardnumber'],
				  $row['seconds'],
				  $row['disposition'],
				  $this->common_model->calculate_currency($row['debit'])
			  );
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($user_array,'CallingCard_CDR_'.date("Y-m-d").'.xls');
	}
	
	function export_cc_cdr_pdf()
	{
	    $query = $this->userreport_model->get_cc_cdrs(true,'','',false);
	    $provider_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    	    
	    $this->fpdf->tablewidths = array(30, 30, 30, 20, 32, 22,22);
	    $user_array[] = array("Date","CallerID","Called Number","Card Number","Bill Seconds","Disposition","Debit");    
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      $user_array[] = array(
				  $row['callstart'],
				  $row['clid'],
				  $row['destination'],
				  $row['cardnumber'],
				  $row['seconds'],
				  $row['disposition'],
				  $this->common_model->calculate_currency($row['debit'])
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
	    $this->fpdf->lMargin=10;
	    
	    $dimensions = $this->fpdf->export_pdf($user_array, "5");
	    $this->fpdf->Output('CallingCard_CDR_'.date("Y-m-d").'.pdf',"D");
	}
	
	
}
?>