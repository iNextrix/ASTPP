<?php

class Accounts_model extends CI_Model {

    function Accounts_model() {
        parent::__construct();
    }

    function add_account($accountinfo) {
        $logintype = $this->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $accountinfo['reseller_id'] = $account_data['id'];
        } else {
            $accountinfo['reseller_id'] = "0";
        }
        $reseller_flag = '0';
        if (isset($accountinfo['account_by_reseller'])) {
            $reseller_flag = '1';
            unset($accountinfo['account_by_reseller']);
        }
        unset($accountinfo['action']);

        $sip_flag = '0';
        if (isset($accountinfo['SIP'])) {
            $sip_flag = '1';
            unset($accountinfo['SIP']);
        }
        $result = $this->db->insert('accounts', $accountinfo);
        $last_id = $this->db->insert_id();
        if ($reseller_flag == '1') {
            //create one default pricelist for reseller

            $reseller_array = array('name' => $accountinfo['number'], 'status' => '1', 'reseller_id' => $last_id);
            $result = $this->db->insert('pricelists', $reseller_array);
        }
        if ($sip_flag == '1') {
            $query = $this->db_model->select("*", "sip_profiles", '', "id", "ASC", '1', '0');

            $sip_id = $query->result_array();
            $free_switch_array = array('fs_username' => $this->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], '', ''),
                'fs_password' => $this->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], '', ''),
                'vm_password' => $this->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], '', ''),
                'context' => 'default',
                'effective_caller_id_name' => '',
                'effective_caller_id_number' => '',
                'sip_profile_id' => $sip_id[0]['id'],
                'pricelist_id' => $accountinfo['pricelist_id'],
                'accountcode' => $last_id);

            $this->load->model('freeswitch/freeswitch_model');
            $this->freeswitch_model->add_freeswith($free_switch_array);
        }
        if ($accountinfo['type'] == '0') {
            $this->common->mail_to_users('email_add_user', $accountinfo);
        }
        if ($result) {
            $purpose = 'Account Setup';
            $this->insert_cdrdata($accountinfo, $purpose);
        }
        return true;
    }

    function edit_account($accountinfo, $edit_id) {
        unset($accountinfo['action']);
        $this->db->where('id', $edit_id);
        $result = $this->db->update('accounts', $accountinfo);
        return true;
    }

    function insert_cdrdata($accountinfo, $purpose) {
        $date = date('Y-m-d H:i:s');
        $data = array('accountid' => $accountinfo['number'],
            'callednum' => $purpose,
            'credit' => '0',
            'callstart' => $date);
        $this->db->insert('cdrs', $data);
        return true;
    }

    function get_admin_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('admin_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller = $this->session->userdata('username');
            $where = array('reseller_id' => $reseller, "deleted" => "0", "type" => "-1");
        } else {
            $where = array("deleted" => "0", "type" => "2");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function get_subadmin_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('subadmin_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller = $this->session->userdata('username');
            $where = array('reseller_id' => $reseller, "deleted" => "0", "type" => "4");
        } else {
            $where = array("deleted" => "0", "type" => "4");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function get_customer_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('customer_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array('reseller_id' => $reseller, "deleted" => "0", "type" => "0");
        } else {
            $where = array("deleted" => "0", "type" => "0", 'reseller_id' => "0");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function get_reseller_Account_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('reseller_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array('reseller_id' => $account_data['id'], "deleted" => "0", "type" => "1");
        } else {
            $where = array('reseller_id' => "0","deleted" => "0", "type" => "1");
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
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller = $this->session->userdata('username');
            $where = array('reseller_id' => $reseller, "deleted" => "0", "type" => "3");
        } else {
            $where = array("deleted" => "0", "type" => "3");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "accounts", $where, "number", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "accounts", $where);
        }
        return $query;
    }

    function get_callshop_Account_list($flag, $start = 0, $limit = 0) {

        $this->db_model->build_search('callshop_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller = $this->session->userdata('username');
            $where = array('reseller_id' => $reseller, "deleted" => "0", "type" => "3");
        } else {
            $where = array("deleted" => "0", "type" => "5");
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
        $data = array('deleted' => '1');
        $this->db->update("accounts", $data);
        return true;
    }

    function insert_block($data, $accountid) {
        $data = explode(",", $data);
        $tmp = array();
        foreach ($data as $key => $data_value) {
            $tmp[$key]["accountid"] = $accountid;
            $tmp[$key]["blocked_patterns"] = $this->get_pattern_by_id($data_value);
        }
        return $this->db->insert_batch("block_patterns", $tmp);
    }

    function get_pattern_by_id($pattern) {
        $patterns = $this->db_model->getSelect("pattern", "routes", array("id" => $pattern));
        $patterns = $patterns->result_array();
        return $patterns[0]['pattern'];
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

    function account_process_payment($data) {
        $data["payment_type"] = $data["payment_type"][0];
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

        $this->db->insert('payments', $data);
        if (isset($data)) {
            if ($data['credit'] == '')
                $data['credit'] = '0';
            $date = date('Y-m-d H:i:s');
            $balance = $this->update_balance($data['credit'], $data['accountid'], "credit");
            $date = date('Y-m-d H:i:s');
            $insert_arr = array("accountid" => $data['accountid'], "description" => "Account refill",
                "created_date" => $date, "credit" => $data['credit'],
                "charge_type" => "account_refill");
            $this->db->insert("invoice_item", $insert_arr);
        }
        $accountinfo['email'] = '';
        $accountdata['email'] = $this->common->get_field_name('email', 'accounts', $data['accountid']);
        $accountdata['first_name'] = $this->common->get_field_name('first_name', 'accounts', $data['accountid']);
        $this->common->mail_to_users('voip_account_refilled', $accountdata);
    }

    function update_balance($amount, $accountid, $payment_type) {
        if ($payment_type == "credit") {
            $query = 'UPDATE `accounts` SET `balance` = (balance - ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        } else {
            $query = 'UPDATE `accounts` SET `balance` = (balance + ' . $amount . ') WHERE `id` = ' . $accountid;
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

}
