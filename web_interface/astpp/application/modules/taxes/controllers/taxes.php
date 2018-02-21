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
class Taxes extends CI_Controller {

    function Taxes() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("taxes_form");
        $this->load->library('astpp/form');
        $this->load->model('taxes_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function taxes_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Tax';
        $data['form'] = $this->form->build_form($this->taxes_form->get_taxes_form_fields(), '');

        $this->load->view('view_taxes_add_edit', $data);
    }

    function taxes_edit($edit_id = '') {
        $data['page_title'] = 'Edit Tax ';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "taxes", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $edit_data['taxes_amount'] = $this->common_model->to_calculate_currency($edit_data['taxes_amount'], '', '', false, false);
        $data['form'] = $this->form->build_form($this->taxes_form->get_taxes_form_fields(), $edit_data);
        $this->load->view('view_taxes_add_edit', $data);
    }

    function taxes_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->taxes_form->get_taxes_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Taxes Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['taxes_amount'] = $this->common_model->add_calculate_currency($add_array['taxes_amount'], '', '', false, false);
                $this->taxes_model->edit_tax($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["taxes_description"]." taxes updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Add Taxes';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['taxes_amount'] = $this->common_model->add_calculate_currency($add_array['taxes_amount'], '', '', false, false);
                $this->taxes_model->add_tax($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["taxes_description"]." taxes added successfully!"));
                exit;
            }
        }
    }

    function taxes_delete($id) {
        $this->taxes_model->remove_taxes($id);
        $this->session->set_flashdata('astpp_notification', 'taxes removed successfully!');
        redirect(base_url() . 'taxes/taxes_list/');
    }

    function taxes_list() {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounting | Taxes';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Taxes';
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 2;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->taxes_form->build_charge_list_for_admin();
        $data["grid_buttons"] = $this->taxes_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->taxes_form->get_search_taxes_form());
        $this->load->view('view_taxes_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function taxes_list_json() {
        $json_data = array();
        $count_all = $this->taxes_model->gettax_list(false);
        $paging_data =  $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->taxes_model->gettax_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->taxes_form->build_charge_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function taxes_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            unset($_POST['action']);
            unset($_POST['advance_search']);
            $this->session->set_userdata('taxes_list_search', $this->input->post());
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'taxes/taxes_list/');
        }
    }

    function taxes_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function taxes_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("taxes");
    }

}

?>
 
