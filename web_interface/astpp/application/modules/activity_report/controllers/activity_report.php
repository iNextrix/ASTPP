<?php

// ##########################################################################
// ASTPP - Open Source Voip Billing
// Copyright (C) 2004, Aleph Communications
//
// Contributor(s)
// "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details..
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>
// ###########################################################################
class activity_report extends MX_Controller
{

    function activity_report()
    {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library("activity_report_form");
        $this->load->library('astpp/form');
        $this->load->library('astpp/form', 'activity_report_form');
        $this->load->model('activity_report_model');
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->load->library('ASTPP_Sms');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function activity_report_list()
    {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Activity Report';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->activity_report_form->build_activity_report_list_for_admin();
        $data["grid_buttons"] = $this->activity_report_form->build_grid_activity_report_buttons();
        $data['form_search'] = $this->form->build_seraching_form($this->activity_report_form->get_activity_report_form());
        $this->load->view('view_customer_report_list', $data);
    }

    function activity_report_list_json()
    {
        $json_data = array();
        $count_all = $this->activity_report_model->getcustomer_report_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->activity_report_model->getcustomer_report_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $balance = $entity_type = $this->common->get_field_name('balance', 'accounts', array(
                'id' => $value['accountid']
            ));
            $credit_limit = $entity_type = $this->common->get_field_name('credit_limit', 'accounts', array(
                'id' => $value['accountid']
            ));
            $start_date = date('Y-m-d', strtotime("-1 days")) . " 00:00:01";
            $end_date = date('Y-m-d', strtotime("-1 days")) . " 23:59:59";
            $result = $this->db_model->getSelect("count(*) as total_count,sum(cost) as cost_total,sum(debit) as debit_total,", "cdrs", array(
                'accountid' => $value['accountid'],
                "callstart >" => $start_date,
                "callstart <" => $end_date
            ));
            $result = $result->result_array();
            if ($result[0]['total_count'] > 0) {
                $debit = $result[0]['debit_total'];
                $cost = $result[0]['cost_total'];
                $debit = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $debit);
                $cost = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $cost);
            } else {
                $prev_date = date('Y-m-d', strtotime($value['callstart'])) . " 00:00:01";
                $next_date = date('Y-m-d', strtotime($value['callstart'])) . " 23:59:59";
                $result = $this->db_model->getSelect("count(*) as total_count,sum(cost) as cost_total,sum(debit) as debit_total,", "cdrs", array(
                    'accountid' => $value['accountid'],
                    "callstart >" => $prev_date,
                    "callstart <" => $next_date
                ));
                $result = $result->result_array();
                $debit = $result[0]['debit_total'];
                $cost = $result[0]['cost_total'];
                $debit = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $debit);
                $cost = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $cost);
            }

            $this->db->select('calltype,callstart');
            $this->db->from('cdrs');
            $this->db->where('accountid', $value['accountid']);
            $this->db->where('calltype', 'DID');
            $this->db->order_by("callstart", "DESC");
            $did_calltype = $this->db->get();
            $did_calltype = $did_calltype->result_array();
            foreach ($did_calltype as $did_calltype_value) {
                $some_time = strtotime($did_calltype_value['callstart']); // outputs a UNIX TIMESTAMP
                $time_diff = (time() - $some_time);
                $time = '';
                $time .= 'Last call ';

                if ($time_diff > 86400) {
                    $time .= round($time_diff / 86400) . " days ";
                } else if ($time_diff > 3600) {
                    $time .= round($time_diff / 3600) . " hours ";
                } else {
                    $time .= round($time_diff / 60) . " minutes ";
                }
                $time .= "ago";
            }

            $this->db->select('calltype,callstart');
            $this->db->from('cdrs');
            $this->db->where('accountid', $value['accountid']);
            $this->db->where('calltype !=', 'DID');
            $this->db->order_by("callstart", "DESC");
            $standard_calltype = $this->db->get();
            $standard_calltype = $standard_calltype->result_array();
            foreach ($standard_calltype as $standard_calltype_value) {
                $some_time = strtotime($standard_calltype_value['callstart']);
                $time_diff = (time() - $some_time);
                $standard_time = '';
                $standard_time .= 'Last call ';

                if ($time_diff > 86400) {
                    $standard_time .= round($time_diff / 86400) . " days ";
                } else if ($time_diff > 3600) {
                    $standard_time .= round($time_diff / 3600) . " hours ";
                } else {
                    $standard_time .= round($time_diff / 60) . " minutes ";
                }
                $standard_time .= "ago";
            }

            $json_data['rows'][] = array(
                'cell' => array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts", $value['accountid']),
                    $debit,
                    $cost,
                    $time,
                    $standard_time,
                    // "",
                    $value['callstart'],
                    $this->common_model->calculate_currency($balance),
                    $this->common_model->calculate_currency($credit_limit)
                )
            );
        }
        echo json_encode($json_data);
    }

    function activity_report_list_search()
    {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['reseller_id']);
            unset($action['action']);

            unset($action['advance_search']);
            $this->session->set_userdata('activity_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/admin_list/');
        }
    }

    function activity_report_clearsearchfilter()
    {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('activity_search', "");
    }

    function activity_report_export_cdr_xls()
    {
        $query = $this->activity_report_model->getcustomer_report_list(true, "", false);
        $customer_array = array();
        $customer_array[] = array(
            "Account",
            "Debit",
            "Cost",
            "Last Call",
            "Last Call Date",
            "Balance",
            "Credit Limit"
        );
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $value) {
                $balance = $entity_type = $this->common->get_field_name('balance', 'accounts', array(
                    'id' => $value['accountid']
                ));
                $credit_limit = $entity_type = $this->common->get_field_name('credit_limit', 'accounts', array(
                    'id' => $value['accountid']
                ));

                $start_date = date('Y-m-d', strtotime("-1 days")) . " 00:00:01";
                $end_date = date('Y-m-d', strtotime("-1 days")) . " 23:59:59";
                $result = $this->db_model->getSelect("count(*) as total_count,sum(cost) as cost_total,sum(debit) as debit_total,", "cdrs", array(
                    'accountid' => $value['accountid'],
                    "callstart >" => $start_date,
                    "callstart <" => $end_date
                ));
                $result = $result->result_array();
                if ($result[0]['total_count'] > 0) {
                    $debit = $result[0]['debit_total'];
                    $cost = $result[0]['cost_total'];
                    $debit = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $debit);
                    $cost = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $cost);
                } else {
                    $prev_date = date('Y-m-d', strtotime($value['callstart'])) . " 00:00:01";
                    $next_date = date('Y-m-d', strtotime($value['callstart'])) . " 23:59:59";
                    $result = $this->db_model->getSelect("count(*) as total_count,sum(cost) as cost_total,sum(debit) as debit_total,", "cdrs", array(
                        'accountid' => $value['accountid'],
                        "callstart >" => $prev_date,
                        "callstart <" => $next_date
                    ));
                    $result = $result->result_array();
                    $debit = $result[0]['debit_total'];
                    $cost = $result[0]['cost_total'];
                    $debit = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $debit);
                    $cost = 'Last day used ' . $this->common->convert_to_currency("first_name,last_name,number", "accounts", $cost);
                }
                $some_time = strtotime($value['callstart']);
                $time_diff = (time() - $some_time);
                $time = '';
                $time .= 'Last call ';
                if ($time_diff > 86400) {
                    $time .= round($time_diff / 86400) . " days ";
                } else if ($time_diff > 3600) {
                    $time .= round($time_diff / 3600) . " hours ";
                } else {
                    $time .= round($time_diff / 60) . " minutes ";
                }
                $time .= "ago";
                $customer_array[] = array(
                    $this->common->build_concat_string("first_name,last_name,number", "accounts", $value['accountid']),
                    $debit,
                    $cost,
                    $time,
                    $value['callstart'],
                    $this->common_model->calculate_currency($balance),
                    $this->common_model->calculate_currency($credit_limit)
                );
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Activity_report_' . date("Y-m-d") . '.csv');
    }
}

?>

