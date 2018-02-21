#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
# Copyright (C) Freeswitch (Some of this code was borrowed from the freeswitch contrib directory
# The attribution of that needs to be cleaned up.)
#
# ASTPP Team (info@astpp.org)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
use POSIX;
use POSIX qw(strftime);
use POSIX ':signal_h';    # used for alarm to ensure we get heartbeats
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Getopt::Long;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;
use FreeSWITCH::Client;
use strict;
use ASTPP;
use lib './lib', '../lib';

require "/usr/local/astpp/astpp-common.pl";

$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
use vars qw($ASTPP $fs $config $astpp_db $osc_db $agile_db $cdr_db
  @output @cardlist $config $params $lastheartbeat);
@output = ( "STDOUT", "LOGFILE" );
my $verbosity = 1;
$ASTPP = ASTPP->new;
$ASTPP->set_verbosity($verbosity)
  ;                         #Tell ASTPP debugging how verbose we want to be.

sub initialize() {
    $config = &load_config();
    $astpp_db = &connect_db( $config, @output );
    $ASTPP->set_astpp_db($astpp_db);
    $config = &load_config_db( $astpp_db, $config ) if $astpp_db;
    $cdr_db = &cdr_connect_db( $config, @output );
    $fs = &fs_client_connect($config);
    open( LOGFILE, ">>$config->{log_file}" )
      || die "Error - could not open $config->{log_file} for writing\n";

    #    $ASTPP->debug("Rating calls for FreeSwitch", $verbosity);

}

sub fs_client_connect() {
    my ($config) = @_;
    my ($fs);
    print STDERR "Connecting to FreeSwitch\n";
    $fs = init FreeSWITCH::Client {
        -password => $config->{freeswitch_password},
        -host     => $config->{freeswitch_host},
        -port     => $config->{ffreeswitch_port}
    };
    if ($fs) {

# channel_create doesnt have the destination number so we wait for the codec event
        $fs->sendmsg(
            { 'command' => 'event plain heartbeat channel_hangup codec' } );
        $lastheartbeat = time;
        return $fs;
    }
    else {
        print STDERR "Unable to connect to FreeSwitch\n";
    }
}

###############################################
&initialize;
$config->{cdr_table} = $config->{freeswitch_cdr_table};
my $timeout = 1;

sigaction SIGALRM, new POSIX::SigAction sub {
    if ( $lastheartbeat < ( time - $config->{freeswitch_timeout} ) ) {
        print "Did not receive a heartbeat in the specified timeout\n";
        $fs->disconnect();
        undef $fs;
        $fs = &fs_client_connect($config);
    }

    # reset the alarm
    alarm $timeout;
} or die "Error setting SIGALRM handler: $!\n";

alarm $timeout;

while ( 1 > 0 ) {
    my ( $myhash, %uuids );
    if ( defined $fs ) {
        my $reply = $fs->readhash(undef);
        if ( $reply->{socketerror} ) {
            $fs = &fs_client_connect($config);
        }

        if ( $reply->{body} ) {
            $myhash = $reply->{event};

            if ( $myhash->{'event-name'} eq "HEARTBEAT" )
            {    ## Deal with heartbeats
                $lastheartbeat = time;
                print "Got a heartbeat\n";

            }
            elsif ( $myhash->{'event-name'} eq "CODEC" ) {    ## New call setup
                if ( !$uuids{ $myhash->{'unique-id'} } ) {
                    print $myhash->{'unique-id'} . " has called\n";
                    $uuids{ $myhash->{'unique-id'} } = time;
                    while ( my ( $key, $value ) = each(%$myhash) ) {
                        print "CODEC: " . $key . ":  " . $value . "\n";
                    }
                }

            }
#            elsif ( $myhash->{'event-name'} eq "CHANNEL_HANGUP" )
            elsif ( $myhash->{'event-name'} eq "CHANNEL_DESTROY" )
            {                                                 ## hangup event
                print "\n\n############################\n\n";
                print $myhash->{'unique-id'} . " has hung up\n";
                delete $uuids{ $myhash->{
                        'unique-id'} };    # we get a codec event after hangup

                while ( my ( $key, $value ) = each(%$myhash) ) {
                    print "HANGUP: " . $key . ":  " . $value . "\n";
                }

                #                if ( $myhash->{'variable_last_app'} ) {
                $myhash->{'variable_last_app'} = ""
                  if $myhash->{'variable_last_app'} eq "";
                $myhash->{'variable_last_arg'} = ""
                  if $myhash->{'variable_last_arg'} eq "";
                $myhash->{'variable_caller_id'} = "N/A"
                  if $myhash->{'variable_caller_id'} eq "";
                my $tmp =
"INSERT INTO `fscdr` (`accountcode`, `src`, `dst`, `dcontext`, `clid`,"
                  . "`channel`, `dstchannel`, `lastapp`, `lastdata`, `calldate`, `answerdate`,"
                  . "`enddate`, `duration`, `billsec`, `disposition`, `amaflags`, `uniqueid`,"
                  . "`userfield`, `read_codec`, `write_codec`, `cost`, `vendor`) VALUES ("
                  . $cdr_db->quote( $myhash->{'variable_accountcode'} ) . ","
                  . $cdr_db->quote( $myhash->{'caller-caller-id-number'} ) . ","
                  . $cdr_db->quote( $myhash->{'caller-destination-number'} ) . ","
                  . $cdr_db->quote( $myhash->{'caller-context'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_caller_id'} ) . ","
                  . $cdr_db->quote( $myhash->{'other-leg-channel-name'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_channel_name'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_last_app'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_last_arg'} ) . ","
                  . $cdr_db->quote( $myhash->{'event-date-local'} ) . ","
#                  . $cdr_db->quote( $myhash->{'variable_start_stamp'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_answer_stamp'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_end_stamp'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_duration'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_billsec'} ) . ","
                  . $cdr_db->quote( $myhash->{'hangup-cause'} ) . ",'',"
                  #		. $cdr_db->quote($myhash->{''}) . "," #amaflags
                  . $cdr_db->quote( $myhash->{'unique-id'} ) . ",'',"
                  #		. $cdr_db->quote($myhash->{''}) . "," #userfield
                  . $cdr_db->quote( $myhash->{'variable_read_codec'} ) . ","
                  . $cdr_db->quote( $myhash->{'variable_write_codec'} )
                  . ",'none','none')";
                print $tmp;
                $cdr_db->do($tmp);
                my (@chargelist);
                push @chargelist, $myhash->{'unique-id'};
                &processlist( $astpp_db, $cdr_db, $config,
                    \@chargelist );
                #                }
                if (   $myhash->{'variable_callingcards'}
                    && $myhash->{'variable_last_app'} eq "" )
                {
                    my ( $cardinfo, $brandinfo, $numberinfo, $pricelistinfo,$cc );
		    my $cardnumber = substr( $myhash->{'variable_accountcode'}, 3 );
        		$cardinfo = &get_callingcard( $astpp_db, $cardnumber, $config );
        		if ( !$cardinfo ) {
                $cardinfo = &get_account_cc( $astpp_db, $cardnumber );
                $cc = 1 if $cardinfo;
        }

# Getting this far means we have a valid card and pin.
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
                        $myhash->{'caller-destination-number'},
                        $brandinfo->{pricelist}, $cardinfo
                    );
                    if (   $myhash->{'hangup-cause'} =~ /ANSWER/
                        || $myhash->{'variable_billsec'} > 0 )
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
"$numberinfo->{connectcost}, $numberinfo->{cost}, $myhash->{'variable_billsec'}, $increment, $numberinfo->{includedseconds}",
                            verbosity => $verbosity
                        );
                        my $charge = &calc_call_cost(
                            $numberinfo->{connectcost},
                            $numberinfo->{cost},
                            $myhash->{'variable_billsec'},
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
                                  $myhash->{'variable_billsec'} =>
                                  $cardinfo->{minute_fee_minutes};
                        }
                        if ( $cardinfo->{min_length_pennies} > 0
                            && ( $cardinfo->{min_length_minutes} * 60 ) >
                            $myhash->{'variable_billsec'} )
                        {
                            $charge =
                              ( ( $cardinfo->{min_length_pennies} * 100 ) +
                                  $charge );
                        }

                        &write_callingcard_cdr(
                            $astpp_db,
                            $config,
                            $cardinfo,
                            $myhash->{'caller-caller-id-number'},
                            $myhash->{'caller-destination-number'},
                            $myhash->{'hangup-cause'},
                            $myhash->{'variable_start_stamp'},
                            $charge,
                            $myhash->{'variable_billsec'}
                        );
			&callingcard_set_in_use($astpp_db,$cardinfo,0);
			&callingcard_update_balance($astpp_db,$cardinfo,$charge);
                    }
                }
            }
            else {    ## Unknown event
                print "EVENT NAME: " . $myhash->{'event-name'} . "\n";
                print Dumper $myhash;

#         print "$reply->{body}\n"; # print out what was sent, myhash is translated by Client.pm
            }

        }
    }
}

