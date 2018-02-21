#!/usr/bin/perl -w
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd. - <astpp@inextrix.com>"
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

use POSIX;
use Time::HiRes qw( gettimeofday tv_interval );
our $session;

use vars qw($params $ASTPP $gbl_config $gbl_astpp_db $gbl_xml_logger $gbl_xml_channels $astpp_calltype $session $sound);
                                                                             
require "/usr/local/astpp/astpp-database.pl";
require "/usr/local/astpp/astpp-logger.pl";
require "/usr/local/astpp/astpp-xml.pl";
require "/usr/local/astpp/astpp-common.pl";
require "/usr/local/astpp/astpp-callingcard-functions.pl";

#Define default variables 
$astpp_calltype = "ASTPP-CALLINGCARD";
my $sound_default_location = "/usr/local/freeswitch/sounds/en/us/callie/";


&initialize();

#Define sound files 
$sound = &define_sounds($sound_default_location);

return 1 if ( !$session->ready() );

#Welcome file playback
$session->streamFile($sound_default_location.$gbl_config->{calling_cards_welcome_file}) if($gbl_config->{calling_cards_welcome_file} ne "");

#Authenticate customer
my $cardinfo = &auth_callingcard();

#Play balance
&say_balance($cardinfo);

#Process for dialing destination number
&process_destination($cardinfo);

#IVR playback 
&playback_ivr($cardinfo);

$session->hangup();
1;
