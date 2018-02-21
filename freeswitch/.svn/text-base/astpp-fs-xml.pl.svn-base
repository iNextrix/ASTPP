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
use XML::Simple;
use Data::Dumper;
use URI::Escape;
use strict;

use vars
  qw($void_xml $cdr_db $params $ASTPP @output $config $freeswitch_db $astpp_db $verbosity );
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

$void_xml = header( -type => 'text/plain' );
$void_xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
$void_xml .= "<document type=\"freeswitch/xml\">\n";
$void_xml .= "<section name=\"result\">\n";
$void_xml .= "<result status=\"not found\" />";
$void_xml .= "</section>\n";
$void_xml .= "</document>\n";

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
&initialize;
my ( $ipinfo, $xml, $maxlength, $maxmins, $callstatus,$astppdid,$didinfo );
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    $ASTPP->debug( debug => "$param $params->{$param}" );
}
$xml = header( -type => 'text/plain' );

$ASTPP->debug(
    debug => "Destination = $params->{'Caller-Destination-Number'}" );

if ( $params->{section} eq "dialplan" ) {
    # Check to see if this is a DID.  If it is we handle it differently.
    #
    $didinfo = &get_did($astpp_db, $params->{'Caller-Destination-Number'});
    if ($didinfo->{number}) {
	$astppdid = "ASTPP-DID";
        $ASTPP->debug( debug => "This is a call for a DID: ");
	$params->{variable_accountcode} = $didinfo->{account};
    }


    if ( !$params->{variable_accountcode} ) {

      # First we strip off X digits to see if this account is prepending numbers
      # as authentications
        $ASTPP->debug( debug => "Checking CC Number: "
              . $params->{'Caller-Destination-Number'} );
        my $cc = substr( $params->{'Caller-Destination-Number'},
            0, $config->{cardlength} );
        my $sql =
          $astpp_db->prepare("SELECT number FROM accounts WHERE cc = $cc");
        $sql->execute;
        my $record = $sql->fetchrow_hashref;
        $sql->finish;
        $params->{variable_accountcode} = $record->{cardnum}
          if ( $record->{cardnum} );
    }
    if ( !$params->{variable_accountcode} ) {
        $ASTPP->debug(
            debug => "Checking IP Address:" . $params->{'Hunt-Network-Addr'} );
        $ipinfo = $ASTPP->ip_address_authenticate(
            ip_address  => $params->{'Hunt-Network-Addr'},
            destination => $params->{'Caller-Destination-Number'}
        );
        if ($ipinfo->{account} ne "") {
            $params->{variable_accountcode} = $ipinfo->{account};
            $params->{'Caller-Destination-Number'} =~ s/$ipinfo->{prefix}//g;
        }
    }

    $xml = $ASTPP->fs_dialplan_xml_header(
        xml                => $xml,
        destination_number => $params->{'Caller-Destination-Number'},
	DID		   => $didinfo->{number},
	IP		   => $ipinfo->{account}
    );

    $ASTPP->debug( debug =>
"$params->{variable_accountcode}, $params->{'Caller-Destination-Number'}"
    );

    my $carddata =
      &get_account( $astpp_db, $params->{variable_accountcode} )
      ;    # Fetch all the account info from the db.

    if ( !$carddata->{number} )
    {      # Check to see if the account exists.  If not then exit.
        $ASTPP->debug( debug => "CALLSTATUS 2" );
        $ASTPP->debug( debug => "CANNOT RETRIEVE CARD" );
        $xml .=
          "<action application=\"reject\" data=\"CANNOT RETRIEVE ACCOUNT\"/>\n";
        $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
        $ASTPP->debug( debug => "Returning nothing so dialplan can continue." );
    	$ASTPP->debug( debug => $void_xml );
	print $void_xml;
        exit(0);
    }

    if ( $carddata->{dialed_modify} ) {
        my @regexs = split( m/,/m, $carddata->{dialed_modify} );
        foreach my $regex (@regexs) {
            $regex =~ s/"//g;    #Strip off quotation marks
            my ( $grab, $replace ) = split( m!/!i, $regex )
              ; # This will split the variable into a "grab" and "replace" as needed
            $ASTPP->debug( debug => "Grab: $grab" );
            $ASTPP->debug( debug => "Replacement: $replace" );
            $ASTPP->debug( debug =>
                  "Phone Before: $params->{'Caller-Destination-Number'}" );
            $$params->{'Caller-Destination-Number'} =~ s/$grab/$replace/is;
            $ASTPP->debug( debug =>
                  "Phone After: $params->{'Caller-Destination-Number'}" );
        }
    }

    $ASTPP->debug( debug => "FINDING LIMIT FOR: " . $carddata->{number} );
    ( $callstatus, $maxlength ) =
      &max_length( $astpp_db, $config, $carddata,
        $params->{'Caller-Destination-Number'} );

    my $routeinfo = &get_route(
        $astpp_db, $config,
        $params->{'Caller-Destination-Number'},
        $carddata->{pricelist}, $carddata,$astppdid
    );

    $ASTPP->debug( debug => "Cost: " . $routeinfo->{cost} );
    $ASTPP->debug( debug => "Pricelist: " . $routeinfo->{pricelist} );
    my $minimumcharge = $routeinfo->{cost};
    my @reseller_list;
    $ASTPP->debug( debug => "CALLSTATUS: $callstatus MAX_LENGTH: $maxlength" );

    if (!$routeinfo->{cost} && !$routeinfo->{pricelist}) {
        $ASTPP->debug( debug => "COULD NOT FIND ROUTE.  EXITING SO DIALPLAN CAN TAKE OVER" );
    	$ASTPP->debug( debug => $void_xml );
	print $void_xml;
	exit(0);
    }
    while ( $carddata->{reseller} && $maxlength > 1 && $callstatus == 1 ) {
        $ASTPP->debug( debug => "FINDING LIMIT FOR: $carddata->{reseller}" );
        $carddata = &get_account( $astpp_db, $carddata->{reseller} );
        push @reseller_list, $carddata->{number};
        $ASTPP->debug( debug =>
"ADDING $carddata->{number} to the list of resellers for this account"
        );
        my ( $resellercallstatus, $resellermaxlength ) =
          &max_length( $astpp_db, $config, $carddata,
            $params->{'Caller-Destination-Number'} );
        my $routeinfo = &get_route(
            $astpp_db, $config,
            $params->{'Caller-Destination-Number'},
            $carddata->{pricelist}, $carddata
        );
        if ( $resellercallstatus != 1 ) {
            $carddata->{reseller} = "";
            $callstatus = $resellercallstatus;
        }
        elsif ( $resellermaxlength < $maxlength ) {
            $maxlength = $resellermaxlength;
        }
        $ASTPP->debug( debug =>
"Reseller cost = $routeinfo->{cost} and minimum charge is $minimumcharge"
        );
        if ( $resellermaxlength < 1 || $routeinfo->{cost} > $minimumcharge ) {
            $ASTPP->debug( debug =>
                  "Reseller call is priced too cheap!  Call being barred!" );
            $xml .=
"<action application=\"reject\" data=\"Reseller call is priced too cheap!  Call being barred!\"/>\n";
            $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
            print $xml;
            exit(0);
        }
        $ASTPP->debug( debug => "RESELLER Max Length: $resellermaxlength" );
        $ASTPP->debug( debug => "RESELLER Call Status: $resellercallstatus" );
    }

    if ( $config->{debug} == 1 ) {
        $ASTPP->debug( debug => "PRINTING LIST OF RESELLERS FOR THIS ACCOUNT" );
        foreach my $reseller (@reseller_list) {
            $ASTPP->debugb( debug => "RESELLER: $reseller" );
        }
    }

    $ASTPP->debug("Max Call Length: $maxlength minutes");
    $ASTPP->debug("Call Status: $callstatus");

    if ( $maxlength <= 1 ) {
        $ASTPP->debug( debug => "NOT ENOUGH CREDIT" );
        $xml .= "<action application=\"reject\" data=\"NOT ENOUGH CREDIT\"/>\n";
        $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
        print $xml;
        exit(0);
    } elsif ($config->{call_max_length} && $maxlength < $config->{call_max_length} / 1000){
	$maxlength = $config->{call_max_length} / 1000;
    }

    $xml = $ASTPP->fs_dialplan_xml_timelimit(
        xml        => $xml,
        max_length => $maxlength,
	accountcode => $carddata->{number}
    );

# Set the timelimit as well as other variables which are needed in the dialplan.
    my $timelimit =
      "L(" . sprintf( "%.0f", $maxlength * 60 * 1000 ) . ":60000:30000)";

    $ASTPP->debug( debug => "Looking for outbound Route" );
    my $routeinfo = &get_route(
        $astpp_db, $config,
        $params->{'Caller-Destination-Number'},
        $carddata->{pricelist}, $carddata, $astppdid
    );

    if ($didinfo->{number} ) {
	$ASTPP->debug( debug => "THIS IS A DID CALL: $xml");
        my ($returned_data) = $ASTPP->fs_dialplan_xml_did(
		did		   => $params->{'Caller-Destination-Number'}
       );
	$xml .= $returned_data;
        $xml .= "</condition>\n";
        $xml .= "</extension>\n";
        $xml .= "</context>\n";
	$xml .= "<context name=\"default\">\n";
        $xml .= "<extension name=\"" . $params->{'Caller-Destination-Number'} . "\">\n";
        $xml .= "<condition field=\"destination_number\" expression=\"" . $params->{'Caller-Destination-Number'} . "\">\n";
	$xml .= $returned_data;

	} else {
		# Get the list of routes for the phone number.
		my @outboundroutes = &get_outbound_routes( $astpp_db, $params->{'Caller-Destination-Number'},
			$carddata, $routeinfo, @reseller_list );
	      	$xml .= $ASTPP->fs_dialplan_xml_bridge_start() if @outboundroutes;
		my $count = 0;
		foreach my $route (@outboundroutes) {
			$ASTPP->debug( debug => "$route->{trunk}: cost $route->{cost}\t $route->{pattern}" );
			if ( $route->{cost} > $routeinfo->{cost} ) {
	       			$ASTPP->debug( debug => "$route->{trunk}: $route->{cost} > $routeinfo->{cost}, skipping" );
	       		}
	       		else {
				$xml .= $ASTPP->fs_dialplan_xml_bridge(
			                destination_number => $params->{'Caller-Destination-Number'},
	       			        route_prepend      => $route->{prepend},
	               			trunk_name         => $route->{trunk},
	               			route_id	   => $route->{id},
					count		   => $count
	       			);
			}
			$count++;
	    	}
	        $xml .= $ASTPP->fs_dialplan_xml_bridge_end() if @outboundroutes;
	}
	$xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	$ASTPP->debug( debug => $xml );
	print $xml;
}
elsif ( $params->{section} eq "directory" ) {

    #hostname darren-laptop
    #section directory
    #tag_name domain
    #key_name name
    #key_value 192.168.2.119
    #action sip_auth
    #sip_profile internal
    #sip_user_agent Zoiper rev.1118
    #sip_auth_username 1000
    #sip_auth_realm 192.168.2.119
    #sip_auth_nonce 83005e62-7e13-11dd-9eb1-25560b0691a8
    #sip_auth_uri sip:192.168.2.119;transport=UDP
    #sip_auth_qop auth
    #sip_auth_cnonce a79169d2656f292a
    #sip_auth_nc 00000001
    #sip_auth_response 4475154556879ec2017978f1347192a6
    #sip_auth_method REGISTER
    #key id
    #user 1000
    #domain 192.168.2.119
    #ip 192.168.2.119

    my $user_count = 0;
    if ($params->{'user'}) {
       $xml = $ASTPP->fs_directory_xml_header( xml => $xml );
       ($xml,$user_count) = $ASTPP->fs_directory_xml(
           xml    => $xml,
           ip     => $params->{'ip'},
           user   => $params->{'user'},
           domain => $params->{'domain'}
       );
       $xml = $ASTPP->fs_directory_xml_footer( xml => $xml );
    }
    
    $ASTPP->debug( debug => "User Count: " . $user_count );
    if ($user_count > 0) {
    	$ASTPP->debug( debug => $xml );
    	print $xml;
	exit(0);
    } else {
    	$ASTPP->debug( debug => $void_xml );
	print $void_xml;
	exit(0);
    }
} elsif ( $params->{cdr} ) { # PROCESS CDRs.
print header( -type => 'text/plain' );
    
# create object
my $xml = new XML::Simple;

# read XML file
my $data = $xml->XMLin($params->{cdr});

# print output
print STDERR Dumper($data);


my $tmp = "INSERT INTO " . $config->{freeswitch_cdr_table} . "(accountcode,src,dst,dcontext,clid,channel,dstchannel,lastapp,"
	. "lastdata,calldate,answerdate,enddate,duration,billsec,disposition,amaflags,uniqueid,originator,userfield,read_codec,"
	. "write_codec,cost,vendor,provider,trunk,outbound_route,progressmsec,answermsec,progress_mediamsec) VALUES ("
	. "'"
	. uri_unescape($data->{variables}->{accountcode})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{callflow}->{caller_profile}->{username})
	. "'"
	. ","
#	. $cdr_db->quote($data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{destination_number})
	. "'"
	. uri_unescape($data->{callflow}->{caller_profile}->{destination_number})
	. "'"
	. ","
#	. $cdr_db->quote($data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{context})
	. "'"
	. uri_unescape($data->{callflow}->{caller_profile}->{context})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{caller_id})
	. "'"
#	. "\"" . $cdr_db->quote($data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{caller_id_name}) . "\""
#	. "<" . $cdr_db->quote($data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{caller_id_number}) . ">"
#	. "\"" . $data->{callflow}->{caller_profile}->{caller_id_name} . "\""
#	. "<" . $data->{callflow}->{caller_profile}->{caller_id_number} . ">"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{channel_name})
	. "'"
	. ","
	. "''"
	. ","
	. "'" . uri_unescape($data->{variables}->{last_app}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{last_arg}) . "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{start_stamp})
	. "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{answer_stamp}) . "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{end_stamp})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{duration})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{billsec})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{hangup_cause})
	. "'"
	. ","
	. "''"
	. ","
	. "'"
	. uri_unescape($data->{callflow}->{caller_profile}->{uuid})
	. "'"
	. ","
	. "'"
	. uri_unescape($data->{variables}->{originator})
	. "'"
	. ","
	. "''"
	. ","
	. "'" . uri_unescape($data->{variables}->{read_code}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{write_code}) . "'"
	. ",'none','none'"
	. ","
	. "'" . uri_unescape($data->{variables}->{provider}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{trunk}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{outbound_route}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{progressmsec}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{answermsec}) . "'"
	. ","
	. "'" . uri_unescape($data->{variables}->{progress_mediamsec}) . "'"
	. ")";

print STDERR "\n" . $tmp . "\n";
$cdr_db->do($tmp);
print "Wrote CDR";
my (@chargelist);
push @chargelist, $data->{callflow}->{caller_profile}->{uuid};
&processlist( $astpp_db, $cdr_db, $config, \@chargelist );
print STDERR "VENDOR CHARGES: " . $config->{trackvendorcharges} . "\n";
&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, "none",  $data->{callflow}->{caller_profile}->{uuid},"" ) if $config->{trackvendorcharges} == 1;

&process_callingcard_cdr if $data->{variables}->{callingcard};    
	
sub process_callingcard_cdr() {
	my ( $cardinfo, $brandinfo, $numberinfo, $pricelistinfo,$cc );
	my $destination = uri_unescape($data->{callflow}->{caller_profile}->{destination_number});
	$destination =~ s/@.*//g;
        my $cardnumber = uri_unescape($data->{variables}->{callingcard});
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

                    print STDERR "THIS IS A CALLINGCARD CALL! \n";
                    print STDERR "CARD: $cardinfo->{cardnumber} \n";
                    print STDERR "CARD: $cardnumber \n";
                    $numberinfo = &get_route(
                        $astpp_db, $config,
                        $destination,
                        $brandinfo->{pricelist}, $cardinfo
                    );
                    if ( $data->{variables}->{billsec} > 0 )
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
"$numberinfo->{connectcost}, $numberinfo->{cost}, $data->{variables}->{billsec}, $increment, $numberinfo->{includedseconds}",
                            verbosity => $verbosity
                        );
                        my $charge = &calc_call_cost(
                            $numberinfo->{connectcost},
                            $numberinfo->{cost},
                            $data->{variables}->{billsec},
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
                                   $data->{variables}->{billsec} =>
                                  $cardinfo->{minute_fee_minutes};
                        }
                        if ( $cardinfo->{min_length_pennies} > 0
                            && ( $cardinfo->{min_length_minutes} * 60 ) >
                             $data->{variables}->{billsec} )
                        {
                            $charge =
                              ( ( $cardinfo->{min_length_pennies} * 100 ) +
                                  $charge );
                        }
			print STDERR "CARDNUMBER: " . $cardinfo->{cardnumber};
                        &write_callingcard_cdr(
                            $astpp_db,
                            $config,
                            $cardinfo,
                            uri_unescape($data->{variables}->{caller_id}),
                             $destination,
                            uri_unescape($data->{variables}->{hangup_cause}),
                            uri_unescape($data->{variables}->{start_stamp}),
                            $charge,
                             $data->{variables}->{billsec}
                        );
                        &callingcard_set_in_use($astpp_db,$cardinfo,0);
                        &callingcard_update_balance($astpp_db,$cardinfo,$charge);
            }
                }

    
}
exit(0);
