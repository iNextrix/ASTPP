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

        if (
            $this->session->userdata('user_login') == FALSE
            && $this->router->fetch_method() !== 'ym_check_payments'
        )
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

/* Yandex.Money  */

    function ympay () {
        $get_vars = $this->input->get();
        $user_data = $this->session->all_userdata();
        $data['page_title'] = gettext('Yandex.Money payments');
        $data['uid'] = $user_data['accountinfo']['id'];

        if (isset($get_vars['ym_status'])){
            $data['status'] = $get_vars['ym_status'];
            $this->load->view('view_ym_payments_status', $data);
        } else {
            $this->load->view('view_ym_payments', $data);
        }
    }
    
    function ym_replace_order () {
        $this->load->library('ym_pgateway');

        $base_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/payments/ympay';

        $this->ym_pgateway->setCBUrl(array(
            'urlSuccess' => $base_url.'?ym_status=success',
            'urlFail'    => $base_url.'?ym_status=fail',
        ));

        $post_vars = $this->input->post();
        $json_data = array();

        if (
               isset($post_vars['u']) && intval($post_vars['u']) > 0
            && isset($post_vars['s']) && intval($post_vars['s']) > 0
        ){
            if ($this->ym_pgateway->ready()){
                $json_data['rurl'] = $this->ym_pgateway->placeOrder(
                    $post_vars['u'],
                    $post_vars['s'],
                    ''
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode($json_data);
    }

    function ym_check_payments () {
        $this->load->library('ym_pgateway');

        $pay_list = $this->ym_pgateway->getConfirmedPayments();

        foreach ($pay_list as $k => $v) {
            $pay_attrs = array(
                'id'           => $v['uid'],
                'payment_type' => 0,
                'credit'       => $v['psum'],
                'description'  => 'YM-'.$v['oid'].' '.$v['pmsg'],
                'date'         => $v['pdate'],
                    );

            $this->accounts_model->account_process_payment($pay_attrs);
        }
    }
}