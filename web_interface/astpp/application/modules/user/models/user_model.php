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
 function change_password($id) {
			
		 $this->db->select('password');
       		 $this->db->where('id',$id);
       		 $query=$this->db->get('accounts');
			
		$result = $query->result();	
			
		
		return $result;

	}
	function change_db_password($update,$id)
	{			
	
            $this->db->where('id',$id);
	     $this->db->update('accounts',array('password'=>$update));
           //$var = $this->db->last_query();
		//print_r($var);	    
		//exit;     
			
		

	}

}
    ?>
