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
use HTML::Template;
use Time::HiRes qw( gettimeofday tv_interval ); 
use Data::Paginate;
use strict;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";

my $starttime = [gettimeofday]; 

my $copyright =
  gettext("ASTPP - Open Source Voip Billing &copy;2004 Aleph Communications");
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR gettext("Interface language is set to:") . " $ENV{LANGUAGE}\n";
bindtextdomain( "ASTPP", "/usr/local/share/locale" );
textdomain("ASTPP");
use vars qw(@output @modes $body $menu $astpp_db $params
  %sweeplist $status $config $limit $loginstat $cardinfo);
@output = ("STDERR");
@modes  = (
	gettext("Home"), gettext("Account"), gettext("Calling Cards"),
	gettext("ANI Mapping"), gettext("DIDs"),
	gettext("Logout"), gettext("Report")
);

my %sweeplist = (
    '0' => gettext("daily"),
    '1' => gettext("weekly"),
    '2' => gettext("monthly"),
    '3' => gettext("quarterly"),
    '4' => gettext("semi-annually"),
    '5' => gettext("annually")
);

sub login() {
	my ( $sql, $count, $record, $cookie, $cookie1, $tmp );
	$tmp =
	    "SELECT COUNT(*) FROM accounts WHERE number = "
	  . $astpp_db->quote( $params->{username} )
	  . " AND password = "
	  . $astpp_db->quote( $params->{password} )
	  . " AND status = 1";
	print STDERR $tmp if $config->{debug} == 1;
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
	elsif ( $config->{auth} eq $params->{password} ) {
		$status = gettext("Successful Login!");
		$count  = 1;
		print header( -cookie => [ $cookie, $cookie1 ] );
	}
	else {
		$params->{mode} = "";
		$status = gettext("Login Failed");
		print header();
	}
	print STDERR gettext("ASTPP-USER:") . $params->{username} . "\n" if $config->{debug} == 1;
	print STDERR gettext("ASTPP-PASS:") . $params->{password} . "\n" if $config->{debug} == 1;
	print STDERR gettext("ASTPP-USER-COUNT:") . " $count" if $config->{debug} == 1;
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
	print STDERR $tmp if $config->{debug} == 1;
	$sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$count  = $record->{"COUNT(*)"};
	$sql->finish;

	if ( $config->{auth} eq $params->{password} ) {
		$count = 1;
	}
	if ( $count != 1 ) {
		$params->{mode} = "";
		$status = gettext("Login Failed");
	}
	print STDERR gettext("ASTPP-USER:") . $params->{username} . "\n" if $config->{debug} == 1;
	print STDERR gettext("ASTPP-PASS:") . $params->{password} . "\n" if $config->{debug} == 1;
	print STDERR gettext("ASTPP-USER-COUNT:") . " $count\n " if $config->{debug} == 1;
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
		my $out = new Asterisk::Outgoing;
#		$out->setvariable( "Channel",    "$channel" );
#		$out->setvariable( "MaxRetries", "0" );
#		$out->setvariable( "context",    "$context" );
#		$out->setvariable( "extension",  "$extension" );
#		$out->setvariable( "CallerID",   "$outgoingclid $clidnumber" );
		$out->setvariable( "Account",    "$params->{username}" );
		$out->outtime( time() + 15 );
		$out->create_outgoing;
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
	  . textfield( -name => 'destination', -size => '15' )
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
		$credit,      $pageno,		$credit_limit
	);
	my $template = HTML::Template->new(filename => '/var/lib/astpp/templates/users-account-info.tpl', die_on_bad_params => $config->{template_die_on_bad_params});
	return gettext("Cannot view account until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	if ( $params->{action} eq gettext("Purchase DID") ) {
		$status .= &purchase_did($astpp_db,$config,$params->{did_list},$params->{username});
		$params->{action} = gettext("Information...");
	}
	 elsif ($params->{action} eq gettext("Remove...")) {
		$status .= &remove_did($astpp_db,$config,$params->{did},$params->{username});
        	$params->{action} = gettext("Information...");
	}
	if ( $params->{action} eq gettext("Information...") ) {
		my (@did_list,@cdr_list,@charge_list);
		$accountinfo = &get_account( $astpp_db,    $params->{username} );
		$balance     = &accountbalance( $astpp_db, $accountinfo->{number} );
		$balance     = $balance / 10000;
		$balance     = sprintf( "%." . $config->{decimalpoints} . "f", $balance );
		$template->param(account_name => $params->{username});
		$credit_limit	     	= $accountinfo->{credit_limit};
		$credit_limit           = $credit_limit / 10000;
		$credit_limit           = sprintf( "%." . $config->{decimalpoints} . "f", $credit_limit );
		$template->param(account_credit_limit => $credit_limit);
		$template->param(account_balance => $balance);
		$template->param(first_name => $accountinfo->{first_name});
		$template->param(middle_name => $accountinfo->{middle_name});
		$template->param(last_name => $accountinfo->{last_name});
		$template->param(company_name => $accountinfo->{company_name});
		$template->param(telephone_1 => $accountinfo->{telephone_1});
		$template->param(address_1 => $accountinfo->{address_1});
		$template->param(telephone_2 => $accountinfo->{telephone_2});
		$template->param(address_2 => $accountinfo->{address_2});
		$template->param(fascimilie => $accountinfo->{fascimilie});
		$template->param(address_3 => $accountinfo->{address_3});
		$template->param(email => $accountinfo->{email});
		$template->param(city => $accountinfo->{city});
		$template->param(postal_code => $accountinfo->{postal_code});
		$template->param(country => $accountinfo->{country});
		$template->param(province => $accountinfo->{province});
		my @account_charge_list =
		  &list_account_charges( $astpp_db, $accountinfo->{number} );
		my @pricelist_charge_list =
		  &list_pricelist_charges( $astpp_db, $accountinfo->{pricelist} );
		foreach $charge (@account_charge_list) {
			my (%row);
			print STDERR "CHARGE_ID $charge->{charge_id}\n" if $config->{debug} == 1;
			my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
			print STDERR "SWEEP: " . $chargeinfo->{sweep} if $config->{debug} == 1;
			$cost = sprintf( "%.2f", $chargeinfo->{charge} / 10000 );
			$row{sweep} = $sweeplist{$chargeinfo->{sweep}};
			$row{charge} = $cost;
			$row{id} = $charge->{id};
			$row{description} =  $chargeinfo->{description};
			push( @charge_list, \%row );
		}
		foreach $charge (@pricelist_charge_list) {
			my (%row);
			my $chargeinfo = &get_charge( $astpp_db, $charge );
			$cost = sprintf( "%.2f", $chargeinfo->{charge} / 10000 );
			$row{sweep} = $sweeplist{$chargeinfo->{sweep}};
			$row{charge} = $cost;
			$row{id} = $charge;
			$row{description} =  $chargeinfo->{description};
			push( @charge_list, \%row );
		}
		$template->param(charge_list => \@charge_list);
		my @dids =
		  &list_dids_account( $astpp_db, $accountinfo->{number} );
		foreach my $did_info (@dids) {
			$did_info = &get_did_reseller($astpp_db,$did_info->{number},$accountinfo->{reseller}); 
			my (%row);
			$cost = sprintf( "%.2f", $did_info->{monthlycost} / 10000 );
			$row{number} = $did_info->{number};
			$row{charge} = $cost;
			push( @did_list, \%row );
		}
		$template->param(did_list => \@did_list);
		my @availabledids = &list_available_dids( $astpp_db, $accountinfo->{number} );
		  my $order_dids = popup_menu(
			-name   => "did_list",
			-values => \@availabledids
		  );
		$template->param(order_dids => $order_dids);
		my $tmp =
		    "SELECT * FROM cdrs WHERE cardnum = "
		  . $astpp_db->quote( $accountinfo->{number} )
		  . " AND status IN (NULL, 0, 1)"
		  . " ORDER BY callstart DESC";
		print STDERR $tmp if $config->{debug} == 1;
		my $sql = $astpp_db->prepare($tmp);
		$sql->execute;
		my $results = $sql->rows;
		$pagesrequired = ceil( $results / $results_per_page );
		$tmp           =
		    "SELECT * FROM cdrs WHERE cardnum ="
		  . $astpp_db->quote( $accountinfo->{number} )
		  . "and status IN (NULL, 0, 1)"
		  . " ORDER BY callstart DESC "
		  . " limit $params->{limit} , $results_per_page";
		print STDERR $tmp if $config->{debug} == 1;
		$sql = $astpp_db->prepare($tmp);
		$sql->execute;
		while ( $record = $sql->fetchrow_hashref ) {
			my (%row);
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
			$row{uniqueid} = $record->{uniqueid};
			$row{callstart} = $record->{callstart};
			$row{callerid} = $record->{callerid};
			$row{callednum} = $record->{callednum};
			$row{disposition} = $record->{disposition};
			$row{billseconds} = $record->{billseconds};
			$row{debit} = $debit;
			$row{credit} = $credit;
			$row{notes} = $record->{notes};
			push( @cdr_list, \%row );
		}
		$template->param(cdr_list => \@cdr_list);
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
				$body .= ($i + 1) . "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
	}
	$body .= "";
	$body .= "Page $pageno of $pagesrequired";
	$template->param(pagination => $body);
	$template->param(status => $status);
	return $template->output;

}

sub build_list_cards() {
	my (
		@pricelist, $status,   $body,  $number, $inuse,
		$cardstat,  $cardinfo, $count, $sql,    $value,
		$used,      $pageno,   $pagesrequired);
	my (@cdr_list, @card_list);
	my $template = HTML::Template->new(filename => '/var/lib/astpp/templates/users-list-callingcards.tpl', die_on_bad_params => $config->{template_die_on_bad_params});
	my $no       = gettext("NO");
	my $yes      = gettext("YES");
	my $active   = gettext("ACTIVE");
	my $inactive = gettext("INACTIVE");
	my $deleted  = gettext("DELETED");
	return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	$status = "&nbsp;";
	my $results_per_page = $config->{results_per_page};
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
			my (%row);
			my $cost = $cdrinfo->{debit} / 10000;
			$row{destination} = $cdrinfo->{destination};
			$row{disposition} = $cdrinfo->{disposition};
			$row{callerid} = $cdrinfo->{clid};
			$row{callstart} = $cdrinfo->{callstart};
			$row{billseconds} = $cdrinfo->{seconds};
			$row{cost} = $cost;
			push( @cdr_list, \%row );
		}
		$template->param(cdr_list => \@cdr_list);
		$params->{action} eq gettext("Information...");
	}
	if ( $params->{action} eq gettext("Information...") ) {
		my $tmp = 
			    "SELECT cardnumber FROM callingcards WHERE account = "
			  . $astpp_db->quote( $params->{username} )
			  . " AND status < 2";
		print STDERR "$tmp \n" if $config->{debug} == 1;
		$sql =
		  $astpp_db->prepare($tmp);
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";
		my $results       = $sql->rows;
		my $pagesrequired = ceil( $results / $results_per_page );
		print gettext("Pages Required:") . " $pagesrequired\n"
		  if ( $config->{debug} eq "YES" );
		$sql->finish;
		print STDERR "$tmp \n" if $config->{debug} == 1;
		$tmp = "SELECT * FROM callingcards WHERE account = "
			  . $astpp_db->quote( $params->{username} )
			  . " AND status < 2 ORDER BY id limit $params->{limit} , $results_per_page";
		print STDERR "$tmp \n" if $config->{debug} == 1;
		$sql =
		  $astpp_db->prepare($tmp); 
		$sql->execute
		  || return gettext(
			"Something is wrong with the callingcards database!")
		  . "\n";
		while ( $cardinfo = $sql->fetchrow_hashref ) {
			print STDERR "PROCESSING CARD NUMBER :$cardinfo->{cardnumber}\n" if $config->{debug} == 1;
			my (%row);
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
			$row{number} = $cardinfo->{cardnumber};
			$row{pin} = $cardinfo->{pin};
			$row{brand} = $cardinfo->{brand};
			$row{value} = $value;
			$row{used} = $used;
			$row{validfordays} = $cardinfo->{validfordays};
			$row{created} = $cardinfo->{created};
			$row{firstused} = $cardinfo->{firstused};
			$row{expiry} = $cardinfo->{expiry};
			if ( $cardinfo->{inuse} == 1 ) {
				$row{inuse} = $yes;
			}
			else {
				$row{inuse} = $no;
			}
			$row{cardstat} = $cardstat;
			push( @card_list, \%row );
		}
		$template->param(card_list => \@card_list);

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
					$body .= ($i + 1) . "</a>";
				}
				else {
					$pageno = $i + 1;
					$body .= " |";
				}
			}
		}
		$body .=
		  gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
	}
	$template->param(pagination => $body);
	$template->param(status => $status);
	return $template->output;
}

sub build_dids() {
	my ( $total, $body, $status, $description, $pricelist, $pageno, $pagesrequired );
	my $template = HTML::Template->new(filename => '/var/lib/astpp/templates/users-dids.tpl', die_on_bad_params => $config->{template_die_on_bad_params});
	return gettext("Cannot view DIDs until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
		my $results_per_page = $config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;
	if ( $params->{action} eq gettext("Purchase DID") ) {
		$status .= &purchase_did($astpp_db,$config,$params->{did_list},$params->{username});
	} elsif ($params->{action} eq gettext("Edit...")) {
        my $didinfo = &get_did( $astpp_db, $params->{did} );
	my $accountinfo = &get_account($astpp_db,$params->{username});
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode',   -value => gettext("DIDs") )
          . hidden( -name => 'did', -value => $params->{number} )
          . gettext("Number")
          . "</td><td>"
          . gettext("Country")
          . "</td><td>"
          . gettext("Province")
          . "</td><td>"
          . gettext("City")
          . "</td><td>"
          . "<acronym title=\""
      . gettext("To dial a sip address set it to: SIP/ipaddress.  To dial a pstn number just enter the telephone number here.") . "\">"
      . gettext("Dialstring")
      . "</acronym>"
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr><td>" . $didinfo->{number} . "</td><td>"
. $didinfo->{country} . "</td><td>"
. $didinfo->{province} . "</td><td>"
. $didinfo->{city} . "</td><td>"
          . textfield(
            -name    => 'extensions',
            -size    => 20,
            -default => $didinfo->{extensions}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr></table>";
        return $body;
	} elsif ($params->{action} eq gettext("Save...")) {
		my $tmp    =
		    "UPDATE dids SET extensions = "
		  . $astpp_db->quote($params->{extensions})
		  . " WHERE number = "
		  . $astpp_db->quote( $params->{did} )
		  . " AND account = "
		  . $astpp_db->quote($params->{username});
		$astpp_db->do($tmp);
	} elsif ($params->{action} eq gettext("Remove...")) {
		$status .= &remove_did($astpp_db,$config,$params->{did},$params->{username});
        	$params->{action} = gettext("Information...");
	}
	my $accountinfo = &get_account( $astpp_db, $params->{username} );
	my @availabledids = &list_available_dids( $astpp_db, $params->{username} );
	$body = start_form
	  . "<table class=\"default\"><tr class=\"header\"><td>"
	  . hidden( -name => 'mode', -value => gettext("DIDs") )
	  . gettext("Order DID") . "</td></tr>" . "<tr><td>"
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
	  . "</td><td>"
	  . gettext("Extension to dial")
	  . "</td><td colspan=2>"
	  . gettext("Action")
	  . "</td></tr>\n";
	my $tmp =
	    "SELECT number FROM dids WHERE account = "
	  . $astpp_db->quote( $accountinfo->{'number'} )
	  . "and status = '1' "
	  . " ORDER BY number";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	my $results = $sql->rows;
	$pagesrequired = ceil( $results / $results_per_page );
	$tmp           =
	    "SELECT number FROM dids WHERE account = "
	  . $astpp_db->quote( $accountinfo->{'number'} )
	  . "and status = '1' "
	  . " ORDER BY number"
	  . " limit $params->{limit} , $results_per_page";
	$sql = $astpp_db->prepare($tmp);
	$sql->execute;
	$count = 0;

	while ( my $did = $sql->fetchrow_hashref ) {
		my $record = &get_did_reseller($astpp_db,$did->{number},$accountinfo->{reseller}); 
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
		  . "$record->{province}</td><td>$record->{city}</td><td>$record->{extensions}</td><td>"
		  . "<a href=\"astpp-users.cgi?mode="
		  . gettext("DIDs")
		  . "&limit=0&did=$record->{number}&action=" .  gettext("Edit...") . "\">" . gettext("Edit...") . "</a>"
		  . "</td><td>"
		  . "<a href=\"astpp-users.cgi?mode="
		  . gettext("DIDs")
		  . "&limit=0&did=$record->{number}&action=" .  gettext("Remove...") . "\">" . gettext("Remove...") . "</a>"
		  . "</tr>";
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
				$body .= ($i + 1) . "</a>";
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
	my ( @ani_list,$total, $body, $status, $description, $pricelist, $pageno,
		$pagesrequired );
	my $template = HTML::Template->new(filename => '/var/lib/astpp/templates/users-map-ani.tpl', die_on_bad_params => $config->{template_die_on_bad_params});
	return gettext("Not available until database is configured")
	  unless $astpp_db;
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
	my $results_per_page = $config->{results_per_page};
	if ( $results_per_page eq "" ) { $results_per_page = 25; }
	if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }
	my $count = 0;

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
	my $tmp           =
	    "SELECT number FROM ani_map WHERE account = "
	  . $astpp_db->quote( $params->{username} )
	  . " ORDER BY number";
	my $sql = $astpp_db->prepare($tmp);
	$sql->execute;
	while ( my $record = $sql->fetchrow_hashref ) {
		my (%row);
		$row{ani} = $record->{number};
		push( @ani_list, \%row );
	}
	$template->param(status => $status);
	$template->param(ani_list => \@ani_list);
	return $template->output;
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
	return gettext("Not Available!");
}
#######################Program Starts Here####################
foreach my $param ( param() ) {
	$params->{$param} = param($param);
	print STDERR "$param $params->{$param}\n";
}
$limit      = $params->{limit};
$config     = &load_config();
$astpp_db   = &connect_db( $config, @output );
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
$params->{accountnum} = $params->{username};
$ENV{LANGUAGE} = $cardinfo->{language};
print STDERR gettext("Interface language is set to:") . " $ENV{LANGUAGE}\n" if $config->{debug} == 1;

my $template = HTML::Template->new(filename => '/var/lib/astpp/templates/users-main.tpl', die_on_bad_params => $config->{template_die_on_bad_params});
my ($body, $menu);
if ( $loginstat == 1 && $params->{mode} ne gettext("Download")) {
	$params->{mode} = gettext("Home")
	  unless grep( /^$params->{mode}$/, @modes );
	$body = &build_body( $params->{mode} );
	$menu = &build_menu_ts( $params->{mode} );
} elsif ($loginstat == 1 && $params->{mode} eq gettext("Download")) {
        print header("text/csv");
 my $tmp =
                    "SELECT * FROM cdrs WHERE cardnum ="
                  . $astpp_db->quote( $params->{username} )
                  . "and status IN (NULL, 0, 1)";
        print STDERR $tmp if $config->{debug} == 1;
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
$body = "<table width=100\%><tr><td colspan=2 align=center>$status</td></tr>"
	  . "<tr><td colspan=2 align=center>"
	  . gettext("Please Login Now")
	  . "</td></tr>"
	  . startform . "<tr><td width=50\% align=right>"
	  . gettext("Username:")
	  . "</td><td width=50\%>"
	  . textfield('username')
	  . "</td></tr>" . "<tr><td align=right width=50\%>"
	  . gettext("Password:")
	  . "</td><td width=50\%>"
	  . password_field('password')
	  . "</td></tr>" . "<tr><td colspan=2 align=center>"
	  . submit( -name => 'mode', -value => gettext("Login") )
	  . reset()
	  . "</td></tr>";
}

$template->param(body => $body);
$template->param(menu => $menu);
$template->param(host => $ENV{SERVER_NAME});
$template->param(username => $params->{username});
$template->param(logintype => $params->{logintype});
$template->param(mode => $params->{mode});
$template->param(company_name => $config->{company_name});
$template->param(company_website => $config->{company_website});
$template->param(company_slogan => $config->{company_slogan});
$template->param(company_logo => $config->{company_logo});
my $generation_time = tv_interval ($starttime);
$template->param(time_gen => $generation_time);
my $time_now = localtime time;
$template->param(time_now => $time_now);
print $template->output;
print end_html;
