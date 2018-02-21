#!/usr/bin/perl
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# ASTPP Team (info@astpp.org)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
#  minbalance should be specified in dollars.
#
# @daily /usr/local/astpp/astpp-low-balance.pl minbalance=5
#
###############################################################################
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Locale::Language;
use Locale::gettext_pp qw(:locale_h);
use lib './lib', '../lib';
use warnings;
#use strict;
require "/usr/local/astpp/astpp-common.pl";
$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";

bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");

my ( $config, $params, $astpp_db, @cardlist );
my @output = ("STDERR");

sub initialize() {
    $config     = &load_config();
    $astpp_db   = &connect_db( $config, @output );
    $config     = &load_config_db($astpp_db,$config);
}

###########################################
# Program Starts Here
###########################################
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n";
}

&initialize();

@cardlist = &list_accounts($astpp_db);
$params->{minbalance} = 1;
foreach my $card (@cardlist) {
    my $cardinfo = &get_account( $astpp_db,  $card );
    my $balance  = &accountbalance( $astpp_db, $card );       
    $balance = $balance / 1;
    if ( ($balance * -1) <= $params->{minbalance} && $cardinfo->{posttoexternal} == 0 )    
    {
        print "\n Card Number: $card Balance: $balance\n";
	&email_low_balance( $config, $cardinfo->{email},
            $balance );
    }
}
