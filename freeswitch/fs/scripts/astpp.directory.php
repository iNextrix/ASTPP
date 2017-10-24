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
$logger->log ( "*************************** Directory Starts ********************************" );
$xml = "";
$logger->log ( $_REQUEST );
if (isset ( $_REQUEST ['Event-Name'] ) && $_REQUEST ['Event-Name'] == 'CUSTOM' && $_REQUEST ['VM-Action'] == 'change-password') {
	$logger->log ( "*************************** VM password change ********************************" );
	update_vm_data ( $logger, $db, $_REQUEST ['VM-User-Password'], $_REQUEST ['VM-User'] );
}

if (isset ( $_REQUEST ['user'] ) && isset ( $_REQUEST ['domain'] )) {
	$xml = load_directory ( $logger, $db );
	if ($xml == "")
		xml_not_found ();
	echo $xml;
} else {
	xml_not_found ();
}
$logger->log ( "*************************** Directory Ends **********************************" );
exit ();
?>
