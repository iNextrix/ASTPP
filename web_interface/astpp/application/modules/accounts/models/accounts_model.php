<?php

###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# Samir Doshi <samir.doshi@inextrix.com>
# ASTPP Version 3.0 and above
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################

class Accounts_model extends CI_Model {

    function Accounts_model() {
        parent::__construct();
    }

    function add_account($accountinfo) {
	
        $account_data = $this->session->userdata("accountinfo");
        $accountinfo['reseller_id'] = ($account_data['type'] == 1 ) ? $account_data['id'] : 0;
        $accountinfo['maxchannels'] = ($accountinfo['type'] == 1 || $account_data['type'] == 2 || $account_data['type'] == -1 ) ? "0" : $accountinfo['maxchannels'];
        unset($accountinfo['action']);
        $sip_flag = isset($accountinfo['sip_device_flag']) ? 1 : 0;
        $opensip_flag = isset($accountinfo['opensips_device_flag']) ? 1 : 0;
        unset($accountinfo['sip_device_flag'],$accountinfo['opensips_device_flag'],$accountinfo['tax_id']);

        /*         * ******************************** */
        $accountinfo['creation'] = gmdate('Y-m-d H:i:s');
        $accountinfo['expiry'] = gmdate('Y-m-d H:i:s', strtotime('+20 years'));
        if(isset($accountinfo['is_recording'])){
	  $accountinfo['is_recording']=0;
        }else{
	  $accountinfo['is_recording']=1;
        }
        if(isset($accountinfo['allow_ip_management'])){
	  $accountinfo['allow_ip_management']=0;
        }else{
	  $accountinfo['allow_ip_management']=1;
        }
        if(isset($accountinfo['local_call'])){
	  $accountinfo['local_call']=0;
        }else{
	  $accountinfo['local_call']=1;
        }
        if ($accountinfo['type'] == 1){
        $invoice_config = $accountinfo['invoice_config_flag'];
		}else{
		$invoice_config = "";		
		}
		unset($accountinfo['invoice_config_flag']);
        $result = $this->db->insert('accounts', $accountinfo);
        $last_id = $this->db->insert_id();
                /**
          ASTPP  3.0 
          For Invoice Configuration
         * */ 
         if ($accountinfo['type'] == 1 && isset($invoice_config) && $invoice_config == "0") {
                if ($accountinfo['country_id'] == NULL) {
                    $accountinfo['country_id'] = "";
                } else {
                    $data = $this->db_model->getSelect("country", "countrycode", array("id" => $accountinfo['country_id']));
                    $data = $data->result_array();
                    $country_name = $data[0];
                }
                if ($accountinfo['postal_code'] == NULL) {
                    $accountinfo['postal_code'] = "";
                }

                $invoice_config = array('accountid' => $last_id, 'company_name' => $accountinfo['company_name'], 'address' => $accountinfo['address_1'], 'city' => $accountinfo['city'], 'province' => $accountinfo['province'], 'country' => $country_name['country'], 'zipcode' => $accountinfo['postal_code'], 'telephone' => $accountinfo['telephone_1'], 'emailaddress' => $accountinfo['email']);
                $this->db->where('account_id', $accountinfo['id']);
                $this->db->insert('invoice_conf', $invoice_config);
            }
        if ($sip_flag == '1') {
            $this->db->select('id');
            $this->db->where('name','default');
            $sipprofile_result=(array)$this->db->get('sip_profiles')->first_row();
            $free_switch_array = array('fs_username' => $accountinfo['number'],
                'fs_password' => $this->common->decode($accountinfo['password']),
                'context' => 'default',
                'effective_caller_id_name' => $accountinfo['number'],
                'effective_caller_id_number' => $accountinfo['number'],
                'sip_profile_id' => $sipprofile_result['id'],
                'pricelist_id' => $accountinfo['pricelist_id'],
                'accountcode' => $last_id,
                'status' => $accountinfo['status']);
            $this->load->model('freeswitch/freeswitch_model');
            $this->freeswitch_model->add_freeswith($free_switch_array);
        }
        if ($opensip_flag == 1) {
            $opensips_array = array('username' => $accountinfo['number'],
                'domain' => common_model::$global_config['system_config']['opensips_domain'],
                'password' => $accountinfo['password'],
                'accountcode' => $accountinfo['number'],
                'pricelist_id' => $accountinfo['pricelist_id']);
            $this->load->model('opensips/opensips_model');
            $this->opensips_model->add_opensipsdevices($opensips_array);
        }
        $accountinfo['confirm'] =  base_url();
        if($accountinfo['id'] == ""){
        $accountinfo['id'] = $last_id;
		}
        $accountinfo['password'] = $this->common->decode($accountinfo['password']);    

        $this->common->mail_to_users('email_add_user', $accountinfo);
        return $last_id;
    }

    /**
      ASTPP  3.0 
      Reseller Batch Update
     * */
    function reseller_rates_batch_update($update_array) {
        unset($update_array['action']);
        $update_array['type'] = 1;
        $date = gmdate("Y-m-d h:i:s");
        $this->db_model->build_search('reseller_list_search');
        if ($update_array['type'] == 1) {
            $this->db_model->build_batch_update_array($update_array);
            $login_type = $this->session->userdata('logintype');
            $reseller_info = $this->session->userdata['accountinfo'];
            if ($reseller_info['type'] == 1) {
                $this->db->where('reseller_id', $reseller_info['id']);
            } else {
                $this->db->where('reseller_id', '0');
            }
            $this->db->where('type', '1');
            $this->db->update("accounts");
            $this->db_model->build_search('reseller_list_search');
            if (isset($update_array['balance']['balance']) && $update_array['balance']['balance'] != '') {
                $search_flag = $this->db_model->build_search('reseller_list_search');
                $account_data = $this->session->userdata("accountinfo");
                if ($account_data['type'] == 1) {
                    $where = array('type' => 1, "balance" => $update_array['balance']['balance'], "reseller_id" => $account_data['id'], 'deleted' => '0', 'status' => '0');
                } else {
                    $where = array('type' => 1, "balance" => $update_array['balance']['balance'], 'deleted' => '0', 'status' => '0');
                }

                $this->db_model->build_search('reseller_list_search');
                $query_pricelist = $this->db_model->getSelect("id,reseller_id,balance", "accounts", $where);
                if ($query_pricelist->num_rows > 0) {
                    $description = '';
                    if ($update_array['balance']['operator'] == '2') {
                        $description .="Reseller update set balance by admin";
                    }
                    if ($update_array['balance']['operator'] == '3') {
                        $description .="Reseller update increase balance by admin";
                    }
                    if ($update_array['balance']['operator'] == '4') {
                        $description .="Reseller update descrise balance by admin";
                    }
                    foreach ($query_pricelist->result_array() as $key => $reseller_payment) {
                        if (!empty($reseller_payment['reseller_id']) && $reseller_payment['reseller_id'] != '') {
                            $payment_by = $reseller_payment['reseller_id'];
                        } else {
                            $payment_by = '-1';
                        }
                        $insert_arr = array("accountid" => $reseller_payment['id'],
                            "credit" => $update_array['balance']['balance'],
                            'payment_mode' => 0,
                            'type' => "SYSTEM",
                            "notes" => $description,
                            "payment_date" => $date,
                            'payment_by' => $payment_by,
                            'reseller_id' => $reseller_payment['reseller_id'],
                        );
                        $this->db->insert("payments", $insert_arr);
                    }
                }
            }
        }

        return true;
    }
    /**
      ASTPP  3.0 
      Customer Batch Update
     * */
    function customer_rates_batch_update($update_array) {

        unset($update_array['action']);
        $date = gmdate("Y-m-d h:i:s");
        $this->db_model->build_search('customer_list_search');
        $reseller_info = $this->session->userdata['accountinfo'];
        if ($reseller_info['type'] == 1) {
            $this->db->where('reseller_id', $reseller_info['id']);
        } else {
            $this->db->where('reseller_id', '0');
        }
        $this->db_model->build_search('customer_list_search');
        $this->db->where('type !=', '1');
        $this->db_model->build_batch_update_array($update_array);
        $this->db->update("accounts");
        if (isset($update_array['balance']['balance']) && $update_array['balance']['balance'] != '') {
            $account_data = $this->session->userdata("accountinfo");

            if ($account_data['type'] == 1) {
                $where = array('type' => 1, "reseller_id" => $account_data['id'], 'deleted' => '0', 'status' => '0');
            } else {
                $where = array('type !=' => '-1', "balance" => $update_array['balance']['balance'], 'deleted' => '0', 'status' => '0');
            }

            $this->db_model->build_search('customer_list_search');
            $query_pricelist = $this->db_model->getSelect("id,reseller_id,balance", "accounts", $where);
            if ($query_pricelist->num_rows > 0) {
                $description = '';
                if ($update_array['balance']['operator'] == '2') {
                    $description .="Customer update set balance by admin";
                }
                if ($update_array['balance']['operator'] == '3') {
                    $description .="Customer update increase balance by admin";
                }
                if ($update_array['balance']['operator'] == '4') {
                    $description .="Customer update descrise balance by admin";
                }
                foreach ($query_pricelist->result_array() as $key => $customer_payment) {
                    if (!empty($customer_payment['reseller_id']) && $customer_payment['reseller_id'] != '0') {
                        $payment_by = $customer_payment['reseller_id'];
                    } else {
                        $payment_by = '-1';
                    }
                    $insert_arr = array("accountid" => $customer_payment['id'],
                        "credit" => $update_array['balance']['balance'],
                        'payment_mode' => 0,
                        'type' => "SYSTEM",
                        "notes" => $description,
                        "payment_date" => $date,
                        'payment_by' => $payment_by,
                        'reseller_id' => $customer_payment['reseller_id'],
                    );
                    $this->db->insert("payments", $insert_arr);
                }
            }
        }
        return true;
    }

    /*     * ************************************************************************ */

    function edit_account($accountinfo, $edit_id) {
        unset($accountinfo['action']);
        unset($accountinfo['onoffswitch']);
        $this->db->where('id', $edit_id);
        $result = $this->db->update('accounts', $accountinfo);
        return true;
    }

    function bulk_insert_accounts($add_array) {
        $logintype = $this->session->userdata('logintype');
        $creation_limit = $this->get_max_limit($add_array);
        $count = $add_array['count'];
        $pin_flag = $add_array['pin'];
        
        $balance = $add_array['balance'] != '' ? $add_array['balance'] : 0.0000;
        $credit_limit = $add_array['credit_limit'] != '' ? $add_array['credit_limit'] : 0.0000;
        $prefix = $add_array['prefix'];
        $account_length = $add_array['account_length'];
        
        $length = strlen($prefix);
        if ($length != 0) {
            $number_length = $account_length - $length;
        } else {
            $number_length = $account_length;
        }

        $pricelist_id = $add_array['pricelist_id'] != '' ? $add_array['pricelist_id'] : 0;
        $number = $this->common->find_uniq_rendno_accno($number_length, 'number', 'accounts', $prefix, $count);
        $password = $this->common->find_uniq_rendno_accno($number_length, 'password', 'accounts', '', $count);
        if ($pin_flag) {
            $pin = $this->common->find_uniq_rendno_accno($number_length, 'pin', 'accounts', '', $count);
        }
        $sip_flag = false;
        $opensip_flag=false;
        if (isset($add_array['sip_device_flag']) && common_model::$global_config['system_config']['opensips']== 0) {
            $sip_flag = true;
        }
        if (isset($add_array['opensips_device_flag']) && common_model::$global_config['system_config']['opensips']== 1) {
            $opensip_flag = true;
        }
        unset(
                $add_array['count'],
                $add_array['pin'],
                $add_array['account_length'],
                $add_array['prefix'],
                $add_array['sip_device_flag'],
                $add_array['opensips_device_flag']
        );
        if(isset($add_array['is_recording']) &&  $add_array['is_recording'] != ''){
        $is_recording=1;
        }else{
        $is_recording=0;
        }
        if(isset($add_array['allow_ip_management']) &&  $add_array['allow_ip_management'] != ''){
        $allow_ip_management=1;
        }else{
        $allow_ip_management=0;
        }
        if(isset($add_array['local_call']) &&  $add_array['local_call'] != ''){
        $local_call=1;
        }else{
        $local_call=0;
        }
        if ($sip_flag) {
            $query = $this->db_model->select("*", "sip_profiles", array('status' => "0"), "id", "ASC", '1', '0');
            $sip_id = $query->result_array();
            $sip_profile_id = $sip_id[0]['id'];
            for ($i = 0; $i < $count; $i++) {
                $acc_num = $number[$i];
                $current_password = $password[$i];
                $insert_array = array('number' => $acc_num,
                    'password' => $this->common->encode($current_password),
                    'pricelist_id' => $pricelist_id,
                    'reseller_id' => $add_array['reseller_id'],
                    'status' => 0,
                    'credit_limit' => $credit_limit,
                    'posttoexternal' => $add_array['posttoexternal'],
                    'balance' => $balance,
                    'currency_id' => $add_array['currency_id'],
                    'country_id' => $add_array['country_id'],
                    'timezone_id' => $add_array['timezone_id'],
                    'company_name' => $add_array['company_name'],
                    'first_name' => $acc_num,
                    'type' => 0,
                    'charge_per_min'=>$add_array['charge_per_min'],
                    'validfordays' => $add_array['validfordays'],
                    "creation"=>gmdate("Y-m-d H:i:s"),
                    "maxchannels"=>0,
                    "sweep_id"=>$add_array['sweep_id'],
                    "local_call"=>$local_call,
                     "invoice_day"=>$add_array['invoice_day'],
                    "allow_ip_management"=>$allow_ip_management,
                    "is_recording"=>$is_recording,
                    "expiry" => gmdate('Y-m-d H:i:s', strtotime('+10 years'))
                );
                if ($pin_flag == 1) {
                    $insert_array['pin'] = $pin[$i];
                }
                $this->db->insert('accounts', $insert_array);
                $last_id = $this->db->insert_id();
                $params_array = array('password' => $password[$i],
                    "vm-enabled"=>"true",
                    "vm-password"=>"",
                    "vm-mailto"=>"",
                    "vm-attach-file"=>"true",
                    "vm-keep-local-after-email"=>"true",
                    "vm-email-all-messages"=>"true"
                    );
                $params_array_vars = array('effective_caller_id_name' => $acc_num,
                    'effective_caller_id_number' => $acc_num,
                    'user_context' => 'default');
                $sip_device_array[$i] = array('username' => $acc_num,
                    'sip_profile_id' => $sip_profile_id,
                    'reseller_id' => $add_array['reseller_id'],
                    'accountid' => $last_id,
                    'dir_params' => json_encode($params_array),
                    'dir_vars' => json_encode($params_array_vars),
                    'status' => 0,
                    'creation_date' => gmdate("Y-m-d H:i:s")
                    );
            }
            $this->db->insert_batch('sip_devices', $sip_device_array);
        } else {
            for ($i = 0; $i < $count; $i++) {
                $acc_num = $number[$i];
                $current_password = $password[$i];
                $insert_array[$i] = array('number' => $acc_num,
                    'password' => $this->common->encode($current_password),
                    'pricelist_id' => $pricelist_id,
                    'reseller_id' => $add_array['reseller_id'],
                    'status' => 0,
                    'credit_limit' => $credit_limit,
                    'sweep_id' => 0,
                    'posttoexternal' => $add_array['posttoexternal'],
                    'balance' => $balance,
                    'currency_id' => $add_array['currency_id'],
                    'country_id' => $add_array['country_id'],
                    'timezone_id' => $add_array['timezone_id'],
                    'company_name' => $add_array['company_name'],
                    'invoice_day' => 0,
                    'first_name' => $acc_num,
                    'type' => 0,
                    'validfordays' => $add_array['validfordays'],
                    "creation"=>gmdate("Y-m-d H:i:s"),
                    "is_recording"=>0,
                    "maxchannels"=>0,
                    "sweep_id"=>2,
                    "invoice_day"=>gmdate("d"),
                    "expiry" => gmdate('Y-m-d H:i:s', strtotime('+10 years'))
                    
                );
                if ($pin_flag == 1) {
                    $insert_array[$i]['pin'] = $pin[$i];
                }
                if ($opensip_flag) {
                    $opensips_domain = common_model::$global_config['system_config']['opensips_domain'];
                    $opensips_array[$i] = array('username' => $acc_num,
                        'domain' => $opensips_domain,
                        'password' => $current_password,
                        'accountcode' => $acc_num,
                        'reseller_id' => $add_array['reseller_id'],
                        "creation_date" => gmdate("Y-m-d H:i:s"),
                        "status"=>0
                        );
                }
            }
            $this->db->insert_batch('accounts', $insert_array);
            if ($opensip_flag == 1) {
                $db_config = Common_model::$global_config['system_config'];
                $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
                $this->opensips_db = $this->load->database($opensipdsn, true);
                $this->opensips_db->insert_batch("subscriber", $opensips_array);
            }
        }
        return TRUE;
    }

    function get_max_limit($add_array) {
		$this->db->where('deleted','0');
        $this->db->where("length(number)", $add_array['account_length']);
        $this->db->like('number', $add_array['prefix'], 'after');
        $this->db->select("count(id) as count");
        $this->db->from('accounts');
        $result = $this->db->get();
        $result = $result->result_array();
        $count = $result[0]['count'];
        $remaining_length = 0;
        $remaining_length = $add_array['account_length'] - strlen($add_array['prefix']);
        $currentlength = pow(10, $remaining_length);
        $currentlength = $currentlength - $count;
        return $currentlength;
    }
    function account_process_payment($data) {
	$data['accountid'] = $data['id'];
        $accountdata=(array)$this->db->get_where('accounts',array("id"=>$data['accountid']))->first_row();
        $accountinfo =$this->session->userdata('accountinfo');
	$data["payment_by"] = $accountdata['reseller_id'] > 0 ? $accountdata['reseller_id'] : '-1';
	$data['payment_mode'] = $data['payment_type'];
	unset($data['action'],$data['id'],$data['account_currency'],$data['payment_type']);
	if (isset($data) && !empty($accountdata)) {
		$data['credit']=$data['credit'] =='' ?  0 : $data['credit'];
		$date = gmdate('Y-m-d H:i:s');
		if($data['payment_mode']== 1){       
		  $balance = $this->update_balance($data['credit'], $data['accountid'],$data['payment_mode']);
			  $insert_arr = array("accountid" => $data['accountid'],
			  "credit" => "-".$data['credit'],
			  'payment_mode'=>$data['payment_mode'],
			  'type'=>"SYSTEM",
			  "notes" => $data['notes'],
			  "payment_date" => $date, 
			  'payment_by'=>$data['payment_by'],
		  );
		  $this->db->insert("payments", $insert_arr);
		}else{ 
		  $balance = $this->update_balance($data['credit'], $data['accountid'],$data['payment_mode']);
		  $insert_arr = array("accountid" => $data['accountid'],
			  "credit" => $data['credit'],
			  'payment_mode'=>$data['payment_mode'],
			  'type'=>"SYSTEM",
			  "notes" => $data['notes'],
			  "payment_date" => $date, 
			  'payment_by'=>$data['payment_by'],
		  );
		  $this->db->insert("payments", $insert_arr);
		  $accountdata['refill_amount']=$data['credit'];
		  $current_id=$accountinfo['type'] ==1 ? $accountinfo['id'] : '0';
		  if($accountdata['reseller_id'] == $current_id){
		    $this->common->mail_to_users('voip_account_refilled', $accountdata);
		  }else{
		    $this->common->mail_to_users('voip_child_account_refilled', $accountdata);
		  }
		}
	}
    }
/****************Completed******************/

    function get_admin_Account_list($flag, $start = 0, $limit = 0, $reseller_id = 0) {
        $this->db_model->build_search('admin_list_search');
        $where = "reseller_id =" . $reseller_id . " AND deleted =0 AND type in (2,4,-1)";
        if ($this->session->userdata('advance_search') == 1) {
            $search = $this->session->userdata('admin_list_search');
            if ($search['type'] == '') {
                $this->db->where($where);
                $this->db_model->build_search('admin_list_search');
            } else {
                $this->db->where('type', $search['type']);
            }
        } else {
            $this->db->where($where);
            $this->db_model->build_search('admin_list_search');
        }
        if ($flag) {
            $this->db->limit($limit, $start);
        }
        if (isset($_GET['sortname']) && $_GET['sortname'] != 'undefined'){
          $this->db->order_by($_GET['sortname'], ($_GET['sortorder']=='undefined')?'desc':$_GET['sortorder']);
        }else{
            $this->db->order_by('number','desc');
        }
        $result = $this->db->get('accounts');

        if ($flag) {
            return $result;
        } else {
            return $result->num_rows();
        }
    }

    function get_customer_Account_list($flag, $start = 0, $limit = 0, $export = false) {
        $this->db_model->build_search('customer_list_search');
        $accountinfo = $this->session->userdata("accountinfo");
        $reseller_id = $accountinfo['type'] == 1 ? $accountinfo['id'] : 0;
        $where = array("deleted" => "0", 'reseller_id' => $reseller_id);
        $this->db->select('*');
        $type = "type IN (0,3)";
        $this->db->where($where);
        if ($this->session->userdata('advance_search') == 1) {
            $search = $this->session->userdata('customer_list_search');
            if ($search['type'] != '0' && $search['type'] != '3') {
                $this->db->where($type);
            }
        } else {
            $this->db->where($type);
        }
        if ($flag) {
            if (!$export)
                $this->db->limit($limit, $start);
        }
        if (isset($_GET['sortname']) && $_GET['sortname'] != 'undefined'){
          $this->db->order_by($_GET['sortname'], ($_GET['sortorder']=='undefined')?'desc':$_GET['sortorder']);
        }else{
            $this->db->order_by('number','desc');
        }
        $result = $this->db->get('accounts');
        if ($flag) {
            return $result;
        } else {
            return $result->num_rows();
        }
    }

    function get_reseller_Account_list($flag, $start = 0, $limit = 0, $export = false) {
        $this->db_model->build_search('reseller_list_search');
        $where = array('reseller_id' => "0", "deleted" => "0", "type" => "1");
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $where['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function get_provider_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('provider_list_search');
        $where = array("deleted" => "0", "type" => "3", 'reseller_id' => 0);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $where['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function remove_customer($id) {
        $this->db->where("id", $id);
        $this->db->where("type <>", "-1");
        $data = array('deleted' => '1');
        $this->db->update("accounts", $data);
        return true;
    }

    function insert_block($data, $accountid) {
        $data = explode(",", $data);
        $tmp = array();
        if (!empty($data)) {
            foreach ($data as $key => $data_value) {
                $tmp[$key]["accountid"] = $accountid;
                $result = $this->get_pattern_by_id($data_value);
                $tmp[$key]["blocked_patterns"] = $result[0]['pattern'];
                $tmp[$key]["destination"] = $result[0]['comment'];
            }
            return $this->db->insert_batch("block_patterns", $tmp);
        }
    }

    function get_pattern_by_id($pattern) {
        $patterns = $this->db_model->getSelect("pattern,comment", "routes", array("id" => $pattern));
        $patterns_value = $patterns->result_array();
        return $patterns_value;
    }

    function get_callerid($account_id) {
        $query = $this->db_model->getSelect("*", "accounts_callerid", array("accountid" => $account_id));
        return $query;
    }

    function get_account_number($accountid) {
        $query = $this->db_model->getSelect("number", "accounts", array("id" => $accountid));
        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function add_callerid($data) {
        unset($data['action'],$data['flag']);
        $data['accountid'] = $this->common->get_field_name('id', 'accounts', array('number' => $data['accountid']));
        $this->db->insert('accounts_callerid', $data);
        return true;
    }

    function edit_callerid($data) {
        unset($data['action']);
        unset($data['flag']);
        $data['accountid'] = $this->common->get_field_name('id', 'accounts', array('number' => $data['accountid']));
        $this->db->where('accountid', $data['accountid']);
        $this->db->update('accounts_callerid', $data);
        return true;
    }

    /**
     * -------Here we write code for model accounting functions remove_all_account_tax------
     * for remove all account's taxes enteries from database.
     */
    function remove_all_account_tax($account_tax) {
        $this->db->where('accountid', $account_tax);
        $this->db->delete('taxes_to_accounts');
        return true;
    }

    /**
     * -------Here we write code for model accounting functions add_account_tax------
     * this function use to insert data for add taxes to account.
     */
    function add_account_tax($data) {
        $this->db->insert('taxes_to_accounts', $data);
    }

    /**
     * -------Here we write code for model accounting functions get_accounttax_by_id------
     * this function use get the account taxes details as per account number
     * @account_id = account id
     */
    function get_accounttax_by_id($account_id) {
        $this->db->where("accountid", trim($account_id));
        $query = $this->db->get("taxes_to_accounts");
        if ($query->num_rows() > 0)
            return $query->result_array();
        else
            return false;
    }

    /**
     * -------Here we write code for model accounting functions check_account_num------
     * this function write to verify the account number is valid or not.
     * @acc_num = account number
     */
    function check_account_num($acc_num) {
        $this->db->select('accountid');
        $this->db->where("number", $acc_num);
        $query = $this->db->get("accounts");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function get_account_by_number($id) {
        $this->db->where("id", $id);
        $query = $this->db->get("accounts");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function get_currency_by_id($currency_id) {

        $query = $this->db_model->getSelect("*", 'currency', array('id' => $currency_id));
        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }


    function update_balance($amount, $accountid, $payment_type) {
        if ($payment_type == 0) {
            $query = 'UPDATE `accounts` SET `balance` = (balance + ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        }if ($payment_type == 1) {
            $query = 'UPDATE `accounts` SET `balance` = (balance - ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        }
    }

    function account_authentication($where_data, $id) {
        if ($id != "") {
            $this->db->where("id <>", $id);
        }
        $this->db->where($where_data);
        $this->db->from("accounts");
        $query = $this->db->count_all_results();
        return $query;
    }

    function get_animap($flag, $start, $limit, $id) {
        $where = array('accountid' => $id);

        if ($flag) {
            $query = $this->db_model->select("*", "ani_map", $where, "number", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "ani_map", $where);
        }
        return $query;
    }

    function add_animap($data) {
        $this->db->insert('ani_map', $data);
        return true;
    }

    function edit_animap($data, $id) {
        $new_array = array('number' => $data['number'], 'status' => $data['status']);
        $this->db->where('id', $id);
        $this->db->update('ani_map', $new_array);
        return true;
    }

    function remove_ani_map($id) {
        $this->db->where('id', $id);
        $this->db->delete('ani_map');
        return true;
    }

    function animap_authentication($where_data, $id) {
        if ($id != "") {
            $this->db->where("id <>", $id);
        }
        $this->db->where($where_data);
        $this->db->from("ani_map");
        $query = $this->db->count_all_results();
        return $query;
    }
    function add_invoice_config($add_array) {
        $result = $this->db->insert('invoice_conf', $add_array);
        return true;
    }

    function edit_invoice_config($add_array, $edit_id) {
        $this->db->where('id', $edit_id);
        $result = $this->db->update('invoice_conf', $add_array);
        return true;
    }

}
