#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# ASTPP Team (info@astpp.org)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
#use LWP::Simple;
use URI::Escape;
use Getopt::Long;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;
use ASTPP;

#use strict;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
my $verbosity = 4;
$ASTPP = ASTPP->new;
$ASTPP->set_verbosity($verbosity); #Tell ASTPP debugging how verbose we want to be.

$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
$ASTPP->debug( "Interface language is set to: $ENV{'LANGUAGE'}", $verbosity);
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
use vars qw(@output $shucks $params $astpp_db $cdr_db $config
  $uniqueid $run_type);
@output = ( "STDOUT", "LOGFILE" );
$shucks = 0;

sub initialize() {
	$SIG{INT}   = \&catch_zap;
	$config     = &load_config();

	foreach my $param ( param() ) {
		$config->{$param} = param($param);
	}

	$astpp_db = &connect_db( $config,     @output );
	$config     = &load_config_db($astpp_db,$config);
	foreach my $param ( param() ) {
		$config->{$param} = param($param);
	}
	$cdr_db   = &cdr_connect_db( $config, @output );
	open( LOGFILE, ">>$config->{log_file}" )
	  || die "Log Error - could not open $config->{log_file} for writing\n";
}

sub shutdown {
	close("LOGFILE");
}
################# Program Starts HERE #################################
$uniqueid = param('uniqueid'); 
$run_type = param('runtype');

&initialize();

if ($config->{softswitch} == 1) {
	$config->{cdr_table} = $config->{freeswitch_cdr_table};
	$ASTPP->debug("Rating calls for FreeSwitch", $verbosity);
	&cleanup_cdrs_fs($cdr_db, $config);
} else {
	$config->{cdr_table} = $config->{asterisk_cdr_table};
	$ASTPP->debug("Rating calls for Asterisk", $verbosity);
	&cleanup_cdrs($cdr_db, $config);
}

if ( $uniqueid ne "" ) {
	my @chargelist;
	push @chargelist, $uniqueid;
	#sleep $config->{sleep};
	&processlist($astpp_db, $cdr_db,$config, \@chargelist);
	#&cleanup_cdrs($cdr_db, $config);
	&shutdown();
	exit(0);
}
elsif ($run_type eq "cdr_shell" || $ARGV[2] && $ARGV[5] && $ARGV[7] && $ARGV[14]) {
	my %args = (
		accountcode => $ARGV[0],
		src => $ARGV[1],
		dst => $ARGV[2],
		dst2 => $ARGV[3],
		clid => $ARGV[4],
		channel => $ARGV[5],
		dcontext => $ARGV[6],
		lastapp => $ARGV[7],
		lastdata => $ARGV[8],
		answertime => $ARGV[9],
		starttime => $ARGV[10],
		endtime => $ARGV[11],
		billsec => $ARGV[12],
		duration => $ARGV[13],
		disposition => $ARGV[14],
		amaflags => $ARGV[15],
		uniqueid => $ARGV[16],
		dstchannel => $ARGV[17],
		userfield => $ARGV[18],
		cost => 'none',
		vendor => 'none'
	);
	my @chargelist;
	push @chargelist, $ARGV[16];
	&processlist($astpp_db, $cdr_db, $config, \@chargelist, \%args);
	&cleanup_cdrs($cdr_db, $config);
}
elsif ( $run_type eq "price_only" ) {

	# If we are using this mode then we need the following parameters set:
	# dest          Number Dialed
	# pricelist     Pricelist to bill for
	# billseconds   Length of call in seconds
	#
	# It will return the cost of the call in 100ths of a penny.
	my ($increment);
	my $branddata = &get_pricelist( $astpp_db, param('pricelist') );
	my $numdata =
	  &get_route( $astpp_db, $config, param('dest'), param('pricelist') );
	if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
		$numdata->{connectcost} = $numdata->{connectcost} * ( ( $branddata->{markup} / 10000 ) + 1 );
		$numdata->{cost} = $numdata->{cost} * ( ( $branddata->{markup} / 10000 ) + 1 );
	}
	if ( $numdata->{inc} > 0 ) {
			$increment = $numdata->{inc};
	}
	else {
		$increment = $branddata->{inc};
	}  
	my $cost = &calc_call_cost( $numdata->{connectcost},
		$numdata->{cost}, param('billsec'), $increment,
		$numdata->{includedseconds} );
	$ASTPP->debug("Call Cost: $cost", $verbosity);
	exit(0);
}
elsif ( $run_type eq "realtime" ) {
	use POE;
	use POE::Component::Client::Asterisk::Manager;
	$ASTPP->debug(gettext("Connecting to Asterisk"), $verbosity);
	POE::Component::Client::Asterisk::Manager->new(
		Alias         => 'monitor',
		RemoteHost    => $config->{astman_host},
		RemotePort    => 5038,                                    # default port
		Username      => $config->{astman_user},
		Password      => $config->{astman_secret},
		CallBacks     => { Hangup => { 'Event' => 'Hangup' }, },
		inline_states => {
			Hangup => sub {
				my $input      = $_[ARG0];
				my @chargelist = $input->{Uniqueid};
				$ASTPP->debug("$input->{Uniqueid} Just Hungup!",$verbosity);
				if ( !$astpp_db ) {
					$astpp_db = &connect_db( $config, @output );
				}
				if ( !$cdr_db ) {
					$cdr_db = &connect_db( $config, @output );
				}
				my $cdrinfo  = &get_cdr( $config, $cdr_db, $input->{Uniqueid},1 );
#				my $tmp = "UPDATE $config->{asterisk_cdr_table} SET cost = 'rating' WHERE uniqueid = " . $input->{Uniqueid} . " AND cost = 'none' AND dst = " . $cdr_db->quote($cdrinfo->{dst}) . " LIMIT 1";
#				print STDERR $tmp if $config->{debug} == 1;
#				print $tmp if $config->{debug} == 1;
#				$cdr_db->do($tmp);
				if ($cdrinfo->{lastapp} eq "MeetMe" || $cdrinfo->{billsec} > 0 || $cdrinfo->{cost} eq "none") { 
					&processlist($astpp_db, $cdr_db, $config, \@chargelist);
					&vendor_process_rating( $astpp_db, $cdr_db, $config, "none", $input->{Uniqueid} ) if $config->{trackvendorcharges} == 1;
				} # else {
					&cleanup_cdrs($cdr_db, $config);
				#}	
			},
		},
	);
	$poe_kernel->run();
	&shutdown();
	exit(0);
}
else {
	my @chargelist;
	my $phrase = "none";
	@chargelist = &list_cdrs_status( $config, $cdr_db, $phrase );    # Grab a list of all calls with "none" assigned in the cost field
	&processlist($astpp_db, $cdr_db, $config,\@chargelist);    # Bill as many calls as we can.
	$phrase = "error";
	@chargelist = &list_cdrs_status( $config, $cdr_db, $phrase );    # Grab a list of all calls with "none" assigned in the cost field
	&processlist($astpp_db, $cdr_db, $config, \@chargelist);    # See if we can now bill some of the calls that are marked in "error"
	if ($config->{trackvendorcharges} == 1) {
		print STDERR gettext("START ON VENDOR CALL RATING!") . "\n"  if $config->{debug} == 1;
		if ($config->{softswitch} == 0) {
			&vendor_process_rating( $astpp_db, $cdr_db, $config, "none", 0 );
			&vendor_process_rating( $astpp_db, $cdr_db, $config. $config, "error", 0 );
		} elsif ($config->{softswitch} == 1) {
			&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, "none", 0 );
			&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, $config, "error", 0 );
		}
	print STDERR gettext("VENDOR CALLS WHICH HAVE NOT BEEN RATED.") . "\n" if $config->{debug} == 1;
	# Print a list of calls which have not been rated
	&vendor_not_billed($config, $cdr_db);
	}
	&cleanup_cdrs($cdr_db, $config);
}
&shutdown();
exit(0);
