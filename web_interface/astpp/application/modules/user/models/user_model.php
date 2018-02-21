<?php

class user_model extends CI_Model {

    function user_model() {
        parent::__construct();
    }

  function validate_password($pass,$id){
        $this->db->select('password');
        $this->db->where('number',$id);
        $query=$this->db->get('accounts');
        $count = $query->num_rows();
        return $count;
    }
    function update_password($newpass,$id){
            $this->db->update('password',$newpass);
            $this->db->where('number',$id);
            $result=$this->db->get('accounts');
            return $result->result();
    }
}
    ?>