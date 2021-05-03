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
class Fsmonitor extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library("fsmonitor_form");
        $this->load->library('freeswitch_lib');
        $this->load->library('astpp/form');
        $this->load->helper('xml');
        $this->load->model('fsmonitor_model');
        $db_config = Common_model::$global_config['system_config'];



        if (Common_model::$global_config['system_config'] ['opensips'] == 0) {
        $opensipdsn = "mysqli://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        	
        }


        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
           }
    function sip_devices_authentication(){
	$data['username'] = $this->session->userdata('user_name');
	$data['page_title'] = "Authentication";	
	$data['type'] = "Sip";
	$query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $data['fs_data'] = $query->result_array();


        // echo "string";die();
	$this->load->view('view_authentication_key',$data);
    }
    function opensips_devices(){
	$data['username'] = $this->session->userdata('user_name');
	$data['page_title'] = "Opensip Devices";
	$query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $data['fs_data'] = $query->result_array();
	$this->load->view('view_opensips_extension_report',$data);
    }
    function opensips_devices_json($id=''){
        $data['page_title'] = 'Edit Opensips ';
         $this->opensips_db->select("*");  
        $location = $this->opensips_db->get("location");
        $response=$location->result_array();
        $json_data = array();
	$json_data['total']='';
	if($response != ''){
	foreach($response as $response_value){
	  $json_data['page'] = 1;

         	   $json_data['rows'][] = array('cell'=>array(
             $response_value["username"],
             $response_value["domain"],
             $response_value["contact"],
             $response_value["socket"],
             $response_value["expires"],
                            ));

       }
      }
       if(isset($json_data['rows'])){
            $count = count ($json_data['rows']);
            $json_data['total'] = $count;
       }
	else{
	   $count = 0;
            $json_data['row'] = '';
	}
       echo json_encode($json_data);
    }








 function sip_devices_json($id=0){
	$account_info = $accountinfo = $this->session->userdata('accountinfo');
        $command = "api sofia xmlstatus profile default reg";  
        $response = $this->fsmonitor_model->reload_freeswitch($command,$id);
        $json_data = array();
	$json_data['total']='';
	if($response != ''){
	foreach($response as $response1){
          $response_arr = json_decode(json_encode((array) simplexml_load_string(trim($response1))),1);
	  $json_data['page'] = 1;
	  if(array_key_exists("registration",$response_arr["registrations"])){
            if(!array_key_exists("0",$response_arr["registrations"])){
              $response_final_arr = $response_arr["registrations"];
            	if(array_key_exists("0",$response_final_arr["registration"])){
                    foreach($response_final_arr as $device_val){
                   	foreach($device_val as $response_value) {
				if ($account_info ['id'] == '1'){
					$json_data['rows'][] = array('cell'=>array(
						$response_value["sip-auth-user"],
						htmlentities($response_value['contact']),
						$response_value["network-ip"],
						$response_value["network-port"],
						$response_value["status"],
						$response_value["agent"],
					));
				} else {
					$reseller_id=$this->common->get_field_name('reseller_id', 'sip_devices',array('username'=>@$response_final_arr['registration']["sip-auth-user"]));	
					$res_id= ($reseller_id==0)?1:$reseller_id;
					if($res_id == $account_info['id']){
						$json_data['rows'][] = array('cell'=>array(
							$response_value["sip-auth-user"],
							htmlentities($response_value['contact']),
							$response_value["network-ip"],
							$response_value["network-port"],
							$response_value["status"],
							$response_value["agent"],
						));
					}
				}
            		}
                    }
		}
		else{
	   		   $reseller_id=$this->common->get_field_name('reseller_id', 'sip_devices',array('username'=>$response_value["sip-auth-user"]));	
			   $res_id= ($reseller_id==0)?1:$reseller_id;
			   if($res_id == $account_info['id']){		   	   
		  foreach($response_final_arr as $response_value){
		      $json_data['rows'][] = array('cell'=>array(
			   $response_value["sip-auth-user"],
			   htmlentities($response_value['contact']),
			   $response_value["network-ip"],
			   $response_value["network-port"],
			   $response_value["status"],
		  	   $response_value["agent"],
		      ));
		 }
		}
	      }
	   }
	 }
       }
      }
       if(isset($json_data['rows'])){
            $count = count ($json_data['rows']);
            $json_data['total'] = $count;
       }
	else{
	   $count = 0;
            $json_data['row'] = '';
	}
       echo json_encode($json_data);
    }











    function sip_devices() {

    	// echo "string";die();
		$data['username']   = $this->session->userdata('user_name');
		$data['page_title'] = "Sip Devices";
		$query              = $this->db_model->getSelect("*", "freeswich_servers", "");
	    $data['fs_data']    = $query->result_array();
		$this->load->view('registered_extension_report',$data);
    }
/*    function sip_devices_json($id=''){
        $command = "api sofia xmlstatus profile default reg";  
        $response = $this->fsmonitor_model->reload_freeswitch($command,$id);
        $json_data = array();
	$json_data['total']='';
	if($response != ''){
	foreach($response as $response1){
          $response_arr = json_decode(json_encode((array) simplexml_load_string(trim($response1))),1);
	  $json_data['page'] = 1;
	  if(array_key_exists("registration",$response_arr["registrations"])){
            if(!array_key_exists("0",$response_arr["registrations"])){
              $response_final_arr = $response_arr["registrations"];
            	if(array_key_exists("0",$response_final_arr["registration"])){

            		// echo "<pre>";
            		// print_r($response_final_arr);die();

                    foreach($response_final_arr as $device_val){
                   	foreach($device_val as $response_value){

                      	   $json_data['rows'][] = array('cell'=>array(
                                 $response_value["sip-auth-user"],
                                 $response_value["network-ip"],
                                 $response_value["network-port"],
                                 $response_value["status"],
                                 $response_value["agent"],
                            ));
            		}
                     }
		}
		else{
		  foreach($response_final_arr as $response_value){
		      $json_data['rows'][] = array('cell'=>array(
			   $response_value["sip-auth-user"],
			   $response_value["network-ip"],
			   $response_value["network-port"],
			   $response_value["status"],
		  	   $response_value["agent"],
		      ));
		 }
	      }
	   }
	 }
       }
      }
       if(isset($json_data['rows'])){
            $count = count ($json_data['rows']);
            $json_data['total'] = $count;
       }
	else{
	   $count = 0;
            $json_data['row'] = '';
	}
       echo json_encode($json_data);
    }*/
    function fs_cli_authentication(){
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Authentication';
	$data['type'] = "fs_cli";
	$this->load->view('view_authentication_key',$data);
   }
    function fs_cli() {
		$data['username']   = $this->session->userdata('user_name');
		$data['page_title'] = 'Freeswitch CLI';
		$data['response']   = '';
		$data['command']    = '';
		$query              = $this->db_model->getSelect("*", "freeswich_servers", "");
	    $data['fs_data']    = $query->result_array();
	    $this->load->view('view_fs_fsmonitor_execute', $data);
    }
    function fs_cli_command(){
        $freeswitch_data = $this->input->post();
	$command ='api '.$freeswitch_data['freeswitch_command'];
	$host_id =$freeswitch_data['host_id'];
	$data['host_id']=$host_id;
        $new_arr = $this->fsmonitor_model->reload_live_freeswitch_show($command,$host_id);
	$data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Freeswitch Cli';
	$data['response']=$new_arr;
	$query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $data['fs_data'] = $query->result_array();
	$data['command']=$command;
        $this->load->view('view_fs_fsmonitor_execute', $data);
    }

    function gateways_authentication(){
	$data['username'] = $this->session->userdata('user_name');
	$data['page_title'] ="Authentication";	
	$data['type'] = "gateways";
	$this->load->view('view_authentication_key',$data);
   }
    function gateways(){
	$data['username'] = $this->session->userdata('user_name');
	$data['page_title'] ="Gateways";	
	$query = $this->db_model->getSelect("*", "freeswich_servers", "");
	$data['fs_data'] = $query->result_array();
	$this->load->view('registered_gateway_report',$data);
    }
    function gateways_json($id=''){
        $command = "api sofia xmlstatus gateway"; 
        $response = $this->fsmonitor_model->reload_freeswitch($command,$id);
	$json_data['total']='';
	if($response != ''){
	foreach($response as $response1){
	   $json_data['page'] = 1;
           $response_arr = json_decode(json_encode((array) simplexml_load_string(trim($response1))),1);
           if(count($response_arr)>0){
              $response_final_arr = $response_arr["gateway"];
              if(array_key_exists("0",$response_arr["gateway"])){
                 foreach($response_final_arr as $response_value){
		    $arr = $response_value;
		    $final_arr = json_encode($arr);
		    $data = urlencode($final_arr);
		    if($response_value['state'] == 'FAIL_WAIT'){
		        $url=base_url();
		        $btn = '<span class="label_red label-sm_red label-inverse_red arrowed-in_red" style="padding-top:4px;">FAIL_WAIT</span>';
		    }
		    elseif($response_value['state'] == 'REGED'){
		        $url=base_url();
		        $btn = '<span class="label label-sm label-inverse arrowed-in" style="padding-top:4px;">REGED</span>';
		    }
		    else{ 
			$url=base_url();
		 	$btn = '<span class="label label-sm label-inverse arrowed-in"  style="padding-top:4px;">NOREG</span> ';
		   } 
		   $edit_action ='<a href="../gateways_popup/'.$response_value["name"].'/'.$id.'" class="btn btn-royelblue btn-sm"  rel="facebox_medium" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>';
	 	   $action=   $edit_action;
                   $json_data['rows'][] = array('cell'=>array(
					'<a href="../gateways_popup/'.$response_value["name"].'/'.$id.'" rel="facebox_medium"><b style="color:blue;"> '. $response_value["name"].'</b></a>',
                                        $response_value["proxy"],
                                        $response_value["username"],
                                        $response_value["calls-in"],
                                        $response_value["calls-out"],
                                        $response_value["failed-calls-in"],
                                        $response_value["failed-calls-out"],
                                        $response_value["status"],
					$btn,
					$action,
                                    ));
                 }
            }
	    else{
		$arr = $response_final_arr;
		$final_arr = json_encode($arr);
		$data = urlencode($final_arr);
		if($response_final_arr['state'] == 'FAIL_WAIT'){
		    $url=base_url();
		    $btn = '<span class="label_red label-sm_red label-inverse_red arrowed-in_red" style="padding-top:4px;">FAIL_WAIT</span>';
		 }
		 elseif($response_final_arr['state'] == 'REGED'){ 
		     $url=base_url();
		     $btn = '<span class="label label-sm label-inverse arrowed-in"  style="padding-top:4px;">REGED</span> ';
		} 
		 else{ 
		     $url=base_url();
		     $btn = '<span class="label label-sm label-inverse arrowed-in"  style="padding-top:4px;">NOREG</span> ';
		} 
		$edit_action ='<a href="../gateways_popup/'.$response_final_arr["name"].'/'.$id.'" class="btn btn-royelblue btn-sm"  rel="facebox_medium" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>';
		$action= $edit_action;
                $json_data['rows'][] = array('cell'=>array(
                                    	'<a href="../gateways_popup/'.$response_final_arr["name"].'/'.$id.'" rel="facebox_medium"> <b style="color:blue;">'.$response_final_arr["name"].'</b></a>',
                                     $response_final_arr["proxy"],
                                     $response_final_arr["username"],
                                     $response_final_arr["calls-in"],
                                     $response_final_arr["calls-out"],
                                     $response_final_arr["failed-calls-in"],
                                     $response_final_arr["failed-calls-out"],
                                     $response_final_arr["status"],
				     $btn,
				     $action,
                                 ));
            }
        }
	}
       }
       if(isset($json_data['rows'])){
            $count = count ($json_data['rows']);
            $json_data['total'] = $count;
       }
	else{
	   $count = 0;
            $json_data['row'] = '';
	}
        echo json_encode($json_data);
    }
     function gateways_popup($gateway_name,$id){
        $command = "api sofia xmlstatus gateway"; 
        $response = $this->fsmonitor_model->reload_freeswitch($command,$id);
	if($response == ''){
	    $data_result = '';
	}
	else{
	    $custom_array=array();
 	    $new_response_array=array();
	    error_reporting(0);
	    @ini_set('display_errors', 0);

	    foreach($response as $key=>$response1){
	       if($response1 !='' && simplexml_load_string(trim($response1)) !=''){	     
	        if(!json_decode(json_encode((array) simplexml_load_string(trim($response1))),1) == ''){
        	  $response_arr = json_decode(json_encode((array) simplexml_load_string(trim($response1))),1);
		  $new_response_array[]=$response_arr;
	        }
		else{
		   $new_response_array ='';
	        }
	       }	
	      else{
		$new_response_array ='';
	      }
            }
	    if(!empty($new_response_array)){
	       $i=0;
	       foreach($new_response_array as $response_key=>$response_arr){
                  if(isset($response_arr['gateway'][$i])&& is_array($response_arr['gateway'][$i])){
		      foreach($response_arr['gateway'] as $gateway_key=>$gateway_value){
		        $custom_array[$i]=$gateway_value;
	   		$i++;
		      }
	          }
	          else{
			$custom_array[$i]=$response_arr['gateway'];
			 $i++;
	          }
	       }
	       foreach($custom_array as $key=>$cust_name){

			if($gateway_name == $custom_array[$key]['name'])
			{
			   $data_name = $gateway_name;
			   $data_res[$gateway_name] =$cust_name;
				break;
			}
	       }
	       $data_result =$data_res[$gateway_name];
	    }
	    else{
		$data_result = '';
	    }
	}
	if($data_result == ''){
		$data['page_title'] = 'Gatway';
		$data['fail'] = 1;
	}
	else{
		$data =array();
		$data['page_title'] = 'Gatway';
		$data['fail'] = 0;
		$data['gname'] = $data_result['name'];
		$data['proxy']= $data_result['proxy'];
		$data['realm']= $data_result['realm'];
		$data['expires']= $data_result['expires'];
		$data['username'] = $data_result['username'];
		$data['frequency']= $data_result['freq'];
		$data['context']= $data_result['context'];
		$data['ping']= $data_result['ping'];
		$data['status']= $data_result['status'];
		$data['ping_frequency'] = $data_result['pingfreq'];
		$data['profile']= $data_result['profile'];
		$data['call_in']= $data_result['calls-in'];
		$data['password']= $data_result['password'];
		$data['call_out']= $data_result['calls-out'];
		$data['from'] = $data_result['from'];
		$data['fail_call_in']= $data_result['failed-calls-in'];
		$data['contact']= $data_result['contact'];
		$data['fail_call_out']= $data_result['failed-calls-out'];
		$data['to']= $data_result['to'];
		$data['state']= $data_result['state'];

	}
		$this->load->view('view_gateway_popup', $data);
    }
    function live_call_authentication(){
	$data['username'] = $this->session->userdata('user_name');
	$data['page_title'] ="Authentication";	
	$data['type'] = "live_call";
	$this->load->view('view_authentication_key',$data);
   }
    function live_call_graph() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Live Calls';
	$query = $this->db_model->getSelect("*", "freeswich_servers", "");
        $data['fs_data'] = $query->result_array();
        $data['country']=$this->db_model->getSelect("country", "countrycode",'');
        $this->load->view('view_fs_livecall_graph', $data);
    } 
    function livecall_data($id='0'){

        $command = "api show calls";
	$response='';
	$calls = array(0=>0);
	$servers=$this->db->get('freeswich_servers');
		if($id == 0){
         	   $servers = $this->db_model->getSelect("*", "freeswich_servers", "");
	           $servers_data=$servers->result_array();
	}
	else{
	     $where =array('id'=>$id);
	     $servers = $this->db_model->getSelect("*", "freeswich_servers", $where);
	     $servers_data=$servers->result_array();
	}
	    $servers_data=$servers->result_array();
	    foreach($servers_data as $servers){
	     $fp = $this->freeswitch_lib->event_socket_create($servers['freeswitch_host'],$servers['freeswitch_port'],$servers['freeswitch_password']);
	     if ($fp) {
		$response .= $this->freeswitch_lib->event_socket_request($fp, $command);
		fclose($fp);
	     }
	     $data = explode("\n",$response);
	     $cnt = count($data);
	     if(isset($data[$cnt-2]) && $data[$cnt-2] >= 0){
		$tmp = explode(" ",$data[$cnt-2]);
		if($tmp[1] == 'total.'){
			$calls[0] = $calls[0]+(int) $tmp[0];
		}
		else{
			$calls[0] = $calls[0]+(int) $cnt - 4;
		}
	     }
	    }
      echo json_encode($calls);
    }
    function live_call_key(){
        $data['page_title'] = 'Authentication';
        $this->load->view('check',$data);
    }
    function sip_devices_file_exits(){
        $this->load->view('file_exist','');
   }
 }

