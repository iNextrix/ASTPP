<?php

class Opensips_model extends CI_Model {

    function Opensips_model() {
        parent::__construct();
    }

    function getopensipsdevice_list($flag, $start = 0, $limit = 0) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->build_search_opensips('opensipsdevice_list_search');
        if ($flag) {
            $this->opensips_db->limit($limit,$start);
            $query = $this->opensips_db->get("subscriber");
        } else {
            $query = $this->opensips_db->get("subscriber");
            $query = $query->num_rows();
        }
        return $query;
    }

    function getopensipsdevice_customer_list($flag, $accountid = "", $start = "0", $limit = "0") {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        if ($accountid != "") {
            $where = array("accountcode" => $this->common->get_field_name('number', 'accounts', array('id' => $accountid)));
        }
        if ($flag) {
            $this->opensips_db->where($where);
            $query = $this->opensips_db->get("subscriber");
        } else {
// $this->opensips_db->limit($limit,$start);
            $this->opensips_db->where($where);
            $query = $this->opensips_db->get("subscriber");
            $query = $query->num_rows();
        }
        return $query;
    }

    function getopensipsdispatcher_list($flag, $start = '', $limit = '') {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        if ($flag) {
            $this->opensips_db->limit( $limit,$start);
            $query = $this->opensips_db->get("dispatcher");
        } else {
            $query = $this->opensips_db->get("dispatcher");
            $query = $query->num_rows();
        }
        return $query;
    }

    function add_opensipsdevices($data) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->opensips_db = $this->load->database($opensipdsn, true);
        unset($data["action"]);
        $this->opensips_db->insert("subscriber", $data);
    }

    function edit_opensipsdevices($data, $id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        unset($data["action"]);
        $this->opensips_db->where("id", $id);
        $this->opensips_db->update("subscriber", $data);
    }

    function remove_opensips($id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->opensips_db->where("id", $id);
        $this->opensips_db->delete("subscriber");
        return true;
    }

    function add_opensipsdispatcher($data) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        unset($data["action"]);
        $this->opensips_db->insert("dispatcher", $data);
    }

    function edit_opensipsdispatcher($data, $id) {
        unset($data["action"]);

        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        $this->opensips_db->where("id", $id);
        $this->opensips_db->update("dispatcher", $data);
    }

    function remove_dispatcher($id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        $this->opensips_db->where("id", $id);
        $this->opensips_db->delete("dispatcher");
        return true;
    }

    function build_search_opensips($accounts_list_search) {
        if ($this->session->userdata('advance_search') == 1) {
            $account_search = $this->session->userdata($accounts_list_search);
            unset($account_search["ajax_search"]);
            unset($account_search["advance_search"]);
            foreach ($account_search as $key => $value) {
                if ($value != "") {
                    if (is_array($value)) {
                        if (array_key_exists($key . "-integer", $value)) {
                            $this->get_interger_array($key, $value[$key . "-integer"], $value[$key]);
                        }
                        if (array_key_exists($key . "-string", $value)) {
                            $this->get_string_array($key, $value[$key . "-string"], $value[$key]);
                        }
                    } else {
                        $this->opensips_db->where($key, $value);
                    }
                }
            }
        }
    }

    function get_interger_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $this->opensips_db->where($field, $search_array);
                    break;
                case "2":
                    $this->opensips_db->where($field . ' <>', $search_array);
                    break;
                case "3":
                    $this->opensips_db->where($field . ' > ', $search_array);
                    break;
                case "4":
                    $this->opensips_db->where($field . ' < ', $search_array);
                    break;
                case "5":
                    $this->opensips_db->where($field . ' >= ', $search_array);
                    break;
                case "6":
                    $this->opensips_db->where($field . ' <= ', $search_array);
                    break;
            }
        }
    }

    function get_string_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $this->opensips_db->like($field, $search_array);
                    break;
                case "2":
                    $this->opensips_db->not_like($field, $search_array);
                    break;
                case "3":
                    $this->opensips_db->where($field, $search_array);
                    break;
                case "4":
                    $this->opensips_db->where($field . ' <>', $search_array);
                    break;
            }
        }
    }

}
