<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
class Reports_model extends CI_Model {

    function Reports_model() {
        parent::__construct();
    }

    function getcustomer_cdrs_list($flag, $start, $limit,$export =true) {
	$start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
        $this->db_model->build_search('customer_cdr_list_search');
        if($this->session->userdata('advance_search') != 1){
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date,'type'=>'0');
	    } else {
		$where = array('reseller_id' => '0','callstart >= '=>$start_date,'callstart <='=>$end_date);
	    }
        }
        else{
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id'],'type'=>'0');
	    } else {
		$where = array("reseller_id" => "0");
	    }
        }
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs", $where, "callstart", "DESC", $limit, $start);
//echo $this->db->last_query();
//exit;
            
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }
        return $query;
    }
     function users_cdrs_list($flag,$accountid,$entity_type,$start,$limit) {
        
	$where = array('callstart >= '=>date('Y-m-d 00:00:00'),"callstart <= "=>date('Y-m-d 23:59:59') );
	$account_type= $entity_type =='provider' ? 'provider_id' :'accountid';
	$where[$account_type]= $accountid;
	$table=$entity_type=='reseller'?'reseller_cdrs' : 'cdrs';
        if ($flag) {
            $query = $this->db_model->select("*", $table, $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*",$table, $where);
        }
        return $query;

    }
    function getreseller_list($flag, $start, $limit) {
        $this->db_model->build_search('reseller_cdr_list_search');
        $start_date=gmdate("Y-m-d")." 00:00:01";
	$end_date=gmdate("Y-m-d")." 23:59:59";
	if($this->session->userdata('advance_search') != 1){
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id'],"accountid <>"=>$account_data['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date);
	    } else {
		$where = array("reseller_id" => "0",'callstart >= '=>$start_date,'callstart <='=>$end_date);
	    }
        }
        else{
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id'],"accountid <>"=>$account_data['id']);
	    } else {
		$where = array("reseller_id" => "0");
	    }
        }
        if ($flag) {
            $query = $this->db_model->select("*", "reseller_cdrs", $where, "callstart", "DESC", $limit, $start);
  //          echo $this->db->last_query();
//exit;
        } else {
            $query = $this->db_model->countQuery("*", "reseller_cdrs", $where);
        }
        
        return $query;
    }
    
    function getprovider_list($flag, $start, $limit) {
        $this->db_model->build_search('provider_cdr_list_search');
        $start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
	if($this->session->userdata('advance_search') != 1){
	  if ($this->session->userdata('logintype') == 3) {
	      $account_data = $this->session->userdata("accountinfo");
	      $where = array("provider_id"=>$account_data['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date);
	  }
	  else{
	      $where = array('callstart >= '=>$start_date,'callstart <='=>$end_date);
	  }
        }
        else{
	    if ($this->session->userdata('logintype') == 3) {
	      $account_data = $this->session->userdata("accountinfo");
	      $where = array("provider_id"=>$account_data['id']);
	    }
	    else{
	      $where=array();
	    }
        }
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs",$where, "callstart", "DESC", $limit, $start);
          //  echo $this->db->last_query();
		//exit;
        } else {
            $query = $this->db_model->countQuery("*", "cdrs",$where);
        }
        return $query;
    }

    function getReseller($username = "", $type) {
        $reseller = "";
        if ($username != "") {
            $reseller = "reseller_id = '" . $username . "' AND";
        }
        $q = "SELECT * FROM accounts WHERE  " . $reseller . " type IN ('" . $type . "')";

        $query = $this->db->query($q);

        $options = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $options[] = $row['number'];
            }
        }
        return $options;
    }

    function getDestination($username = "") {
        $notes = "";
        if ($username != "") {
            $notes = "WHERE notes LIKE '" . $username . " %'";
        }
        $q = "SELECT DISTINCT notes,pattern FROM cdrs " . $notes . " ";

        $query = $this->db->query($q);

        $options = array();
        $dst = array();
        $ptn = array();

        if ($query->num_rows() > 0) {
            $i = 0;
            foreach ($query->result_array() as $row) {

                $notes = $row['notes'];
                $note = preg_split('/(\^|DID:)/', $notes, 2);
                $caret_sign = "";
                if (isset($note[1])) {
                    $caret_sign = "^";
                }
                $pos = strpos($notes, "DID:");
                if ($pos) {
                    $caret_sign = 'DID:';
                }

                //$ptn[$i] =  @$caret_sign.@$note[1] . @$note[2];				
                $ptn[$i] = $row['pattern'];
                $note = preg_split('/\|/', $note[0]);
                $dst[$i] = (@$note == 1) ? @$note[0] : ( (@$note[0] != "") ? @$note[1] : "");
                $i++;
            }
        }


        return array('1' => array_unique($dst), '2' => array_unique($ptn));
    }

    function getCardNum($reseller, $table, $start, $limit, $name) {
        $admin_reseller_report = array();
        $q = "SELECT DISTINCT accountid AS '" . $name . "' FROM $table";

        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {

                $bth = @$row['' . $name . ''];
                //$bth = $this->session->userdata('username');

                $sql1 = "SELECT notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                        . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                        . " SUM(debit) AS cost, SUM(cost) AS price FROM "
                        . $table . " WHERE (notes IS NOT NULL AND notes != '') AND accountid = '" . $bth . "' GROUP BY notes";
                $query1 = $this->db->query($sql1);
                //echo $query1->num_rows();
                if ($query1->num_rows() > 0) {
                    foreach ($query1->result_array() as $row1) {
                        $note1 = preg_split('/(\^|DID:)/', $row1['notes'], 2);
                        $caret_sign = "";
                        if (isset($note1[1])) {
                            $caret_sign = "^";
                        }
                        $pos = strpos($row1['notes'], "DID:");
                        if ($pos) {
                            $caret_sign = 'DID:';
                        }

                        $idd = $caret_sign . @$note1[1] . @$note1[2];
                        // $note1 = explode( "[|.-]", @$note1[0] );
                        $note1 = preg_split('/\|/', @$note1[0]);
                        $dst = ( @$note1[0] == 1 ) ? @$note1[0] : @$note1[1];
                        if ($dst == "")
                            $dst = $row1['pattern'];
                        $atmpt = $row1['attempts'];
                        $acd = $row1['acd'];
                        $mcd = $row1['mcd'];
                        $bill = $row1['billable'];
                        $price = $row1['price'];
                        $cost = $row1['cost'];

                        $notes = $row1['notes'];

                        $notes = "notes = '" . $row1['notes'] . "' ";

                        $sql2 = "SELECT COUNT(*) AS completed FROM $table WHERE $notes AND disposition IN ('ANSWERED','NORMAL_CLEARING')";
// 							echo $sql2."<br/>";
                        $query2 = $this->db->query($sql2);
                        $row2 = $query2->row_array();
                        $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;

                        $asr = ( ( $atmpt - $cmplt ) / $atmpt ) * 100;

                        $in = "";
                        $sql3 = "SELECT uniqueid FROM $table WHERE $notes ";
                        $query3 = $this->db->query($sql3);
                        foreach ($query3->result_array() as $row3) {
                            if ($row3['uniqueid'])
                                $in .= "'" . $row3['uniqueid'] . "',";
                        }

                        if (strlen($in) > 0)
                            $in = substr($in, 0, -1);

//                        $this->db_fscdr = Common_model::$global_config['fscdr_db'];
//                        $sql4 = "SELECT SUM(duration) AS actual FROM " . Common_model::$global_config['system_config']['freeswitch_cdr_table'] . " WHERE uniqueid IN ($in)";
//                        $query4 = $this->db_fscdr->query($sql4);
                        $sql4 = "SELECT SUM(billseconds) AS actual FROM cdrs WHERE uniqueid IN ($in)";                        
                        $query4 = $this->db->query($sql4);
                        $row4 = $query4->row_array();

                        $act = $row4['actual'];

                        $act = (int) ( $act / 60 ) . ":" . ( $act % 60 );
                        $acd = (int) ( $acd / 60 ) . ":" . ( $acd % 60 );
                        $mcd = (int) ( $mcd / 60 ) . ":" . ( $mcd % 60 );
                        $bill = (int) ( $bill / 60 ) . ":" . ( $bill % 60 );
                        $price = $price / 1;
                        $cost = $cost / 1;


                        $admin_reseller_report[] = array('bth' => $bth, 'dst' => $dst, 'idd' => $idd, 'atmpt' => $atmpt, 'cmplt' => $cmplt, 'asr' => round($asr, 2), 'acd' => $acd, 'mcd' => $mcd, 'act' => $act, 'bill' => $bill, 'price' => $price, 'cost' => $cost);
                    }
                }
            }
        }
        $sth = mysql_query("DROP TEMPORARY TABLE $table");
        //$sth = $this->db->query("DROP VIEW $table");
        return $admin_reseller_report;
    }

    function getcustomercdrs($flag, $start = 0, $limit = 0, $export = true) {
    $start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
        if($this->session->userdata('advance_search') != 1){
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date);
	    } else {
		$where = array('reseller_id' => '0','callstart >= '=>$start_date,'callstart <='=>$end_date);
	    }
        }
        else{
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$account_data = $this->session->userdata("accountinfo");
		$where = array("reseller_id" => $account_data['id']);
	    } else {
		$where = array("reseller_id" => "0","type"=> "0");
	    }
        }
        
        $this->db_model->build_search('customer_cdr_list_search');
        $this->db->where($where);
        $this->db->from('cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {

            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getresellercdrs($flag, $start = 0, $limit = 0, $export = true) {
        
        $start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
	$accountinfo = $this->session->userdata('accountinfo');
        $reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	if($this->session->userdata('advance_search') != 1){
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
		$where = array("reseller_id" => $reseller_id,"accountid <>"=>$reseller_id,'callstart >= '=>$start_date,'callstart <='=>$end_date);
	    } else {
		$where = array("reseller_id" => $reseller_id,'callstart >= '=>$start_date,'callstart <='=>$end_date);
	    }
        }
        else{
	    if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {

		$where = array("reseller_id" => $reseller_id,"accountid <>"=>$reseller_id);
	    } else {
		$where = array("reseller_id" => $reseller_id);
	    }
        }
        $this->db_model->build_search('reseller_cdr_list_search');
        $this->db->where($where);
        $this->db->from('reseller_cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getprovidercdrs($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('provider_cdr_list_search');
        $start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
	if($this->session->userdata('advance_search') != 1){
	  if ($this->session->userdata('logintype') == 3) {
	      $account_data = $this->session->userdata("accountinfo");
	      $where = array("accountid"=>$account_data['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date,"type"=>"1");
	  }
	  else{
	      $where = array('callstart >= '=>$start_date,'callstart <='=>$end_date,"type"=>"1");
	  }
        }
        else{
	    if ($this->session->userdata('logintype') == 3) {
	      $account_data = $this->session->userdata("accountinfo");
	      $where = array("accountid"=>$account_data['id']);
	    }
	    else{
	      $where=array();
	    }
        }
        $this->db->where($where);
        $this->db->from('cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getuser_cdrs_list($flag, $start, $limit, $accountid = "") {
	$start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
	$accountinfo = $this->session->userdata("accountinfo");
        if($this->session->userdata('advance_search') != 1){
		$where = array('accountid' =>$accountinfo['id'],'callstart >= '=>$start_date,'callstart <='=>$end_date);
        }
        else{
		$where = array("accountid" =>$accountinfo['id']);
        }
        $this->db_model->build_search('user_cdrs_report_search');
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs", $where, "callstart", "DESC", $limit, $start);
           // echo $this->db->last_query(); exit;
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }

        return $query;
    }

    function getuser_payment_list($flag, $start, $limit) {
        $this->db_model->build_search('cdr_payment_search');
        $account_data = $this->session->userdata("accountinfo");
        $this->db_model->build_search('customer_cdr_list_search');
        $where = array("accountid" => $account_data["id"]);
        if ($flag) {
            $query = $this->db_model->select("*", "payments", $where, "payment_date", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "payments", $where);
        }

        return $query;
    }

    function getcustomer_payment_list($flag, $start, $limit) {
        $this->db_model->build_search('cdr_payment_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $where = array("payment_by"=>$accountinfo["id"]);
        } else {
            $where = array("payment_by"=>"-1");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "payments", $where, "payment_date", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "payments", $where);
        }

        return $query;
    }
    function getcdrs_list($flag, $start, $limit, $accountid = "") {
        $start_date=date("Y-m-d")." 00:00:01";
	$end_date=date("Y-m-d")." 23:59:59";
	$this->db->limit(100);
        if ($accountid == "") {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("accountid" => $account_data["id"],'callstart >= '=>$start_date,'callstart <='=>$end_date);
        } else {
            $where = array("accountid" => $accountid,'callstart >= '=>$start_date,'callstart <='=>$end_date);
        }
        $this->db_model->build_search('customer_cdr_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }
        return $query;
    }
    function getreseller_commission_list($flag, $start, $limit) {
        $this->db_model->build_search('reseller_commission_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $reseller_id = $accountinfo["id"];
        } else {
            $reseller_id = "0";
        }
        if ($flag) {
            $query = $this->db_model->select_by_in("*", "commission","" , "date", "DESC", $limit, $start,"","reseller_id",$reseller_id);

        } else {
            $query = $this->db_model->countQuery_by_in("*", "commission", "","reseller_id",$reseller_id);
        }

        return $query;
    }

function get_resellersummary_report($flag,$start=0,$limit=0){
       
       $this->db_model->build_search('resellersummary_reports_search');
       // $where =$this->resellersummary_report_search();
$reseller_id = $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ? $this->session->userdata['accountinfo']['id'] :0;
       if($this->session->userdata('advance_search') != 1){
                $where = array('reseller_id' =>$reseller_id,'callstart >= '=>date('Y-m-d')." 00:00:01",'callstart <='=>date("Y-m-d")." 23:59:59");
       }
       else{
                $where = array("reseller_id" =>$reseller_id);
       }
       $this->db->where($where);
        if($flag){
            $result=$this->db_model->select("accountid,uniqueid,notes,pattern, COUNT(*) AS attempts, (CASE WHEN disposition IN (('SUCCESS'),('NORMAL_CLEARING')) THEN AVG(billseconds) ELSE 0 END) AS acd,MAX(billseconds) AS mcd, SUM(billseconds) AS billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(debit) AS debit, SUM(cost) AS cost,sum(debit-cost) as profit", "reseller_cdrs",'' , "callstart", "DESC",$limit,$start,'pattern,accountid');
        }
        else{
            $this->db->order_by('callstart','desc');
	    $this->db->group_by("pattern,accountid"); 
            $result = $this->db_model->getSelect("count(*) as total_count","reseller_cdrs",$where);
            $result=$result->num_rows();
        }
        return $result;
        
    }
    function get_providersummary_report_list($flag,$start=0,$limit=0,$export=false){
        $this->db_model->build_search('providersummary_reports_search');
        $this->db->where("provider_id > ","0");
        if($flag){
if(!$export){
	    
	 $result=$this->db_model->select("provider_id,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(case when billseconds > 0 then billseconds end) AS acd,MAX(billseconds) AS mcd, SUM(billseconds) AS billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(cost) AS cost, SUM(provider_call_cost) AS price", "cdrs",'' , "callstart", "DESC",$limit,$start,'pattern,provider_id');

        }
else
{

 $result=$this->db_model->select("provider_id,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(case when billseconds > 0 then billseconds end) AS acd,MAX(billseconds) AS mcd, SUM(billseconds) AS billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(cost) AS cost, SUM(provider_call_cost) AS price", "cdrs",'' , "callstart", "DESC",'','','pattern,provider_id');

}
     }   else{
	    $this->db->order_by('provider_id','desc');
	    $this->db->group_by("pattern","provider_id"); 
            $result = $this->db_model->getSelect("count(*) as total_count","cdrs",'');
            $result=$result->num_rows();
        }
        return $result;
    }

function get_customersummary_report_list($flag,$start=0,$limit=0){
       $this->db_model->build_search('customersummary_reports_search');
       $reseller_id = $this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5 ? $this->session->userdata['accountinfo']['id'] :0;
       if($this->session->userdata('advance_search') != 1){
		$where = array('reseller_id' =>$reseller_id,'callstart >= '=>date('Y-m-d')." 00:00:01",'callstart <='=>date("Y-m-d")." 23:59:59","type"=>0);
       }
       else{
		$where = array("reseller_id" =>$reseller_id,"type"=>0);
       }
       $this->db->where($where);   
       if($flag){
               $result=$this->db_model->select("accountid,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,MAX(billseconds) AS mcd,SUM(billseconds) AS billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(debit) AS cost,SUM(cost) AS price", "cdrs",'' , "callstart", "DESC",$limit,$start,'pattern,accountid');
//             $result=$this->db->query($query);
       }
       else{
//             $query = "SELECT count(*) as total_count FROM cdrs $where GROUP BY pattern,accountid order by accountid";
//             $result=$this->db->query($query);
		$this->db->order_by('accountid','desc');
		$this->db->group_by("pattern,accountid"); 
		$result = $this->db_model->getSelect("count(*) as total_count","cdrs",'');
		$result=$result->num_rows();
       }
//    echo $this->db->last_query();
       return $result;
    }

}
