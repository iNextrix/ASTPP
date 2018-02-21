#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# Darren Wiebe <darren@aleph-com.net>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
# This is the user agent for ASTPP.  It can be posted in a
# location on the web where the public can access it.  I'm
# not sure on security details.
#
###########################################################################
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Getopt::Long;
use Locale::Country;
use Locale::Language;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;
use strict;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
my $copyright =
  gettext("ASTPP - Open Source Voip Billing &copy;2004 Aleph Communications");
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR gettext("Interface language is set to:") . " $ENV{LANGUAGE}\n";
bindtextdomain( "ASTPP", "/usr/local/share/locale" );
textdomain("ASTPP");
use vars qw(@output @modes $astpp_db $params $enh_config
  $status $config $limit $loginstat $cardinfo);
@output = ("STDERR");
@modes  = (
	gettext("Home"), gettext("Account"), gettext("Calling Cards"),
	gettext("ANI Mapping"), gettext("DIDs"),
	gettext("Logout")
);

sub login() {
	my ( $sql, $count, $record, $cookie, $cookie1, $tmp );
	$tmp =
	    "SELECT COUNT(*) FROM accounts WHERE number = "
	  . $astpp_db->quote( $params->{username} )
	  . " AND password = "
	  . $astpp_db->quote( $params->{password} )
	  . " AND status = 1";
	print STDERR $tmp;
	$sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$count  = $record->{"COUNT(*)"};
	$sql->finish;
	$cookie = cookie(
		-name    => 'ASTPP_User',
		-value   => $params->{username},
		-expires => '+1h'
	);
	$cookie1 = cookie(
		-name    => 'ASTPP_Password',
		-value   => $params->{password},
		-expires => '+1h'
	);

	if ( $count == 1 ) {
		$status = gettext("Successful Login!");
		print header( -cookie => [ $cookie, $cookie1 ] );
	}
	elsif ( $enh_config->{auth} eq $params->{password} ) {
		$status = gettext("Successful Login!");
		$count  = 1;
		print header( -cookie => [ $cookie, $cookie1 ] );
	}
	else {
		$params->{mode} = "";
		$status = gettext("Login Failed");
		print header();
	}
	print STDERR gettext("ASTPP-USER:") . $params->{username} . "\n";
	print STDERR gettext("ASTPP-PASS:") . $params->{password} . "\n";
	print STDERR gettext("ASTPP-USER-COUNT:") . " $count";
	return ( $params->{mode}, $count );
}

sub verify_login() {
	my ( $sql, $count, $record, $tmp );
	$params->{username} = cookie('ASTPP_User');
	$params->{password} = cookie('ASTPP_Password');
	$tmp                =
	    "SELECT COUNT(*) FROM accounts WHERE number = "
	  . $astpp_db->quote( $params->{username} )
	  . " AND password = "
	  . $astpp_db->quote( $params->{password} )
	  . " AND status = 1";
	print STDERR $tmp;
	$sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$count  = $record->{"COUNT(*)"};
	$sql->finish;

	if ( $enh_config->{auth} eq $params->{password} ) {
		$count = 1;
	}
	if ( $count != 1 ) {
		$params->{mode} = "";
		$status = gettext("Login Failed");
	}
	print STDERR gettext("ASTPP-USER:") . $params->{username} . "\n";
	print STDERR gettext("ASTPP-PASS:") . $params->{password} . "\n";
	print STDERR gettext("ASTPP-USER-COUNT:") . " $count\n ";
	if ($params->{mode} ne gettext("Download")) {
	print header();
	}
	return $count;
}

sub logout() {
	my $cookie = cookie(
		-name    => 'ASTPP_User',
		-value   => 'loggedout',
		-expires => 'now'
	);
	my $cookie1 = cookie(
		-name    => 'ASTPP_Password',
		-value   => 'loggedout',
		-expires => 'now'
	);
	print header( -cookie => [ $cookie, $cookie1 ] );
	$status = gettext("Successfully Logged User Out");
	return "";
}

sub build_menu_ts() {
	my ($selected) = @_;
	my ( $tmp, $body, $x );
	my $i = 0;
	foreach $tmp (@modes) {
		$body .=
"<div class=\"ts_ddm\" name=tt$i id=tt$i style=\"visibility:hidden;width:150;background-color:#efefef;\"onMouseover=\"clearhidemenu()\" onMouseout=\"dynamichide(event)\"><table width=100% border=0 cellspacing=0 cellpadding=0>";
		my $j = 0;
		$body .= "</table></div>";
		$i++;
	}
	$body .= "<table width=600 cellpadding=0 class=ts_menu><tr>";
	$i = 0;
	foreach $tmp (@modes) {
		$body .=
"<td name=t$i id=t$i><a href=\"?mode=$tmp\"  onmouseover='light_on(t$i);dropdownmenu(this, event,\"tt$i\");' onmouseout='light_off(t$i);delayhidemenu();'>$tmp</a></td>\n";
		$i++;
	}
	$body .= "</tr></table>";
	return $body;
}

sub build_callback() {
	my ( $body, $pstn, $voip );
	if ( $params->{action} eq gettext("Place Call") ) {

		#		my $out = new Asterisk::Outgoing;
		#		$out->setvariable( "Channel",    "$channel" );
		#		$out->setvariable( "MaxRetries", "0" );
		#		$out->setvariable( "context",    "$context" );
		#		$out->setvariable( "extension",  "$extension" );
		#		$out->setvariable( "CallerID",   "$outgoingclid $clidnumber" );
		#		$out->setvariable( "Account",    "$params->{username}" );
		#		$out->outtime( time() + 15 );
		#		$out->create_outgoing;
		#		$AGI->stream_file("callback-confirmed");
	}
	$voip = gettext("VOIP Route");
	$pstn = gettext("PSTN Route");
	$body =
	    "<table><tr><td> $status </td></tr><td><td>"
	  . gettext("Web Call")
	  . "</td></tr>";
	$body .= "<tr><td>"
	  . gettext("My Number:")
	  . "</td><td>"
	  . textfield( -name => 'origin', -size => '15' )
	  . "</td></tr>";
	$body .= "<tr><td>"
	  . gettext("Destination:")
	  . "</td><td>"
	  . textfield( -name => 'origin', -size => '15' )
	  . "</td></tr>";
	$body .= "<tr><td>"
	  . submit( -name => 'action', -value => gettext("Place Call") )
	  . "</td></tr></table>";
	return $body;
}

sub build_home() {
	my $tmp =
	  "<table width=80%><tr><td>"
	  . gettext(
		"Welcome to ASTPP.  Please select a function from the menu above.")
	  . "</td></tr></table>";
	return $tmp;
}

sub build_account_info() {
	my (
		$total,       $body,          $status, $description,
		$pricelist,   $tmp,           $number, $chargeid,
		$accountinfo, $balance,       $charge, $sweep,
		$cost,        $pagesrequired, $record, $debit,
		$credit,      $pageno
	);
	return gettext("Cannot view account until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $enh_config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;
	$body = start_form
	  . "<table class=\"default\"><tr class=\"header\"><td>"
	  . "</td></tr>"
          . "<tr class=\"header\"><td colspan=6><a href=\"astpp-users.cgi?"
      . "mode=Download\" target=\"_blank\">"
      . gettext("Download CDRs as CSV file (Right Click and select SAVE AS") . "</a></td></tr>";


	if ( $params->{action} eq gettext("Purchase DID") ) {
		$number = $params->{username};
		$tmp    =
		    "UPDATE dids SET account = "
		  . $astpp_db->quote($number)
		  . " WHERE number = "
		  . $astpp_db->quote( $params->{did_list} );
		$astpp_db->do($tmp);
		$params->{action} = gettext("Information...");
	}
	if ( $params->{action} eq gettext("Information...") ) {
		$accountinfo = &get_account( $astpp_db,    $params->{username} );
		$balance     = &accountbalance( $astpp_db, $accountinfo->{number} );
		$balance     = $balance / 10000;
		$balance     = sprintf( "%.2f",            $balance );
		if ( $accountinfo->{number} ) {
			$status .=
			    "<table><tr><td colspan=2>"
			  . hidden( -name => 'mode', -value => gettext("Account") )
			  . $accountinfo->{first_name} . " "
			  . $accountinfo->{middle_name} . " "
			  . $accountinfo->{last_name}
			  . "</td></tr><tr><td width=400>"
			  . $accountinfo->{company_name}
			  . "</td><td>Phone: "
			  . $accountinfo->{telephone_1}
			  . "</td></tr><tr><td>"
			  . $accountinfo->{address_1}
			  . "</td><td>Phone 2: "
			  . $accountinfo->{telephone_2}
			  . "</td></tr><tr><td>"
			  . $accountinfo->{address_2}
			  . "</td><td>Facsimile: "
			  . $accountinfo->{fascimilie}
			  . "</td></tr><tr><td>"
			  . $accountinfo->{address_3}
			  . "</td><td>Email: "
			  . $accountinfo->{email}
			  . "</td></tr><tr><td colspan=2>"
			  . $accountinfo->{city} . ", "
			  . $accountinfo->{postal_code} . ", "
			  . $accountinfo->{country}
			  . "</td></tr></table";
			$status .=
			    "Account&nbsp;&nbsp;</i><b>"
			  . $accountinfo->{'number'}
			  . "&nbsp;&nbsp;</b><i>"
			  . gettext("balance: ")
			  . "</i><b>$balance</b></i>\n";
			$status .=
			  gettext("with a credit limit of")
			  . " </i><b>\$$accountinfo->{'credit_limit'}</b></i>\n";
		}
		else {
			$status =
			    gettext("No such account number")
			  . " '$accountinfo->{number}' "
			  . gettext("found!") . "\n";
		}
		$body .=
		    "<table class=\"default\">"
		  . "<tr class=\"header\"><td>"
		  . gettext("Action")
		  . "</td><td>"
		  . gettext("Id")
		  . "</td><td>"
		  . gettext("Description")
		  . "</td><td>"
		  . gettext("Sweep")
		  . "</td><td>"
		  . gettext("Amount")
		  . "</td></tr>";
		my @account_charge_list =
		  &list_account_charges( $astpp_db, $accountinfo->{number} );
		my @pricelist_charge_list =
		  &list_pricelist_charges( $astpp_db, $accountinfo->{pricelist} );
		foreach $charge (@account_charge_list) {
			$count++;
			print STDERR "CHARGE_ID $charge->{charge_id}\n";
			my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
			print STDERR "SWEEP: " . $chargeinfo->{sweep};
			if ( $chargeinfo->{sweep} == 0 ) {
				$sweep = gettext("daily");
			}
			elsif ( $chargeinfo->{sweep} == 1 ) {
				$sweep = gettext("weekly");
			}
			elsif ( $chargeinfo->{sweep} == 2 ) {
				$sweep = gettext("monthly");
			}
			elsif ( $chargeinfo->{sweep} == 3 ) {
				$sweep = gettext("quarterly");
			}
			elsif ( $chargeinfo->{sweep} == 4 ) {
				$sweep = gettext("semi-annually");
			}
			elsif ( $chargeinfo->{sweep} == 5 ) {
				$sweep = gettext("annually");
			}
			$cost = sprintf( "%.2f", $chargeinfo->{charge} / 10000 );
			if ( $count % 2 == 0 ) {
				$body .= "<tr class=\"rowtwo\">";
			}
			else {
				$body .= "<tr class=\"rowone\">";
			}
			$body .=
			    "<td></td><td>"
			  . $charge->{id}
			  . "</td><td>"
			  . $chargeinfo->{description}
			  . "</td><td>"
			  . $sweep
			  . "</td><td> \$"
			  . $cost
			  . "</td></tr>";
		}
		$count = 0;
		foreach $charge (@pricelist_charge_list) {
			$count++;
			my $chargeinfo = &get_charge( $astpp_db, $charge );
			if ( $chargeinfo->{sweep} == 0 ) {
				$chargeinfo->{sweep} = gettext("daily");
			}
			elsif ( $chargeinfo->{sweep} == 1 ) {
				$chargeinfo->{sweep} = gettext("weekly");
			}
			elsif ( $chargeinfo->{sweep} == 2 ) {
				$chargeinfo->{sweep} = gettext("monthly");
			}
			elsif ( $chargeinfo->{sweep} == 3 ) {
				$chargeinfo->{sweep} = gettext("quarterly");
			}
			elsif ( $chargeinfo->{sweep} == 4 ) {
				$chargeinfo->{sweep} = gettext("semi-annually");
			}
			elsif ( $chargeinfo->{sweep} == 5 ) {
				$chargeinfo->{sweep} = gettext("annually");
			}
			$cost = sprintf( "%.2f", $chargeinfo->{charge} / 10000 );
			if ( $count % 2 == 0 ) {
				$body .= "<tr class=\"rowtwo\">";
			}
			else {
				$body .= "<tr class=\"rowone\">";
			}
			$body .= "<td>"
			  . "</td><td>"
			  . $charge
			  . "</td><td>"
			  . $chargeinfo->{description}
			  . "</td><td>"
			  . $chargeinfo->{sweep}
			  . "</td><td> \$"
			  . $cost
			  . "</td></tr>";
		}
		$body .=
		    "<tr bgcolor=ccccff><td colspan=5>"
		  . gettext("DIDs")
		  . "</td></tr>"
		  . "<tr bgcolor=ccccff><td>"
		  . gettext("Number")
		  . "</td><td>"
		  . gettext("Monthly Fee")
		  . "</td><td colspan=3></td></tr>";
		my @did_list =
		  &list_dids_account( $astpp_db, $accountinfo->{'number'} );
		foreach my $did_info (@did_list) {
			$cost = sprintf( "%.2f", $did_info->{monthlycost} / 10000 );
			$body .= "<tr><td>"
			  . $did_info->{number}
			  . "</td><td> \$"
			  . $cost
			  . "</td></tr>";
		}
		my @availabledids = &list_dids_number_account( $astpp_db, "" );
		$body .= "<tr><td>" . gettext("Order DID") . "</td></tr>" . "<tr><td>"
		  . popup_menu(
			-name   => "did_list",
			-values => \@availabledids
		  )
		  . "</td><td>"
		  . submit(
			-name  => "action",
			-value => gettext("Purchase DID")
		  )
		  . "</td></tr></table>";
		$body .= $status;
		$body .=
		    "<table class=\"default\">"
		  . "<tr class=\"header\"><td>"
		  . gettext("UniqueID")
		  . "</td><td>"
		  . gettext("Date & Time")
		  . "</td><td>"
		  . gettext("Caller*ID")
		  . "</td><td>"
		  . gettext("Called Number")
		  . "</td><td>"
		  . gettext("Trunk")
		  . "</td><td>"
		  . gettext("Disposition")
		  . "</td><td>"
		  . gettext("Billable Seconds")
		  . "</td><td>"
		  . gettext("Charge")
		  . "</td><td>"
		  . gettext("Credit")
		  . "</td><td>"
		  . gettext("Notes")
		  . "</td></tr>\n";
		my $tmp =
		    "SELECT * FROM cdrs WHERE cardnum ="
		  . $astpp_db->quote( $accountinfo->{'number'} )
		  . "and status IN (NULL, 0, 1)"
		  . " ORDER BY callstart DESC";
		my $sql = $astpp_db->prepare($tmp);
		$sql->execute;
		my $results = $sql->rows;
		$pagesrequired = ceil( $results / $results_per_page );
		$tmp           =
		    "SELECT * FROM cdrs WHERE cardnum ="
		  . $astpp_db->quote( $accountinfo->{'number'} )
		  . "and status IN (NULL, 0, 1)"
		  . " ORDER BY callstart DESC "
		  . " limit $params->{limit} , $results_per_page";
		my $sql = $astpp_db->prepare($tmp);
		$sql->execute;
		$count = 0;

		while ( $record = $sql->fetchrow_hashref ) {
			$count++;
			$record->{callerid} = gettext("unknown") unless $record->{callerid};
			$record->{uniqueid} = gettext("N/A")     unless $record->{uniqueid};
			$record->{disposition} = gettext("N/A")
			  unless $record->{disposition};
			$record->{notes}       = "" unless $record->{notes};
			$record->{callstart}   = "" unless $record->{callstart};
			$record->{callednum}   = "" unless $record->{callednum};
			$record->{billseconds} = "" unless $record->{billseconds};
			if ( $record->{debit} ) {
				$debit = $record->{debit} / 10000;
				$debit = sprintf( "%.6f", $debit );
			}
			else {
				$debit = "-";
			}
			if ( $record->{credit} ) {
				$credit = $record->{credit} / 10000;
				$credit = sprintf( "%.6f", $credit );
			}
			else {
				$credit = "-";
			}
			if ( $count % 2 == 0 ) {
				$body .= "<tr class=\"rowtwo\">";
			}
			else {
				$body .= "<tr class=\"rowone\">";
			}
			$body .=
			    "<td>$record->{uniqueid}</td><td>$record->{callstart}</td><td>"
			  . "$record->{callerid}</td><td>$record->{callednum}</td><td>"
			  . "N/A</td><td>$record->{disposition}</td><td>$record->{billseconds}</td><td>"
			  . "$debit</td><td>$credit</td><td>$record->{notes}</td></tr>";
		}
	}
	$body .= "</table>";
	for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
		if ( $i == 0 ) {
			if ( $params->{limit} != 0 ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("Account")
				  . "&action="
				  . gettext("Information...")
				  . "&accountnum="
				  . $accountinfo->{number}
				  . "&limit=0\">";
				$body .= $i + 1;
				$body .= "</a>";
			}
			else {
				$body .= $i + 1;
			}
		}
		if ( $i > 0 ) {
			if ( $params->{limit} != ( $i * $results_per_page ) ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("Account")
				  . "&action="
				  . gettext("Information...")
				  . "&accountnum="
				  . $accountinfo->{number}
				  . "&limit=";
				$body .= ( $i * $results_per_page );
				$body .= "\">\n";
				$body .= $i + 1, "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
	}
	$body .= "";
	$body .= "Page $pageno of $pagesrequired";
	return $body;
}

sub build_list_cards() {
	my (
		@pricelist, $status,   $body,  $number, $inuse,
		$cardstat,  $cardinfo, $count, $sql,    $value,
		$used,      $pageno,   $pagesrequired, $inuse);
	my $no       = gettext("NO");
	my $yes      = gettext("YES");
	my $active   = gettext("ACTIVE");
	my $inactive = gettext("INACTIVE");
	my $deleted  = gettext("DELETED");
	return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	$status = "&nbsp;";
	my $results_per_page = $enh_config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }

	if ( !$params->{action} ) {
		$params->{action} = gettext("Information...");
	}
	$body = start_form;
	if ( $params->{action} eq gettext("View Card") ) {
		my $no  = gettext("NO");
		my $yes = gettext("YES");
		return gettext("Database is NOT configured!") . "\n"
		  unless $astpp_db;
		$body .=
		  "<table class=\"default\"><tr class=\"header\"><td>"
		  . gettext("Account Number")
		  . "</td><td>"
		  . gettext("Sequence")
		  . "</td><td>"
		  . gettext("Card Number")
		  . "</td><td>"
		  . gettext("Pin")
		  . "</td><td>"
		  . gettext("Value")
		  . "</td><td>"
		  . gettext("Used")
		  . "</td><td>"
		  . gettext("Days Valid For")
		  . "</td><td>"
		  . gettext("Creation")
		  . "</td><td>"
		  . gettext("First Use")
		  . "</td><td>"
		  . gettext("Expiration")
		  . "</td><td>"
		  . gettext("In Use?")
		  . "</td></tr>"
		. "</td></tr>";
		$sql =
		  $astpp_db->prepare( "SELECT * FROM callingcards WHERE cardnumber = "
			  . $astpp_db->quote( $params->{number} )
			  . " AND account = "
			  . $astpp_db->quote( $params->{username} ) );
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";
		$cardinfo = $sql->fetchrow_hashref;
		$sql->finish;

		if ( $cardinfo->{inuse} == 0 ) {
			$inuse = $no;
		}
		elsif ( $cardinfo->{inuse} == 1 ) {
			$inuse = $yes;
		}
		$body .=
		    "<tr class=\"rowone\"><td>$cardinfo->{account}"
		  . "</td><td>$cardinfo->{id}"
		  . "</td><td>$cardinfo->{cardnumber}"
		  . "</td><td>$cardinfo->{pin}"
		  . "</td><td>"
		  . sprintf(
			"%." . $config->{decimalpoints} . "f",
			$cardinfo->{value} / 10000
		  )
		  . "</td><td>"
		  . sprintf(
			"%." . $config->{decimalpoints} . "f",
			$cardinfo->{used} / 10000
		  )
		  . "</td><td>$cardinfo->{validfordays}"
		  . "</td><td>$cardinfo->{created}"
		  . "</td><td>$cardinfo->{firstused}"
		  . "</td><td>$cardinfo->{expiry}"
		  . "</td><td>$inuse"
		  . "</td></tr></table>"
		  . "<table class=\"default\"><tr class=\"header\"><td>"
		  . gettext("Destination")
		  . "</td><td>"
		  . gettext("Disposition")
		  . "</td><td>"
		  . gettext("CallerID")
		  . "</td><td>"
		  . gettext("Starting Time")
		  . "</td><td>"
		  . gettext("Length in Seconds")
		  . "</td><td>"
		  . gettext("Cost")
		  . "</td></tr>";
		$sql =
		  $astpp_db->prepare(
			"SELECT * FROM callingcardcdrs WHERE cardnumber = "
			  . $astpp_db->quote( $cardinfo->{cardnumber} ) );
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";
		$count = 0;
		while ( my $cdrinfo = $sql->fetchrow_hashref ) {
			$count++;
			if ( $count % 2 == 0 ) {
				$body .= "<tr class=\"rowtwo\">";
			}
			else {
				$body .= "<tr class=\"rowone\">";
			}
			my $cost = $cdrinfo->{debit} / 10000;
			$body .=
			    "<td>$cdrinfo->{destination}"
			  . "</td><td>$cdrinfo->{disposition}"
			  . "</td><td>$cdrinfo->{clid}"
			  . "</td><td>$cdrinfo->{callstart}"
			  . "</td><td>$cdrinfo->{seconds}"
			  . "</td><td>$cost "
			  . "</td></tr>";
		}
	}
	if ( $params->{action} eq gettext("Information...") ) {
		$body .= "<table class=\"default\"><tr class=\"header\"><td>"
		  . gettext("Card Number")
		  . "</td><td>"
		  . gettext("Pin")
		  . "</td><td>"
		  . gettext("Pricelist")
		  . "</td><td>"
		  . gettext("Value") . ""
		  . "</td><td>"
		  . gettext("Used")
		  . "</td><td>"
		  . gettext("Days Valid For")
		  . "</td><td>"
		  . gettext("Creation")
		  . "</td><td>"
		  . gettext("First Use")
		  . "</td><td>"
		  . gettext("Expiration")
		  . "</td><td>"
		  . gettext("In Use?")
		  . "</td><td>"
		  . gettext("Status")
		  . "</td></tr>\n";
		$sql =
		  $astpp_db->prepare(
			    "SELECT cardnumber FROM callingcards WHERE account = "
			  . $astpp_db->quote( $params->{username} )
			  . " AND status < 2" );
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";
		my $results       = $sql->rows;
		my $pagesrequired = ceil( $results / $results_per_page );
		print gettext("Pages Required:") . " $pagesrequired\n"
		  if ( $config->{debug} eq "YES" );
		$sql->finish;
		$sql =
		  $astpp_db->prepare( "SELECT * FROM callingcards WHERE account = "
			  . $astpp_db->quote( $params->{username} )
			  . " AND status < 2 ORDER BY id limit $params->{limit} , $results_per_page"
		  );
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";

		while ( $cardinfo = $sql->fetchrow_hashref ) {
			$count++;
			if ( $cardinfo->{inuse} == 0 ) {
				$inuse = $no;
			}
			elsif ( $cardinfo->{inuse} == 1 ) {
				$inuse = $yes;
			}
			if ( $cardinfo->{status} == 0 ) {
				$cardstat = $inactive;
			}
			elsif ( $cardinfo->{status} == 1 ) {
				$cardstat = $active;
			}
			elsif ( $cardinfo->{status} == 2 ) {
				$cardstat = $deleted;
			}
			$value = $cardinfo->{value} / 10000;
			$value =
			  sprintf( "%." . $config->{decimalpoints} . "f", $value );
			$used = $cardinfo->{used} / 10000;
			$used = sprintf( "%." . $config->{decimalpoints} . "f", $used );
			if ( !( $count % 2 ) ) {
				$body .= "<tr class=\"rowone\">";
			}
			else {
				$body .= "<tr class=\"rowtwo\">";
			}
			$body .=
			    "</td><td><a href=\"astpp-users.cgi?mode="
			  . gettext("Calling Cards")
			  . "&number=$cardinfo->{cardnumber}&action="
			  . gettext("View Card")
			  . " \">$cardinfo->{cardnumber}</a>"
			  . "</td><td>$cardinfo->{pin}"
			  . "</td><td>$cardinfo->{brand}"
			  . "</td><td>$value "
			  . "</td><td>$used "
			  . "</td><td>$cardinfo->{validfordays}"
			  . "</td><td>$cardinfo->{created}"
			  . "</td><td>$cardinfo->{firstused}"
			  . "</td><td>$cardinfo->{expiry}";
			if ( $cardinfo->{inuse} == 1 ) {
				$body .= "</td><td>$yes";
			}
			else {
				$body .= "</td><td> $no ";
			}
			$body .= "</td><td>$cardstat" . "</td></tr>\n";
		}
		$body .= "</table>";
		for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
			if ( $i == 0 ) {
				if ( $params->{limit} != 0 ) {
					$body .=
					    "<a href=\"astpp-users.cgi?mode="
					  . gettext("List Cards")
					  . "&limit=0\">";
					$body .= $i + 1;
					$body .= "</a>";
				}
				else {
					$body .= $i + 1;
				}
			}
			if ( $i > 0 ) {
				if ( $params->{limit} != ( $i * $results_per_page ) ) {
					$body .=
					    "<a href=\"astpp-users.cgi?mode="
					  . gettext("List Cards")
					  . "&limit=";
					$body .= ( $i * $results_per_page );
					$body .= "\">\n";
					$body .= $i + 1, "</a>";
				}
				else {
					$pageno = $i + 1;
					$body .= " |";
				}
			}
		}
		$body .= "";
		$body .=
		  gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
	}
	$body .= "<table><tr><td colspan = 4> $status </td></tr></table>";
	return $body;
}

sub build_dids() {
	my ( $total, $body, $status, $description, $pricelist, $pageno,
		$pagesrequired );
	return gettext("Cannot view DIDs until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $enh_config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;
	$body = start_form
	  . "<table class=\"default\"><tr class=\"header\"><td>"
	  . hidden( -name => 'mode', -value => gettext("DIDs") )
	  . "</td></tr>";

	if ( $params->{action} eq gettext("Purchase DID") ) {
		my $number = $params->{username};
		my $tmp    =
		    "UPDATE dids SET account = "
		  . $astpp_db->quote($number)
		  . " WHERE number = "
		  . $astpp_db->quote( $params->{did_list} );
		$astpp_db->do($tmp);
	}
	my $accountinfo = &get_account( $astpp_db, $params->{username} );
	my @availabledids = &list_dids_number_account( $astpp_db, "" );
	$body .= "<tr><td>" . gettext("Order DID") . "</td></tr>" . "<tr><td>"
	  . popup_menu(
		-name   => "did_list",
		-values => \@availabledids
	  )
	  . "</td><td>"
	  . submit(
		-name  => "action",
		-value => gettext("Purchase DID")
	  )
	  . "</td></tr></table>";
	$body .= $status;
	$body .=
	    "<table class=\"default\">"
	  . "<tr class=\"header\"><td>"
	  . gettext("Number")
	  . "</td><td>"
	  . gettext("Connect Fee")
	  . "</td><td>"
	  . gettext("Included Seconds")
	  . "</td><td>"
	  . gettext("Cost")
	  . "</td><td>"
	  . gettext("Monthly Fee")
	  . "</td><td>"
	  . gettext("Country")
	  . "</td><td>"
	  . gettext("Province/State")
	  . "</td><td>"
	  . gettext("City")
	  . "</td></tr>\n";
	my $tmp =
	    "SELECT * FROM dids WHERE account = "
	  . $astpp_db->quote( $accountinfo->{'number'} )
	  . "and status = '1' "
	  . " ORDER BY number";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	my $results = $sql->rows;
	$pagesrequired = ceil( $results / $results_per_page );
	$tmp           =
	    "SELECT * FROM dids WHERE account = "
	  . $astpp_db->quote( $accountinfo->{'number'} )
	  . "and status = '1' "
	  . " ORDER BY number"
	  . " limit $params->{limit} , $results_per_page";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$count = 0;

	while ( my $record = $sql->fetchrow_hashref ) {
		$count++;
		if ( $count % 2 == 0 ) {
			$body .= "<tr class=\"rowtwo\">";
		}
		else {
			$body .= "<tr class=\"rowone\">";
		}
		my $monthlycost = sprintf( "%.2f", $record->{monthlycost} / 10000 );
		$body .=
		    "<td>$record->{number}</td><td>$record->{connectcost}</td><td>"
		  . "$record->{includedseconds}</td><td>$record->{cost}</td><td>"
		  . "$monthlycost</td><td>$record->{country}</td><td>"
		  . "$record->{province}</td><td>$record->{city}</td></tr>";
	}
	$body .= "</table>";
	for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
		if ( $i == 0 ) {
			if ( $params->{limit} != 0 ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("DIDs")
				  . "&limit=0\">";
				$body .= $i + 1;
				$body .= "</a>";
			}
			else {
				$body .= $i + 1;
			}
		}
		if ( $i > 0 ) {
			if ( $params->{limit} != ( $i * $results_per_page ) ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("DIDs")
				  . "&limit=";
				$body .= ( $i * $results_per_page );
				$body .= "\">\n";
				$body .= $i + 1, "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
		$body .= "";
		$body .= "Page $pageno of $pagesrequired";
	}
	return $body;
}

sub build_refills() {
	my ($tmp, $body);
	return gettext("Not available until database is configured")
	  unless $astpp_db;
	my @cardlist = &list_callingcards_account($astpp_db, $params->{username});
	my $accountinfo = &get_account($astpp_db, $params->{username});  
	$body = start_form
		. "<table class=\"default\"><tr class=\"header\">"
		. "<td>" . gettext("Refill Cards / Account") . "</td></tr>";
		  	
}

sub build_ani_map() {
	my ( $total, $body, $status, $description, $pricelist, $pageno,
		$pagesrequired );
	return gettext("Not available until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $enh_config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;
	$body = start_form
	  . "<table class=\"default\"><tr class=\"header\"><td>"
	  . hidden( -name => 'mode', -value => gettext("ANI Mapping") )
	  . "</td></tr>";

	if ( $params->{action} eq gettext("Map ANI") ) {
		my $tmp =
		    "INSERT INTO ani_map (number,account) VALUES ("
		  . $astpp_db->quote( $params->{ANI} ) . ", "
		  . $astpp_db->quote( $params->{username} ) . ")";
		if ( $astpp_db->do($tmp) ) {
			$status =
			    gettext("ANI") . " '"
			  . $params->{ANI} . "' "
			  . gettext("has been added!");
		}
		else {
			$status =
			    gettext("ANI") . " '"
			  . $params->{ANI} . "' "
			  . gettext("FAILED to create!");
		}
	}
	elsif ( $params->{action} eq gettext("Remove ANI") ) {
		my $tmp =
		    "DELETE FROM ani_map WHERE number = "
		  . $astpp_db->quote( $params->{ANI} )
		  . " AND account = "
		  . $astpp_db->quote( $params->{username} );
		if ( $astpp_db->do($tmp) ) {
			$status .=
			    gettext("ANI") . " '"
			  . $params->{ANI} . "' "
			  . gettext("has been dropped!");
		}
		else {
			$status .=
			    gettext("ANI") . " '"
			  . $params->{ANI} . "' "
			  . gettext("FAILED to remove!");
		}
	}
	my $accountinfo = &get_account( $astpp_db, $params->{username} );
	$body .=
	  "<tr><td>" . gettext("Map ANI to Account") . "</td></tr>" . "<tr><td>"
	  . textfield(
		-name  => "ANI",
		-width => 20
	  )
	  . "</td><td>"
	  . submit(
		-name  => "action",
		-value => gettext("Map ANI")
	  )
	  . "</td></tr></table>";
	$body .= $status;
	$body .=
	    "<table class=\"default\">"
	  . "<tr class=\"header\"><td>"
	  . gettext("Action")
	  . "</td><td>"
	  . gettext("ANI")
	  . "</td></tr>\n";
	my $tmp =
	    "SELECT number FROM ani_map WHERE account = "
	  . $astpp_db->quote( $params->{username} )
	  . " ORDER BY number";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	my $results = $sql->rows;
	$pagesrequired = ceil( $results / $results_per_page );
	$tmp           =
	    "SELECT number FROM ani_map WHERE account = "
	  . $astpp_db->quote( $params->{username} )
	  . " ORDER BY number"
	  . " limit $params->{limit} , $results_per_page";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$count = 0;

	while ( my $record = $sql->fetchrow_hashref ) {
		$count++;
		if ( $count % 2 == 0 ) {
			$body .= "<tr class=\"rowtwo\">";
		}
		else {
			$body .= "<tr class=\"rowone\">";
		}
		my $monthlycost = sprintf( "%.2f", $record->{monthlycost} / 10000 );
		$body .=
		    "<td><a href=\"astpp-users.cgi?mode="
		  . gettext("ANI Mapping")
		  . "&ANI=$record->{number}&action="
		  . gettext("Remove ANI") . " \">"
		  . gettext("Remove ANI") . "</a>"
		  . "<td>$record->{number}</td></tr>";
	}
	$body .= "</table>";
	for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
		if ( $i == 0 ) {
			if ( $params->{limit} != 0 ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("ANI Mapping")
				  . "&limit=0\">";
				$body .= $i + 1;
				$body .= "</a>";
			}
			else {
				$body .= $i + 1;
			}
		}
		if ( $i > 0 ) {
			if ( $params->{limit} != ( $i * $results_per_page ) ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("ANI Mapping")
				  . "&limit=";
				$body .= ( $i * $results_per_page );
				$body .= "\">\n";
				$body .= $i + 1, "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
		$body .= "";
		$body .= "Page $pageno of $pagesrequired";
	}
	return $body;
}

sub build_vendor_info() {
	my ( $total, $body, $status, $description, $pricelist, $pageno,
		$pagesrequired );
	return gettext("Not available until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $enh_config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;
	$body = start_form
	  . "<table class=\"default\">"
	  . "<tr class=\"header\"><td>"
	  . gettext("Action")
	  . "</td><td>"
	  . gettext("ANI")
	  . "</td></tr>\n";
	my $tmp =
	  "SELECT number FROM trunks WHERE provider = "
	  . $astpp_db->quote( $params->{username} );
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$count = 0;

	while ( my $record = $sql->fetchrow_hashref ) {
		$count++;
		if ( $count % 2 == 0 ) {
			$body .= "<tr class=\"rowtwo\">";
		}
		else {
			$body .= "<tr class=\"rowone\">";
		}
		my $monthlycost = sprintf( "%.2f", $record->{monthlycost} / 10000 );
		$body .=
		    "<td><a href=\"astpp-users.cgi?mode="
		  . gettext("ANI_Mapping")
		  . "&ANI=$record->{number}&action="
		  . gettext("Remove ANI") . " \">"
		  . gettext("Remove ANI") . "</a>"
		  . "<td>$record->{number}</td></tr>";
	}
	$body .= "</table>";
	for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
		if ( $i == 0 ) {
			if ( $params->{limit} != 0 ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("ANI_Mapping")
				  . "&limit=0\">";
				$body .= $i + 1;
				$body .= "</a>";
			}
			else {
				$body .= $i + 1;
			}
		}
		if ( $i > 0 ) {
			if ( $params->{limit} != ( $i * $results_per_page ) ) {
				$body .=
				    "<a href=\"astpp-users.cgi?mode="
				  . gettext("ANI_Mapping")
				  . "&limit=";
				$body .= ( $i * $results_per_page );
				$body .= "\">\n";
				$body .= $i + 1, "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
		$body .= "";
		$body .= "Page $pageno of $pagesrequired";
	}
	return $body;
}

sub build_body() {
	return &build_home()         if $params->{mode} eq gettext("Home");
	return &build_account_info() if $params->{mode} eq gettext("Account");
	return &build_list_cards()   if $params->{mode} eq gettext("Calling Cards");
	return &build_view_card()    if $params->{mode} eq gettext("View Card");
	return &build_dids()         if $params->{mode} eq gettext("DIDs");
	return &build_callback()     if $params->{mode} eq gettext("Callback");
	return &build_ani_map()      if $params->{mode} eq gettext("ANI Mapping");
	return &build_refills()      if $params->{mode} eq gettext("Refills");
	return &build_vendor_info()
	  if $params->{mode} eq gettext("Vendor Info") && $cardinfo->{type} == 3;
	return gettext("Not Available!");
}
#######################Program Starts Here####################3333
foreach my $param ( param() ) {
	$params->{$param} = param($param);
	print STDERR "$param $params->{$param}\n";
}
$limit      = $params->{limit};
$config     = &load_config();
$enh_config = &load_config_enh();
$astpp_db   = &connect_db( $config, $enh_config, @output );
$config     = &load_config_db($astpp_db,$config);
if (   ( $params->{mode} eq gettext("Login") )
	|| ( $params->{mode} eq gettext("Logout") ) )
{
	( $params->{mode}, $loginstat ) = &login
	  if $params->{mode} eq gettext("Login");
	$params->{mode} = &logout if $params->{mode} eq gettext("Logout");
}
else {
	$loginstat = &verify_login();
}
$cardinfo = &get_account( $astpp_db, $params->{username} );
if ( $cardinfo->{type} == 3 ) {
	push @modes, gettext("Vendor Info");
}
$params->{accountnum} = $params->{username};
$ENV{LANGUAGE} = $cardinfo->{language};
print STDERR gettext("Interface language is set to:") . " $ENV{LANGUAGE}\n";
if ( $loginstat == 1 && $params->{mode} ne gettext("Download")) {
	$params->{mode} = gettext("Home")
	  unless grep( /^$params->{mode}$/, @modes );
	my $body = &build_body( $params->{mode} );
	my $menu = &build_menu_ts( $params->{mode} );
	print "<title>ASTPP - "
	  . gettext("ASTPP - Billing User Info Sheet")
	  . "</title>\n"
	  . "<STYLE TYPE=\"text/css\">\n"
	  . "<!--\n"
	  . "  \@import url(/_astpp/style.css); \n" . "-->\n"
	  . "</STYLE>\n"
	  . "<BODY>\n"
	  . "<table width=100\%><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
	  . "<td align=center><H2>ASTPP - Open Source Voip Billing (www.astpp.org)</H2></td>"
	  . "</tr></table><table><tr><td width=100\%>"
	  . $menu
	  . "</td></tr>"
	  . "</td></tr></table>"
	  . $body . "<hr>"
	  . "<table align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr></table>"
	  . "</BODY>";
print end_html;
} elsif ($loginstat == 1 && $params->{mode} eq gettext("Download")) {
	my ($body);
        print header("text/csv");
 my $tmp =
                    "SELECT * FROM cdrs WHERE cardnum ="
                  . $astpp_db->quote( $params->{username} )
                  . "and status IN (NULL, 0, 1)";
        print STDERR $tmp;
        my $sql = $astpp_db->prepare($tmp);
    $sql->execute
          || return gettext("Something is wrong with the CDRs database!")
          . "\n";
while ( my $record = $sql->fetchrow_hashref ) {
	my ($credit,$debit);
                        $record->{callerid} = gettext("unknown") unless $record->{callerid};
                        $record->{uniqueid} = gettext("N/A")     unless $record->{uniqueid};
                        $record->{disposition} = gettext("N/A")
                          unless $record->{disposition};
                        $record->{notes}       = "" unless $record->{notes};
                        $record->{callstart}   = "" unless $record->{callstart};
                        $record->{callednum}   = "" unless $record->{callednum};
                        $record->{billseconds} = "" unless $record->{billseconds};
                        if ( $record->{debit} ) {
                                $debit = $record->{debit} / 10000;
                                $debit = sprintf( "%.6f", $debit );
                        }
                        else {
                                $debit = "0";
                        }
                        if ( $record->{credit} ) {
                                $credit = $record->{credit} / 10000;
                                $credit = sprintf( "%.6f", $credit );
                        }
                        else {
                                $credit = "0";
                        }
                        $body .= "\"" . $record->{uniqueid} . "\",\"" . $record->{callstart} . "\",\""
                          . $record->{callerid} . "\",\"" . $record->{callednum} . "\",\"" 
                          . "N/A" . "\",\"" . $record->{disposition} . "\",\"" . $record->{billseconds} . "\",\""
			  . $credit . "\",\"" . $debit;
        		$body .= "\"\n\r";
    }
    print $body;
}
else {
	print "<title>"
	  . gettext("ASTPP - Open Source Voip Billing Login")
	  . "</title>\n";
	print "<STYLE TYPE=\"text/css\">\n";
	print "<!--\n";
	print " \@import url(/_astpp/style.css); \n";
	print "-->\n";
	print "</STYLE>\n";
	print "<BODY>\n";
	print "<table><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
	  . "<td align=center><H2>"
	  . gettext("ASTPP - Open Source VOIP Billing Login")
	  . "</H2></td></tr></TABLE>\n";
	print "<table width=100\%><tr><td colspan=2 align=center>$status</td></tr>"
	  . "<tr><td colspan=2 align=center>"
	  . gettext("Please Login Now")
	  . "</td></tr>"
	  . startform;
	print "<tr><td width=50\% align=right>"
	  . gettext("Username:")
	  . "</td><td width=50\%>"
	  . textfield('username')
	  . "</td></tr>";
	print "<tr><td align=right width=50\%>"
	  . gettext("Password:")
	  . "</td><td width=50\%>"
	  . password_field('password')
	  . "</td></tr>";
	print "<tr><td colspan=2 align=center>"
	  . submit( -name => 'mode', -value => gettext("Login") )
	  . reset()
	  . "</td></tr>";
print end_html;
}
