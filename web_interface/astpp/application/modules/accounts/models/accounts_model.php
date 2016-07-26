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
class Accounts_model extends CI_Model {

    function Accounts_model() {
        parent::__construct();
    }

function add_account($accountinfo) {
        $logintype = $this->session->userdata('logintype');
        $accountinfo['reseller_id']=0;
        if ($logintype == 1 || $logintype == 5) {
            $accountinfo['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
        }
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->session->userdata("accountinfo");
             $account_data['id'];
        }
        $reseller_flag = '0';
        if (isset($accountinfo['account_by_reseller'])) {
            $reseller_flag = '1';
            unset($accountinfo['account_by_reseller']);
        }
        unset($accountinfo['action']);

        $sip_flag = '0';
        if (isset($accountinfo['sip_device_flag'])) {
            $sip_flag = '1';
            unset($accountinfo['sip_device_flag']);
        }
        $opensip_flag = '0';
        if (isset($accountinfo['opensips_device_flag'])) {
            $opensip_flag = '1';
            unset($accountinfo['opensips_device_flag']);
        }
        if(isset($accountinfo['tax_id'])){
            unset($accountinfo['tax_id']);
        }
        $accountinfo['creation']=gmdate('Y-m-d H:i:s');
 	$result = $this->db->insert('accounts', $accountinfo);
        $last_id = $this->db->insert_id();
        if ($reseller_flag == '1') {
            $reseller_array = array('name' => $accountinfo['number'], 'status' => '1', 'reseller_id' => $last_id);
            $result = $this->db->insert('pricelists', $reseller_array);
        }
        if ($sip_flag == '1') {
            $query = $this->db_model->select("*", "sip_profiles",array('name'=>"default"), "id", "ASC", '1', '0');
            $sip_id = $query->result_array();
            $free_switch_array = array('fs_username' => $accountinfo['number'],
                'fs_password' => $accountinfo['password'],
                'context' => 'default',
                'effective_caller_id_name' => $accountinfo['number'],
                'effective_caller_id_number' => $accountinfo['number'],
                'sip_profile_id' => $sip_id[0]['id'],
                'pricelist_id' => $accountinfo['pricelist_id'],
                'accountcode' => $last_id,
                'status' => $accountinfo['status']);
            $this->load->model('freeswitch/freeswitch_model');
            $this->freeswitch_model->add_freeswith($free_switch_array);
        }
        if($opensip_flag == 1){
            $opensips_array = array('username' => $accountinfo['number'],
		    'domain' => common_model::$global_config['system_config']['opensips_domain'],
                    'password'=>$accountinfo['password'],
                    'accountcode'=>$accountinfo['number'],
                    'pricelist_id'=>$accountinfo['pricelist_id']);
	    $this->load->model('opensips/opensips_model');
	    $this->opensips_model->add_opensipsdevices($opensips_array);
        }
        if ($accountinfo['type'] == '0') {
            $this->common->mail_to_users('email_add_user', $accountinfo);
        }
        return $last_id;
    }


    function edit_account($accountinfo, $edit_id) {
        unset($accountinfo['action']);
        $this->db->where('id', $edit_id);
        $result = $this->db->update('accounts', $accountinfo);
        return true;
    }
    function bulk_insert_accounts($add_array){
        $logintype = $this->session->userdata('logintype');
        $add_array['reseller_id']=0;
        $insert_array=array();
        $sip_device_array=array();
        if ($logintype == 1 || $logintype == 5) {
            $add_array['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
        }
        $creation_limit=$this->get_max_limit($add_array);        
        $count=$add_array['count'];
        $pin_flag=$add_array['pin'];
        unset($add_array['count']);
        unset($add_array['pin']);
        $balance=$add_array['balance'] !=  '' ? $add_array['balance'] :0.0000;
        $credit_limit=$add_array['credit_limit'] != '' ? $add_array['credit_limit'] : 0.0000;
        $prefix=$add_array['prefix'];
        $account_length=$add_array['account_length'];
        unset($add_array['account_length']);
        unset($add_array['prefix']);
        $length=strlen($prefix);
        if($length !=0){
	  $number_length=$account_length-$length;
	}
	else{
	  $number_length=$account_length;
	}
        
        $pricelist_id=$add_array['pricelist_id'] != ''  ? $add_array['pricelist_id'] : 0;
        $number= $this->common->find_uniq_rendno_accno($number_length, 'number', 'accounts',$prefix,$count);
        $password= $this->common->find_uniq_rendno_accno($number_length, 'password', 'accounts','',$count);
        if($pin_flag){
	  $pin= $this->common->find_uniq_rendno_accno($number_length, 'pin', 'accounts','',$count);
        }
        $sip_flag = false;
        if (isset($add_array['sip_device_flag'])) {
            $sip_flag = true;
            unset($add_array['sip_device_flag']);
        }
        if($sip_flag){
            $query = $this->db_model->select("*", "sip_profiles",array('name'=>"default"), "id", "ASC", '1', '0');
            $sip_id = $query->result_array();
            $sip_profile_id=$sip_id[0]['id'];
         for ($i = 0; $i < $count; $i++) {
            $acc_num=$number[$i];
            $current_password=$password[$i];
            $insert_array=array('number'=>$acc_num,
				      'password'=>$current_password,
				      'pricelist_id'=>$pricelist_id,
				      'reseller_id'=>$add_array['reseller_id'],
				      'status'=>0,
				      'credit_limit'=>$credit_limit,
				      'sweep_id'=>0,
				      'posttoexternal'=>$add_array['posttoexternal'],
				      'balance'=>$balance,
				      'currency_id'=>$add_array['currency_id'],
				      'country_id'=>$add_array['country_id'],
				      'timezone_id'=>$add_array['timezone_id'],
				      'company_name'=>$add_array['company_name'],
				      'invoice_day'=>0,
				      'first_name'=>$acc_num,
				      'type'=>0,
				      'validfordays'=>$add_array['validfordays']
				      );
            if($pin_flag == 1){
	      $insert_array['pin']=$pin[$i];
            }
             $this->db->insert('accounts',$insert_array);
             $last_id=$this->db->insert_id();
            $params_array = array('password' => $password[$i]);
	    $params_array_vars = array('effective_caller_id_name' => $acc_num,
				      'effective_caller_id_number' => $acc_num,
				      'user_context' => 'default');
            $sip_device_array[$i]=array('username'=>$acc_num,
					'accountid'=>$last_id,
					'sip_profile_id'=>$sip_profile_id,
					'dir_params'=>json_encode($params_array),
					'dir_vars'=>json_encode($params_array_vars),
					'status'=>0);
	  }
	  $this->db->insert_batch('sip_devices',$sip_device_array);
        }else{
         $opensip_flag = '0';
	 if (isset($add_array['opensips_device_flag'])) {
            $opensip_flag = '1';
            $opensips_domain=common_model::$global_config['system_config']['opensips_domain'];
            unset($add_array['opensips_device_flag']);
	 }
          for ($i = 0; $i < $count; $i++) {
            $acc_num=$number[$i];
            $current_password=$password[$i];
            $insert_array[$i]=array('number'=>$acc_num,
				      'password'=>$current_password,
				      'pricelist_id'=>$pricelist_id,
				      'reseller_id'=>$add_array['reseller_id'],
				      'status'=>0,
				      'credit_limit'=>$credit_limit,
				      'sweep_id'=>0,
				      'posttoexternal'=>$add_array['posttoexternal'],
				      'balance'=>$balance,
				      'currency_id'=>$add_array['currency_id'],
				      'country_id'=>$add_array['country_id'],
				      'timezone_id'=>$add_array['timezone_id'],
				      'company_name'=>$add_array['company_name'],
				      'invoice_day'=>0,
				      'first_name'=>$acc_num,
				      'type'=>0,
				      'validfordays'=>$add_array['validfordays']
				      );
            if($pin_flag == 1){
	      $insert_array[$i]['pin']=$pin[$i];
            }
            if($opensip_flag==1){
	      $opensips_array[$i] = array('username' => $acc_num,
		    'domain' => $opensips_domain,
                   'password'=>$current_password,
                   'accountcode'=>$acc_num,
                   'pricelist_id'=>$pricelist_id);
            }
        }
	  $this->db->insert_batch('accounts',$insert_array);
	  if($opensip_flag == 1){
	      $db_config = Common_model::$global_config['system_config'];
	      $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
	      $this->opensips_db = $this->load->database($opensipdsn, true);
	      $this->opensips_db = $this->load->database($opensipdsn, true);
	      $this->opensips_db->insert_batch("subscriber", $opensips_array);
	  }
        }
	return TRUE;
    }
    function get_max_limit($add_array){
            $this->db->where("length(number)",$add_array['account_length']);
            $this->db->like('number',$add_array['prefix'],'after');
            $this->db->select("count(id) as count");
            $this->db->from('accounts');
            $result=$this->db->get();
            $result=$result->result_array();
            $count=$result[0]['count'];
            $remaining_length =0;
            $remaining_length=$add_array['account_length']-strlen($add_array['prefix']);
            $currentlength =pow(5,$remaining_length);
            $currentlength=$currentlength-$count;
            return $currentlength;
    }
     /** code for fund transer */ 
    function account_process_payment($data) {
//        print_r($data);exit;
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $reseller = $accountinfo["id"];
        } else {
            $reseller = "-1";
        }
        $data["payment_by"] = $reseller;
        $data['accountid'] = $data['id'];
        $data['payment_mode'] = $data['payment_type'];
        unset($data['action']);
        unset($data['id']);
        unset($data['account_currency']);
        unset($data['payment_type']);
        
//         $this->db->insert('payments',$data);
        if (isset($data)) {
            $data['credit']=$data['credit'] =='' ?  0 : $data['credit'];
            $date = gmdate('Y-m-d H:i:s');
            
            $balance = $this->update_balance($data['credit'], $data['accountid'],$data['payment_mode']);
            
         //   if($data['posttoexternal'] == 1)
          //  {
           //    $data['credit'] = -1 * ($data['credit']);
           // }
            
            if($data['payment_mode'] == 0){
            		$insert_arr = array("accountid" => $data['accountid'],
				    "credit" => $data['credit'],
				    'payment_mode'=>$data['payment_mode'],
				    'type'=>"SYSTEM",
				    "notes" => $data['notes'],
				    "payment_date" => $date, 
				    'payment_by'=>$data['payment_by'],
				    );
// 				    print_r($insert_arr);exit;
			$this->db->insert("payments", $insert_arr);
            }
        }
        $accountinfo['email'] = '';
        $accountdata=$this->db->get('accounts',array('id'=>$data['accountid']));
         if($accountdata->num_rows()>0){
 	  $accountdata= $accountdata->result_array();
 	  $accountdata=$accountdata[0];
// 	  $this->common->mail_to_users('voip_account_refilled', $accountdata);
 	  return TRUE;
         }
    }

    function get_admin_Account_list($flag, $start = 0, $limit = 0,$reseller_id=0) {
	$this->db_model->build_search('admin_list_search');
        $where="reseller_id =".$reseller_id." AND deleted =0 AND type in (2,4,-1)";
        if($this->session->userdata('advance_search')== 1){
	    $search= $this->session->userdata('admin_list_search');
	    if($search['type'] == ''){
	      $this->db->where($where);
	      $this->db_model->build_search('admin_list_search');
	    }else{
	      $this->db->where('type',$search['type']);
	    }
	}else{
          $this->db->where($where);
	  $this->db_model->build_search('admin_list_search');
	}
        if ($flag) {
            $this->db->limit($limit, $start);
            $this->db->order_by('number','desc');
        }
        $result =$this->db->get('accounts');
        
        if($flag){
         return $result;
        }
        else{
	  return $result->num_rows();
        }
    }

    function get_subadmin_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('customer_list_search');
        $where = array("deleted" => "0", 'reseller_id' => "0",'type'=>4);
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

    function get_customer_Account_list($flag, $start = 0, $limit = 0,$export=false) {
        $this->db_model->build_search('customer_list_search');
        $reseller_flag=false;
        $where = array("deleted" => "0", 'reseller_id' => "0");
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller_flag =true;
            $where['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
        }
        $this->db->select('*');
        $this->db->where($where);
        if($this->session->userdata('advance_search')== 1){
	    $search= $this->session->userdata('customer_list_search');
	    if($search['type'] == ''){
	      $this->db->where('type','0');
	      if(!$reseller_flag){
		  $this->db->or_where('type',"3");
		  $this->db->where($where);
		  $this->db_model->build_search('customer_list_search');
	      }
	    }else{
	      $this->db->where('type',$search['type']);
	    }
	}else{
	  $this->db->where('type','0');
	  if(!$reseller_flag){
	      $this->db->or_where('type',"3");
	      $this->db->where($where);
	      $this->db_model->build_search('customer_list_search');
	  }
	}
        if ($flag) {
            if(!$export)
            $this->db->limit($limit, $start);
            $this->db->order_by('number','desc');
        }
        $result =$this->db->get('accounts');
        if($result->num_rows() > 0){
	  if($flag){
	    return $result;
	  }
	  else{
	    return $result->num_rows();
	  }
        }else{
        
         if($flag){
	      $result=(object)array('num_rows'=>0);
	  }
	  else{
	      $result=0;
	  }
	  return $result;
        }
    }

    function get_reseller_Account_list($flag, $start = 0, $limit = 0,$export=false) {
        $this->db_model->build_search('reseller_list_search');
        $where = array('reseller_id' => "0","deleted" => "0", "type" => "1");
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
        $where = array("deleted" => "0", "type" => "3",'reseller_id'=>0);
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
       // echo $this->db->last_query(); exit;
        return true;
    }

    function insert_block($data, $accountid) {
	 $data = explode(",", $data);
        $tmp = array();
        foreach ($data as $key => $data_value) {
            $tmp[$key]["accountid"] = $accountid;
                $result = $this->get_pattern_by_id($data_value);
                 $tmp[$key]["blocked_patterns"] = $result[0]['pattern'];
                 $tmp[$key]["destination"] = $result[0]['comment'];

        }
        return $this->db->insert_batch("block_patterns", $tmp);

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
        unset($data['action']);
        unset($data['flag']);
        if (isset($data['status'])) {
            $data['status'] = '1';
        } else {
            $data['status'] = '0';
        }
        $data['accountid'] = $this->common->get_field_name('id', 'accounts', array('number' => $data['accountid']));
        $this->db->insert('accounts_callerid', $data);
        return true;
    }

    function edit_callerid($data) {
        unset($data['action']);
        unset($data['flag']);
        if (isset($data['status'])) {
            $data['status'] = '1';
        } else {
            $data['status'] = '0';
        }

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

    function get_account_by_number($account_number) {
        $this->db->where("id", $account_number);
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

//        function account_process_payment($data) {
// //        print_r($data);exit;
//         if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
//             $accountinfo = $this->session->userdata['accountinfo'];
//             $reseller = $accountinfo["id"];
//         } else {
//             $reseller = "-1";
//         }
//         $data["payment_by"] = $reseller;
//         $data['accountid'] = $data['id'];
//         $data['payment_mode'] = $data['payment_type'];
//         unset($data['action']);
//         unset($data['id']);
//         unset($data['account_currency']);
//         unset($data['payment_type']);
// //         $this->db->insert('payments',$data);
//         if (isset($data)) {
//             $data['credit']=$data['credit'] =='' ?  0 : $data['credit'];
//             $date = gmdate('Y-m-d H:i:s');
//             $balance = $this->update_balance($data['credit'], $data['accountid'],$data['payment_mode']);
//             if($data['payment_mode'] == 0){
//             		$insert_arr = array("accountid" => $data['accountid'],
// 				    "credit" => $data['credit'],
// 				    'payment_mode'=>$data['payment_mode'],
// 				    'type'=>"SYSTEM",
// 				    "notes" => $data['notes'],
// 				    "payment_date" => $date, 
// 				    'payment_by'=>$data['payment_by'],
// 				    );
// // 				    print_r($insert_arr);exit;
// 		$this->db->insert("payments", $insert_arr);
//             }
//         }
//         $accountinfo['email'] = '';
//         $accountdata=$this->db->get('accounts',array('id'=>$data['accountid']));
//          if($accountdata->num_rows()>0){
//  	  $accountdata= $accountdata->result_array();
//  	  $accountdata=$accountdata[0];
// // 	  $this->common->mail_to_users('voip_account_refilled', $accountdata);
//  	  return TRUE;
//          }
//     }

    function update_balance($amount, $accountid, $payment_type) {
        if ($payment_type == 0) {
            $query = 'UPDATE `accounts` SET `balance` = (balance + ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        }if ($payment_type == 1){
            $query = 'UPDATE `accounts` SET `balance` = (balance - ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        }
    }
    function account_authentication($where_data,$id) {
        if($id != ""){
            $this->db->where("id <>",$id);
        }
        $this->db->where($where_data);
        $this->db->from("accounts");
        $query = $this->db->count_all_results();
        return $query;
    }
      function add_reseller_pricing($accountid, $did_id){
        $data = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
        $data = $data->result_array();
       
      // echo "<pre>";
        // print_r($this->session->userdata['accountinfo']); exit;
        
	  if($this->session->userdata['userlevel_logintype'] == -1)
		  {
		    $parent_id=0;
		  }else{
		    $parent_id=$this->session->userdata['accountinfo']['id'];
		  }
        
	  $insert_array=array("reseller_id"=>$accountid,'type' => '1',
            "monthlycost"=>$data[0]['monthlycost'],
            "setup"=>$data[0]['setup'],
            "cost"=>$data[0]['cost'],
            "inc"=>$data[0]['inc'],
            "parent_id"=>$parent_id,
            'extensions'=>$data[0]['inc'],
            'call_type'=>$data[0]['call_type'],
            "includedseconds"=>$data[0]['includedseconds'],
            "connectcost"=>$data[0]['connectcost'],
            "note"=>$data[0]['number'],
            "disconnectionfee"=>$data[0]['disconnectionfee'],
            "prorate"=>$data[0]['prorate'],
            'status' => '0'
	  );
	
        $result = $this->db->insert('reseller_pricing', $insert_array);
//        echo $this->db->last_query();exit;
        return true;
        
    }
         function get_animap($flag, $start, $limit,$id) {
	$where = array('accountid'=>$id);

        if ($flag) {
            $query = $this->db_model->select("*", "ani_map",$where, "number", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "ani_map",$where);
        }
        return $query;
    }
    function add_animap($data){
	$this->db->insert('ani_map', $data);
        return true;
    }
    function edit_animap($data,$id){
	$new_array=array('number'=>$data['number'],'status'=>$data['status']);
	$this->db->where('id',$id);
	$this->db->update('ani_map',$new_array);
	return true;
    }
    function remove_ani_map($id){
	$this->db->where('id',$id);
	$this->db->delete('ani_map');
	return true;
    }
    function animap_authentication($where_data,$id) {
        if($id != ""){
            $this->db->where("id <>",$id);
        }
        $this->db->where($where_data);
        $this->db->from("ani_map");
        $query = $this->db->count_all_results();
        return $query;
    }


}
