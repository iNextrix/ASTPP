<?php
###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# Samir Doshi <samir.doshi@inextrix.com>
# ASTPP Version 3.0 and above
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################
class Department extends CI_Controller {
//echo "hello";exit;
    function __construct() {
        parent::__construct();

        $this->load->helper('template_inheritance');
		$this->load->helper('file');
        $this->load->library('session');
        $this->load->library("department_form");
        $this->load->library('astpp/form');
        $this->load->model('department_model');
	$this->load->library('csvreader');
        $this->load->dbutil();

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    
    function department_list() {
	$base_currency = Common_model::$global_config['system_config']['base_currency'];
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = gettext('Department'); 
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 4;
		$this->session->set_userdata('department_search', 0);
        $data['grid_fields'] = $this->department_form->build_department_list_for_admin();
        //~ echo "<pre>"; print_r(json_encode(array()));exit;
        if ($this->session->userdata('logintype') == 2) {
            $data["grid_buttons"] = $this->department_form->build_admin_department_grid_buttons();
        } else {
            $data["grid_buttons"] = json_encode(array());
        }
	$data['form_search'] = $this->form->build_serach_form($this->department_form->get_search_department_form());
        $this->load->view('view_department_list', $data);
    }

    function department_list_json() {
		//echo "hello";exit;
		$json_data = array();
		
        $count_all = $this->department_model->getdepartment_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->department_model->getdepartment_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->department_form->build_department_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
		//print_r($json_data);exit;
        echo json_encode($json_data);
    }

    function department_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
	
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('department_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'department/department_list/');
        }
    }

    function department_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('department_search', "");
    }

    function department_add() {
	$data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = gettext('Add Department');
        $data['drp_down'] =  $this->department_model->drp_downlist();
        $data['drp_downlist_subadmin'] =  $this->department_model->drp_downlist_subadmin();
         //~ $data['form'] = $this->form->build_form($this->department_form->get_department_form_fields(), '');
        $this->load->view('view_department_add', $data);
    }

    function department_list_edit($edit_id = '') {
 	
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "department", $where);
        foreach ($account->result_array() as $key => $value) {
            $department_array= $value;
        }
        
       
		$data['department_array_result']=$department_array;
        $data['drp_down'] =  $this->department_model->drp_downlist();
        $data['drp_downlist_subadmin'] =  $this->department_model->drp_downlist_subadmin();
		$admin_id=explode(',',$department_array['admin_id_list']);
		$subadmin_id=explode(',',$department_array['sub_admin_id_list']);
		$data['admin_user_id']= explode(',',$department_array['admin_id_list']);
		$data['sub_admin_user_id']= explode(',',$department_array['sub_admin_id_list']);
		$data['additional_email_address']=$data['department_array_result']['additional_email_address'];
		
		foreach($admin_id as $key=>$val){
			$data['admin_user_id_data'][$val]=$val;
		}
		
		
		foreach($subadmin_id as $key=>$val){
			$data['subadmin_user_id_data'][$val]=$val;
		}
		 
		 
		
		if(isset($data['additional_email_address']) && $data['additional_email_address'] != '' && $data['additional_email_address'] != ',,,,'){
			$data['email_id_new']= explode(',',$data['additional_email_address']);
			$additional_count= count($data['email_id_new']);
//echo $additional_count; exit;
			for($i=0 ; $i < $additional_count ; $i++){
				$data['email_id_new'][$i]= $data['email_id_new'][$i];
			}
		}
		
		$data['edit_id']=$edit_id;
		//$data['password'] = $this->common->decode($data['password']);
		$data['department_array_result']['smtp_password'] = $this->common->decode($department_array['smtp_password']);
        $data['page_title'] = gettext('Edit Department');
        $this->load->view('view_department_edit', $data);
    }
    

    function department_save() {
	
	//echo "<pre>";print_r($add_array);
//~ change by bansi faldu
//~ issue: #42	
		
		$add_array = $this->input->post();
		
		

		if(isset($add_array['admin_user_id']) && $add_array['admin_user_id'] !=""){
			$admin_id=$add_array['admin_user_id'];
		}else{
			$admin_id= array();
		}
		if(isset($add_array['sub_admin_user_id']) && $add_array['sub_admin_user_id'] !=""){
			$subadmin_id=$add_array['sub_admin_user_id'];
		}else{
			$subadmin_id= array();
		}

		if(isset($add_array['admin_user_id']) && !empty($add_array['admin_user_id'])){
			$add_array['admin_id_list'] = implode(',',$add_array['admin_user_id']);
			unset($add_array['admin_user_id']);
		}
		
		if(isset($add_array['sub_admin_user_id']) && !empty($add_array['sub_admin_user_id'])){
			$add_array['sub_admin_id_list'] = implode(',',$add_array['sub_admin_user_id']);
			unset($add_array['sub_admin_user_id']);
		}
	
	
		foreach($admin_id as $key=>$val){
			$data['admin_user_id_data'][$val]=$val;
		}
		
		
		foreach($subadmin_id as $key=>$val){
			$data['subadmin_user_id_data'][$val]=$val;
		}
		 
	
	
	$add_array['email_id_new'][]=$add_array['email_id_new1'];
	$add_array['email_id_new'][]=$add_array['email_id_new2'];
	$add_array['email_id_new'][]=$add_array['email_id_new3'];
	$add_array['email_id_new'][]=$add_array['email_id_new4'];
	$add_array['email_id_new'][]=$add_array['email_id_new5'];
	unset($add_array['email_id_new1']);
	unset($add_array['email_id_new2']);
	unset($add_array['email_id_new3']);
	unset($add_array['email_id_new4']);
	unset($add_array['email_id_new5']);
	 
	
	
	
	 
	/*if(isset($add_array['email_id_new']) && !empty($add_array['email_id_new'])){
		$add_array['additional_email_address'] = implode(',',$add_array['email_id_new']);
		
		unset($add_array['email_id_new']);
	}*/
	
	$email_address=array();
	if(isset($add_array['email_id_new']) && count($add_array['email_id_new'])>0){
		foreach($add_array['email_id_new'] as $key=>$val){
			$email_address[]=$val;	
			//$add_array['additional_email_address'][]=$email_address;	
		}
	}
	
	unset($add_array['email_id_new']);
	$add_array['additional_email_address']=implode(',', $email_address);
	
	
	
	
	
	   //$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
       
        if (isset($add_array['id']) && $add_array['id'] != '' && $add_array['id']>0) {
			 $data['form'] = $this->form->build_form($this->department_form->get_department_form_fields_edit(), $add_array);
            $data['page_title'] = gettext('Edit Department');
            if ($this->form_validation->run() == FALSE) {
				$data['department_array_result']=$add_array;
				$data['values']= $this->input->post();
				$data['flag'] = 'create';
				$data['page_title'] = gettext('Edit Department');
				$data['drp_down'] =  $this->department_model->drp_downlist();
				$data['drp_downlist_subadmin'] =  $this->department_model->drp_downlist_subadmin();
					$data['validation_errors'] = validation_errors();
            } else {
                $add_array['password'] = $this->common->encode($add_array['password']);
                $add_array['smtp_password'] = $this->common->encode($add_array['smtp_password']);
                
                $this->department_model->edit_department($add_array, $add_array['id']);
				$this->session->set_flashdata('astpp_errormsg', 'Department updated successfully');
				redirect(base_url() . 'department/department_list/');
				exit;
            }
	        $this->load->view('view_department_edit', $data);
        } else {
			 $data['form'] = $this->form->build_form($this->department_form->get_department_form_fields(), $add_array);
            $data['page_title'] = gettext('Create Department');
            if ($this->form_validation->run() == FALSE) {
				$data['values']= $this->input->post();
				$data['flag'] = 'create';
				$data['page_title'] = gettext('Add Department');
				$data['drp_down'] =  $this->department_model->drp_downlist();
				$data['drp_downlist_subadmin'] =  $this->department_model->drp_downlist_subadmin();
				$data['validation_errors'] = validation_errors();
            } else {
                $add_array['password'] = $this->common->encode($add_array['password']);
                $add_array['smtp_password'] = $this->common->encode($add_array['smtp_password']);
                 
                $response = $this->department_model->add_department($add_array);
		$this->session->set_flashdata('astpp_errormsg', 'Department added successfully!');
	        redirect(base_url() . 'department/department_list/');
		exit;
            }
	        $this->load->view('view_department_add', $data);
        }
    }
    
     function department_remove($id) {
        $this->department_model->remove_department($id);
        $this->session->set_flashdata('astpp_notification','Department removed successfully!');
        redirect(base_url() . 'department/department_list/');
    }

    function department_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("department");
    }
    
    

}

?>	
  
