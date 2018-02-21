<?php

class System_model extends CI_Model {

    function System_model() {
        parent::__construct();
    }

    function getsystem_list($flag, $start, $limit) {

        $this->db_model->build_search('configuration_search');
        if ($flag) {
            $query = $this->db_model->select("*", "system", "", "id", "ASC", $limit, $start);
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

}
