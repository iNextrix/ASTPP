<?php

class Opensips_model extends CI_Model {

    function Opensips_model() {
        parent::__construct();
    }

    function add_opensips($data) {
        $this->load->library("curl");
        $url = "astpp-wraper.cgi";
        $data['mode'] = "Open SIP Devices";
        $data['logintype'] = $this->session->userdata('logintype');
        $data['username'] = $this->session->userdata('username');
        $this->curl->sendRequestToPerlScript($url, $data);
    }

    function edit_opensips($data) {
        $this->load->library("curl");
        $url = "astpp-wraper.cgi";
        $data['mode'] = "Open SIP Devices";
        $data['logintype'] = $this->session->userdata('logintype');
        $data['username'] = $this->session->userdata('username');
        $this->curl->sendRequestToPerlScript($url, $data);
    }

    function get_opensip_count() {
        return $this->opensips_db->get("subscriber");
    }

    function get_data() {
       
        $query = $this->opensips_db->get("subscriber");
        return $query;
    }
     function getdata($id) {
         
          $this->opensips_db->where('id',$id);
        $query = $this->opensips_db->get("subscriber");
        return $query->result_array();
    }

    function add_opensip($data) {
        $insertdata = array(
            "username" => trim($data['username']),
            "password" => trim($data['password']),
            "domain" => trim($data['domain']),
            "accountcode" => trim($data['accountcode'])
        );
        $this->opensips_db->insert('subscriber', $insertdata);
        return true;
    }
    function update_opensip($id,$data) {

        $updatedata = array(
            "username" => trim($data['username']),
            "password" => trim($data['password']),
            "domain" => trim($data['domain']),
            "accountcode" => trim($data['accountcode'])
        );
        $this->opensips_db->where('id',$id);
        $this->opensips_db->update('subscriber', $updatedata);
        return true;
    }

    function delete_opensips($id) {
      
        $this->opensips_db->where('id',$id);
        $this->opensips_db->delete('subscriber');
        return true;
    }
    function get_dispatcher_count()
    {
        return $this->opensips_db->get("dispatcher");
    }
    function  get_dispatcher_data()
    {
         $query = $this->opensips_db->get("dispatcher");
        return $query;
    }

    function add_dispatcher($data)
    {
        $insertdata = array(
            "destination" => trim($data['destination']),
            "weight" => trim($data['weight']),
            "description" => trim($data['description']),
            "setid" => trim($data['setid']),
            "attrs" => trim($data['attrs']),
            "flags" => trim($data['flags'])
        
        );
        $this->opensips_db->insert('dispatcher', $insertdata);
        return true;
    }
    function update_dispatcher($id,$data)
    { 
        
        
        $updatedata = array(
           
               "destination" => trim($data['destination']),
            "weight" => trim($data['weight']),
            "description" => trim($data['description']),
            "setid" => trim($data['setid']),
            "attrs" => trim($data['attrs']),
            "flags" => trim($data['flags'])
              
        );
          
        $this->opensips_db->where('id',$id);
      $this->opensips_db->update('dispatcher', $updatedata);
       
        return true;
    }
    function get_dispatcher_data_by_id($id)
    { 
        $this->opensips_db->where('id',$id);
        $query = $this->opensips_db->get("dispatcher");

        return $query;
    }
    function delete_dispatcher($id)
    {
         $this->opensips_db->where('id',$id);
        $this->opensips_db->delete('dispatcher');
        return true;
    }
}

?>