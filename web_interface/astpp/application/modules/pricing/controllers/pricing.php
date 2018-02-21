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
class pricing extends CI_Controller {

    function pricing() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("pricing_form");
        $this->load->library('astpp/form');
        $this->load->model('pricing_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function price_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'Create Rate Group';
        $data['page_title'] = 'Create Rate Group';
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), '');

        $this->load->view('view_price_add_edit', $data);
    }

    function price_edit($edit_id = '') {
        $data['page_title'] = 'Edit Rate Group';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", " pricelists", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
	$routing_data = $this->db_model->getSelect("trunk_id", "routing",array("pricelist_id"=>$edit_id));
	if($routing_data->num_rows > 0){
	  foreach($routing_data->result_array() as $trunkid){
	    $edit_data["trunk_id"][] = $trunkid["trunk_id"];
	  }
	}
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), $edit_data);
        $this->load->view('view_price_add_edit', $data);
    }

    function price_save() {
        $add_array = $this->input->post();
 
        $data['form'] = $this->form->build_form($this->pricing_form->get_pricing_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Price Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
	  	  $where = array("pricelist_id"=>$add_array['id']);
	  	  $this->db->delete("routing",$where);
		if(isset($add_array['trunk_id']) && $add_array['trunk_id'] != ''){
		  $this->set_force_routing($add_array['id'],$add_array['trunk_id']);
		  unset($add_array['trunk_id']);
		}
                $this->pricing_model->edit_price($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["name"]." rate group updated successfully!"));
                exit;
            }
            $this->load->view('view_price_add_edit', $data);
       }else {
            $data['page_title'] = 'Create Price Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
		if(isset($add_array['trunk_id']) && !empty($add_array['trunk_id']))
                $trunk_id=$add_array['trunk_id'];
                unset($add_array['trunk_id']);
                $priceid=$this->pricing_model->add_price($add_array);
		if(isset($trunk_id) && $trunk_id != ''){
		  $this->set_force_routing($priceid,$trunk_id);
		}
                echo json_encode(array("SUCCESS"=> $add_array["name"]." rate group added successfully!"));
                exit;
            }
        }
    }
    function set_force_routing($priceid,$trunkid){
// 	echo "<pre>".$priceid; print_r($trunkid);
	foreach($trunkid as $id){
	  $routing_arr = array("trunk_id" => $id, "pricelist_id"=>$priceid);
	  $this->db->insert("routing",$routing_arr);
	}
    }
    function price_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('price_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function price_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function price_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Rate Groups';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->pricing_form->build_pricing_list_for_admin();
        $data["grid_buttons"] = $this->pricing_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->pricing_form->get_pricing_search_form());
        $this->load->view('view_price_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function price_list_json() {
        $json_data = array();
        $count_all = $this->pricing_model->getpricing_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->pricing_model->getpricing_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->pricing_form->build_pricing_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function price_delete($pricelist_id) {
        $where = array("id" => $pricelist_id);
        $this->db_model->update("pricelists", array("status" => "2"), $where);
	$this->db->delete("routing",array("pricelist_id"=>$pricelist_id));
        $this->session->set_flashdata('astpp_notification', 'Rate group removed successfully!');
        redirect(base_url() . 'pricing/price_list/');
    }

    function price_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        echo $this->db_model->update("pricelists", array("status" => "2"), $where);
	$where = "pricelist_id IN ($ids)";
	$this->db->delete("routing",$where);

    }

}

?>
 
