<?php

class dashboard extends CI_Controller {

    function dashboard() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('Auth_model');
        $this->load->model('Astpp_common');
    }

    function index() {
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . 'login/login');
        $data['app_name'] = '';
        if ($this->session->userdata('logintype') != 2) {
            $data['username'] = $this->session->userdata('user_name');
            $account_data = $this->session->userdata("accountinfo");
            $where = array('id' => $account_data["id"]);
            $account = $this->db_model->getSelect("*", "accounts", $where);
            $account = $account->result_array();
            $data["account"] = $account[0];
	    $data["account"]["balance"] = $this->common_model->add_calculate_currency(($data["account"]["balance"]*-1), "", '', true, true);
            $data["account"]['country_id'] = $this->common->get_field_name('country', 'countrycode', $data["account"]['country_id']);
	    $data["account"]['timezone_id'] = $this->common->get_field_name('gmtzone', 'timezone', $data["account"]['timezone_id']);
            $data["account"]["credit_limit"] = $this->common_model->calculate_currency($data["account"]["credit_limit"]);
            $this->load->view('view_user_dashboard', $data);
        } else {
            $data['customer_count'] = $this->Astpp_common->count_accounts(" WHERE type = 0 ");
            $data['reseller_count'] = $this->Astpp_common->count_accounts(" WHERE type = 1 ");
            $data['vendor_count'] = $this->Astpp_common->count_accounts(" WHERE type = 3 ");
            $data['admin_count'] = $this->Astpp_common->count_accounts(" WHERE type = 2 ");
            $data['callshop_count'] = $this->Astpp_common->count_accounts(" WHERE type = 5 ");
            $data['total_owing'] = $this->common_model->calculate_currency($this->Astpp_common->accounts_total_balance(""));
            $data['total_due'] = $this->common_model->calculate_currency($this->Astpp_common->accountbalance(""));

            $data['dids'] = $this->Astpp_common->count_dids("");

//            $data['unbilled_cdrs'] = $this->Astpp_common->count_unbilled_cdrs("NULL,'',");
            $data['unbilled_cdrs'] = "0";
            $data['calling_cards_active'] = $this->Astpp_common->count_callingcards(" WHERE status = 1");
            $data['calling_cards_unused'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1", "SUM(value-used)"));
            $data['calling_cards_used'] = $this->common_model->calculate_currency($this->Astpp_common->count_callingcards(" WHERE status = 1", "SUM(used)"));
            $data['username'] = $this->session->userdata('user_name');

            $this->load->view('view_dashboard', $data);
        }
    }

}

?>
