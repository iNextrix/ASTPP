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
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Dynamically build forms for display
 */
class Locale {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
		$this->CI->load->model ( 'db_model' );
		$this->CI->load->library ( 'email' );
		$this->CI->load->library ( 'session' );
		$this->CI->load->driver ( 'cache' );
		$this->set_lang ();
	}
	function set_lang($lang = FALSE) {
		$current_locale = $this->CI->session->userdata ( 'user_language' );		
		if (empty ( $current_locale )) {
			//$current_locale = 'en_US';
			$current_locale = DEFAULT_LANGUAGE;
		}
		putenv ( "LANG=$current_locale" );
		//setlocale ( LC_ALL, $current_locale . ".UTF-8" );
		setlocale ( LC_MESSAGES, $current_locale );
		setlocale ( LC_TIME, $current_locale );
		setlocale ( LC_CTYPE, $current_locale );
		$domain = 'messages';
		$uri_segment = '';
		$uri_segment = $this->CI->uri->segments;
		if (isset ( $uri_segment [1] )) {
			$filename = getcwd () . '/application/modules/user/language/' . $lang . '/LC_MESSAGES/messages.mo';
			bindtextdomain ( WEBSITE_DOMAIN, getcwd () . '/application/modules/' . $uri_segment [1] . '/language/' );
		}
		bind_textdomain_codeset ( WEBSITE_DOMAIN, 'UTF-8' );
		textdomain ( WEBSITE_DOMAIN );
		return true;
	}
}

