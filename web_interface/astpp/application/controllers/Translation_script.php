<?php
// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
class Translation_script extends CI_Controller {
    public function index(){
    
        if (!file_exists(FCPATH. 'language')) {
            mkdir(FCPATH.'language', 0777, true);
        }
        $query = $this->db->get('translations');
        if ($query->num_rows () > 0) {
            $result    = $query->result_array();
            $this->db->select('locale');
            $languages = $this->db->get('languages');
            if ($languages->num_rows () > 0) {
                    $locale_data = $languages->result_array();

                        foreach ($locale_data as $locale_value) {

			    if(!is_dir(FCPATH.'language/'.$locale_value['locale'])){
	                        mkdir(FCPATH.'language/'.$locale_value['locale']);
                            	chmod(FCPATH.'language/'.$locale_value['locale'], 0777);
				mkdir(FCPATH.'language/'.$locale_value['locale'].'/LC_MESSAGES/');
                            	chmod(FCPATH.'language/'.$locale_value['locale'].'/LC_MESSAGES/', 0777);
			    }
                            

                            $myfile = fopen(FCPATH.'language/'.$locale_value['locale'].'/LC_MESSAGES/'.'messages'.'.po', "w");
			    if($myfile){
                                foreach ($result as $data_value) {
                                    unset($data_value['id']);
					$txt = "";
					    $msgid = $data_value['module_name'];
					    $msgstr = $data_value[$locale_value['locale']];
                                            $txt .= 'msgid'.' "'.$msgid.'"'. "\n";
                                            $txt .= 'msgstr'.' "'.$msgstr.'"'. "\n\n";
                                            fwrite($myfile, $txt);
                                }
				fclose($myfile);
	                        $command = 'cd '.FCPATH.'language/'.$locale_value['locale'].'/LC_MESSAGES/ && msgfmt '.'messages'. '.po -o ' .'messages'.'.mo';
                            	exec($command);
	                        
			    }else{
		                $this->session->set_flashdata ( 'astpp_errormsg', 'unable to write files.' );
			    }
                        }

                $this->session->set_flashdata ( 'astpp_errormsg', 'Language Translations sucessfull!' );
                redirect ( base_url () . 'systems/languages_list/' );
                die();
            } else {
                $this->session->set_flashdata ( 'astpp_notification', 'No data found!' );
                redirect ( base_url () . 'systems/languages_list/' );
                die();
            }
        } else {
            $this->session->set_flashdata ( 'astpp_notification', 'No data found!' );
            redirect ( base_url () . 'systems/languages_list/' );
            die();
        }
    }
}
