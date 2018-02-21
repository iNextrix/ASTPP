#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# Darren Wiebe (darren@aleph-com.net)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use LWP::Simple;
use URI::Escape;
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
use vars qw(@output $shucks $params $astpp_db $cdr_db $config
  $uniqueid $run_type $enh_config);
@output = ( "STDOUT", "LOGFILE" );
$shucks = 0;

sub preprocesslist() {
	my (@chargelist) = @_;
	foreach (@chargelist) {
		my $uniqueid = $_;
		my $cdrinfo  = &get_cdr( $cdr_db, $uniqueid );
		if (   $cdrinfo->{disposition} =~ /CONGESTION/
			|| $cdrinfo->{disposition} eq "CONGESTION" )
		{
			&save_ast_cdr( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1;
			&save_ast_cdr_vendor( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1
			  && $config->{trackvendorcharges} == 1;
			foreach my $handle (@output) {
				print $handle "\n----------------------\n";
				print $handle "CDR Written\n";
				print $handle
"uniqueid $uniqueid, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n";
				print $handle "disposition $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
		elsif ($cdrinfo->{disposition} =~ /BUSY/
			|| $cdrinfo->{disposition} eq "BUSY" )
		{
			&save_ast_cdr( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1;
			&save_ast_cdr_vendor( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1
			  && $config->{trackvendorcharges} == 1;
			foreach my $handle (@output) {
				print $handle "\n----------------------\n";
				print $handle "CDR Written\n";
				print $handle
"uniqueid $uniqueid, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n";
				print $handle "disposition $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
		elsif ( $cdrinfo->{disposition} eq "NO ANSWER" ) {
			&save_ast_cdr( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1;
			&save_ast_cdr_vendor( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1
			  && $config->{trackvendorcharges} == 1;
			foreach my $handle (@output) {
				print $handle "\n----------------------\n";
				print $handle "CDR Written\n";
				print $handle
"uniqueid $uniqueid, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n";
				print $handle "disposition $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
		elsif ($cdrinfo->{disposition} =~ /CANCEL/
			|| $cdrinfo->{disposition} eq "CANCEL" )
		{
			&save_ast_cdr( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1;
			&save_ast_cdr_vendor( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1
			  && $config->{trackvendorcharges} == 1;
			foreach my $handle (@output) {
				print $handle "\n----------------------\n";
				print $handle "CDR Written\n";
				print $handle
"uniqueid $uniqueid, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n";
				print $handle "disposition $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
		elsif ($cdrinfo->{disposition} =~ /FAILED/
			|| $cdrinfo->{disposition} eq "FAILED" )
		{
			&save_ast_cdr( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1;
			&save_ast_cdr_vendor( $cdr_db, $uniqueid, 0 )
			  if $enh_config->{astcdr} == 1
			  && $config->{trackvendorcharges} == 1;
			foreach my $handle (@output) {
				print $handle "\n----------------------\n";
				print $handle "CDR Written\n";
				print $handle
"uniqueid $uniqueid, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n";
				print $handle "disposition $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
	}
}

sub rating() {
	my ( $cdrinfo, $carddata ) = @_;
	my ( $increment, $numdata, $handle, $package, $notes, $status );
	foreach $handle (@output) {
		print $handle "----------------------\n";
		print $handle
"uniqueid: $cdrinfo->{uniqueid}, cardno: $carddata->{number}, phoneno: $cdrinfo->{dst}\n";
		print $handle
"disposition: $cdrinfo->{disposition} Pricelist: $carddata->{pricelist} reseller: $carddata->{reseller}\n";
	}
	if ( $cdrinfo->{disposition} =~ /^ANSWERED$/ ) {
		$numdata =
		  &get_route( $astpp_db, $config, $cdrinfo->{dst},
			$carddata->{pricelist}, $carddata );
		if ( !$numdata->{pattern} ) {    # !~ /\d+/ ) {
			&save_ast_cdr( $cdr_db, $cdrinfo->{uniqueid}, "error" )
			  if $enh_config->{astcdr} == 1;
			foreach $handle (@output) {
				print $handle "ERROR - ERROR - ERROR - ERROR - ERROR \n";
				print $handle "NO MATCHING PATTERN\n";
				print $handle "----------------------\n";
			}
		}
		else {
			my $branddata = &get_pricelist( $astpp_db, $carddata->{pricelist} );
			foreach $handle (@output) {
				print $handle
"pricelistData: $branddata->{name} $branddata->{markup} $branddata->{inc} $branddata->{status}\n";
			}
my $package = &get_package( $astpp_db, $carddata, $cdrinfo->{dst} );
                        if ($package->{name}) {
                                my $counter =
                                  &get_counter( $astpp_db, $package->{name},
                                        $carddata->{number} );
                                if ( !$counter->{id}) {
                                        my $tmp =
                                            "INSERT INTO counters (package,account) VALUES ("
                                          . $astpp_db->quote( $package->{name} ) . ", "
                                          . $astpp_db->quote( $carddata->{number} ) . ")";
                                        $astpp_db->do($tmp);
                                        $counter =
                                                  &get_counter( $astpp_db, $package->{name},
                                                $carddata->{number} );
                                }
                                if ( $package->{includedseconds} > $counter->{seconds}) {
                                        $cdrinfo->{billsec} = $cdrinfo->{billsec} - ($package->{includedseconds} - $counter
->{seconds});
                                        my $sql =
                                          "UPDATE counters SET seconds = "
                                          . $astpp_db->quote(
                                                $counter->{seconds} + $difference )
                                          . " WHERE id = "
                                          . $astpp_db->quote( $counter->{id} );
                                        $astpp_db->do($sql);
                                }
                        }

			if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
				$numdata->{connectcost} =
				  $numdata->{connectcost} *
				  ( ( $branddata->{markup} / 10000 ) + 1 );
				$numdata->{cost} =
				  $numdata->{cost} * ( ( $branddata->{markup} / 10000 ) + 1 );
			}
			if ( $numdata->{inc} > 0 ) {
				$increment = $numdata->{inc};
			}
			else {
				$increment = $branddata->{inc};
			}
			print STDERR
"$numdata->{connectcost}, $numdata->{cost}, $cdrinfo->{billsec}, $increment, $numdata->{includedseconds}";
			my $cost = &calc_call_cost(
				$numdata->{connectcost}, $numdata->{cost},
				$cdrinfo->{billsec},     $increment,
				$numdata->{includedseconds}
			);

			#$cost = $cost / 10000;
			$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
			foreach $handle (@output) {
				print $handle "Matching pattern is $numdata->{pattern}\n";
			}

   #Blocks all signals so that the program cannot be killed while writing costs.
			my $sigset = POSIX::SigSet->new;
			my $blockset = POSIX::SigSet->new( SIGINT, SIGQUIT, SIGCHLD );
			sigprocmask( SIG_BLOCK, $blockset, $sigset )
			  or die "Could not block INT,QUIT,CHLD signals: $!\n";
			&save_ast_cdr( $cdr_db, $cdrinfo->{uniqueid}, $cost )
			  if $enh_config->{astcdr} == 1;
			if ( $cdrinfo->{accountcode} ne $carddata->{number} && $cdrinfo->{accountcode} ne $carddata->{cc}) {
				$notes = $cdrinfo->{accountcode} . "|" . $numdata->{comment};
			}
			else {
				$notes = "|" . $numdata->{comment};
			}
			&post_cdr(
				$astpp_db,               $enh_config,
				$cdrinfo->{uniqueid},    $carddata->{number},
				$cdrinfo->{src},         $cdrinfo->{dst},
				$cdrinfo->{disposition}, $cdrinfo->{billsec},
				$cost,                   $cdrinfo->{calldate},
				"",                      $cdrinfo->{trunk},
				$notes
			  )
			  if $enh_config->{posttoastpp} == 1;
			&print_csv(
				$carddata->{number},   $cdrinfo->{disposition},
				$cdrinfo->{calldate},  $cdrinfo->{dst},
				$cdrinfo->{billsec},   $cost,
				$carddata->{reseller}, $cdrinfo
			);
			sigprocmask( SIG_SETMASK, $sigset ) # Restore the passing of signals
			  or die "Could not restore INT,QUIT,CHLD signals: $!\n";    #
			$status = 1;
		}
	}
	else {
		&save_ast_cdr( $cdr_db, $cdrinfo->{uniqueid}, "error" )
		  if $enh_config->{astcdr} == 1;
		foreach $handle (@output) {
			print $handle "ERROR - ERROR - ERROR - ERROR - ERROR \n";
			print $handle "DISPOSITION: $cdrinfo->{disposition} \n";
			print $handle "UNIQUEID: $cdrinfo->{uniqueid} \n";
			print $handle "----------------------\n\n";
		}
		$status = 0;
	}
	return $status;
}

sub catch_zap {
	my $signame = shift;
	our $shucks++;
	die "Somebody sent me a SIG$signame!";
}

sub vendor_not_billed() {
	my $tmp = "SELECT * FROM cdr WHERE vendor IN ('none','error')";
	my $sql = $cdr_db->prepare($tmp);
	$sql->execute;
	while ( my $cdr = $sql->fetchrow_hashref ) {
		print STDERR "----------------------\n";
		print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n";
		print STDERR gettext("Destination: ") . $cdr->{dst} . "\n";
		print STDERR gettext("Trunk: ") . $cdr->{dstchannel} . "\n";
		print STDERR gettext("Date/Time: ") . $cdr->{calldate} . "\n";
		print STDERR gettext("Disposition: ") . $cdr->{disposition} . "\n";
		print STDERR gettext("Source: ") . $cdr->{src} . "\n";
		print STDERR gettext("UniqueID: ") . $cdr->{uniqueid} . "\n";
		print STDERR gettext("Destination: ") . $cdr->{dst} . "\n";
		print STDERR "----------------------\n\n";
	}                
}

sub vendor_process_rating() {
	my ( $phrase, $uniqueid ) = @_;
	my $tmp = "SELECT * FROM trunks ORDER BY LENGTH(path)";
	my $sql = $astpp_db->prepare($tmp);
	print STDERR $tmp . "\n" if $config->{debug} == 1;
	$sql->execute;
	while ( my $trunk = $sql->fetchrow_hashref ) {
		my $tmp;
		if ( $uniqueid > 0 ) {
			$tmp =
			    "SELECT * FROM cdr where lastapp = 'Dial'"
			  . " AND vendor = "
			  . $cdr_db->quote($phrase)
			  . " AND (dstchannel LIKE '$trunk->{tech}/$trunk->{path}%'"
			  . " OR dstchannel LIKE '$trunk->{tech}\[$trunk->{path}\]%' )"
			  . " AND uniqueid = "
			  . $cdr_db->quote($uniqueid)
			  . " AND disposition = 'ANSWERED'";
		}
		else {
			$tmp =
			    "SELECT * FROM cdr where lastapp = 'Dial'"
			  . " AND vendor = "
			  . $cdr_db->quote($phrase)
			  . " AND (dstchannel LIKE '$trunk->{tech}/$trunk->{path}%'"
			  . " OR dstchannel LIKE '$trunk->{tech}\[$trunk->{path}\]%' )"
			  . " AND disposition = 'ANSWERED'";
		}
		print STDERR $tmp . "\n" if $config->{debug} == 1;
		my $sql1 = $cdr_db->prepare($tmp);
		$sql1->execute;
		while ( my $cdrinfo = $sql1->fetchrow_hashref ) {
			my $tmp =
			    "SELECT * FROM outbound_routes WHERE "
			  . $astpp_db->quote( $cdrinfo->{dst} )
			  . " RLIKE pattern AND status = 1 AND trunk = "
			  . $astpp_db->quote( $trunk->{name} )
			  . " ORDER by LENGTH(pattern) DESC, cost";
			my $sql2 = $astpp_db->prepare($tmp);
			$sql2->execute;
			print STDERR $tmp . "\n" if $config->{debug} == 1;
			my $pricerecord = $sql2->fetchrow_hashref;
			$sql2->finish;
			if ( $pricerecord->{id} ) {
				my $cost = &calc_call_cost(
					$pricerecord->{connectcost}, $pricerecord->{cost},
					$cdrinfo->{billsec},     $pricerecord->{inc},
					$pricerecord->{includedseconds}
				);
				&post_cdr(
					$astpp_db,               $enh_config,
					$cdrinfo->{uniqueid},    $trunk->{provider},
					$cdrinfo->{src},         $cdrinfo->{dst},
					$cdrinfo->{disposition}, $cdrinfo->{billsec},
					$cost * -1,              $cdrinfo->{calldate},
					"",                      $cdrinfo->{trunk},
					$pricerecord->{comment}
				  )
				  if $enh_config->{posttoastpp} == 1;
				&save_ast_cdr_vendor( $cdr_db, $cdrinfo->{uniqueid}, $cost );
			}
			else {
				&save_ast_cdr_vendor( $cdr_db, $cdrinfo->{uniqueid}, "error" );
			}

		}
	}
	$sql->finish;
}

sub processlist() {
	my (@chargelist) = @_;
	my ( $status, $handle );
	foreach (@chargelist) {
		my $uniqueid = $_;
		my $cdrinfo  = &get_cdr( $cdr_db, $uniqueid );
		if ( $cdrinfo->{accountcode} ) {
			my $carddata = &get_account( $astpp_db, $cdrinfo->{accountcode} );
			if ( $carddata ne "" ) {
				if ( $cdrinfo->{accountcode} ) {
					if ( $cdrinfo->{lastapp} eq "MeetMe" ) {    #
						$cdrinfo->{billsec} = $cdrinfo
						  ->{duration}; # There is an issue with calls that come out of meetme
					}    # not having the right billable seconds.
					$status = &rating( $cdrinfo, $carddata );
					if ( $status == 1 ) {
						while ( $carddata->{reseller} ) {
#							my $reseller = $carddata->{reseller};
							$carddata =
							  &get_account( $astpp_db, $carddata->{reseller} );
							&rating( $cdrinfo, $carddata );
						}
					}
				}
			}
			else {
				foreach $handle (@output) {
					print $handle "----------------------\n";
					print $handle "ERROR - ERROR - ERROR - ERROR - ERROR \n";
					print $handle "NO ACCOUNT EXISTS IN ASTPP\n";
					print $handle
"uniqueid: $uniqueid, Account: $cdrinfo->{accountcode}, phoneno: $cdrinfo->{dst}\n";
					print $handle "disposition: $cdrinfo->{disposition}\n";
					print $handle "----------------------\n";
				}
			}
		}
		else {
			foreach $handle (@output) {
				print $handle "----------------------\n";
				print $handle "ERROR - ERROR - ERROR - ERROR - ERROR \n";
				print $handle "NO ACCOUNTCODE IN DATABASE\n";
				print $handle
"uniqueid: $uniqueid, cardno: $cdrinfo->{accountcode}, phoneno: $cdrinfo->{dst}\n";
				print $handle "disposition: $cdrinfo->{disposition}\n";
				print $handle "----------------------\n";
			}
		}
	}
}

sub initialize() {
	$SIG{INT}   = \&catch_zap;
	$config     = &load_config();
	$enh_config = &load_config_enh();
	$astpp_db = &connect_db( $config,     $enh_config, @output );
	$config     = &load_config_db($astpp_db,$config);
	$cdr_db   = &cdr_connect_db( $config, $enh_config, @output );
	open( LOGFILE, ">>$config->{log_file}" )
	  || die "Log Error - could not open $config->{log_file} for writing\n";
}

sub print_csv {
	my ( $cardno, $disposition, $calldate, $dst, $billsec, $cost, $reseller,
		$cdrinfo )
	  = @_;
	my ( $handle, $outfile );
	foreach $handle (@output) {
		print $handle "Reseller: $reseller \n";
	}
	if ( $reseller eq "" ) {
		$outfile = $config->{rate_engine_csv_file};
	}
	else {
		$outfile = $config->{csv_dir} . $reseller . ".csv";
	}
	my $notes = "Notes: " . $cdrinfo->{accountcode};
	open( OUTFILE, ">>$outfile" )
	  || die "CSV Error - could not open $outfile for writing\n";
	print OUTFILE << "ending_print_tag";
$cardno,$cost,$cdrinfo->{disposition},$cdrinfo->{calldate},$cdrinfo->{dst},$billsec,$notes
ending_print_tag
	close(OUTFILE);
}

sub shutdown {
	close("LOGFILE");
}
################# Program Starts HERE #################################
$uniqueid = param('uniqueid');    #$ARGV[0];
$run_type = param('runtype');     # realtime,batch,price_only
&initialize();
if ( $uniqueid ne "" ) {
	my @chargelist = $uniqueid;
	sleep $enh_config->{sleep};
	&processlist(@chargelist);
}
elsif ( $run_type eq "price_only" ) {

	# If we are using this mode then we need the following parameters set:
	# dest          Number Dialed
	# pricelist     Pricelist to bill for
	# billseconds   Length of call in seconds
	#
	# It will return the cost of the call in 100ths of a penny.
	my ($increment);
	my $branddata = &get_pricelist( $astpp_db, param('pricelist') );
	my $numdata =
	  &get_route( $astpp_db, $config, param('dest'), param('pricelist') );
	if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
		$numdata->{connectcost} = $numdata->{connectcost} * ( ( $branddata->{markup} / 10000 ) + 1 );
		$numdata->{cost} = $numdata->{cost} * ( ( $branddata->{markup} / 10000 ) + 1 );
	}
	if ( $numdata->{inc} > 0 ) {
			$increment = $numdata->{inc};
	}
	else {
		$increment = $branddata->{inc};
	}  
	my $cost = &calc_call_cost( $numdata->{connectcost},
		$numdata->{cost}, param('billsec'), $increment,
		$numdata->{includedseconds} );
	print STDERR "\n\n" . $cost . "\n\n";
	exit(0);
}
elsif ( $run_type eq "realtime" ) {
	use POE;
	use POE::Component::Client::Asterisk::Manager;
	print STDERR gettext("Connecting to Asterisk");
	POE::Component::Client::Asterisk::Manager->new(
		Alias         => 'monitor',
		RemoteHost    => $config->{astman_host},
		RemotePort    => 5038,                                    # default port
		Username      => $config->{astman_user},
		Password      => $config->{astman_secret},
		CallBacks     => { Hangup => { 'Event' => 'Hangup' }, },
		inline_states => {
			Hangup => sub {
				my $input      = $_[ARG0];
				my @chargelist = $input->{Uniqueid};
				if ( !$astpp_db ) {
					$astpp_db = &connect_db( $config, $enh_config, @output );
				}
				if ( !$cdr_db ) {
					$cdr_db = &connect_db( $config, $enh_config, @output );
				}
				sleep $enh_config->{sleep};
				&processlist(@chargelist);
				&vendor_process_rating( "none", $input->{Uniqueid} )
				  if $config->{trackvendorcharges} == 1;
				print STDERR "$input->{Uniqueid} Just Hungup!\n";
			},
		},
	);
	$poe_kernel->run();
	&shutdown();
	exit(0);
}
else {

	# First we cleanup all calls that are not answered
	$cdr_db->do("UPDATE cdr SET cost = '0' WHERE disposition = 'NO ANSWER'");
	$cdr_db->do("UPDATE cdr SET vendor = '0' WHERE disposition = 'NO ANSWER'")
	  if $enh_config->{astcdr} == 1;
	$cdr_db->do("UPDATE cdr SET cost = '0' WHERE disposition = 'BUSY'");
	$cdr_db->do("UPDATE cdr SET vendor = '0' WHERE disposition = 'BUSY'")
	  if $enh_config->{astcdr} == 1;
	$cdr_db->do("UPDATE cdr SET cost = '0' WHERE disposition = 'FAILED'");
	$cdr_db->do("UPDATE cdr SET vendor = '0' WHERE disposition = 'FAILED'")
	  if $enh_config->{astcdr} == 1;
	$cdr_db->do("UPDATE cdr SET cost = '0' WHERE disposition = 'CONGESTION'");
	$cdr_db->do("UPDATE cdr SET vendor = '0' WHERE disposition = 'CONGESTION'")
	  if $enh_config->{astcdr} == 1;
	my @chargelist;
	@chargelist =
	  &list_cdrs_status( $cdr_db, "none" )
	  ;    # Grab a list of all calls with "none" assigned in the cost field
	&preprocesslist(@chargelist)
	  ;    # Any calls that were not answered, failed, etc get marked as billed.
	@chargelist =
	  &list_cdrs_status( $cdr_db, "none" )
	  ;    # Grab a list of all calls with "none" assigned in the cost field
	&processlist(@chargelist);    # Bill as many calls as we can.
	@chargelist =
	  &list_cdrs_status( $cdr_db, "error" )
	  ;    # Grab a list of all calls with "none" assigned in the cost field
	&processlist(@chargelist)
	  ;    # See if we can now bill some of the calls that are marked in "error"
	print STDERR gettext("START ON VENDOR CALL RATING!") . "\n"
	  if $config->{debug} == 1;
	&vendor_process_rating( "none", 0 )
	  if $config->{trackvendorcharges} == 1;
	&vendor_process_rating( "error", 0 )
	  if $config->{trackvendorcharges} == 1;
	print STDERR gettext("VENDOR CALLS WHICH HAVE NOT BEEN RATED.") . "\n"
		if $config->{debug} == 1;
	# Print a list of calls which have not been rated
	&vendor_not_billed	
	  if $config->{trackvendorcharges} == 1;

}
&shutdown();
exit(0);
