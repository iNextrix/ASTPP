<?php

class Invoices_model extends CI_Model {

    function Invoices_model() {
        parent::__construct();
    }
    function getcharges_list($flag, $start = 0, $limit = 0) {
	$where = array();
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller = $this->session->userdata('username');
	    //$where = array("accountid"=>$reseller,"paid_status"=>"1");
	    $where = array("accountid"=>$reseller);
        } else {
	    //$where = array("paid_status"=>"1");
	  //$where = array("accountid"=>0);
        }
        if ($flag) {
            $query = $this->db_model->select("*","invoices",$where,"invoice_date","desc",$limit,$start);
        } else {
              $query = $this->db_model->countQuery("*","invoices",$where);
        }
        return $query;
    }
    
    function save_invoiceconf($post_array){
            if ($this->session->userdata('userlevel_logintype') == -1) {
            $accountid = -1;
        } else {
            $accountdata = $this->session->userdata('accountinfo');
            $accountid = $accountdata['accountid'];
        }
        unset($post_array['action']);
        $this->db->where('accountid',$accountid);
        $this->db->update('invoice_conf',$post_array);
        return true;
        }
        
         /**
     * -------Here we write code for model accounting functions get_invoiceconf------
     * this function get the invoice configration to generate pdf file.
     */
    function get_invoiceconf() {
        if ($this->session->userdata('userlevel_logintype') == -1) {
            $accountid = -1;
        } else {
            $accountdata = $this->session->userdata('accountinfo');
//            echo "<pre>";print_r($accountdata);
            $accountid = $accountdata['id'];
        }
        $return_array=array();
        $where=array('accountid'=> $accountid);
        
        $query = $this->db_model->getSelect("*","invoice_conf",$where);
        foreach($query->result_array() as $key => $value)
        {
            $return_array=$value;
        }
        
         return $return_array;
    }
    function getCdrs_invoice($invoiceid)
	{
		$this->db->where('invoiceid', $invoiceid);
		$this->db->from('cdrs');
		$query = $this->db->get();
		return $query;
	}
        function get_account_including_closed($accountdata)
	{
		$q = "SELECT * FROM accounts WHERE number = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		$q = "SELECT * FROM accounts WHERE accountid = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}

		return NULL;
	}

}
