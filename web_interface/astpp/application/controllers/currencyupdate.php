<?php
// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// Hacked about to use 1forge.com by Alex Heylin <alex@alexheylin.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################


class Currencyupdate extends CI_Controller {
	function __construct() {
		parent::__construct ();
		// if(!defined( 'CRON' ) )
		// exit();
		$this->load->model ( "db_model" );
		$this->load->library ( "astpp/common" );
	}

    function update_currency() {
        $apiKey = Common_model::$global_config ['system_config'] ['currency_conv_api_key'];
        if (Common_model::$global_config ['system_config'] ['currency_conv_api_key'] != ""
            && Common_model::$global_config ['system_config'] ['currency_conv_api_key'] != "YourKeyGoesHere"
        ) {
            $baseCurrency = Common_model::$global_config ['system_config'] ['base_currency'];
            $url = "https://forex.1forge.com/1.0.3/symbols?api_key=".$apiKey."";
            $symbolsData = $this->curl_response ( $url );
            $symbolsData = json_decode($symbolsData);
            $symbolString = "" ;
            foreach ($symbolsData as $symbol) {
                if ( substr($symbol,0, 3) == $baseCurrency ) {
                    $curQry = "select currency from astpp.currency WHERE currency REGEXP '[A-Z][A-Z][A-Z]';";
                    $curData = $this->db->query($curQry);
                    if ($curData->num_rows() > 0) {
                        $currency_arr = $curData->result_array();
                        foreach ( $currency_arr  as $currency ) {
                            foreach ($currency  as $key => $value ) {
                                if (strtolower($value) == strtolower(substr($symbol, 3, 3))) {
                                    if ($symbolString == "") {
                                        $symbolString = $symbol;
                                    } else {
                                        $symbolString = $symbolString . "," . $symbol;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $url = "https://forex.1forge.com/1.0.3/quotes?pairs=".$symbolString."&api_key=".$apiKey."";
            $quotesData = $this->curl_response ( $url );
            $quotesData = json_decode($quotesData);
            $debugstring = "";
            foreach ($quotesData as $quote) {
                $debugstring = $debugstring . "A," ;
                if (isset(Common_model::$global_config ['system_config'] ['currency_conv_loss_pct'])
                    && (Common_model::$global_config ['system_config'] ['currency_conv_loss_pct'] > 0 )
                    && (Common_model::$global_config ['system_config'] ['currency_conv_loss_pct'] < 100)
                ) {
                    $debugstring = $debugstring . "B," ;
                    $value = ($quote->price * (1 - ( Common_model::$global_config ['system_config'] ['currency_conv_loss_pct'] / 100)));
                } else {
                    $debugstring = $debugstring . "C," ;
                    $value = $quote->price;
                }
                $curSymbol = strtoupper(substr($quote->symbol,3, 3));
                $debugstring = $debugstring . "D_" . $curSymbol . ":" . $value . "," ;
                $sql = "UPDATE currency SET currencyrate = '".$value."',last_updated = now() WHERE currency = '" . $curSymbol . "'\n";
                $debugstring = $debugstring . $sql ;
                $this->db->query ( $sql );
            }
            $updatebasecurrency = "UPDATE currency SET currencyrate = '1.000',last_updated = now() WHERE currency = '" . Common_model::$global_config ['system_config'] ['base_currency'] . "'";
            $this->db->query ( $updatebasecurrency );
            $this->session->set_flashdata ( "astpp_errormsg", "Currency exchange rates successfully updated." );

            redirect ( base_url () . "/systems/currency_list/" );
        } else {
            $this->session->set_flashdata ( "astpp_notification", "You must enter your API key for 1forge.com." );
            redirect ( base_url () . "/systems/configuration/global" );
        }
        exit;
    }

	
	/**
	 *
	 * @param string $url        	
	 */
	function curl_response($url) {
		$ch = curl_init (); // Initialising cURL
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_ENCODING, "" );
		curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)" );
		$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
		curl_close ( $ch ); // Closing cURL
		return $data;
	}
}

?>
