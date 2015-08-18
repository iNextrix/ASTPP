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
class DID_model extends CI_Model {

    function DID_model() {
        parent::__construct();
    }

    function add_did($add_array) {
        unset($add_array["action"]);
 	$add_array['assign_date']=gmdate('Y-m-d H:i:s');
        $this->db->insert("dids", $add_array);


        return true;
    }


    function insert_pricelist() {
        $insert_array = array('name' => 'default', 'markup' => '', 'inc' => '');
        return $this->db->insert_id();
    }

    function edit_did($data, $id,$number) {
   // echo '<pre>'; print_r($data); exit;
        unset($data["action"]);
        $this->db->where("number", $number);
        $this->db->where("id", $id);
       return $this->db->update("dids", $data);
       //echo $this->db->last_query(); exit;
    }

    function getdid_list($flag, $start = 0, $limit = 0) {
	    $this->db_model->build_search('did_list_search');
	    if($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5)
	    {
	      if($this->session->userdata["accountinfo"]['reseller_id'] != 0)
	      {
		$parent_id = $this->session->userdata["accountinfo"]['reseller_id'];
	      }else{
		$parent_id = 0;
	      }
	      $where = array('reseller_id' => $this->session->userdata["accountinfo"]['id'],"parent_id"=>$parent_id);
	      if ($flag) {
		  $query = $this->db_model->select("*,note as number,reseller_id as accountid", "reseller_pricing", $where, "note", "desc", $limit, $start);
	      } else {
		  $query = $this->db_model->countQuery("*", "reseller_pricing", $where);
	      }
	      return $query;
	    }
	    else
	    {		
	      if ($flag) {
		$this->db->select('dids.id,dids.connectcost,dids.includedseconds,dids.number,dids.extensions,dids.call_type,dids.country_id,dids.inc,dids.cost,dids.setup,dids.monthlycost,dids.status,(CASE when parent_id > 0 THEN (SELECT reseller_id as accountid from reseller_pricing where dids.number=reseller_pricing.note AND reseller_pricing.parent_id=0) ELSE dids.accountid END ) as accountid');
		$query=$this->db->get('dids');
	      } else {
		$query = $this->db_model->countQuery("*", "dids");
	      }
		return $query;
	    }
  }
    function remove_did($id) {
        $this->db->where("id", $id);
        $this->db->delete("dids");
        return true;
    }

    function get_coutry_id_by_name($field_value) {
        $this->db->where("country", ucfirst($field_value));
        $query = $this->db->get('countrycode');
        $data = $query->result();
        if ($query->num_rows > 0)
            return $data[0]->id;
        else
            return '';
    }

    function bulk_insert_dids($field_value) {
        $this->db->insert_batch('dids', $field_value);
        $affected_row = $this->db->affected_rows();
        return $affected_row;
    }

    function get_account($accountdata) {
        $q = "SELECT * FROM accounts WHERE number = '" . $this->db->escape_str($accountdata) . "' AND status = 0";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        $q = "SELECT * FROM accounts WHERE cc = '" . $this->db->escape_str($accountdata) . "' AND status = 0";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        $q = "SELECT * FROM accounts WHERE accountid = '" . $this->db->escape_str($accountdata) . "' AND status = 0";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        return NULL;
    }

    function get_did_reseller_new($did, $reseller_id = "") {
        $sql = "SELECT dids.number AS number, "
                . "reseller_pricing.monthlycost AS monthlycost, "
                . "reseller_pricing.prorate AS prorate, "
                . "reseller_pricing.setup AS setup, "
                . "reseller_pricing.cost AS cost, "
                . "reseller_pricing.connectcost AS connectcost, "
                . "reseller_pricing.includedseconds AS includedseconds, "
                . "reseller_pricing.inc AS inc, "
                . "reseller_pricing.disconnectionfee AS disconnectionfee, "
                . "dids.provider_id AS provider_id, "
                . "dids.country_id AS country_id, "
                . "dids.city AS city, "
                . "dids.province AS province, "
                . "dids.extensions AS extensions, "
                . "dids.accountid AS account, "
                . "dids.variables AS variables, "
                . "dids.options AS options, "
                . "dids.maxchannels AS maxchannels, "
                . "dids.chargeonallocation AS chargeonallocation, "
                . "dids.allocation_bill_status AS allocation_bill_status, "
                . "dids.limittime AS limittime, "
                . "dids.dial_as AS dial_as, "
                . "dids.status AS status "
                . "FROM dids, reseller_pricing "
                . "WHERE dids.id = " . $did
                . " AND reseller_pricing.type = '1' AND reseller_pricing.reseller_id = "
                . $reseller_id;
//                . " AND reseller_pricing.note = "
//                . $did

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        //return $this->db_get_arrays($sql);
    }

    function get_did_by_number($number) {
        $this->db->where("id", $number);
        $this->db->or_where("number", $number);
        $query = $this->db->get("dids");
// 	echo $this->db->last_query();exit;
        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }
    
//   function edit_reseller_pricing($post)
//   {
//     
//   }

   function edit_did_reseller($did_id,$post) {
        $accountinfo = $this->session->userdata('accountinfo');

        $where_array=array('reseller_id' => $accountinfo['id'], 'note' => $post['number'], 'type' => '1');
	$this->db->where($where_array);
	$flag='0';
        $query = $this->db->get('reseller_pricing');
//         echo $query->num_rows();
//         exit;
        if($query->num_rows() > 0){
// 	  $this->delete_pricing_reseller($accountinfo['id'], $post['number']);
	  $flag='1';
        }
        
        $this->insert_reseller_pricing($accountinfo, $post);
//         $this->update_dids_reseller($post);

// 	$query_pricelist = $this->db_model->getSelect("*", "pricelists", array('name' => $accountinfo['number']));
//         if ($query_pricelist->num_rows > 0) {
//             $result_pricelist = $query_pricelist->result_array();
//             $pricelist_id = $result_pricelist[0]['id'];
//         }

// 	$this->delete_routes($accountinfo['number'], $post['number'], $pricelist_id);
//         $this->insert_routes($post, $pricelist_id,$accountinfo['id']);
        return $flag;
    }

    function delete_pricing_reseller($username, $number) {
        $where = array('reseller_id' => $username, 'note' => $number, 'type' => '1');
        $this->db->where($where);
        $this->db->delete('reseller_pricing');
        return true;
    }

function insert_reseller_pricing($accountinfo, $post) {
        $insert_array = array('reseller_id' => $accountinfo['id'], 'type' => '1', 'note' => $post['number'],
            'parent_id'=>$accountinfo['reseller_id'],
            'monthlycost' => $post['monthlycost'],
            'prorate' => $post['prorate'],
            'setup' => $post['setup'],
            'cost' => $post['cost'],
            'inc' => $post['inc'],
            'extensions'=>$post['extensions'],
            'call_type'=>$post['call_type'],
            'disconnectionfee' => $post['disconnectionfee'],
            'connectcost' => $post['connectcost'],
            'includedseconds' => $post['includedseconds'],
            'status' => '0',
	    'assign_date'=>gmdate('Y-m-d H:i:s'));

        $this->db->insert('reseller_pricing', $insert_array);
        return true;
    }

    function update_dids_reseller($post) {
        $where = array('id' => $post['did_id']);
        $update_array = array('dial_as' => $post['dial_as'], 'extensions' => $post['extension']);
        $this->db->where($where);
        $this->db->update('dids', $update_array);
    }

    function delete_routes($id, $number, $pricelist_id) {
        $number = "^" . $number . ".*";
        $where = array('pricelist_id' => $pricelist_id, 'pattern' => $number);
        $this->db->where($where);
        $this->db->delete('routes');
    }

    function insert_routes($post, $pricelist_id) {
        $commment = "DID:" . $post['country'] . "," . $post['province'] . "," . $post['city'];
        $insert_array = array('pattern' => "^" . $post['number'] . ".*", 'comment' => $commment, 'pricelist_id' => $pricelist_id,
            'connectcost' => $post['connectcost'], 'includedseconds' => $post['included'], 'cost' => $post['cost'], 'inc' => $post['inc']);
        $this->db->insert('routes', $insert_array);
        return true;
    }

   function remove_did_pricing($array_did, $reseller_id) {
        $reseller_ids=$this->common->subreseller_list($reseller_id);
        $where="note = ".$array_did['number']." AND reseller_id IN ($reseller_ids) OR  note= ".$array_did['number']." AND parent_id IN ($reseller_ids)";
        $this->db->where($where);
        $this->db->delete('reseller_pricing');
        $accountinfo=$this->session->userdata('accountinfo');
        $reseller_id =$accountinfo['type'] != 1 ? 0 : $accountinfo['reseller_id'];
	$update_array = array('accountid'=>"0",'parent_id'=>$reseller_id);
        $where_dids = array("number" => $array_did['number']);
        $where_dids='number = '.$array_did['number']." AND parent_id IN ($reseller_ids)";
        $this->db->where($where_dids);
        $this->db->update('dids', $update_array);
        return true;
    }
    function add_invoice_data($accountid,$charge_type,$description,$credit)
    {
	$insert_array = array('accountid' => $accountid, 
			      'charge_type' => $charge_type, 
			      'description' => $description,
			      'credit' => $credit,
			      'charge_id' => '0',
			      'package_id' => '0'
			    );

        $this->db->insert('invoice_item', $insert_array);
        return true;
    }
    function check_unique_did($number)
    {

      $where=array('number'=>$number);
      $query = $this->db_model->countQuery("*", "dids", $where);
      return $query;
    }
   

}
