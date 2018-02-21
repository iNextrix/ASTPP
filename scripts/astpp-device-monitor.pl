#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2007, Aleph Communications
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
use Getopt::Long;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;
use ASTPP;
use strict;
use lib './lib', '../lib';

require "/usr/local/astpp/astpp-common.pl";

$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
use vars qw($ASTPP @output $shucks $params $astpp_db $cdr_db $config
  $uniqueid $run_type);
@output = ( "STDOUT", "LOGFILE" );
$shucks = 0;
$ASTPP = ASTPP->new;
$ASTPP->set_verbosity(4); #Tell ASTPP debugging how verbose we want to be.

sub initialize() {
	$SIG{INT}   = \&catch_zap;
	$config     = &load_config();
	$astpp_db = &connect_db( $config, @output );
	$config     = &load_config_db($astpp_db,$config);
	$ASTPP->set_astpp_db($astpp_db);
}

################# Program Starts HERE #################################
&initialize();

use POE;
use POE::Component::Client::Asterisk::Manager;
$ASTPP->debug( debug=> gettext("Connecting to Asterisk"));
POE::Component::Client::Asterisk::Manager->new(
	Alias         => 'monitor',
	RemoteHost    => $config->{astman_host},
	RemotePort    => 5038,                                    # default port
	Username      => $config->{astman_user},
	Password      => $config->{astman_secret},
	CallBacks     => { 
		input => ':all',
		dial => { 'Event' => 'Newexten', 			# This is triggered when an extension starts a call.
#			'Application' => 'Dial'
		},
		hangup => { 'Event' => 'Hangup',
		},
		answered => { 'Event' => 'Newstate',
		},
                register => { 'Event' => 'PeerStatus',          # Triggered when a device is marked as registered.
                        'PeerStatus' => 'Registered',
                },
		unregister => { 'Event' => 'PeerStatus',        # Triggered when a device is marked as unregistered.
	                 'PeerStatus' => 'Unregistered',
	        },

	},
	inline_states => {
		input	   => \&call_debug,
        	register   => \&manager_record_register,
        	dial       => \&manager_record_dial,
        	hangup     => \&manager_record_hangup,
        	unregister => \&manager_record_unregister,
        	answered   => \&manager_record_answer,
	},
);

sub call_debug() {
	&debug($_[ARG0]);
}

sub manager_record_register() {
	my $peer = $_[ARG0]->{Peer};
	my @extension = split("/", $peer);
	if ( !$astpp_db ) {
		$astpp_db = &connect_db( $config, @output );
	}

	$ASTPP->debug( debug=> "Peer: $peer REGISTERED");
	my $tmp = "INSERT INTO extensions_status (tech,extension,status,Event,Privilege,PeerStatus,Peer) VALUES ("
		. $astpp_db->quote(@extension[0]) . ","
		. $astpp_db->quote(@extension[1]) . ",'Registered',"
		. $astpp_db->quote($_[ARG0]->{Event}) . ","
		. $astpp_db->quote($_[ARG0]->{Privilege}) . ","
		. $astpp_db->quote($_[ARG0]->{PeerStatus}) . ","
		. $astpp_db->quote($_[ARG0]->{Peer}) . ")";
	$ASTPP->debug( debug=> "SQL: $tmp");
	$astpp_db->do($tmp);
}

sub manager_record_unregister() {
	my $peer = $_[ARG0]->{Peer};
	my @extension = split("/", $peer);
	if ( !$astpp_db ) {
		$astpp_db = &connect_db( $config, @output );
	}
	$ASTPP->debug( debug=> "Peer: $peer UNREGISTERED");
	my $tmp = "INSERT INTO extensions_status (tech,extension,status) VALUES ("
		. $astpp_db->quote(@extension[0]) . ","
		. $astpp_db->quote(@extension[1]) . ",'UnRegistered')";
	$ASTPP->debug( debug=> "SQL: $tmp");
	$astpp_db->do($tmp);
}

sub manager_record_answer() {
	my $source = $_[ARG0]->{Channel};
	my @extension = split("/", $source);
	my @extension1 = split("-", @extension[1]);
	if ( !$astpp_db ) {
		$astpp_db = &connect_db( $config, @output );
	}
	$ASTPP->debug( debug=> "Peer: @extension1[0] Answer");
	my $tmp = "INSERT INTO extensions_status (tech,extension,status,UniqueID,number) VALUES ("
		. $astpp_db->quote(@extension[0]) . ","
		. $astpp_db->quote(@extension1[0]) . ",'Answer',"
		. $astpp_db->quote($_[ARG0]->{Uniqueid}) . ","
		. $astpp_db->quote($_[ARG0]->{Extension}) . ")";
	$ASTPP->debug( debug=> "SQL: $tmp");
	$astpp_db->do($tmp);
}

sub manager_record_dial() {
	my $source = $_[ARG0]->{Channel};
	my @extension = split("/", $source);
	my @extension1 = split("-", @extension[1]);
	if ( !$astpp_db ) {
		$astpp_db = &connect_db( $config, @output );
	}
	$ASTPP->debug( debug=> "Peer: @extension1[0] PLACING CALL");
	my $tmp = "INSERT INTO extensions_status (tech,extension,status,number,Channel,Privilege,Context,AstExtension,Application,Uniqueid,AppData,Priority) VALUES ("
		. $astpp_db->quote(@extension[0]) . ","
		. $astpp_db->quote(@extension1[0]) . ",'Dial',"
		. $astpp_db->quote($_[ARG0]->{Extension}) . ","
		. $astpp_db->quote($_[ARG0]->{Channel}) . ","
		. $astpp_db->quote($_[ARG0]->{Privilege}) . ","
		. $astpp_db->quote($_[ARG0]->{Context}) . ","
		. $astpp_db->quote($_[ARG0]->{Extension}) . ","
		. $astpp_db->quote($_[ARG0]->{Application}) . ","
		. $astpp_db->quote($_[ARG0]->{Uniqueid}) . ","
		. $astpp_db->quote($_[ARG0]->{AppData}) . ","
		. $astpp_db->quote($_[ARG0]->{Priority}) . ")";
	$ASTPP->debug( debug=> "SQL: $tmp");
	$astpp_db->do($tmp);
}

sub manager_record_hangup() {
	my $channel = $_[ARG0]->{Channel};
	my @extension = split("/", $channel);
	my @extension1 = split("@", @extension[1]);
	my @extension2 = split("-", @extension1[0]);
	if ( !$astpp_db ) {
		$astpp_db = &connect_db( $config, @output );
	}
	$ASTPP->debug( debug=> "Peer: @extension2[0] HUNGUP");
	my $tmp = "INSERT INTO extensions_status (tech,extension,UniqueID,status) VALUES ("
		. $astpp_db->quote(@extension[0]) . ","
		. $astpp_db->quote($_[ARG0]->{Uniqueid}) . ","
		. $astpp_db->quote(@extension2[0]) . ",'Hungup')";
	$ASTPP->debug( debug=> "SQL: $tmp");
	$astpp_db->do($tmp);
}

$poe_kernel->run();
exit(0);
