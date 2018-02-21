#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
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
# @hourly /usr/local/astpp/astpp-currency-update.pl

use LWP::Simple qw(!head);
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
use vars qw($config $astpp_db $osc_db $agile_db $cdr_db
  @output @cardlist $config $params $ASTPP);
@output = ( "STDOUT", "LOGFILE" );
$ASTPP     = ASTPP->new;
$ASTPP->set_verbosity(4);    #Tell ASTPP debugging how verbose we want to be.


sub initialize() {
    $config     = &load_config();    
    $astpp_db = &connect_db( $config, @output );    
    $config     = &load_config_db($astpp_db,$config) if $astpp_db;    
    $config->{base_currency}='USD';
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

my ( $url, @currencylist, $currency, $content, @content_data,
        @currency, @currency_arr,$sql,$val );

$url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=';
@currencylist = &get_all_currency($astpp_db, $config);
foreach $currency(@currencylist) {
    $url .= $config->{base_currency}.$currency.'=X+';
}
$url .= '&f=l1';

$content = get $url;
@content_data = split(' ',$content);

foreach $val(@content_data){
    @currency_arr= split(',', $val,3);
    $sql = "UPDATE currency SET CurrencyRate = ".@currency_arr[1]." WHERE Currency = ".$astpp_db->quote(substr (@currency_arr[0],4,3));
    $astpp_db->do($sql);
}
&shutdown;
exit(0);