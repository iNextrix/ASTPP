<?php

class Auth_model extends CI_Model {

    function Auth_model() {
        parent::__construct();
    }
    /**
     * -------Here we write code for model auth_model functions verify_login------
     * Purpose: Validate Login Name and Password.
     * @param $username,$password.
     * @return If login user name and password is valid then return true else return false.
     */
    function verify_login($username, $password) {
        $q = "SELECT COUNT(*) as cnt FROM accounts WHERE (number = '".$this->db->escape_str($username)."'";
	$q .= " OR email = '".$this->db->escape_str($username)."')";
        $q .= " AND password = '".$this->db->escape_str($password)."'";
        $q .= " AND status = 1 AND type IN (1,2,3,4,5,0)";

        $query = $this->db->query($q);
        //echo $this->db->last_query();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            if ($row->cnt > 0) {
                $this->session->set_userdata('user_name', $username);
                return 1;
            } else {
                return 0;
            }
        }

        return 0;
    }
}

//end class
?>
