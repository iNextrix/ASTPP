#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2006, Aleph Communications
#
# ASTPP Team (info@astpp.org)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
use DBI;
use Asterisk::AGI;
use POSIX qw(ceil floor);
use POSIX qw(strftime);
use strict;
use Email::Simple;
use vars qw(@output $verbose $AGI $config $SIG $astpp_db);
@output = ( "STDERR", "LOGFILE" );
$verbose = 2;
require "/usr/local/astpp/astpp-common.pl";
$AGI = new Asterisk::AGI;

sub initialize() {
	$SIG{HUP}   = 'ignore_hup';
	$config     = &load_config();
	$astpp_db = &connect_db( $config, @output );
	$config     = &load_config_db($astpp_db,$config);
}

sub ignore_hup {
	foreach my $handle (@output) {
		print $handle "HUP received\n";
		$AGI->verbose( "HUP received!\n", $verbose );
	}
}

################# Program Starts Here #################################
my $email_string;
while (<STDIN>) {
	$email_string .= $_;
}

#print STDERR $email;
my $email = Email::Simple->new($email_string);
my $subject = $email->header("Subject");
print STDERR "$subject";
&initialize;
