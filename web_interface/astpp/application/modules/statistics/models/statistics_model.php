<?php

class Statistics_model extends CI_Model {

    function Statistics_model() {
        parent::__construct();
    }

    function geterror_list($flag, $start, $limit) {
        $this->db_fscdr = Common_model::$global_config['fscdr_db'];
        if ($flag) {
            $this->db_fscdr->where_in('cost', array('error', 'rating', 'none'));
            $this->db_fscdr->or_where(array('accountcode' => NULL));
            $this->db_fscdr->or_where('accountcode', '');
// 		$this->db_fscdr->where('cost', 'none'); 
            $this->db->order_by("calldate", "desc");
            $this->db_fscdr->limit($limit, $start);
            $this->db_fscdr->from(Common_model::$global_config['system_config']['freeswitch_cdr_table']);
            $query = $this->db_fscdr->get();
            //echo $this->db->last_query();		
            return $query;
        } else {
            $this->db_fscdr->where_in('cost', array('error', 'rating'));
            $this->db_fscdr->or_where(array('accountcode' => NULL));
            $this->db_fscdr->or_where('accountcode', '');
            $this->db_fscdr->where('cost', 'none');
            $this->db_fscdr->from(Common_model::$global_config['system_config']['freeswitch_cdr_table']);
            $errorscnt = $this->db_fscdr->count_all_results();
            //echo $this->db_fscdr->last_query();
            return $errorscnt;
        }
        return $query;
    }

    function getTrunkStatsCount() {
        if ($this->session->userdata('advance_search') == 1) {
            $trunkstats_search = $this->session->userdata('trunkstats_search');
            $this->db->where('name ', $trunkstats_search['trunk']);
            if (!empty($trunkstats_search['start_date'])) {
                $sd = $trunkstats_search['start_date'] . ':00';
            }
            if (!empty($trunkstats_search['end_date'])) {
                $ed = $trunkstats_search['end_date'] . ':59';
            }
        }

        if ($this->session->userdata('logintype') == 3) {
            $this->db->where('provider', "" . $this->session->userdata('username') . "");
        }
        $this->db->from('trunks');
        $trunkcnt = $this->db->count_all_results();
        //echo $this->db->last_query();
        return $trunkcnt;
    }

    function getTrunkStatsList($start, $limit, $sd, $ed) {
        $trunkstats = array();
        if ($this->session->userdata('advance_search') == 1) {
            $trunkstats_search = $this->session->userdata('trunkstats_search');
            $this->db->where('name ', $trunkstats_search['trunk']);
            if (!empty($trunkstats_search['start_date'])) {
                $sd = $trunkstats_search['start_date'] . ':00';
            }
            if (!empty($trunkstats_search['end_date'])) {
                $ed = $trunkstats_search['end_date'] . ':59';
            }
        }
        if ($this->session->userdata('logintype') == 3) {
            $this->db->where('provider', "" . $this->session->userdata('username') . "");
        }
        $this->db->limit($limit, $start);
        $this->db->from('trunks');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($row['tech'] == "SIP") {
                    $path = explode("/", $row['path']);
                    $freeswitch_trunk = "%sofia/gateway/" . @$path[0] . "/%" . @$path[1];
                } else {
                    $freeswitch_trunk = $row['tech'];
                }
                $this->db_fscdr = Common_model::$global_config['fscdr_db'];
                $sql1 = "SELECT COUNT(*) AS calls, AVG(billsec) AS bs,"
                        . " AVG( duration-billsec ) as acwt from cdrs WHERE lastapp IN('Dial','Bridge')"
                        . " AND disposition IN ('ANSWERED','NORMAL_CLEARING')"
                        . " and calldate >= '" . $sd . "' and calldate <= '" . $ed . "' and (dstchannel like '" . $row['tech'] . "/" . $row['path'] . "%'"
                        . " or lastdata like '" . $freeswitch_trunk . "' ) ";

                $query1 = $this->db_fscdr->query($sql1);
                $row1 = $query1->row_array();
                $sql2 = " select count(*) as ct from " . Common_model::$global_config['system_config']['freeswitch_cdr_table'] . " where calldate >= '" . $sd . "' AND calldate <= '" . $ed
                        . "' AND disposition NOT IN('ANSWERED','16','NORMAL_CLEARING')"
                        . " AND (dstchannel like '" . $row['tech'] . "/" . $row['path'] . "%'"
                        . " or lastdata like '" . $freeswitch_trunk . "' )";

                $query2 = $this->db_fscdr->query($sql2);
                $row3 = $row2 = $query2->row_array();
                $sql4 = "SELECT COUNT(*) as ct from " . Common_model::$global_config['system_config']['freeswitch_cdr_table'] . " where calldate >= '" . $sd . "' AND calldate <= '" . $ed
                        . "' AND (dstchannel like '" . $row['tech'] . "/" . $row['path'] . "%'"
                        . " or lastdata like '" . $freeswitch_trunk . "' )";

                $query4 = $this->db_fscdr->query($sql4);
                $row4 = $query4->row_array();
                $success_rate = 0;
                $congestion_rate = 0;

                if ($row4['ct'] > 0 && $row1['calls'] > 0) {
                    $success_rate = ( $row1['calls'] / $row4['ct'] ) * 100;
                }
                if ($row4['ct'] > 0 && $row3['ct'] > 0) {
                    $congestion_rate = ( $row3['ct'] / $row4['ct'] ) * 100;
                }


                $trunkstats[] = array('tech_path' => $row['tech'] . "/" . $row['path'], 'ct' => $row4['ct'], 'bs' => number_format($row1['bs'], 2, '.', ''), 'acwt' => number_format($row1['acwt'], 2, '.', ''), 'calls' => $row1['calls'], 'success_rate' => number_format($success_rate, 2), 'congestion_rate' => number_format($congestion_rate, 2), 'failed_calls' => $row3['ct']);
            }
        }
        return $trunkstats;
    }

}
