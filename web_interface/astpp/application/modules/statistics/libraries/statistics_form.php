<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Statistics_form{
    
    function build_error_list_for_admin(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(array("uniqueID","120","uniqueid","","",""),
				    array("Date","100","calldate","","",""),
				    array("CalleID","100","clid","","",""),
				    array("Source","100","src","","",""),
				    array("Dest","80","dst","","",""),
				    array("Dest. Context","70","dcontext","","",""),
				    array("Channel","170","channel","","",""),
				    array("Dest. Channel","90","dstchannel","","",""),
				    array("Last App","80","lastapp","","",""),
				    array("Last Data","70","lastdata","","",""),
				    array("Duration","70","duration","","",""),
				    array("Bill Sec","70","billsec","","",""),
				    array("Disposition","70","disposition","","",""),
				    array("AMA Flags","70","amaflags","","",""),
				    array("Account Code","80","accountcode","","",""),
				    array("User Field","50","userfield","","",""),
				    array("Cost","50","cost","","","")
                ));
      return $grid_field_arr;
    }
    function get_trunk_stat_search_form() {
        $form['forms'] = array(base_url().'statistics/trunkstats/', array('id' => "trunk_stat_search"));
        $form['Search Provider Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'start_date', 'id' => 'provider_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('TO Date', 'INPUT', array('name' => 'end_date', 'id' => 'provider_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Trunk Name', 'trunkid', 'SELECT', '', '', 'tOOL TIP', '', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status <>"=>"2")),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "search_providerreport", 'content' => 'Search', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

}
?>
