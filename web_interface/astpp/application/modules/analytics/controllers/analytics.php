<?php
class Analytics extends MX_Controller {

    public function Analytics() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('analytics_model');

        if ($this->router->fetch_method() != 'genReports'){
            if ($this->session->userdata('user_login') == FALSE){
                redirect(base_url() . 'astpp/login');
                exit();
            }

            if (!$this->analytics_model->isResourcePermited($this->router->fetch_method())){
               redirect(base_url().'dashboard/');
               exit();
            }
        }
    }

    private function addReport($bd, $ed, $aid) {
        $ans = array(
                'error'  => 0,
                'status' => 'ok'
        );

        $bdi  = strtotime($bd);
        $edi  = strtotime($ed);
        $days = abs(round(($edi-$bdi) / 86400));

        if ( $bdi <= $edi ) {
            if ( $days <= 31 ) {
                $this->analytics_model->addReport($bd, $ed, $aid);
            } else {
                $ans['error']  = 1;
                $ans['status'] = gettext('Too much days in period');
            }
        }else{
            $ans['error']  = 1;
            $ans['status'] = gettext('Wrong period');
        }

        return json_encode($ans);
    }

    private function getReportsList(){
        return json_encode($this->analytics_model->getReportsList());
    }

    private function getReportForDownload($hsum){
        $fname = $this->analytics_model->getFileNameByHsum($hsum);

        if (strlen($fname)>0){
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename='.$fname);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($this->config->item('recordings_path').$fname);
        }
    }

    public function rep_direction($act = NULL, $prm = NULL) {
        $params = $this->input->post();

        if ($act === NULL){
            $data['page_title'] = $this->analytics_model->getRATTR($this->router->fetch_method(), 'description');
            $data['action']     = $act;
            $data['acc_list']   = $this->db_model->build_dropdown("id,first_name", "accounts", "where_arr", array("type" => "3", "type" => "0"));
            $data['login_type'] = ($this->session->userdata('accountinfo'))['type'];

            $this->load->view('view_rep_direction', $data);
        };

        if ($act === 'addreport'){
            $this->output->set_content_type('application/json');
            echo $this->addReport($params['bd'], $params['ed'], $params['aid']);
        };

        if ($act === 'getlist'){
            $this->output->set_content_type('application/json');
            echo $this->getReportsList();
        }

        if ( $act === 'getreport' && strlen($prm)>0 ){
            $this->getReportForDownload($prm);
        }

        return TRUE;
    }

    public function rep_payperday() {
        print('Dummy stub. Go back. Nothing doing here!');

        return TRUE;
    }

    private function gen_rep_direction(){
        $report_data = array();
        $task_list   = $this->analytics_model->getTaskList();

        require_once BASEPATH.'../'.APPPATH.'libraries'."/vendor/autoload.php";
        $this->html2pdf = new \Mpdf\Mpdf(['tempDir' => BASEPATH.'../'.APPPATH.'logs']);

        if (sizeof($task_list)){
            foreach($task_list as $titem){
                if ($this->analytics_model->checkTaskStatus($titem['rid'], 'O')){
                    $this->analytics_model->setTaskStatus($titem['hsum'], 'P');
                    $report_data['data']   = $this->analytics_model->getReportData($titem['raid'], $titem['bdate'], $titem['edate']);
                    $report_data['bdate']  = $titem['bdate'];
                    $report_data['edate']  = $titem['edate'];
                    $report_data['cdate']  = $titem['cdate'];
                    $report_data['locale'] = $titem['locale'];
                    $rendered_report = $this->load->view('view_rep_direction_tmpl', $report_data, 'TRUE');
                    $this->html2pdf->WriteHTML($rendered_report);
                    $this->html2pdf->Output($this->config->item('recordings_path').$titem['hsum'].'.pdf', 'F');
                    $this->analytics_model->setTaskStatus($titem['hsum'], 'R', $titem['hsum'].'.pdf');
                }
            }
        }

        return TRUE;

    }

    private function garb_rep_direction(){
        $rm_list = $this->analytics_model->getReportListForDelete();
        
        foreach($rm_list as $ritem){
            $this->analytics_model->delRepDirection($ritem['rid']);

            if (intval($ritem['hsum_cnt']) == 1){
                unlink($this->config->item('recordings_path').$ritem['fname']);
            }
        }
    }

    public function genReports() {
        $this->gen_rep_direction();
        $this->garb_rep_direction();

        return TRUE;
    }
}
?> 
