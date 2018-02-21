#!/usr/bin/perl
#
# ASTPP - Open Source VoIP Billing
#
# Copyright (C) 2004/2013 www.astpp.org
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# Terms of the GNU General Public License version 2.
############################################################

use CGI;
use CGI qw/:standard Vars/;
use ASTPP ':all';
use strict;
use warnings;

use vars qw($void_xml $cdr_db $params $ASTPP @output $config $freeswitch_db $astpp_db $verbosity );

require "/usr/local/astpp/astpp-common.pl";

$ASTPP     = ASTPP->new;

################# Programs start here #######################################
&initialize;
my ( $ipinfo, $xml, $maxlength, $maxmins, $callstatus,$astppdid,$didinfo );
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    $ASTPP->debug( debug => "$param $params->{$param}", verbosity => $verbosity);
}

if (defined $params->{section} && ( $params->{section} eq "configuration" || $params->{section} eq "dialplan"  || $params->{section} eq "directory")) {
      require "astpp-$params->{section}-xml.pl";
      &xml_process($params,$ASTPP,$config);
      
}
exit(0);
