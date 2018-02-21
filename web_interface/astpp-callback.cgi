#!/usr/bin/perl  
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2006, DNW Consutling
#
# Darren Wiebe <darren@dnwconsulting.net>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
# This program can be called from the web browser.  It is designed to get your information and
# perform a callback based on that info.
#
#############################################################################

use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Getopt::Long;
use Asterisk::Manager;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{LANGUAGE}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");

my $astpp_db;

my $copyright = "ASTPP - Open Source Voip Billing &copy;2006 DNW Consulting";
my @output = ("STDERR");

sub request_callback() {
	my ($body);
	$body = start_form
      . "<table width=100/% align=\"left\" class=\"default\">"
      . "<tr class=\"header\"><td colspan=7>"
      . gettext("Calling Card - Callbacks")
      . "</td></tr><tr class=\"header\"><td>"
      . gettext("Callback Location (Where you are at)")
      . "</td><td>"
      . gettext("Number you wish to call - Can be left blank")
      . "</td><td>"
      . gettext("Calling Card Number")
      . "</td><td>"
      . gettext("PIN")
      . "</td></tr>" 
      . "<tr><td>"
      . textfield(
        -name => 'callbacknumber',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'destinationnumber',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'callingcard',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'pin',
        -size => 10
      )
      . "</td></tr>" 
      . "<tr><td colspan=4>"
      . submit( -name => "action", -value => gettext("Perform Callback") )  
      . "</td></tr>"
      . "</table>";
    return $body;
}

sub perform_callback() {
	my ($status,$sql);
	my $cardinfo = &get_callingcard( $astpp_db, $params->{callingcard}, $config );
	if ( $cardinfo->{status} != 1 ) {
			return gettext("Cardnumber or PIN is either invalid, empty, in use, or has expired!");
	}	
	if ($cardinfo->{pin} != $params->{pin}) {
			return gettext("Cardnumber or PIN is either invalid, empty, in use, or has expired!");
	}	
	$valid = &check_card_status($astpp_db,$cardinfo);
	if ($valid != 0) {
			return gettext("Cardnumber or PIN is either invalid, empty, in use, or has expired!");
	}
	# If we get this far then it means that the account is good to go.
	my $brandinfo = &get_cc_brand( $astpp_db, $cardinfo->{brand} );
	my $rateinfo =
	  &get_route( $astpp_db, $config, $params->{destinationnumber}, $brandinfo->{pricelist},
				  $cardinfo );
	%variables = ( CONNECTSURCHARGE => $rateinfo->{connectcost},
		PERMINUTESURCHARGE => $rateinfo->{cost},
		CARDNUMBER         => $params->{callingcard},
		DESTINATION        => $params->{destinationnumber},
		PIN                => $params->{pin});
	my $ActionID = &perform_callout(
					  $astpp_db, $config,
					  $params->{callbacknumber},
					  $config->{lcrcontext},
					  $config->{callout_accountcode},
					  $config->{maxretries},
					  $config->{waittime},
					  $config->{retrytime},
					  $config->{clidname},
					  $config->{clidnumber},
					  $config->{callingcards_callback_context},
					  $config->{callingcards_callback_extension},
					  %variables
	);
	return gettext("Callback Performed!");
	
}

########################### The program starts here ##################
my ($body,$status);
foreach $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n";
}
$config = &load_config;
$astpp_db = &connect_db($config,@output);
$config = &load_config_db($astpp_db, $config);

if ($params->{action} eq gettext("Perform Callback")) {
	$status = &perform_callback();
}

print header();
	$body = &request_callback();

if (!$astpp_db) {
	$status .= "<i>" . gettext("Database Unavailable!") . "</i>\n";
}	

print "<title>ASTPP - Callback Request</title>\n"
  . "<STYLE TYPE=\"text/css\">\n"
  . "<!--\n"
  . "  \@import url(/_astpp/style.css); \n" . "-->\n"
  . "</STYLE>\n"
  . "<BODY>\n"
  . "<table><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
  . "<td align=center><H2>ASTPP - Request Callback</H2></td>"
  . "</tr></table>"
  . "<table align=\"left\" width=100\%>"
  . "<tr><td><H2></H2></td></tr></table>\n"
  . $body
  . "</table><table align=\"left\" width=100\%>"
  . $status
  . "</table>" . "<hr>"
  . "<table align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr></table>"
  . "</BODY>";	


