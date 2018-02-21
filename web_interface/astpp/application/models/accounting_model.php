<?php

class Accounting_model extends CI_Model {

    function Accounting_model() {
        parent::__construct();
    }

    /**
     * -------Here we write code for model accounting functions get_invoiceconf------
     * this function get the invoice configration to generate pdf file.
     */
    function get_invoiceconf() {
        if ($this->session->userdata('userlevel_logintype') == -1) {
            $accountid = -1;
        } else {
            $accountdata = $this->session->userdata('accountinfo');
            $accountid = $accountdata['accountid'];
        }
        $this->db->where('accountid', $accountid);
        $this->db->from('invoice_conf');
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }

    /**
     * -------Here we write code for model accounting functions save_invoiceconf------
     * this function set the invoice configration to generate pdf file.
     * @data = the configuration details
     */
    function save_invoiceconf($data) {
        if ($this->session->userdata('userlevel_logintype') == -1) {
            $accountid = -1;
        } else {
            $accountdata = $this->session->userdata('accountinfo');
            $accountid = $accountdata['accountid'];
        }
        extract($data);
        $query = "UPDATE invoice_conf set company_name='" . $company_name . "',address='" . $address . "',city='" . $city . "',province='" . $province . "',country='" . $country . "',zipcode='" . $zipcode . "',telephone='" . $telephone . "',emailaddress='" . $emailaddress . "',website='" . $website . "',fax='" . $fax . "' where accountid='" . $accountid . "'";
        $this->db->query($query);
    }

    /**
     * -------Here we write code for model accounting functions getAccount_taxes_count------
     * this function count the total enetry of account taxes in database.
     */
    function getAccount_taxes_count() {
        $this->db->from('taxes_to_accounts');
        $row_count = $this->db->count_all_results();
        return $row_count;
    }

    /**
     * -------Here we write code for model accounting functions getAccount_taxesList------
     * this function fetch the account taxes entries from database and return array of that recordes
     */
    function getAccount_TaxesList($start, $limit) {
        $myflag = false;
        if ($this->session->userdata('advance_search') == 1) {

            $account_taxes_search = $this->session->userdata('account_taxes_search');

            $priority_operator = $account_taxes_search['taxes_priority_operator'];

            if (!empty($account_taxes_search['taxes_priority'])) {
                switch ($priority_operator) {
                    case "1":
                        $this->db->where('taxes_to_accounts_view.taxes_priority', $account_taxes_search['taxes_priority']);
                        break;
                    case "2":
                        $this->db->where('taxes_to_accounts_view.taxes_priority <>', $account_taxes_search['taxes_priority']);
                        break;
                    case "3":
                        $this->db->where('taxes_to_accounts_view.taxes_priority > ', $account_taxes_search['taxes_priority']);
                        break;
                    case "4":
                        $this->db->where('taxes_to_accounts_view.taxes_priority < ', $account_taxes_search['taxes_priority']);
                        break;
                    case "5":
                        $this->db->where('taxes_to_accounts_view.taxes_priority >= ', $account_taxes_search['taxes_priority']);
                        break;
                    case "6":
                        $this->db->where('taxes_to_accounts_view.taxes_priority <= ', $account_taxes_search['taxes_priority']);
                        break;
                }
            }

            if (!empty($account_taxes_search['account_number'])) {
                $this->db->where('accounts.number', $account_taxes_search['account_number']);
            }

            $tax_rate_operator = $account_taxes_search['taxes_rate_operator'];

            if (!empty($account_taxes_search['taxes_rate'])) {
                switch ($tax_rate_operator) {
                    case "1":
                        $this->db->where('taxes_to_accounts_view.taxes_rate', $account_taxes_search['taxes_rate']);
                        break;
                    case "2":
                        $this->db->where('taxes_to_accounts_view.taxes_rate <>', $account_taxes_search['taxes_rate']);
                        break;
                    case "3":
                        $this->db->where('taxes_to_accounts_view.taxes_rate > ', $account_taxes_search['taxes_rate']);
                        break;
                    case "4":
                        $this->db->where('taxes_to_accounts_view.taxes_rate < ', $account_taxes_search['taxes_rate']);
                        break;
                    case "5":
                        $this->db->where('taxes_to_accounts_view.taxes_rate >= ', $account_taxes_search['taxes_rate']);
                        break;
                    case "6":
                        $this->db->where('taxes_to_accounts_view.taxes_rate <= ', $account_taxes_search['taxes_rate']);
                        break;
                }
            }

            $amount_operator = $account_taxes_search['taxes_amount_operator'];

            if (!empty($account_taxes_search['taxes_amount'])) {
                switch ($amount_operator) {
                    case "1":
                        $this->db->where('taxes_to_accounts_view.taxes_amount', $account_taxes_search['taxes_amount']);
                        break;
                    case "2":
                        $this->db->where('taxes_to_accounts_view.taxes_amount <>', $account_taxes_search['taxes_amount']);
                        break;
                    case "3":
                        $this->db->where('taxes_to_accounts_view.taxes_amount > ', $account_taxes_search['taxes_amount']);
                        break;
                    case "4":
                        $this->db->where('taxes_to_accounts_view.taxes_amount < ', $account_taxes_search['taxes_amount']);
                        break;
                    case "5":
                        $this->db->where('taxes_to_accounts_view.taxes_amount >= ', $account_taxes_search['taxes_amount']);
                        break;
                    case "6":
                        $this->db->where('taxes_to_accounts_view.taxes_amount <= ', $account_taxes_search['taxes_amount']);
                        break;
                }
            }


            $description_operator = $account_taxes_search['taxes_contain_operator'];
            if (!empty($account_taxes_search['taxes_contain'])) {
                switch ($description_operator) {
                    case "1":
                        $this->db->like('taxes_to_accounts_view.taxes_description', $account_taxes_search['taxes_contain']);
                        break;
                    case "2":
                        $this->db->not_like('taxes_to_accounts_view.taxes_description', $account_taxes_search['taxes_contain']);
                        break;
                    case "3":
                        $this->db->where('taxes_to_accounts_view.taxes_description', $account_taxes_search['taxes_contain']);
                        break;
                    case "4":
                        $this->db->where('taxes_to_accounts_view.taxes_description <>', $account_taxes_search['taxes_contain']);
                        break;
                }
            }
            if ($this->session->userdata['logintype'] == 1 || $this->session->userdata['logintype'] == 5) {
                $tax_invoice_type = $account_taxes_search['taxes_invoice_type'];
                if (!empty($tax_invoice_type)) {
                    $myflag = true;
                    switch ($tax_invoice_type) {
                        case "1":
                            $this->db->where('accounts.number', $this->session->userdata['accountinfo']['number']);
                            break;
                        case "2":
                            $this->db->where('accounts.reseller', $this->session->userdata['accountinfo']['number']);
                            break;
                    }
                }
            }
        }

        if ($this->session->userdata['logintype'] == 1 && $myflag == FALSE) {
//                $account_number = $this->session->userdata['accountinfo']['number'];
//                $this->db->where('accounts.reseller',$account_number);
//                $this->db->or_where('accounts.number',$account_number);
            $this->db->where('accounts.number', $this->session->userdata['accountinfo']['number']);
        }


        $this->db->select('*,(select number from accounts where accounts.accountid = taxes_to_accounts_view.accountid) as number ');
        $this->db->limit($limit, $start);
        $this->db->order_by("taxes_priority");
        $this->db->from('taxes_to_accounts_view', 'accounts');
        $this->db->join('accounts', 'accounts.accountid = taxes_to_accounts_view.accountid');
        $query = $this->db->get();

        return $query;
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
     * -------Here we write code for model accounting functions remove_account_tax------
     * for remove account taxes enteries from database.
     * *@id = account id
     */
    function remove_account_tax($id) {
        $this->db->where('id', $id);
        $this->db->delete('taxes_to_accounts');
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
     * -------Here we write code for model accounting functions get_account_number------
     * this function write to get account number for specific account id.
     * @id = accountid
     */
    function get_account_number($id) {
        $this->db->select('number');
        $this->db->where("accountid", $id);
        $query = $this->db->get("accounts");

        if ($query->num_rows() > 0)
            return $query->row_array();
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

    /**
     * -------Here we write code for model accounting functions getAccount_invoiceList------
     * this function write to get invoice list from database
     */
    function getAccount_invoiceList($start, $limit) {
        $myflag = false;
        if ($this->session->userdata('advance_search') == 1) {

            $invoice_search = $this->session->userdata('invoice_search');

            if (!empty($invoice_search['account_number'])) {
                $this->db->where('accounts.number', $invoice_search['account_number']);
            }
            if (!empty($invoice_search['invoice_date'])) {
                $this->db->where('invoice_list_view.date ', $invoice_search['invoice_date']);
            }

            $creditlimit_operator = $invoice_search['creditlimit_operator'];
            if (!empty($invoice_search['creditlimit'])) {
                switch ($creditlimit_operator) {
                    case "1":
                        $this->db->where('invoice_list_view.value', $invoice_search['creditlimit']);
                        break;
                    case "2":
                        $this->db->where('invoice_list_view.value <>', $invoice_search['creditlimit']);
                        break;
                    case "3":
                        $this->db->where('invoice_list_view.value > ', $invoice_search['creditlimit']);
                        break;
                    case "4":
                        $this->db->where('invoice_list_view.value < ', $invoice_search['creditlimit']);
                        break;
                    case "5":
                        $this->db->where('invoice_list_view.value >= ', $invoice_search['creditlimit']);
                        break;
                    case "6":
                        $this->db->where('invoice_list_view.value <= ', $invoice_search['creditlimit']);
                        break;
                }
            }
            if ($this->session->userdata['logintype'] == 1 || $this->session->userdata['logintype'] == 5) {
                $invoice_type = $invoice_search['invoice_type'];
                if (!empty($invoice_type)) {
                    $myflag = true;
                    switch ($invoice_type) {
                        case "1":
                            $this->db->where('accounts.number', $this->session->userdata['accountinfo']['number']);
                            break;
                        case "2":
                            $this->db->where('accounts.reseller', $this->session->userdata['accountinfo']['number']);
                            break;
                    }
                }
            }
        }

        if ($this->session->userdata['logintype'] == 1 && $myflag == false) {
//                $account_number = $this->session->userdata['accountinfo']['number'];
//                $this->db->where('accounts.reseller',$account_number);
//                $this->db->or_where('accounts.number',$account_number);
            $this->db->where('accounts.number', $this->session->userdata['accountinfo']['number']);
        }

        $this->db->select('*,(select accounts.number from accounts where accounts.accountid = invoice_list_view.accountid) as number');
        $this->db->from('invoice_list_view', 'accounts');
        $this->db->order_by('invoice_list_view.accountid');
        $this->db->join('accounts', 'accounts.accountid = invoice_list_view.accountid');
        $query = $this->db->get();

        return $query;
    }

}
