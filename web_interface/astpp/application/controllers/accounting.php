<?php

class Accounting extends CI_Controller {

    function Accounting() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('form');
        $this->load->helper('romon');
        $this->load->library('astpp');

        $this->load->library('session');
        $this->load->library('form_builder');

//		$this->load->model('Pricelists_model');
//		$this->load->model('accounts_model');
//		
//		$this->load->model('Astpp_common');	
//		$this->load->model('rates_model');	
//		$this->load->model('switch_config_model');
        $this->load->model('accounting_model');
        $this->load->model('common_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function index() {
        $this->account_taxes();
    }

    /**
     * -------Here we write code for controller accounting functions invoiceconf------
     * this function set the invoice configration to generate pdf file.
     */
    function invoiceconf() {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Invoice Configuration';
        $data['page_title'] = 'Invoice Configuration';

        if (isset($_POST['action'])) {
            $this->accounting_model->save_invoiceconf($_POST);
            $this->session->set_userdata('astpp_notification', 'Invoice Configuration Updated Sucessfully!');
        }

        $invoiceconf = $this->accounting_model->get_invoiceconf();
        $data['invoiceconf'] = $invoiceconf;

        $this->load->view('view_accounting_invoiceconf', $data);
    }

    function get_action_buttons_taxes($tax_id) {
        $delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
        $ret_url = '';
        $ret_url .= '<a href="' . base_url() . 'accounting/account_taxes/delete/' . $tax_id . '/" class="icon" ' . $delete_style . ' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        return $ret_url;
    }

    /**
     * -------Here we write code for controller accounting functions account_taxes------
     * this function check action of the form and than perform action.
     * List account taxes add taxes to account edit account taxes and delete that account taxes.
     * @id: Account id
     */
    function account_taxes($action=false, $id=false) {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounting | account tax list';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Account Tax List';
        $data['cur_menu_no'] = 11;

        if ($action == false)
            $action = "list";


        if ($action == 'list') {
            $this->load->view('view_account_taxes_list', $data);
        } elseif ($action == 'add') {

            if (!empty($_POST)) {
                $query = $this->accounting_model->remove_all_account_tax($_POST['account_id']);
                foreach ($_POST as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $_POST['account_id'],
                            'taxes_id' => $_POST[$key],
                        );
                        $this->accounting_model->add_account_tax($data);
                    }
                }
                $this->session->set_userdata('astpp_notification', 'Account Tax added successfully!');
                redirect(base_url() . 'accounting/account_taxes/');
            }
            $data['tax_ids'] = array();
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'edit') {
            $taxes_id = $this->accounting_model->get_accounttax_by_id($id);
            $account_num = $this->accounting_model->get_account_number($id);
            $data['accountnum'] = $account_num['number'];
            $data['account_id'] = $id;
            for ($i = 0; $i < count($taxes_id); $i++) {
                $tax_ids[] = $taxes_id[$i]['taxes_id'];
            }
            $data['tax_ids'] = $tax_ids;

            $data['tax_id'] = $taxes_id;



            if (!empty($_POST)) {
                $query = $this->accounting_model->remove_all_account_tax($_POST['account_id']);
                foreach ($_POST as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $_POST['account_id'],
                            'taxes_id' => $_POST[$key],
                        );
                        $this->accounting_model->add_account_tax($data);
                    }
                }
                $this->session->set_userdata('astpp_notification', 'Account Tax added successfully!');
                redirect(base_url() . '/accounts/account_list/');
            }
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'delete') {
            $this->accounting_model->remove_account_tax($id);
            $this->session->set_userdata('astpp_notification', 'Account Tax removed successfully!');
            redirect(base_url() . 'accounting/account_taxes/');
        }
    }

    /**
     * -------Here we write code for controller accounting functions account_taxes_grid------
     * List account taxes in grid.
     */
    function account_taxes_grid() {
        $json_data = array();
        $count_all = $this->accounting_model->getAccount_taxes_count();

        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'];

        $page_no = $_GET['page'];

        $json_data['page'] = $page_no;
        $json_data['total'] = ($config['total_rows'] > 0) ? $config['total_rows'] : 0;

        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

//                 if($this->session->userdata['logintype']==1) 
//                 {
//                     $account_number = $this->session->userdata['accountinfo']['number'];
//                     $this->db->where('accounts.reseller',$account_number);
//                     $this->db->or_where('accounts.number',$account_number);
//                 }

        $query = $this->accounting_model->getAccount_TaxesList($start, $perpage);
        $json_data['rows'] = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $json_data['rows'][] = array('cell' => array(
                        $row['taxes_priority'],
                        $this->get_account_details($row['number']),
                        $row['first_name'] . ' ' . $row['last_name'],
                        ucfirst(Common_model::$global_config['userlevel'][$row['type']]),
                        $row['taxes_rate'],
                        $row['taxes_amount'],
                        $row['taxes_description'],
                        $this->get_action_buttons_taxes($row['id'])
                        ));
            }
        }
        echo json_encode($json_data);
    }

    /**
     * -------Here we write code for controller accounting functions vallid_account_tax------
     * here this function called by ajax form and vallidate the account number
     * @$_POST['username']: Account Number
     */
    function valid_account_tax() {
        $tax_id = '';
        if (!empty($_POST['username'])) {

            $account_num = mysql_real_escape_string($_POST['username']);
            $row = $this->accounting_model->check_account_num($account_num);
            if (isset($row['accountid']) && $row['accountid'] != '') {
                $taxes_id = $this->accounting_model->get_accounttax_by_id($row['accountid']);
                if ($taxes_id) {
                    foreach ($taxes_id as $id) {
                        $tax_id.=$id['taxes_id'] . ",";
                    }

                    $tax_id = rtrim($tax_id, ",");
                    echo $row['accountid'] . ',' . $tax_id;
                }
            } else {
                echo $row['accountid'];
            }
        }
    }

    /**
     * -------Here we write code for controller accounting functions invoice_list------
     * List account invoice.
     * @id: Account id
     */
    function invoice_list($action=false, $id=false) {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounting | account invoice list';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Account Invoice List';
        $data['cur_menu_no'] = 11;

        if ($action == false)
            $action = "list";

        if ($action == 'list') {
            $this->load->view('view_account_invoice_list', $data);
        }
    }

    /**
     * -------Here we write code for controller accounting functions account_invoice_grid------
     * List account invoice in grid.
     */
    function account_invoice_grid() {
        $json_data = array();
        $count_all = $this->accounting_model->getAccount_taxes_count();

        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'] = 1;

        $page_no = $_GET['page'] = 10;

        $json_data['page'] = $page_no;
        $json_data['total'] = ($config['total_rows'] > 0) ? $config['total_rows'] : 0;

        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

        $json_data['rows'] = array();
        $query = $this->accounting_model->getAccount_invoiceList($start, $perpage);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $json_data['rows'][] = array('cell' => array(
                        $row['invoiceid'],
                        $this->get_account_details($row['number']),
                        $row['first_name'] . ' ' . $row['last_name'],
                        ucfirst(Common_model::$global_config['userlevel'][$row['type']]),
                        $row['date'],
                        $this->common_model->calculate_currency($row['value']),
                        $this->get_action_buttons_invoice($row['invoiceid'])
                        ));
            }
        }
        echo json_encode($json_data);
    }

    function get_action_buttons_invoice($invoiceid) {
        $details_style = 'style="text-decoration:none;background-image:url(/images/details.png);"';
        $pdf_style = 'style="text-decoration:none;background-image:url(/images/pdf.png);"';
        $ret_url = '';
        $ret_url .= '<a href="' . base_url() . 'accounts/view_invoice/' . $invoiceid . '/" class="icon" ' . $details_style . ' title="Details">&nbsp;</a>';
        $ret_url .= '<a href="' . base_url() . 'accounts/download_invoice/' . $invoiceid . '/" class="icon" ' . $pdf_style . ' title="Details">&nbsp;</a>';
        return $ret_url;
    }

    function get_account_details($number) {
        $ret_url = '';
        if(isset($this->session->userdata['accountinfo']['number']) && $number==$this->session->userdata['accountinfo']['number']){
            $ret_url.= $number;    
        }
        else{
            $ret_url .= '<a href="' . base_url() . 'accounts/account_detail/' . $number . '" title="Details">&nbsp;' . $number . '</a>';
        }
        return $ret_url;
    }

    /**
     * -------Here we write code for controller accounting functions search------
     * search invoice from database and redirect to invoice list function.
     */
    function search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('invoice_search', $_POST);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounting/invoice_list');
        }
    }

    /**
     * -------Here we write code for controller accounting functions clearsearchfilter------
     * flush session for search values.
     */
    function clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('invoice_search', "");
        redirect(base_url() . 'accounting/invoice_list');
    }

    /**
     * -------Here we write code for controller accounting functions search_taxes------
     * search account taxes from database and redirect to invoice list function.
     */
    function search_taxes() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('account_taxes_search', $_POST);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounting/account_taxes');
        }
    }

    /**
     * -------Here we write code for controller accounting functions clearsearchfilter_taxes------
     * flush session for search values for account taxes search.
     */
    function clearsearchfilter_taxes() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_taxes_search', "");
        redirect(base_url() . 'accounting/account_taxes');
    }

}

?>
