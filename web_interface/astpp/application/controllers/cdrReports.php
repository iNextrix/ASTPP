<?php 

class CdrReports extends CI_Controller
{
	function CdrReports()
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
		$this->load->model('cdr_model');
		
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
		$access_control = validate_access($logintype,$method, "cdrreports");
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
	 * -------Here we write code for controller cdrreports functions customerReport------
	 * Listing of Customer Cdrs
	*/
	function customerReport()
	{		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Customer Call Detail Report';
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = 'Customer Call Detail Report';
		$data['cur_menu_no'] = 4;
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		$data['trunks'] = $this->common_model->list_trunks_select('');
		if($this->session->userdata['logintype']==2)
		{
		    $this->load->view('view_cdr_customer',$data);
		}else{
		    $this->load->view('view_cdr_reseller_customer',$data);
		}
	}
	
	/**
	 * -------Here we write code for controller cdrreports functions customerReport------
	 * Listing of Customer Cdrs
	*/
	function customerReport_grid()
	{
		$json_data = array();		
		
		$count_all = $this->cdr_model->getcustomercdrs(false);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		$query = $this->cdr_model->getcustomercdrs(true, $start, $perpage);
		$json_data['rows'] = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			      if($this->session->userdata['logintype']==2)
			      {
				$json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$this->common_model->calculate_currency($row['cost']),
					$row['trunk'],
					$row['provider'],
					$row['pricelist'],
					$row['pattern'],
					$row['notes'],
					$row['calltype'],
				));
			      }else{
				  $json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$this->common_model->calculate_currency($row['cost']),
					$row['pricelist'],
					$row['pattern'],
					$row['notes'],
					$row['calltype'],
				));
			      }
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller cdrreports functions customerReport_search------	 
	 * We post array of user field to CI database session variable user_search
	 */
	function customerReport_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('customercdr_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		  redirect(base_url().'cdrReports/customerReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_customerReports()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('customercdr_search', "");
		redirect(base_url().'cdrReports/customerReport/');
		
	}
	
	
	function export_cdr_customer_xls()
	{
	    $query = $this->cdr_model->getcustomercdrs(true,'','',false);
	    $customer_array = array();    
	    if($this->session->userdata['logintype']==2)
	    {
	      $customer_array[] = array("Date","CallerID","Called Number","Account Number","Bill Seconds","Disposition","Debit","Cost","Trunk","Provider","Pricelist","Code","Destination","Call Type");
	    }else{
	      $customer_array[] = array("Date","CallerID","Called Number","Account Number","Bill Seconds","Disposition","Debit","Cost","Pricelist","Code","Destination","Call Type");
	    }
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      if($this->session->userdata['logintype']==2)
		      {
			$customer_array[] = array(
				$row['callstart'],
				$row['callerid'],
				$row['callednum'],
				$row['number'],
				$row['billseconds'],
				$row['disposition'],					
				$this->common_model->calculate_currency($row['debit']),
				$this->common_model->calculate_currency($row['cost']),
				$row['trunk'],
				$row['provider'],
				$row['pricelist'],
				$row['pattern'],
				$row['notes'],
				$row['calltype']
			);
		      }else{
			  $customer_array[] = array(
				$row['callstart'],
				$row['callerid'],
				$row['callednum'],
				$row['number'],
				$row['billseconds'],
				$row['disposition'],					
				$this->common_model->calculate_currency($row['debit']),
				$this->common_model->calculate_currency($row['cost']),
				$row['pricelist'],
				$row['pattern'],
				$row['notes'],
				$row['calltype']
			);
		      }
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($customer_array,'Customer_CDR_'.date("Y-m-d").'.xls');
	}
	
	
	function export_cdr_customer_pdf()
	{
	    $query = $this->cdr_model->getcustomercdrs(true);
	    $customer_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    
	    if($this->session->userdata['logintype']==2)
	    {
		$this->fpdf->tablewidths = array(20, 20, 16, 16, 10, 18,13, 13, 16,14, 12, 10, 15, 13);
		$customer_array[] = array("Date","CallerID","Called Number","Account Number","BillSec","Dispo.","Debit","Cost","Trunk","Provider","Pricelist","Code","Destination","Call Type");
	    }else{
		$this->fpdf->tablewidths = array(22, 24, 20, 18, 10, 27,13, 13, 14, 13, 15, 16);
		$customer_array[] = array("Date","CallerID","Called Number","Account Number","BillSec","Dispo.","Debit","Cost","Pricelist","Code","Destination","Call Type");
	    }
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      if($this->session->userdata['logintype']==2)
		      {
			$customer_array[] = array(
				$row['callstart'],
				$row['callerid'],
				$row['callednum'],
				$row['number'],
				$row['billseconds'],
				$row['disposition'],					
				$this->common_model->calculate_currency($row['debit']),
				$this->common_model->calculate_currency($row['cost']),
				$row['trunk'],
				$row['provider'],
				$row['pricelist'],
				$row['pattern'],
				$row['notes'],
				$row['calltype']
			);
		      }else{
			  $customer_array[] = array(
				$row['callstart'],
				$row['callerid'],
				$row['callednum'],
				$row['number'],
				$row['billseconds'],
				$row['disposition'],					
				$this->common_model->calculate_currency($row['debit']),
				$this->common_model->calculate_currency($row['cost']),
				$row['pricelist'],
				$row['pattern'],
				$row['notes'],
				$row['calltype']
			);
		      }
		}
	    }
	    
	    $this->fpdf->AliasNbPages();
	    $this->fpdf->AddPage();    
	    
	    $this->fpdf->SetFont('Arial', '', 15);
	    $this->fpdf->SetXY(60, 5);
	    $this->fpdf->Cell(100, 10, "Customer CDR Report ".date('Y-m-d'));
	    	    
	    $this->fpdf->SetY(20);
	    $this->fpdf->SetFont('Arial', '', 7);
	    $this->fpdf->SetFillColor(255, 255, 255);
	    $this->fpdf->lMargin=2;
	    
	    $dimensions = $this->fpdf->export_pdf($customer_array, "5");
	    $this->fpdf->Output('Customer_CDR_'.date("Y-m-d").'.pdf',"D");
	}
	
	
	//************************************ Reseller Report ***************************************************//
		
	function resellerReport()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Reseller Call Detail Report';
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = 'Reseller Call Detail Report';
		$data['cur_menu_no'] = 4;
		
		$this->load->model('rates_model');
		
		
		if($this->session->userdata['logintype']==2)
		{
		  $data['pricelist'] = $this->rates_model->get_price_list();
		  $data['trunks'] = $this->common_model->list_trunks_select('');
		  $this->load->view('view_cdr_reseller',$data);
		}else{
		  $this->load->view('view_cdr_reseller_reseller',$data);
		}
	}
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_grid------
	 * Listing of Brands Cdrs
	*/
	function resellerReport_grid()
	{
		$json_data = array();		
		
		$count_all = $this->cdr_model->getresellercdrs(false);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
		 
		$perpage = $config['per_page'];
		$start = ($page_no-1) * $perpage;
		if($start < 0 )
		  $start = 0;
		$query = $this->cdr_model->getresellercdrs(true, $start, $perpage);
		$json_data['rows'] = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
			      if($this->session->userdata['logintype']==2)
			      {
				$json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$this->common_model->calculate_currency($row['cost']),
					$row['trunk'],
					$row['provider'],
					$row['pricelist'],
					$row['pattern'],
					$row['notes'],
					$row['calltype'],
				));
			      }else{
				  $json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$this->common_model->calculate_currency($row['cost']),
					$row['pricelist'],
					$row['pattern'],
					$row['notes'],
					$row['calltype'],
				));
			      }
	 		}
		}
		echo json_encode($json_data);		
	}
	
	
	/**
	 * -------Here we write code for controller adminReports functions user_search------
	 * We post array of user field to CI database session variable user_search
	 */
	function resellerReport_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('resellercdr_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		  redirect(base_url().'cdrReports/resellerReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_resellerReports()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('resellercdr_search', "");
		redirect(base_url().'cdrReports/resellerReport/');
		
	}
	
	function export_cdr_reseller_xls()
	{
	    $query = $this->cdr_model->getresellercdrs(true,'','',false);
	    $reseller_array = array();    
	    if($this->session->userdata['logintype']==2)
	    {
	      $reseller_array[] = array("Date","CallerID","Called Number","Account Number","Bill Seconds","Disposition","Debit","Cost","Trunk","Provider","Pricelist","Code","Destination","Call Type");
	    }else{
	      $reseller_array[] = array("Date","CallerID","Called Number","Account Number","Bill Seconds","Disposition","Debit","Cost","Pricelist","Code","Destination","Call Type");
	    }
	    if($query->num_rows() > 0)
	    {		
		foreach ($query->result_array() as $row)
		{
		      if($this->session->userdata['logintype']==2)
		      {
			  $reseller_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['number'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $this->common_model->calculate_currency($row['cost']),
				  $row['trunk'],
				  $row['provider'],
				  $row['pricelist'],
				  $row['pattern'],
				  $row['notes'],
				  $row['calltype']			  
			);
		      }else{
			    $reseller_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['number'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $this->common_model->calculate_currency($row['cost']),
				  $row['pricelist'],
				  $row['pattern'],
				  $row['notes'],
				  $row['calltype']			  
			);
		      }
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($reseller_array,'Reseller_CDR_'.date("Y-m-d").'.xls');
	}
	
	function export_cdr_reseller_pdf()
	{
	    $query = $this->cdr_model->getresellercdrs(true,'','',false);
	    $reseller_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    
	    if($this->session->userdata['logintype']==2)
	    {
		$this->fpdf->tablewidths = array(20, 20, 16, 16, 10, 18,13, 13, 16,14, 12, 10, 15, 13);
		$reseller_array[] = array("Date","CallerID","Called Number","Account Number","BillSec","Dispo.","Debit","Cost","Trunk","Provider","Pricelist","Code","Destination","Call Type");
	    }else{
		$this->fpdf->tablewidths = array(22, 24, 20, 18, 10, 27,13, 13, 14, 13, 15, 16);
		$reseller_array[] = array("Date","CallerID","Called Number","Account Number","BillSec","Dispo.","Debit","Cost","Pricelist","Code","Destination","Call Type");
	    }
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      if($this->session->userdata['logintype']==2)
		      {
			  $reseller_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['number'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $this->common_model->calculate_currency($row['cost']),
				  $row['trunk'],
				  $row['provider'],
				  $row['pricelist'],
				  $row['pattern'],
				  $row['notes'],
				  $row['calltype']			  
			);
		      }else{
			    $reseller_array[] = array(
				  $row['callstart'],
				  $row['callerid'],
				  $row['callednum'],
				  $row['number'],
				  $row['billseconds'],
				  $row['disposition'],					
				  $this->common_model->calculate_currency($row['debit']),
				  $this->common_model->calculate_currency($row['cost']),
				  $row['pricelist'],
				  $row['pattern'],
				  $row['notes'],
				  $row['calltype']			  
			);
		      }
		}
	    }
	    
	    $this->fpdf->AliasNbPages();
	    $this->fpdf->AddPage();    
	    
	    $this->fpdf->SetFont('Arial', '', 15);
	    $this->fpdf->SetXY(60, 5);
	    $this->fpdf->Cell(100, 10, "Reseller CDR Report ".date('Y-m-d'));
	    	    
	    $this->fpdf->SetY(20);
	    $this->fpdf->SetFont('Arial', '', 7);
	    $this->fpdf->SetFillColor(255, 255, 255);
	    $this->fpdf->lMargin=2;
	    
	    $dimensions = $this->fpdf->export_pdf($reseller_array, "5");
	    $this->fpdf->Output('Reseller_CDR_'.date("Y-m-d").'.pdf',"D");
	}
	
	
	//************************************** Provider Call Detail Report ********************************************//
	
	
	function providerReport()
	{		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Provider Call Detail Report';
		$data['username'] = $this->session->userdata('user_name');
		$data['page_title'] = 'Provider Call Detail Report';
		$data['cur_menu_no'] = 4;
		
		$this->load->model('rates_model');
		$data['pricelist'] = $this->rates_model->get_price_list();
		
		$data['trunks'] = $this->common_model->list_trunks_select('');
		
		$this->load->view('view_cdr_provider',$data);		
	}
	
	/**
	 * -------Here we write code for controller callingcards functions brands_cdrs_grid------
	 * Listing of Brands Cdrs
	*/
	function providerReport_grid()
	{
		$json_data = array();		
		
		$count_all = $this->cdr_model->getprovidercdrs(false);
		
		$config['total_rows'] = $count_all;			
		$config['per_page'] = $_GET['rp'];

		$page_no = $_GET['page']; 
		
		$json_data['page'] = $page_no;			
		$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
					
		 
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		$query = $this->cdr_model->getprovidercdrs(true, $start, $perpage);
		if($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$json_data['rows'][] = array('cell'=>array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
// 					$this->common_model->calculate_currency($row['cost']),
					$row['trunk'],
// 					$row['provider'],
// 					$row['pricelist'],
					$row['pattern'],
					$row['notes'],
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
	function providerReport_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('providercdr_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		  redirect(base_url().'cdrReports/providerReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller adminReports functions clearsearchfilter_user------
	 * Empty CI database session variable user_search for normal listing
	 */
	function clearsearchfilter_providerReports()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('providercdr_search', "");
		redirect(base_url().'cdrReports/providerReport/');
		
	}

	function export_cdr_provider_xls()
	{
	    $query = $this->cdr_model->getprovidercdrs(true,'','',false);
	    $provider_array = array();    	    
	    $provider_array[] = array("Date","CallerID","Called Number","Account Number","Bill Seconds","Disposition","Debit","Trunk","Code","Destination","Call Type");
	   
	    if($query->num_rows() > 0)
	    {		
		foreach ($query->result_array() as $row)
		{
		      $provider_array[] = array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$row['trunk'],
					$row['pattern'],
					$row['notes'],
					$row['calltype'],
		      );
		}
	    }
	    $this->load->helper('csv');
	    array_to_csv($provider_array,'Provider_CDR_'.date("Y-m-d").'.xls');
	}
	
	function export_cdr_provider_pdf()
	{
	    $query = $this->cdr_model->getprovidercdrs(true,'','',false);
	    $provider_array = array();    
	    $this->load->library('fpdf');
	    $this->load->library('pdf');
	    $this->fpdf = new PDF('P','pt');
	    $this->fpdf->initialize('P','mm','A4');
	    	    
	    $this->fpdf->tablewidths = array(22, 25, 21, 18, 10, 27,16, 20, 14, 13, 17);
	    $provider_array[] = array("Date","CallerID","Called Number","Account Number","BillSec","Dispo.","Debit","Trunk","Code","Destination","Call Type");	    
	    if($query->num_rows() > 0)
	    {				
		
		foreach ($query->result_array() as $row)
		{
		      $provider_array[] = array(
					$row['callstart'],
					$row['callerid'],
					$row['callednum'],
					$row['number'],
					$row['billseconds'],
					$row['disposition'],					
					$this->common_model->calculate_currency($row['debit']),
					$row['trunk'],
					$row['pattern'],
					$row['notes'],
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
	    $this->fpdf->lMargin=2;
	    
	    $dimensions = $this->fpdf->export_pdf($provider_array, "5");
	    $this->fpdf->Output('Provider_CDR_'.date("Y-m-d").'.pdf',"D");
	}
}
?>