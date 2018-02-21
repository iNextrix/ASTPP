<?php
class Astpp_common extends CI_Model {
    // ------------------------------------------------------------------------
    /**
     * initialises the class inheriting the methods of the class Model 
     *
     * @return Usermodel
     */
    function Astpp_common() {
        parent::__construct();
    }
    /**
     * -------Here we write code for model astpp_common_model functions list_applyable_charges------
     * Purpose: build array for applyable charge dropdown list.
     * @param 
     * @return return array of applyable chargelist.
     */
    function list_applyable_charges() {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = " AND reseller_id = $reseller";
        } else {
            $where = " AND reseller_id = 0";
        }
        $q = "SELECT * FROM charges WHERE status < 2 AND pricelist_id = '' $where";

        $item_arr = array();
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                //$item_arr[] = $row[$colname];
                if ($row['charge'] > 0) {
                    $row['charge'] = $this->common_model->calculate_currency($row['charge']);
                    //$row->{charge} = sprintf( "%.4f", $row->{charge} );
                }
                $item_arr[$row['id']] = $row['description'] . ' - ' . $row['charge'];
            }
        }
        return $item_arr;
    }
    function quote($inp) {
        return "'" . $this->db->escape_str($inp) . "'";
    }
    function db_get_item($q, $colname) {
        $item_arr = array();
        $query = $this->db->query($q);
        //echo $this->db->last_query();	
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row[$colname];
        }
        return '';
    }
    // Return the balance for a specific ASTPP account.
    function accountbalance($account) {
        $debit = 0;
        $q = "SELECT SUM(debit) as val1 FROM cdrs WHERE accountid=" . $this->quote($account) . " AND status NOT IN (1, 2)";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $debit = $row['val1'];
        }
        $credit = 0;
        $q = "SELECT SUM(credit) as val1  FROM cdrs WHERE accountid= " . $this->quote($account) . " AND status NOT IN (1, 2)";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $credit = $row['val1'];
        }
        $posted_balance = 0;
        $q = "SELECT * FROM accounts WHERE id = " . $this->quote($account);
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $posted_balance = $row['balance'];
        }
        $balance = ( $debit - $credit + $posted_balance );
        return $balance;
    }
    function accounts_total_balance($reseller) {
        $debit = 0;
        $credit = 0;
        if ($reseller == "") {
            $q = "SELECT SUM(debit) as val1 FROM cdrs WHERE status NOT IN (1, 2)";
            $debit = $this->db_get_item($q, 'val1');

            $q = "SELECT SUM(credit)  as val1 FROM cdrs WHERE status NOT IN (1, 2)";
            $credit = $this->db_get_item($q, 'val1');

            $tmp = "SELECT SUM(balance) as val1 FROM accounts WHERE reseller_id = ''";
        } else {
            $tmp = "SELECT SUM(balance) as val1 FROM accounts WHERE reseller_id = " . $this->quote($reseller);
        }
        $posted_balance = $this->db_get_item($tmp, "val1");

        $balance = ( $debit - $credit + $posted_balance );
        return $balance;
    }
    function count_dids($test) {
        $tmp = "SELECT COUNT(*) as val1 FROM dids " . $test;
        return $this->db_get_item($tmp, 'val1');
    }

    function count_callingcards($where, $field = 'COUNT(*)') {
        $tmp = "SELECT $field as val FROM callingcards " . $where;
        return $this->db_get_item($tmp, 'val');
    }

    function count_accounts($test) {
        $tmp = "SELECT COUNT(*) as val1 FROM accounts " . $test;
        return $this->db_get_item($tmp, 'val1');
    }

} 
?>
