<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fsmonitor_form {
      	function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
    function fsmonitor_details_button() {
        $buttons_json = json_encode(array(array("Create", "btn btn-line-warning btn" ,"fa fa-plus-circle fa-lg", "button_action", "/fsmonitor/fsmonitor_gateway_popup/", "popup"),
            ));
        return $buttons_json;
    }
}
