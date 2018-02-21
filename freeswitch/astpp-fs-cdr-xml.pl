#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2008, Aleph Communications
#
# Darren Wiebe (darren@aleph-com.net)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
#
# Usage-example:
#

use DBI;
use CGI;
use CGI qw/:standard Vars/;
use ASTPP ':all';
use URI::Escape;
use strict;
use Data::Dumper;
use XML::LibXML;


use vars
  qw($cdr_db $params $ASTPP @output $config $freeswitch_db $astpp_db $verbosity );
use Locale::gettext_pp qw(:locale_h);
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");
$verbosity = 2;
@output    = ("STDERR");
$ASTPP     = ASTPP->new;
$ASTPP->set_verbosity(4);    #Tell ASTPP debugging how verbose we want to be.

sub initialize() {
    $config = &load_config();
    $astpp_db = &connect_db( $config, @output );
    $ASTPP->set_astpp_db($astpp_db);
    $config = &load_config_db( $astpp_db, $config ) if $astpp_db;
    $freeswitch_db = &connect_freeswitch_db( $config, @output );
    $ASTPP->set_freeswitch_db($freeswitch_db);
    $cdr_db = &cdr_connect_db( $config, @output );
    $config->{cdr_table} = $config->{freeswitch_cdr_table};
}

################# Programs start here #######################################
print header( -type => 'text/plain' );
&initialize;
my ( $xml, $maxlength, $maxmins, $callstatus,$astppdid,$didinfo );
foreach my $param ( param() ) {
    $params->{$param} = param($param);
}
my $cdrinfo;

print STDERR $params->{cdr};

my $parser = XML::LibXML->new();
my $cdr    = $parser->parse_string($params->{cdr});

foreach my $var ($cdr->findnodes('/cdr/channel_data/direction')) {
    $cdrinfo->{direction} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/callingcard')) {
    $cdrinfo->{callingcard} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/callingcard_destination')) {
    $cdrinfo->{callingcard_destination} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/accountcode')) {
    $cdrinfo->{accountcode} = uri_unescape($var->to_literal);
}
foreach my $var ($cdr->findnodes('/cdr/variables/caller_id')) {
    $cdrinfo->{caller_id} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/channel_name')) {
    $cdrinfo->{channel_name} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/last_app')) {
    $cdrinfo->{last_app} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/last_arg')) {
    $cdrinfo->{last_arg} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/start_stamp')) {
    $cdrinfo->{start_stamp} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/answer_stamp')) {
    $cdrinfo->{answer_stamp} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/end_stamp')) {
    $cdrinfo->{end_stamp} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/duration')) {
    $cdrinfo->{duration} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/billsec')) {
    $cdrinfo->{billsec} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/hangup_cause')) {
    $cdrinfo->{hangup_cause} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/read_code')) {
    $cdrinfo->{read_code} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/write_code')) {
    $cdrinfo->{write_code} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/provider')) {
    $cdrinfo->{provider} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/trunk')) {
    $cdrinfo->{trunk} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/outbound_route')) {
    $cdrinfo->{outbound_route} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/progressmsec')) {
    $cdrinfo->{progressmess} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/answermsec')) {
    $cdrinfo->{answermsec} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/progress_mediamsec')) {
    $cdrinfo->{progress_mediamsec} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/callflow/caller_profile/username')) {
    $cdrinfo->{username} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/variables/effective_destination_number')) {
    $cdrinfo->{destination_number} = $var->to_literal;
}

if($cdrinfo->{destination_number} eq "")
{
    foreach my $var ($cdr->findnodes('/cdr/callflow/caller_profile/destination_number')) {
      $cdrinfo->{destination_number} = $var->to_literal;
    }
}

foreach my $var ($cdr->findnodes('/cdr/callflow/caller_profile/context')) {
    $cdrinfo->{context} = $var->to_literal;
}
foreach my $var ($cdr->findnodes('/cdr/callflow/caller_profile/uuid')) {
    $cdrinfo->{uuid} = $var->to_literal;
}
if ($cdrinfo->{callingcard_destination} && $cdrinfo->{direction} eq "outbound") {
	$cdrinfo->{destination_number} = $cdrinfo->{callingcard_destination};
}

print STDERR Dumper $cdrinfo if $config->{debug} == 1;

my $tmp = "INSERT INTO " . $config->{freeswitch_cdr_table} . "(accountcode,src,dst,dcontext,clid,channel,dstchannel,lastapp,"
	. "lastdata,calldate,answerdate,enddate,duration,billsec,disposition,amaflags,uniqueid,userfield,read_codec,"
	. "write_codec,cost,vendor,provider,trunk,outbound_route,progressmsec,answermsec,progress_mediamsec) VALUES ("
	. "'" . $cdrinfo->{accountcode} . "'"
	. "," 
	. "'" . $cdrinfo->{username} . "'" 
	. ","
	. "'" . $cdrinfo->{destination_number} . "'"
	. ","
	. "'" . $cdrinfo->{context} . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{caller_id}) . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{channel_name}) . "'"
	. ","
	. "''"
	. ","
	. "'" . $cdrinfo->{last_app} . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{last_arg}) . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{start_stamp}) . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{answer_stamp}) . "'"
	. ","
	. "'" . uri_unescape($cdrinfo->{end_stamp}) . "'"
	. ","
	. "'" . $cdrinfo->{duration} . "'"
	. ","
	. "'" . $cdrinfo->{billsec} . "'"
	. ","
	. "'" . $cdrinfo->{hangup_cause} . "'"
	. ","
	. "''"
	. ","
	. "'" . $cdrinfo->{uuid} . "'"
	. ","
	. "''"
	. ","
	. "'" . $cdrinfo->{read_code} . "'"
	. ","
	. "'" . $cdrinfo->{write_code} . "'"
	. ",'none','none'"
	. ","
	. "'" . $cdrinfo->{provider} . "'"
	. ","
	. "'" . $cdrinfo->{trunk} . "'"
	. ","
	. "'" . $cdrinfo->{outbound_route} . "'"
	. ","
	. "'" . $cdrinfo->{progressmsec} . "'"
	. ","
	. "'" . $cdrinfo->{answermsec} . "'"
	. ","
	. "'" . $cdrinfo->{progress_mediamsec} . "'"
	. ")";

print STDERR "\n" . $tmp . "\n" if $config->{debug} == 1;
$cdr_db->do($tmp);
print "Wrote CDR\n";
my (@chargelist);
push @chargelist, $cdrinfo->{uuid};
&processlist( $astpp_db, $cdr_db, $config, \@chargelist );
print STDERR "VENDOR CHARGES: " . $config->{trackvendorcharges} . "\n" if $config->{debug} == 1;
&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, "none",  $cdrinfo->{uuid},"" ) if $config->{trackvendorcharges} == 1;

if ( $cdrinfo->{callingcard_destination} && $cdrinfo->{direction} eq "outbound") {
	&process_callingcard_cdr;    
}
	
sub process_callingcard_cdr() {
	my ( $cardinfo, $brandinfo, $numberinfo, $pricelistinfo,$cc );
	my $destination = $cdrinfo->{destination_number};
	$cdrinfo->{billsec} = 0 if $cdrinfo->{hangup_cause} ne "NORMAL_CLEARING";
	$destination =~ s/@.*//g;
        my $cardnumber = $cdrinfo->{callingcard};
        $cardinfo = &get_callingcard( $astpp_db, $cardnumber, $config );
        if ( !$cardinfo ) {
                $cardinfo = &get_account_cc( $astpp_db, $cardnumber );
                $cc = 1 if $cardinfo;
        }
	$brandinfo = &get_cc_brand( $astpp_db, $cardinfo->{brand} ) if $cc == 0;
	if ($brandinfo->{reseller}) {
	        $config     = &load_config_db_reseller($astpp_db,$config,$brandinfo->{reseller});
	}
	$config     = &load_config_db_brand($astpp_db,$config,$cardinfo->{brand});
	$pricelistinfo = &get_pricelist( $astpp_db, $brandinfo->{pricelist} )
	  if $cc == 0;
	$pricelistinfo = &get_pricelist( $astpp_db, $cardinfo->{pricelist} )
	  if $cc == 1;

                    print STDERR "THIS IS A CALLINGCARD CALL! \n" if $config->{debug} == 1;
                    print STDERR "CARD: $cardinfo->{cardnumber} \n" if $config->{debug} == 1;
                    print STDERR "CARD: $cardnumber \n" if $config->{debug} == 1;
                    $numberinfo = &get_route(
                        $astpp_db, $config,
                        $destination,
                        $brandinfo->{pricelist}, $cardinfo
                    );
                    if ( $cdrinfo->{billsec} > 0 )
                    {
                        $ASTPP->debug(
                            debug     => "CALL ANSWERED",
                            verbosity => $verbosity
                        );
                        my $increment;
                        if ( $numberinfo->{inc} > 0 ) {
                            $increment = $numberinfo->{inc};
                        }
                        else {
                            $increment = $pricelistinfo->{inc};
                        }
                        $ASTPP->debug(
                            debug =>
"$numberinfo->{connectcost}, $numberinfo->{cost}, $cdrinfo->{billsec}, $increment, $numberinfo->{includedseconds}",
                            verbosity => $verbosity
                        );
                        my $charge = &calc_call_cost(
                            $numberinfo->{connectcost},
                            $numberinfo->{cost},
                            $cdrinfo->{billsec},
                            $increment,
                            $numberinfo->{includedseconds}
                        );
                        $ASTPP->debug(
                            debug     => "Cost $charge ",
                            verbosity => $verbosity
                        );
                        if ( $cardinfo->{minute_fee_pennies} > 0 ) {
                            $charge =
                              ( ( $cardinfo->{minute_fee_pennies} * 100 ) +
                                  $charge )
                              if $cardinfo->{timeused} +
                                   $cdrinfo->{billsec} =>
                                  $cardinfo->{minute_fee_minutes};
                        }
                        if ( $cardinfo->{min_length_pennies} > 0
                            && ( $cardinfo->{min_length_minutes} * 60 ) >
                             $cdrinfo->{billsec} )
                        {
                            $charge =
                              ( ( $cardinfo->{min_length_pennies} * 100 ) +
                                  $charge );
                        }
			print STDERR "CARDNUMBER: " . $cardinfo->{cardnumber} if $config->{debug} == 1;
                        &write_callingcard_cdr(
                            $astpp_db,
                            $config,
                            $cardinfo,
                            uri_unescape($cdrinfo->{caller_id}),
                             $destination,
                            uri_unescape($cdrinfo->{hangup_cause}),
                            uri_unescape($cdrinfo->{start_stamp}),
                            $charge,
                             $cdrinfo->{billsec}
                        );
                        &callingcard_set_in_use($astpp_db,$cardinfo,0);
                        &callingcard_update_balance($astpp_db,$cardinfo,$charge);
            	}
                        &callingcard_set_in_use($astpp_db,$cardinfo,0);
                }


exit(0);
