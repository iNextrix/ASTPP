<?php

class Reports_model extends CI_Model {

    function Reports_model() {
        parent::__construct();
    }

    function getsystem_list($flag, $start, $limit) {
        $this->db_model->build_search('customer_cdr_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("reseller_id" => $account_data['id']);
        } else {
            $where = array("reseller_id" => "0");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "customer_cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "customer_cdrs", $where);
        }
        return $query;
    }

    function getreseller_list($flag, $start, $limit) {
        $this->db_model->build_search('reseller_cdr_list_search');
        $this->db_model->build_search('customer_cdr_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("reseller_id" => $account_data['id'],"accountid <>"=>$account_data['id']);
        } else {
            $where = array("reseller_id" => "0");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "reseller_cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "reseller_cdrs", $where);
        }
        return $query;
    }

    function getprovider_list($flag, $start, $limit) {
        $this->db_model->build_search('provider_cdr_list_search');
        if ($this->session->userdata('logintype') == 3) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("accountid"=>$account_data['id']);
        }
        if ($flag) {
            $query = $this->db_model->select("*", "provider_cdrs", "", "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "provider_cdrs", "");
        }
        return $query;
    }

    function getReseller($username = "", $type) {
        $reseller = "";
        if ($username != "") {
            $reseller = "reseller_id = '" . $username . "' AND";
        }
        $q = "SELECT * FROM accounts WHERE  " . $reseller . " type IN ('" . $type . "')";

        $query = $this->db->query($q);

        $options = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $options[] = $row['number'];
            }
        }
        return $options;
    }

    function getDestination($username = "") {
        $notes = "";
        if ($username != "") {
            $notes = "WHERE notes LIKE '" . $username . " %'";
        }
        $q = "SELECT DISTINCT notes,pattern FROM cdrs " . $notes . " ";

        $query = $this->db->query($q);

        $options = array();
        $dst = array();
        $ptn = array();

        if ($query->num_rows() > 0) {
            $i = 0;
            foreach ($query->result_array() as $row) {

                $notes = $row['notes'];
                $note = preg_split('/(\^|DID:)/', $notes, 2);
                $caret_sign = "";
                if (isset($note[1])) {
                    $caret_sign = "^";
                }
                $pos = strpos($notes, "DID:");
                if ($pos) {
                    $caret_sign = 'DID:';
                }

                //$ptn[$i] =  @$caret_sign.@$note[1] . @$note[2];				
                $ptn[$i] = $row['pattern'];
                $note = preg_split('/\|/', $note[0]);
                $dst[$i] = (@$note == 1) ? @$note[0] : ( (@$note[0] != "") ? @$note[1] : "");
                $i++;
            }
        }


        return array('1' => array_unique($dst), '2' => array_unique($ptn));
    }

    function getCardNum($reseller, $table, $start, $limit, $name) {
        $admin_reseller_report = array();
        $q = "SELECT DISTINCT accountid AS '" . $name . "' FROM $table";

        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {

                $bth = @$row['' . $name . ''];
                //$bth = $this->session->userdata('username');

                $sql1 = "SELECT notes,pattern, COUNT(*) AS attempts, AVG(billseconds) AS acd,"
                        . " MAX(billseconds) AS mcd, SUM(billseconds) AS billable, "
                        . " SUM(debit) AS cost, SUM(cost) AS price FROM "
                        . $table . " WHERE (notes IS NOT NULL AND notes != '') AND accountid = '" . $bth . "' GROUP BY notes";
                $query1 = $this->db->query($sql1);
                //echo $query1->num_rows();
                if ($query1->num_rows() > 0) {
                    foreach ($query1->result_array() as $row1) {
                        $note1 = preg_split('/(\^|DID:)/', $row1['notes'], 2);
                        $caret_sign = "";
                        if (isset($note1[1])) {
                            $caret_sign = "^";
                        }
                        $pos = strpos($row1['notes'], "DID:");
                        if ($pos) {
                            $caret_sign = 'DID:';
                        }

                        $idd = $caret_sign . @$note1[1] . @$note1[2];
                        // $note1 = explode( "[|.-]", @$note1[0] );
                        $note1 = preg_split('/\|/', @$note1[0]);
                        $dst = ( @$note1[0] == 1 ) ? @$note1[0] : @$note1[1];
                        if ($dst == "")
                            $dst = $row1['pattern'];
                        $atmpt = $row1['attempts'];
                        $acd = $row1['acd'];
                        $mcd = $row1['mcd'];
                        $bill = $row1['billable'];
                        $price = $row1['price'];
                        $cost = $row1['cost'];

                        $notes = $row1['notes'];

                        $notes = "notes = '" . $row1['notes'] . "' ";

                        $sql2 = "SELECT COUNT(*) AS completed FROM $table WHERE $notes AND disposition IN ('ANSWERED','NORMAL_CLEARING')";
// 							echo $sql2."<br/>";
                        $query2 = $this->db->query($sql2);
                        $row2 = $query2->row_array();
                        $cmplt = ($row2['completed'] != 0) ? $row2['completed'] : 0;

                        $asr = ( ( $atmpt - $cmplt ) / $atmpt ) * 100;

                        $in = "";
                        $sql3 = "SELECT uniqueid FROM $table WHERE $notes ";
                        $query3 = $this->db->query($sql3);
                        foreach ($query3->result_array() as $row3) {
                            if ($row3['uniqueid'])
                                $in .= "'" . $row3['uniqueid'] . "',";
                        }

                        if (strlen($in) > 0)
                            $in = substr($in, 0, -1);

//                        $this->db_fscdr = Common_model::$global_config['fscdr_db'];
//                        $sql4 = "SELECT SUM(duration) AS actual FROM " . Common_model::$global_config['system_config']['freeswitch_cdr_table'] . " WHERE uniqueid IN ($in)";
//                        $query4 = $this->db_fscdr->query($sql4);
                        $sql4 = "SELECT SUM(billseconds) AS actual FROM cdrs WHERE uniqueid IN ($in)";                        
                        $query4 = $this->db->query($sql4);
                        $row4 = $query4->row_array();

                        $act = $row4['actual'];

                        $act = (int) ( $act / 60 ) . ":" . ( $act % 60 );
                        $acd = (int) ( $acd / 60 ) . ":" . ( $acd % 60 );
                        $mcd = (int) ( $mcd / 60 ) . ":" . ( $mcd % 60 );
                        $bill = (int) ( $bill / 60 ) . ":" . ( $bill % 60 );
                        $price = $price / 1;
                        $cost = $cost / 1;


                        $admin_reseller_report[] = array('bth' => $bth, 'dst' => $dst, 'idd' => $idd, 'atmpt' => $atmpt, 'cmplt' => $cmplt, 'asr' => round($asr, 2), 'acd' => $acd, 'mcd' => $mcd, 'act' => $act, 'bill' => $bill, 'price' => $price, 'cost' => $cost);
                    }
                }
            }
        }
        $sth = mysql_query("DROP TEMPORARY TABLE $table");
        //$sth = $this->db->query("DROP VIEW $table");
        return $admin_reseller_report;
    }

    function getcustomercdrs($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('customer_cdr_list_search');
        $this->db->from('customer_cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {

            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getresellercdrs($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('resller_cdr_list_search');
        $this->db->from('reseller_cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getprovidercdrs($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('provider_cdr_list_search');
        $this->db->from('provider_cdrs');
        $this->db->order_by("callstart desc");
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getuser_cdrs_list($flag, $start, $limit, $accountid = "") {
        if ($accountid == "") {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("accountid" => $account_data["id"]);
        } else {
            $where = array("accountid" => $accountid);
        }
        $this->db_model->build_search('customer_cdr_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "customer_cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "customer_cdrs", $where);
        }

        return $query;
    }

    function getuser_payment_list($flag, $start, $limit) {
        $this->db_model->build_search('cdr_payment_search');
        $account_data = $this->session->userdata("accountinfo");
        $where = array("accountid" => $account_data["id"]);
        if ($flag) {
            $query = $this->db_model->select("*", "payments", $where, "accountid", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "payments", $where);
        }

        return $query;
    }

    function getcustomer_payment_list($flag, $start, $limit) {
        $this->db_model->build_search('cdr_payment_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $where = array("payment_by"=>$accountinfo["id"]);
        } else {
            $where = array("payment_by"=>"-1");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "payments", $where, "accountid", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "payments", $where);
        }

        return $query;
    }
    function getcustomer_cdrs_list($flag, $start, $limit, $accountid = "") {
        if ($accountid == "") {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("accountid" => $account_data["id"]);
        } else {
            $where = array("accountid" => $accountid);
        }
        $this->db_model->build_search('customer_cdr_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "cdrs", $where, "callstart", "DESC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "cdrs", $where);
        }

        return $query;
    }
    function getreseller_commission_list($flag, $start, $limit) {
        $this->db_model->build_search('reseller_commission_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $accountinfo = $this->session->userdata['accountinfo'];
            $reseller_id = $accountinfo["id"];
        } else {
            $reseller_id = "0";
        }
        if ($flag) {
            $query = $this->db_model->select_by_in("*", "commission","" , "date", "DESC", $limit, $start,"","reseller_id",$reseller_id);

        } else {
            $query = $this->db_model->countQuery_by_in("*", "commission", "","reseller_id",$reseller_id);
        }

        return $query;
    }
    

}
