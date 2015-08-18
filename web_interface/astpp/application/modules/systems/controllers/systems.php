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
class Systems extends CI_Controller {

    function Systems() {
        parent::__construct();

        $this->load->helper('template_inheritance');
	$this->load->helper('file');
        $this->load->library('session');
        $this->load->library("system_form");
        $this->load->library('astpp/form');
        $this->load->model('system_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function configuration_edit($edit_id = '') {
        $data['page_title'] = 'Edit Settings';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "system", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_configuration_form_fields(), $edit_data);
        $this->load->view('view_configuration_add_edit', $data);
    }

    function configuration_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('configuration_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/configuration/');
        }
    }

    function configuration_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }

    function configuration_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->system_form->get_configuration_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Settings';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->system_model->edit_configuration($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "setting updated successfully!"));
                exit;
            }
        }
    }
/*
* Purpose : Changes in setting menu
* Verion 2.1
*/
   /* function configuration() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Settings';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->system_form->build_system_list_for_admin();
        $data["grid_buttons"] = $this->system_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->system_form->get_configuration_search_form());
        $this->load->view('view_configuration_list', $data);
    }*/

    function configuration($group_title='') {
	if($group_title==""){
		redirect(base_url() . '/dashboard');
	}
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Settings';
	$data['group_title']=$group_title;
	$where=array("group_title"=>$group_title);
	$details = $this->db_model->getSelect("*", "system", $where);
	$data['details']=$details->result_array();
	$add_array = $this->input->post();
	//echo '<pre>'; print_r($add_array); exit;
 	if (!empty($add_array)) {

		foreach($add_array as $key=>$val){
			$update_array=array('value'=>$val);
			$this->system_model->edit_configuration($update_array, $key);
		}
            	$this->session->set_flashdata('astpp_errormsg', ucfirst($group_title).' Settings updated sucessfully!');
	    	redirect(base_url() . 'systems/configuration/'.$group_title);
        }else{
	 	$this->load->view('view_systemconf', $data);
	}
    }
/***************************************************************************************/

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function configuration_json() {

        $json_data = array();
        $count_all = $this->system_model->getsystem_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->getsystem_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_system_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function template() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Email Templates';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->system_form->build_template_list_for_admin();
        $data["grid_buttons"] = $this->system_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->system_form->get_template_search_form());
        $this->load->view('view_template_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function template_json() {
        $json_data = array();
        $count_all = $this->system_model->gettemplate_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->gettemplate_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_template_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function template_edit($edit_id = '') {
        $data['page_title'] = 'Edit Email template';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "default_templates", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $edit_data);
        $this->load->view('view_template_add_edit', $data);
    }
/*
* Purpose : changes reseller can edit own email template 
* Verion 2.1
*/
    function template_save() {
        $add_array = $this->input->post();
        
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
             $account_data = $this->session->userdata("accountinfo");
	     $reseller = $account_data['id'];
	     
             $this->resellertemplate_save($add_array,$reseller);
        }
        else
        {
            
            $this->admintemplate_save($add_array);
        }
    }
    function resellertemplate_save($data,$resellerid)
    {
        
        $where = array('name' => $data['name'],'reseller_id'=>$resellerid);
        $count = $this->db_model->countQuery("*", "default_templates", $where);
        $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $data);
        if($count >0)
        {
            $data['page_title'] = 'Edit Template';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                
                $this->system_model->edit_resellertemplate($data, $data['id']);
                $this->session->set_flashdata('astpp_errormsg', 'Template updated successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        } else {
//              echo "<pre>";
//              echo $resellerid;
//              print_r($data);exit; 
            //$data['page_title'] = 'Template Details';
            if ($this->form_validation->run() == FALSE) {
                
                $data['validation_errors'] = validation_errors();
            } else {
                 unset($data['form']);
                 $data['reseller_id'] = $resellerid;
//                  echo "<pre>";print_r($data);exit;
                $this->system_model->add_resellertemplate($data);
                $this->session->set_flashdata('astpp_errormsg', 'Template added successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        }
        $this->load->view('view_trunk_add_edit', $data);   
    }
    function admintemplate_save($data)
    {
        $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $data);
        if ($data['id'] != '') {
            $data['page_title'] = 'Edit Template';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                 unset($data['form']);
                 unset($data['page_title']);
                $this->system_model->edit_template($data, $data['id']);
                $this->session->set_flashdata('astpp_errormsg', 'Template updated successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                 unset($data['form']);
                $this->system_model->add_template($data);
                $this->session->set_flashdata('astpp_errormsg', 'Template added successfully!');
                redirect(base_url() . 'systems/template/');
                exit;
            }
        }
        $this->load->view('view_trunk_add_edit', $data);   
    }
//     function template_save() {
//         $add_array = $this->input->post();
// 
//         $data['form'] = $this->form->build_form($this->system_form->get_template_form_fields(), $add_array);
//         if ($add_array['id'] != '') {
//             $data['page_title'] = 'Edit Template';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $this->system_model->edit_template($add_array, $add_array['id']);
//                 $this->session->set_flashdata('astpp_errormsg', 'Template updated successfully!');
//                 redirect(base_url() . 'systems/template/');
//                 exit;
//             }
//         } else {
//             $data['page_title'] = 'Termination Details';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $this->system_model->add_template($add_array);
//                 $this->session->set_flashdata('astpp_errormsg', 'Template added successfully!');
//                 redirect(base_url() . 'systems/template/');
//                 exit;
//             }
//         }
//         $this->load->view('view_trunk_add_edit', $data);
//     }

    function template_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('template_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/template/');
        }
    }

    function template_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }
    
    // country code =====================================
    function country_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Countries';
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 4;
	$this->session->set_userdata('country_search', 0);
        $data['grid_fields'] = $this->system_form->build_country_list_for_admin();
        if ($this->session->userdata('logintype') == 2) {
            $data["grid_buttons"] = $this->system_form->build_admin_grid_buttons();
        } else {
            $data["grid_buttons"] = json_encode(array());
        }
	$data['form_search'] = $this->form->build_serach_form($this->system_form->get_search_country_form());
        $this->load->view('view_country_list', $data);
    }

    function country_list_json() {
	$json_data = array();
        $count_all = $this->system_model->getcountry_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->system_model->getcountry_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_country_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }
    
    function country_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
	
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('country_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/country_list/');
        }
    }

    function country_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('country_search', "");
    }
    
    function country_add() {
	$data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Country';
        $data['form'] = $this->form->build_form($this->system_form->get_country_form_fields(), '');
        $this->load->view('view_country_add_edit', $data);
    }

    function country_list_edit($edit_id = '') {
 	$data['page_title'] = 'Edit Country';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "countrycode", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_country_form_fields(), $edit_data);
        $this->load->view('view_country_add_edit', $data);
    }
    

    function country_save() {
	$add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->system_form->get_country_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Country';
	    $data['page_title'] = 'Edit Country';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->system_model->edit_country($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["country"]." country updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Country';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $response = $this->system_model->add_country($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["country"]." country added successfully!"));
                exit;
            }
        }
    }
    
     function country_remove($id) {
        $this->system_model->remove_country($id);
        $this->session->set_flashdata('astpp_notification', 'Country removed successfully!');
        redirect(base_url() . 'systems/country_list/');
    }

    function country_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("countrycode");
    }

// currency code =====================================
    function currency_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Currencies';
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 4;
	$this->session->set_userdata('currency_search', 0);
        $data['grid_fields'] = $this->system_form->build_currency_list_for_admin();
        if ($this->session->userdata('logintype') == 2) {
            $data["grid_buttons"] = $this->system_form->build_admin_currency_grid_buttons();
        } else {
            $data["grid_buttons"] = json_encode(array());
        }
	$data['form_search'] = $this->form->build_serach_form($this->system_form->get_search_currency_form());
        $this->load->view('view_currency_list', $data);
    }

    function currency_list_json() {
      $json_data = array();

        $count_all = $this->system_model->getcurrency_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->getcurrency_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_currency_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function currency_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
	
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('currency_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'systems/currency_list/');
        }
    }

    function currency_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('currency_search', "");
    }

    function currency_add() {
	$data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Currency';
        $data['form'] = $this->form->build_form($this->system_form->get_currency_form_fields(), '');
        $this->load->view('view_currency_add_edit', $data);
    }

    function currency_list_edit($edit_id = '') {
 	$data['page_title'] = 'Edit Currency';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "currency", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->system_form->get_currency_form_fields(), $edit_data);
        $this->load->view('view_country_add_edit', $data);
    }
    

    function currency_save() {
	$add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->system_form->get_currency_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Currency';
	    $data['page_title'] = 'Edit Currency';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->system_model->edit_currency($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> $add_array["currency"]." currency updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Currency';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $response = $this->system_model->add_currency($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["currency"]." currency added successfully!"));
                exit;
            }
        }
    }
    
     function currency_remove($id) {
        $this->system_model->remove_currency($id);
        $this->session->set_flashdata('astpp_notification', 'Currency removed successfully!');
        redirect(base_url() . 'systems/currency_list/');
    }

    function currency_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("currency");
    }
    function database_backup()
    {
	$data=array();
	$data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Database backup';
        $data['form'] = $this->form->build_form($this->system_form->get_backup_database_form_fields(), '');
        $this->load->view('view_database_backup', $data);
    }
    function database_backup_save()
    {
	$add_array=$_POST;
	if($add_array['backup_name'] != '' && $add_array['path'] != ''){
	$astpp_config = parse_ini_file("/var/lib/astpp/astpp-config.conf");
// 	$astpp_config = parse_ini_file("/home/html/astpp/astpp-config.conf");
	$db_name = $astpp_config['dbname'];
	$db_username = $astpp_config['dbuser'];
	$db_password = $astpp_config['dbpass'];
	$backup_file = $add_array['path'];
	//print_r($backup_file);
	//exit;
		if (substr($backup_file,-3)=='.gz'){
			// WE NEED TO GZIP
			$backup_file = substr($backup_file,0,-3);
			$do_gzip=1;
		}
	$run_backup="/usr/bin/mysqldump -all --databases ".$db_name." -u'".$db_username."' -p'".$db_password."' > '$backup_file'";
// 	echo $run_backup."\n\n";
	 exec($run_backup,$output,$error);
	 
	  if ($do_gzip){ 
		 // $gzip="/usr/bin/gzip";
        $gzip =     exec("which gzip");
		  // Compress file
		  $run_gzip = $gzip." '$backup_file'";
 		  echo $run_gzip."<br>";
      		exec($run_gzip,$output,$error_zip);
	  }
//print_r($error);
//print_r($error_zip);
// exit;
 		if($error ==0 && $error_zip ==0 )
		{
		
		    $this->system_model->backup_insert($add_array);
		    $this->session->set_flashdata('astpp_errormsg', 'backup added successfully!');
	  	}
		elseif($error!=0)
		{
					
			$this->session->set_flashdata('astpp_notification', 'An error occur when the system tried to backup of the database. Please check yours system settings for the backup section');
		}
		else
		{
	   	$this->session->set_flashdata('astpp_notification', 'An error occur when the system tried to compress the backup realized. Please check yours system settings for the backup section!');
		  
		}
	  redirect(base_url() . 'systems/database_restore/');
	  exit;
	  
      }

      else{
	$this->session->set_flashdata('astpp_notification', 'Please fill up proper information!');
	redirect(base_url() . 'systems/database_restore/');
      }
    }
    
    function database_restore() {
        $data['page_title'] = 'Backup Database';
	$data['form'] = $this->form->build_form($this->system_form->get_backup_database_form_fields(), '');
        $data['grid_fields'] = $this->system_form->build_backupdastabase_list();
        $data["grid_buttons"] = $this->system_form->build_backupdastabase_buttons();
//         echo "<pre>";print_r($data);exit;
        $this->load->view('view_database_list', $data);
    }
    function database_restore_json()
    {
	$json_data = array();
        $count_all = $this->system_model->getbackup_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp']=10, $_GET['page']=1);
        $json_data = $paging_data["json_paging"];

        $query = $this->system_model->getbackup_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->system_form->build_backupdastabase_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
	exit;
    }
    function database_restore_one($id='')
    {
	 $result = $this->system_model->get_backup_data($id);
	 $result_array=$result->result_array();
	  if($result->num_rows() > 0)
	  {
		    $astpp_config = parse_ini_file("/var/lib/astpp/astpp-config.conf");
		    $db_name = $astpp_config['dbname'];
		    $db_username = $astpp_config['dbuser'];
		    $db_password = $astpp_config['dbpass'];
		    
		    $path=$result_array[0]['path'];
		    if(file_exists($path)){
			  if (substr($path,-3)=='.gz') {
                 $GUNZIP_EXE =     exec("which gunzip");
				  //$GUNZIP_EXE="/usr/bin/gunzip";
				  $run_gzip = $GUNZIP_EXE." -c ".$path." | ";
			  }
			  $MYSQL="/usr/bin/mysql";
			  $run_restore = $run_gzip.$MYSQL." -u ".$db_username." -p".$db_password;
			  exec($run_restore);
		    }else{
			  $this->session->set_flashdata('astpp_notification', 'File not exists!');
			  redirect(base_url() . 'systems/database_restore/');
			  exit;
		    }
	  }
	$this->session->set_flashdata('astpp_errormsg', 'Backup restore successfully!');
	redirect(base_url() . 'systems/database_restore/');
    }

function country_export_xls()
{
	echo "developing remaining"; 
}

function currency_export_xls()
{
	echo "developing remaining"; exit;
}
//       function database_upload_sql_file()
//       {
// 	    $upload_dir = "/var/www/html/";
// 	  if (isset($_FILES["sql_file_name"])) {
// 	      if ($_FILES["sql_file_name"]["error"] > 0) {
// 		  echo "Error: " . $_FILES["file"]["error"] . "<br>";
// 	      } else {
// 		  move_uploaded_file($_FILES["sql_file_name"]["tmp_name"], $upload_dir . $_FILES["sql_file_name"]["name"]);
// 		  //echo "Uploaded File :" . $_FILES["myfile"]["name"];
// 		  echo "<pre>";
// 		  print_r($_POST);
// 		  print_r($_FILES);
// 		  exit;
// 	      }
// 	  }
//       }

    function database_download($id='')
    {
    
 	$result = $this->system_model->get_backup_data($id);
 	$result_array=$result->result_array();
 	if($result->num_rows() > 0)
 	{
	  $path=$result_array[0]['path'];
	  $filename = basename($path);
	  $len = filesize($path);
	  
	  header("Content-Encoding: binary");
	  header("Content-Type: application/octet-stream");
	  header( "content-length: " . $len );
	  header( "content-disposition: attachment; filename=" . $filename );
	  header("Expires: 0");
	  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	  header("Cache-Control: private");
	  header("Pragma: public");
	  ob_clean();
	  $fp=fopen( $path, "r" );
	  fpassthru( $fp );
	  exit;
 	}
    }

    function database_import()
    {
	$data['page_title'] = 'Import Database';
        $this->load->view('view_import_database', $data);
    
    }
   function database_import_file()
    {
	// ini_set('upload_max_size','128MB');
	$filename = $_POST['fname'];
	//print_r($filename);
	//exit;
		
	$target_path = $this->config->item('db_upload-file-path');
	$upload_greeting_file= $_FILES['userfile']['name'];
	$db_file = explode(".",$upload_greeting_file);
	if( $db_file[1] == 'csv' || $db_file[1] == 'tar' || $db_file[1] == 'sql')
		{
			$target_path = $target_path . basename( $_FILES['userfile']['name']); 
			move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_path );
			$this->load->model('system_model');
			$query = $this->system_model->import_database($filename,$target_path);
			echo "The file ".  basename( $_FILES['userfile']['name'])." has been uploaded";
			redirect(base_url() . 'systems/database_restore/');
			//$this->redirect_notification("Something wrong",'/system/database_restore/");	
		}		
		else
		{
			echo "The file ".  basename( $_FILES['userfile']['name'])." Uploaded Fail";
			redirect(base_url() . 'systems/database_restore/');
			//$this->redirect_notification("Something wrong",'/system/database_restore/");	
		
		}

	 	
    }
    function database_delete($id)
    {
      $where = array("id"=>$id);
      $this->db->where($where);
      $this->db->delete("backup_database");
      redirect(base_url() . 'systems/database_restore/');
      return true;
    }
    function database_backup_delete_multiple() {
        
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo  $this->db->delete("backup_database");
    }
}

?>
  
