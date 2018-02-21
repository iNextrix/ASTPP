<?php

class Taxes_model extends CI_Model {

    function Taxes_model() {
        parent::__construct();
    }

    function getcharges_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('taxes_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "taxes", '', "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "taxes", '');
        }
        return $query;
    }

    function add_tax($data) {
        unset($data["action"]);
        $data["date_added"] = date("Y-m-d H:i:s");
        $this->db->insert("taxes", $data);
    }

    function edit_tax($data, $id) {
        unset($data["action"]);
        $data["last_modified"] = date("Y-m-d H:i:s");
        $this->db->where("id", $id);
        $this->db->update("taxes", $data);
    }

    function remove_taxe($id) {
        $this->db->where("id", $id);
        $this->db->delete("taxes");
        return true;
    }

}
