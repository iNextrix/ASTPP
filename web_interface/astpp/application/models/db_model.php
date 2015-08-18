<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################

class Db_model extends CI_Model {

    function Db_model() {
//		parent::Model();		
        parent::__construct();
        $this->db->query("SET time_zone='+0:00'");
    }

    /*     * ********************************************************
      Function getCriteria(Where=Condition in Array Format)
     * ******************************************************** */

    function getCriteria($condition = "", $tableName) {
        //print_r($condition);
        if ($condition != "") {
            $this->db->where($condition);
        }
        return $this->db->get($tableName);
    }

    /*     * ********************************************************
      Function save() for addingthe record
     * ******************************************************** */

    function save($tableName, $arr, $val = 'false') {
        $str = $this->db->insert_string($tableName, $arr);
        $rs = $this->db->query($str);
        if ($val == true)
            return $this->db->insert_id();
        else
            return $rs;
    }

    /*     * ********************************************************
      Function update() for editing the record
     * ******************************************************** */

    function update($tableName, $arr, $where) {
        $str = $this->db->update_string($tableName, $arr, $where);
        $rs = $this->db->query($str);
        return $rs;
    }

    /*     * ********************************************************
      Function getSelect()n for displaying record
     * ******************************************************** */

    function getSelect($select, $tableName, $where) {
        $this->db->select($select, false);
        $this->db->from($tableName);
        if ($where != '') {
            $this->db->where($where);
        }

        $query = $this->db->get();

        return $query;
//	  if($query->num_rows > 0){
//	      return $query->result(); 
//	  }else{
//	    return null;
//	  }
    }

    /*     * ********************************************************
      Function getSelectWithOrder()n for displaying record
     * ******************************************************** */

    function getSelectWithOrder($select, $tableName, $where, $order_type, $order_by) {
        $this->db->select($select);
        $this->db->from($tableName);
        $this->db->where($where);
        $this->db->order_by($order_by,$order_type);
        $query = $this->db->get();
        return $query;
    }

    /*     * ********************************************************
      Function getSelectWithOrderAndLimit()n for displaying record
     * ******************************************************** */

    function getSelectWithOrderAndLimit($select, $tableName, $where, $order_type, $order_by, $paging_limit) {
//            echo $paging_limit;
        $this->db->select($select);
        $this->db->from($tableName);

//        if($where != ''){
        $this->db->where($where);
//        }
        $this->db->order_by($order_by, $order_type);
        $this->db->limit($paging_limit);
        $query = $this->db->get();
        return $query;
    }

    /*     * ********************************************************
      Function delete() for deletingthe record
     * ******************************************************** */

    function delete($tableName, $where) {
        $this->db->where($where);
        $this->db->delete($tableName);
    }

    /*     * ********************************************************
      Function excecute() take compelet query
     * ******************************************************** */

    function excecute($query) {
        $rs = $this->db->query($query);
        return $rs;
    }

    /*     * ********************************************************
      Function select() take full complete perms
     * ******************************************************** */
     
    function select($select, $tableName, $where, $order_by, $order_type, $paging_limit = '', $start_limit = '', $groupby = '') {
        $this->db->select($select);
        $this->db->from($tableName);
        if ($where != "") {
            $this->db->where($where);
        }
        if($order_by)
        $this->db->order_by($order_by, $order_type);
        if ($paging_limit)
            $this->db->limit($paging_limit, $start_limit);
        if (!empty($groupby))
            $this->db->group_by($groupby);
        //echo $this->db->query();
        
        $query = $this->db->get();
// echo $this->db->last_query();exit;
        return $query;
    }

    /*     * ********************************************************
      Function select for In query () take full complete perms
     * ******************************************************** */

    function select_by_in($select, $tableName, $where, $order_by, $order_type, $paging_limit, $start_limit, $groupby = '', $key, $where_in) {
        $this->db->select($select);
        $this->db->from($tableName);
        if ($where != "") {
            $this->db->where($where);
        }
        $this->db->where_in($key, $where_in);
        $this->db->order_by($order_by, $order_type);
        if ($paging_limit)
            $this->db->limit($paging_limit, $start_limit);
        if (!empty($groupby))
            $this->db->groupby($groupby);
        //echo $this->db->query();
        $query = $this->db->get();

        return $query;
    }

    /*     * ********************************************************
      Function countQuery() take table name and select feild
     * ******************************************************** */

    function countQuery($select, $table, $where = "") {
        $this->db->select($select);
        if ($where != "") {
            $this->db->where($where);
        }
        $this->db->from($table);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*     * ********************************************************
      Function countQuery for where in query() take table name and select feild
     * ******************************************************** */

    function countQuery_by_in($select, $table, $where = "", $key, $where_in) {
        $this->db->select($select);
        if ($where != "") {
            $this->db->where($where);
        }
        if (!empty($where_in)) {
            $this->db->where_in($key, $where_in);
        }
        $this->db->from($table);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*     * ********************************************************
      Function maxQuery() take table name and select feild
     * ******************************************************** */

    function maxQuery($table, $select, $where = "", $name) {

        $this->db->select($select);
        $this->db->from($table);
        if ($where != "") {
            $this->db->where($where);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rowP = $query->row();
            return $rowP->$name;
        } else {
            return 0;
        }
    }

    /*     * ********************************************************
      Function getCurrent get current value of the feild
     * ******************************************************** */

    function getCurrent($table, $feild, $where) {
//		echo "<pre>table====><br>".$table."field====><br>".$feild."where====><br>".print_r($where);
        $this->db->select($feild);
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {

            $rowP = $query->row();
            return $rowP->$feild;
        } else {
            return false;
        }
    }

    /*     * ********************************************************
      Function getJionQuery get result set on criteria
     * ******************************************************** */

    function getJionQuery($table, $feild, $where = "", $jionTable, $jionCondition, $type = 'inner', $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
        $start = (int) $start;
        $end = (int) $end;
        $this->db->select($feild);
        $this->db->from($table);
        $this->db->join($jionTable, $jionCondition, $type);
        if ($where != "") {
            $this->db->where($where);
        }

        if ($order_type != '' && $order_by != '') {
            $this->db->order_by($order_type, $order_by);
        }

        if ($group_by != '') {
            $this->db->group_by($group_by);
        }

        $this->db->limit($start, $end);

        return $query = $this->db->get();
    }

    /**
      By: Nirav Makwana
      issue: 95
      changes done: Added the function below to get the count of the rows fetched using the Join Query.
     */
    function getJionQueryCount($table, $feild, $where = "", $jionTable, $jionCondition, $type = 'inner', $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
        $start = (int) $start;
        $end = (int) $end;
        $this->db->select($feild);
        $this->db->from($table);
        $this->db->join($jionTable, $jionCondition, $type);
        if ($where != "") {
            $this->db->where($where);
        }

        if ($order_type != '' && $order_by != '') {
            $this->db->orderby($order_type, $order_by);
        }

        if ($group_by != '') {
            $this->db->group_by($group_by);
        }


        $query = $this->db->get();
        return $query->num_rows();
    }

    /** ============================================================================================================================= */
    function getAllJionQuery($table, $feild, $where = "", $jionTable, $jionCondition, $type, $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
        $start = (int) $start;
        $end = (int) $end;
        $this->db->select($feild);
        $this->db->from($table);
        $jion_table_count = count($jionTable);
        for ($i = 0; $i < $jion_table_count; $i++) {
            $this->db->join($jionTable[$i], $jionCondition[$i], $type[$i]);
        }

        if ($where != "") {
            $this->db->where($where);
        }
        if ($order_type != '' && $order_by != '') {
            $this->db->order_by($order_by, $order_type);
        }

        if ($group_by != '') {
            $this->db->group_by($group_by);
        }

        if ($start != '' && $end != '') {
            $this->db->limit($start, $end);
        }

        if ($start != '' && $end == '') {
            $this->db->limit($start);
        }

        return $query = $this->db->get();
    }

    function getCountWithJion($table, $feild, $where = "", $jionTable, $jionCondition, $type, $group_by = '') {
        $this->db->select($feild);
        $this->db->from($table);
        $jion_table_count = count($jionTable);
        for ($i = 0; $i < $jion_table_count; $i++) {
            $this->db->join($jionTable[$i], $jionCondition[$i], $type[$i]);
        }

        if ($where != "") {
            $this->db->where($where);
        }
        if ($group_by != '') {
            $this->db->group_by($group_by);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
//			$rowP = $query->row(); 
//			return $rowP->$feild;		   
        } else {
            return false;
        }
    }

    /*     * ********************************************************
      Function getCurrentWithOrder
     * ******************************************************** */

    function getCurrentWithOrder($table, $feild, $where, $order, $order_by, $limit, $option) {
        $this->db->select($feild);
        $this->db->from($table);
        $this->db->where($where);
        $this->db->order_by($order, $order_by);
        if ($limit != 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rowP = $query->row();
            if ($option == 'yes') {
                return $rowP->$feild;
            } else {
                return $query;
            }
        } else {
            if ($option == 'no') {
                return $query;
            } else {
                return false;
            }
        }
    }

    /*     * ********************************************************
      Function getReferPatients
     * ******************************************************** */

    function getAllWithOrder($table, $feild, $where) {
        $this->db->select($feild);
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $rowP = $query->row();
            return $rowP->$feild;
        } else {
            return false;
        }
    }

    function getCommaSperated($table, $select, $where, $limit, $return_message = FALSE, $message = '') {
        if ($table != '') {
            $this->db->select($select);
            $this->db->from($table);
            $this->db->where($where);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $query = $this->db->get();
            $string = '';
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $rows) {
                    $string .= $rows->$select . ',';
                }

                return substr($string, '', -1);
            } else {
                if ($return_message == FALSE) {
                    return '';
                } else {
                    return $message;
                }
            }
        } else {
            return '';
        }
    }

    function build_concat_dropdown($select, $table, $id_where = '', $id_value = '') {
        $select_params = explode(',', $select);
//            if($id_where == "where_arr"){
        $select_params = explode(',', $select);
        if (isset($select_params[3])) {
            $cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
        } else {
            $cnt_str = " $select_params[1],' (',$select_params[2],')' ";
        }
        $select = $select_params[0] . ", concat($cnt_str) as $select_params[1] ";
        $logintype = $this->session->userdata('logintype');
        if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr') {
            $account_data = $this->session->userdata("accountinfo");
            $id_value['reseller_id'] = $account_data['id'];
        }
        $where = $id_value;
//            }
        $drp_array = $this->getSelect($select, $table, $where);
        $drp_array = $drp_array->result();

        $drp_list = array();
        foreach ($drp_array as $drp_value) {
            $drp_list[$drp_value->$select_params[0]] = $drp_value->$select_params[1];
        }
        return $drp_list;
    }
function build_concat_select_dropdown($select, $table, $id_where = '', $id_value = '') {         
	
        $select_params = explode(',', $select);
//            if($id_where == "where_arr"){
        $select_params = explode(',', $select);
        if (isset($select_params[3])) {
            $cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
        } else {
            $cnt_str = " $select_params[1],' (',$select_params[2],')' ";
        }
        $select = $select_params[0] . ", concat($cnt_str) as $select_params[1] ";
	$where = $id_value;
        $drp_array = $this->getSelect($select, $table, $id_value);
        $drp_array = $drp_array->result();

        $drp_list = array();
        $drp_list[0] = "--Select--";
        foreach ($drp_array as $drp_value) {
            $drp_list[$drp_value->$select_params[0]] = $drp_value->$select_params[1];
        }
        return $drp_list;
    }
    function build_dropdown($select, $table, $id_where = '', $id_value = '') {
        $select_params = explode(',', $select);
        $where = '';
        if(isset($id_value["type"]) && $id_value["type"] == "GLOBAL"){
            $where = "type IN ('0','3')";
            $this->db->where($where);
            unset($id_value["type"]);
        }
        if ($id_where != '' && $id_value != '') {
            if ($id_where == 'group_by') {
                $this->db->group_by($id_value);
            } else if ($id_where == "where_arr") {
                $logintype = $this->session->userdata('logintype');
                if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr' && $this->db->field_exists('reseller_id',$table)) {
                    $id_value['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
                }
                $where = $id_value;
            } else {
                $logintype = $this->session->userdata('logintype');
                if (($logintype == 1 || $logintype == 5) && $id_where == 'reseller_id') {
                    $account_data = $this->session->userdata("accountinfo");
                    $id_value = $account_data['id'];
                }
                $where = array($id_where => $id_value);
            }
        }

        $drp_array = $this->getSelect($select, $table, $where);
        $drp_array = $drp_array->result();

        $drp_list = array();
        foreach ($drp_array as $drp_value) {
            $drp_list[$drp_value->$select_params[0]] = $drp_value->$select_params[1];
        }
        return $drp_list;
    }
  function build_dropdown_deleted($select, $table, $id_where = '', $id_value = '') {
        $select_params = explode(',', $select);
        if(isset($id_value["type"]) && $id_value["type"] == "GLOBAL"){
            $where = "type IN ('0','3')";
            $this->db->where($where);
            unset($id_value["type"]);
        }
        $where = '';
        if ($id_where != '' && $id_value != '') {
            if ($id_where == 'group_by') {
                $this->db->group_by($id_value);

	 } else if ($id_where == "where_arr") {
                $logintype = $this->session->userdata('logintype');
                if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr') {
                    $account_data = $this->session->userdata("accountinfo");
                    $id_value['reseller_id'] = $account_data['id'];
                }
                $where = $id_value;
            } else {
                $logintype = $this->session->userdata('logintype');
                if (($logintype == 1 || $logintype == 5) && $id_where == 'reseller_id') {
                    $account_data = $this->session->userdata("accountinfo");
                    $id_value = $account_data['id'];
                }
                $where = array($id_where => $id_value);
            }
        }

        $drp_array = $this->getSelect($select, $table, $where);

        $drp_array = $drp_array->result();

        $name=explode("as",$select);
        if(isset($name[3])){
            $name=trim($name[3]);
        }else{
            $name=trim($name[1]);
        }

        $drp_list = array();
        $dele =array();
        foreach ($drp_array as $drp_value) {
            $dele=explode("^",$drp_value->$name);
            if(isset($dele[1]))
            {
               $drp_list['Deleted'][$drp_value->$select_params[0]] =  str_replace("^","",$drp_value->$name);
            }else{
               $drp_list['Active'][$drp_value->$select_params[0]] = $drp_value->$name;
            }
        }
	ksort($drp_list);
        return $drp_list;
    }

    function build_search($accounts_list_search) {
        if ($this->session->userdata('advance_search') == 1) {
            $account_search = $this->session->userdata($accounts_list_search);
            unset($account_search["ajax_search"]);
            unset($account_search["advance_search"]);
            if (!empty($account_search)) {
                foreach ($account_search as $key => $value) {
                    if ($value != "") {
                        if (is_array($value)) {
                            if (array_key_exists($key . "-integer", $value)) {
                                $this->get_interger_array($key, $value[$key . "-integer"], $value[$key]);
                            }
                            if (array_key_exists($key . "-string", $value)) {
                                $this->get_string_array($key, $value[$key . "-string"], $value[$key]);
                            }
                            if ($key == 'callstart'|| $key == 'date'|| $key =='payment_date' || $key =='from_date'|| $key =='invoice_date') {
                                $this->get_date_array($key, $value);
                            }
                        } else {
                            $this->db->where($key, $value);
                        }
                    }
                }
		return true;
            }
        }
    }

    function get_date_array($field, $value) {
        if ($value != '') {
            if (!empty($value[0])) {
                $this->db->where($field . ' >= ', gmdate('Y-m-d H:i:s',strtotime($value[0])));
            }
            if (!empty($value[1])) {
                $this->db->where($field . ' <= ', gmdate('Y-m-d H:i:s',strtotime($value[1])));
            }
        }
    }

    function get_interger_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $this->db->where($field, $search_array);
                    break;
                case "2":
                    $this->db->where($field . ' <>', $search_array);
                    break;
                case "3":
                    $this->db->where($field . ' > ', $search_array);
                    break;
                case "4":
                    $this->db->where($field . ' < ', $search_array);
                    break;
                case "5":
                    $this->db->where($field . ' >= ', $search_array);
                    break;
                case "6":
                    $this->db->where($field . ' <= ', $search_array);
                    break;
            }
        }
    }

    function get_string_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $str1 = $field . " LIKE '%$search_array%'";
                    $this->db->where($str1);
                    break;
                case "2":
                    $str1 = $field . " NOT LIKE '%$search_array%'";
                    $this->db->where($str1);
                    break;
                case "3":
                    $this->db->where($field, $search_array);
                    break;
                case "4":
                    $this->db->where($field . ' <>', $search_array);
                    break;
            }
        }
    }

    function get_available_bal($account_info) {
        $available_bal = 0;
        $available_bal = ($account_info["balance"]) + $account_info["posttoexternal"] * ($account_info["credit_limit"]);
        return $available_bal;
    }

    function update_balance($amount, $accountid, $payment_type) {
        if ($payment_type == "debit") {
            $query = 'UPDATE `accounts` SET `balance` = (balance - ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        } else {
            $query = 'UPDATE `accounts` SET `balance` = (balance + ' . $amount . ') WHERE `id` = ' . $accountid;
            return $this->db->query($query);
        }
    }

    function build_batch_update_array($update_array) {
        foreach ($update_array as $key => $update_fields) {
            if (is_array($update_fields)) {
                switch ($update_fields["operator"]) {
                    case "1":
                        //                        $this->db->where($field, $search_array);
                        break;
                    case "2":
                        $this->db->set($key, $update_fields[$key]);
                        break;
                    case "3":
                        $this->db->set($key, $key . "+" . $update_fields[$key], FALSE);
                        break;
                    case "4":
                        $this->db->set($key, $key . "-" . $update_fields[$key], FALSE);
                        break;
                }
            } else {
                if ($update_fields != "")
                    $this->db->set($key, $update_fields);
            }
        }
    }
}

?>
