<?php

class Statistics extends CI_Controller
{
	function  Statistics()
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
		$access_control = validate_access($logintype,$method, "statistics");
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
		$data['page_title'] = 'Statistics';	
				
		$this->load->view('view_statistics',$data);
	}
	
	/**
	 * -------Here we write code for controller statistics functions error_search------
	 * We post an array of error field to CI database session variable error_search
	 */
	function error_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('error_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'statistics/listerrors/');
		}
	}
	
	/**
	 * -------Here we write code for controller statistics functions clearsearchfilter_error------
	 * Empty CI database session variable error_search for normal listing
	 */
	function clearsearchfilter_error()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('error_search', "");
		redirect(base_url().'statistics/listerrors/');
		
	}	
	
	/**
	 * -------Here we write code for controller statistics functions listerrors------
	 * Listing of Errors data through php function json_encode
	 * @action: delete, list
	 */
	function listerrors($action = false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Statistics - list errors';	
		$data['cur_menu_no'] = 8;
		//echo $this->uri->segment(3);
		if ($action === FALSE)
		{	
			$this->load->view('view_statistics_listerrors',$data);
		}
		elseif($action == 'delete')
		{
			$this->load->model("statistics_model");
			$data = array("uniqueid"=>$id);
			$this->statistics_model->remove_error($data);
			$this->session->set_userdata('astpp_notification', 'Error removed successfully!');
			redirect(base_url().'statistics/listerrors');
		}
		else if($action == "grid")
		{
			//grid json data	
			$json_data = array();
					
			$this->fscdr_db = Common_model::$global_config['fscdr_db'];
			
			$this->load->model("statistics_model");
			$count_all = $this->statistics_model->getErrorsCount();
			
			$config['total_rows'] = $count_all;			
			$config['per_page'] = $_GET['rp'];
	
			$page_no = $_GET['page'];			
			
			$json_data['page'] = $page_no;			
			$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
						
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			
			$query = $this->statistics_model->getErrorsList($start, $perpage);	
					
			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$json_data['rows'][] = array('cell'=>array(
						$row['uniqueid'],
						$row['calldate'],//calldate
						$row['clid'],
						$row['src'],
						$row['dst'],
						$row['dcontext'],
						$row['channel'],
						$row['dstchannel'],
						$row['lastapp'],
						$row['lastdata'],
						$row['duration'],
						$row['billsec'],						
						$row['disposition'],
						$row['amaflags'],
						$row['accountcode'],										
						$row['userfield'],										
						$this->common_model->calculate_currency($row['cost']),
						$this->get_action_buttons($row['uniqueid'])
					));
				}
			}
			
			echo json_encode($json_data);					
		}
	}
	
	
	function get_action_buttons($id)
	{		
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		$ret_url = '';		
		$ret_url .= '<a href="/statistics/listerrors/delete/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	
	/**
	 * -------Here we write code for controller statistics functions trunkstats_search------
	 * We post an array of trunkstats field to CI database session variable trunkstats_search
	 */
	function trunkstats_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('trunkstats_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'statistics/trunkstats/');
		}
	}
	
	/**
	 * -------Here we write code for controller statistics functions clearsearchfilter_trunkstats------
	 * Empty CI database session variable trunkstats_search for normal listing
	 */
	function clearsearchfilter_trunkstats()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('trunkstats_search', "");
		redirect(base_url().'statistics/trunkstats/');
		
	}
	
	/**
	 * -------Here we write code for controller statistics functions trunkstats------
	 * Listing of trunks stat data through php function json_encode
	 */
	function trunkstats($grid=NULL, $start_date=NULL, $end_date=NULL, $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Statistics - trunk stats';	
		$data['cur_menu_no'] = 8;
		
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
		
		if ($this->uri->segment(3) === FALSE)
		{	
			$this->load->view('view_statistics_trunkstats',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_statistics_trunkstats',$data);
		}
		else 
		{
			//grid json data	
			$json_data = array();
			
			$this->load->model("statistics_model");
			$count_all = $this->statistics_model->getTrunkStatsCount();
			
			$config['total_rows'] = $count_all;			
			$config['per_page'] = $_GET['rp']=10;
	
			$page_no = $_GET['page']=1; 
			
			$json_data['page'] = $page_no;			
			$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
						
			$perpage = $config['per_page'];
			$start = ($page_no-1) * $perpage;
			if($start < 0 )
			$start = 0;
			
			/*$sd =  $start_date;		   
		   	$ed =  $end_date;*/  	
			
			//$this->fscdr_db = Common_model::$global_config['fscdr_db'];
			
			//$this->fscdr_db->where('cost','error'); //@build_list_errors
			//$query = $this->fscdr_db->get('trunks');
			//$query = $this->db->get('trunks');
			
			$trunkstats = $this->statistics_model->getTrunkStatsList($start, $perpage, $sd, $ed);
			
			if(count($trunkstats) > 0)
			{
				foreach ($trunkstats as $key => $value)
				{
					$json_data['rows'][] = array('cell'=>array(
						$value['tech_path'],
						$value['ct'],
						$value['bs'],
						$value['acwt'],
						"(".$value['calls'].") ". $value['success_rate']."%",
						"(".$value['failed_calls'].") ". $value['congestion_rate']."%"
						
					));
				}
			}
			
			echo json_encode($json_data);					
		}
		
	}
	
	/**
	 * -------Here we write code for controller statistics functions viewcdrs------
	 * Listing of cdrs data through php function json_encode
	 */
	function viewcdrs($grid=NULL, $start_date=NULL, $end_date=NULL, $answered=NULL, $accountcode=NULL, $trunk=NULL, $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Statistics - view CDRs';	
		$data['cur_menu_no'] = 8;
		
		//$this->load->view('view_statistics_viewcdrs',$data);
		$trunklist = $this->common_model->list_trunks();
		
		$data['trunklist'] = $trunklist;		
		
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
		
				
		if ($this->uri->segment(3) === FALSE)
		{	
			$this->load->view('view_statistics_viewcdrs',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_statistics_viewcdrs',$data);
		}
		else 
		{
			//grid json data	
			$json_data = array();	
			
			$this->load->model("statistics_model");			
			$count_all = $this->statistics_model->getViewCdrsCount($sd, $ed, $answered, $accountcode, $trunk);
			
			$config['total_rows'] = $count_all;			
			$config['per_page'] = $_GET['rp'];
	
			$page_no = $_GET['page']; 
						
			$json_data['page'] = $page_no;			
			$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
						
			 
			$perpage = $config['per_page'];
			$start = ($page_no-1) * $perpage;
			if($start < 0 )
			$start = 0;
			
			//$this->fscdr_db = $this->load->database('fscdr', true);
			
			//$this->fscdr_db->where('cost','error'); //@build_list_errors
			//$query = $this->fscdr_db->get('trunks');
			//$query = $this->db->get('trunks');
			
			$query = $this->statistics_model->getViewCdrsList($sd, $ed, $answered, $accountcode, $trunk, $start, $perpage);
			
			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					
// 					$dcontext   = substr( $row['dcontext'], 0, 4 ) . "..";
// 					$channel    = substr( $row['channel'],  0, 9 ) . "..";
// 					$dstchannel = substr( $row['channel'],  0, 9 ) . "..";
// 					$lastdata   = substr( $row['lastdata'], 0, 4 ) . "..";
					if ($this->session->userdata('logintype') != 2 ) {
						$row['accountcode'] = "";
						$row['cost']        = "";
					}
					
// 					$json_data['rows'][] = array('cell'=>array(
// 						$row['calldate'],
// 						$row['clid'],//calldate
// 						$row['src'],
// 						$row['dst'],
// 						"<acronym title='".$row['dcontext']."'>".$dcontext."</acronym>",
// 						"<acronym title='".$row['channel'].">".$channel."</acronym>",
// 						"<acronym title='".$row['dstchannel'].">".$dstchannel."</acronym>",
// 						$row['lastapp'],
// 						"<acronym title='".$row['lastdata'].">".$lastdata."</acronym>",
// 						$row['duration'],
// 						$row['billsec'],
// 						$row['disposition'],
// 						$row['amaflags'],
// 						$row['accountcode'],
// 						"<acronym title='".$row['uniqueid']."'>...</acronym>",
// 						$row['userfield'],
// 						$this->common_model->calculate_currency($row['cost'])
// 					));
					$json_data['rows'][] = array('cell'=>array(
						$row['calldate'],
						$row['clid'],//calldate
						$row['src'],
						$row['dst'],
						$row['dcontext'],
						$row['channel'],
						$row['dstchannel'],
						$row['lastapp'],
						$row['lastdata'],
						$row['duration'],
						$row['billsec'],
						$row['disposition'],
						$row['amaflags'],
						$row['accountcode'],
						$row['uniqueid'],
						$row['userfield'],
						$this->common_model->calculate_currency($row['cost'])
					));
				}
			}
			
			echo json_encode($json_data);					
		}
		
	}
	
	
	/**
	 * -------Here we write code for controller statistics functions fscdrs_search------
	 * We post an array of trunkstats field to CI database session variable fscdrs_search
	 */
	function fscdrs_search()
	{
		$ajax_search = $this->input->post('ajax_search',0);
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('fscdrs_search', $_POST);		
		}		
		if(@$ajax_search!=1) {
		redirect(base_url().'statistics/viewfscdrs/');
		}
	}
	
	/**
	 * -------Here we write code for controller statistics functions clearsearchfilter_fscdrs------
	 * Empty CI database session variable fscdrs_search for normal listing
	 */
	function clearsearchfilter_fscdrs()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('fscdrs_search', "");
		redirect(base_url().'statistics/viewfscdrs/');		
	}
	
	
	/**
	 * -------Here we write code for controller statistics functions viewfscdrs------
	 * Listing of fscdrs data through php function json_encode
	 */
	function viewfscdrs($grid=NULL, $start_date=NULL, $end_date=NULL, $answered=NULL, $accountcode=NULL, $trunk=NULL, $start_hour=NULL,$start_minute=NULL, $start_second=NULL,   $end_hour=NULL, $end_minute=NULL, $end_second=NULL)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Statistics - View Freeswitch CDRs';	
		$data['cur_menu_no'] = 8;
		
		//$this->load->view('view_statistics_viewfscdrs',$data);
		
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
		
		$trunklist = $this->common_model->list_trunks();
		
		$data['trunklist'] = $trunklist;		

		if ($this->uri->segment(3) === FALSE)
		{	
			$this->load->view('view_statistics_viewfscdrs',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_statistics_viewfscdrs',$data);
		}
		else 
		{
			//grid json data	
			$json_data = array();
		
			/*$sd =  $start_date;		   
		    $ed =  $end_date;
			
			if($sd==NULL || $ed==NULL){
				$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
				$ed = date('Y-m-d 23:59:59');
			}	*/			
			$this->load->model("statistics_model");		
			$count_all = $this->statistics_model->getViewCdrsCount($sd, $ed, $answered, $accountcode, $trunk);
			
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
			
			//$this->fscdr_db = $this->load->database('fscdr', true);
			
			//$this->fscdr_db->where('cost','error'); //@build_list_errors
			//$query = $this->fscdr_db->get('trunks');
			$query = $this->statistics_model->getViewCdrsList($sd, $ed, $answered, $accountcode, $trunk, $start, $perpage);
			
			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					
// 					$dcontext   = substr( $row['dcontext'], 0, 4 ) . "..";
// 					$channel    = substr( $row['channel'],  0, 9 ) . "..";
// 					$dstchannel = substr( $row['channel'],  0, 9 ) . "..";
// 					$lastdata   = substr( $row['lastdata'], 0, 4 ) . "..";
					if ($this->session->userdata('logintype') != 2 ) {
						$row['accountcode'] = "";
						$row['cost']        = "";
					}
					
				  $json_data['rows'][] = array('cell'=>array(
						$row['calldate'],
						$row['clid'],//calldate
						$row['src'],
						$row['dst'],
						$row['dcontext'],
						$row['channel'],
						$row['dstchannel'],
						$row['lastapp'],
						$row['lastdata'],
						$row['duration'],
						$row['billsec'],
						$row['disposition'],
						$row['amaflags'],
						$row['accountcode'],
						$row['uniqueid'],
						$row['userfield'],
						$this->common_model->calculate_currency($row['cost'])
					));
				}
			}
			
			echo json_encode($json_data);					
		}
		
	}
	
}


?>