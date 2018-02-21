<?php

class Opensipsconfig extends CI_Controller {

    var $opensips_db;

    function Opensipsconfig() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('authorization');
        $this->load->helper('form');
        $this->load->helper('romon');
        $this->load->library('astpp');

        $this->load->library('session');
        $this->load->library('form_builder');

        $this->load->model('Pricelists_model');

        $this->load->model('opensips_model');
        $this->load->model('Astpp_common');
        $this->load->model('accounts_model');

        $db_config = Common_model::$global_config['system_config'];


        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";

        $this->opensips_db = $this->load->database($opensipdsn, true);
    }

    function index() {
        $this->opensipdevice();
    }
    
    /*
    * CI has a built in method named _remap which allows
    * you to overwrite the behavior of calling your controller methods over URI
    */
    public function _remap($method, $params = array())
    {
	    $logintype = $this->session->userdata('logintype');
	    $access_control = validate_access($logintype,$method, "opensipsconfig");
	    if ($access_control){
		    return call_user_func_array(array($this, $method), $params);			 
	    //$this->$method();
	    }
	    else{
		    $errors =  "Permission Access denied";
		    $this->session->set_userdata('astpp_errormsg', $errors);
		    if($logintype!=0){
			    redirect(base_url().'astpp/dashboard');
		    }
		    else{
			    redirect(base_url().'user/dashboard');
		    }			
	    }
    }

    /**
     * -------Here we write code for controller opensipsconfig functions opensipsdevices------
     * @action: Add, Edit, Delete opensips devices
     */
    function opensipdevice($action=false, $id=false) {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Opensips  Config | Opensips Devices';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'OPENSIPS DEVICES';
        $data['cur_menu_no'] = 9;
        if ($action === false)
            $action = "list";

        if ($action == 'list') {
            $this->load->view('view_opensipsconfig_devices', $data);
        } elseif ($action == 'add') {

            if (!empty($_POST)) {
                $errors = "";
                if (trim($_POST['username']) == "")
                    $errors .= "Username is required<br />";
                if (trim($_POST['password']) == "")
                    $errors .= "Password is required<br />";


                if ($errors == "") {
                    $this->opensips_model->add_opensip($_POST);
                    $this->session->set_userdata('astpp_notification', 'Opensips added successfully!');
                    redirect(base_url() . 'opensipsconfig/opensipdevice');
                } else {
                    $this->session->set_userdata('astpp_errormsg', $errors);
                    redirect(base_url() . 'opensipsconfig/opensipdevice/');
                }
            }

            $this->load->view('view_opensipsconfig_devices_add', $data);
        } elseif ($action == 'edit') {
            if (!empty($_POST)) {
                $errors = "";
                if (trim($_POST['username']) == "")
                    $errors .= "Username is required<br />";
                if (trim($_POST['password']) == "")
                    $errors .= "Password is required<br />";

                if ($errors == "") {
                    $this->opensips_model->update_opensip($id, $_POST, $id);
                    $this->session->set_userdata('astpp_notification', 'Opensips updated successfully!');
                    redirect(base_url() . 'opensipsconfig/opensipdevice');
                } else {
                    $this->session->set_userdata('astpp_errormsg', $errors);
                    redirect(base_url() . 'opensipsconfig/opensipdevice');
                }
            } else {
                if ($switch = $this->opensips_model->getdata($id)) {

                    $data['opensips'] = $switch;
                } else {
                    echo "This Opensip Configuration is not available.";
                    return;
                }
                $data['edit_id'] = $id;
                $this->load->view('view_opensipsconfig_devices_add', $data);
            }
        } elseif ($action == 'delete') {


            $this->opensips_model->delete_opensips($id);
            $this->session->set_userdata('astpp_notification', 'Opensips removed successfully!');
            redirect(base_url() . 'opensipsconfig/opensipdevice/');
        }
    }

    /**
     * -------Here we write code for controller switchconfig functions opensipsdevice_grid------
     * Listing of opensips devices data through php function json_encode
     */
    function opensipdevice_grid() {
        $json_data = array();
        $json_data['page'] = 1;
        $json_data['total'] = 1;
        $domain = "";

        $query = $this->opensips_model->get_opensip_count();


        if ($query->num_rows() > 0) {
            $perpage = 20;
            if (isset($_GET['rp'])) {
                $perpage = $_GET['rp'];
            }

            if (!isset($_GET['page'])) {
                $json_data['page'] = 1;
                $start_from = 0;
            } else {
                $json_data['page'] = $_GET['page'];
                $start_from = ($json_data['page'] - 1) * $perpage;
            }

            $json_data['total'] = $query->num_rows();


            $this->db->limit($perpage, $start_from);

            $json_data['rows'] = array();

            $record = $this->opensips_model->get_data();

            foreach ($record->result_array() as $row) {

                $json_data['rows'][] = array('cell' => array(
                        $row['username'],
                        $row['password'],
                        $row['accountcode'],
			$row['domain'],
                        $this->get_action_buttons($row['id'])
                        ));
            }

            echo json_encode($json_data);
        }
    }

    function get_action_buttons($id) {
        $update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
        $delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
        $ret_url = '';
        $ret_url = '<a href="/opensipsconfig/opensipdevice/edit/' . $id . '/" class="icon" rel="facebox" ' . $update_style . ' title="Update">&nbsp;</a>';
        $ret_url .= '<a href="/opensipsconfig/opensipdevice/delete/' . $id . '/" class="icon" ' . $delete_style . ' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        return $ret_url;
    }

    function dispatcher($action=false, $id=false) {

        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Opensip  Config | Dispatcher';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Dispatcher List';
        $data['cur_menu_no'] = 9;
        if ($action === false)
            $action = "list";

        if ($action == 'list') {
            $this->load->view('view_opensips_dispatcher', $data);
        } elseif ($action == 'add') {

            if (!empty($_POST)) {
                $errors = "";
                if (trim($_POST['destination']) == "")
                    $errors .= "Username is required<br />";
                


                if ($errors == "") {
                    $this->opensips_model->add_dispatcher($_POST);
                    $this->session->set_userdata('astpp_notification', 'Opensips added successfully!');
                    redirect(base_url() . 'opensipsconfig/dispatcher');
                } else {
                    $this->session->set_userdata('astpp_errormsg', $errors);
                    redirect(base_url() . 'opensipsconfig/dispatcher');
                }
            }

            $this->load->view('view_opensips_dispatcher_add', $data);
        }
        elseif ($action == 'edit') {
            if (!empty($_POST)) {
                $errors = "";
             if (trim($_POST['destination']) == "")
                    $errors .= "Username is required<br />";
                if ($errors == "") {

                    $this->opensips_model->update_dispatcher($id,$_POST);
                    $this->session->set_userdata('astpp_notification', 'Dispatcher updated successfully!');
                    redirect(base_url() . 'opensipsconfig/dispatcher');
                } else {
                    $this->session->set_userdata('astpp_errormsg', $errors);
                    redirect(base_url() . 'opensipsconfig/dispatcher');
                }
            } else {
                if ($switch = $this->opensips_model->get_dispatcher_data_by_id($id)) {
                    $data['dispatcher'] = $switch->result_array();
                } else {
                    echo "This Dispatcher is not available.";
                    return;
                }
                $data['edit_id'] = $id;
                $this->load->view('view_opensips_dispatcher_add', $data);
            }
        } elseif ($action == 'delete') {


            $this->opensips_model->delete_dispatcher($id);
            $this->session->set_userdata('astpp_notification', 'Dispatcher Removed successfully!');
            redirect(base_url() . 'opensipsconfig/dispatcher/');
        }
    }

    function dispatcher_grid() {
        $json_data = array();
        $json_data['page'] = 1;
        $json_data['total'] = 1;
        $domain = "";

        $query = $this->opensips_model->get_dispatcher_count();


        if ($query->num_rows() > 0) {
            $perpage = 20;
            if (isset($_GET['rp'])) {
                $perpage = $_GET['rp'];
            }

            if (!isset($_GET['page'])) {
                $json_data['page'] = 1;
                $start_from = 0;
            } else {
                $json_data['page'] = $_GET['page'];
                $start_from = ($json_data['page'] - 1) * $perpage;
            }

            $json_data['total'] = $query->num_rows();


            $this->db->limit($perpage, $start_from);

            $json_data['rows'] = array();

            $record = $this->opensips_model->get_dispatcher_data();
            foreach ($record->result_array() as $row) {

                $json_data['rows'][] = array('cell' => array(
			$row['setid'],
                        $row['destination'],
			$row['flags'],
                        $row['weight'],
			$row['attrs'],
                        $row['description'],
                        $this->get_action_button_dispatcher($row['id'])
                        ));
            }

            echo json_encode($json_data);
        }
    }
    function get_action_button_dispatcher($id) {
        $update_style = 'style="text-decoration:none;background-image:url(/images/page_edit.png);"';
        $delete_style = 'style="text-decoration:none;background-image:url(/images/delete.png);"';
        $ret_url = '';
        $ret_url = '<a href="/opensipsconfig/dispatcher/edit/' . $id . '/" class="icon" rel="facebox" ' . $update_style . ' title="Update">&nbsp;</a>';
        $ret_url .= '<a href="/opensipsconfig/dispatcher/delete/' . $id . '/" class="icon" ' . $delete_style . ' title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        return $ret_url;
    }

}

?>