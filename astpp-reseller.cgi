#!/usr/bin/perl
#
# Asterisk(tm) Post Paid Platform
#
# Copyright (C) 2004, Digium, Inc.
# Copyright (C) 2004, Aleph Communications
#
# Mark Spencer <markster@digium.com
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2 or later
# at your option.
#
# This program is designed for resellers to add money to a card.
# Cards for the reseller must be added under a brand with the same
# name as the resellers account number.

use CGI qw/:standard Vars/;
use DBI;
use POSIX qw(strftime);
use POSIX qw(ceil floor);

require "/usr/local/astpp/astpp-common.pl";

my $copyright =
"ASTPP - Open Source VOIP Billing, &copy;2004 Digium, Inc. &copy;2004 Aleph Communications";
my @modes = ( "Home", "Cards", "Routes" );

#my @incs      = ( "1", "6", "30", "60" );
my %yesno = (
    '0' => gettext("NO"),
    '1' => gettext("YES")
);
my %sweeplist = (
    '0' => gettext("daily"),
    '1' => gettext("weekly"),
    '2' => gettext("monthly"),
    '3' => gettext("quarterly"),
    '4' => gettext("semi-annually"),
    '5' => gettext("annually")
);
my $dbh;

sub timestamp() {
    my $now = strftime "%Y%m%d%H%M%S", gmtime;
    return $now;
}

sub prettytimestamp() {
    my $now = strftime "%Y-%m-%d %H:%M:%S", gmtime;
    return $now;
}

sub build_menu() {
    my ($selected) = @_;
    my ( $body, $tmp, $bgcolor, $fgcolor );
    $body = "<table bgcolor=#777777 cellpadding=4 width=100>\n";
    foreach $tmp (@modes) {
        if ( $tmp eq $selected ) {
            if ( ( $tmp eq "Configure" ) && !$dbh ) {
                $bgcolor = "#ff4400";
            }
            else {
                $bgcolor = "#ff8800";
            }
            $fgcolor = "#ffffff";
        }
        else {
            if ( ( $tmp eq "Configure" ) && !$dbh ) {
                $bgcolor = "#cc0000";
            }
            else {
                $bgcolor = "#ccccff";
            }
            $fgcolor = "#999999";
        }
        $body .=
            "<tr><td bgcolor=\"$bgcolor\">"
          . "<font face=helvetica color=\"$fgcolor\">"
          . "&nbsp;&nbsp;<a href=\"?mode=$tmp\">$tmp</a>"
          . "</td></tr>\n";
    }
    $body .= "</table>\n";
    return $body;
}

sub build_home() {
    return
"<table width=400><tr><td>Welcome to the Asterisk&trade; Post Paid Reseller Sheet.  Please select "
      . "a function from the menu on the left.</td></tr></table>";
}

sub count_cards() {
    my ($clause) = @_;
    my $row;
    my $res;
    $sth = $dbh->prepare("SELECT COUNT(*) FROM cards $clause");
    $sth->execute;
    $row = $sth->fetchrow_hashref;
    $res = $row->{"COUNT(*)"};
    $sth->finish;
    return $res;
}

sub list_cards() {
    my $sth;
    my @cardlist;
    my $row;
    $sth =
      $dbh->prepare( "SELECT number FROM cards WHERE status < 2 AND reseller="
          . $dbh->quote($brand) );
    $sth->execute;
    while ( $row = $sth->fetchrow_hashref ) {
        push @cardlist, $row->{number};
    }
    $sth->finish;
    return @cardlist;
}

#sub list_cards_reseller() {
#	my $sth;
#	my @cardlist;
#	my $row;
#	$sth = $dbh->prepare("SELECT number FROM cards WHERE brand=" . $dbh->quote($brand));
#	$sth->execute;
#	while($row = $sth->fetchrow_hashref) {
#		push @cardlist, $row->{number};
#	}
#	$sth->finish;
#	return @cardlist;
#}

sub card_balance() {
    my ( $cardno, $cardinfo ) = @_;
    my $row;
    my $res;
    $sth =
      $dbh->prepare( "SELECT SUM(debit) FROM cdrs WHERE cardnum="
          . $dbh->quote($cardno)
          . " and status NOT IN (1, 2)" );
    $sth->execute;
    $row = $sth->fetchrow_hashref;
    $res = $row->{"SUM(debit)"};
    $sth->finish;
    $sth =
      $dbh->prepare( "SELECT SUM(credit) FROM cdrs WHERE cardnum="
          . $dbh->quote($cardno)
          . " and status NOT IN (1, 2)" );
    $sth->execute;
    $row  = $sth->fetchrow_hashref;
    $res1 = $row->{"SUM(credit)"};
    $sth->finish;
    $posted_balance = $cardinfo->{'balance'};
    $balance        = $res - $res1 + $posted_balance;
    return $balance;
}

sub isunique() {
    my ($number) = @_;
    my $clause   = "WHERE number = " . $dbh->quote($number);
    my $count    = &count_cards($clause);
    return 1 if $count == "0";
    return 0;
}

sub findunique() {
    my $number;
    for ( ; ; ) {
        $number =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        if ( $config{'startingdigit'} ne "" && $config{'startingdigit'} ne "0" )
        {
            $startingdigit = substr( $number, 0, 1 );
            if ( $startingdigit == $config{'startingdigit'} ) {
                $number = substr( $number, 0, $config{'cardlength'} );
                return $number if ( &isunique($number) );
            }
        }
        else {
            $number = substr( $number, 0, $config{'cardlength'} );
            return $number if ( &isunique($number) );
        }
    }
}

sub isuniquecc() {
    my ($cc)   = @_;
    my $clause = "WHERE cc = " . $dbh->quote($cc);
    my $count  = &count_cards($clause);
    return 1 if $count == "0";
    return 0;
}

sub finduniquecc() {
    my $cc;
    for ( ; ; ) {
        $cc =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        if ( $config{'startingdigit'} ne "" && $config{'startingdigit'} ne "0" )
        {
            $startingdigit = substr( $cc, 0, 1 );
            if ( $startingdigit == $config{'startingdigit'} ) {
                $cc = substr( $cc, 0, $config{'cardlength'} );
                return $number if ( &isuniquecc($number) );
            }
        }
        else {
            $cc = substr( $cc, 0, $config{'cardlength'} );
            return $cc if ( &isuniquecc($cc) );
        }
    }
}

sub savecdr() {
    my (
        $uniqueid,    $cardnum,  $callerid,  $callednum, $disposition,
        $billseconds, $billcost, $callstart, $carddata
      )
      = @_;
    $trunk    = "N/A" if ( $trunk    eq "" );
    $uniqueid = "N/A" if ( $uniqueid eq "" );
    if ( $carddata->{posttoexternal} == 1 ) {
        $status = 1;
    }
    else {
        $status = 0;
    }
    $dbh->do(
"INSERT INTO cdrs (uniqueid,cardnum,callerid,callednum,trunk,disposition,billseconds,debit,callstart,status) VALUES ("
          . $dbh->quote($uniqueid) . ", "
          . $dbh->quote($cardnum) . ", "
          . $dbh->quote($callerid) . ", "
          . $dbh->quote($callednum) . ", "
          . $dbh->quote($trunk) . ", "
          . $dbh->quote($disposition) . ", "
          . $dbh->quote($billseconds) . ", "
          . $dbh->quote( $billcost * 10000 ) . ", "
          . $dbh->quote($callstart) . ", "
          . $dbh->quote($status)
          . ")" );
}

sub addcard() {
    my ( $number, $inc, $brand, $sweep, $cc, $credit_limit, $posttoexternal ) =
      @_;

    #	if ( $sweep eq "daily" ) {
    #		$sweep = 0;
    #	}
    #	elsif ( $sweep eq "weekly" ) {
    #		$sweep = 1;
    #	}
    #	elsif ( $sweep eq "monthly" ) {
    #		$sweep = 2;
    #	}
    #	elsif ( $sweep eq "quarterly" ) {
    #		$sweep = 3;
    #	}
    #	elsif ( $sweep eq "semi-annually" ) {
    #		$sweep = 4;
    #	}
    #	elsif ( $sweep eq "annually" ) {
    #		$sweep = 5;
    #	}
    #	if ( $posttoexternal eq "YES" ) {
    #		$posttoexternal = 1;
    #	}
    #	else {
    #		$posttoexternal = 0;
    #	}
    my $tmp =
"INSERT INTO cards (cc,number,brand,status,sweep,credit_limit,posttoexternal,reseller) VALUES ("
      . $dbh->quote($cc) . ", "
      . $dbh->quote($number) . ", "
      . $dbh->quote($brand) . ", 1,"
      . $dbh->quote($sweep) . ", "
      . $dbh->quote($credit_limit) . ", "
      . $dbh->quote($posttoexternal) . ", "
      . $dbh->quote($reseller) . ")";
    $dbh->do($tmp) || print "$tmp failed";
}

sub getcard() {
    my ($cardno) = @_;
    my $res;
    $sth =
      $dbh->prepare( "SELECT * FROM cards WHERE number="
          . $dbh->quote($cardno)
          . "AND status = 1" );
    $sth->execute;
    $res = $sth->fetchrow_hashref;
    $sth->finish;
    return $res;
}

sub getbrand() {
    my ($brand) = @_;
    my $res;
    $sth =
      $dbh->prepare( "SELECT * FROM brands WHERE name= "
          . $dbh->quote($brand)
          . " AND status IN (0, 1)" );
    $sth->execute;
    $res = $sth->fetchrow_hashref;
    $sth->finish;
    return $res;
}

# Copyright Digium (Most of this one was written by Darren Wiebe
sub generatecards() {
    my ( $count, $pennies, $brand, $number, $sweep, $credit_limit,
        $posttoexternal )
      = @_;
    my ( $row, $x, $language, $inc, $markup, $cardlist );
    my $status      = "";
    my $description = "Initial Credit";

    # Retrieve brand specifications
    my $sth =
      $dbh->prepare(
        "SELECT * FROM brands WHERE name = " . $dbh->quote($brand) );
    $sth->execute;
    if ( $row = $sth->fetchrow_hashref ) {
        ( $language, $inc, $markup ) =
          ( $row->{language}, $row->{inc}, $row->{markup} );
    }
    $sth->finish;
    $cardlist = &getcard($number);
    if ( $cardlist->{number} eq "" ) {
        if ( $number ne "" ) {
            &addcard( $number, $inc, $brand, $sweep, $credit_limit,
                $posttoexternal );
            if ( $config{'email'} == 1 ) {
                open( EMAIL, "| $enh_config{mailprog} -t " )
                  || die "Error - could not write to $enh_config{mailprog}\n";
                print EMAIL "From: $config{company_email}\n";
                print EMAIL
                  "Subject: $config{company_name} New Account Created\n";
                print EMAIL "To: $config{emailadd}\n";
                print EMAIL "Content-type: text/plain\n\n";
                print EMAIL
"You have added an $count $brand account in the amount of $pennies cents. \n\n";
                $timestamp = &prettytimestamp;
                $dbh->do(
"INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
                      . $dbh->quote($number) . ","
                      . $dbh->quote($description) . ", "
                      . $dbh->quote( $pennies * 100 ) . ", "
                      . $dbh->quote($timestamp)
                      . ")" );
                $status .= "Account: $number has been created<br>";
            }
        }
        else {
            $status .= "No account number entered.<br>";
        }
    }
    elsif ( $cardlist->{status} != 1 ) {
        $dbh->do( "UPDATE cards SET status = 1 WHERE number = "
              . $dbh->quote($number) );
        $status .= "Account: $number has been (re)activated<br>";
    }
    else {
        $status .= "Account: $number exists already!<br>";
    }
    if ( $config{'email'} == 1 ) {
        print EMAIL "Account: $number \n";
    }
    if ( $config{'email'} == 1 ) {
        close(EMAIL);
    }
    return $status;
}

sub build_cards() {
    my ( $total, $used, $pennies, $count, $brand );
    my $accountcount = 0;
    my $totalbalance = 0;
    return "Cannot edit accounts until database is configured" unless $dbh;
    if ( param('action') eq "Generate..." ) {
        $count   = param('count');
        $pennies = param('pennies');
        if ( $count < 1 ) {
            $status = "Can't generate fewer than one account\n";
        }
        else {
            $status = &generatecards( $count, $pennies, $brand );
        }
    }
    elsif ( param('action') eq "Refill..." ) {
        $description = "Apply Payment";
        $cardinfo    = &getcard( param('refillnum') );
        $number      = $cardinfo->{number};
        $timestamp   = &prettytimestamp;
        if (
            $dbh->do(
"INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
                  . $dbh->quote($number) . ", "
                  . $dbh->quote($description) . ", "
                  . $dbh->quote( param('refillpennies') * 100 ) . ", "
                  . $dbh->quote($timestamp) . ")"
            )
          )
        {
            $status = "Refilled Account " . param('refillnum') . "'.";
        }
        else {
            $status = "Unable to Refill Account" . param('refillnum') . "'.";
        }
    }
    elsif ( param('action') eq "Generate_Account..." ) {
        my $count          = 1;
        my $pennies        = param('custom_pennies');
        my $cardnumber     = param('customnum');
        my $sweep          = param('sweep');
        my $credit_limit   = param('credit_limit');
        my $posttoexternal = param('posttoexternal');
        $status =
          &generatecards( $count, $pennies, $brand, $cardnumber, $sweep,
            $credit_limit, $posttoexternal );
    }
    elsif ( param('action') eq "Drop..." ) {
        if (
            $dbh->do(
                "UPDATE cards SET status = 2 WHERE number = "
                  . $dbh->quote( param('number') )
            )
          )
        {
            $status = "Deactivated Account " . param('number') . "'.";
        }
        else {
            $status = "Unable to deactivate account " . param('number') . "'.";
        }
    }
    $body = "<table><tr><td>" . start_form . "</td></tr>";
    $body .=
        "<tr><td>Generate Account #:"
      . hidden( -name => "mode", -value => "Accounts" )
      . textfield( -name => "customnum", -size => 20 )
      . "Billing Schedule: "
      . popup_menu( -name => "sweep", -values => \%sweeplist )
      . "</td></tr>&nbsp;&nbsp;<tr><td>"
      . "Credit Limit in dollars "
      . textfield(
        -name => "credit_limit",
        -size => 10 . "</td></tr>&nbsp;&nbsp;<tr><td>"
      )
      . submit( -name => "action", -value => "Generate_Account..." )
      . "</td></tr><td>-</td>";
    $body .=
        "<tr><td>Delete account&nbsp;&nbsp;"
      . hidden( -name => "mode", -value => "Accounts" )
      . textfield( -name => "number", -size => 20 )
      . "&nbsp;&nbsp;&nbsp;&nbsp;"
      . submit( -name => "action", -value => "Drop..." )
      . "</td></tr>"
      . "<td>-</td>";
    $body .=
        "<tr><td>Apply Payment to Account: &nbsp;&nbsp;\n"
      . hidden( -name => "mode", -value => "Accounts" )
      . textfield( -name => "refillnum", -size => 20 )
      . "&nbsp;&nbsp;In the Amount of &nbsp;&nbsp;"
      . textfield( -name => "refillpennies", -size => 8 )
      . "&nbsp;&nbsp;pennies&nbsp;&nbsp;"
      . submit( -name => "action", -value => "Refill..." )
      . "</td></tr>"
      . "<td>-</td>";
    $body .=
        "<tr><td>Get information on account&nbsp;&nbsp;"
      . hidden( -name => "mode", -value => "Accounts" )
      . textfield( -name => "cardnum", -size => 20 )
      . "&nbsp;&nbsp;&nbsp;&nbsp;"
      . submit( -name => "action", -value => "Information..." )
      . "</td></tr>"
      . "<td>-</td>";
    $body .=
        "<tr><td>List Accounts&nbsp;&nbsp;"
      . hidden( -name => "mode", -value => "Accounts" )
      . submit( -name => "action", -value => "List_Accounts..." )
      . "</td></tr>";
    $body .= "</table>";
    $cardnum = param('cardnum');

    if ( param('action') eq "List_Accounts..." ) {
        my $cardinfo;
        my $status;
        @cardlist = &list_cards();
        $body .= "<table>";
        $body .=
          "<tr bgcolor=ffffff><tdcolspan=4><i>" . $status . "</i></td></tr>\n";
        $body .=
"<tr bgcolor=ccccff><td>Card Number</td><td>Account Number</td><td>Increment</td><td>Brand</td><td>Balance</td><td>Credit Limit</td><td>Billing Sweep</td><td>Post to External</td></tr>\n";
        foreach (@cardlist) {
            $cardnum = $_;
            $accountcount++;
            $cardinfo = &getcard($cardnum);
            $balance  = &card_balance( $cardnum, $cardinfo );
            $balance  = $balance / 10000;
            $balance  =
              sprintf( "%." . $enh_config{decimalpoints} . "f", $balance );
            $totalbalance = $totalbalance + $balance;
            if ( $cardinfo->{'number'} ) {
                $count++;
                if ( !( $count % 2 ) ) {
                    $color = "#ccffcc";
                }
                else {
                    $color = "#ffffcc";
                }

                $body .=
"<tr bgcolor=$color><td>$cardinfo->{cc}</td><td>$cardnum</td><td>$cardinfo->{inc}</td><td>$cardinfo->{brand}</td><td>\$$balance</td><td>$cardinfo->{credit_limit}</td><td>$cardinfo->{sweep}</td><td>$cardinfo->{posttoexternal}";
                $body .= "</td></tr>\n";
            }
        }
        $body .=
"<tr bgcolor=ff8800><td colspan=3>Number of Accounts: $accountcount</td><td colspan=6>Total Owing: \$ $totalbalance</td></tr></ERROR>";
        $body .= "</table>";
    }
    elsif ( ( param('action') eq "Information..." )
        && length( param('cardnum') ) )
    {
        my $cardinfo;
        my $status;
        $cardinfo = &getcard( param('cardnum') );
        $balance  = &card_balance( $cardnum, $cardinfo );
        $balance  = $balance / 10000;
        $balance  = sprintf( "%.2f", $balance );
        if ( $cardinfo->{'number'} ) {
            $status =
                "Account&nbsp;&nbsp;</i><b>"
              . param('cardnum')
              . "&nbsp;&nbsp;</b><i> has a balance of&nbsp;&nbsp;"
              . "</i><b>$balance dollars</b></i>\n";
            $status .=
"with a credit limit of </i><b>\$$cardinfo->{'credit_limit'}</b></i>\n";
        }
        else {
            $status = "No such card number '" . param('cardnum') . "' found!\n";
        }
        $body .= "<table>";
        $body .=
          "<tr bgcolor=ffffff><tdcolspan=4><i>" . $status . "</i></td></tr>\n";
        if ( $cardinfo->{'number'} ) {
            $body .=
"<tr bgcolor=ccccff><td>UniqueID</td><td>Date & Time</td><td>Caller*ID</td><td>Called Number</td><td>Trunk</td><td>Disposition</td><td>Billable Seconds</td><td>Charge</td><td>Credit</td></tr>\n";
            my $sth =
              $dbh->prepare( "SELECT * FROM cdrs WHERE cardnum = "
                  . $dbh->quote( $cardinfo->{'number'} )
                  . " and status IN (NULL, 0, 1)ORDER BY callstart DESC" );
            my $res;
            my $count;
            my $callerid;
            $sth->execute;
            while ( ( $res = $sth->fetchrow_hashref ) ) {
                $callerid = $res->{callerid};
                $callerid = "&lt;unknown&gt;" unless $callerid;
                $uniqueid = $res->{uniqueid};
                $uniqueid = "&lt;NA&gt;" unless $uniqueid;
                eval { $credit = $res->{credit} / 10000 };
                eval { $debit  = $res->{debit} / 10000 };
                $debit  = sprintf( "%.6f", $debit );
                $credit = sprintf( "%.6f", $credit );
                $debit  = "-" if ( $debit == 0 );
                $credit = "-" if ( $credit == 0 );
                $count++;

                if ( !( $count % 2 ) ) {
                    $color = "#ccffcc";
                }
                else {
                    $color = "#ffffcc";
                }
                $body .=
"<tr bgcolor=$color><td>$uniqueid</td><td>$res->{callstart}</td><td>$callerid</td><td>$res->{callednum}</td>"
                  . "<td>N/A</td><td>$res->{disposition}</td><td>$res->{billseconds}</td><td>$debit</td><td>$credit</td></tr>";
            }
        }
        $body .= "</table>";
    }
    return $body;
}

#sub build_cards() {
#	my $total, $used;
#	my $pennies, $count, $brand;
#	return "Cannot edit cards until database is configured" unless $dbh;
##	my $brands = list_brands();
##	return "Please define at least one brand before creating cards" unless $brands;
#	if (param('action') eq "Generate...") {
#		$count = param('count');
#		$pennies = param('pennies');
#		if ($count < 1) {
#			$status = "Can't generate fewer than one card\n";
#		} else {
#			$status = &generatecards($count, $pennies, $brand);
#		}
#	} elsif (param('action') eq "Generate_Custom...") {
#		my $count = 1;
#		my $pennies = param('custom_pennies');
##		my $brand = param('brand');
#		my $cardnumber = param('customnum');
#		$status = &generatecards($count, $pennies, $brand, $cardnumber);
#	} elsif (param('action') eq "Drop...") {
#		if ($dbh->do("DELETE FROM cards WHERE number = " . $dbh->quote(param('number')))) {
#			$status = "Dropped card " . param('number') . "'.";
#		} else {
# 			$status = "Unable to drop card ". param('number') . "'.";
#		}
#	}
#	$body = "<table><tr><td>" . start_form . "</td></tr>";
#	$body .= "<tr><td>Account Code " . hidden(-name => "mode", -value => "Cards") .
#		textfield(-name => "customnum", -size => 20) . "&nbsp;" .
#		submit(-name => "action", -value => "Generate_Custom...") .
#		"</td></tr>";
#	$body .= "<tr><td>Delete account&nbsp;&nbsp;" . hidden(-name => "mode", -value => "Cards") .
#		textfield(-name => "number", -size => 20)  . "&nbsp;&nbsp;&nbsp;&nbsp;" .
#		submit(-name => "action", -value => "Drop...") . "</td></tr>";
#	$body .= "<tr><td>List Accounts&nbsp;&nbsp;" . hidden(-name => "mode", -value => "Cards") .
#		submit(-name => "action", -value => "List_Cards...") . "</td></tr>";
#	$body .= "</table>";
#	$cardnum = param('cardnum');
#	if (param('action') eq "List_Cards...") {
#		my $cardinfo;
#		my $status;
#		@cardlist = &list_cards_reseller();
#		$body .= "<table>";
#		$body .= "<tr bgcolor=ffffff><tdcolspan=4><i>" . $status . "</i></td></tr>\n";
#		$body .= "<tr bgcolor=ccccff><td>Card Number</td><td>Increment</td></tr>\n";
#		foreach (@cardlist) {
#			$cardnum = $_;
#			$cardinfo = &getcard($cardnum);
#			if ($cardinfo->{'number'}) {
#				$count++;
#					if (!($count % 2)) {
#					$color = "#ccffcc";
#				} else {
#					$color = "#ffffcc";
#				}
#
#			$body .= "<tr bgcolor=$color><td>$cardnum</td><td>$cardinfo->{inc}";
#			$body .= "</td></tr>\n";
#			}
#		}
#		$body .= "</table>";
#	}
#	return $body;
#}

sub build_routes() {
    my $sth;
    my $count;
    my $color;
    my $tmp;
    my $editing;
    my $creating;
    my $brands;

    #	my $newbrands;
    my $limit = param('limit');
    if ( $limit < 1 ) { $limit = 0 }
    my $status = "&nbsp;";
    return "Cannot edit routes until database is configured" unless $dbh;

    #
    # Find what we're editing if anything
    #
    if ( param("action") eq "Yes, drop it" ) {
        if (
            $dbh->do(
                    "DELETE FROM routes WHERE id = "
                  . $dbh->quote( param('dropitem') )
                  . " AND brand = "
                  . $dbh->quote($brand)
            )
          )
        {
            $status = "Dropped route '" . param('dropitem') . "'.";
        }
        else {
            $status = "Unable to drop route '" . param('dropitem') . "'.";
        }
    }
    for ( $x = 1 ; length( param("item$x") ) ; $x++ ) {
        if ( param("action$x") eq "Edit..." ) {
            $editing = param("item$x");
        }
        elsif ( param("action$x") eq "Add..." ) {
            $editing  = param("item$x");
            $creating = "yes";
            $status   = "Creating new entry $x";
        }
        elsif ( param("action$x") eq "Save..." ) {
            $dbh->do( "DELETE FROM routes WHERE id = "
                  . $dbh->quote( param("item$x") ) );
            $tmp =
"INSERT INTO routes (pattern,comment,connectcost, includedseconds, cost, brand, reseller,status) VALUES ("
              . $dbh->quote( param("newpat$x") ) . ", "
              . $dbh->quote( param("newcomment$x") ) . ", "
              . $dbh->quote( param("newconnect$x") ) . ", "
              . $dbh->quote( param("newincluded$x") ) . ", "
              . $dbh->quote( param("newcost$x") ) . ", "
              . $dbh->quote($brand) . ", "
              . $dbh->quote($reseller) . ", 1)";
            if ( $dbh->do($tmp) ) {
                $status .=
                  "Pattern '" . param("newpat$x") . "' has been updated";
            }
            else {
                $status .= "Pattern '"
                  . param("newpat$x")
                  . "' FAILED to update ($tmp)!";
            }
        }
        elsif ( param("action$x") eq "Create..." ) {
            $tmp =
"INSERT INTO routes (pattern,comment,brand,connectcost, includedseconds, cost, reseller, status) VALUES ("
              . $dbh->quote( param("newpat$x") ) . ", "
              . $dbh->quote( param("newcomment$x") ) . ", "
              . $dbh->quote($brand) . ", "
              . $dbh->quote( param("newconnect$x") ) . ", "
              . $dbh->quote( param("newincluded$x") ) . ", "
              . $dbh->quote( param("newcost$x") ) . ", "
              . $dbh->quote($reseller) . ", 1)";
            if ( $dbh->do($tmp) ) {
                $status =
                  "Pattern '" . param("newpat$x") . "' has been created";
            }
            else {
                $status = "Pattern '"
                  . param("newpat$x")
                  . "' FAILED to create ($tmp)!";
            }
        }
        elsif ( param("action$x") eq "Drop..." ) {
            $status =
                "Are you sure you want to delete the pattern '"
              . param("item$x")
              . "'?<br>"
              . hidden( -name => "dropitem", -value => param("item$x") )
              . submit( -name => "action", -value => "Yes, drop it" )
              . "&nbsp;&nbsp;&nbsp;"
              . submit( -name => "action", -value => "Cancel" );
            $editing = "dropping something";
        }
    }
    $sth =
      $dbh->prepare(
        'SELECT id FROM routes WHERE brand = ' . $dbh->quote($brand) );
    $sth->execute || return "Something is wrong with the routes database\n";
    my $results = $sth->rows;
    if ( $results > 0 ) {
        eval {
            my $pagesrequired =
              ceil( $results / $enh_config{results_per_page} );
        };
        print "Pages Required: $pagesrequired\n" if ( $config{debug} == 1 );
    }
    $sth =
      $dbh->prepare( "SELECT * FROM routes WHERE brand = "
          . $dbh->quote($brand)
          . "limit $limit, $config{results_per_page}" );
    $sth->execute || return "Something is wrong with the routes database\n";
    $body = "<table>"
      . "<tr bgcolor=ffffff><td colspan=7>"
      . start_form
      . "<i>$status</i></td></tr>\n"
      . "<tr bgcolor=ffffff><td colspan=3><font size=-1><i>Pattern is a REGEX</i></font></td><td colspan=4>"
      . "<font size=-1><i>All costs are in 1/100 of a penny</i></font></td></tr>\n"
      . "<tr bgcolor=ccccff><td>"
      . hidden( -name => 'mode', -value => 'Routes' )
      . "Pattern</td><td>Comment</td><td>Connect Fee</td><td>Inc. Seconds</td><td colspan=2>Cost per additional minute</td></tr>\n";
    while ( $row = $sth->fetchrow_hashref ) {
        $count++;
        Delete("newpat$count");
        Delete("item$count");
        if ( !( $count % 2 ) ) {
            $color = "#ccffcc";
        }
        else {
            $color = "#ffffcc";
        }
        if ( $editing eq $row->{id} ) {
            $body .=
                "<tr valign=top bgcolor=$color><td>"
              . hidden( -name => "item$count", -value => $row->{id} )
              . textfield(
                -name    => "newpat$count",
                -size    => 8,
                -default => $row->{pattern}
              )
              . "</td><td>"
              . textfield(
                -name    => "newcomment$count",
                -size    => 20,
                -default => $row->{comment}
              )
              . "</td><td>"
              . textfield(
                -name    => "newconnect$count",
                -size    => 5,
                -default => $row->{connectcost}
              )
              . "</td><td>"
              . textfield(
                -name    => "newincluded$count",
                -size    => 5,
                -default => $row->{includedseconds}
              )
              . "</td><td>"
              . textfield(
                -name    => "newcost$count",
                -size    => 5,
                -default => $row->{cost}
              )
              . "</td>";
            $body .=
                "<td bgcolor=white>"
              . submit( -name => "action$count", -value => 'Save...' )
              . submit( -name => "action$count", -value => 'Cancel...' );
            $body .= "</td></tr>";
        }
        else {
            $body .=
                "<tr bgcolor=$color><td>"
              . hidden( -name => "item$count", -value => $row->{id} )
              . "$row->{pattern}</td>"
              . "<td>$row->{comment}</td><td>$row->{brand}</td><td>$row->{connectcost}</td><td>$row->{includedseconds}</td><td>$row->{cost}</td>"
              . "<td bgcolor=white>";
            if ( !$editing ) {
                $body .=
                    submit( -name => "action$count", -value => 'Edit...' )
                  . submit( -name => "action$count", -value => 'Drop...' );
            }
            else {
                $body .= "&nbsp;";
            }
            $body .= "</td></tr>\n";
        }
    }
    $sth->finish;
    $count++;
    if ($creating) {
        $body .=
            "<tr valign=top bgcolor=$color><td>"
          . hidden( -name => "item$count", -value => $row->{name} )
          . textfield( -name => "newpat$count", -size => 8 )
          . "</td><td>"
          . textfield( -name => "newcomment$count", -size => 20 )
          . "</td><td>"
          . textfield( -name => "newconnect$count", -size => 5, default => 0 )
          . "</td><td>"
          . textfield( -name => "newincluded$count", -size => 5, default => 0 )
          . "</td><td>"
          . textfield( -name => "newcost$count", -size => 5, -default => 0 )
          . "</td>";
        $body .=
            "<td bgcolor=white>"
          . submit( -name => "action$count", -value => 'Create...' )
          . submit( -name => "action$count", -value => 'Cancel...' );
        $body .= "</td></tr>";
    }
    if ( !$editing ) {
        $body .= "<tr bgcolor=ffffff><td colspan=7><hr></td></tr>\n";
        $body .=
            "<tr bgcolor=ffffff><td colspan=6>&nbsp;"
          . hidden( -name => "item$count", -value => "new" )
          . "</td><td align=right>"
          . submit( -name => "action$count", -value => "Add..." )
          . "</td></tr>\n";
    }
    $body .= "</table>\n";
    for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
        if ( $i == 0 ) {
            if ( $limit != 0 ) {
                $body .= "<a href=\"astpp-admin.cgi?mode=Routes&limit=0\">";
                $body .= $i + 1;
                $body .= "</a>";
            }
            else {
                $body .= $i + 1;
            }
        }
        if ( $i > 0 ) {
            if ( $limit != ( $i * $enh_config{results_per_page} ) ) {
                $body .= "<a href=\"astpp-admin.cgi?mode=Routes&limit=";
                $body .= ( $i * $enh_config{results_per_page} );
                $body .= "\">\n";
                $body .= $i + 1, "</a>";
            }
            else {
                $pageno = $i + 1;
                $body .= " | ", $i + 1;
            }
        }
    }
    $body .= "";
    $body .= " Page $pageno of $pagesrequired";
    return $body;
}

sub build_body() {
    my ($mode) = @_;
    my $body;
    if ( $reseller ne "" ) {
        return &build_home()   if ( $mode eq "Home" );
        return &build_cards()  if ( $mode eq "Cards" );
        return &build_routes() if ( $mode eq "Routes" );
        return "<h2>Not yet implemented!</h2>\n";
    }
    else {
        return "<h2>You are NOT logged in!</h2>\n";
    }
}

#Darren Wiebe Copyright starts here
sub connect_db() {
    $dbh->disconnect if $dbh;
    $dbh =
      DBI->connect(
        "DBI:mysql:database=$config{'dbname'};host=$config{'dbhost'}",
        $config{'dbuser'}, $config{'dbpass'} );
    if ( !$dbh ) {
        foreach $handle (@output) {
            print $handle "ASTPP DATABASE IS DOWN\n";
        }
    }
    else {
        return $dbh;
    }
}

sub login() {
    my ( $sql, $count, $record, $cookie );
    $params{username} = "" if !$params{username};
    $params{password} = "" if !$params{password};
    $sql              =
      $astpp_db->prepare( "SELECT COUNT(*) FROM cards WHERE number = "
          . $astpp_db->quote( $params{username} )
          . " AND password = "
          . $astpp_db->quote( $params{password} )
          . " AND status = 1 AND type IN (2, 3)" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    $cookie = cookie(
        -name    => 'ASTPP_User',
        -value   => $params{username},
        -expires => '+1h'
    );
    $cookie1 = cookie(
        -name    => 'ASTPP_Password',
        -value   => $params{password},
        -expires => '+1h'
    );

    if ( $count == 1 ) {
        $status = "Successful Login!";
        print header( -cookie => [ $cookie, $cookie1 ] );
    }
    elsif ( $enh_config{auth} eq $params{password} ) {
        $status = "Successful Login!";
        $count  = 1;
        print header( -cookie => [ $cookie, $cookie1 ] );
    }
    else {
        $params{mode} = "";
        $status = "Login Failed";
        print header();
    }
    print STDERR "ASTPP-USER: $params{username}\n";
    print STDERR "ASTPP-PASS: $params{password}\n";
    print STDERR "ASTPP-USER-COUNT: $count";
    return ( $params{mode}, $count );
}

sub verify_login() {
    my ( $sql, $count, $record );
    $params{username} = cookie('ASTPP_User');
    $params{password} = cookie('ASTPP_Password');
    $params{username} = "" if !$params{username};
    $params{password} = "" if !$params{password};
    $sql              =
      $astpp_db->prepare( "SELECT COUNT(*) FROM cards WHERE number = "
          . $astpp_db->quote( $params{username} )
          . " AND password = "
          . $astpp_db->quote( $params{password} )
          . " AND status = 1 AND type IN (2, 3)" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;

    if ( $enh_config{auth} eq $params{password} ) {
        $count = 1;
    }
    if ( $count != 1 ) {
        $params{mode} = "";
        $status = "Login Failed";
    }
    print STDERR "ASTPP-USER: $params{username}\n";
    print STDERR "ASTPP-PASS: $params{password}\n";
    print STDERR "ASTPP-USER-COUNT: $count";
    print header();
    return $count;
}

sub logout() {
    $cookie = cookie(
        -name    => 'ASTPP_User',
        -value   => 'loggedout',
        -expires => 'now'
    );
    $cookie1 = cookie(
        -name    => 'ASTPP_Password',
        -value   => 'loggedout',
        -expires => 'now'
    );
    print header( -cookie => [ $cookie, $cookie1 ] );
    $status = "Successfully Logged User Out";
    return "";
}

####################Program Starts here##########################33
my ( $body, $menu );
foreach my $param ( param() ) {
    $params{$param} = param($param);
    print STDERR "$param $params{$param}\n";
}
my $mode = $params{mode};

$config     = &load_config();
$enh_config = &load_config_enh();
&connect_db;

if (   ( $params{mode} eq "Login" )
    || ( $params{mode} eq "Logout" ) )
{
    ( $params{mode}, $loginstat ) = &login if $params{mode} eq "Login";
    $params{mode} = &logout if $params{mode} eq "Logout";
}
else {
    $loginstat = &verify_login();
}
$reseller = $params{username};
$brand    = $reseller;

print start_html('ASTPP - Open Source VOIP Billing');

# Darren Wiebe Copyright ends here
if ( $loginstat == 1 ) {
    $msg  = "&nbsp;";
    $mode = "Home" unless grep( /^$mode$/, @modes );
    $body = &build_body($mode);
    $menu = &build_menu($mode);
    $msg  = "<i>Database unavailable -- please check configuration</i>"
      unless $dbh;
    print
      "<title>ASTPP - Open Source VOIP Billing Reseller Sheet: $mode</title>\n";
    print "<STYLE TYPE=\"text/css\">\n";
    print "<!--\n";
    print "  \@import url(/_astpp/style.css) \n";
    print "-->\n";
    print "</STYLE>\n";
    print "<body><table align=center width=800>\n";
    print "   <tr><td><img src=\"/_astpp/logo.jpg\"></td>\n";
    print
"   <td align=center><font face=helvetica size=5>ASTPP - Open Source VOIP Billing: <b>$mode</b></font>"
      . "<br><font face=helvetica color=#444444>$msg</font></td></tr>";
    print
"   <tr><td height=350 valign=top>$menu</td><td valign=top width=90% align=center><font face=helvetica>$body</font></td></tr>";
    print
"<tr><td align=center colspan=2><hr><font face=helvetica size=-1>$copyright</font></td></tr>\n";
    print "</table></body>\n";

    # Darren Wiebe Copyright starts here
}
else {
    print "<title>ASTPP - Open Source Voip Billing Login</title>\n";
    print "<STYLE TYPE=\"text/css\">\n";
    print "<!--\n";
    print "  \@import url(/_astpp/style.css); \n";
    print "-->\n";
    print "</STYLE>\n";
    print "<BODY>\n";
    print "<table><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
      . "<td align=center><H2>ASTPP - Open Source VOIP Billing Login</H2></td></tr></TABLE>\n";
    print "<table><tr><td align=center>$status</td></tr>"
      . "<tr><td align=center>Please Login Now</td></tr>"
      . startform;
    print "<tr><td>Username: " . textfield('username') . "</td></tr>";
    print "<tr><td>Password: " . password_field('password') . "</td></tr>";
    print "<tr><td>"
      . submit( -name => 'mode', -value => 'Login' )
      . reset()
      . "</td></tr>";
}

print end_html;

