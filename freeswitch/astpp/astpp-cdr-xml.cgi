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

use DBI;
use CGI;
use CGI qw/:standard Vars/;
use ASTPP ':all';
use XML::Simple;
use Data::Dumper;
use URI::Escape;
# use strict;
# use warnings;

use vars qw($cdr_db $params $ASTPP @output $config $freeswitch_db $astpp_db $verbosity );
use Locale::gettext_pp qw(:locale_h);
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
# print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");
@output    = ("STDERR");
$ASTPP     = ASTPP->new;

################# Programs start here #######################################
&initialize;
my ( $ipinfo, $xml, $maxlength, $maxmins, $callstatus,$astppdid,$didinfo, $var, );
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    $ASTPP->debug( debug => "$param $params->{$param}", verbosity => $verbosity);
}
$xml = header( -type => 'text/plain' );

if ( $params->{cdr} ) { # PROCESS CDRs.
      print header( -type => 'text/plain' );
	  
      # create object
      my $xml = new XML::Simple;

      # read XML file
      my $data = $xml->XMLin($params->{cdr});

      $ASTPP->debug( debug => "Call hangup and CDR Generating");      

      # print output
      print STDERR Dumper($data) if $config->{debug} == 1;

      #Clean cdr data. So, we can do further process easily.
      my $cdrdata = &clean_cdr_data($data);            
                  
      if($cdrdata->{callingcard})
      {
	  &process_callingcard_cdr( $astpp_db, $config, $cdrdata,$var);
      }else{
	  &processlist( $astpp_db, $config, $cdrdata,$var );  
      }
}
&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
exit(0);