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
#
################### NOTHING AFTER HERE SHOULD NEED CHANGING ####################
# You will need lines like this in your crontab
#
# @hourly /usr/local/astpp/astpp-update-balance.pl
# @daily /usr/local/astpp/astpp-update-balance.pl sweep=0
# 0 0 * * 0 /usr/local/astpp/astpp-update-balance.pl sweep=1
# 0 0 1 * * /usr/local/astpp/astpp-update-balance.pl sweep=2
# 0 0 1 1,4,7,10 * /usr/local/astpp/astpp-update-balance.pl sweep=3
# 0 0 * 1,7 * /usr/local/astpp/astpp-update-balance.pl sweep=4
# 0 0 * 1 * /usr/local/astpp/astpp-update-balance.pl sweep=5
#
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Getopt::Long;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;

#use strict;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
use vars qw($config $astpp_db $osc_db $agile_db $cdr_db
  @output @cardlist $config $params);
@output = ( "STDOUT", "LOGFILE" );

sub initialize() {
    $config     = &load_config();
    $astpp_db   = &connect_db( $config, @output );
    $config     = &load_config_db( $astpp_db, $config ) if $astpp_db;
    $cdr_db     = &cdr_connect_db( $config, @output );
    $osc_db     = &osc_connect_db( $config, @output )
      if $config->{externalbill} eq "oscommerce";
    open( LOGFILE, ">>$config->{log_file}" )
      || die "Error - could not open $config->{log_file} for writing\n";
}

sub shutdown() {
    close("LOGFILE");
}

################# Program Starts HERE #################################
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n";
}
&initialize();
@cardlist = &update_list_cards( $astpp_db, $config );
foreach (@cardlist) {
    my $cardno = $_;
    foreach my $handle (@output) {
        print $handle "Card: $cardno \n";
    }
    my $cardinfo = &get_account( $astpp_db, $cardno );
    my $cost = &calc_charges( $astpp_db, $config, $cardno, @output );
    if ( $cost != 0 ) {
        my $balance = $cost + $cardinfo->{balance};
        &update_astpp_balance( $astpp_db, $cardno, $balance );
    }
}
## Update calling cards and expire them and mark them as being empty.
#
$astpp_db->do("UPDATE callingcards SET status = 2 WHERE value - used <= 0");
$astpp_db->do("UPDATE callingcards SET status = 2 WHERE expiry <= NOW() AND expiry != '0000-00-00 00:00:00'");

if ( $params->{sweep} == 0 ) {

   #Here comes our daily maintenance.
   #Task # 1 - Check which dids have been assigned but have not been billed yet.
   #
    my $tmp =
"SELECT * FROM dids WHERE chargeonallocation = 0 and allocation_bill_status = 0 AND account != ''";
    print STDERR "$tmp \n" if $config->{debug} == 1;
    my $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( my $record = $sql->fetchrow_hashref ) {
        my $tmp =
            "SELECT * FROM $config->{asterisk_cdr_table} WHERE dst = "
          . $astpp_db->quote( $record->{number} )
          . " AND disposition = 'ANSWERED' LIMIT 1";
        print STDERR "$tmp \n" if $config->{debug} == 1;
        my $sql = $cdr_db->prepare($tmp);
        $sql->execute;
        while ( my $cdr = $sql->fetchrow_hashref ) {
	    print STDERR "Billing for DID: $record->{number} belonging to $record->{account}\n";
            &apply_did_activated_charges( $astpp_db, $config, $record->{number},
                $record->{account} );
        }
        $sql->finish;
    }
    $sql->finish;
}
elsif ( $params->{sweep} == 2 ) {    #If it's monthly billing we process DIDs.
    my @account_list = &list_all_accounts( $astpp_db );
    foreach my $account (@account_list) {
        my $accountinfo = &get_account( $astpp_db,       $account );
        my @did_list    = &list_dids_account( $astpp_db, $account );
        my $callstart   = &prettytimestamp;
        foreach my $did (@did_list) {
            my $accountdata = $accountinfo;
            my $dest        = gettext("DID: ") . $did->{number};
            while ( $accountdata->{reseller} ne '' ) {
                $didinfo =
                  &get_did_reseller( $astpp_db, $did->{number},
                    $accountdata->{reseller} );
                print STDERR
"DID: $did->{number} - ACCOUNT: $accountdata->{number} - CHARGE: $didinfo->{monthlycost}\n"
                  if $config->{debug} == 1;
                if ( $didinfo->{monthlycost} != 0 ) {
                    $dest = gettext("DID: ") . $did->{number};
                    &post_cdr(
                        $astpp_db,                    $config,
                        '',                           $accountdata->{number},
                        '',                           $dest,
                        '',                           '',
                        $didinfo->{monthlycost},      $callstart,
                        $accountdata->{postexternal}, '',
                        $notes
                    );
                }
                $accountdata =
                  &get_account( $astpp_db, $accountdata->{reseller} );
            }
            $didinfo = &get_did( $astpp_db, $did->{number}, $account );
            print STDERR
"DID: $did->{number} - ACCOUNT: $accountdata->{number} - CHARGE: $didinfo->{monthlycost}\n"
              if $config->{debug} == 1;
            if ( $didinfo->{monthlycost} != 0 ) {
                &post_cdr(
                    $astpp_db,                    $config,
                    '',                           $accountdata->{number},
                    '',                           $dest,
                    '',                           '',
                    $didinfo->{disconnectionfee}, $callstart,
                    $accountdata->{postexternal}, '',
                    $notes
                );
            }
            $accountdata = &get_account( $astpp_db, $account );
        }
    }
    # We also reset the counters every month.
    $astpp_db->do("UPDATE counters SET status = 2");
}

if ( $params->{sweep} ) {
    my @pricelistlist = &list_pricelists($astpp_db);
    foreach my $pricelist (@pricelistlist) {
        my @pricelist_charge_list =
          &list_pricelist_charges( $astpp_db, $pricelist );
        my @account_list = &list_pricelist_accounts( $astpp_db, $pricelist );

        foreach my $account (@account_list) {
            my @account_charge_list =
              &list_account_charges( $astpp_db, $account );
            my $accountinfo = &get_account( $astpp_db, $account );
            foreach my $charge (@pricelist_charge_list) {
                my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
                if ( $chargeinfo->{sweep} == $params->{sweep} ) {
                    my $now = &prettytimestamp;
                    &post_cdr(
                        $astpp_db,
                        $config,
                        '',
                        $account,
                        '',
                        $chargeinfo->{description},
                        '',
                        '',
                        $chargeinfo->{charge},
                        $now,
                        $accountinfo->{posttoexternal},
                        '','','',''
                    );
                }
            }
            foreach my $charge (@account_charge_list) {
                my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
                if ( $chargeinfo->{sweep} == $params->{sweep} ) {
                    my $now = &prettytimestamp;
                    &post_cdr(
                        $astpp_db,
                        $config,
                        '',
                        $account,
                        '',
                        $chargeinfo->{description},
                        '',
                        '',
                        $chargeinfo->{charge},
                        $now,
                        $accountinfo->{posttoexternal},
                        '','','',''
                    );
                }
            }
        }
    }
}
&shutdown;
exit(0);
