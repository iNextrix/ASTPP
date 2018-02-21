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
use DBI;
use POSIX qw(ceil floor);
use POSIX qw(strftime);
use strict;
use vars qw(@output $config $astpp_db );
@output = ("STDERR");
require "/usr/local/astpp/astpp-common.pl";

sub initialize() {
    $SIG{HUP} = 'ignore_hup';
    $config   = &load_config();
    $astpp_db = &connect_db($config);
    $config     = &load_config_db($astpp_db,$config);
}

sub list_active_callingcards() {
    my ( $sql, @cardlist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT cardnumber FROM callingcards WHERE status = 1");
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cardlist, $row;
    }
    $sql->finish;
    return @cardlist;
}

sub update_balance() {
    my ( $cardinfo, $charge ) = @_;
    my $sql =
        "UPDATE callingcards SET used = "
      . $astpp_db->quote( $cardinfo->{maint_fee_pennies} + $cardinfo->{used} )
      . " WHERE cardnumber = "
      . $astpp_db->quote( $cardinfo->{cardnumber} );
    $astpp_db->do($sql);
}
################## Program starts here ##############################
my ($sql);
&initialize();
my @cardlist = &list_active_callingcards();
foreach my $cardinfo (@cardlist) {
    my $sql = $astpp_db->prepare("SELECT NOW() ");
    $sql->execute;
    my $now = $sql->fetchrow_hashref;
    $sql->finish;
    if (   $cardinfo->{maint_fee_days} > 0
        && $cardinfo->{status} == 1
        && $now >= $cardinfo->{expiry} )
    {
        $sql =
	  "INSERT INTO callingcardcdrs (cardnumber,clid,disposition,callstart,charge) VALUES ("
          . $astpp_db->quote( $cardinfo->{cardnumber} )
          . ", 'Maintenance Fee', "
          . $astpp_db->quote( $$cardinfo->{maint_fee_pennies} / 1 ) . ")";
        $astpp_db->do($sql);
        my $sql =
            "UPDATE callingcards SET expiry = DATE_ADD("
          . $astpp_db->quote( $cardinfo->{firstuse} )
          . ", INTERVAL "
          . $astpp_db->quote( $cardinfo->{maint_fee_days} )
          . " day) WHERE number = "
          . $astpp_db->quote( $cardinfo->{cardnumber} );
        $astpp_db->do($sql);
        &update_balance($cardinfo);
    }
}
