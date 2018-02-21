#!/usr/bin/perl  
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004 - 2006, Aleph Communications
#
# Darren Wiebe <darren@aleph-com.net>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
# This program can be called from the web browser.  It is designed to get your information and
# empty one calling card into another.
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

my $copyright = "ASTPP - Open Source Voip Billing &copy;2004-2006 Aleph Communications";
my @output = ("STDERR");

sub build_body() {
	my ($body);
    $body = start_form
      . "<table width=100/% align=\"left\" class=\"default\">"
      . "<tr class=\"header\"><td colspan=7>"
      . gettext("Calling Card - Transfer Funds")
      . "</td></tr><tr class=\"header\"><td>"
      . gettext("Source Card (Calling Card to Empty)")
      . "</td><td>"
      . gettext("Source Card Pin")
      . "</td><td>"
      . gettext("Destination Card (Calling Card to Refill)")
      . "</td><td>"
      . gettext("Destination Card Pin")
      . "</td></tr>" 
      . "<tr><td>"
      . textfield(
        -name => 'sourcecard',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'sourcecardpin',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'destcard',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'destcardpin',
        -size => 10
      )
      . "</td></tr>" 
      . "<tr><td colspan=4>"
      . submit( -name => "action", -value => gettext("Transfer Funds") )  
      . "</td></tr>"
      . "</table>";
    return $body;
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

if ($params->{action} eq gettext("Transfer Funds")) {
	$status = &transfer_funds($astpp_db,$config,$params->{sourcecard}, $params->{sourcecardpin}, $params->{destcard}, $params->{destcardpin});
	if ($status == 0) {
		$status = gettext("Funds Successfully Transfered");
	} else {
		$status = gettext("Please ensure that the card numbers and pins are correct.  Transfer NOT Successfuly.");
	}
}

print header();
$body = &build_body();

if (!$astpp_db) {
	$status .= "<i>" . gettext("Database Unavailable!") . "</i>\n";
}	

print "<title>ASTPP - Transfer Funds</title>\n"
  . "<STYLE TYPE=\"text/css\">\n"
  . "<!--\n"
  . "  \@import url(/_astpp/style.css); \n" . "-->\n"
  . "</STYLE>\n"
  . "<BODY>\n"
  . "<table><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
  . "<td align=center><H2>ASTPP - Transfer Funds</H2></td>"
  . "</tr></table>"
  . "<table align=\"left\" width=100\%>"
  . "<tr><td><H2></H2></td></tr></table>\n"
  . $body
  . "</table><table align=\"left\" width=100\%>"
  . $status
  . "</table>" . "<hr>"
  . "<table align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr></table>"
  . "</BODY>";	


