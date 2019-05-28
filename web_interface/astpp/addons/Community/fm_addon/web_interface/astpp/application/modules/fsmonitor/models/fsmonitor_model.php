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
class Fsmonitor_model extends CI_Model {

   	function __construct() {
		parent::__construct ();
	}
    function reload_freeswitch($command,$id) {
        $response=array();
	if($id == 0){
           $query = $this->db_model->getSelect("*", "freeswich_servers", "");
           $fs_data = $query->result_array();
	}
	else{
	   $where = array('id'=>$id);
           $query = $this->db_model->getSelect("*", "freeswich_servers", $where);
           $fs_data = $query->result_array();
	}
        foreach ($fs_data as $fs_key => $fs_value) {
	      $fp = $this->freeswitch_lib->event_socket_create($fs_value["freeswitch_host"], $fs_value["freeswitch_port"],$fs_value["freeswitch_password"]);
	      if ($fp) {
	        $response[] = $this->freeswitch_lib->event_socket_request($fp, $command);
		$response=array_map('trim',$response);
		fclose($fp);
	      }
	}
	return $response;
    }
    function reload_freeswitch_gateway($command,$id) {
        $response='';
	if($id == 0){
          $query = $this->db_model->getSelect("*", "freeswich_servers", "");
          $fs_data = $query->result_array();
	}
	else{
	  $where = array('id'=>$id);
          $query = $this->db_model->getSelect("*", "freeswich_servers", $where);
          $fs_data = $query->result_array();
	}
        foreach ($fs_data as $fs_key => $fs_value) {
	      $fp = $this->freeswitch_lib->event_socket_create($fs_value["freeswitch_host"], $fs_value["freeswitch_port"],$fs_value["freeswitch_password"]);
	      if ($fp) {
	        $response.= $this->freeswitch_lib->event_socket_request($fp, $command);
		fclose($fp);
	      }
	}
        return $response;
    }
    function reload_live_freeswitch_show($command,$hostid) {
	$response='';
	$where=array('id'=>$hostid);
        $query = $this->db_model->getSelect("*", "freeswich_servers", $where);
        $fs_data = $query->result_array();
        foreach ($fs_data as $fs_key => $fs_value) {
	    $fp = $this->freeswitch_lib->event_socket_create($fs_value["freeswitch_host"], $fs_value["freeswitch_port"], $fs_value["freeswitch_password"]);
	    if ($fp) {
		$host= $fs_value["freeswitch_host"];
		$response .= $this->freeswitch_lib->event_socket_request($fp, $command);
//		$response = str_replace("0 total.","",$response);
		$response = $response;
		fclose($fp);
	    }
        } 
        return $response;
    }
}
