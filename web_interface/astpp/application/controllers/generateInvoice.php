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

class GenerateInvoice extends MX_Controller {
    function __construct()
    {
        parent::__construct();
/*        if(!defined( 'CRON' ) )  
          exit();*/
        $this->load->model("db_model");
        $this->load->library("astpp/common");
        $this->load->library('fpdf');
        $this->load->library('pdf');
        ini_set("memory_limit","2048M");
	ini_set("max_execution_time","259200");
    }
    function getInvoiceData(){ 
        $where = array("posttoexternal"=> 1,"deleted" => "0","status"=>"0");
        $query = $this->db_model->getSelect("*", "accounts", $where);
        if($query->num_rows >0){
            $account_data = $query->result_array();
//             print_r($account_data);exit;
            foreach($account_data as $data_key =>$account_value){
//echo "Billing Cycle :".$account_value['sweep_id']."\n";
               switch ($account_value['sweep_id']) {
                case 0:
                        $this->Generate_Daily_invoice($account_value);
                case 2:
                        $this->Generate_Monthly_invoice($account_value);                     
               }
            }
        }
        exit;
    }
    
    function validate_invoice_date($account_value)
    {
        $last_invoice_date = $this->get_invoice_date($account_value["id"]);
        $last_invoice_date = ($last_invoice_date)?$last_invoice_date:$account_value['creation'];
        return $last_invoice_date;
    }

    function Generate_Daily_invoice($account_value){
        $start_date = $this->validate_invoice_date($account_value);
        $end_date = gmdate("Y-m-d H:i:s");
        $this->process_invoice($start_date,$end_date,$account_value);        
    }
    
    function Generate_Monthly_invoice($account_value){
        $start_date = $this->validate_invoice_date($account_value);    
        $end_date = gmdate("Y-m-d H:i:s");
        $days_between = gmdate('d');
        if($days_between == $account_value['invoice_day']){
	        $this->process_invoice($start_date,$end_date,$account_value);
        }
    }
    function process_invoice($start_date,$end_date,$accountdata)
    {
        $invoice_data_count = 0;
        $sort_order = 1;
	$invoice_data_count = $this->count_invoice_data($accountdata['id'],$start_date,$end_date);
	if($invoice_data_count > 0){
	    $invoiceid = $this->create_invoice($accountdata['id'],$start_date,$end_date);
	    $update_cdrs = $this->update_cdrs_data($accountdata['id'],$start_date,$end_date,$invoiceid);
	    $sub_total = $this->count_invoice_subtotal($accountdata['id'],$invoiceid);
	    $sort_order = $this->insert_invoice_total_data($invoiceid,$sub_total,$sort_order);
	    $sort_order = $this->apply_invoice_taxes($invoiceid,$accountdata['id'],$sort_order);
	    $invoice_total = $this->set_invoice_total($invoiceid,$sort_order);
//	    $this->download_invoice($invoiceid,$accountdata);
	}else{
	    $invoiceid = $this->create_invoice($accountdata['id'],$start_date,$end_date);
	    $sort_order = $this->insert_invoice_total_data($invoiceid,"0.0000",$sort_order);
	    $sort_order = $this->apply_invoice_taxes($invoiceid,$accountdata['id'],$sort_order);
	    $invoice_total = $this->set_invoice_total($invoiceid,$sort_order);
	}
    }
    
   
    function get_invoice_date($accountid){
        $where = array("accountid"=>$accountid,'type'=>"I");
        $query = $this->db_model->getSelect("invoice_date", "invoices", $where);
        if($query->num_rows >0){
            $invoiceid = $query->result();
            return $invoiceid[0]->invoice_date;
        }
        return false;
    }
    function count_invoice_data($accountid,$start_date="",$end_date=""){
        $cdr_query = ""; $inv_data_query ="";
        $cdr_query = "select count(uniqueid) as count from cdrs where accountid = ".$accountid;
        $inv_data_query = "select count(id) as count from invoice_item where accountid=".$accountid;
        
	$cdr_query .= " AND callstart >='".$start_date."' AND callstart <= '".$end_date."' AND invoiceid=0";
	$inv_data_query .= " AND created_date >='".$start_date."' AND created_date <= '".$end_date."'  AND invoiceid=0";
	    
        $query = $cdr_query." UNION ".$inv_data_query;	
        
        $invoice_data = $this->db->query($query);
        if($invoice_data->num_rows > 0){
            $invoice_data = $invoice_data->result_array();
            foreach($invoice_data as $data_key => $data_value){
                if($data_value['count'] > 0){
                    return $data_value['count'];
                }
            }
        }
        return "0";
    }
    function update_cdrs_data($accountid,$start_date="",$end_date="",$invoiceid){
        $cdr_query = ""; $inv_data_query ="";
        $cdr_query = "Update cdrs SET invoiceid = '".$invoiceid."' where accountid = ".$accountid;
        $inv_data_query = "update invoice_item SET invoiceid = '".$invoiceid."' where accountid=".$accountid;
        
	$cdr_query .= " AND callstart >='".$start_date."' AND callstart <= '".$end_date."' AND invoiceid=0";
	$inv_data_query .= " AND created_date >='".$start_date."' AND created_date <= '".$end_date."'  AND invoiceid=0";

        $cdr_data = $this->db->query($cdr_query);
        $invoice_data = $this->db->query($inv_data_query);
        return true;
    }
    
    function create_invoice($accountid,$from_date,$to_date){
        $due_date = gmdate("Y-m-d H:i:s",strtotime($to_date." +1 week"));
        $invoice_data = array("accountid"=>$accountid,"invoice_date"=>$to_date,
                            "from_date"=>$from_date,"to_date"=>$to_date,
                            "status"=>0);
        $this->db->insert("invoices",$invoice_data);
        $invoiceid = $this->db->insert_id();
        return $invoiceid;
    }
    function count_invoice_subtotal($accountid,$invoiceid){
        $total=0;
	
	$subtotal_query = "select SUM(invoice_item.debit) as invoice_debit , SUM(invoice_item.credit) as invoice_credit from invoice_item WHERE invoice_item.invoiceid = ".$invoiceid;
	
        $subtotal_data = $this->db->query($subtotal_query);
        $subtotal_data = $subtotal_data->result_array();
        foreach($subtotal_data as $subtotal_key =>$subtotal_value){
            //$total = ($subtotal_value['cdrs_debit']+$subtotal_value['cdrs_credit'])-($subtotal_value['cust_debit'] + $subtotal_value['cust_credit']);
	  $total += ($subtotal_value['invoice_debit'] - $subtotal_value['invoice_credit']);
        }
	$invoice_item_total = $total; 
	
	$subtotal_query = "select SUM(cdrs.debit) as cdrs_debit , SUM(cdrs.cost) as cdrs_credit  from cdrs WHERE cdrs.invoiceid = ".$invoiceid;	
        $subtotal_data = $this->db->query($subtotal_query);
        $subtotal_data = $subtotal_data->result_array();
        foreach($subtotal_data as $subtotal_key =>$subtotal_value){
            //$total = ($subtotal_value['cdrs_debit']+$subtotal_value['cdrs_credit'])-($subtotal_value['cust_debit'] + $subtotal_value['cust_credit']);
	  $total += $subtotal_value['cdrs_debit'];
        }                        
        return $total;
    }
    
    function insert_invoice_total_data($invoiceid,$sub_total,$sort_order){
        $invoice_total_arr = array("invoiceid"=>$invoiceid,"sort_order"=>$sort_order,
            "value"=>$sub_total, "title"=>"Sub Total","text"=>"Sub Total","class"=>"1");
        $this->db->insert("invoices_total",$invoice_total_arr);
        return $sort_order++;
    }
    
    function apply_invoice_taxes($invoiceid,$accountid,$sort_order){
        $tax_priority="";
        $where = array("accountid"=>$accountid);
        $accounttax_query = $this->db_model->getSelectWithOrder("*", "taxes_to_accounts", $where,"ASC","taxes_priority");
        if($accounttax_query->num_rows > 0){
            $accounttax_query = $accounttax_query->result_array();
            foreach($accounttax_query as $tax_key => $tax_value){ 
            $taxes_info=$this->db->get_where('taxes',array('id'=>$tax_value['taxes_id']));
            if($taxes_info->num_rows() > 0 ){
		$tax_value=$taxes_info->result_array();
		$tax_value= $tax_value[0];
                 if($tax_value["taxes_priority"] == ""){
                     $tax_priority = $tax_value["taxes_priority"];
                 }else if($tax_value["taxes_priority"] > $tax_priority){
                     $query = $this->db_model->getSelect("SUM(value) as total", "invoices_total", array("invoiceid"=> $invoiceid));
                     $query =  $query->result_array();
                     $sub_total = $query["0"]["total"];
                 }
                $tax_total = (($sub_total * ( $tax_value['taxes_rate'] / 100 )) + $tax_value['taxes_amount'] );
                $tax_array = array("invoiceid"=>$invoiceid,"title"=>"TAX","text"=>$tax_value['taxes_description'],
                    "value"=>$tax_total,"class"=>"2","sort_order"=>$sort_order);
                $this->db->insert("invoices_total",$tax_array);
                $sort_order++;
            }
            }
        }
        return $sort_order;
    }
    
    function set_invoice_total($invoiceid,$sort_order){
        $query = $this->db_model->getSelect("SUM(value) as total", "invoices_total", array("invoiceid"=> $invoiceid));
        $query =  $query->result_array();
        $sub_total = $query["0"]["total"];
        
        $invoice_total_arr = array("invoiceid"=>$invoiceid,"sort_order"=>$sort_order,
            "value"=>$sub_total,"title"=>"Total","text"=>"Total","class"=>"9");
        $this->db->insert("invoices_total",$invoice_total_arr);
        return true;
    }        

}?> 
