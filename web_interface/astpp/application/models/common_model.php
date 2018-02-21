<?php

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
        $this->get_user_levels_list();
        $this->get_sweep_list();
//            $this->CI = & get_instance();
        $this->load->library('astpp/common');
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

    function get_user_levels_list() {
        $this->db->where_not_in('userlevelid', array('-1'));
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $this->db->where_in('userlevelid', array('0', '1', '5'));
            $query = $this->db->get("userlevels");
        } else {
            $query = $this->db->get("userlevels");
        }
        $userlevel_list = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $userlevel_list[$row['userlevelid']] = $row['userlevelname'];
        }
        self::$global_config['userlevel'] = $userlevel_list;
        return $userlevel_list;
    }

    function get_currency_list() {
        $query = $this->db->get("currency");
        $sweep_list = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $sweep_list[$row['Currency']] = $row['CurrencyName'];
        }
        return $sweep_list;
    }

    function get_sweep_list() {
        $query = $this->db->get("sweeplist");
        $sweep_list = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $sweep_list[$row['id']] = $row['sweep'];
        }
        self::$global_config['sweeplist'] = $sweep_list;
        return $sweep_list;
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

    function list_providers() {
        $this->db->where('type', '3');
        $this->db->where("status", "1");
        $query = $this->db->get("accounts");

        $providers = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $providers[] = $row['number'];
            }
        }

        return $providers;
    }

    function list_providers_select($default) {
        $ret_html = '';
        $providers = $this->list_providers();
        foreach ($providers as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }
        return $ret_html;
    }

    function list_sellers() {
        $this->db->where('type', '1');
        $this->db->where("status", "1");
        if ($this->session->userdata('logintype') == 1)
            $this->db->where("reseller", $this->session->userdata('username'));
        $query = $this->db->get("accounts");

        $sellers = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $sellers[] = $row['number'];
            }
        }

        return $sellers;
    }

    function list_resellers() {
        $this->db->where("status", "1");
        if ($this->session->userdata('logintype') == 1)
            $this->db->where("name", $this->session->userdata('username'));
        $query = $this->db->get("resellers");

        $sellers = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $sellers[] = $row['name'];
            }
        }

        return $sellers;
    }

    function list_resellers_select($default) {
        $ret_html = '';
        $sellers = $this->list_resellers();
        foreach ($sellers as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }
        return $ret_html;
    }

    function list_sellers_select($default) {
        $ret_html = '';
        $sellers = $this->list_sellers();
        foreach ($sellers as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }
        return $ret_html;
    }

    function list_accounts() {
        $this->db->where("status", "1");
        $query = $this->db->get("accounts");

        $accounts = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $accounts[] = $row['number'];
            }
        }

        return $accounts;
    }

    function list_accounts_select($default) {
        $ret_html = '';
        $accounts = $this->list_accounts();
        foreach ($accounts as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }
        return $ret_html;
    }

    function list_trunks() {

        if ($this->session->userdata('logintype') == 3)
            $this->db->where("provider", $this->session->userdata('username'));

        $this->db->where("status", "1");
        $query = $this->db->get("trunks");

        $trunks = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $trunks[] = $row['name'];
            }
        }

        return $trunks;
    }

    function list_trunks_select($default) {
        $ret_html = '';
        $trunks = $this->list_trunks();
        $ret_html .= "<option value=''>--Select Trunk--</option>";
        foreach ($trunks as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }
        return $ret_html;
    }

    function list_cc_brands() {
        $item_arr = array();
        $q = "SELECT name FROM callingcardbrands WHERE status < 2 AND (reseller_id IS NULL OR reseller = '')";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $item_arr[] = $row['name'];
            }
        }
        return $item_arr;
    }

    function list_cc_brands_reseller($reseller) {
        $item_arr = array();
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $q = "SELECT id FROM callingcardbrands WHERE status < 2 AND reseller_id='" . $this->db->escape_str($reseller) . "'";
            $query = $this->db->query($q);
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $item_arr[] = $row['id'];
                }
            }
        } else {
            //$item_arr[] = $data['username'] = $this->session->userdata('username'); 
            $item_arr[] = $this->session->userdata('username');
        }

        return $item_arr;
    }

    function list_cc_brands_select($reseller, $default) {
        $ret_html = '';

        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $providers = $this->list_cc_brands_reseller($this->session->userdata('username'));
        } else {
            $providers = $this->list_cc_brands();
        }

        /* 	if($reseller == '')
          $providers = $this->list_cc_brands();
          else
          $providers = $this->list_cc_brands_reseller($reseller); */

        foreach ($providers as $elem) {
            $ret_html .= '<option value="' . $elem . '"';
            if ($elem == $default)
                $ret_html .= 'selected="selected"';
            $ret_html .= ">$elem</option>";
        }

        return $ret_html;
    }

    function third_party_db_connect($localhost, $username, $password, $database) {
        $config['hostname'] = "" . $localhost . "";
        $config['username'] = "" . $username . "";
        $config['password'] = "" . $password . "";
        $config['database'] = "" . $database . "";
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        $config['swap_pre'] = '';
        $config['autoinit'] = TRUE;
        $config['stricton'] = FALSE;

        //$dsn = "mysql://$username:$password@$localhost/$database?db_debug=1";		
        return $this->load->database($config);
    }

    function list_sip_account_rt($accountinfo_number) {
        //$config = $this->get_system_config();

        $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

        $accountcode = array($accountinfo_number);
        $this->rt_db->where_in('accountcode', $accountcode);
        $this->rt_db->from(self::$global_config['system_config']['rt_sip_table']);
        $query = $this->rt_db->get();
        //echo $this->rt_db->last_query();
        $devicelist = array();
        if ($sip_names->num_rows() > 0) {
            foreach ($sip_names->result_array() as $row) {
                array_push($devicelist, $row['name']);
            }
        }

        return $devicelist;
    }

    function get_sip_account_rt($name) {

        //$config = $this->get_system_config();

        $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

        $this->rt_db->where('name', $name);
        $this->rt_db->limit(1);
        $this->rt_db->from(self::$global_config['system_config']['rt_sip_table']);
        $query = $this->rt_db->get();
        $record = array();
        if ($query->num_rows() > 0) {
            $record = $query->row_array();
        }
        return $record;
    }

    function list_iax_account_rt($accountinfo_number) {
        //$config = $this->get_system_config();

        $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

        $accountcode = array($accountinfo_number);
        $this->rt_db->where_in('accountcode', $accountcode);
        $this->rt_db->from(self::$global_config['system_config']['rt_iax_table']);
        $query = $this->rt_db->get();
        $devicelist = array();
        if ($iax_names->num_rows() > 0) {
            foreach ($iax_names->result_array() as $row) {
                array_push($devicelist, $row['name']);
            }
        }
        return $devicelist;
    }

    function get_iax_account_rt($name) {
        //$config = $this->get_system_config();

        $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

        $this->rt_db->where('name', $name);
        $this->rt_db->limit(1);
        $this->rt_db->from(self::$global_config['system_config']['rt_iax_table']);
        $query = $this->rt_db->get();
        $record = array();
        if ($query->num_rows() > 0) {
            $record = $query->row_array();
        }
        return $record;
    }

    function fs_list_sip_usernames($accountcode) {
        $domain = 0;

        if ($accountcode) {
            $tmp = "SELECT directory.id AS id, directory.username AS username, directory.domain AS domain FROM "
                    . "directory,directory_vars WHERE directory.id = directory_vars.directory_id "
                    . "AND directory_vars.var_name = 'accountcode' "
                    . "AND directory_vars.var_value IN ('" . $accountcode . "')";
        } else {
            $tmp = "SELECT id,username,domain FROM directory ";
            if ($this->session->userdata('logintype') == 1) {
                $tmp .= " WHERE username = '" . $this->session->userdata('username') . "'";
                if ($domain) {
                    $tmp .= " AND domain IN( '" . $domain . "','$${local_ip_v4}')";
                }
            }
        }

        $this->db_fs = Common_model::$global_config['fs_db'];
        $query1 = $this->db_fs->query($tmp);
        //echo $this->db_fs->last_query();
        $devicelist = array();
        if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row) {
                //array_push($devicelist, $row['username']);					
                array_push($devicelist, $row);
            }
        }
        return $devicelist;
    }

    function list_sip_account_freepbx($accountinfo_number) {
        //$config = $this->get_system_config();

        $this->freepbx_db = $this->third_party_db_connect(self::$global_config['system_config']['freepbx_host'], self::$global_config['system_config']['freepbx_user'], self::$global_config['system_config']['freepbx_pass'], self::$global_config['system_config']['freepbx_db']);

        $this->freepbx_db->where('keyword', 'accountcode');
        $accountcode = array($accountinfo_number);
        $this->freepbx_db->where_in('value', $accountcode);
        $this->freepbx_db->from(self::$global_config['system_config']['freepbx_table']);
        $query = $this->freepbx_db->get();
        $devicelist = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                array_push($devicelist, $row['id']);
            }
        }
        return $devicelist;
    }

    function list_iax_account_freepbx($accountinfo_number) {
        //$config = $this->get_system_config();

        $this->freepbx_db = $this->third_party_db_connect(self::$global_config['system_config']['freepbx_host'], self::$global_config['system_config']['freepbx_user'], self::$global_config['system_config']['freepbx_pass'], self::$global_config['system_config']['freepbx_db']);

        $this->freepbx_db->where('keyword', 'accountcode');
        $accountcode = array($accountinfo_number);
        $this->freepbx_db->where_in('value', $accountcode);
        $this->freepbx_db->from(self::$global_config['system_config']['freepbx_iax_table']);
        $query = $this->freepbx_db->get();
        $devicelist = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                array_push($devicelist, $row['id']);
            }
        }
        return $devicelist;
    }

    function get_sip_account_freepbx($name) {
        $deviceinfo = array();
        //$config = $this->get_system_config();	

        $this->freepbx_db = $this->third_party_db_connect(self::$global_config['system_config']['freepbx_host'], self::$global_config['system_config']['freepbx_user'], self::$global_config['system_config']['freepbx_pass'], self::$global_config['system_config']['freepbx_db']);

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_table'] . " WHERE id = '" . $name . "' AND keyword = 'context' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['context'] = $row;
        }

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_table'] . " WHERE id = '" . $name . "' AND keyword = 'secret' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['secret'] = $row;
        }
        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_table'] . " WHERE id = '" . $name . "' AND keyword = 'type' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['type'] = $row;
        }

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_table'] . " WHERE id = '" . $name . "' AND keyword = 'username' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['username'] = $row;
        }

        return $deviceinfo;
    }

    function get_iax_account_freepbx($name) {
        $deviceinfo = array();
        //$config = $this->get_system_config();	

        $this->freepbx_db = $this->third_party_db_connect(self::$global_config['system_config']['freepbx_host'], self::$global_config['system_config']['freepbx_user'], self::$global_config['system_config']['freepbx_pass'], self::$global_config['system_config']['freepbx_db']);

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_iax_table'] . " WHERE id = '" . $name . "' AND keyword = 'context' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['context'] = $row;
        }

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_iax_table'] . " WHERE id = '" . $name . "' AND keyword = 'secret' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['secret'] = $row;
        }

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_iax_table'] . " WHERE id = '" . $name . "' AND keyword = 'type' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['type'] = $row;
        }

        $tmp = "SELECT value FROM " . self::$global_config['system_config']['freepbx_iax_table'] . " WHERE id = '" . $name . "' AND keyword = 'username' LIMIT 1";
        $query = $this->freepbx_db->query($tmp);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $deviceinfo['username'] = $row;
        }

        return $deviceinfo;
    }

    function count_unrated_cdrs_account($account, $cc) {
        //$config = $this->get_system_config();

        $this->db_fscdr = self::$global_config['fscdr_db'];

        $this->db_fscdr->where_in('cost', array('none', 'error'));
        $this->db_fscdr->where_in('accountcode', array($account, $cc));
        $this->db_fscdr->from(Common_model::$global_config['system_config']['freeswitch_cdr_table']);
        $count = $this->db_fscdr->count_all_results();
        return $count;
    }

    function getVoipInfo_sip_login($cc) {
        //$config = $this->get_system_config();
        $record = array();
        if (self::$global_config['system_config']['users_dids_rt'] == 1) {

            $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

            $this->rt_db->where('accountcode', $cc);
            $this->rt_db->limit(1);
            $this->rt_db->select('name,secret');
            $this->rt_db->from(self::$global_config['system_config']['rt_sip_table']);
            $query = $this->rt_db->get();

            $sip_login = array();
            if ($query->num_rows() > 0) {
                $sip_login = $query->row_array();
            }
            $record = $sip_login;
        }
        return $record;
    }

    function getVoipInfo_iax_login($cc) {
        //$config = $this->get_system_config();
        $record = array();
        if (self::$global_config['system_config']['users_dids_rt'] == 1) {

            $this->rt_db = $this->third_party_db_connect(self::$global_config['system_config']['rt_host'], self::$global_config['system_config']['rt_user'], self::$global_config['system_config']['rt_pass'], self::$global_config['system_config']['rt_db']);

            $this->rt_db->where('accountcode', $cc);
            $this->rt_db->limit(1);
            $this->rt_db->select('name,secret');
            $this->rt_db->from(self::$global_config['system_config']['rt_iax_table']);
            $query = $this->rt_db->get();
            $iax2_login = array();
            if ($query->num_rows() > 0) {
                $iax2_login = $query->row_array();
            }

            $record = $iax2_login;
        }
        return $record;
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

    function calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        $from_currency = ($from_currency == '') ? self::$global_config['system_config']['base_currency'] : $from_currency;
        if ($to_currency == '') {
            if ($this->session->userdata['userlevel_logintype'] == -1 && $to_currency == '') {
                $to_currency = self::$global_config['system_config']['base_currency'];
            } else {
                $to_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $to_currency = $this->common->get_field_name('currency', 'currency', $to_currency1);
            }
        }
        $cal_amount = ($amount * self::$global_config['currency_list'][$to_currency]) / self::$global_config['currency_list'][$from_currency];
        if ($format_currency)
            $cal_amount = $this->format_currency($cal_amount);
        if ($append_currency)
            $cal_amount = $cal_amount . " " . $to_currency;
        return $cal_amount;
    }

    function add_calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        if ($from_currency == '') {
            if ($this->session->userdata['userlevel_logintype'] == -1 && $from_currency == '') {
                $from_currency = self::$global_config['system_config']['base_currency'];
            } else {
                $from_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $from_currency = $this->common->get_field_name('currency', 'currency', $from_currency1);
            }
        }
        $to_currency = ($to_currency == '') ? self::$global_config['system_config']['base_currency'] : $to_currency;
        $cal_amount = ($amount * self::$global_config['currency_list'][$to_currency]) / self::$global_config['currency_list'][$from_currency];
        if ($format_currency)
            $cal_amount = $this->format_currency($cal_amount);
        if ($append_currency)
            $cal_amount = $cal_amount . " " . $to_currency;
        return $cal_amount;
    }

    function to_calculate_currency($amount = 0, $from_currency = '', $to_currency = '', $format_currency = true, $append_currency = true) {
        if ($to_currency == '') {
            if ($this->session->userdata['userlevel_logintype'] == -1 && $to_currency == '') {
                $to_currency = self::$global_config['system_config']['base_currency'];
            } else {
                $to_currency1 = $this->session->userdata['accountinfo']['currency_id'];
                $to_currency = $this->common->get_field_name('currency', 'currency', $to_currency1);
            }
        }
        $from_currency = ($from_currency == '') ? self::$global_config['system_config']['base_currency'] : $from_currency;
        $cal_amount = ($amount * self::$global_config['currency_list'][$to_currency]) / self::$global_config['currency_list'][$from_currency];
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

    function status_message($response_tmp) {
        $response = json_decode($response_tmp);
        if ($response->status == 0) {
            $this->session->set_userdata('astpp_notification', $response->message);
        } else {
            $this->session->set_userdata('astpp_errormsg', $response->message);
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