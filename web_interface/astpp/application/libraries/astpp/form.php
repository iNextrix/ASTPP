<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Dynamically build forms for display
 */
class Form {

    protected $CI; // codeigniter
    protected $fields = array();  // array of fields
    protected $form_title = 'Form';
    protected $form_id = 'form';
    protected $form_action = '';
    protected $form_class = '';
    protected $hidden = array();
    protected $multipart = FALSE; // default to standard form
    protected $submit_button = 'Submit';
    protected $after_button = '';
    protected $rules = array(); // storage for validation rules

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('form_validation');
        $this->CI->load->library('astpp/common');
        $this->CI->load->model('db_model');
        $this->check_permissions();
    }

// __construct
    /**
     * adds raw html to the field array
     * @param type $label
     * @param type $field add
     */
    function check_permissions() {
        if ($this->CI->session->userdata('user_login') == TRUE) {
            $module_info = unserialize($this->CI->session->userdata("permited_modules"));
            $url = $this->CI->uri->uri_string;
            $file_name = explode("/", $url);
            $module = explode('_', $file_name['1']);

// echo $module[0]."<pre>========";
// print_r($module_info);
// exit;

            if (in_array($module[0], $module_info)) {
                return true;
            } else {
                $this->CI->session->set_userdata('astpp_errormsg', 'You do not have permission to access this module..!');
                if ($this->CI->session->userdata('userlevel_logintype') == '-1' || $this->CI->session->userdata('logintype') == '1') {
                    redirect(base_url() . 'dashboard/');
                } else {
                    redirect(base_url() . 'user/user/');
                }
            }
        } else {
            redirect(base_url());
        }
    }

    function build_form($fields_array, $values) {
        $form_contents = '';
        $form_contents.= '<div class="portlet-content">';
        $form_contents.= form_open($fields_array['forms'][0], $fields_array['forms'][1]);
        unset($fields_array['forms']);
        $button_array = array();
        if (isset($fields_array['button_save']) || isset($fields_array['button_cancel']) || isset($fields_array['additional_button'])) {
            $save = $fields_array['button_save'];
            unset($fields_array['button_save']);
            if (isset($fields_array['button_cancel'])) {
                $cancel = $fields_array['button_cancel'];
                unset($fields_array['button_cancel']);
            }
            if (isset($fields_array['additional_button'])) {
                $additiopnal_button = $fields_array['additional_button'];
                unset($fields_array['additional_button']);
            }
        }
        if (isset($additiopnal_button)) {
            $form_contents.= form_button($additiopnal_button);
        }
        $i = 0;
        foreach ($fields_array as $fieldset_key => $form_fileds) {
            if (count($fields_array) > 1) {
                if ($i == 1 || $i == 3) {
                    $form_contents.= '<div style="width:50%;float:right;">';
                    $form_contents.= '<div style="width:100%;float:right;">';
                } else {
                    $form_contents.= '<div style="width:50%;float:left;">';
                    $form_contents.= '<div style="width:100%;float:left;">';
                }
            } else {
                $form_contents.= '<div style="width:100%;float:left;">';
                $form_contents.= '<div style="width:100%;float:left;">';
            }
            $form_contents.= '<ul>';

            if ($i == 1 || $i == 3) {
                $form_contents.= form_fieldset($fieldset_key, array('style' => 'margin-left:10px;margin-top:10px;font-weight:bold;'));
            } else {
                $form_contents.= form_fieldset($fieldset_key, array('style' => 'margin-left:10px;margin-top:10px;font-weight:bold;'));
            }
            $form_contents.= '<div class="content-box-wrapper">';
            foreach ($form_fileds as $fieldkey => $fieldvalue) {
                $form_contents.= '<li>';
                if ($fieldvalue[1] == 'HIDDEN') {
                    if (isset($this->CI->input->post))
                        $fieldvalue[2]['value'] = (!$this->CI->input->post($fieldvalue[2]['name'])) ? @$fieldvalue[2]['value'] : $this->CI->input->post($fieldvalue[2]['name']);
                    else
                        $fieldvalue[2]['value'] = ($values) ? @$values[$fieldvalue[2]['name']] : @$fieldvalue[2]['value'];

                    $form_contents.= form_hidden($fieldvalue[2]['name'], $fieldvalue[2]['value']);
                }else {
                    if (is_array($fieldvalue[1]) || (is_array($fieldvalue[2]) && isset($fieldvalue[2]['hidden']))) {
                        $form_contents.= form_label($fieldvalue[0], $fieldvalue[0], array('class' => 'formlabel add_settings'));
                    } else {
                        $form_contents.= form_label($fieldvalue[0], "", array("class" => "formlabel"));
                    }
                }
                if ($fieldvalue[2] == 'SELECT' && !isset($fieldvalue[13])) {
                    if ($fieldvalue[7] != '' && $fieldvalue[8] != '') {
                        $str = $fieldvalue[7] . "," . $fieldvalue[8];

                        if (isset($this->CI->input->post)){
                            $fieldvalue['value'] = (!$this->CI->input->post($fieldvalue[1])) ? @$fieldvalue[1] : $this->CI->input->post($fieldvalue[1]);
                        }else{
                            if (is_array($fieldvalue[1])) {
                                $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]['name']] : @$fieldvalue[1];
                            } else {
                                $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]] : @$fieldvalue[1];
                            }
//                            $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]] : @$fieldvalue[1];
                        }
//                     echo $fieldvalue[10];
                        $drp_array = call_user_func_array(array($this->CI->db_model, $fieldvalue[10]), array($str, $fieldvalue[9], $fieldvalue[11], $fieldvalue[12]));
//                         echo $fieldvalue[1];

                        if ($fieldset_key === 'System Configuration Information' || ($fieldset_key === 'Card Information' && $fieldvalue[0] == 'Rate Group') || ($fieldset_key === 'DID Billing' && $fieldvalue[0] == 'Account') || $fieldset_key === 'Freeswitch Devices' && $fieldvalue[0] == 'Rate Group') {
                            $form_contents.=form_dropdown_all($fieldvalue[1], $drp_array, $fieldvalue['value'], '');
                        } else {
                            $form_contents.=form_dropdown($fieldvalue[1], $drp_array, $fieldvalue['value'], '');
                        }
                        $form_contents.= '<br/>';
                        $form_contents.= '<span class="error" id="' . (is_array($fieldvalue[1])?$fieldvalue[1]['name']:$fieldvalue[1]) . '_error">&nbsp;</span>';
                    } else {
                        if (isset($this->CI->input->post)) {
                            $fieldvalue['value'] = (!$this->CI->input->post($fieldvalue[1])) ? @$fieldvalue[1] : $this->CI->input->post($fieldvalue[1]);
                        } else {
                            if (is_array($fieldvalue[1])) {
                                $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]['name']] : @$fieldvalue[1];
                            } else {
                                $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]] : @$fieldvalue[1];
                            }
                        }

                        $str = $fieldvalue[7] . "," . $fieldvalue[8];
                        $drp_array = call_user_func_array(array($this->CI->common, $fieldvalue[10]), array($fieldvalue[9]));
                        $form_contents.=form_dropdown($fieldvalue[1], $drp_array, $fieldvalue['value']);
                        $form_contents.= '<br/>';                        
                        $form_contents.= '<span class="error" id="' . (is_array($fieldvalue[1])?$fieldvalue[1]['name']:$fieldvalue[1]) . '_error">&nbsp;</span>';
                    }
                } else if (isset($fieldvalue[13]) && $fieldvalue[13] != '') {

                    /* For multi select code */
                    $str = $fieldvalue[7] . "," . $fieldvalue[8];

                    if (isset($this->CI->input->post))
                        $fieldvalue['value'] = (!$this->CI->input->post($fieldvalue[1])) ? @$fieldvalue[1] : $this->CI->input->post($fieldvalue[1]);
                    else
                        $fieldvalue['value'] = ($values) ? @$values[$fieldvalue[1]] : @$fieldvalue[1];

                    $drp_array = call_user_func_array(array($this->CI->db_model, $fieldvalue[10]), array($str, $fieldvalue[9], $fieldvalue[11], $fieldvalue[12]));
                    if ($fieldset_key === 'System Configuration Information') {
                        $form_contents.=form_dropdown_multiselect($fieldvalue[1], $drp_array, '');
                    } else {
                        $form_contents.=form_dropdown_multiselect($fieldvalue[1] . "[]", $drp_array, $fieldvalue['value']);
                    }
                    $form_contents.= '<br/>';
                    $form_contents.= '<span class="error" id="' . $fieldvalue[1] . '_error">&nbsp;</span>';
                    /* End---------------------   For multi select code */
                } else if ($fieldvalue[1] == 'INPUT') {
                    if (isset($this->CI->input->post))
                        $fieldvalue[2]['value'] = (!$this->CI->input->post($fieldvalue[2]['name'])) ? @$fieldvalue[2]['value'] : $this->CI->input->post($fieldvalue[2]['name']);
                    else
                        $fieldvalue[2]['value'] = ($values) ? @$values[$fieldvalue[2]['name']] : @$fieldvalue[2]['value'];
                    $form_contents.= form_input($fieldvalue[2], 'readonly');
                    $this->CI->form_validation->set_rules($fieldvalue[2]['name'], $fieldvalue[0], $fieldvalue[3]);
                    $form_contents.= '<br/>';
                    $form_contents.= '<span class="error" id="' . $fieldvalue[2]['name'] . '_error">&nbsp;</span>';
                } else if ($fieldvalue[1] == 'PASSWORD') {
                    if (isset($this->CI->input->post))
                        $fieldvalue[2]['value'] = (!$this->CI->input->post($fieldvalue[2]['name'])) ? @$fieldvalue[2]['value'] : $this->CI->input->post($fieldvalue[2]['name']);
                    else
                        $fieldvalue[2]['value'] = ($values) ?@$values[$fieldvalue[2]['name']] : @$fieldvalue[2]['value'];
                    $form_contents.= form_password($fieldvalue[2]);
//                    exit;
                    $this->CI->form_validation->set_rules($fieldvalue[2]['name'], $fieldvalue[0], $fieldvalue[3]);
                    $form_contents.= '<br/>';
                    $form_contents.= '<span class="error" id="' . $fieldvalue[2]['name'] . '_error">&nbsp;</span>';
                } else if ($fieldvalue[2] == 'CHECKBOX') {
                    if (isset($this->CI->input->post))
                        $fieldvalue[3]['value'] = (!$this->CI->input->post($fieldvalue[1])) ? @$fieldvalue[3]['value'] : $this->CI->input->post($fieldvalue[1]);
                    else
                        $fieldvalue[3]['value'] = ($values) ? @$values[$fieldvalue[1]] : @$fieldvalue[3]['value'];

                    if ($fieldvalue[3]['value'] == "1") {
                        $checked = true;
                    } else {
                        $checked = false;
                    };
                    $form_contents.= form_checkbox($fieldvalue[1], $fieldvalue[3]['value'], $checked);
                } else if ($fieldvalue[1] == 'TEXTAREA') {

                    if (isset($this->CI->input->post))
                        $fieldvalue[2]['value'] = (!$this->CI->input->post($fieldvalue[2]['name'])) ? @$fieldvalue[2]['value'] : $this->CI->input->post($fieldvalue[2]['name']);
                    else
                        $fieldvalue[2]['value'] = ($values) ? $values[$fieldvalue[2]['name']] : @$fieldvalue[2]['value'];
                    $form_contents.= form_textarea($fieldvalue[2]);
                }
                else if ($fieldvalue[2] == 'RADIO') {

                    $form_contents.= form_radio($fieldvalue[1], $fieldvalue[3]['value'], $fieldvalue[3]['checked']);
                }
                $form_contents.= '</li>';
            }

            $form_contents.= '</ul>';
            $form_contents.= '<div>';
            $form_contents.= '</div>';
            $form_contents.= '</div>';
            $form_contents.= '</div>';
            $i++;
        }

        $form_contents.= '<div style="width:100%; float:left;height:40px;margin-top:20px;">';

        if (isset($cancel)) {
            $form_contents.= form_button($cancel);
        }

        $form_contents.= form_button($save);

        $form_contents.= '</div>';
        $form_contents.= form_fieldset_close();
        $form_contents.= form_close();
        $form_contents.= '</div>';


        return $form_contents;
    }

    function build_serach_form($fields_array) {
        $form_contents = '';
        $form_contents.= '<div >';
        $form_contents.= form_open($fields_array['forms'][0], $fields_array['forms'][1]);
        unset($fields_array['forms']);
        $button_array = array();
        if (isset($fields_array['button_search']) || isset($fields_array['button_reset'])) {
            $save = $fields_array['button_search'];
            unset($fields_array['button_search']);
            if ($fields_array['button_reset']) {
                $cancel = $fields_array['button_reset'];
                unset($fields_array['button_reset']);
            }
        }
        $i = 1;
        foreach ($fields_array as $fieldset_key => $form_fileds) {

            $form_contents.= '<ul>';
            $form_contents.= form_fieldset($fieldset_key, array('style' => 'margin-left:0px;font-weight:bold;'));

            foreach ($form_fileds as $fieldkey => $fieldvalue) {

                if ($i == 0) {
                    $form_contents.= '<li>';
                }
                $form_contents.= '<div class="float-left" style="width:32%">';
                if ($fieldvalue[1] == 'HIDDEN') {
                    $form_contents.= form_hidden($fieldvalue[2], $fieldvalue[3]);
                } else {
                    $form_contents.= form_label($fieldvalue[0], "", array("class" => "search_label")) . "<br/><br/>";
                }
                if ($fieldvalue[1] == 'INPUT') {
                    $form_contents.= form_input($fieldvalue[2]);
                }
                if ($fieldvalue[2] == 'SELECT' || $fieldvalue[5] == '1') {

                    if ($fieldvalue[7] != '' && $fieldvalue[8] != '') {
                        $str = $fieldvalue[7] . "," . $fieldvalue[8];

                        $drp_array = call_user_func_array(array($this->CI->db_model, $fieldvalue[10]), array($str, $fieldvalue[9], $fieldvalue[11], $fieldvalue[12]));
                        $form_contents.=form_dropdown_all($fieldvalue[1], $drp_array, '');
                    } else {

                        if ($fieldvalue[1] == 'INPUT') {
                            $fieldvalue[1] = $fieldvalue[6];
                        }
                        $drp_array = call_user_func_array(array($this->CI->common, $fieldvalue[10]), array($fieldvalue[9]));
                        $form_contents.=form_dropdown($fieldvalue[1], $drp_array, '');
                    }
                } else if ($fieldvalue[1] == 'PASSWORD') {
                    $form_contents.= form_password($fieldvalue[2]);
                } else if ($fieldvalue[2] == 'CHECKBOX') {
                    $form_contents.= form_checkbox($fieldvalue[1], $fieldvalue[3]['value'], $fieldvalue[3]['checked']);
                }
                $form_contents.= '</div>';
                if ($i % 5 == 0) {
                    $form_contents.= '</li>';
                    $i = 0;
                }
                $i++;
            }
        }

        $form_contents.= '</ul>';
        $form_contents.= '<div style="width:100%; float:left;height:40px;margin-top:20px;">';


        $form_contents.= form_button($cancel);
        $form_contents.= form_button($save);
        $form_contents.= form_fieldset_close();
        $form_contents.= form_close();
        $form_contents.= '</div>';
        $form_contents.= '</div>';

        return $form_contents;
    }
    function build_batchupdate_form($fields_array) {
        $form_contents = '';
        $form_contents.= '<div >';
        $form_contents.= form_open($fields_array['forms'][0], $fields_array['forms'][1]);
        unset($fields_array['forms']);
        $button_array = array();
        if (isset($fields_array['button_search']) || isset($fields_array['button_reset'])) {
            $save = $fields_array['button_search'];
            unset($fields_array['button_search']);
            if ($fields_array['button_reset']) {
                $cancel = $fields_array['button_reset'];
                unset($fields_array['button_reset']);
            }
        }
        $i = 1;
        foreach ($fields_array as $fieldset_key => $form_fileds) {

            $form_contents.= '<ul>';
            $form_contents.= form_fieldset($fieldset_key, array('style' => 'margin-left:0px;font-weight:bold;'));
            foreach ($form_fileds as $fieldkey => $fieldvalue) {
                if ($i == 0) {
                    $form_contents.= '<li>';
                }
                $form_contents.= '<div class="float-left" style="width:33%">';
                if ($fieldvalue[1] == 'HIDDEN') {
                    $form_contents.= form_hidden($fieldvalue[2], $fieldvalue[3]);
                } else {
                    $form_contents.= form_label($fieldvalue[0], "", array("class" => "search_label")) . "<br/><br/>";
                }
                if ($fieldvalue[2] == 'SELECT' || $fieldvalue[5] == '1') {
                    if ($fieldvalue[7] != '' && $fieldvalue[8] != '') {
                        $str = $fieldvalue[7] . "," . $fieldvalue[8];
                        if(is_array($fieldvalue[13])){
                            $drp_array = call_user_func_array(array($this->CI->common, $fieldvalue[14]), array($fieldvalue[13]));
                            $form_contents.=form_dropdown($fieldvalue[13], $drp_array, '');
                        }
                        $drp_array = call_user_func_array(array($this->CI->db_model, $fieldvalue[10]), array($str, $fieldvalue[9], $fieldvalue[11], $fieldvalue[12]));
                        $form_contents.=form_dropdown_all($fieldvalue[1], $drp_array, '');
                    } else {
                        if ($fieldvalue[1] == 'INPUT') {
                            $drp_name = $fieldvalue[6];
                        }
                        $drp_array = call_user_func_array(array($this->CI->common, $fieldvalue[10]), array($fieldvalue[9]));
                        $form_contents.=form_dropdown($drp_name, $drp_array, '');
                    }
                }
                if ($fieldvalue[1] == 'INPUT') {
                    $form_contents.= form_input($fieldvalue[2]);
                } else if ($fieldvalue[2] == 'CHECKBOX') {
                    $form_contents.= form_checkbox($fieldvalue[1], $fieldvalue[3]['value'], $fieldvalue[3]['checked']);
                }
                $form_contents.= '</div>';
                if ($i % 5 == 0) {
                    $form_contents.= '</li>';
                    $i = 0;
                }
                $i++;
            }
        }

        $form_contents.= '</ul>';
        $form_contents.= '<div style="width:100%; float:left;height:40px;margin-top:20px;">';

        $form_contents.= form_button($cancel);
        $form_contents.= form_button($save);
        $form_contents.= form_fieldset_close();
        $form_contents.= form_close();
        $form_contents.= '</div>';
        $form_contents.= '</div>';

        return $form_contents;
    }

    function load_grid_config($count_all, $rp, $page) {
        $json_data = array();
        $config['total_rows'] = $count_all;
        $config['per_page'] = $rp;

        $page_no = $page;
        $json_data["json_paging"]['page'] = $page_no;

        $json_data["json_paging"]['total'] = $config['total_rows'];
        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;
        $json_data["paging"]['start'] = $start;
        $json_data["paging"]['page_no'] = $perpage;
        return $json_data;
    }

    function build_grid($query, $grid_fields) {
        $jsn_tmp = array();
        $json_data = array();
        if ($query->num_rows > 0) {
            foreach ($query->result_array() as $row) {
                foreach ($grid_fields as $field_key => $field_arr) {
                    if ($field_arr[2] != "") {
                        if ($field_arr[3] != "") {
                            $jsn_tmp[$field_key] = call_user_func_array(array($this->CI->common, $field_arr[5]), array($field_arr[3], $field_arr[4], $row[$field_arr[2]]));
                        } else {
                            $jsn_tmp[$field_key] = $row[$field_arr[2]];
                        }
                    } else {
                        if ($field_arr[0] == "Action") {
                            $jsn_tmp[$field_key] = $this->CI->common->get_action_buttons($field_arr[5], $row["id"]);
                        } else {
                            $jsn_tmp[$field_key] = '<input type="checkbox" name="chkAll" id=' . $row['id'] . ' class="chkRefNos" onclick="clickchkbox(' . $row['id'] . ')" value=' . $row['id'] . '>';
                        }
                    }
                }
                $json_data[] = array('cell' => $jsn_tmp);
            }
        }
        return $json_data;
    }

    function build_json_grid($query, $grid_fields) {
        $jsn_tmp = array();
        $json_data = array();
        foreach ($query as $row) {
            foreach ($grid_fields as $field_key => $field_arr) {
                if ($field_arr[2] != "") {
                    if ($field_arr[3] != "") {
                        $jsn_tmp[$field_key] = call_user_func_array(array($this->CI->common, $field_arr[5]), array($field_arr[3], $field_arr[4], $row[$field_arr[2]]));
                    } else {
                        $jsn_tmp[$field_key] = isset($row[$field_arr[2]]) ? $row[$field_arr[2]] : "";
                    }
                } else {
                    if ($field_arr[0] == "Action") {
                        $jsn_tmp[$field_key] = $this->CI->common->get_action_buttons($field_arr[5], $row["id"]);
                    }
                }
            }
            $json_data[] = array('cell' => $jsn_tmp);
        }
        return $json_data;
    }

}
