#!/usr/bin/perl -w

# Convergence FreeSwitch Tools Version 7.0 : AGI
# (c) MMII Convergence. All rights reserved.
# <info@convergence.pk> http://www.convergence.pk
# <darren@aleph-com.net> http://www.aleph-com.net

# This program is free software, distributed under the terms of
# the GNU General Public License.http://www.gnu.org/licenses.html

use strict;
use DBI();
use ASTPP;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";

use vars qw($config $astpp_db $osc_db $agile_db $cdr_db
  @output @cardlist $config $params $ASTPP);

sub initialize() {
    $config     = &load_config();
    $astpp_db = &connect_db( $config,     @output );
    $config     = &load_config_db($astpp_db,$config) if $astpp_db;
    $cdr_db   = &cdr_connect_db( $config, @output );
}

$ASTPP = ASTPP->new;
$ASTPP->set_verbosity(4); #Tell ASTPP debugging how verbose we want to be.
&initialize;

my @cc 	= ("killall", "-HUP", "freeswitch");
system(@cc) == 0
	or die "$0: system @cc failed: $?";

my @LS	= `ls -1t /usr/local/freeswitch/log/cdr-csv/Master.csv.*`;
foreach my $line (@LS) {
	chop($line);
	$ASTPP->debug( debug => $line);
	my $stm	= "load data local infile '$line' into table $config->{freeswitch_cdr_table} fields enclosed by '\"' terminated by ','";
	$ASTPP->debug( debug => $stm);
	my $ul	= $cdr_db->prepare($stm)
		or die "$0: Couldn't prepare statement $stm: " . $cdr_db->errstr;;
	$ul->execute();
	$ul->finish;
	system("cat $line >> /usr/local/freeswitch/log/cdr-csv/FULL_Master.csv");
	unlink $line;
}

exit;
