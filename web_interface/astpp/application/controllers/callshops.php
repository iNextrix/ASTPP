<?php 
class Callshops extends CI_Controller
{
	function  Callshops()
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
		$this->load->model('callshop_model');
		$this->load->model('common_model');
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
		$access_control = validate_access($logintype,$method, "callshops");
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
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'Call Shops';	
				
		$this->load->view('view_callshops',$data);
	}
	
	
	/**
	 * -------Here we write code for controller callshops functions add------
	 * Create Call Shops
	 */
	function add()
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Callshops | Create';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'Create Callshop';	
		$data['cur_menu_no'] = 3;		
		
		if(!empty($_POST))
		{			
			$errors = "";
			if(trim($_POST['callshop_name']) == "")
			$errors .= "Invalid Callshoname<br />";
			if(trim($_POST['accountpassword']) == "" || strlen($_POST['accountpassword']) < 6)
			$errors .= "Account password is required<br />";
			if(trim($_POST['credit_limit']) == "")
			$errors .= "Credit limit is required<br />";
			if(trim($_POST['osc_site']) == "")
			$errors .= "OS Commerce Site is required<br />";
			if(trim($_POST['osc_dbname']) == "")
			$errors .= "OS Commerce Site database name is required<br />";
			if(trim($_POST['osc_dbhost']) == "")
			$errors .= "OS Commerce Site database host is required<br />";
			if(trim($_POST['osc_dbpass']) == "")
			$errors .= "OS Commerce Site database password is required<br />";
			if(trim($_POST['osc_dbuser']) == "")
			$errors .= "OS Commerce Site database user is required<br />";
							
			if ($errors == "")
			{				
				$this->callshop_model->add_callshop($_POST);
				$this->session->set_userdata('astpp_notification', 'Callshop added successfully!');
				redirect(base_url().'callshops/listAll/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', $errors);
				redirect(base_url().'callshops/listAll/');				
			}			
		}
				
		$data['sweepList'] = $this->common_model->get_sweep_list();
		$data['currency_list'] = $this->common_model->get_currency_list();
		
		$this->load->view('view_callshops_add',$data);
		
	}	
	
	
	/**
	 * -------Here we write code for controller callshops functions listAll------
	 * Listing of Callshops table data through php function json_encode
	 */
	function listAll($grid=NULL, $callshop_name=NULL)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | List Callshops';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'List Callshops';	
		
		$data['cur_menu_no'] = 3;
			
		if (!empty($_POST))
		{
			if ($this->_process_create($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('.');
			}
		}

		if ($this->uri->segment(3) === FALSE)
		{				
			$this->load->view('view_callshops_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_callshops_list',$data);
		}
		else 
		{	
			$json_data = array();		
			
			
			$count_all = $this->callshop_model->getCallShopsCount($callshop_name);
			
			$config['total_rows'] = $count_all;			
			$config['per_page'] = $_GET['rp'];
	
			$page_no = $_GET['page']; 
			
			$json_data['page'] = $page_no;			
			$json_data['total'] = ($config['total_rows']>0) ? $config['total_rows'] : 0;	
						
			 
			 $perpage = $config['per_page'];
			 $start = ($page_no-1) * $perpage;
			 if($start < 0 )
			 $start = 0;
			 
			$query = $this->callshop_model->getCallShopsList($start, $perpage, $callshop_name);
			
			if($query->num_rows() > 0)
			{			
				foreach ($query->result_array() as $row)
				{
					$json_data['rows'][] = array('cell'=>array(
							$row['name'],
							$row['osc_dbname'],
							$row['osc_dbpass'],
							$row['osc_dbuser'],
							$row['osc_dbhost'],
							$row['osc_site'],
							$this->get_action_buttons($row['name'])
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
		$ret_url .= '<a href="/callshops/remove/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}	
	
	/**
	 * -------Here we write code for controller callshops functions remove------
	 * this function check if callshop id exist or not then remove from database.
	 * @id: CallShop ID
	 */	
	function remove($id="")
	{
		if (!($callshop = $this->callshop_model->get_callshop_by_name(urldecode($id))))
		{				
			$this->session->set_userdata('astpp_errormsg', 'Callshop not found!');
			redirect(base_url().'callshops/listAll/');
		}
		
		$this->callshop_model->remove_callshop(array("callshop"=>$callshop['name']));		
		$this->session->set_userdata('astpp_notification', 'Callshop removed successfully!');
		redirect(base_url().'callshops/listAll/');		
	}
	
	
	/**
	 * -------Here we write code for controller callshops functions boothReport------
	 * Booth report with call record info from start date to end date with IDD code , destination and pattern
	*/
	function boothReport($grid=NULL, $start_date=NULL, $end_date=NULL, $reseller=NULL, $destination=NULL, $pattern=NULL)
	{
		
		$name = "Booth";
		$type = "6";
		
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | Reseller Report';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'Booth Report';	
		
		$data['cur_menu_no'] = 2;
		
		//For Reseller
		
		//$Reseller_post = $this->input->post('Reseller',0);
		 
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
		
		$data['Pattern'] = $pattern;
		$data['pattern'] = $pattern_list;
		
		
		
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
			$this->load->view('view_boothReport',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_boothReport',$data);
		}
		else 
		{			
		//Filter		 
		   $sd =  $start_date;		   
		   $ed =  $end_date;
		   
		   if($sd==NULL || $ed==NULL){
				$sd = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
				$ed = date('Y-m-d 23:59:59');
			}
		   
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
			if ( $pattern == 'ALL' || $pattern == "") {
				$where .= "";
			}
			else {
				$where .= "AND notes LIKE " . "%" . $pattern  . " ";
			}
		}
		else {
			if ( $pattern == 'ALL' || $pattern == "") {
				$where .= ""; 
			}
			else {
				$where .= "AND (notes LIKE '". "%" . $destination . "%" . "' "
				  . "OR notes LIKE '" .  "%" . $pattern . "' ";
			}
		}
		
		$table = "tmp_" . time();
		//$drop_view = @mysql_query("DROP VIEW $table");
		//$query ="CREATE TEMPORARY TABLE $table SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
		$query = "CREATE VIEW  $table AS SELECT * FROM cdrs WHERE uniqueid != '' " . $where;
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
															   $value['mcd'],
															   $value['act'],
															   $value['bill'],
															   $value['price'],
															   $value['cost']));
															   
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
	 * -------Here we write code for controller callshops functions booths_list------
	 * @action: DeactiveBooth
	 * @id: booth name
	 
	 * @action: RestoreBooth
	 * @id: booth name 
	 
	 * @action: HangupCall
	 * @id: booth name 
	*/
	function booths_list($action=false,$id=false)
	{
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | List Booths';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'List Booths';	
		
		$data['cur_menu_no'] = 11;
		
		if ($action === false)
		{
		    $action = 'list';
		}
		
		switch($action) {
			case "DeactiveBooth":
			{
				$array['booth_name'] = $id;
				$msg = $this->callshop_model->deactivate_Booth($array);
				$this->session->set_userdata('astpp_notification', "Booth Deactivated Successfully!");			
				redirect(base_url().'callshops/booths_list');		
			}
			break;
			case "RestoreBooth":
			{
				$array['booth_name'] = $id;
				$msg = $this->callshop_model->restore_Booth($array);
				$this->session->set_userdata('astpp_notification', "Booth Restore Successfully!");			
				redirect(base_url().'callshops/booths_list');	
			}
			break;
			case "HangupCall":
			{
				$array['booth_name'] = $id;
				$array['channel'] = "";
				//$msg = $this->callshop_model->booth_Hangup_Call($array);
				$this->session->set_userdata('astpp_notification', "Hangup Call Done!");			
				redirect(base_url().'callshops/booths_list');	
			}
			case "list":
			{
				$this->load->view('view_callshop_booths_list',$data);
			}
			break;
			default:
			$this->load->view('view_callshop_booths_list',$data);		
		}				
		
	}
	
	/**
	 * -------Here we write code for controller callshops functions manage_booths_json------
	 * Listing of Booths for reseller 
	 */
	function manage_booths_json()
	{
		$json_data = array();
		$json_data['page'] = 1;	
		$json_data['total'] = 0;	
		
		$booths = array();
		
		$reseller = $this->session->userdata('username');
		$booth_list = $this->Astpp_common->list_booths_callshop($reseller);
		
		$accountinfo = $this->Astpp_common->get_account( $this->session->userdata('username'));
		
		 foreach($booth_list as $key => $name)
		 {
			$row = array();
			$boothinfo = $this->Astpp_common->get_account_including_closed($name ); 			
			$balance = $this->Astpp_common->accountbalance($name); 		
			
			$count = $this->Astpp_common->get_call_count($name);
			
			$row['name'] = $name;
			$row['balance']    = money_format('%.2n',  ($balance / 10000) );
			$row['call_count'] =$count;
			$row['currency']   = $boothinfo['currency'];	
			
			
			if ( $boothinfo['status'] != 1 ) {
				$row['status'] = "Blocked";
			}
			else {
				$row['status'] = "Active";
			}
			array_push($booths, $row);
			
		 }
		
		 $count_all = count($booths);		
		 $config['total_rows'] = $count_all;			
		
		 $config['per_page'] = $_GET['rp'];

		 $page_no = $_GET['page']; 
		
		 $json_data['page'] = $page_no;		
		 $json_data['total'] = $config['total_rows'];	
		
		 $perpage = $config['per_page'];
		 $start = ($page_no-1) * $perpage;
		 if($start < 0 )
		 $start = 0;
		
		 for($i=$start;$i<=($config['per_page']+$start-1);$i++) { 
		 	if (isset($booths[$i]['name'])) {
			$json_data['rows'][] = array('cell'=>array(
						$booths[$i]['name'],
						$booths[$i]['balance'],
						$booths[$i]['currency'],
						$booths[$i]['call_count'],
						"",
						"",
						"",
						"",
						$booths[$i]['status'],
						$this->get_action_buttons_booths($booths[$i]['name'])				
					));
			}
		 }
		echo json_encode($json_data);			
	}
	
	
	/**
	 * -------Here we write code for controller callshops functions remove_callshop_booth------
	 * this function check if callshop booth id exist or not then remove from database.
	 * @id: Call Shop Booth ID
	 */
	function remove_callshop_booth($id=false)
	{
		if($id!=""){
			$data['booth_list'] = $id;
			$this->callshop_model->remove_callshop_booth($data);
			$this->session->set_userdata('astpp_notification', 'Booth Shop Removed Successfully!');			
		}
		redirect(base_url().'callshops/booths_list');
	}
	
	/**
	 * -------Here we write code for controller callshops functions booth_detail------
	 * Booth details through booth name
	 * @id: Booth Name
	 */
	function booth_detail($id=false)
	{
		$data = array();
		$data['app_name'] = 'ASTPP - Open Source Billing Solution | View Booth';
		$data['username'] = $this->session->userdata('username');	
		$data['page_title'] = 'View Booth';	
		$data['cur_menu_no'] = 11;
		
		$booth_name = $id;
		$accountinfo = $this->Astpp_common->get_account($booth_name);
		
		$booth_info = $this->callshop_model->getBoothDetail($booth_name);
		
		$cdrs = array();
		
		if(sizeof($booth_info)>0) {
			foreach($booth_info as $key => $record){
			if(!$record['callerid']) {
				 $record['callerid'] = "unknown";
			}
			if(!$record['uniqueid']){
				 $record['uniqueid'] = "N/A";
			}
			
			if(!$record['disposition']){
				 $record['disposition'] = "N/A";
			}
			
			if(!$record['notes']){
				$record['notes'] = "";
			}
			
			if(!$record['callstart']){
				$record['callstart'] = "";
			}
			
			if(!$record['callednum']) {
				$record['callednum'] = "";
			}
			if(!$record['billseconds']) 
			{
				 $record['billseconds'] = "";
			}
			
			if($record['debit']){
				  $record['debit'] = $record['debit'] / 10000;
                $record['debit'] = money_format( "%.6n", $record['debit'] );
			}
			else{
				 $record['debit'] = "-";
			}
			
			if ( $record['credit'] ) {
                $record['credit'] = $record['credit'] / 10000;
                $record['credit'] = money_format("%.6n",$record['credit']);
            }
            else {
                $record['credit'] = "-";
            }
			
			 if ( $record['cost'] ) {
                $record['cost'] = $record['cost'] / 10000;
                $record['cost'] = money_format( "%.6n", $record['cost']);
            }
            else {
                $record['credit'] = "-";
            }
			
			 $record['profit'] = ( $record['debit'] - $record['cost']);
			  array_push( $cdrs, $record );
		}
		}
		$data['cdrs'] = $cdrs;
		$data['booth_name'] = $booth_name;
		
		$balance = ($this->Astpp_common->accountbalance($booth_name ) / 10000);
		$data['balance'] = $balance;
    	$unrated = $this->common_model->count_unrated_cdrs_account($accountinfo['number'], $accountinfo['cc'] );
		$data['unrated'] = $unrated;
		
		$data['sip_login'] = $this->common_model->getVoipInfo_sip_login($accountinfo['cc']);
		$data['iax_login'] = $this->common_model->getVoipInfo_iax_login($accountinfo['cc']);
		
		$this->load->view("view_callshop_booths_detail", $data);
	}
	
	function booth_action()
	{
		if(!empty($_POST)) {
			if($_POST['action']=="Remove CDRs") {
				$booth_name = $this->input->post('booth_list',0);
				$data['booth_name'] = $this->input->post('booth_list',0);
				$msg = $this->callshop_model->remove_CDRs($_POST);
				$this->session->set_userdata('astpp_notification', 'CDRS Marked As Billed!');			
				redirect(base_url().'callshops/booth_detail/'.$booth_name);	
			}
			elseif($_POST['action'] == 'Generate Invoice'){
				$booth_name = $this->input->post('booth_list',0);
				$data['booth_name'] = $this->input->post('booth_list',0);
				$_POST['booth_list'] = '';
				$msg = $this->callshop_model->generate_invoice($_POST);
				$this->session->set_userdata('astpp_notification', "View Invoice");			
				redirect(base_url().'callshops/booth_detail/'.$booth_name);	
			}
		}
		else{
			redirect(base_url().'callshops/booths_list');
		}		
	}
	
	function get_action_buttons_booths($id)
	{
		//$update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
		$viewdetails_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
    	$delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
		//$import_style = 'style="text-decoration:none;background-image:url(/images/import.png);"';
		$url = '';
		$ret_url = '';

		$ret_url = '<a href="/callshops/booths_list/HangupCall/'.$id.'/" class="icon"  title="Update">Hangup Call</a>';
		$ret_url .= '<a href="/callshops/booths_list/DeactivateBooth/'.$id.'/" class="icon"   title="Update">Deactivate Booth</a>';
		$ret_url .= '<a href="/callshops/booths_list/RestoreBooth/'.$id.'/" class="icon"  title="Update">Restore Booth</a>';	
		$ret_url .= '<a href="'.base_url().'callshops/booth_detail/'.$id.'/" class="icon" '.$viewdetails_style.' title="View Details">&nbsp;</a>';	
		$ret_url .= '<a href="/callshops/remove_callshop_booth/'.$id.'/" class="icon" '.$delete_style.' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';

		return $ret_url;
	}
	
	
	/**
	 * -------Here we write code for controller callshops functions add_booth------
	 * Add CallShop Booth detail
	 */
	function add_booth()
	{
		
		$data = array();
		$this->load->model('common_model');
		$account_info = $this->session->userdata('accountinfo');
		
		$data['currency'] = $account_info['currency'];
		$this->load->model('rates_model');
		$data['price_list'] = $this->rates_model->get_price_list();
		
		$config = $this->common_model->get_system_config();
		$data['context']  = $config['booth_context'];
		
		if(!empty($_POST))
		{	
			$errors = "";
		
							
			if ($errors == "")
			{	
				$msg =$this->callshop_model->add_callshop_booth($_POST);
				//$this->session->set_userdata('astpp_notification', 'Booth added successfully!');
				$this->session->set_userdata('astpp_notification', $msg);
				redirect(base_url().'callshops/booths_list/');				
			}
			else 
			{
				$this->session->set_userdata('astpp_errormsg', $errors);
				redirect(base_url().'callshops/booths_list/');				
			}		
		}
		
		$this->load->view('view_callshops_add_booth',$data);
		
	}
	
	
	/**
	 * -------Here we write code for controller callshops functions booth_search------
	 * We post an array of user field to CI database session variable booth_search
	 */
	function booth_search()
	{	
		$ajax_search = $this->input->post('ajax_search',0);
			
		if($this->input->post('advance_search', TRUE)==1) {		
			$this->session->set_userdata('advance_search',$this->input->post('advance_search'));
			unset($_POST['action']);
			unset($_POST['advance_search']);
			$this->session->set_userdata('booth_search', $_POST);		
		}
		if(@$ajax_search!=1) {		
		redirect(base_url().'callshops/boothReport/');
		}
	}
	
	/**
	 * -------Here we write code for controller callshops functions clearsearchfilter_booth------
	 * Empty CI database session variable booth_search for normal listing
	 */
	function clearsearchfilter_booth()
	{
		$this->session->set_userdata('advance_search',0);
		$this->session->set_userdata('user_search', "");
		redirect(base_url().'callshops/boothReport/');
		
	}
	
	function callshop_booth_list($grid=NULL, $account=NULL, $company=NULL,$fname=NULL, $lname=NULL)
	{
				
		$this->load->model('accounts_model');
		$data['app_name'] = 'ASTPP - Open Source Billing Solution';
		$data['username'] = $this->session->userdata('user_name');	
		$data['page_title'] = 'List User';
		
		if ($this->uri->segment(3) === FALSE)
		{			
			$this->load->view('view_callshopbooth_list',$data);
		}
		elseif($this->uri->segment(3) != 'grid')
		{
			$this->load->view('view_callshopbooth_list',$data);
		}
		else 
		{
			
			$count_all = $this->accounts_model->getAccount_Count($account, $company,$fname,$lname,'5');	
			
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
			 
			$query = $this->accounts_model->getAccount_list($start,$perpage, $account, $company,$fname,$lname,'5');
			
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

}


?>