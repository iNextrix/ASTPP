<?php
class UpdateBalance extends CI_Controller {
    function __construct()
    {
        parent::__construct();
//        if(!defined( 'CRON' ) )  
//          exit();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
        $this->load->library('fpdf');
        $this->load->library('pdf');
        
    }
    function GetUpdateBalance(){
        $day = date('d');
        $rawDate = date("Y-m-d");
        $week = date('N', strtotime($rawDate));
        
        $where = array("posttoexternal"=> 1,"deleted" => "0");
        $query = $this->db_model->getSelect("*", "accounts", $where);
        if($query->num_rows >0){
            $account_data = $query->result_array();
            foreach($account_data as $data_key =>$account_value){
                switch ($account_value["sweep_id"]) {
                    case '0':
                        $this->update_balace_daily($account_value);
                        break;
                    case '1':
                        if($week == $account_value["invoice_day"])
                            $this->update_balace_weekly($account_value);
                        break;
                    case '2':
                        if($day == $account_value["invoice_day"])
                            $this->update_balace_monthly($account_value);
                        break;
                    case '3':
                        if($day == $account_value["invoice_day"])
                            $this->update_balace_quarterly($account_value);
                        break;
                    case '4':
                        if($day == $account_value["invoice_day"])
                            $this->update_balace_semianually($account_value);
                        break;
                    case '5':
                        if($day == $account_value["invoice_day"])
                            $this->update_balace_anually($account_value);
                        break;
                }
            }
        }
    }
    function update_balace_daily($accountdata){
        
    }
    function update_balace_weekly($accountdata){
        
    }
    function update_balace_monthly($accountdata){
        
    }
    function update_balace_quarterly($accountdata){
        
    }
    function update_balace_semianually($accountdata){
        
    }
    function update_balace_anually($accountdata){
        
    }
}
?> 
