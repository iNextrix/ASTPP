#!/usr/bin/perl  
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# Darren Wiebe <darren@aleph-com.net>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
######################################################################################################
use POSIX;
use POSIX qw(strftime);
use CGI;
use CGI qw/:standard Vars/;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
my @output    = ("STDERR");
my $copyright =
  "ASTPP - Open Source Voip Billing &copy;2004 Aleph Communications";
my @language = all_language_codes;    #("EN");
@language = sort @language;
my @currency   = ("CAD");
my @devicetype = ("SIP");
my @countries  = all_country_names();
@countries = sort @countries;
my %yesno = (
    '0' => gettext("NO"),
    '1' => gettext("YES")
);


sub build_configure() {
	my ( $body, $status, %newconfig );
	$body = start_form;
	if ( $params->{action} eq gettext("Save...") ) {
		%newconfig = (
			'dbuser'           => $params->{dbuser},
			'dbpass'           => $params->{dbpass},
			'dbname'           => $params->{dbname},
			'dbhost'           => $params->{dbhost},
			'cdr_dbuser'       => $params->{cdr_dbuser},
			'cdr_dbpass'       => $params->{cdr_dbpass},
			'cdr_dbname'       => $params->{cdr_dbname},
			'cdr_dbhost'       => $params->{cdr_dbhost},
			'amp_dbuser'       => $params->{amp_dbuser},
			'amp_dbpass'       => $params->{amp_dbpass},
			'amp_dbname'       => $params->{amp_dbname},
			'amp_dbhost'       => $params->{amp_dbhost},
			'cardlength'       => $params->{cardlength},
			'startingdigit'    => $params->{startingdigit},
			'email'            => $params->{email},
			'emailadd'         => $params->{emailadd},
			'default_brand'    => $params->{default_brand},
			'currency_name'    => $params->{currency_name},
			'company_website'  => $params->{company_website},
			'company_email'    => $params->{company_email},
			'asterisk_server'  => $params->{asterisk_server},
			'astpp_dir'        => $params->{astpp_dir},
			'new_user_brand'   => $params->{new_user_brand},
			'user_email'       => $params->{user_email},
			'debug'            => $params->{debug},
			'asterisk_dir'     => $params->{asterisk_dir},
			'default_context'  => $params->{default_context},
			'reg_seconds'      => $params->{reg_seconds},
			'sip_port'         => $params->{sip_port},
			'iax_port'         => $params->{iax_port},
			'ipaddr'           => $params->{ipaddr},
			'codecs'           => $params->{codecs},
			'type'             => $params->{type},
			'key'              => $params->{key},
			'remote_incoming'  => $params->{remote_incoming},
			'key_home'         => $params->{key_home},
			'astman_user'      => $params->{astman_user},
			'astman_secret'    => $params->{astman_secret},
			'astman_hosts'     => $params->{astman_hosts},
			'results_per_page' => $params->{results_per_page},
			'enablelcr'        => $params->{enablelcr},
			'language'         => $params->{language}
		);
		if ( &save_config(%newconfig) ) {
			$status .= gettext("Configuration Saved...");
		}
		else {
			$status .= gettext("Configuration Not Saved...");
		}
	}
	if ( $params->{action} eq gettext("Create Database...") ) {
		$worked = &create_db( $config, $enh_config, @output );
		if ( $worked == 0 ) {
			$status .= gettext("Database Created");
			$status .= " - ";
			$astpp_db = &connect_db( $config, $enh_config, @output );
			$worked = &populate_db($astpp_db);
			if ( $worked == 0 ) {
				$status .= gettext("Database Populated");
			}
			else {
				$status .= gettext("Database Failed to Populate");
			}
		}
		else {
			$status .= gettext("Database Failed to Create");
		}
	}
	$body = start_form
	  . "<table style=\"default\" border=\"1\" cellpadding=\"2\" cellspacing=\"2\"><tr><td colspan=4>"
	  . hidden( -name => 'mode', -default => gettext("Configure") )
	  . $status
	  . "</td></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("ASTPP Database Configuration")
	  . "</td></tr><tr><td>"
	  . gettext("Host Name/IP")
	  . "</td><td>"
	  . textfield(
		-name    => 'dbhost',
		-default => $config->{dbhost}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("User Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'dbuser',
		-default => $config->{dbuser}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Password")
	  . "</td><td>"
	  . textfield(
		-name    => 'dbpass',
		-default => $config->{dbpass}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Database Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'dbname',
		-default => $config->{dbname}
	  )
	  . "</td></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("Asterisk(tm) CDR Database Configuration")
	  . "</td></tr><tr><td>"
	  . gettext("Host Name/IP")
	  . "</td><td>"
	  . textfield(
		-name    => 'cdr_dbhost',
		-default => $config->{cdr_dbhost}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("User Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'cdr_dbuser',
		-default => $config->{cdr_dbuser}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Password")
	  . "</td><td>"
	  . textfield(
		-name    => 'cdr_dbpass',
		-default => $config->{cdr_dbpass}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Database Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'cdr_dbname',
		-default => $config->{cdr_dbname}
	  )
	  . "</td></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("AMP(tm) Database Configuration - Optional")
	  . "</td></tr><tr><td>"
	  . gettext("Host Name/IP")
	  . "</td><td>"
	  . textfield(
		-name    => 'amp_dbhost',
		-default => $config->{amp_dbhost}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("User Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'amp_dbuser',
		-default => $config->{amp_dbuser}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Password")
	  . "</td><td>"
	  . textfield(
		-name    => 'amp_dbpass',
		-default => $config->{amp_dbpass}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Database Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'amp_dbname',
		-default => $config->{amp_dbname}
	  )
	  . "</td></tr><tr></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("General Options")
	  . "</td></tr><tr><td>"
	  . gettext("Card Number Length (4-20)")
	  . "</td><td>"
	  . textfield(
		-name    => 'cardlength',
		-default => $config->{cardlength}
	  )
	  . "</td></tr><tr><td colspan=2>"
	  . gettext("Card Starting Number Enter 0 if not required")
	  . "</td><td>"
	  . "</td><td>"
	  . "</tr><tr><td></td><td>"
	  . textfield(
		-name    => 'cardlength',
		-default => $config->{cardlength}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Email New Account Info to admin?")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'email',
		-values  => \%yesno,
		-default => $config->{email}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Admin Email Address")
	  . "</td><td>"
	  . textfield(
		-name    => 'emailadd',
		-default => $config->{emailadd}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Currency")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'currency_name',
		-values  => \@currency,
		-default => $config->{currency_name}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Language")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'language',
		-values  => \@language,
		-default => $config->{language}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Company Name (No Spaces)")
	  . "</td><td>"
	  . textfield(
		-name    => 'company_name',
		-default => $config->{company_name}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Company Website")
	  . "</td><td>"
	  . textfield(
		-name    => 'company_website',
		-default => $config->{company_website}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Customer Service Email Address")
	  . "</td><td>"
	  . textfield(
		-name    => 'company_email',
		-default => $config->{company_email}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Asterisk Server (DNS/IP)")
	  . "</td><td>"
	  . textfield(
		-name    => 'asterisk_server',
		-default => $config->{asterisk_server}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("New User Default Pricelist")
	  . "</td><td>"
	  . textfield(
		-name    => 'new_user_brand',
		-default => $config->{new_user_brand}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Email Users on action?")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'user_email',
		-values  => \%yesno,
		-default => $config->{user_email}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Debug Mode")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'debug',
		-values  => \%yesno,
		-default => $config->{debug}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("# of Results to show per Page")
	  . "</td><td>"
	  . textfield(
		-name    => 'results_per_page',
		-default => $config->{results_per_page}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Enable LCR (Least Cost Routing)?")
	  . "</td><td>"
	  . popup_menu(
		-name    => 'enablelcr',
		-values  => \%yesno,
		-default => $config->{enablelcr}
	  )
	  . "</td></tr><tr></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("Rating Engine Configuration")
	  . "</td></tr><tr><td>"
	  . "</td></tr><tr><td colspan=2>"
	  . gettext(
		"The Default Pricelist is used during billing.  If it cannot find")
	  . "</td></tr><tr><td colspan=2>"
	  . gettext(
		"a cost under the cards brand it looks under the default brand.")
	  . "</td></tr><tr><td>"
	  . gettext("Default Pricelist:")
	  . "</td><td>"
	  . textfield(
		-name    => 'default_brand',
		-default => $config->{default_brand}
	  )
	  . "</td></tr><tr></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("Asterisk(tm) Related Configuration")
	  . "</td></tr><tr><td>"
	  . gettext("Asterisk Configuration Directory")
	  . "</td><td>"
	  . textfield(
		-name    => 'asterisk_dir',
		-default => $config->{asterisk_dir}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("New User Context")
	  . "</td><td>"
	  . textfield(
		-name    => 'default_context',
		-default => $config->{default_context}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Default Reg Seconds")
	  . "</td><td>"
	  . textfield(
		-name    => 'reg_seconds',
		-default => $config->{reg_seconds}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Sip Port")
	  . "</td><td>"
	  . textfield(
		-name    => 'sip_port',
		-default => $config->{sip_port}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("IAX Port")
	  . "</td><td>"
	  . textfield(
		-name    => 'iax_port',
		-default => $config->{iax_port}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Client IP Address (dynamic usually)")
	  . "</td><td>"
	  . textfield(
		-name    => 'ipaddr',
		-default => $config->{ipaddr}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Codecs usually (all)")
	  . "</td><td>"
	  . textfield(
		-name    => 'codecs',
		-default => $config->{codecs}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Default Client Type (friend/user/peer)")
	  . "</td><td>"
	  . textfield(
		-name    => 'type',
		-default => $config->{type}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Outoing Key Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'key',
		-default => $config->{key}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Remote Incoming User Name")
	  . "</td><td>"
	  . textfield(
		-name    => 'remote_incoming',
		-default => $config->{remote_incoming}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Key Home  (http://keylocation)")
	  . "</td><td>"
	  . textfield(
		-name    => 'key_home',
		-default => $config->{key_home}
	  )
	  . "</td></tr><tr></tr><tr style=\"header\"><td colspan=2>"
	  . gettext("Asterisk(tm) Manager Configuration")
	  . "</td></tr><tr><td>"
	  . gettext("User")
	  . "</td><td>"
	  . textfield(
		-name    => 'astman_user',
		-default => $config->{astman_user}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Password")
	  . "</td><td>"
	  . password_field(
		-name    => 'astman_secret',
		-default => $config->{astman_secret}
	  )
	  . "</td></tr><tr><td>"
	  . gettext("Host")
	  . "</td><td>"
	  . textfield(
		-name    => 'astman_host',
		-default => $config->{astman_host}
	  )
	  . "</td></tr><tr><td>"
	  . "</td></tr><tr><td>"
	  . gettext("Action")
	  . "</td><td>"
	  . submit( -name => 'action', -value => gettext("Save...") )
	  . "</td></tr><tr><td></td><td>"
	  . submit( -name => 'action', -value => gettext("Create Database...") )
	  . "</td></tr></table>";
	return $body;
}
####################################################################################
$config     = &load_config();
$enh_config = &load_config_enh();
foreach my $param ( param() ) {
	$params->{$param} = param($param);
	print STDERR "$param $params->{$param}\n";
}
print header();
if ( !$astpp_db ) {
	$status .= "<i>" . gettext("Database Unavailable!") . "</i>\n";
}
$body = &build_configure();
print "<title>ASTPP - Open Source Voip Billing Installation</title>\n"
  . "<STYLE TYPE=\"text/css\">\n"
  . "<!--\n"
  . "  \@import url(/_astpp/style.css); \n" . "-->\n"
  . "</STYLE>\n"
  . "<BODY>\n"
  . "<table width=100\%><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
  . "<td align=center><H2>ASTPP - Open Source Voip Billing (www.astpp.org)</H2></td>"
  . "</tr><tr><td colspan = 2>"
  . "<H2>Please remove this file as soon as ASTPP is installed.</H2></td></tr>"
  . "<tr><td colspan=2>"
  . "<H2>Leaving it in place is a MAJOR security risk!!</H2></td></tr>"
  . "</td></tr></table>"
  . $body . "<hr>"
  . "<table align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr></table>"
  . "</BODY>";
