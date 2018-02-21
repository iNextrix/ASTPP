<?php

class Freeswitch extends MX_Controller {

    function Freeswitch() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("freeswitch_form");
        $this->load->library('astpp/form');
        $this->load->library('freeswitch_lib');
        $this->load->model('freeswitch_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function fssipdevices_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create freeswitch Account';
        if ($type != "") {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($type), '');
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), '');
        }
        $this->load->view('view_freeswitch_add_edit', $data);
    }
    function customer_fssipdevices_add($accountid) {
        $data['page_title'] = 'freeswitch Account';
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($accountid),"");
        $this->load->view('view_freeswitch_add_edit', $data);
    }

    function fssipdevices_edit($edit_id = '') {
        $data['page_title'] = 'freeswitch Account';
        $where = array('id' => $edit_id);
        $account = $this->freeswitch_model->get_edited_data($edit_id);
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), $account);
        $this->load->view('view_freeswitch_add_edit', $data);
    }

    function customer_fssipdevices_edit($edit_id, $accountid) {
        $data['page_title'] = 'freeswitch Account';
        $where = array('id' => $edit_id);
        $account = $this->freeswitch_model->get_edited_data($edit_id);
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($accountid), $account);
        $this->load->view('view_freeswitch_add_edit', $data);
    }

    function fssipdevices_save($user_flg = false) {
        $add_array = $this->input->post();
        if (!$user_flg) {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), $add_array);
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"]), $add_array);
        }
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices updates Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices added Successfully."));
                exit;
            }
        }
    }
    function customer_fssipdevices_save($user_flg = false) {
        $add_array = $this->input->post();
        if (!$user_flg) {
            $data['form'] = $this->form->build_form($this->freeswitch_form->get_freeswith_form_fields(), $add_array);
        } else {
            $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"]), $add_array);
        }
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices added Successfully."));
                exit;
            }
        }
    }
    

    function user_fssipdevices_save($user_flg = false) {
        
        $add_array = $this->input->post();
//        print_r($add_array);
//        exit;
        $data['form'] = $this->form->build_form($this->freeswitch_form->fsdevice_form_fields_for_customer($add_array["accountcode"]), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_freeswith($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "SIP Devices updated Successfully."));
                exit;
            }
        }else{
            $data['page_title'] = 'Create Freeswitch SIP Devices';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_freeswith($add_array);
                echo json_encode(array("SUCCESS"=> "SIP Devices added Successfully."));
                exit;
            }
        }
    }

    function fssipdevices_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            
//             print_r($action);
//             exit;
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fssipdevices_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fssipdevices/');
        }
    }

    function fssipdevices_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function fssipdevices() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch SIP Devices';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_system_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->freeswitch_form->get_freeswith_search_form());
        $this->load->view('view_freeswitch_sip_devices_list', $data);
    }

    function fssipdevices_json() {
        $json_data = array();
        $count_all = $this->freeswitch_model->fs_retrieve_sip_user(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->freeswitch_model->fs_retrieve_sip_user(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_system_list_for_admin());
        foreach ($query as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
//                     $value['id'],
                    $value['username'],
                    $value['password'],
                    $this->common->get_field_name('name', '`sip_profiles', array('id' => $value['sip_profile_id'])),
                    $this->common->get_field_name('number', 'accounts', array('id' => $value['accountid'])),
                    $value['effective_caller_id_name'],
                    $value['effective_caller_id_number'],
                    $value['context'],
                    $this->get_action_buttons_fssipdevices($value['id'])
                    ));
        }

        echo json_encode($json_data);
    }

    function customer_fssipdevices_json($accountid) {
        $json_data = array();
        $count_all = $this->freeswitch_model->get_sipdevices_list(false, $accountid);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $devices_result = array();
        $query = $this->freeswitch_model->get_sipdevices_list(true, $accountid, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        foreach ($query as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
                    $value['username'],
                    $value['password'],
                    $this->common->get_field_name('name', '`sip_profiles', array('id' => $value['sip_profile_id'])),
                    $value['effective_caller_id_name'],
                    $value['effective_caller_id_number'],
//                     $value['context'],
                    $this->get_action_fssipdevices_buttons($value['id'], $value['accountid'])
                    ));
        }
        echo json_encode($json_data);
    }

    function get_action_fssipdevices_buttons($id, $accountid) {
        $ret_url = '';
        if ($this->session->userdata("logintype") == '0') {
            $ret_url = '<a href="/user/user_fssipdevices_action/edit/' . $id . '/' . $accountid . '/" class="icon edit_image"  rel="facebox" title="Update">&nbsp;</a>';
            $ret_url .= '<a href="/user/user_fssipdevices_action/delete/' . $id . '/' . $accountid . '/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        } else {
            $ret_url = '<a href="/accounts/customer_fssipdevices_action/edit/' . $id . '/' . $accountid . '/" class="icon edit_image"  rel="facebox" title="Update">&nbsp;</a>';
            $ret_url .= '<a href="/accounts/customer_fssipdevices_action/delete/' . $id . '/' . $accountid . '/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        }
        return $ret_url;
    }

    function fssipdevices_delete($id) {
        $this->freeswitch_model->delete_freeswith_devices($id);
        $this->session->set_flashdata('astpp_notification', 'Sip devices remove successfully!');
        redirect(base_url() . 'freeswitch/fssipdevices/');
        exit;
    }

    function get_action_buttons_fssipdevices($id) {
        $ret_url = '';
        $ret_url = '<a href="/freeswitch/fssipdevices_edit/' . $id . '/" class="icon edit_image"  rel="facebox" title="Update">&nbsp;</a>';
        $ret_url .= '<a href="/freeswitch/fssipdevices_delete/' . $id . '/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        return $ret_url;
    }

    function livecall_report() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Live Call Report';
        $this->load->view('view_fs_livecall_report', $data);
    }

    function livecall_report_json() {
/*        $fp = $this->freeswitch_lib->event_socket_create("127.0.0.1","8021","ClueCon");
        $cmd = "api show channels";
        $response_arr = array();
        $response_arr[] = $this->freeswitch_lib->event_socket_request($fp, $cmd);*/

        $command = "api show channels";
        $response = $this->freeswitch_model->reload_live_freeswitch($command);
        $calls = array();
        $calls_final = array();
        $data_header = array();
        $k = 0;
//         foreach($response_arr as $resp_key=>$response){
            $data = explode("\n",$response);
            for ($i = 0; $i < count($data) - 2; $i++) {
                if (trim($data[$i]) != '') {
                    if (count($data_header) ==0 ) {
                        $data_header = explode(",", $data[$i]);
                    } else {
                        $data_call = explode(",", $data[$i]);
                        for ($j = 0; $j < count($data_call); $j++) {
                            $calls[$k][@$data_header[$j]] = @$data_call[$j];
                            $calls_final[@$calls[$k]['uuid']] = @$calls[$k];
                        }
                        $k++;
                    }
                }
            }
//         }
        $json_data = array();
        $count = 0;

        //for($i=0;$i<count($calls)-1;$i++)
        foreach ($calls as $key => $value) {
            if (isset($value['state']) && $value['state'] == 'CS_EXCHANGE_MEDIA') {
                $calls[$i]['application'] = $calls_final[$value['call_uuid']]['application'];
                $calls[$i]['application_data'] = $calls_final[$value['call_uuid']]['application_data'];
                $json_data['rows'][] = array('cell' => array(
                        $value['created'],
                        $value['cid_name'],
                        $value['cid_num'],
                        $value['ip_addr'],
                        $value['dest'],
                        $calls[$i]['application_data'],
                        $value['read_codec'],
                        $value['write_codec'],
                        $value['callstate']
                        ));
                $count++;
            } else {
                unset($calls[$i]);
            }
        }
	$json_data['page'] = 1;
        $json_data['total'] = $count;
//         fclose($fp);
        echo json_encode($json_data);
    }

    function fsgateway() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch Gateway List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fsgateway_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_fdgateway_grid_buttons();
//        $data['form_search']=$this->form->build_serach_form($this->freeswitch_form->get_freeswith_search_form());
        $this->load->view('view_fsgateway_list', $data);
    }

    function fsgateway_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_gateway_list(false);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $gateway_data = array();
        $query = $this->freeswitch_model->get_gateway_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $gateway_result = array();
        if ($query->num_rows > 0) {
            $query = $query->result_array();
            foreach ($query as $key => $query_value) {
                foreach ($query_value as $gateway_key => $gatewau_val) {
                    if ($gateway_key != "gateway_data") {
                        $gateway_data[$gateway_key] = $gatewau_val;
                    } else {
                        $tmp = (array) json_decode($gatewau_val);
                        $gateway_result[$key] = array_merge($gateway_data, $tmp);
                    }
                }
            }
        }
        $grid_fields = json_decode($this->freeswitch_form->build_fsgateway_list_for_admin());
        $json_data['rows'] = $this->form->build_json_grid($gateway_result, $grid_fields);
        echo json_encode($json_data);
    }

    function fsgateway_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Gateway';
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), '');
        $this->load->view('view_fsgateway_add', $data);
    }

    function fsgateway_edit($edit_id = '') {
        $data['page_title'] = 'Freeswitch Gateway';
        $where = array('id' => $edit_id);
        $query = $this->db_model->getSelect("*", "gateways", $where);
        $query = $query->result_array();
        $gateway_result = array();
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
                if ($gateway_key != "gateway_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), $gateway_result);
        $this->load->view('view_fsgateway_add', $data);
    }

    function fsgateway_save() {
        $gateway_data = $this->input->post();
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_gateway_form_fields(), $gateway_data);
        $insert_arr = array();
        $gateway_arr = array();
        foreach ($gateway_data as $key => $gateway_value) {
            if ($gateway_value != "") {
                if ($key == "sip_profile_id") {
                    $insert_arr['sip_profile_id'] = $gateway_data["sip_profile_id"];
                } else if ($key == "name") {
                    $insert_arr['name'] = $gateway_data["name"];
                } else if ($key == "sip_profile_id") {
                    $insert_arr['sip_profile_id'] = $gateway_data["sip_profile_id"];
                } else {
                    if ($key != "id") {
                        $gateway_arr[$key] = $gateway_value;
                    }
                }
            }
        }

        $insert_arr["gateway_data"] = json_encode($gateway_arr);

        if ($gateway_data['id'] != '') {
            $data['page_title'] = 'Edit Gateway Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $update = $this->db->update("gateways", $insert_arr, array('id' => $gateway_data['id']));
                if ($update) {
                    $profile_name = $this->common->get_field_name('name', 'sip_profiles', $insert_arr['sip_profile_id']);
		    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $insert_arr['sip_profile_id']);
                    $cmd = "api sofia profile ".$profile_name." killgw '".$insert_arr['name']."' ";
                    $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);

                    $cmd2 = "api sofia profile " . $profile_name . " restart reloadacl reloadxml";
                    $this->freeswitch_model->reload_freeswitch($cmd2,$sip_ip);
                }
                echo json_encode(array("SUCCESS"=> $insert_arr['name']." Gateway updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Gateway Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $insert = $this->db->insert("gateways", $insert_arr);
                if ($insert) {
                    $profile_name = $this->common->get_field_name('name', 'sip_profiles', $insert_arr['sip_profile_id']);
		    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $insert_arr['sip_profile_id']);
                    $cmd = "api sofia profile " . $profile_name . " restart reloadacl reloadxml";
                    $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);
                }
                echo json_encode(array("SUCCESS"=> $insert_arr['name']." Gateway added Successfully."));
                exit;
            }
        }
    }

    function fsgateway_delete($gateway_id) {
        $delete = $this->db_model->delete("gateways", array("id" => $gateway_id));
        if ($delete) {
            $profile_id = $this->common->get_field_name('sip_profile_id', 'gateways', $gateway_id);
            $profile_name = $this->common->get_field_name('name', 'sip_profiles', $profile_id);
	    $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $profile_id);
            $gateway_name = $this->common->get_field_name('name', 'gateways', $gateway_id);
            $cmd = "api sofia profile " . $profile_name . " killgw " . $gateway_name . " reloadxml";
            $this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);
        }

        $this->session->set_flashdata('astpp_notification', 'Gateway Deleted successfully!');
        redirect(base_url() . 'freeswitch/fsgateway/');
    }

    function fssipprofile() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch Sip Profile List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fssipprofile_list_for_admin();
        $data["grid_buttons"] = $this->freeswitch_form->build_fssipprofile_grid_buttons();
        $this->load->view('view_fssipprofile_list', $data);
    }

    function fssipprofile_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_sipprofile_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $gateway_data = array();
        $query = $this->freeswitch_model->get_sipprofile_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_fssipprofile_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function fssipprofile_add() {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Freeswitch Sip Profile';
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_sipprofile_form_fields(), '');
        $this->load->view('view_fssipprofile_add', $data);
    }

    function fssipprofile_edit($edit_id = '') {
        $data['page_title'] = 'Freeswitch Sip Profile';
        $where = array('id' => $edit_id);
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        $query = $query->result_array();
        $gateway_result = array();
        foreach ($query as $key => $query_value) {
            foreach ($query_value as $gateway_key => $gatewau_val) {
                if ($gateway_key != "profile_data") {
                    $gateway_data[$gateway_key] = $gatewau_val;
                } else {
                    $tmp = (array) json_decode($gatewau_val);
                    $gateway_result = array_merge($gateway_data, $tmp);
                }
            }
        }
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_sipprofile_form_fields(), $gateway_result);
        $this->load->view('view_fssipprofile_add', $data);
    }

    function fssipprofile_save() {
        $sipprofile_data = $this->input->post();
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_sipprofile_form_fields(), $sipprofile_data);
        $insert_arr = array();
        $sipprofile_arr = array();

        foreach ($sipprofile_data as $key => $profile_value) {
            if ($profile_value != "") {
                if ($key == "name") {
                    $insert_arr['name'] = $sipprofile_data["name"];
                } else if ($key == "sip_port") {
                    $insert_arr['sip_port'] = $sipprofile_data["sip_port"];
                } else if ($key == "sip_ip") {
                    $insert_arr['sip_ip'] = $sipprofile_data["sip_ip"];
                } else {
                    if ($key != "id") {

                        if ($sipprofile_data['rtp_ip'] == '')
                            $sipprofile_arr['rtp_ip'] = $sipprofile_data['sip_ip'];

                        $sipprofile_arr[$key] = $profile_value;
                    }
                }
            }
        }
        $insert_arr["profile_data"] = json_encode($sipprofile_arr);
        if ($sipprofile_data['id'] != '') {
            $data['page_title'] = 'Edit SIP Profile Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $check_authentication = $this->freeswitch_model->profile_authentication($sipprofile_data);
                if ($check_authentication->num_rows == 0) {
		    $profile_name = $this->common->get_field_name('name', 'sip_profiles', $sipprofile_data['id']);
                    $cmd = "api sofia profile '$profile_name' stop";
		    $response= $this->freeswitch_model->reload_freeswitch($cmd,$sipprofile_data['sip_ip']);
		    
		    $command = "api reloadacl";
		    $response= $this->freeswitch_model->reload_freeswitch($cmd,$sipprofile_data['sip_ip']);
		    		    
                    $update = $this->db->update("sip_profiles", $insert_arr, array('id' => $sipprofile_data['id']));
                    if ($update) {
			sleep(2);  
                        $cmd = "api sofia profile '".$sipprofile_data['name']."' start reloadxml";
                        $response= $this->freeswitch_model->reload_freeswitch($cmd,$sipprofile_data['sip_ip']);

		      $command = "api reloadacl";
		      $this->freeswitch_model->reload_freeswitch($command,$sipprofile_data['sip_ip']);
                    }
                      echo json_encode(array("SUCCESS"=> $sipprofile_data['name']." SIP Profile updated Successfully."));
		      exit;
                } else {
                    $data['validation_errors'] = json_encode(array("name_error"=>"Duplicate SIP IP OR Port found it must be unique."));
                    echo $data['validation_errors'];
                    exit;
                }
            }
        } else {
            $data['page_title'] = 'Create SIP Profile Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $check_authentication = $this->freeswitch_model->profile_authentication($sipprofile_data);
                if ($check_authentication->num_rows == 0) {
                    $insert = $this->db->insert("sip_profiles", $insert_arr);
                    if ($insert) {
                       $cmd = "api sofia profile  '".$sipprofile_data['name']."' start reloadxml";
                       $response= $this->freeswitch_model->reload_freeswitch($cmd,$sipprofile_data['sip_ip']);

		      $command = "api reloadacl";
		      $this->freeswitch_model->reload_freeswitch($command,$sipprofile_data['sip_ip']);
                    }
                      echo json_encode(array("SUCCESS"=> $sipprofile_data['name']." SIP Profile added Successfully."));
		      exit;
                } else {
                     $data['validation_errors'] = json_encode(array("name_error"=>"Duplicate SIP IP OR Port found it must be unique."));
                    echo $data['validation_errors'];
                    exit;
                }
            }
        }
//         $this->load->view('view_fssipprofile_add', $data);
    }

    function fssipprofile_delete($profile_id) {
	$profile_name = $this->common->get_field_name('name', 'sip_profiles', $profile_id);
        $sip_ip = $this->common->get_field_name('sip_ip', 'sip_profiles', $profile_id);
	$cmd = "api sofia profile '$profile_name' stop reloadxml";
	$this->freeswitch_model->reload_freeswitch($cmd,$sip_ip);
	$command = "api reloadacl";
	$this->freeswitch_model->reload_freeswitch($command,$sip_ip);
	$delete = $this->db_model->delete("sip_profiles", array("id" => $profile_id));
        $this->session->set_flashdata('astpp_notification', 'Gateway Deleted successfully!');
        redirect(base_url() . 'freeswitch/fssipprofile/');
    }

    function fsserver_list() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch Server List';
        $data['cur_menu_no'] = 1;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->freeswitch_form->build_fsserver_list();
        $data["grid_buttons"] = $this->freeswitch_form->build_fsserver_grid_buttons();

        $data['form_search'] = $this->form->build_serach_form($this->freeswitch_form->get_search_fsserver_form());
        $this->load->view('view_fsserver_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function fsserver_list_json() {
        $json_data = array();

        $count_all = $this->freeswitch_model->get_fsserver_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->freeswitch_model->get_fsserver_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->freeswitch_form->build_fsserver_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function fsserver_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Freeswich Server';
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), '');

        $this->load->view('view_fsserver_add_edit', $data);
    }

    function fsserver_edit($edit_id = '') {
        $data['page_title'] = 'Edit Freeswich Server';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "freeswich_servers", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), $edit_data);
        $this->load->view('view_fsserver_add_edit', $data);
    }

    function fsserver_save() {
        $add_array = $this->input->post();

        $data['form'] = $this->form->build_form($this->freeswitch_form->get_form_fsserver_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Freeswitch Server';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->edit_fsserver($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> " Freeswitch Server updated Successfully."));
                exit;
            }
        } else {
            $data['page_title'] = 'Freeswich Server';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->freeswitch_model->add_fssever($add_array);
                echo json_encode(array("SUCCESS"=> "Freeswitch Server added Successfully."));
                exit;
            }
        }
        $this->load->view('view_callshop_details', $data);
    }

    function fsserver_delete($id) {
        $this->freeswitch_model->fsserver_delete($id);
        $this->session->set_flashdata('astpp_notification', 'Freeswitch server Deleted!!!');
        redirect(base_url() . 'freeswitch/fsserver_list/');
        exit;
    }

    function fsserver_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fsserver_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'freeswitch/fsserver_list/');
        }
    }

    function fsserver_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

}

?>
 
