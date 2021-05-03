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

// Define user rates array for parsing
$CONST_USER_RATE = array (
		'RID' => 'id',
		'RCD' => 'code',
		'RDS' => 'destination',
		'RCC' => 'connectioncost',
		'RFS' => 'freeseconds',
		'RCO' => 'cost',
		'RIB' => 'initial_billing_block',
		'RBB' => 'billing_block',
		'UID' => 'user_id',
		'RTI' => 'ratecard_id'
);

// Define carrier rates array for parsing
$CONST_CARRIER_RATE = array (
		'CID' => 'id',
		'CCD' => 'code',
		'CDS' => 'destination',
		'CCC' => 'connectioncost',
		'CFS' => 'freeseconds',
		'CCO' => 'cost',
		'CIB' => 'initial_billing_block',
		'CBB' => 'billing_block' 
);

define ( "tbl_configuration", "system" );
?>
