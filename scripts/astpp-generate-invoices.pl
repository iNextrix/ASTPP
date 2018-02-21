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
# @hourly /usr/local/astpp/astpp-generate-invoices.pl
# @daily /usr/local/astpp/astpp-generate-invoices.pl sweep=0
# 0 0 * * 0 /usr/local/astpp/astpp-generate-invoices.pl sweep=1
# 0 0 1 * * /usr/local/astpp/astpp-generate-invoices.pl sweep=2
# 0 0 1 1,4,7,10 * /usr/local/astpp/astpp-generate-invoices.pl sweep=3
# 0 0 * 1,7 * /usr/local/astpp/astpp-generate-invoices.pl sweep=4
# 0 0 * 1 * /usr/local/astpp/astpp-generate-invoices.pl sweep=5
#
# To generate invoices only for a specific date range use the startdate & enddate.
# ie startdate=2008-06-01 endate=2008-06-30
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
use ASTPP;

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
$ASTPP     = ASTPP->new;
$ASTPP->set_verbosity(4);    #Tell ASTPP debugging how verbose we want to be.


sub initialize() {
    $config     = &load_config();
    $astpp_db = &connect_db( $config,     @output );
    $config     = &load_config_db($astpp_db,$config) if $astpp_db;
    $cdr_db   = &cdr_connect_db( $config, @output );
    $osc_db   = &osc_connect_db( $config, @output )
      if $config->{externalbill} eq "oscommerce";
    open( LOGFILE, ">>$config->{log_file}" )
      || die "Error - could not open $config->{log_file} for writing\n";
    $ASTPP->set_astpp_db($astpp_db);
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


# print $config->{externalbill};exit;

### Deal with external billing.  This will create the appropriate invoices in the external apps.
if ( $config->{externalbill} eq "oscommerce" ) {
    my @cardlist;
    if ( $params->{sweep} ) {
        @cardlist = &update_list_cards($astpp_db, $config, $params->{sweep} );
    }
    
    foreach (@cardlist) {
        my $cardno = $_;
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
	        foreach my $handle (@output) {
      		      print $handle "Card: $cardno \n";
   		}
	        &osc_charges($astpp_db, $osc_db, $config, $cardno,$params);
	}
    }
}
elsif ( $config->{externalbill} eq "internal" ) {
    my @cardlist;    
    if ( $params->{sweep} || $params->{sweep}=='0') {      
        @cardlist = &update_list_cards($astpp_db, $config, $params->{sweep} );	
    }    
    foreach (@cardlist) {
        my $cardno = $_;
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
	        foreach my $handle (@output) {
      		      print $handle "Card: $cardno \n";
   		}
		my $cdr_count = $ASTPP->invoice_cdrs(
			accountid	=> $carddata->{accountid},
			cardnum		=> $carddata->{number},
			function	=> 1
			);
		if ($cdr_count > 0) {
		my $invoice = $ASTPP->invoice_create_internal(
			accountid	=> $carddata->{accountid}
			);
		$ASTPP->invoice_cdrs(
			accountid	=> $carddata->{accountid},
			cardnum		=> $carddata->{number},
			invoiceid	=> $invoice,
			function	=> 3
			);

		my $sort_order = 1;

		my $subtotal = $ASTPP->invoice_cdrs_subtotal_internal(
			invoiceid	=> $invoice
			);
		$sort_order = $ASTPP->invoice_subtotal_post_internal(
			decimalpoints_total => $config->{decimalpoints_total},
			invoiceid	=> $invoice,
			sort_order	=> $sort_order,
			value 		=> $subtotal,
			title		=> "Sub Total",
			text		=> "Sub Total",
			class		=> "1"
			);
		$sort_order = $ASTPP->invoice_taxes_internal(
			accountid	=> $carddata->{accountid},
			invoiceid	=> $invoice,
			sort_order	=> $sort_order,
			function	=> 2,
			decimalpoints_tax => $config->{decimalpoints_tax},
			decimalpoints_total => $config->{decimalpoints_total},
			invoice_subtotal => $subtotal
			);
		$subtotal = $ASTPP->invoice_subtotal_internal(
			invoiceid	=> $invoice
			);
		$sort_order = $ASTPP->invoice_subtotal_post_internal(
			decimalpoints_total => $config->{decimalpoints_total},
			invoiceid	=> $invoice,
			sort_order	=> $sort_order,
			value 		=> $subtotal,
			title		=> "Total",
			text		=> "Total",
			class		=> "9" #class 9 = total
			);
		}
	}
    }
}
elsif ( $config->{externalbill} eq "agile" ) {
    my @cardlist = &list_cards($astpp_db);
    foreach my $cardno (@cardlist) {
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
            my @recordlist = &get_charges($astpp_db, $config, $cardno,$params);
            foreach my $record (@recordlist) {
                my $cdrinfo = &get_charge($record);
                my $cost;
                if ( $cdrinfo->{debit} ne "" ) {
                    $cost = $cdrinfo->{debit};
                }
                else {
                    $cost = $cdrinfo->{credit} * -1;
                }
                &agilesavecdr(
                    $agile_db, $astpp_db,
                    $config,  
                    @output,   $carddata,
                    $cost,     $config->{agile_site_id},
                    $cdrinfo,  $config->{agile_dbprefix}
                );
            }
        }
    }
}
elsif ( $config->{externalbill} eq "optigold" ) {
    my @cardlist = &list_cards($astpp_db);
    foreach my $cardno (@cardlist) {
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
            my @recordlist = &get_charges($astpp_db, $config, $cardno,$params);
            foreach my $record (@recordlist) {
                my $cdrinfo = &get_charge($record);
                my $cost;
                if ( $cdrinfo->{debit} ne "" ) {
                    $cost = $cdrinfo->{debit};
                }
                else {
                    $cost = $cdrinfo->{credit} * -1;
                }
                &ogsavecdr(
                    $carddata->{number},  $cdrinfo->{disposition},
                    $cdrinfo->{calldate}, $cdrinfo->{dst},
                    $cdrinfo->{billsec},  $cost,
                    $cdrinfo->{src}
                );
            }
        }
    }
}
&shutdown;
exit(0);
