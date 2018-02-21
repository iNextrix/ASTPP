<?php
class Statistics_model extends CI_Model 
{
    function Userdid_model()
    {     
        parent::__construct();      
    }
	
	function remove_error($data)
	{
		$this->load->library("curl");
		$url = "astpp-wraper.cgi";
		$data['mode'] = "List Errors";
		$data['action'] = "Deactivate...";
		$data['logintype'] = $this->session->userdata('logintype');
		$data['username'] = $this->session->userdata('username');		
		$this->curl->sendRequestToPerlScript($url,$data);		
	}
	
	function getErrorsCount()
	{
		$this->db_fscdr = Common_model::$global_config['fscdr_db'];  
		
		if($this->session->userdata('advance_search')==1){
			
			$error_search =  $this->session->userdata('error_search');
			
			if(!empty($error_search['start_date'])) {
				$this->db_fscdr->where('calldate >= ', $error_search['start_date'].':00');
			}
			if(!empty($error_search['end_date'])) {
				$this->db_fscdr->where('calldate <= ', $error_search['end_date'].':59');
			}
			
			$source_operator = $error_search['source_operator'];
			
			if(!empty($error_search['source'])) {
				switch($source_operator){
					case "1":
					$this->db_fscdr->like('clid', $error_search['source']); 
					break;
					case "2":
					$this->db_fscdr->not_like('clid', $error_search['source']);
					break;
					case "3":
					$this->db_fscdr->where('clid', $error_search['source']);
					break;
					case "4":
					$this->db_fscdr->where('clid <>', $error_search['source']);
					break;
				}
			}
			
			$dst_operator = $error_search['dst_operator'];
			
			if(!empty($error_search['dst'])) {
				switch($dst_operator){
					case "1":
					$this->db_fscdr->like('dst', $error_search['dst']); 
					break;
					case "2":
					$this->db_fscdr->not_like('dst', $error_search['dst']);
					break;
					case "3":
					$this->db_fscdr->where('dst', $error_search['dst']);
					break;
					case "4":
					$this->db_fscdr->where('dst <>', $error_search['dst']);
					break;
				}
			}
			
			$duration_operator = $error_search['duration_operator'];
			if(!empty($error_search['duration'])) {
				switch($duration_operator){
					case "1":
					$this->db_fscdr->where('duration ', $error_search['duration']);
					break;
					case "2":
					$this->db_fscdr->where('duration <>', $error_search['duration']);					
					break;
					case "3":
					$this->db_fscdr->where('duration > ', $error_search['duration']); 					
					break;
					case "4":
					$this->db_fscdr->where('duration < ', $error_search['duration']); 	
					break;
					case "5":
					$this->db_fscdr->where('duration >= ', $error_search['duration']);
					break;
					case "6":
					$this->db_fscdr->where('duration <= ', $error_search['duration']);
					break;
				}
			}	
			
			$bill_sec_operator = $error_search['bill_sec_operator'];
			if(!empty($error_search['bill_sec'])) {
				switch($bill_sec_operator){
					case "1":
					$this->db_fscdr->where('billsec ', $error_search['bill_sec']);
					break;
					case "2":
					$this->db_fscdr->where('billsec <>', $error_search['bill_sec']);					
					break;
					case "3":
					$this->db_fscdr->where('billsec > ', $error_search['bill_sec']); 					
					break;
					case "4":
					$this->db_fscdr->where('billsec < ', $error_search['bill_sec']); 	
					break;
					case "5":
					$this->db_fscdr->where('billsec >= ', $error_search['bill_sec']);
					break;
					case "6":
					$this->db_fscdr->where('billsec <= ', $error_search['bill_sec']);
					break;
				}
			}
			
			$this->db_fscdr->where('disposition ', $error_search['disposition']);
			
			$cost_operator = $error_search['cost_operator'];
			if(!empty($error_search['cost'])) {
				switch($cost_operator){
					case "1":
					$this->db_fscdr->where('cost ', $error_search['cost']);
					break;
					case "2":
					$this->db_fscdr->where('cost <>', $error_search['cost']);					
					break;
					case "3":
					$this->db_fscdr->where('cost > ', $error_search['cost']); 					
					break;
					case "4":
					$this->db_fscdr->where('cost < ', $error_search['cost']); 	
					break;
					case "5":
					$this->db_fscdr->where('cost >= ', $error_search['cost']);
					break;
					case "6":
					$this->db_fscdr->where('cost <= ', $error_search['cost']);
					break;
				}
			}				
			
		}
		
		$this->db_fscdr->where_in('cost', array('error','rating'));
		$this->db_fscdr->or_where(array( 'accountcode' => NULL ));
		$this->db_fscdr->or_where('accountcode', ''); 
		$this->db_fscdr->where('cost', 'none'); 
		$this->db_fscdr->from(Common_model::$global_config['system_config']['freeswitch_cdr_table']);
		$errorscnt = $this->db_fscdr->count_all_results();
		//echo $this->db_fscdr->last_query();
		return $errorscnt;
	}
	
	function getErrorsList($start, $limit)
	{
		
		$this->db_fscdr = Common_model::$global_config['fscdr_db'];  
		
		if($this->session->userdata('advance_search')==1){
			
			$error_search =  $this->session->userdata('error_search');
			
			if(!empty($error_search['start_date'])) {
				$this->db_fscdr->where('calldate >= ', $error_search['start_date'].':00');
			}
			if(!empty($error_search['end_date'])) {
				$this->db_fscdr->where('calldate <= ', $error_search['end_date'].':59');
			}
			
			$source_operator = $error_search['source_operator'];
			
			if(!empty($error_search['source'])) {
				switch($source_operator){
					case "1":
					$this->db_fscdr->like('clid', $error_search['source']); 
					break;
					case "2":
					$this->db_fscdr->not_like('clid', $error_search['source']);
					break;
					case "3":
					$this->db_fscdr->where('clid', $error_search['source']);
					break;
					case "4":
					$this->db_fscdr->where('clid <>', $error_search['source']);
					break;
				}
			}
			
			$dst_operator = $error_search['dst_operator'];
			
			if(!empty($error_search['dst'])) {
				switch($dst_operator){
					case "1":
					$this->db_fscdr->like('dst', $error_search['dst']); 
					break;
					case "2":
					$this->db_fscdr->not_like('dst', $error_search['dst']);
					break;
					case "3":
					$this->db_fscdr->where('dst', $error_search['dst']);
					break;
					case "4":
					$this->db_fscdr->where('dst <>', $error_search['dst']);
					break;
				}
			}
			
			$duration_operator = $error_search['duration_operator'];
			if(!empty($error_search['duration'])) {
				switch($duration_operator){
					case "1":
					$this->db_fscdr->where('duration ', $error_search['duration']);
					break;
					case "2":
					$this->db_fscdr->where('duration <>', $error_search['duration']);					
					break;
					case "3":
					$this->db_fscdr->where('duration > ', $error_search['duration']); 					
					break;
					case "4":
					$this->db_fscdr->where('duration < ', $error_search['duration']); 	
					break;
					case "5":
					$this->db_fscdr->where('duration >= ', $error_search['duration']);
					break;
					case "6":
					$this->db_fscdr->where('duration <= ', $error_search['duration']);
					break;
				}
			}	
			
			$bill_sec_operator = $error_search['bill_sec_operator'];
			if(!empty($error_search['bill_sec'])) {
				switch($bill_sec_operator){
					case "1":
					$this->db_fscdr->where('billsec ', $error_search['bill_sec']);
					break;
					case "2":
					$this->db_fscdr->where('billsec <>', $error_search['bill_sec']);					
					break;
					case "3":
					$this->db_fscdr->where('billsec > ', $error_search['bill_sec']); 					
					break;
					case "4":
					$this->db_fscdr->where('billsec < ', $error_search['bill_sec']); 	
					break;
					case "5":
					$this->db_fscdr->where('billsec >= ', $error_search['bill_sec']);
					break;
					case "6":
					$this->db_fscdr->where('billsec <= ', $error_search['bill_sec']);
					break;
				}
			}
			
			$this->db_fscdr->where('disposition ', $error_search['disposition']);
			
			$cost_operator = $error_search['cost_operator'];
			if(!empty($error_search['cost'])) {
				switch($cost_operator){
					case "1":
					$this->db_fscdr->where('cost ', $error_search['cost']);
					break;
					case "2":
					$this->db_fscdr->where('cost <>', $error_search['cost']);					
					break;
					case "3":
					$this->db_fscdr->where('cost > ', $error_search['cost']); 					
					break;
					case "4":
					$this->db_fscdr->where('cost < ', $error_search['cost']); 	
					break;
					case "5":
					$this->db_fscdr->where('cost >= ', $error_search['cost']);
					break;
					case "6":
					$this->db_fscdr->where('cost <= ', $error_search['cost']);
					break;
				}
			}				
			
		}
		
		$this->db_fscdr->where_in('cost', array('error','rating','none'));
		$this->db_fscdr->or_where(array( 'accountcode' => NULL ));
		$this->db_fscdr->or_where('accountcode', ''); 
// 		$this->db_fscdr->where('cost', 'none'); 
		$this->db->order_by("calldate", "desc"); 
		$this->db_fscdr->limit($limit,$start);
	  	$this->db_fscdr->from(Common_model::$global_config['system_config']['freeswitch_cdr_table']);	
		$query = $this->db_fscdr->get();	
		//echo $this->db->last_query();		
		return $query;
	}
	
	function getTrunkStatsCount()
	{
		if($this->session->userdata('advance_search')==1){
			
			$trunkstats_search =  $this->session->userdata('trunkstats_search');
			
			$this->db->where('name ', $trunkstats_search['trunk']);
			
			if(!empty($trunkstats_search['start_date'])) {
				$sd =  $trunkstats_search['start_date'].':00';
			}
			if(!empty($trunkstats_search['end_date'])) {
				$ed =  $trunkstats_search['end_date'].':59';
			}
		}
		
		if ( $this->session->userdata('logintype') == 3  ) {        
		$this->db->where('provider',"".$this->session->userdata('username')."");
		}
		$this->db->from('trunks');
		$trunkcnt = $this->db->count_all_results();
		//echo $this->db->last_query();
		return $trunkcnt;
	}
	
	function getTrunkStatsList($start, $limit, $sd, $ed)
	{
		$trunkstats = array();
		
		if($this->session->userdata('advance_search')==1){
			
			$trunkstats_search =  $this->session->userdata('trunkstats_search');
			
			$this->db->where('name ', $trunkstats_search['trunk']);
			
			if(!empty($trunkstats_search['start_date'])) {
				$sd =  $trunkstats_search['start_date'].':00';
			}
			if(!empty($trunkstats_search['end_date'])) {
				$ed =  $trunkstats_search['end_date'].':59';
			}
		}
		
		if ( $this->session->userdata('logintype') == 3  ) {        
		$this->db->where('provider',"".$this->session->userdata('username')."");
		}		
		$this->db->limit($limit,$start);
	  	$this->db->from('trunks');
		$query = $this->db->get();	
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row)
			{
				if ( $row['tech'] == "SIP" ) {						
						$path = explode("/", $row['path']);
						//list($profile,$dest)
// 						if (@$path[0] == "gateway") {
// 							$freeswitch_trunk = "sofia/gateway/" . @$path[1] . "/%";
// 						} else {
							$freeswitch_trunk = "%sofia/gateway/" . @$path[0] . "/%" . @$path[1];
// 						}
				 }
				
				//$this->db_fscdr =  $this->load->database('fscdr', TRUE);  
				$this->db_fscdr =  Common_model::$global_config['fscdr_db'];
				  $sql1 = "SELECT COUNT(*) AS calls, AVG(billsec) AS bs,"
							. " AVG( duration-billsec ) as acwt from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." WHERE lastapp IN('Dial','Bridge')"
							. " AND disposition IN ('ANSWERED','NORMAL_CLEARING')"
							. " and calldate >= '" . $sd. "' and calldate <= '" . $ed. "' and (dstchannel like '".$row['tech']."/".$row['path']."%'"
							. " or lastdata like '" . $freeswitch_trunk . "' ) ";
							
					$query1 = $this->db_fscdr->query($sql1);		
					$row1 = $query1->row_array();
// 				
// 				echo $sql1."<br/><br/>";
				 $sql2 = " select count(*) as ct from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where calldate >= '". $sd. "' AND calldate <= '" .$ed
							. "' AND disposition NOT IN('ANSWERED','16','NORMAL_CLEARING')"
							. " AND (dstchannel like '".$row['tech']."/".$row['path']."%'"
							. " or lastdata like '" . $freeswitch_trunk . "' )";
					
					$query2 = $this->db_fscdr->query($sql2);		
					$row3 = $row2 = $query2->row_array();			
// 				echo $sql2."<br/><br/>";
// 				 $sql3 = "SELECT count(*) as ct from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where calldate >= '".$sd. "' AND calldate <= '" .$ed
// 							. "' AND disposition IN ('CONGESTION','NORMAL_CIRCUIT_CONGESTION','SWITCH_CONGESTION')"
// 							. " AND (dstchannel like '".$row['tech']."/".$row['path']."%'"
// 							. " OR lastdata like '" . $freeswitch_trunk . "' )";
// 							
// 				
// 					$query3 = $this->db_fscdr->query($sql3);		
// 					$row3 = $query3->row_array();	
// echo $sql3."<br/><br/>";						
				 $sql4 = "SELECT COUNT(*) as ct from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where calldate >= '". $sd. "' AND calldate <= '" . $ed
							. "' AND (dstchannel like '".$row['tech']."/".$row['path']."%'"
							. " or lastdata like '" . $freeswitch_trunk . "' )";	
							
					$query4 = $this->db_fscdr->query($sql4);		
					$row4 = $query4->row_array();			
// 	echo $sql4."<br/><br/>";				exit;
					$success_rate    = 0;
					$congestion_rate = 0;
			
					if ( $row4['ct'] > 0 && $row1['calls'] > 0 ) {
						$success_rate = ( $row1['calls'] / $row4['ct'] ) * 100;
					}
					if ( $row4['ct'] > 0 && $row3['ct'] > 0 ) {
						$congestion_rate = ( $row3['ct'] / $row4['ct'] ) * 100;
					}
					
					
				$trunkstats[] = array('tech_path' => $row['tech']."/".$row['path'], 'ct' => $row4['ct'], 'bs' =>number_format($row1['bs'],2,'.',''), 'acwt' => number_format($row1['acwt'],2,'.',''), 'calls' => $row1['calls'], 'success_rate' => number_format($success_rate, 2), 'congestion_rate' => number_format($congestion_rate,2),'failed_calls'=>$row3['ct'] );
			
				 
			}
		}
		
		return $trunkstats;
		
	}
	
	
	function getViewCdrsCount($sd, $ed, $answered, $accountcode, $trunk)
	{
		$this->db_fscdr =  Common_model::$global_config['fscdr_db'];
		if ( $answered == 1 ) {
			$tmp =" SELECT * from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where disposition IN ('ANSWERED','NORMAL_CLEARING')"
				  . " and calldate >= '".$sd."' and calldate <= '".$ed."' ";				  
		}
		else {
			$tmp =" SELECT * from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where calldate >= '".$sd."' and calldate <= '".$ed."' ";				
		}
		
		if ( $accountcode!="" && $accountcode!=NULL && $this->session->userdata('logintype') == 2 ) {
			$tmp .= " and accountcode = '" .$accountcode. "' ";
		}
		
		 if ( $trunk!="" && $trunk!=NULL ) {
			$freeswitch_trunk = ""; 
			
			$tmpsql ="SELECT * FROM trunks WHERE name = '". $trunk. "' LIMIT 1";
			$query = $this->db->query($tmpsql);
			//if($query->num_rows()>0) {
			$row = $query->row_array();
			if ( @$row['tech'] == "SIP" ) {						
						$path = explode("/", @$row['path']);
						//list($profile,$dest)
// 						if (@$path[0] == "gateway") {
// 							$freeswitch_trunk = "sofia/gateway/" . @$path[1] . "/%";
// 						} else {
							$freeswitch_trunk = "$sofia/gateway/" . @$path[0] . "/%" . @$path[1];
// 						}
				 }
			//}
			
			$tmp .= "and (dstchannel like '".@$row['tech']."/".@$row['path']."%'"
							. " or dstchannel like '" . $freeswitch_trunk . "'"
							. "or dstchannel like '".@$row['tech'][@$row['path']]."%' ) ";	 
		 }
		 
		 //echo $tmp;
		 $query = $this->db_fscdr->query($tmp);
		 return $query->num_rows();
		
	}
	
	function getViewCdrsList($sd, $ed, $answered, $accountcode, $trunk, $start, $limit)
	{
		if ( $answered == 1 ) {
			$tmp =" SELECT * from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where disposition IN ('ANSWERED','NORMAL_CLEARING')"
				  . " and calldate >= '".$sd."' and calldate <= '".$ed."'  ORDER BY calldate DESC";				  
		}
		else {
			$tmp =" SELECT * from ".Common_model::$global_config['system_config']['freeswitch_cdr_table']." where calldate >= '".$sd."' and calldate <= '".$ed."'  ORDER BY calldate DESC";				
		}
		
		if ( $accountcode!="" && $accountcode!=NULL && $this->session->userdata('logintype') == 2 ) {
			$tmp .= " and accountcode = '" .$accountcode. "' ";
		}
		
		 if ( $trunk!="" && $trunk!=NULL ) {
			$freeswitch_trunk = ""; 
			
			$tmpsql ="SELECT * FROM trunks WHERE name = '". $trunk. "' LIMIT 1";
			$query = $this->db->query($tmpsql);
			$row = $query->row_array();
			if ( @$row['tech'] == "SIP" ) {						
						$path = explode("/", @$row['path']);
						//list($profile,$dest)
// 						if (@$path[0] == "gateway") {
// 							$freeswitch_trunk = "sofia/gateway/" . @$path[1] . "/%";
// 						} else {
							$freeswitch_trunk = "$sofia/gateway/" . @$path[0] . "/%" . @$path[1];
// 						}
				 }
							  	 
			$tmp .= "and (dstchannel like '".@$row['tech']."/".@$row['path']."%'"
							. " or dstchannel like '" . $freeswitch_trunk . "'"
							. "or dstchannel like '".@$row['tech'][@$row['path']]."%' ) ";	 
		 }
		 
		 $tmp .= " limit $start, $limit";
		 $this->db_fscdr =  Common_model::$global_config['fscdr_db'];
		 //$this->db_fscdr->limit($limit,$start);
		 $query = $this->db_fscdr->query($tmp);
		 //echo $this->db_fscdr->last_query();
		 return $query;
		 
	}
	
	
}
?>