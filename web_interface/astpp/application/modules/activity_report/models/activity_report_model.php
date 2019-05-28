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
class Activity_report_model extends CI_Model
{

    function activity_report_model()
    {
        parent::__construct();
    }

    function getcustomer_payment_list($flag, $start, $limit)
    {
        $this->db_model->build_search('cdr_payment_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $where = array(
                "payment_by" => $accountinfo["id"]
            );
        } else {
            $where = '';
        }
        if ($flag) {
            $query = $this->db_model->select("*", "payments", $where, "payment_date", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "payments", $where);
        }

        return $query;
    }

    function getcdrs_list($flag, $start, $limit, $accountid = "")
    {
        $start_date = date("Y-m-d") . " 00:00:01";
        $end_date = date("Y-m-d") . " 23:59:59";
        $this->db->limit(100);
        if ($accountid == "") {
            $account_data = $this->session->userdata("accountinfo");
            $where = array(
                "accountid" => $account_data["id"],
                'callstart >= ' => $start_date,
                'callstart <=' => $end_date
            );
        } else {
            $where = array(
                "accountid" => $accountid,
                'callstart >= ' => $start_date,
                'callstart <=' => $end_date
            );
        }
        $this->db_model->build_search('customer_cdr_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }
        return $query;
    }

    function getcustomer_report_list($flag, $start, $limit, $export = true)
    {
        $this->db_model->build_search('activity_search');
        $start_date = date('Y-m-d', strtotime("-1 days")) . " 00:00:01";
        $end_date = date('Y-m-d', strtotime("-1 days")) . " 23:59:59";
        $this->db_model->build_search('customer_cdr_list_search');
        $this->db->group_by('accountid');
        $account_data = $this->session->userdata("accountinfo");
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");

            $where = array(
                "reseller_id" => $account_data['id'],
                'type' => '0'
            );
        } else {
            $where = '';
        }
        if ($flag) {
            $query = $this->db_model->select("MAX(callstart) as callstart ,accountid", "cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }
        return $query;
    }

    function getcustomercdrs_user($flag, $start = 0, $limit = 0, $export = true)
    {
        $start_date = date("Y-m-d") . " 00:00:01";
        $end_date = date("Y-m-d") . " 23:59:59";
        $account_data = $this->session->userdata("accountinfo");
        if ($this->session->userdata('advance_search') != 1) {
            $where = array(
                "accountid" => $account_data['id'],
                'callstart >= ' => $start_date,
                'callstart <=' => $end_date
            );
        } else {
            $where = array(
                "accountid" => $account_data['id']
            );
        }

        $this->db_model->build_search('customer_cdr_list_search');
        $this->db->where($where);
        $this->db->from('cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {

            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        } else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getcharges_list($flag, $start = 0, $limit = 0)
    {
        $accountinfo = $this->session->userdata('accountinfo');
        $reseller_id = $accountinfo['id'];
        if ($accountinfo['type'] == 1) {
            $where['reseller_id'] = $reseller_id;
        } else {
            $where = array();
        }
        if ($this->session->userdata('advance_search') != 1) {
            $where['created_date >='] = gmdate("Y-m-01 00:00:01");
            $where['created_date <='] = gmdate("Y-m-d 23:59:59");
        }
        $this->db_model->build_search('charges_list_search');
        if ($this->session->userdata('advance_search') != 1) {
            $select = '*';
        } else {
            if ($this->session->userdata('group_by_search') != 456) {
                $select = "sum(debit) as debit,sum(credit) as credit,sum(before_balance) as before_balance,sum(after_balance) as after_balance,accountid,item_type,reseller_id,created_date,invoiceid,description";
            } else {
                $select = '*';
            }
        }
        $this->db->where($where);
        $whr = "( invoiceid =0 OR invoiceid in (  select id  from invoices where confirm = 1) )";
        $this->db->where($whr);
        if ($flag) {
            $query = $this->db_model->select($select, "invoice_details", "", "id", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "invoice_details", "");
        }
        return $query;
    }
}
