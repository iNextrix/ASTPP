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
        $where = array("posttoexternal"=>0,"notify_flag"=>1,"deleted" => "0","status"=>"0");
        $query = $this->db_model->getSelect("*", "accounts", $where);
        if($query->num_rows >0){
            $account_data = $query->result_array();
            foreach($account_data as $data_key =>$account_value){
                if(($account_value["balance"]) <= $account_value["notify_credit_limit"]){
                  $this->common->mail_to_users("email_low_balance",$account_value);   
                }
            }
        }
        exit;
    }
} 
?>
