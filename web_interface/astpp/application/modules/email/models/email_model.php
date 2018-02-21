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
class Email_model extends CI_Model {

    function Email_model() {
        parent::__construct();
    }


    function get_email_list($flag, $start = 0, $limit = 0) {
	$account_data = $this->session->userdata("accountinfo");
	$account_id = $account_data['id'];
	$account_email = $account_data['email'];
	$account_type = $account_data['type'];
	if($account_type == 0){
		$this->db->where('accountid',$account_id);	
	}
	if($account_type == 1){
	  $response=$this->db_model->level_reseller($account_id,'id','reseller_id','','');
	  $this->db->where_in('reseller_id',$response);
          $this->db->select('id');
 //         $this->db->select('notify_email');
          $email_address=$this->db->get('accounts');
          $email_address=$email_address->result_array();

	  if(empty($email_address)){
		$this->db->or_where('accountid',0);

	  }else{
//	  $notify_email = $email_address[0]['notify_email'];
          foreach($email_address as $value){
		$value = $value;
		$this->db->or_where('accountid',$value['id']);
	   }
	}
//	  $this->db->or_where('to',$notify_email);
	  }
            $this->db_model->build_search('email_search_list');
          if ($flag) {
            
            $query = $this->db_model->select("*", "mail_details", '', "id", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "mail_details",'');
        }
//echo $this->db->last_query(); exit;
        return $query;
    }

    function add_email($add_array) {
        $this->db->insert("mail_details", $add_array);
        return true;
    }

    function remove_email($id) {
        $this->db->where("id", $id);
        $this->db->delete("mail_details");
        return true;
    }


    function edit_email($data, $id) {
        $this->db->where("id", $id);
        $this->db->update("mail_details", $data);
    }
   function customer_get_email_list($flag,$accountid, $start = 0, $limit = 0) {

	  $this->db->where('accountid',$accountid);
       	    if ($flag) {
            $query = $this->db_model->select("*", "mail_details", '', "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "mail_details", '');
        }


        return $query;
    }
    function get_email_client_data($data, $start = 0, $limit = 0){
	if($data['type'] == ''){
		$where = array('pricelist_id'=>$data['pricelist_id'],'posttoexternal'=>$data['posttoexternal'],'status'=>$data['status'],'deleted'=>'0');
	}
	else{
		$where = array('pricelist_id'=>$data['pricelist_id'],'posttoexternal'=>$data['posttoexternal'],'status'=>$data['status'],'type'=>$data['type'],'deleted'=>'0');
	}
	
        $query = $this->db_model->getSelect("email,id", "accounts", $where);
        $query = $query->result_array();
	return $query;	
    }
    /*Mass mail*/
    function multipal_email($data){
       $mail_ids=explode(',',$data['to']);
	foreach($mail_ids as $key=>$val)
	{
		if($val!='')
		{
			$template_type['message']=$data['template'];
			$template_type['subject']=$data['subject'];
			$act_details = $this->db_model->getSelect("*", "accounts", array('email'=>$val,"deleted"=>0));
			$count=$act_details->num_rows();
			$account_info=array();
			if($count<=0)
			{
				$account_info['email']=$val;
				$account_info['accountid']='0';
			}
			else{
				$res_data = $act_details->result_array();
				$account_info=$res_data[0];
				$account_info['accountid']=$account_info['id'];
			}
			$this->email_lib->send_email($template_type,$account_info,'',$data['file'],0,1);
		}
	}
        return true;
   }
	/*************************************************************/
}

