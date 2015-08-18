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

class Common_model extends CI_Model {

    var $host;
    var $user;
    var $pass;
    var $db2;
    var $db_link;
    var $conn = false;
    public static $global_config;

    function Common_model() {
        parent::__construct();
        $this->get_system_config();
        $this->get_currencylist();
        $this->get_language_list();
        $this->get_country_list();
        $this->get_admin_info();
//        $this->get_user_levels_list();
//        $this->get_sweep_list();
//            $this->CI = & get_instance();
        $this->load->library('astpp/common');
        $this->db->query('SET time_zone = "+00:00"');
    }

    function get_language_list() {
        $query = $this->db->get("language");
        $result = $query->result_array();
        $language_list = array();
        foreach ($result as $row) {
            $language_list[$row['language']] = $row['languagename'];
        }
        self::$global_config['language_list'] = $language_list;
        return $language_list;
    }
    function get_system_config() {
        $query = $this->db->get("system");
        $config = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $config[$row['name']] = $row['value'];
        }
        self::$global_config['system_config'] = $config;
        return $config;
    }
    function get_country_list() {
        $query = $this->db->get("countrycode");
        $result = $query->result_array();
        $country_list = array();
        foreach ($result as $row) {
            $country_list[$row['id']] = $row['country'];
        }
        self::$global_config['country_list'] = $country_list;
        return $country_list;
    }
    
        //Return list of currency with rate
 function get_currencylist() {
        $query = $this->db->get("currency");
        $currencylist = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $currencylist[$row['currency']] = $row['currencyrate'];
        }
        self::$global_config['currency_list'] = $currencylist;
        return $currencylist;
    }
    
    function get_admin_info(){
         $result=$this->db->get_where('accounts',array('type'=>'-1'));
         $result=$result->result_array();
         self::$global_config['admin_info'] = $result[0];
         return $result[0];
    }
    
     /**============ From below code developed for ASTPP version 2.0 ======================================**/
     function generate_receipt($accountid,$amount){
        $invoice_data = array("accountid"=>$accountid,"invoice_date"=>gmdate("Y-m-d H:i:s"),
                            "from_date"=>gmdate("Y-m-d H:i:s"),"to_date"=>gmdate("Y-m-d H:i:s"),"type"=>'R');
        $this->db->insert("invoices",$invoice_data);
        $invoiceid = $this->db->insert_id();    
        $sort_order = 0;
        $sort_order = $this->insert_invoice_total_data($invoiceid,$amount,$sort_order);
        $sort_order = $this->apply_invoice_taxes($invoiceid,$accountid,$sort_order);
        $invoice_total = $this->set_invoice_total($invoiceid,$sort_order);   
        return  $invoiceid;     
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
                    $tax_value=$tax_value['0'];
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

    function calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        $from_currency = ($from_currency == '') ? self::$global_config['system_config']['base_currency'] : $from_currency;
        
        if ($to_currency == '') {
/*            if ($this->session->userdata['userlevel_logintype'] == -1 && $to_currency == '') {
                $to_currency = self::$global_config['system_config']['base_currency'];
            } else {*/
                $to_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $to_currency = $this->common->get_field_name('currency', 'currency', $to_currency1);
//            }
        }
//echo $to_currency; exit;        
        $from_cur_rate = (self::$global_config['currency_list'][$from_currency] > 0)?self::$global_config['currency_list'][$from_currency]:1;
        $to_cur_rate = (self::$global_config['currency_list'][$to_currency])?self::$global_config['currency_list'][$to_currency]:1;
        $cal_amount = ($amount * $to_cur_rate) / $from_cur_rate;
        if ($format_currency)
            $cal_amount = $this->format_currency($cal_amount);
        if ($append_currency)
            $cal_amount = $cal_amount . " " . $to_currency;
        return $cal_amount;
    }

    function add_calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        if ($from_currency == '') {
/*            if ($this->session->userdata['userlevel_logintype'] == -1 && $from_currency == '') {
                $from_currency = self::$global_config['system_config']['base_currency'];
            } else {*/
                $from_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $from_currency = $this->common->get_field_name('currency', 'currency', $from_currency1);
//            }
        }
        $to_currency = ($to_currency == '') ? self::$global_config['system_config']['base_currency'] : $to_currency;
        if(self::$global_config['currency_list'][$from_currency] > 0){
	  $cal_amount = ($amount * self::$global_config['currency_list'][$to_currency]) / self::$global_config['currency_list'][$from_currency];
	}else{
	  $cal_amount=$amount;
	}
        if ($format_currency)
            $cal_amount = $this->format_currency($cal_amount);
        if ($append_currency)
            $cal_amount = $cal_amount . " " . $to_currency;
        return $cal_amount;
    }

    function to_calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        if ($to_currency == '') {
/*            if ($this->session->userdata['userlevel_logintype'] == -1 && $to_currency == '') {
                $to_currency = self::$global_config['system_config']['base_currency'];
            } else {*/
                $to_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $to_currency = $this->common->get_field_name('currency', 'currency', $to_currency1);
//            }
        }
        $from_currency = ($from_currency == '') ? self::$global_config['system_config']['base_currency'] : $from_currency;

        $from_cur_rate = (self::$global_config['currency_list'][$from_currency] > 0)?self::$global_config['currency_list'][$from_currency]:1;
        $to_cur_rate = (self::$global_config['currency_list'][$to_currency])?self::$global_config['currency_list'][$to_currency]:1;
        
        $cal_amount = ($amount * $to_cur_rate) / $from_cur_rate;
        if ($format_currency)
            $cal_amount = $this->format_currency($cal_amount);
        if ($append_currency)
            $cal_amount = $cal_amount . " " . $to_currency;
        return $cal_amount;
    }

    function format_currency($amount) {
        return money_format('%.' . Common_model::$global_config['system_config']['decimalpoints'] . 'n', $amount);
    }

    function money_format($format, $number) {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
                '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                        $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                        $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int) $fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int) $fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int) $fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                        $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                                STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }

    function get_list_taxes() {
        $this->db->select('id,taxes_description');
        $query = $this->db->get("taxes");
        $taxesList = array();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    function get_params($table_name, $select, $where) {
        if (is_array($select)) {
            
        } else {
            $this->db->select($select);
        }
        if (is_array($where)) {
            
        } else {
            $this->db->where($where);
        }
        $query = $this->db->get($table_name);
        $query = $query->result();
        return $query;
    }

    
}
