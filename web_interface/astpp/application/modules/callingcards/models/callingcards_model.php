<?php

class Callingcards_model extends CI_Model {

    function Callingcards_model() {
        parent::__construct();
    }

    function get_cc_brands() {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $where = array('status' => '2', 'reseller' => $this->session->userdata('username'));
            $query = $this->db_model->select("*", "callingcardbrands", $where, "name", "ASC", false, false, '');
        } else {
            $where = '';
            $where = array("status" => "1");
            $query = $this->db_model->select("*", "callingcardbrands", $where, "name", "ASC", false, false, '');
        }
        $brands = array();
        foreach ($query->result() as $row) {
            $brands[$row->id] = $row->id;
        }
        return $brands;
    }

    function getCCList($flag, $brandsql, $start = 0, $limit = 0) {
        $this->db_model->build_search('callingcard_list_search');
        $where = array('status < ' => '2');
        if ($flag) {
            $query = $this->db_model->select_by_in("*", "callingcards", $where, "id", "ASC", $limit, $start, '', 'brand_id', $brandsql);
        } else {
            $query = $this->db_model->countQuery_by_in("*", "callingcards", $where, 'brand_id', $brandsql);
        }
        return $query;
    }

    function getccbrand_list($flag, $start, $limit) {
        $this->db_model->build_search('ccbrand_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "callingcardbrands", "", "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "callingcardbrands", "");
        }
        return $query;
    }

    function add_callingcard($add_array) {
        $count = $add_array['count'];
        unset($add_array['action']);
        unset($add_array['count']);
        $add_array['account_id'] = $this->common->get_field_name('id', 'accounts', array('number' => $add_array['account_id']));
        for ($i = 0; $i < $count; $i++) {
            $add_array['cardnumber'] = $this->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'cardnumber', 'callingcards');
            $add_array['created'] = date('Y-m-d H:i:s');
            $date = date('Y-m-d H:i:s');
            $acc_result = $this->db_model->getSelect('pin,validfordays,maint_fee_pennies,disconnect_fee_pennies,minute_fee_minutes,minute_fee_pennies,min_length_minutes,min_length_pennies', 'callingcardbrands', array('id' => $add_array['brand_id']));
            $acc_result = $acc_result->result_array();
            if($acc_result[0]["pin"]!= "1"){
                $add_array['pin'] = $this->common->find_uniq_rendno(common_model::$global_config['system_config']['pinlength'], '', '');
                unset($acc_result[0]["pin"]);
            }else{
                $add_array['pin'] = "";
                unset($acc_result[0]["pin"]);
            }
            $add_array['expiry'] = date("Y-m-d H:i:s",strtotime($date."+".$acc_result[0]['validfordays']."day"));
            $merge_array_value = array_merge($add_array, $acc_result[0]);
            $this->db->insert("callingcards", $merge_array_value);
        }

        $accountdata['email'] = $this->common->get_field_name('email', 'accounts', $add_array['account_id']);
        $accountdata['first_name'] = $this->common->get_field_name('first_name', 'accounts', $add_array['account_id']);
        $accountdata['cardnumber'] = $add_array['cardnumber'];
        $accountdata['pin'] = $add_array['pin'];
        $accountdata['balance'] = $add_array['value'];

        $this->common->mail_to_users('email_calling_card', $accountdata);
        return true;
    }

    function add_ccbrand($add_array) {
        unset($add_array['action']);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array["reseller_id"] = $account_data['id'];
        }
        $this->db->insert('callingcardbrands', $add_array);
        return true;
    }

    function edit_ccbrand($add_array, $id) {
        unset($add_array['action']);
        $this->db->where('id', $id);
        $this->db->update('callingcardbrands', $add_array);
        return true;
    }

    function remove_callingcard($id) {
        $this->db->where("id", $id);
        $this->db->delete("callingcards");
        return true;
    }

    function remove_ccbrand($data) {
        $this->db->where("id", $data);
        $this->db->delete("callingcardbrands");
        return true;
    }

    function update_status_card($post_array) {
        $status = array('status' => $post_array['status']);
        $this->db->where('id BETWEEN ' . $post_array['start_no'] . ' AND ' . $post_array['end_no'], NULL, FALSE);
        $this->db->update('callingcards', $status);
        return true;
    }

    function refill_card($data_post) {
        $query = 'UPDATE `callingcards` SET `value` = (value + ' . $data_post['value'] . ') WHERE `id` = ' . $data_post['id'];
        $query = $this->db->query($query);
        return true;
    }

    function getcallingcard_cdr($flag, $start, $limit) {
        $this->db_model->build_search('cc_cdr_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "callingcardcdrs", "", "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "callingcardcdrs", "");
        }
        return $query;
    }

    function get_price_list_for_cdrs() {
        if ($this->session->userdata('username') != "" && $this->session->userdata('logintype') != 2) {
            $this->db->where('reseller', $this->session->userdata('username'));
        }
        $this->db->where('status <', 2);
        $this->db->order_by('name', 'desc');
        $query = $this->db->get("pricelists");
        $price_list = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $price_list[$row['name']] = $row['name'];
        }
        return $price_list;
    }

    function get_card_by_number($number) {
        $this->db->where("id", $number);
        $query = $this->db->get("callingcards");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function get_callingcard_cdrs($card_number = false) {
        if ($card_number != false)
            $this->db->where("callingcard_id", $card_number);
        $query = $this->db->get("callingcardcdrs");

        if ($query->num_rows() > 0)
            return $query->result_array();
        else
            return false;
    }

    function get_callerid($id) {
        $query = $this->db_model->getSelect("*", "callingcards_callerid", array("callingcard_id" => $id));
        return $query;
    }

    function add_callerid($data) {
        unset($data['action']);
        unset($data['add']);
        unset($data['callingcard_number']);
        if (isset($data['status'])) {
            $data['status'] = '1';
        }
        $this->db->insert('callingcards_callerid', $data);
        return true;
    }

    function edit_callerid($data) {
        unset($data['action']);
        unset($data['edit']);
        unset($data['callingcard_number']);
        if (isset($data['status'])) {
            $data['status'] = '1';
        }
        $this->db->where('callingcard_id', $data['callingcard_id']);
        $this->db->update('callingcards_callerid', $data);
        return true;
    }

    function list_cc_brands_reseller($reseller) {
        $item_arr = array();
        $q = "SELECT id FROM callingcardbrands WHERE status < 2 AND reseller_id='" . $this->db->escape_str($reseller) . "'";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $item_arr[] = $row['id'];
            }
        }
        return $item_arr;
    }

}