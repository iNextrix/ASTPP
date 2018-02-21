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
class Email extends MX_Controller {

    function Email() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library('email_form');
        $this->load->library('astpp/form');
        $this->load->model('email_model');
        $this->load->library('csvreader');
	$this->load->library('astpp/email_lib');
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function email_edit($edit_id = '') {
        $data['page_title'] = 'Edit Email List';
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array('id' => $edit_id, "reseller_id" => $reseller);
        } else {
            $where = array('id' => $edit_id);
        }
        $account = $this->db_model->getSelect("*", "mail_details", $where);
        if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email(), $edit_data);
            $this->load->view('view_email_add_edit', $data);
        } else {
            redirect(base_url() . 'email/email_history_list/');
        }
            redirect(base_url() . 'email/email_history_list/');
    }

    function email_resend() {
        $add_array = $this->input->post();
      //  echo '<pre>'; print_r($add_array); exit;
        $data['page_title'] = 'Resand Email';
            $where = array('id' => $add_array['id']);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
	    $add_array=array('accountid'=>$edit_data['accountid'],
			     'subject'=>$add_array['subject'],
			     'body'=>$add_array['body'],
			     'from'=>$edit_data['from'],
			     'to'=>$edit_data['to'],
			     'status'=>$edit_data['status'],
			     'template'=>$edit_data['template'],
			   //  'attachment'=>$edit_data['attachment'],
			
			    );
                $this->email_re_send($add_array);		
        
	    $this->session->set_flashdata('astpp_errormsg', 'Email resend successfully!');
            redirect(base_url() . 'email/email_history_list/');
    }
    function email_resend_edit($edit_id = '') {
	$data['page_title'] = 'Resent Email';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
      if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email_edit(), $edit_data);

            $this->load->view('view_email_add_edit', $data);
        } else {
            redirect(base_url() . 'email/email_history_list/');
        }

    }
    function email_resend_edit_customer($edit_id = '') {
	$data['page_title'] = 'Resent Email';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
      if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email_view_cus_edit(), $edit_data);

            $this->load->view('view_email_add_edit', $data);
        } else {
            redirect(base_url() . 'email/email_history_list/');
        }

    }

    function email_resend_customer($edit_id = '') {
        $add_array = $this->input->post();
        $data['page_title'] = 'Resand Email';
            $where = array('id' => $add_array['id']);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
	    $add_array=array('accountid'=>$edit_data['accountid'],
			     'subject'=>$add_array['subject'],
			     'body'=>$add_array['body'],
			     'from'=>$edit_data['from'],
			     'to'=>$edit_data['to'],
			     'status'=>$edit_data['status'],
			     'template'=>$edit_data['template'],
			
			    );
            $this->email_model->add_email($add_array);
	    $this->email_lib->send_email('',$add_array,'','',1);

            $this->load->module('accounts/accounts');
	    $this->session->set_flashdata('astpp_errormsg', 'Email resend successfully!');
            redirect(base_url() . 'accounts/customer_edit/'.$value["accountid"]);
    }
    function email_add($type = "") {

        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Commission Rate';
        $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email(), '');

        $this->load->view('view_email_add_edit', $data);
    }
    function email_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit email List';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->email_model->edit_email($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Email list updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Email List';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $this->email_model->add_email($add_array);
                echo json_encode(array("SUCCESS"=> "Email list added successfully!"));
                exit;
            }
        }
    }
    function email_re_send($edit_data) {
	$this->email_lib->send_email('',$edit_data,'','',1);
	$this->session->set_flashdata('astpp_errormsg', 'Email resend successfully!');
        redirect(base_url() . '/email/email_history_list/');
    }
    function email_view($edit_id = '') {
	$data['page_title'] = 'View Email';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
      if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email_view(), $edit_data);

            $this->load->view('view_email_add_edit', $data);
        } else {
            redirect(base_url() . 'email/email_history_list/');
        }
    }
    function email_view_customer($edit_id = '') {
	$data['page_title'] = 'View Email';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
      if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $data['form'] = $this->form->build_form($this->email_form->get_form_fields_email_view_cus(), $edit_data);

            $this->load->view('view_email_add_edit', $data);
        } else {
            redirect(base_url() . 'email/email_history_list/'.$edit_id);
        }
    }
    function email_delete($id) {
        $this->email_model->remove_email($id);
        $this->session->set_flashdata('astpp_notification', 'Email removed successfully!');
        redirect(base_url() . '/email/email_history_list/');
    }
    function email_delete_cus($accountid,$id) {
        $this->email_model->remove_email($id);
        $where = array('id' => $id);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
	foreach ($account->result_array() as $key => $value) {
	    $edit_data = $value;
	}
        $url ="accounts/customer_edit/$accountid";
        $this->session->set_flashdata('astpp_notification', 'Email removed successfully!');
	$this->load->module('accounts/accounts');
        redirect(base_url() . $url);
    }
    function email_mass() {
	$data['username'] = $this->session->userdata('user_name');	
        $data['page_title'] = 'Email Mass';
        $data['form'] = $this->form->build_form($this->email_form->build_list_for_email_client_area(), '');
	$this->load->view('view_email_client_area',$data); 
    }
 /*Mass Email*/
   function attachment_icons($select = "", $table = "", $attachement="") {
	if($attachement!="")
	{
		$array=explode(",", $attachement);
		$str='';
	//echo '<pre>'; print_r($array); exit;	
		foreach($array as $key =>$val){
			$link = base_url() . "email/email_history_list_attachment/".$val;
			$str.="<a href='".$link."' title='".$val."' class='btn btn-royelblue btn-sm'><i class='fa fa-paperclip fa-fw'></i></a>&nbsp;&nbsp;";
		}
		return $str;
	}
	else{
		return "";
	}
    }
    function email_client_get()
      {
	       $files=$_FILES;
	//echo '<pre>';     print_r( $files); exit;
	       $add_array = $this->input->post();
	       $add_array['page_title'] = 'Compose email';
	       $nooffile= $files['file']['name'];
	       $count=count($nooffile);
	    /*   if(isset($files['file']['name'][3]) && $files['file']['name'][3] == ""){
		       $count=3;
	       }
	       if(isset($files['file']['name'][2]) && $files['file']['name'][2] == ""){
		       $count=2;
	       }
	       if(isset($files['file']['name'][1]) && $files['file']['name'][1] == ""){
	    		$count=1;
	       }*/
	       $add_array['attachment']='';
	       $add_array['file']='';
	        // echo '<pre>'; print_r($count); exit;
	       
	       for($i=0;$i<$count;$i++){
	       
		       $tmp_name[]= $files['file']['tmp_name'][$i];
		       if($files['file']['error'][$i]==0){
			       $cur_name = $files['file']['name'][$i];
			       $parts = explode(".", $cur_name);
			       $add_array['attachment'].=date('ymdhis').$i.'.'.$parts[1].',';
			       $add_array['file'].=date('ymdhis').$i.'.'.$parts[1].',';
		     	       $uploadedFile1 = $files['file']['tmp_name'][$i];
			       $user_name='inextrix';
			       $actual_file_name=date('ymdhis').$i.'.'.$parts[1];
			       $dir_path=  getcwd()."/attachments/";
			       $path =$dir_path.$actual_file_name;
	//print_r($path); exit;
			       if (move_uploaded_file($uploadedFile1,$path)) {
				   $this->session->set_flashdata('astpp_errormsg', 'files added successfully!');
			       }
			       else{
				    $this->session->set_flashdata('astpp_errormsg', 'Please try again   !');
			       }
		       }
	       }
	       $add_array['attachment']=trim($add_array['attachment'],',');
	       $add_array['file']=trim($add_array['file'],',');
               $add_array['email']= explode(",",$add_array['to']);   
               $this->email_model->multipal_email($add_array);
               $screen_path = getcwd()."/cron";
               $screen_filename = "Email_Broadcast_".strtotime('now');
               $command = "cd ".$screen_path." && /usr/bin/screen -d -m -S  $screen_filename php cron.php BroadcastEmail";
               exec($command);
               $this->session->set_flashdata('astpp_errormsg', 'Email broad cast successfully!');
               redirect(base_url() . 'email/email_history_list/');
               exit; 
        }
    
       function email_client_area() {
        $add_array = $this->input->post();
        if($add_array['temp'] == ''){
	 	$subject = '';
		$body ='';	
             }
	else{
                $where = array('id' => $add_array['temp']);
		$account = $this->db_model->getSelect("subject,template", "default_templates", $where);
		$account_data =$account->result_array();
                $subject = isset($account_data[0]['subject'])?$account_data[0]['subject']:'';
		$body = isset($account_data[0]['template'])?$account_data[0]['template']:'';	
	
            
        }
       	$count_all = $this->email_model->get_email_client_data($add_array);
	$email_arr = array();
	$id_arr = array();
	foreach($count_all as $key=>$value){
		$value = $value;
		if($value['email']!=''){
		$email_arr[$value['email']]= $value['email'];
		$id_arr[]= $value['id'];}
	} 
	if (empty($email_arr))
	{
        	$this->session->set_flashdata('astpp_notification', 'No record found! ');
	        redirect(base_url() . 'email/email_mass/');
	}
	$to_email = $email_arr;
	
	$to_id = $id_arr;
	$to_send_mail = implode(",",$to_email);
	if($to_send_mail != ''){
		$add_arr['email']= $email_arr;
	}
	$data['username'] = $this->session->userdata('user_name');	
	$data['page_title'] = 'Compose Email';
	$send_id = $this->db_model->getSelect("emailaddress", "invoice_conf", array());
	$send_id =$send_id->result_array();
	$send_id =$send_id[0]['emailaddress'];
	$add_arr['template'] = $body;
	$add_arr['subject'] = $subject;
        $add_arr['accountid'] = $id_arr;
	$add_arr['from'] = $send_id;
	$add_arr['temp'] = $add_array['temp'];
	$add_arr['to']=$to_send_mail;
	$add_arr['temp']=$add_array['temp'];
        $this->load->view('view_email_brod', $add_arr);
    }

/********************************************************/
    function email_history_list_cus() {
	$add_array = $this->input->post();
            $where = array('id' => $add_array['id']);
        $account = $this->db_model->getSelect("*", "mail_details", $where);
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
	    $this->load->module('accounts/accounts');
            redirect(base_url() . 'accounts/customer_edit/'.$value['accountid']);
    }
    function email_history_list() {
	$data['logintype']=$this->session->userdata('logintype');
        $data['username'] = $this->session->userdata('user_name');
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
	$data['page_title'] = 'Email History List';
	$data['grid_fields'] = $this->email_form->build_list_for_email();
	$data["grid_buttons"] = $this->email_form->build_grid_buttons_email();
        $data['form_search'] = $this->form->build_serach_form($this->email_form->get_email_history_search_form());
	$this->load->view('view_email_list', $data);
    }
    function email_history_list_json() {
	$data['logintype']=$this->session->userdata('logintype');
        $json_data = array();
        $count_all = $this->email_model->get_email_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->email_model->get_email_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
	$grid_fields = json_decode($this->email_form->build_list_for_email());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function customer_mail_record($accountid){
	$data['logintype']=$this->session->userdata('logintype');
        $json_data = array();
        $count_all = $this->email_model->customer_get_email_list(false,$accountid,"","");
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->email_model->customer_get_email_list(true,$accountid,$paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
	$grid_fields = json_decode($this->email_form->build_list_for_email_customer($accountid));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }    
    function email_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("email");
    }

    function email_send_multipal(){
	$add_array = $this->input->post();
//echo "<pre>";print_r($add_array);exit;
	if($add_array['email'] == '' || $add_array['subject'] == '' || $add_array['template'] == ''){
		$this->session->set_flashdata('astpp_notification', 'Email address not found!');
	        redirect(base_url() . '/email/email_client_area/');
	}
        $this->email_model->multipal_email($add_array);
	$screen_path = "/var/www/html/ITPLATP/cron";
	$screen_filename = "Email_Broadcast_".strtotime('now');
	$command = "cd ".$screen_path." && /usr/bin/screen -d -m -S  $screen_filename php cron.php BroadcastEmail";
        exec($command);
	$this->session->set_flashdata('astpp_errormsg', 'Email broad cast successfully!');
    	redirect(base_url() . 'email/email_history_list/');

    }
    function email_history_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('email_search_list', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'email/email_history_list/');
        }
    }

    function email_history_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('email_search', "");
    }
    /*
* Purpose : Add following code for download attached file
* Version 2.1
*/
    function email_history_list_attachment($file_name) {
   // echo 'da';
	if(file_exists(getcwd().'/attachments/'.$file_name)){
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$file_name);
		//header('Pragma: no-cache');
		ob_clean();
		flush();
		readfile(getcwd().'/attachments/'.$file_name);
	}
    }

}
?>
 
