<?php
class Callshop_model extends CI_Model 
{
    function Callshop_model()
    {     
        parent::__construct();      
    }
	
	function add_callshop($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Create CallShop";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function get_callshop_by_name($name)
	{
		$this->db->where("name",$name);
		$query = $this->db->get("callshops");

		if($query->num_rows() > 0)
		return $query->row_array();
		else 
		return false;
	}
	
	
	function remove_callshop($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Remove CallShop";
		$data['action'] = "Remove CallShop";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function add_callshop_booth($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Create Booth";
		//$data['action'] = "Generate Booth";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	function remove_callshop_booth($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "Remove Booth";
		$data['action'] = "Remove Booth";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function getBoothDetail($booth_name)
	{
		$this->db->where('cardnum', $booth_name);
		$this->db->where('status',0);
		$this->db->select('uniqueid,callstart,callerid,callednum,disposition,billseconds,debit,credit,notes,cost');
		$this->db->order_by('callstart','DESC');
		$this->db->from('cdrs');
		$query = $this->db->get();
		
		if($query->num_rows()>0){
			return $query->result_array();
		}
	}
	
	function remove_CDRs($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Booth";
		$data['action'] = "Remove CDRs";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function generate_invoice($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "View Booth";
		$data['action'] = "Generate Invoice";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function deactivate_Booth($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "List Booths";
		$data['action'] = "Deactivate Booth";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	function restore_Booth($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "List Booths";
		$data['action'] = "Restore Booth";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		return $this->curl->sendRequestToPerlScript($url,$data);	
	}
	
	
	function getCallShopsCount($callshopname="")
	{		
		if($callshopname!="" && $callshopname!=NULL){
		$this->db->where('name',$callshopname);	
		}
		
		if($this->session->userdata('logintype') == 1)
		$this->db->where('reseller',$this->session->userdata('username'));
		
		$this->db->where('callshops.status','1');
		$this->db->from('callshops');
		$this->db->join("accounts","callshops.name=accounts.number");
		$trunkcnt = $this->db->count_all_results();
		return $trunkcnt;
	}
	
	function getCallShopsList($start, $limit, $callshopname="")
	{
		if($callshopname!="" && $callshopname!=NULL){
		$this->db->where('name',$callshopname);	
		}
		
		if($this->session->userdata('logintype') == 1)
		$this->db->where('reseller',$this->session->userdata('username'));
		
		$this->db->limit($limit,$start);	
	  	$this->db->where('callshops.status','1');
		$this->db->from('callshops');
		$this->db->join("accounts","callshops.name=accounts.number");	
		$query = $this->db->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	function getCardNum($reseller, $table, $start, $limit, $name)
	{		
		$admin_reseller_report = array();
		$q = "SELECT DISTINCT cardnum AS '".$name."' FROM $table";		
		$query = $this->db->query($q);					
    	if($query->num_rows() > 0)
		{
			
			foreach ($query->result_array() as $row)
			{
				
				 $bth = @$row[''.$name.''];
				 //$bth = $this->session->userdata('username');
				 
				 $sql1 = "SELECT notes, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
				  				. " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, " 
				  				. " SUM(debit) AS cost, SUM(cost) AS price FROM "
								. $table . " WHERE (notes IS NOT NULL AND notes != '') AND cardnum = '". $bth . "' GROUP BY notes";
				
				
				 $query1 = $this->db->query($sql1);
				 //echo $query1->num_rows();
				 if($query1->num_rows() > 0)
					{
						foreach ($query1->result_array() as $row1)
						{
							//echo $row1['notes'];
							//echo "<br>";
							//	^800801802$
							 //333333|USA|1<br>777777|USA|1<br>888888|USA|1<br>|Calling Card DID|800801802<br>|Calling Card DID|^800801802$<br>|USA|1<br>666666|USA|1<br>
							// $note1 = explode( '/(\^|DID)/', $row1['notes'], 2 );
							// my @note1 = explode( m/(\^|DID:)/, $row1->{notes}, 2 );
							 // $note1 = explode( 'DID|^', $row1['notes'], 2);
							 $note1 = preg_split('/(\^|DID:)/', $row1['notes'], 2);
							 
							 /*
							Array(	[0] => 333333|USA|1	)
							Array(	[0] => 777777|USA|1)
							Array(	[0] => 888888|USA|1 )
							Array( [0] => |Calling Card DID|800801802 )
							Array( [0] => |Calling Card DID|^800801802$)
							Array(	[0] => |USA|1)
							Array(	[0] => 666666|USA|1)
							 */
							 $caret_sign="";
							 if(isset($note1[1])) {
								 $caret_sign = "^";
							 }
							 $pos = strpos($row1['notes'], "DID:");
							 if($pos){
								 $caret_sign = 'DID:';
							 }
							 
							 $idd   = $caret_sign.@$note1[1] . @$note1[2];
							// $note1 = explode( "[|.-]", @$note1[0] );
							$note1 = preg_split( '/\|/', @$note1[0]);
							 $dst   = ( @$note1[0] == 1 ) ? @$note1[0] : @$note1[1];
							 if($dst == "")
							 	$dst   = 'N/A';
								
							$atmpt = $row1['attempts'];
							$acd   = $row1['acd'];
							$mcd   = $row1['mcd'];
							$bill  = $row1['billable'];
							$price = $row1['price'];
							$cost  = $row1['cost'];	
							
							$notes = $row1['notes'];
							
							$notes = "notes = '" . $row1['notes'] . "' ";
							
							$sql2 = "SELECT COUNT(*) AS completed FROM $table WHERE $notes AND disposition IN ('ANSWERED','NORMAL_CLEARING')";
// 							echo $sql2."<br/>";
							$query2 = $this->db->query($sql2); 
							$row2 = $query2->row_array();
							$cmplt = ($row2['completed']!=0) ? $row2['completed']:0;
							
							$asr = ( ( $atmpt - $cmplt ) / $atmpt ) * 100;
				
							$in = "";
							$sql3 =  "SELECT uniqueid FROM $table WHERE $notes ";
							$query3 = $this->db->query($sql3);
							foreach($query3->result_array() as $row3) 
							{
								if($row3['uniqueid'])
								$in .=   "'" . $row3['uniqueid'] . "',";
							}
							
							if(strlen($in) > 0)
							$in = substr( $in, 0, -1 ) ;
							
							$this->db_fscdr = Common_model::$global_config['fscdr_db'];
							$sql4 = "SELECT SUM(duration) AS actual FROM ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." WHERE uniqueid IN ($in)";
							$query4 = $this->db_fscdr->query($sql4); 							
							$row4 = $query4->row_array();
							
							$act = $row4['actual'];
							
							$act  = (int)( $act / 60 ) . ":" .  ( $act % 60 );
							$acd  = (int)( $acd / 60 ) . ":" .  ( $acd % 60 );
							$mcd  = (int)( $mcd / 60 ) . ":" .  ( $mcd % 60 );
							$bill = (int)( $bill / 60 ) . ":" . ( $bill % 60 );
							$price = $price / 1;
							$cost  = $cost / 1;			
							
						
							$admin_reseller_report[] = array('bth' => $bth, 'dst' => $dst, 'idd' => $idd, 'atmpt' => $atmpt, 'cmplt' => $cmplt, 'asr' => $asr, 'acd' => $acd, 'mcd'=> $mcd, 'act' => $act, 'bill' => $bill,'price' => $price, 'cost' => $cost);	
							
						}
					}
				 
        
				 	
			}
		}
		$sth = mysql_query("DROP TEMPORARY TABLE $table");
		//$sth = $this->db->query("DROP VIEW $table");
		return $admin_reseller_report;
			
	}
	
	function getDestination($username="")
	{
		$notes = "";
		if($username!=""){
			$notes = "WHERE notes LIKE '".$username." %'";
		}
		$q = "SELECT DISTINCT notes FROM cdrs ".$notes." "; 
		
		$query = $this->db->query($q);	
		
		$options = array();
		$dst = array();
		$ptn = array();
	
        if($query->num_rows() > 0)
		{
			$i=0;
			foreach ($query->result_array() as $row)
			{
				
				$notes = $row['notes'];
			 	//$note = split( 'm/(\^|DID:)/', $notes);	
				$note = preg_split('/(\^|DID:)/', $notes, 2);
				//$note = explode( "m/(\^|DID:)/", $notes, 2 );											
				//$note = explode( "DID|^", $notes);	
				//echo "<pre>";
				//print_r($note);
				$caret_sign="";
				if(isset($note[1])) {
					 $caret_sign = "^";
				 }
				 $pos = strpos($notes, "DID:");
				 if($pos){
					 $caret_sign = 'DID:';
				 }
				 
				$ptn[$i] =  @$caret_sign.@$note[1] . @$note[2];
				//$note = explode( "[|.-]", @$note[0] );
				$note = preg_split( '/\|/', $note[0] );
				//$dst[$i] = ( $note == 1 ) ? $note[0] : (if($note[0] != "") $note[1]);
				$dst[$i] = (@$note ==1) ? @$note[0] : ( (@$note[0]!="") ? @$note[1]: "");
				$i++;
					
				//$options[] = $row['number'];
			}
			
		}
		
		
		return array('1'=> array_unique($dst), '2' => array_unique($ptn));  
	}

	
}
?>