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
use DBI;
use strict;

use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
use vars qw($config $astpp_db @output @cardlist $config $params $ASTPP);

sub initialize_cur() {
    $config     = &load_config();    
    $astpp_db = &connect_db( $config, @output );    
    $config     = &load_config_db($astpp_db,$config) if $astpp_db; 
}

sub shutdown() {
	close("LOGFILE");
}

################# Program Starts HERE #################################
&initialize_cur();

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
    $sql = "UPDATE currency SET currencyRate = ".@currency_arr[1]." WHERE currency = ".$astpp_db->quote(substr (@currency_arr[0],4,3));
    $astpp_db->do($sql);
}
&shutdown;
exit(0);