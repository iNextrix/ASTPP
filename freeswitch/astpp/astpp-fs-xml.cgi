#!/usr/bin/perl
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

use CGI qw/:standard Vars/;
use strict;
use warnings;
use JSON;

use vars qw($params $ASTPP $gbl_config $gbl_astpp_db $gbl_xml_logger $gbl_xml_channels $astpp_calltype $sound $session $sound);

require "/usr/local/astpp/astpp-database.pl";
require "/usr/local/astpp/astpp-logger.pl";
require "/usr/local/astpp/astpp-xml.pl";
require "/usr/local/astpp/astpp-common.pl";

################# Programs start here #######################################
&initialize;

foreach my $param ( param() ) {
    $params->{$param} = param($param);
    &logger("$param $params->{$param}");
}

if (defined $params->{section} && ( $params->{section} eq "configuration" || $params->{section} eq "dialplan"  || $params->{section} eq "directory")) {
      require "astpp-$params->{section}-xml.pl";
      &xml_process();
      
}
exit(0);
