<?php
// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// ##############################################################################
class Payments extends MX_Controller {
    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('astpp/payment');

        $this->load->model('payments_model');
        $this->load->model('accounts/accounts_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function bpay() {
        $data['page_title'] = gettext('Bank payment');

        $this->load->view('view_bank_payments', $data);
    }

    function pattr() {
        $post_vars = $this->input->post();
        $json_data = array();

        if (isset($post_vars['i']) && intval($post_vars['i']) > 0){
            $json_data = $this->payments_model->get_pay_attrs($post_vars['i']);
        }

        header('Content-Type: application/json');
        echo json_encode($json_data);
    }

    function bpay_transaction() {
        $post_vars = $this->input->post();
        $json_data = array();

        if (
               isset($post_vars['u']) && intval($post_vars['u']) > 0
            && isset($post_vars['d']) && strlen($post_vars['d']) > 0
            && isset($post_vars['s']) && floatval($post_vars['s']) > 0
        ){
// Prepare data for transaction
            $pay_attrs = array(
                'id'           => $post_vars['u'],
                'payment_type' => 0,
                'credit'       => $post_vars['s'],
                'description'  => $post_vars['ds'],
                'date'         => $post_vars['d'],
                    );

            $json_data = $this->accounts_model->account_process_payment($pay_attrs);

            if (intval($json_data) == 0){
                $json_data['error'] = 200;
                $json_data['error_str'] = gettext('Transaction progress error');
            }
        } else {
            $json_data['error'] = 100;
            $json_data['error_str'] = gettext('Invalid params');
        }

        header('Content-Type: application/json');
        echo json_encode($json_data);
    }
}