#!/usr/bin/perl
# Auto Callout
#
# Copyright (C) 2006, Aleph Communications
#
# ASTPP Team <info@astpp.org>
#
#    This program is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License version 2 as
#    published by the Free Software Foundation.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
###########################################################################`
use DBI;
use CGI;
use strict;
use Asterisk;
use Asterisk::Outgoing;
use Asterisk::Manager;
use POSIX;
use POSIX qw(strftime);
use Time::DayOfWeek qw(:dow);


my ( $config, $aac_db, $astman );

sub load_config {
    my $config;
    open( CONFIG, "</var/lib/aac/aac.conf" );
    while (<CONFIG>) {
        chomp;       # no newline
        s/#.*//;     # no comments
        s/^\s+//;    # no leading white
        s/\s+$//;    # no trailing white
        next unless length;    # anything left?
        my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
        $config->{$var} = $value;
    }
    close(CONFIG);
    return $config;
}

sub timestamp() {
    my $now = strftime "%Y%m%d%H%M%S", localtime;
    return $now;
}

sub prettytimestamp() {
    my $now = strftime "%Y-%m-%d %H:%M:%S", localtime;
    return $now;
}

sub get_hour() {
    my $now = strftime "%H", localtime;
    return $now;
}

sub get_date() {
	my $year = strftime "%Y", localtime;
	my $month = strftime "%m", localtime;
	my $day = strftime "%d", localtime;
	return ($year,$month,$day);
}

sub mark_last_try() {
    my ( $id ) = @_;
    my $tmp;
    my $timestamp = &timestamp();
    $tmp =
        "UPDATE callouts SET lasttry = "
      . $aac_db->quote($timestamp)
      . " WHERE id = "
      . $aac_db->quote( $id );
    $aac_db->do($tmp);
}

sub mark_tried() {
    my ( $id,$count ) = @_;
    my $tmp =
        "UPDATE callouts SET tries = "
      . $aac_db->quote($count)
      . " WHERE id = "
      . $aac_db->quote( $id );
    $aac_db->do($tmp);
}

sub connect_db() {
    my ( $dbh, $handle, $dsn );
    $dsn = "DBI:mysql:database=$config->{dbname};host=$config->{dbhost}";
    $dbh = DBI->connect( $dsn, $config->{dbuser}, $config->{dbpass} );
    if ( !$dbh ) {
        print STDERR "AAC DATABASE IS DOWN\n";
        exit(0);
    }
    else {
        print STDERR "Connected to AAC Database!" . "\n";
        return $dbh;
    }
}

#sub connect_manager() {
#    my $astman = new Asterisk::Manager;
#    $astman->user( $config->{astman_user} );
#    $astman->secret( $config->{astman_secret} );
#    $astman->host( $config->{astman_host} );
#    $astman->connect || die $astman->error . "\n";
#    return $astman;
#
#    # my %sip_peers0 = $astman->sendcommand( 'sip show peers', '0' );
#    # my @sip_peers2 = $astman->sendcommand( 'sip show peers', '2' );
#}

sub shutdown() {
 #   $astman->disconnect;
}

sub perform_callout() {
   my ($number) = @_;
   my $out = new Asterisk::Outgoing;
   my $channel = "Local\/" . $number->{number} . "\@$config->{destcontext}";
   $out->setvariable( 'Channel',  $channel );
   $out->setvariable( 'MaxRetries', $config->{maxretries} );
   $out->setvariable( 'RetryTime',  60 );
   $out->setvariable( 'WaitTime',   60 );
	$out->setvariable( 'Application',	"DeadAGI");
	$out->setvariable( 'Data',	"aleph-aac.agi|" . $number->{id});
	$out->setvariable( "CallerID",
		"<$number->{clidname}> $number->{clidnumber}" );
	$out->setvariable( "Account", "$number->{accountcode}" );
	$out->outtime( time() + 15 );
	$out->create_outgoing;
	print STDERR "Created Call to: $number->{number}\n";
}

sub set_in_use() {
    my ( $id, $status ) = @_;
    my $tmp;
    $tmp =
        "UPDATE callouts SET inuse = "
      . $aac_db->quote($status)
      . " WHERE id = "
      . $aac_db->quote( $id );
    $aac_db->do($tmp);
	print STDERR "SET IN USE $id Status: $status\n";
}

sub list_dialouts() {
    my ( $tmp, $sql, @numberlist );
	$tmp = "SELECT callouts.id AS id, callouts.number AS number,"
		. "series.clidname AS clidname, series.clidnumber AS clidnumber,"
		. "series.starthour AS starthour, series.endhour AS endhour,"
		. "series.Sun as Sun, series.Mon as Mon,"
		. "series.Tue as Tue, series.Wed as Wed,"
		. "series.Thu as Thu, series.Fri as Fri,"
		. "series.Sat as Sat, series.external_extension AS external_extension,"
		. "series.external_context AS external_context,"
		. "series.sound_file AS sound_file,"
		. "customers.accountcode AS accountcode "
		. "FROM callouts,series,customers "
		. "WHERE series.status = 1 AND "
		. "callouts.answered = 0 AND "
		. "series.id = callouts.series AND "
		. "customers.id = series.customer AND "
		. "callouts.tries < series.try_times";
    print STDERR $tmp;
    $sql = $aac_db->prepare($tmp);
    $sql->execute;
    while ( my $row = $sql->fetchrow_hashref ) {
        push @numberlist, $row;
	print STDERR "NUMBERLIST: $row->{number}\n";
    }
    return @numberlist;
}

sub count_in_use() {
    my ( $sql, $count, $record );
    $sql = $aac_db->prepare("SELECT COUNT(*) FROM callouts WHERE inuse = 1");
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub place_calls() {
	my (@diallist) = @_;
	foreach my $number (@diallist) {
	        my $hour = &get_hour();
	        if ($number->{Dow(&get_date)} == 1 && $hour > $number->{starthour} && $hour < $number->{endhour}) {
			&perform_callout($number);
			&mark_tried($number->{id}, $number->{tries} + 1);
			&mark_last_try($number->{id});
			sleep 20;
			my $inuse = &count_in_use();
			while ( $inuse >= $config->{maxcalls} ) {
				print STDERR "COUNT $inuse";
			      	sleep 10;
			       	$inuse = &count_in_use();
			}
		}
	}
}

##################################################################
# Start of Program
#################################################################

$config = &load_config();
$aac_db = &connect_db();
#$astman = &connect_manager();

my $hour = &get_hour();
#while ($hour < $config->{endhour}) {
while (0 < 1) {
	my @diallist = &list_dialouts();
	&place_calls(@diallist);
        my $hour = &get_hour();
	print STDERR "SLEEPING FOR A WHILE\n";
	sleep 240;
}

&shutdown();

exit(0);
