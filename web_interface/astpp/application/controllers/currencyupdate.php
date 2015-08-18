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
class Currencyupdate extends CI_Controller {
    function __construct()
    {
        parent::__construct();
     //   if(!defined( 'CRON' ) )
       //   exit();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
    }
    function update_currency(){
        $where = array("currency <>"=> Common_model::$global_config['system_config']['base_currency']);
        $query = $this->db_model->getSelect("*", "currency", $where);

        if($query->num_rows >0){
            $currency_data =$query->result_array();
    		$url = "http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=";
	    	foreach ($currency_data as $currency_value) {
	    	    $url .= Common_model::$global_config['system_config']['base_currency'].$currency_value['currency'].'=X+';
	    	}
	    	$url .= '&f=l1';

	    	$sql='';
	    	$response = $this->curl_response($url);
	    	$content_data = explode(' ',$response);

	    	foreach ($content_data as $content_data1){
	    	   $currency_arr= explode("\n", $content_data1);
	    	    foreach($currency_arr as $final_val)
	    	    {
	    	        $currency_final = array();
	    		    $currency_final= explode(',', $final_val);
	    		    if(isset($currency_final[1]) && $currency_final[1] != "" && $currency_final[0]!='' && $currency_final[1] != 'N/A')
	    		    {
    	    		    $sql = "UPDATE currency SET currencyRate = ".$currency_final[1]." WHERE currency = '".substr($currency_final[0],4,3)."'";
    	    		    $this->db->query($sql);
	    		    }
	    	    }
	    	}
      }
	      $this->session->set_flashdata("astpp_errormsg", "Currency exchange rates successfully updated.");
	      redirect(base_url()."/systems/currency_list/");
	      exit;
	}
	function curl_response($url)
	{
		    $ch = curl_init();  // Initialising cURL
		    curl_setopt ( $ch, CURLOPT_URL, $url );
		    curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		    curl_setopt ( $ch, CURLOPT_ENCODING, "" );
		    curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)" );
		    $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
		    curl_close($ch);        // Closing cURL
		    return $data;
	}
}
?>

