<?php
class Analytics_model extends CI_Model {

    const CATEGORY_CODE    = 'ANALYTICS';
    const MAX_REPORT_COUNT = 10;

    const RESOURCES = array(
        array(
            'id'           => 1,
            'description'  => 'Report by direction',
            'resource_url' => 'analytics/rep_direction',
        ),
        array(
            'id'           => 2,
            'description'  => 'User payment per day',
            'resource_url' => 'analytics/rep_payperday',
        ),
    );

    function __construct()
    {
        parent::__construct();
    }

    function getResources() {
        return serialize(self::RESOURCES);
    }


    function getRATTR($n, $res){
        $rid = NULL;

        foreach (self::RESOURCES as $r_item){
            if (strpos($r_item['resource_url'], $n)){
                $rid = $r_item[$res];
                break;
            }
        }

        return $rid;
    }

    function getMenuItems() {
        $acc_info  = $this->session->userdata('accountinfo');
        $menu_list = unserialize($this->session->userdata('menuinfo'));
        $permited  = array();

        $query_permited = $this->db->query("SELECT p.attr FROM order_items o, products p WHERE o.is_terminated = 0 and o.accountid = ".$acc_info['id']." and o.product_id = p.id and o.product_category = p.product_category AND p.status = 0");
        $query_catname  = $this->db->query("SELECT name FROM category WHERE code = '".self::CATEGORY_CODE."' AND status = 0");
        $ds_catname     = $query_catname->result_array();
        $category_name  = $ds_catname[0]['name'];

        if ($query_permited->num_rows() > 0) {
            $ds_permited = $query_permited->result_array();
            foreach ($ds_permited as $list) {
                $permited = array_merge(explode(',', $list['attr']), $permited);
            }
            foreach (self::RESOURCES as $r_item){
                if (in_array($r_item[id], $permited)){
                    $menu_list['Reports'][$category_name][] = array (
                        'menu_label' => $r_item['description'],
                        'menu_type'  => 'service',
                        'module_url' => $r_item['resource_url'],
                        'module'     => strtolower($category_name),
                        'menu_image' => ''
                    );
                }
            }
        } elseif ($acc_info['type'] == -1) {
            foreach (self::RESOURCES as $r_item){
                $menu_list['Reports'][$category_name][] = array (
                    'menu_label' => $r_item['description'],
                    'menu_type'  => 'service',
                    'module_url' => $r_item['resource_url'],
                    'module'     => strtolower($category_name),
                    'menu_image' => ''
                );
            }
        }

        $this->session->set_userdata ( 'menuinfo', serialize ( $menu_list ) );

        return true;
    }

    function isResourcePermited($r_name){
        $rid            = $this->getRATTR($r_name, 'id');
        $acc_info       = $this->session->userdata('accountinfo');
        $res            = FALSE;
        $permited       = array();
        $query_permited = $this->db->query("SELECT p.attr, c.name FROM order_items o, products p, category c WHERE o.is_terminated = 0 and o.accountid = ".$acc_info['id']." and o.product_id = p.id and o.product_category = p.product_category AND p.status = 0 AND p.product_category = c.id AND c.code = '".self::CATEGORY_CODE."' AND c.status = 0");

        if ($query_permited->num_rows() > 0) {
            $ds_permited = $query_permited->result_array();
            foreach ($ds_permited as $list) {
                $permited = array_merge(explode(',', $list['attr']), $permited);
            }

            $res = in_array($rid, $permited);
        }

        return $acc_info['type'] == -1 ? true : $res;
    }

    function addReport($bd, $ed, $aid) {
        $login_type = ($this->session->userdata('accountinfo'))['type'];
        $accid      = ($this->session->userdata('accountinfo'))['id'];
        $raid       = $login_type == -1 ? $aid : $accid;
        $raid       = $raid ? $raid : 'null';
        $hsum       = md5($bd.$ed.$raid);

        $report_params = array(
                'aid'    => $accid,
                'raid'   => $raid == 'null'? null: $raid,
                'bdate'  => $bd,
                'edate'  => $ed,
                'hsum'   => $hsum,
                'locale' => $this->session->userdata('user_language')
            );

        $query_exist = $this->db->query("select pstatus, fname from ar_directions where aid=$accid and hsum='$hsum' and raid=$raid");

        if ($query_exist->num_rows() > 0) {
            $ds_exist = ($query_exist->result_array())[0];
    
            if ($ds_exist['pstatus']=='R' && strlen($ds_exist['fname'])>0){
                $report_params['pstatus'] = 'R';
                $report_params['fname']   = $ds_exist['fname'];
            }
        }

        return $this->db->insert('ar_directions', $report_params);
    }

    function getPStatusText($s){
        $str = '';

        switch ( $s ) {
            case 'O':
                $str = gettext('Ordered');
                break;
            case 'P':
                $str = gettext('Processing');
                break;
            case 'R':
                $str = gettext('Ready');
                break;

        };

        return $str;
    }

    function getDownloadLink($res){
        return $res ? "<a href=".$this->router->fetch_method()."/getreport/$res target=_blank>".gettext('Link')."</a>":'';
    }

    function getReportsList() {
        $rep_list   = array();
        $acc_info   = $this->session->userdata('accountinfo');
        $query_list = $this->db->query("SELECT if(raid IS NULL,'All', (SELECT first_name FROM accounts WHERE id=raid)) AS uname , bdate, edate, DATE_FORMAT(cdate, '%Y-%m-%d') as cdate, pstatus, if(length(fname)>0, hsum, '') as hsum from ar_directions where aid=".$acc_info['id']);

        if ($query_list->num_rows() > 0) {
            $rep_list = $query_list->result_array();

            foreach ($rep_list as $key => $val){
                $rep_list[$key]['pstatus'] = $this->getPStatusText($val['pstatus']);
                $rep_list[$key]['hsum']    = $this->getDownloadLink($val['hsum']);
                $rep_list[$key]['uname']   = gettext($val['uname']);
            }
        }

        return $rep_list;
    }

    function getFileNameByHsum($hsum) {
        $fname       = '';
        $acc_info    = $this->session->userdata('accountinfo');
        $query_fname = $this->db->query("select fname from ar_directions where hsum='$hsum' and aid=".$acc_info['id']);

        if ($query_fname->num_rows() > 0) {
            $fname = ($query_fname->result_array())[0]['fname'];
        }

        return $fname;
    }

    function setTaskStatus($hsum, $status, $fname=NULL) {
        return $this->db->update(
                    'ar_directions',
                    array('pstatus' => $status, 'fname' => $fname),
                    array('hsum' => $hsum)
                );
    }

    function getReportData($aid, $bd, $ed) {
        $where_acc1 = intval($aid) > 0 ? "AND c.accountid=$aid" : "";
        $where_acc2 = intval($aid) > 0 ? "AND d.accountid=$aid" : "";
        $directions_count = array();
        $query_dircount = $this->db->query("SELECT 
                                            b.did, r.destination,
                                            (SELECT COUNT(*) FROM cdrs d WHERE d.end_stamp >= STR_TO_DATE('$bd','%Y-%m-%d') AND d.end_stamp <= STR_TO_DATE('$ed','%Y-%m-%d') $where_acc2 AND d.did = b.did) AS cnt
                                            FROM (
                                                SELECT distinct c.did
                                                FROM cdrs c
                                                WHERE     c.end_stamp >= STR_TO_DATE('$bd','%Y-%m-%d')
                                                      AND c.end_stamp <= STR_TO_DATE('$ed','%Y-%m-%d')
                                                      AND c.did <> 0 $where_acc1
                                            ) b, ratedeck r
                                            WHERE b.did=r.id ORDER BY cnt DESC");

        if ($query_dircount->num_rows() > 0) {
            $directions_count = $query_dircount->result_array();
        }

        return $directions_count;
    }

    function checkTaskStatus($rid, $status){
        $query_status = $this->db->query("select rid from ar_directions where rid=$rid and pstatus='$status'");

        return $query_status->num_rows() > 0;
    }

    function getTaskList() {
        $tlist       = array();
        $query_tlist = $this->db->query("select rid, raid, bdate, edate, DATE_FORMAT(cdate, '%Y-%m-%d') as cdate, hsum, locale from ar_directions where pstatus='O'");

        if ($query_tlist->num_rows() > 0) {
            $tlist = $query_tlist->result_array();
        }

        return $tlist;
    }

    function getReportListForDelete() {
        $rlist       = array();
        $acc_info    = $this->session->userdata('accountinfo');
        $query_rlist = $this->db->query("SELECT a.rid, a.hsum, a.fname, (SELECT COUNT(*) FROM ar_directions b WHERE b.aid=a.aid AND b.hsum=a.hsum LIMIT ".self::MAX_REPORT_COUNT." ) AS hsum_cnt FROM ar_directions a WHERE a.aid IN (SELECT DISTINCT aid FROM ar_directions) ORDER BY cdate DESC LIMIT ".self::MAX_REPORT_COUNT.", 10000");

        if ($query_rlist->num_rows() > 0) {
            $rlist = $query_rlist->result_array();
        }

        return $rlist;
    }

    function delRepDirection($rid) {
        return $this->db->delete(
                'ar_directions',
                array('rid' => $rid)
            );
    }
}
?>
