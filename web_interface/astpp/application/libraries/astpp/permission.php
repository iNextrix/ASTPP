<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permission {
    function __construct($library_name = '') {
        $this->CI = & get_instance();
	$this->CI->load->model("db_model");
	$this->CI->load->library('session');
    }

    function get_module_access($user_type){
      $where = array("userlevelid"=>$user_type);
       $modules_arr = $this->CI->db_model->getSelect("module_permissions","userlevels",$where);
      if($modules_arr->num_rows > 0){
	$modules_arr = $modules_arr->result_array();
	$modules_arr = $modules_arr[0]['module_permissions'];

        $menu_arr = $this->CI->db_model->getSelect("*","menu_modules","id IN ($modules_arr)");
	$menu_list = array(); 
	$permited_modules = array();

        $modules_seq_arr = array();
        $modules_seq_arr = explode(",",$modules_arr);
        foreach($modules_seq_arr as $menu_key => $menu_sqe){
            foreach($menu_arr->result_array() as $menu_key =>$menu_value){ 
                if($menu_sqe == $menu_value["id"]){
                    $menu_value["menu_image"] = ($menu_value["menu_image"] == "")?"Home.png":$menu_value["menu_image"];
                    $menu_list[$menu_value["menu_title"]][$menu_value["menu_subtitle"]][] = array("menu_label" =>trim($menu_value["menu_label"]),
                                                "module_url"=>trim($menu_value["module_url"]),"module"=>trim($menu_value["module_name"]),
                                                "menu_image"=>trim($menu_value["menu_image"]));
                    $permited_modules[] = trim($menu_value["module_name"]);
                }
            }                
            
        }
     	$this->CI->session->set_userdata('permited_modules',serialize($permited_modules));
	$this->CI->session->set_userdata('menuinfo',serialize($menu_list));
  	return true;
      }
    }
}
?> 
