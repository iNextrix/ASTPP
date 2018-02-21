<?php
class Lowbalance extends CI_Controller {
    function __construct()
    {
	parent::__construct();
	if(!defined( 'CRON' ) )  
	  exit();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
    }
    function low_balance(){
        $where = array("posttoexternal"=> 0,"notify_flag"=>1,"deleted" => "0");
        $query = $this->db_model->getSelect("*", "accounts", $where);
        if($query->num_rows >0){
            $account_data = $query->result_array();
            foreach($account_data as $data_key =>$account_value){
                if(($account_value["balance"]*-1) <= $account_value["notify_credit_limit"]){
                  $this->common->mail_to_users("email_low_balance",$account_value);   
                }
            }
        }
        exit;
    }
} 
?>
