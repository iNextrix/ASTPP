<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Timezone {
  function __construct(){
	$this->CI =& get_instance();
	$this->CI->load->library('session');
	$this->CI->load->database();
  }

 function uset_timezone(){
	  $logintype = $this->CI->session->userdata('userlevel_logintype');
          
	  if($logintype == "-1"){
	    return Common_model::$global_config['system_config']['default_timezone'];
	  }else{
            $account_data = $this->CI->session->userdata("accountinfo");
	    $QUERY = $this->CI->db->query("SELECT timezone_id FROM accounts where id =".$account_data['id']);
	    $result = $QUERY->result();
	    $account_data['timezone_id'] = $result[0]->timezone_id;
	    $this->CI->session->set_userdata("accountinfo",$account_data);
	    return $result[0]->timezone_id;
	  }
	  
  }
  function display_GMT($currDate,$fulldate = 1)
  {	
      $number = $this->uset_timezone();
      $SERVER_GMT='0';

      $result=$this->CI->db->query("select gmtoffset from timezone where id =".$number);
      $timezone_offset = $result->result();

      $USER_GMT = $timezone_offset['0']->gmtoffset;

      $date_time_array = getdate(strtotime($currDate));
      $hours = $date_time_array['hours'];
      $minutes = $date_time_array['minutes'];
      $seconds = $date_time_array['seconds'];
      $month = $date_time_array['mon'];
      $day = $date_time_array['mday'];
      $year = $date_time_array['year'];
      $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
      
      $timestamp = $timestamp-($USER_GMT-$SERVER_GMT);
      if ($fulldate == 1) {
	      //$date = date("Y-m-d H:i:s", $timestamp);
		$date = date("Y-m-d H:i:s", $timestamp);
      } else {
	      $date = date("Y-m-d", $timestamp);
      }
      return $date;
  }

  function convert_to_GMT($currDate,$fulldate = 1){

      $number = $this->uset_timezone();
      $SERVER_GMT='0';

      $result=$this->CI->db->query("select gmtoffset from timezone where id =".$number);
      $timezone_offset = $result->result();

      $USER_GMT = $timezone_offset['0']->gmtoffset;

      $date_time_array = getdate(strtotime($currDate));
      $hours = $date_time_array['hours'];
      $minutes = $date_time_array['minutes'];
      $seconds = $date_time_array['seconds'];
      $month = $date_time_array['mon'];
      $day = $date_time_array['mday'];
      $year = $date_time_array['year'];
      $timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);

      $timestamp = $timestamp + ($SERVER_GMT - $USER_GMT);
      if ($fulldate == 1) {
	      $date = date("Y-m-d H:i:s", $timestamp);
      } else {
	      $date = date("Y-m-d", $timestamp);
      }
      return $date;
  }
}
?>
