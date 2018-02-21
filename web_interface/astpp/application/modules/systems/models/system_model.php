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
class System_model extends CI_Model {

    function System_model() {
        parent::__construct();
    }

    function getsystem_list($flag, $start, $limit) {

        $this->db_model->build_search('configuration_search');
        $where = "group_title NOT IN ('asterisk','osc','freepbx')";
        $this->db->where($where);
        if ($flag) {
            $query = $this->db_model->select("*", "system", "", "group_title,name", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "system", "");
        }
        return $query;
    }

    function gettemplate_list($flag, $start, $limit) {

        $this->db_model->build_search('template_search');
        if ($flag) {
            $query = $this->db_model->select("*", "default_templates", "", "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "default_templates", "");
        }
        return $query;
    }

    function edit_configuration($add_array, $id) {
        unset($add_array["action"]);
        $this->db->where("id", $id);
        $this->db->update("system", $add_array);
    }

    function edit_template($data, $id) {
        unset($data["action"]);
        $data["modified_date"] = date("Y-m-d H:i:s");
        $this->db->where("id", $id);
        $this->db->update("default_templates", $data);
    }

    function remove_template($id) {
        $this->db->where("id", $id);
        $this->db->update("default_templates", array("status" => 2));
        return true;
    }

    function getcountry_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('country_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "countrycode", '', "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "countrycode", '');
        }
        return $query;
    }

    function add_country($add_array) {
        unset($add_array["action"]);
        $this->db->insert("countrycode", $add_array);
        return true;
    }

     function edit_country($data, $id) {
        unset($data["action"]);
        $this->db->where("id", $id);
        $this->db->update("countrycode", $data);
    }

    function remove_country($id) {
        $this->db->where("id", $id);
        $this->db->delete("countrycode");
        return true;
    }

    function getcurrency_list($flag, $start = 0, $limit = 0) {
         $this->db_model->build_search('currency_list_search');
	
	$where=array('currency <>' =>Common_model::$global_config['system_config']['base_currency']);
        if ($flag) {
            $query = $this->db_model->select("*", "currency", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "currency", $where);
        }
        return $query;
    }

    function add_currency($add_array) {
        unset($add_array["action"]);
// 	echo "<pre>";print_r($add_array);exit;
        $this->db->insert("currency", $add_array);
        return true;
    }

     function edit_currency($data, $id) {
        unset($data["action"]);
        $this->db->where("id", $id);
        $this->db->update("currency", $data);
    }

    function remove_currency($id) {
        $this->db->where("id", $id);
        $this->db->delete("currency");
        return true;
    }
    function backup_insert($add_array='')
    {
	 unset($add_array["action"]);
        $this->db->insert("backup_database", $add_array);
        return true;
    }
     function getbackup_list($flag, $start, $limit) {

        if ($flag) {
            $query = $this->db_model->select("*", "backup_database", "", "date", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "backup_database", "");
        }
        return $query;
    }
    function get_backup_data($id)
    {
      $where=array('id'=>$id);
      $query = $this->db_model->getSelect("*", "backup_database", $where);
      return $query;
    }
   function import_database($filename,$target_path)
    {
		$this->db->insert("backup_database", array('backup_name'=>$filename , 'path'=>$target_path));
        	return true;
    }
}
