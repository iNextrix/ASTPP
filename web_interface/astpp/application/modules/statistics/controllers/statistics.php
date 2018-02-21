<?php

class Statistics extends CI_Controller {

    function Statistics() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library("statistics_form");
        $this->load->library('astpp/form');
        $this->load->model('statistics_model');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function listerrors() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Error List';
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->statistics_form->build_error_list_for_admin();
        $data["grid_buttons"] = $this->statistics_form->build_grid_buttons();
        $this->load->view('view_error_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function listerrors_json() {
        $json_data = array();
        $count_all = $this->statistics_model->geterror_list(false, "", "");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->statistics_model->geterror_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->statistics_form->build_error_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function trunkstats() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'TrunkStats List';
        $this->session->set_userdata('advance_search', 0);
	  $data['form_search'] = $this->form->build_serach_form($this->statistics_form->get_trunk_stat_search_form());
        if(isset($_POST) && !empty($_POST)){
            $search_data = $_POST;
            $this->session->set_userdata('trunk_stats_search', $search_data);
        }

        $this->load->view('view_statistics_trunkstats', $data);
    }

    /**
     * -------Here we write code for controller statistics functions trunkstats------
     * Listing of trunks stat data through php function json_encode
     */
    function trunkstats_json() {
        $where =  "";
        if(isset($this->session->userdata["trunk_stats_search"]) && !empty($this->session->userdata["trunk_stats_search"])){
            $where = " where ";
            $where_len = strlen($where);
            $search_data = $this->session->userdata("trunk_stats_search");
          
            if (!empty($search_data['start_date'])) {
                $where .="callstart >= '".$search_data['start_date']."' ";
            }
            if (!empty($search_data['end_date'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" callstart <= '".$search_data['end_date']."' ";
            }
            if (!empty($search_data['trunkid'])) {
                if(strlen($where) > $where_len)
                    $where .=" AND ";
                $where .=" trunk_id = '".$search_data['trunkid']."' ";
            }
        }
        $this->db_model->build_search('trunk_stats_search');
        $json_data = array();
        $sql1 = "SELECT count(*) as total_count FROM provider_cdrs $where GROUP BY trunk_id";	
        $query1 = $this->db->query($sql1);        
        $count_all = $query1->num_rows();
        
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $this->db_model->build_search('provider_summary_search');
        $sql1 = "SELECT trunk_id,uniqueid,notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                . " SUM(debit) AS cost, SUM(cost) AS price FROM provider_cdrs $where 
                      GROUP BY trunk_id  limit ".$paging_data["paging"]["start"].",". $paging_data["paging"]["page_no"];
        $query1 = $this->db->query($sql1);        
        
                if ($query1->num_rows() > 0) {
            foreach ($query1->result_array() as $row1) {
                $atmpt = $row1['attempts'];
                $acd = $row1['acd'];
                $mcd = $row1['mcd'];
                $bill = $row1['billable'];
                $price = $row1['price'];
                $cost = $row1['cost'];
                $profit = $row1['cost'] - $row1['price'];
                $sql2 = "SELECT COUNT(*) AS completed FROM cdrs
                  where disposition IN ('SUCCESS','NORMAL_CLEARING') AND pattern='".$row1['pattern']."' 
                    AND trunk_id='".$row1['trunk_id']."'";
                $query2 = $this->db->query($sql2);
                $row2 = $query2->row_array();
                $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;
                $asr =  ($cmplt/$atmpt)* 100;

                $json_data['rows'][] = array('cell' => array(
                    $this->common->get_field_name("name", "trunks",array("id"=>$row1['trunk_id'])),
//                     $this->common->get_only_numeric_val("","",$row1["pattern"]),
//                     $row1["notes"],
                    $atmpt,
                    $cmplt,
                    round($asr, 2),
                    round($acd/60, 2),
                    round($mcd/60, 2),                        
                    round($bill/60, 2)));
                }
        }

        echo json_encode($json_data);        
    }

}

?>
 
