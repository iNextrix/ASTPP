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
$logger->log ( "*************************** Configuration Starts ********************************" );

$xml = "";
if ($_REQUEST ['key_value'] == 'sofia.conf') {
	$xml = load_sofia ( $logger, $db, $config );
	header ( 'Content-Type: text/xml' );
	echo $xml;
} elseif ($_REQUEST ['key_value'] == 'acl.conf') {
	$xml = load_acl ( $logger, $db, $config );
	header ( 'Content-Type: text/xml' );
	echo $xml;
} else {
	xml_not_found ();
}
$logger->log ( "*************************** Configuration Ends **********************************" );
exit ();
?>
