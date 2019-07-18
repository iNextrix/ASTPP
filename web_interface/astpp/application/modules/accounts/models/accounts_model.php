<?php

// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
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
class Accounts_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function add_account($accountinfo)
    {
        $account_data = $this->session->userdata("accountinfo");
        $accountinfo['reseller_id'] = ($account_data['type'] == 1) ? $account_data['id'] : ($accountinfo['reseller_id'] > 0 ? $accountinfo['reseller_id'] : 0);
        unset($accountinfo['action']);
        $accountinfo['permission_id'] = ($accountinfo['type'] == 1 || $accountinfo['type'] == 2) ? (isset($accountinfo['permission_id']) ? $accountinfo['permission_id'] : $account_data['permission_id']) : 0;
        $accountinfo['is_distributor'] = $account_data['type'] == 1 ? $account_data['is_distributor'] : (isset($accountinfo['is_distributor']) ? $accountinfo['is_distributor'] : 1);
        $this->load->library("astpp/signup_lib");
        $this->signup_lib->create_account($accountinfo);
        return $last_id;
    }

    function reseller_rates_batch_update($update_array)
    {
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
                    $where = array(
                        'type' => 1,
                        "balance" => $update_array['balance']['balance'],
                        "reseller_id" => $account_data['id'],
                        'deleted' => '0',
                        'status' => '0'
                    );
                } else {
                    $where = array(
                        'type' => 1,
                        "balance" => $update_array['balance']['balance'],
                        'deleted' => '0',
                        'status' => '0'
                    );
                }

                $this->db_model->build_search('reseller_list_search');
                $query_pricelist = $this->db_model->getSelect("id,reseller_id,balance", "accounts", $where);
                if ($query_pricelist->num_rows() > 0) {
                    $description = '';
                    if ($update_array['balance']['operator'] == '2') {
                        $description .= "Reseller update set balance by admin";
                    }
                    if ($update_array['balance']['operator'] == '3') {
                        $description .= "Reseller update increase balance by admin";
                    }
                    if ($update_array['balance']['operator'] == '4') {
                        $description .= "Reseller update decrease balance by admin";
                    }
                    foreach ($query_pricelist->result_array() as $key => $reseller_payment) {
                        if (! empty($reseller_payment['reseller_id']) && $reseller_payment['reseller_id'] != '') {
                            $payment_by = $reseller_payment['reseller_id'];
                        } else {
                            $payment_by = '-1';
                        }
                        $insert_arr = array(
                            "accountid" => $reseller_payment['id'],
                            "amount" => $update_array['balance']['balance'],
                            'tax' => 0,
                            'payment_method' => "SYSTEM",
                            "date" => $date,
                            'reseller_id' => $reseller_payment['reseller_id']
                        );
                        $this->db->insert("payment_transaction", $insert_arr);
                    }
                }
            }
        }

        return true;
    }

    function customer_rates_batch_update($update_array)
    {
        unset($update_array['action']);
        $date = gmdate("Y-m-d h:i:s");
        $this->db_model->build_search('customer_list_search');
        $reseller_info = $this->session->userdata['accountinfo'];
        if ($reseller_info['type'] == 1) {
            $this->db->where('reseller_id', $reseller_info['id']);
        }
        $this->db_model->build_search('customer_list_search');
        $this->db->where('type !=', '1');
        $this->db_model->build_batch_update_array($update_array);
        $this->db->update("accounts");
        if (isset($update_array['balance']['balance']) && $update_array['balance']['balance'] != '') {
            $account_data = $this->session->userdata("accountinfo");

            if ($account_data['type'] == 1) {
                $where = array(
                    'type' => 1,
                    "reseller_id" => $account_data['id'],
                    'deleted' => '0',
                    'status' => '0'
                );
            } else {
                $where = array(
                    'type !=' => '-1',
                    "balance" => $update_array['balance']['balance'],
                    'deleted' => '0',
                    'status' => '0'
                );
            }

            $this->db_model->build_search('customer_list_search');
            $query_pricelist = $this->db_model->getSelect("id,reseller_id,balance", "accounts", $where);
            if ($query_pricelist->num_rows() > 0) {
                $description = '';
                if ($update_array['balance']['operator'] == '2') {
                    $description .= "Customer update set balance by admin";
                }
                if ($update_array['balance']['operator'] == '3') {
                    $description .= "Customer update increase balance by admin";
                }
                if ($update_array['balance']['operator'] == '4') {
                    $description .= "Customer update descrise balance by admin";
                }
                foreach ($query_pricelist->result_array() as $key => $customer_payment) {
                    if (! empty($customer_payment['reseller_id']) && $customer_payment['reseller_id'] != '0') {
                        $payment_by = $customer_payment['reseller_id'];
                    } else {
                        $payment_by = '-1';
                    }
                    $insert_arr = array(
                        "accountid" => $customer_payment['id'],
                        "amount" => $update_array['balance']['balance'],
                        'tax' => 0,
                        'payment_method' => "SYSTEM",
                        "date" => $date,
                        'reseller_id' => isset($customer_payment['reseller_id']) ? $customer_payment['reseller_id'] : 0
                    );
                    $this->db->insert("payment_transaction", $insert_arr);
                }
            }
        }
        return true;
    }

    function edit_account($accountinfo, $edit_id)
    {
        unset($accountinfo['action']);
        unset($accountinfo['onoffswitch']);
        $accountinfo = array_map('trim', $accountinfo);
        $this->db->where('id', $edit_id);
        $result = $this->db->update('accounts', $accountinfo);
        return true;
    }

    function bulk_insert_accounts($add_array)
    {
        $account_data = $this->session->userdata("accountinfo");
        $add_array['reseller_id'] = ($account_data['type'] == 1 || $account_data['type'] == 5) ? $account_data['id'] : 0;
        $this->load->library("astpp/signup_lib");

        $this->signup_lib->bulk_account_creation($add_array);
        return TRUE;
    }

    function get_max_limit($add_array)
    {
        $this->db->where('deleted', '0');
        $this->db->where("length(number)", $add_array['account_length']);
        $this->db->like('number', $add_array['prefix'], 'after');
        $this->db->select("count(id) as count");
        $this->db->from('accounts');
        $result = $this->db->get();
        $result = $result->result_array();
        $count = $result[0]['count'];
        $remaining_length = 0;
        if (! empty($add_array['account_length'] || $add_array['prefix'])) {
            $remaining_length = $add_array['account_length'] - strlen($add_array['prefix']);
        }
        $currentlength = pow(10, $remaining_length);
        $currentlength = $currentlength - $count;
        return $currentlength;
    }

    function account_process_payment($data, $update_balance_flag = 'true')
    {
        $data['accountid'] = $data['id'];
        $accountdata = (array) $this->db->get_where('accounts', array(
            "id" => $data['accountid']
        ))->first_row();
        $accountinfo = $this->session->userdata('accountinfo');
        $data["payment_by"] = $accountdata['reseller_id'] > 0 ? $accountdata['reseller_id'] : '-1';
        $data['payment_mode'] = $data['payment_type'];
        unset($data['action'], $data['id'], $data['account_currency'], $data['payment_type']);
        if (isset($data) && ! empty($accountdata)) {

            $payment_type=($data['payment_mode'] == 1) ? 'POSTCHARGE' : 'REFILL';

            $payment_array = array(
                "accountid" => $accountdata['id'],
                "reseller_id" => $accountdata['reseller_id'],
                "product_category" => 3,
                "price" => $data['credit'],
                "payment_by" => "Manual",
                "payment_method" => "Manual",
                "order_item_id" => 0,
                "charge_type" => $payment_type,
                "description" => "Account has been " . $payment_type . " by " . $accountinfo['first_name'] . " '(' " . $accountinfo['number'] . " ')' ",
                "invoice_type" => $data['payment_mode'] == 1 ? "debit" : "credit",
                "is_apply_tax" => "false"
            );

            $where = array(
                'id' => $accountinfo['currency_id']
            );
            $currency_info = (array) $this->db->get_where("currency", $where)->result_array()[0];
            $invoiceid = $this->payment->add_payments_transcation($payment_array, $accountdata, $currency_info);
            $current_id = $accountinfo['type'] == 1 ? $accountinfo['id'] : '0';
        }
    }

    function get_admin_Account_list($flag, $start = 0, $limit = 0, $reseller_id = 0)
    {
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
        if (isset($_GET['sortname']) && $_GET['sortname'] != 'undefined') {
            $this->db->order_by($_GET['sortname'], ($_GET['sortorder'] == 'undefined') ? 'desc' : $_GET['sortorder']);
        } else {
            $this->db->order_by('number', 'desc');
        }
        $result = $this->db->get('accounts');

        if ($flag) {
            return $result;
        } else {
            return $result->num_rows();
        }
    }

    function get_customer_Account_list($flag, $start = 0, $limit = 0, $export = false)
    {
        $this->db_model->build_search('customer_list_search');
        $where['deleted'] = 0;
        $accountinfo = $this->session->userdata("accountinfo");
        if ($accountinfo['type'] == 1 || $accountinfo['type'] == 5) {
            $where['reseller_id'] = $accountinfo['id'];
        }
        $this->db->select('*');
        $this->db->select('reseller_id as rid');
        $this->db->where_in('type', array(
            '0',
            '3'
        ));
        $this->db->where($where);
        if ($flag) {
            if (! $export) {
                $get_array=$this->input->get();
		$sortfield_name='number';
		$sordorder_name='desc';
		if(isset($get_array['sortname'])){
			$sortfield_name=$get_array['sortname'];
			$sordorder_name=$get_array['sortorder'];	
		}
                $this->db->limit($limit, $start);
                $this->db->order_by($sortfield_name,$sordorder_name);
            }
        }
        $result = $this->db->get('accounts');
        if ($result->num_rows() > 0) {
            if ($flag) {
                return $result;
            } else {
                return $result->num_rows();
            }
        } else {

            if ($flag) {
                $result = $result;
            } else {
                $result = 0;
            }

            return $result;
        }
    }

    function get_reseller_Account_list($flag, $start = 0, $limit = 0, $export = false)
    {
        $this->db_model->build_search('reseller_list_search');
        $where = array(

            "deleted" => "0",
            "type" => "1"
        );
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

    function get_provider_Account_list($flag, $start = 0, $limit = 0)
    {
        $this->db_model->build_search('provider_list_search');
        $where = array(
            "deleted" => "0",
            "type" => "3",
            'reseller_id' => 0
        );
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

    function remove_customer($id, $type)
    {
        $this->db->where("id", $id);
        $this->db->where("type", $type);
        $data = array(
            'deleted' => '1'
        );
        $this->db->update("accounts", $data);
        return true;
    }

    function insert_block($data, $accountid)
    {
        $data = explode(",", $data);
        $tmp = array();
        if (! empty($data)) {
            foreach ($data as $key => $data_value) {
                $tmp[$key]["accountid"] = $accountid;
                $result = $this->get_pattern_by_id($data_value);
                $tmp[$key]["blocked_patterns"] = $result[0]['pattern'];
                $tmp[$key]["destination"] = $result[0]['comment'];
            }
            return $this->db->insert_batch("block_patterns", $tmp);
        }
    }

    function get_pattern_by_id($pattern)
    {
        $patterns = $this->db_model->getSelect("pattern,comment", "routes", array(
            "id" => $pattern
        ));
        $patterns_value = $patterns->result_array();
        return $patterns_value;
    }

    function get_callerid($account_id)
    {
        $query = $this->db_model->getSelect("*", "accounts_callerid", array(
            "accountid" => $account_id
        ));
        return $query;
    }

    function get_account_number($accountid)
    {
        $accountinfo = $this->session->userdata('accountinfo');
        $reseller_id = $accountinfo['type'] == 1 || $accountinfo['type'] == 5 ? $accountinfo['id'] : 0;

        $query = $this->db_model->getSelect("number", "accounts", array(
            "id" => $accountid,
            'reseller_id' => $reseller_id
        ));
        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function add_callerid($data)
    {
        unset($data['action'], $data['flag']);
        $this->db->insert('accounts_callerid', $data);
        return true;
    }

    function edit_callerid($data)
    {
        unset($data['action'], $data['flag']);
        $this->db->where('accountid', $data['accountid']);
        $this->db->update('accounts_callerid', $data);
        return true;
    }

    function remove_all_account_tax($account_tax)
    {
        $this->db->where('accountid', $account_tax);
        $this->db->delete('taxes_to_accounts');
        return true;
    }

    function add_account_tax($data)
    {
        $this->db->insert('taxes_to_accounts', $data);
    }

    function get_accounttax_by_id($account_id)
    {
        $this->db->where("accountid", trim($account_id));
        $query = $this->db->get("taxes_to_accounts");
        if ($query->num_rows() > 0)
            return $query->result_array();
        else
            return false;
    }

    function check_account_num($acc_num)
    {
        $this->db->select('accountid');
        $this->db->where("number", $acc_num);
        $query = $this->db->get("accounts");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function get_account_by_number($id)
    {
        $accountinfo = $this->session->userdata('accountinfo');
        $reseller_id = $accountinfo['type'] == 1 || $accountinfo['type'] == 5 ? $accountinfo['id'] : 0;
        $this->db->where('reseller_id', $reseller_id);
        $this->db->where("id", $id);
        $query = $this->db->get("accounts");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function get_currency_by_id($currency_id)
    {
        $query = $this->db_model->getSelect("*", 'currency', array(
            'id' => $currency_id
        ));
        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function update_balance($amount, $accountid, $payment_type)
    {
        if ($payment_type == 0) {
            $query = "update accounts set balance =  IF(posttoexternal=1,balance-" . $amount . ",balance+" . $amount . ") where id ='" . $accountid . "'";

            return $this->db->query($query);
        }
        if ($payment_type == 1) {
            $query = "update accounts set balance =  IF(posttoexternal=1,balance+" . $amount . ",balance-" . $amount . ") where id ='" . $accountid . "'";

            return $this->db->query($query);
        }
    }

    function account_authentication($where_data, $id)
    {
        if ($id != "") {
            $this->db->where("id <>", $id);
        }
        $this->db->where($where_data);
        $this->db->from("accounts");
        $query = $this->db->count_all_results();
        return $query;
    }

    function get_animap($flag, $start, $limit, $id)
    {
        $where = array(
            'accountid' => $id
        );

        if ($flag) {
            $query = $this->db_model->select("*", "ani_map", $where, "number", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "ani_map", $where);
        }
        return $query;
    }

    function add_animap($data)
    {
        $this->db->insert('ani_map', $data);
        return true;
    }

    function edit_animap($data, $id)
    {
        $new_array = array(
            'number' => $data['number'],
            'status' => $data['status']
        );
        $this->db->where('id', $id);
        $this->db->update('ani_map', $new_array);
        return true;
    }

    function remove_ani_map($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('ani_map');
        return true;
    }

    function animap_authentication($where_data, $id)
    {
        if ($id != "") {
            $this->db->where("id <>", $id);
        }
        $this->db->where($where_data);
        $this->db->from("ani_map");
        $query = $this->db->count_all_results();
        return $query;
    }

    function add_invoice_config($add_array)
    {
        $result = $this->db->insert('invoice_conf', $add_array);
        return true;
    }

    function edit_invoice_config($add_array, $edit_id)
    {
        $this->db->where('id', $edit_id);
        $result = $this->db->update('invoice_conf', $add_array);
        return true;
    }
}
