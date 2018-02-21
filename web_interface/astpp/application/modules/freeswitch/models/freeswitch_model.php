<?php

class Freeswitch_model extends CI_Model {

    function Freeswitch_model() {
        parent::__construct();
    }

    function get_sipdevices_list($flag, $accountid = "", $start = "", $limit = "") {
        if ($accountid != "") {
            $where = array("accountid" => $accountid);
        }
        $this->db_model->build_search('fssipdevices_list_search');
        $query = array();
        if ($flag) {
            $deviceinfo = $this->db_model->select("*", "sip_devices", $where, "id", "ASC", $limit, $start);
            if ($deviceinfo->num_rows > 0) {
                $add_array = $deviceinfo->result_array();
                foreach ($add_array as $key => $value) {
                    $vars = json_decode($value['dir_vars']);
                    $passowrds = json_decode($value['dir_params']);
                    $query[] = array('id' => $value['id'], 'username' => $value['username'], 'accountid' => $value['accountid'],
                        'pricelist_id' => $value['pricelist_id'],
                        'sip_profile_id' => $value['sip_profile_id']
                        , 'effective_caller_id_name' => $vars->effective_caller_id_name,
                        'effective_caller_id_number' => $vars->effective_caller_id_number
                        , 'password' => $passowrds->password,
                        'context' => $vars->user_context);
                }
            }
        } else {
            $query = $this->db_model->countQuery("*", "sip_devices", $where);
        }
        return $query;
    }

    function add_freeswith($add_array) {
        if (isset($add_array['pricelist_id']) && $add_array['pricelist_id'] == '') {
            $add_array['pricelist_id'] = '0';
        }
        if($this->session->userdata("logintype") == 0){
            $account_data = $this->session->userdata("accountinfo");
            $add_array['pricelist_id'] = $account_data["pricelist_id"];
            $add_array['accountid'] = $account_data["id"];
        }
        $parms_array = array('password' => $add_array['fs_password']);

        $parms_array_vars = array('effective_caller_id_name' => $add_array['effective_caller_id_name'],
            'effective_caller_id_number' => $add_array['effective_caller_id_number'],
            'user_context' => $add_array['context']);

        $new_array = array('username' => $add_array['fs_username'], 'accountid' => $add_array['accountcode'],
            'pricelist_id' => $add_array['pricelist_id'], 'dir_params' => json_encode($parms_array),
            'dir_vars' => json_encode($parms_array_vars), 'sip_profile_id' => $add_array['sip_profile_id']);

//        print_r($new_array);
//        exit;
        
        $this->db->insert('sip_devices', $new_array);
        return true;
    }

    function edit_freeswith($add_array, $id) {
        if (isset($add_array['pricelist_id']) && $add_array['pricelist_id'] == '') {
            $add_array['pricelist_id'] = '0';
        }
        $parms_array = array('password' => $add_array['fs_password']
// 		      'vm_password' => ""
        );

        $parms_array_vars = array('effective_caller_id_name' => $add_array['effective_caller_id_name'],
            'effective_caller_id_number' => $add_array['effective_caller_id_number'],
            'user_context' => $add_array['context']);

        //if else for that we can not assign user pricelist selecttion for user side    
//             print_r($add_array);
	if (isset($add_array['pricelist_id'])){
        $new_array = array('username' => $add_array['fs_username'], 'accountid' => $add_array['accountcode'],
            'pricelist_id' => $add_array['pricelist_id'], 'dir_params' => json_encode($parms_array),
            'dir_vars' => json_encode($parms_array_vars), 'sip_profile_id' => $add_array['sip_profile_id']);
	}else{
	    $new_array = array('username' => $add_array['fs_username'], 'accountid' => $add_array['accountcode'],
             'dir_params' => json_encode($parms_array),
            'dir_vars' => json_encode($parms_array_vars), 'sip_profile_id' => $add_array['sip_profile_id']);
	}

        $this->db->where('id', $id);
        $this->db->update('sip_devices', $new_array);
        return true;
    }

    function get_edited_data($edit_id) {
        $deviceinfo = array();
        $where = array('id' => $edit_id);
        $deviceinfo = $this->db_model->getSelect("*", "sip_devices", $where);
        $add_array = $deviceinfo->result_array();
        foreach ($add_array as $key => $value) {
            $vars = json_decode($value['dir_vars']);
            $passowrds = json_decode($value['dir_params']);
            $query = array('id' => $value['id'], 'fs_username' => $value['username']
                , 'accountcode' => $value['accountid'],
                'sip_profile_id' => $value['sip_profile_id'],
                'pricelist_id' => $value['pricelist_id']
                , 'effective_caller_id_name' => $vars->effective_caller_id_name,
                'effective_caller_id_number' => $vars->effective_caller_id_number
                , 'fs_password' => $passowrds->password,
                'context' => $vars->user_context);
        }
        return $query;
    }

    function delete_freeswith_devices($id) {
        $this->db->where('id', $id);
        $this->db->delete('sip_devices');
        return true;
    }

    function fs_retrieve_sip_user($flag, $start = 0, $limit = 0) {

        $deviceinfo = array();
        $this->db_model->build_search('fssipdevices_list_search');
        $query = array();
        if ($flag) {
            $deviceinfo = $this->db_model->select("*", "sip_devices", '', "id", "ASC", $limit, $start);
            if ($deviceinfo->num_rows > 0) {
                $add_array = $deviceinfo->result_array();
                foreach ($add_array as $key => $value) {
                    $vars = json_decode($value['dir_vars']);
                    $passowrds = json_decode($value['dir_params']);
                    $query[] = array('id' => $value['id'], 'username' => $value['username'], 'accountid' => $value['accountid'],
                        'pricelist_id' => $value['pricelist_id'],
                        'sip_profile_id' => $value['sip_profile_id']
                        , 'effective_caller_id_name' => $vars->effective_caller_id_name,
                        'effective_caller_id_number' => $vars->effective_caller_id_number
                        , 'password' => $passowrds->password,
                        'context' => $vars->user_context);
                }
            }
        } else {
            $query = $this->db_model->countQuery("*", "sip_devices", '');
        }
        return $query;
    }

    function get_gateway_list($flag, $start = 0, $limit = 0) {
        if ($flag) {
            $query = $this->db_model->select("*", "gateways", "", "id", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "gateways", "");
        }
        return $query;
    }

    function get_sipprofile_list($flag, $start = 0, $limit = 0) {
        if ($flag) {
            $query = $this->db_model->select("*", "sip_profiles", "", "id", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "sip_profiles", "");
        }
        return $query;
    }

    function profile_authentication($profile_data) {
        if ($profile_data['id'] != '') {
            $where = array('id <>' => $profile_data["id"], 'sip_ip' => $profile_data["sip_ip"], "sip_port" => $profile_data["sip_port"]);
        } else {
            $where = array('sip_ip' => $profile_data["sip_ip"], "sip_port" => $profile_data["sip_port"]);
        }
        $query = $this->db_model->getSelect("*", "sip_profiles", $where);
        return $query;
    }

    function get_fsserver_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('fsserver_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "freeswich_servers", "", "id", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "freeswich_servers", "");
        }
        return $query;
    }

    function add_fssever($data) {
        unset($data['action']);
        $this->db->insert('freeswich_servers', $data);
        return true;
    }

    function edit_fsserver($data, $id) {
        unset($data['action']);

        $this->db->where('id', $id);
        $this->db->update('freeswich_servers', $data);
        return true;
    }

    function fsserver_delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('freeswich_servers');
        return true;
    }

    function reload_freeswitch($command,$server_host="") {
//        $command = "api sofia profile internal start";
	$response='';
        $query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $fs_data = $query->result_array();
//         $fp = $this->freeswitch_lib->event_socket_create($server_host, $fs_data[0]["freeswitch_port"], $fs_data[0]["freeswitch_password"]);
        
       foreach ($fs_data as $fs_key => $fs_value) {
	    $fp = $this->freeswitch_lib->event_socket_create($fs_value["freeswitch_host"], $fs_value["freeswitch_port"], $fs_value["freeswitch_password"]);
	    if ($fp) {
		$response.= $this->freeswitch_lib->event_socket_request($fp, $command);
		fclose($fp);
	    }
       }
        return $response;
    }
    function reload_live_freeswitch($command) {
	$response='';
        $query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $fs_data = $query->result_array();
        
        foreach ($fs_data as $fs_key => $fs_value) {
	    $fp = $this->freeswitch_lib->event_socket_create($fs_value["freeswitch_host"], $fs_value["freeswitch_port"], $fs_value["freeswitch_password"]);
	    if ($fp) {
		$response .= $this->freeswitch_lib->event_socket_request($fp, $command);
		fclose($fp);
	    }
        }
	$response = str_replace("0 total.","",$response);
        return $response;
    }

}
