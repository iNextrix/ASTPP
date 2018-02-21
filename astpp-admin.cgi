#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2006, DNW Consulting
#
# Darren Wiebe (darren@dnwconsulting.net)
# Sergey Tamkovich was hired by Aleph Communications to add
# the import ability and some of the "statistics" features.
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
# Account Status Info
# 1 = Account Active
# 2 = Account Deactivated
#
# CDR Status Info
# 0 - New line
# 1 - Billed Line
# 2 - Deactivated Line
#
# Account Type
# 0 - Regular User (Has login permissions for astpp-users.cgi)
# 1 - Reseller (Has login permissions for astpp-users.cgi and has reduced permissions for astpp-admin.cgi)
# 2 - Admin (Has login permissions everywhere)
# 3 - Vendor (Has reduced login permissions in astpp-admin.cgi)
# 4 - Customer Service (Has reduced login permissions in astpp-admin.cgi)
###############################################################################
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
use lib './lib', '../lib';
use warnings;
use Asterisk::Manager;
use Text::CSV;
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");
use vars qw(@output $astpp_db $params $enh_config
  $status $config $limit $accountinfo
  $freepbx_db $rt_db $openser_db);
my $copyright =
  gettext("ASTPP - Open Source Voip Billing &copy;2006 Aleph Communications");
my @Home     = ( gettext("Home Page") );
my @Accounts = (
    gettext("Create Account"), gettext("Process Payment"),
    gettext("Remove Account"), gettext("Edit Account"),
    gettext("List Accounts"),  gettext("View Details")
);
my @Rates = (
    gettext("Pricelists"),       gettext("Calc Charge"),
    gettext("Routes"),           gettext("Import Routes"),
    gettext("Periodic Charges"), gettext("Packages")
);
my @DIDs      = ( gettext("Manage DIDs"), gettext("Import DIDs") );
my @LCR       = (
    gettext("Providers"), gettext("Trunks"),
    gettext("Outbound Routes"),
    gettext("Import Outbound Routes")
);
my @System =
  ( gettext("Purge Deactivated"), gettext("Logout") );
my @Statistics =
  ( gettext("List Errors"), gettext("Trunk stats"), gettext("View CDRs") );

# gettext("Asterisk Stats"),   # This is turned off until it gets finished
my @Callingcards = (
    gettext("List Cards"),  gettext("Add Cards"),
    gettext("Delete Card"), gettext("Refill Card"),
    gettext("View Card"),   gettext("Update Card(s) Status"),
    gettext("Reset InUse"), gettext("CC Brands")
);
my @SwitchConfig =
  ( gettext("IAX Devices"), gettext("SIP Devices"), gettext("Dialplan") );
my %menumap = (
    gettext('Accounts')      => \@Accounts,
    gettext('Rates')         => \@Rates,
    gettext('DIDs')          => \@DIDs,
    gettext('Statistics')    => \@Statistics,
    gettext('System')        => \@System,
    gettext('LCR')           => \@LCR,
    gettext('Calling Cards') => \@Callingcards,
    gettext('Switch Config') => \@SwitchConfig
);
my @months = (
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
);
my @techs = ( "SIP", "IAX2", "Zap", "Local", "OH323", "OOH323C" );
my @incs  = ( "1",   "6",    "30",  "60" );
my @devicetypes = ( gettext("friend"), gettext("user"), gettext("peer") );
my %yesno = (
    '0' => gettext("NO"),
    '1' => gettext("YES")
);
my @cardstatus = ( gettext("ACTIVE"), gettext("INACTIVE"), gettext("DELETED") );
my %sweeplist = (
    '0' => gettext("daily"),
    '1' => gettext("weekly"),
    '2' => gettext("monthly"),
    '3' => gettext("quarterly"),
    '4' => gettext("semi-annually"),
    '5' => gettext("annually")
);
my @output   = ("STDERR");            # "LOGFILE" );
my @language = all_language_codes;
@language = sort @language;
my @currency       = ("CAD");
my @deviceprotocol = ("SIP");
my @countries      = all_country_names();
@countries = sort @countries;
my (
    $rt_db,  $astpp_db, $config,   $enh_config, $params,
    $param,  $cdr_db,   $agile_db, $body,       $menu,
    $status, $msg,      $loginstat, @modes, $openser_db
);

$params->{mode} = "";
$params->{username} ="";
$params->{password} = "";

sub login() {
    my ( $sql, $count, $record, $cookie, $cookie1, $accountinfo );
    $sql =
      $astpp_db->prepare( "SELECT COUNT(*) FROM accounts WHERE number = "
          . $astpp_db->quote( $params->{username} )
          . " AND password = "
          . $astpp_db->quote( $params->{password} )
          . " AND status = 1 AND type IN (1,2,3,4)" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    $cookie = cookie(
        -name    => 'ASTPP_User',
        -value   => $params->{username},
        -expires => '+8h',
        -domain  => $ENV->{SERVER_NAME},
        -path    => $ENV->{SCRIPT_NAME}
    );
    $cookie1 = cookie(
        -name    => 'ASTPP_Password',
        -value   => $params->{password},
        -expires => '+8h',
        -domain  => $ENV->{SERVER_NAME},
        -path    => $ENV->{SCRIPT_NAME}
    );

    if ( $count == 1 ) {
        $status .= gettext("Successful Login!") . "<br>";
        print header( -cookie => [ $cookie, $cookie1, ] );
	$accountinfo = &get_account($astpp_db, $params->{username});
	$params->{logintype} = $accountinfo->{type};
    }
    if ( !$params->{username} && $enh_config->{auth} eq $params->{password} ) {
        $status .= gettext("Successful Login!") . "<br>";
        $count  = 1;
        print header( -cookie => [ $cookie, $cookie1 ] );
	$params->{logintype} = 2;
    }
    if ($count == 0 && $params->{password} eq ""){
        $params->{mode} = "";
        $status .= gettext("Please Login") . "<br>";
        print header();
    } elsif ($count == 0) {
        $params->{mode} = "";
        $status .= gettext("Login Failed") . "<br>";
        print header();
    }
    print STDERR gettext("ASTPP-USER:") . " $params->{username}\n"
      if $config->{debug} == 1;
    print STDERR gettext("ASTPP-PASS:") . " $params->{password}\n"
      if $config->{debug} == 1;
    print STDERR gettext("ASTPP-AUTHCODE:") . " $enh_config->{auth}\n"
      if $config->{debug} == 1;
    print STDERR gettext("ASTPP-USER-COUNT:") . " $count"
      if $config->{debug} == 1;
    return ( $params->{mode}, $count );
}

sub verify_login() {
    my ( $sql, $count, $record, $username, $password );
    $params->{username} = cookie('ASTPP_User');
    $params->{password} = cookie('ASTPP_Password');
    if ($params->{username} && $params->{password}) {
    $sql                =
      $astpp_db->prepare( "SELECT COUNT(*) FROM accounts WHERE number = "
          . $astpp_db->quote( $params->{username} )
          . " AND password = "
          . $astpp_db->quote( $params->{password} )
          . " AND status = 1 AND type IN (1,2,3,4)" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
	if ($count == 1) {
	$accountinfo = &get_account($astpp_db, $params->{username});
	$params->{logintype} = $accountinfo->{type};
	}
    }
    if ( !$params->{username} && $enh_config->{auth} eq $params->{password} ) {
        $count = 1;
	$params->{logintype} = 2;
    }
    if ( $count != 1 && !$params->{password}) {
        $params->{mode} = "";
        $status .= "" . "<br>";
    } elsif ($count != 1) {
        $params->{mode} = "";
        $status .= gettext("Login Failed") . "<br>";
    }
    print STDERR gettext("ASTPP-USER:") . " $params->{username}\n"
      if $config->{debug} == 1 && $params->{username};
    print STDERR gettext("ASTPP-PASS:") . " $params->{password}\n"
      if $config->{debug} == 1 && $params->{password};
    print STDERR gettext("ASTPP-AUTHCODE:") . " $enh_config->{auth}\n"
      if $config->{debug} == 1;
    print STDERR gettext("ASTPP-USER-COUNT:") . " $count"
      if $config->{debug} == 1;
    print header();
    return $count;
}

sub logout() {
    my ( $cookie, $cookie1 );
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
    $status .= "Successfully Logged User Out" . "<br>";
    return "";
}

sub build_menu_ts() {
    my ($selected) = @_;
    my ( $tmp, $body, $x );
    my $i = 0;
    print STDERR "START OF MENU" if $config->{debug} == 1;
    foreach $tmp (@modes) {
	print STDERR $tmp . "\n";
        $body .=
"<div class=\"ts_ddm\" name=tt$i id=tt$i style=\"visibility:hidden;width:150;background-color:#CCCCFF;\"onMouseover=\"clearhidemenu()\" onMouseout=\"dynamichide(event)\"><table width=100\% border=0 cellspacing=0 cellpadding=0>";
        my $j = 0;
        foreach $x ( @{ $menumap{$tmp} } ) {
            $body .=
"<tr><td name=t$i\_$j id=t$i\_$j><a href=\"?mode=$x\" onmouseover='light_on(t$i\_$j);' onmouseout='light_off(t$i\_$j);'>  $x  </a></td></tr>
\n";
            $j++;
        }
        $body .= "</table>
</div>";
        $i++;
    }
    $body .= "<table width=600 cellpadding=0 class=ts_menu><tr>\n";
    $i = 0;
    foreach $tmp (@modes) {
        $body .=
"<td name=t$i id=t$i><a href=\"?mode=$tmp\"  onmouseover='light_on(t$i);dropdownmenu(this, event,\"tt$i\");' onmouseout='light_off(t$i);delayhidemenu();'> $tmp </a></td>\n";
        $i++;
    }
    $body .= "</tr>
\n</table>
";
    return $body;
}

sub build_body() {
    if ( $params->{logintype} == 2 ) {    #Admin Login
#        return &build_configure()
#          if $params->{mode} eq gettext("Configuration");
        return &build_providers() if $params->{mode} eq gettext("Providers");
        return &build_trunks()    if $params->{mode} eq gettext("Trunks");
        return &build_outbound_routes()
          if $params->{mode} eq gettext("Outbound Routes");
        return &build_pricelists() if $params->{mode} eq gettext("Pricelists");
        return &build_routes()     if $params->{mode} eq gettext("Routes");
#        return &build_resellers()  if $params->{mode} eq gettext("Resellers");
        return &build_calc_charge()
          if $params->{mode} eq gettext("Calc Charge");
        return &build_list_errors()
          if $params->{mode} eq gettext("List Errors");
        return &build_dids() if $params->{mode} eq gettext("Manage DIDs");
	return &build_import_dids() if $params->{mode} eq gettext("Import DIDs");
        return &build_purge_deactivated()
          if $params->{mode} eq gettext("Purge Deactivated");
        return &build_create_account()
          if $params->{mode} eq gettext("Create Account");
        return &build_remove_account()
          if $params->{mode} eq gettext("Remove Account");
        return &build_process_payment()
          if $params->{mode} eq gettext("Process Payment");
        return &build_account_info()
          if $params->{mode} eq gettext("View Details");
        return &build_list_accounts()
          if $params->{mode} eq gettext("List Accounts");
        return &build_edit_account()
          if $params->{mode} eq gettext("Edit Account");
        return &build_import_routes()
          if $params->{mode} eq gettext("Import Routes");
        return &build_import_outbound_routes()
          if $params->{mode} eq gettext("Import Outbound Routes");
        return &logout()         if $params->{mode} eq gettext("Logout");
        return &build_packages() if $params->{mode} eq gettext("Packages");
        return &build_statistics()
          if $params->{mode} eq gettext("Asterisk Stats");
        return &build_create_card() if $params->{mode} eq gettext("Add Cards");
        return &build_list_cards()  if $params->{mode} eq gettext("List Cards");
        return &build_update_card_status()
          if $params->{mode} eq gettext("Update Card(s) Status");
        return &build_reset_card_inuse()
          if $params->{mode} eq gettext("Reset InUse");
        return &build_view_card() if $params->{mode} eq gettext("View Card");
        return &build_cc_brands()
          if $params->{mode} eq gettext("CC Brands");
        return &build_delete_cards()
          if $params->{mode} eq gettext("Delete Card");
        return &build_refill_card()
          if $params->{mode} eq gettext("Refill Card");
        return &build_sip_devices()
          if $params->{mode} eq gettext("SIP Devices");
        return &build_iax_devices()
          if $params->{mode} eq gettext("IAX Devices");
        return &build_dialplan()  if $params->{mode} eq gettext("Dialplan");
        return &build_stats_acd() if $params->{mode} eq gettext("Trunk stats");
        return &build_periodic_charges()
          if $params->{mode} eq gettext("Periodic Charges");
        return &build_view_cdrs() if $params->{mode} eq gettext("View CDRs");
        return &build_homepage()
          if $params->{mode} eq gettext("Home Page")
          || $params->{mode} eq gettext("Home")
          || $params->{mode} eq ""
          || $params->{mode} eq gettext("Login")
          || $params->{mode} eq gettext("Logout");
        return gettext("Not Available!") . "\n";
    }
    elsif ( $params->{logintype} == 3 ) {    #Vendor Login
        return &build_stats_acd()
          if $params->{mode} eq gettext("Trunk Statistics");
        return &logout()          if $params->{mode} eq gettext("Logout");
        return &build_view_cdrs() if $params->{mode} eq gettext("View CDRs");
        return &build_outbound_routes()
          if $params->{mode} eq gettext("Outbound Routes");
        $params->{mode} = gettext("Home");
        return gettext("Welcome to ASTPP!") . "\n"
          if $params->{mode} eq gettext("Home");
    }
    elsif ( $params->{logintype} == 4 ) { #Customer Service Login
        return &build_create_account()
          if $params->{mode} eq gettext("Create Account");
        return &build_remove_account()
          if $params->{mode} eq gettext("Remove Account");
        return &build_process_payment()
          if $params->{mode} eq gettext("Process Payment");
        return &build_account_info()
          if $params->{mode} eq gettext("View Details");
        return &build_list_accounts()
          if $params->{mode} eq gettext("List Accounts");
        return &build_edit_account()
          if $params->{mode} eq gettext("Edit Account");
        return &build_calc_charge()
          if $params->{mode} eq gettext("Calc Charge");
        return &build_dids() if $params->{mode} eq gettext("Manage DIDs");
        return &build_sip_devices()
          if $params->{mode} eq gettext("SIP Devices");
        return &build_iax_devices()
          if $params->{mode} eq gettext("IAX Devices");
        return &build_packages() if $params->{mode} eq gettext("Packages");
        return &build_statistics()
          if $params->{mode} eq gettext("Asterisk Stats");
        return &build_create_card() if $params->{mode} eq gettext("Add Cards");
        return &build_list_cards()  if $params->{mode} eq gettext("List Cards");
        return &build_update_card_status()
          if $params->{mode} eq gettext("Update Card(s) Status");
        return &build_reset_card_inuse()
          if $params->{mode} eq gettext("Reset InUse");
        return &build_view_card() if $params->{mode} eq gettext("View Card");
        return &build_cc_brands()
          if $params->{mode} eq gettext("CC Brands");
        return &build_delete_cards()
          if $params->{mode} eq gettext("Delete Card");
        return &build_refill_card()
          if $params->{mode} eq gettext("Refill Card");

    }
    elsif ( $params->{logintype} == 1 ) {    #Reseller Login
        return &build_pricelists() if $params->{mode} eq gettext("Pricelists");
        return &build_routes()     if $params->{mode} eq gettext("Routes");
        return &build_create_account()
          if $params->{mode} eq gettext("Create Account");
        return &build_remove_account()
          if $params->{mode} eq gettext("Remove Account");
        return &build_process_payment()
          if $params->{mode} eq gettext("Process Payment");
        return &build_account_info()
          if $params->{mode} eq gettext("View Details");
        return &build_list_accounts()
          if $params->{mode} eq gettext("List Accounts");
        return &build_edit_account()
          if $params->{mode} eq gettext("Edit Account");
        return &build_calc_charge()
          if $params->{mode} eq gettext("Calc Charge");
        return &build_import_routes()
          if $params->{mode} eq gettext("Import Routes");
        return gettext("Not Available!") . "\n";
    }
    else {
        $params->{mode} = gettext("Home");
        return gettext("Not Available!") . "\n";
    }
}

sub build_purge_deactivated() {
    my ( $status, $body );
    return gettext("Cannot drop items until database is configured")
      unless $astpp_db;
    if ( $params->{action} eq gettext("Yes, Drop Them") ) {
        if ( $astpp_db->do("DELETE FROM outbound_routes WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated outbound routes.") . "<br>";
        }
        else {
            $status .=
              gettext("Unable to drop deactivated outbound routes.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM routes WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated routes.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated routes.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM accounts WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated accounts.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated accounts.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM pricelists WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated pricelists.") . "<br>";
        }
        else {
            $status .=
              gettext("Unable to drop deactivated pricelists.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM cdrs WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated cdrs.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated cdrs.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM trunks WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated trunks.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated trunks.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM dids WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated DIDs.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated DIDs.") . "<br>";
        }
        if ( $astpp_db->do("DELETE FROM packagess WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated packages.") . "<br>";
        }
        else {
            $status .= gettext("Unable to drop deactivated packages.") . "<br>";
        }
    }
    $body = start_form();
    $body .= "<TABLE><tr><td> $status </td></tr>
\n";
    $body .= "<tr><td>"
      . gettext(
        "Remove records in your system that have been marked as deactivated.")
      . "</td></tr>
\n";
    if ( $params->{action} eq gettext("Drop Deactivated Records") ) {
        $body .= "<tr><td>"
          . hidden( -name => "mode", -value => gettext("Purge Deactivated") )
          . submit( -name => "action", -value => gettext("Yes, Drop Them") )
          . "</td><td>"
          . submit( -name => "action", -value => gettext("Cancel") )
          . "</td></tr>

</table>
";
    }
    else {
        $body .= "<tr><td>"
          . hidden( -name => "mode", -value => gettext("Purge Deactivated") )
          . submit(
            -name  => "action",
            -value => gettext("Drop Deactivated Records")
          )
          . "</td><td>"
          . submit( -name => "action", -value => gettext("Cancel") )
          . "</td></tr>

</table>
";
    }
    return $body;
}
################ Stats stuff ######################
sub build_stats_acd() {
    my ( $body, $id );
    return gettext("Cannot display stats until database is configured")
      unless $astpp_db;
    $cdr_db = &cdr_connect_db( $config, $enh_config, @output );
    return gettext("Cannot display stats until database is configured")
      unless $cdr_db;
    my ( undef, undef, undef, $day, $mnth, $yr ) = localtime();
    my (
        $sd_yr, $sd_mnth, $sd_day, $st_hr, $st_min, $st_sec,
        $ed_yr, $ed_mnth, $ed_day, $et_hr, $et_min, $et_sec
    );
    my $count = 0;
    if ( param('sd_mnth') eq "" ) {
        $sd_yr   = $yr + 1900;
        $sd_mnth = sprintf( "%02d", $mnth + 1 );
        $sd_day  = "01";
        $st_hr   = "00";
        $st_min  = "00";
        $st_sec  = "00";
        $ed_yr   = $yr + 1900;
        $ed_mnth = sprintf( "%02d", $mnth + 1 );
        $ed_day  = $day;
        $et_hr   = "23";
        $et_min  = "59";
        $et_sec  = "59";
    }
    else {
        $sd_yr   = param('sd_yr');
        $sd_mnth = sprintf( "%02d", param('sd_mnth') + 1 );
        $sd_day  = sprintf( "%02d", param('sd_day') );
        $st_hr   = sprintf( "%02d", param('st_hr') );
        $st_min  = sprintf( "%02d", param('st_min') );
        $st_sec  = sprintf( "%02d", param('st_sec') );
        $ed_yr   = param('ed_yr');
        $ed_mnth = sprintf( "%02d", param('ed_mnth') + 1 );
        $ed_day  = sprintf( "%02d", param('ed_day') );
        $et_hr   = sprintf( "%02d", param('et_hr') );
        $et_min  = sprintf( "%02d", param('et_min') );
        $et_sec  = sprintf( "%02d", param('et_sec') );
    }
    $body .=
        "<form method=get><input type=hidden name=mode value=\""
      . param('mode')
      . "\"><table class=\"default\" width=100%>";
    $body .=
        "<tr><td width=50%>"
      . gettext("Start date:")
      . "</td><td><input type=text name=sd_yr value=\"$sd_yr\" size=5><select name=sd_mnth>";
    for ( $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $sd_mnth - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
    }
    $body .=
"</select><input type=text name=sd_day value=\"$sd_day\" size=3></td></tr>

";
    $body .= "<tr><td>"
      . gettext("Start time:")
      . "</td><td><input type=text name=st_hr value=\"$st_hr\" size=3><input type=text name=st_min value=\"$st_min\" size=3><input type=text name=st_sec value=\"$st_sec\" size=3></td></tr>
";
    $body .= "<tr><td>"
      . gettext("End date:")
      . "</td><td><input type=text name=ed_yr value=\"$ed_yr\" size=5><select name=ed_mnth>";
    for ( $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $ed_mnth - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
    }
    $body .=
"</select><input type=text name=ed_day value=\"$ed_day\" size=3></td></tr>
";
    $body .= "<tr><td>"
      . gettext("End time:")
      . "</td><td><input type=text name=et_hr value=\"$et_hr\" size=3><input type=text name=et_min value=\"$et_min\" size=3><input type=text name=et_sec value=\"$et_sec\" size=3></td></tr>
";
    $body .=
"<tr><td align=center colspan=2><input type=submit value=Filter!></td></tr>
";
    $body .= "</table>
</form>";
    my $sd = "$sd_yr-$sd_mnth-$sd_day $st_hr:$st_min:$st_sec";
    my $ed = "$ed_yr-$ed_mnth-$ed_day $et_hr:$et_min:$et_sec";
    my $sth;

    if ( $params->{logintype} == 3 ) {
        $sth =
          $astpp_db->prepare( "SELECT * FROM trunks WHERE provider = "
              . $astpp_db->quote( $params->{username} ) );
    }
    else {
        $sth = $astpp_db->prepare("SELECT * FROM trunks");
    }
    $sth->execute
      || return gettext("Something is wrong with the trunks database") . "\n";
    $body .=
        start_form()
      . "<table class=\"default\" width=100%><tr><td colspan=5 align=center><b>$sd - $ed</b></td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Trunk Name")
      . "</td><td>"
      . gettext("Calls")
      . "</td><td>"
      . "<acronym title=\""
      . gettext("Average Call Duration") . "\">"
      . gettext("ACD")
      . "</acronym>"
      . "</td><td>"
      . "<acronym title=\""
      . gettext("Average Call Wait Time") . "\">"
      . gettext("ACWT")
      . "</acronym>"
      . "</td><td>"
      . gettext("Success")
      . "</td><td>"
      . gettext("Congestion")
      . "</td></tr>
\n";

    while ( my $row = $sth->fetchrow_hashref ) {
        $count++;
        my $sql1 = " SELECT COUNT(
			  *) as calls,
			  AVG(billsec) as bs,
			  AVG( duration-billsec ) as acwt from cdr where lastapp = 'Dial'
				and disposition REGEXP '^ANSWERED$'
				and calldate >= " . $cdr_db->quote($sd) . "
				and calldate <= " . $cdr_db->quote($ed) . "
				and (dstchannel like '$row->{tech}/$row->{path}%'
				  or dstchannel like '$row->{tech}\[$row->{path}\]%' ) ";
        print STDERR " SQL: $sql1 \n " if $config->{debug} == 1;
        my $sth1 = $cdr_db->prepare($sql1);
        $sth1->execute();
        my $ref1 = $sth1->fetchrow_hashref();
        $sth1->finish;
        my $sql2 = " select count(
					  *) as ct from cdr where calldate >=
						" . $cdr_db->quote($sd) . "
						and calldate <= " . $cdr_db->quote($ed) . "
						and disposition not in(
						  'ANSWERED','16'
						)
						and (dstchannel like '$row->{tech}/$row->{path}%'
						  or dstchannel like '$row->{tech}\[$row->{path}\]%' )
						";
        my $sth2 = $cdr_db->prepare($sql2);
        print STDERR " SQL: $sql2 \n " if $config->{debug} == 1;
        $sth2->execute();
        my $ref2 = $sth2->fetchrow_hashref();
        $sth2->finish;
        my $sql3 = " select count(
					  *) as ct from cdr where calldate >=
						" . $cdr_db->quote($sd) . "
						and calldate <= " . $cdr_db->quote($ed) . "
						and disposition REGEXP '^CONGESTION$'
						  and (dstchannel like '$row->{tech}/$row->{path}%'
						  or dstchannel like '$row->{tech}\[$row->{path}\]%' )
						";
        my $sth3 = $cdr_db->prepare($sql3);
        print STDERR " SQL: $sql3 \n " if $config->{debug} == 1;
        $sth3->execute();
        my $ref3 = $sth3->fetchrow_hashref();
        $sth3->finish;
        my $sql4 = " select count(
					  *) as ct from cdr where calldate >=
						" . $cdr_db->quote($sd) . "
						and calldate <= " . $cdr_db->quote($ed) . "
						  and (dstchannel like '$row->{tech}/$row->{path}%'
						  or dstchannel like '$row->{tech}\[$row->{path}\]%' )
						";
        my $sth4 = $cdr_db->prepare($sql4);
        print STDERR " SQL: $sql4 \n " if $config->{debug} == 1;
        $sth4->execute();
        my $ref4 = $sth4->fetchrow_hashref();
        $sth4->finish;
        my $success_rate    = 0;
        my $congestion_rate = 0;

        if ( $ref4->{ct} > 0 && $ref1->{calls} > 0 ) {
            $success_rate = ( $ref1->{calls} / $ref4->{ct} ) * 100;
        }
        if ( $ref4->{ct} > 0 && $ref3->{ct} > 0 ) {
            $congestion_rate = ( $ref3->{ct} / $ref4->{ct} ) * 100;
        }
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
"<td>$row->{tech}/$row->{path}</td><td align=right>$ref4->{ct}</td><td align=right>"
          . "$ref1->{bs}</td><td align=right>$ref1->{acwt}</td><td align=right>"
          . "$ref1->{calls}  "
          . sprintf( "%.04f", $success_rate )
          . " %</td><td>"
          . "$ref3->{ct}  "
          . sprintf( "%.04f", $congestion_rate )
          . "%</td></tr>
\n";
    }
    $sth->finish;
    $body .= "</table>
";
    return $body;
}
################ Stats stuff ######################
sub build_view_cdrs() {
    my (
        @trunklist, $body,   $id,      $tmp,
        $row,       $sql,    $results, $pagesrequired,
        $pageno,    $string, $sd_month
    );
    return gettext("Cannot display stats until database is configured")
      unless $astpp_db;
    $cdr_db = &cdr_connect_db( $config, $enh_config, @output );
    return gettext("Cannot display stats until database is configured")
      unless $cdr_db;
    if ( $params->{limit} < 1 || !$params->{limit} ) { $params->{limit} = 0 }
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    my ( undef, undef, undef, $day, $mnth, $yr ) = localtime();

    if ( $params->{logintype} == 3 ) {
        my $sql =
          $astpp_db->prepare( "SELECT * FROM trunks WHERE provider = "
              . $astpp_db->quote( $params->{username} ) );
        $sql->execute;
        while ( my $record = $sql->fetchrow_hashref ) {
            push @trunklist, $record->{name};
        }
        $sql->finish;
    }
    else {
        @trunklist = &list_trunks($astpp_db);
        push( @trunklist, "" );
    }
    @trunklist = sort @trunklist;
    my (
        $sd_yr, $sd_mnth, $sd_day, $st_hr, $st_min, $st_sec,
        $ed_yr, $ed_mnth, $ed_day, $et_hr, $et_min, $et_sec
    );
    my $count = 0;

    if ( $params->{sd_mnth} eq "" ) {
        $sd_yr   = $yr + 1900;
        $sd_mnth = sprintf( "%02d", $mnth + 1 );
        $sd_day  = "01";
        $st_hr   = "00";
        $st_min  = "00";
        $st_sec  = "00";
        $ed_yr   = $yr + 1900;
        $ed_mnth = sprintf( "%02d", $mnth + 1 );
        $ed_day  = $day;
        $et_hr   = "23";
        $et_min  = "59";
        $et_sec  = "59";
    }
    else {
        $sd_yr   = $params->{sd_yr};
        $sd_mnth = sprintf( "%02d", param('sd_mnth') + 1 );
        $sd_day  = sprintf( "%02d", param('sd_day') );
        $st_hr   = sprintf( "%02d", param('st_hr') );
        $st_min  = sprintf( "%02d", param('st_min') );
        $st_sec  = sprintf( "%02d", param('st_sec') );
        $ed_yr   = param('ed_yr');
        $ed_mnth = sprintf( "%02d", param('ed_mnth') + 1 );
        $ed_day  = sprintf( "%02d", param('ed_day') );
        $et_hr   = sprintf( "%02d", param('et_hr') );
        $et_min  = sprintf( "%02d", param('et_min') );
        $et_sec  = sprintf( "%02d", param('et_sec') );
    }
    $body .=
        "<form method=get><input type=hidden name=mode value=\""
      . param('mode')
      . "\"><table class=\"default\" width=100%>"
      . "<tr><td width=50%>"
      . gettext("Start date:")
      . "</td><td><input type=text name=sd_yr value=\"$sd_yr\" size=5><select name=sd_mnth>";
    for ( $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $sd_mnth - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
    }
    $body .=
        "</select><input type=text name=sd_day value=\"$sd_day\" size=3>"
      . "</td></tr>
<tr><td>"
      . gettext("Start time:")
      . "</td><td><input type=text name=st_hr value=\"$st_hr\" size=3>"
      . "<input type=text name=st_min value=\"$st_min\" size=3>"
      . "<input type=text name=st_sec value=\"$st_sec\" size=3></td></tr>
"
      . "<tr><td>"
      . gettext("End date:")
      . "</td><td><input type=text name=ed_yr value=\"$ed_yr\" size=5><select name=ed_mnth>";
    for ( $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $ed_mnth - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
    }
    $body .=
"</select><input type=text name=ed_day value=\"$ed_day\" size=3></td></tr>
"
      . "<tr><td>"
      . gettext("End time:")
      . "</td><td><input type=text name=et_hr value=\"$et_hr\" size=3>"
      . "<input type=text name=et_min value=\"$et_min\" size=3>"
      . "<input type=text name=et_sec value=\"$et_sec\" size=3></td></tr>
"
      . "<tr><td>"
      . gettext("Answered Calls Only?")
      . popup_menu(
        -name   => "answered",
        -values => \%yesno
      )
      . "</td><td>";
    if ( $params->{logintype} == 2 ) {
        $body .= gettext("AccountCode:")
          . textfield(
            -name  => "accountcode",
            -width => 8
          );
    }
    $body .= "</td></tr>
" . "<tr><td>" . gettext("Select Outbound Trunk?")
      . popup_menu(
        -name   => "trunk",
        -values => \@trunklist
      )
      . "</td></tr>
"
      . "<tr><td align=center colspan=2><input type=submit value=Filter!></td></tr>
"
      . "</table>
</form>";
    my $sd = "$sd_yr-$sd_mnth-$sd_day $st_hr:$st_min:$st_sec";
    my $ed = "$ed_yr-$ed_mnth-$ed_day $et_hr:$et_min:$et_sec";
    $body .=
        start_form()
      . "<table class=\"viewcdrs\" width=100%><tr><td colspan=5 align=center><b>$sd - $ed</b></td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Date")
      . "</td><td>"
      . gettext("CallerID") . "</td>" . "<td>"
      . gettext("Source")
      . "</td><td>"
      . gettext("Dest") . "</td>" . "<td>"
      . gettext("D.Context")
      . "</td><td>"
      . gettext("Chan") . "</td>" . "<td>"
      . gettext("D.Chan")
      . "</td><td>"
      . gettext("Last App")
      . " </td>" . "<td>"
      . gettext("Last Data")
      . "</td><td>"
      . gettext("Duration") . "</td>" . "<td>"
      . gettext("BillSec")
      . "</td><td>"
      . gettext("Disposition") . "</td>" . "<td>"
      . gettext("AMAFlags")
      . "</td><td>";
    if ( $params->{logintype} == 2 ) {
        $body .= gettext("AccountCode");
    }
    $body .=
        "</td>" . "<td>"
      . gettext("U-ID")
      . "</td><td>"
      . gettext("UserField") . "</td>" . "<td>"
      . gettext("Cost")
      . "</td></tr>
\n";
    if ( $params->{answered} == 1 ) {
        $tmp =
            " SELECT * from cdr where disposition = 'ANSWERED'"
          . " and calldate >= "
          . $cdr_db->quote($sd)
          . " and calldate <= "
          . $cdr_db->quote($ed);
        print STDERR " SQL: $tmp \n " if $config->{debug} == 1;
    }
    else {
        $tmp =
            " SELECT * from cdr where calldate >= "
          . $cdr_db->quote($sd)
          . " and calldate <= "
          . $cdr_db->quote($ed);
        print STDERR " SQL: $tmp \n " if $config->{debug} == 1;
    }
    if ( $params->{accountcode} && $params->{logintype} == 2 ) {
        $tmp .=
          " and accountcode = " . $cdr_db->quote( $params->{accountcode} );
    }
    if ( $params->{trunk} ) {
        my $tmpsql =
            "SELECT * FROM trunks WHERE name = "
          . $astpp_db->quote( $params->{trunk} )
          . " LIMIT 1";
        print STDERR $tmpsql if $config->{debug} == 1;
        $sql = $astpp_db->prepare($tmpsql);
        $sql->execute
          || return gettext("Something is wrong with the trunks database")
          . "\n";
        $row = $sql->fetchrow_hashref;
        $sql->finish;
        $tmp .=
            " and (dstchannel like '$row->{tech}/$row->{path}\%'"
          . " or dstchannel like '$row->{tech}\[$row->{path}\]\%')";
    }
    print STDERR $tmp if $config->{debug} == 1;
    $sql = $cdr_db->prepare($tmp);
    $sql->execute;
    $results       = $sql->rows;
    $pagesrequired = ceil( $results / $results_per_page );
    $sql->finish;
    $tmp .= " limit $params->{limit} , $results_per_page";
    $sql = $cdr_db->prepare($tmp);
    $sql->execute;

    while ( my $record = $sql->fetchrow_hashref ) {
        $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        my $dcontext   = substr( $record->{dcontext}, 0, 4 ) . "..";
        my $channel    = substr( $record->{channel},  0, 9 ) . "..";
        my $dstchannel = substr( $record->{channel},  0, 9 ) . "..";
        my $lastdata   = substr( $record->{lastdata}, 0, 4 ) . "..";
        if ( $params->{logintype} != 2 ) {
            $record->{accountcode} = "";
            $record->{cost}        = "";
        }
        $body .=
"<td>$record->{calldate}</td><td>$record->{clid}</td><td>$record->{src}</td>"
          . "<td>$record->{dst}</td>"
          . "<td><acronym title=\"$record->{dcontext}\">$dcontext</acronym></td>"
          . "<td><acronym title=\"$record->{channel}\">$channel</acronym></td>"
          . "<td><acronym title=\"$record->{dstchannel}\">$dstchannel</acronym></td>"
          . "<td>$record->{lastapp}</td>"
          . "<td><acronym title=\"$record->{lastdata}\">$lastdata</acronym></td>"
          . "<td>$record->{duration}</td><td>$record->{billsec}</td><td>$record->{disposition}</td>"
          . "<td>$record->{amaflags}</td><td>$record->{accountcode}</td>"
          . "<td><acronym title=\"$record->{uniqueid}\">...</acronym></td>"
          . "<td>$record->{userfield}</td><td>$record->{cost}</td>"
          . "<td bgcolor=white>";
        $body .= "</td></tr>
\n";
    }
    $sql->finish;
    $sd_month = $sd_mnth - 1;
    $string   =
        "&sd_yr=" . $sd_yr
      . "&sd_mnth="
      . $sd_month
      . "&sd_day="
      . $sd_day
      . "&st_hr="
      . $st_hr
      . "&st_min="
      . $st_min
      . "&st_sec="
      . $st_sec
      . "&ed_yr="
      . $ed_yr
      . "&ed_mnth="
      . $ed_mnth
      . "&ed_day="
      . $ed_day
      . "&et_hr="
      . $et_hr
      . "&et_min="
      . $et_min
      . "&et_sec="
      . $et_sec
      . "&trunk="
      . $params->{trunk};
    for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
        if ( $i == 0 ) {
            if ( $params->{limit} != 0 ) {
                $body .=
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("View CDRs")
                  . "&action="
                  . gettext("Information...")
                  . "&accountcode="
                  . $params->{accountcode}
                  . $string
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
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("View CDRs")
                  . "&action="
                  . gettext("Information...")
                  . "&accountcode="
                  . $params->{accountcode}
                  . $string
                  . "&limit=";
                $body .= ( $i * $results_per_page );
                $body .= "\">\n";
                $body .= $i + 1 . "</a>";
            }
            else {
                $pageno = $i + 1;
                $body .= " |";
            }
        }
    }
    $body .= "";
    $body .= "Page $pageno of $pagesrequired";
    $body .= "</table>
";
    return $body;
}

sub build_homepage() {
    my $total_balance          = &accounts_total_balance($astpp_db) if $astpp_db;
    my $regular_account_count  = &count_accounts( $astpp_db, "WHERE type = 0" ) if $astpp_db;
    my $reseller_account_count = &count_accounts( $astpp_db, "WHERE type = 1" ) if $astpp_db;
    my $admin_account_count    = &count_accounts( $astpp_db, "WHERE type = 2" ) if $astpp_db;
    my $vendor_account_count   = &count_accounts( $astpp_db, "WHERE type = 3" ) if $astpp_db;
    my $total_dids             = &count_dids( $astpp_db, "" ) if $astpp_db;
    my $unbilled_cdrs          = &count_unbilled_cdrs($cdr_db) if $cdr_db;
    my $calling_cards_inuse    =
      &count_callingcards( $astpp_db, "WHERE inuse =1 AND status = 1" ) if $astpp_db;
    my $body =
      gettext("Welcome to ASTPP - The Open Source Voip Billing Solution")
      . "<br>";
    $body .= gettext("Please select a function from the menu above.") . "<br>";
    $body .=
      gettext(
"For more information and help please visit the <a href=\"http://www.astpp.org\">ASTPP wiki.</a>."
      );
    $body .= start_form
      . "<table class=\"default\"><tr class=\"header\"><td colspan=4>"
      . gettext("System Overview")
      . "</td></tr>
"
      . "<tr class=\"header\"><td colspan=4>"
      . gettext("Account Counts")
      . "</td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Customers")
      . "</td><td>"
      . gettext("Resellers")
      . "</td><td>"
      . gettext("Vendors")
      . "</td><td>"
      . gettext("Admins")
      . "</td></tr>
"
      . "<tr class=\"rowone\"><td>"
      . $regular_account_count
      . "</td><td>"
      . $reseller_account_count
      . "</td><td>"
      . $vendor_account_count
      . "</td><td>"
      . $admin_account_count
      . "</td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Total Funds Owing")
      . "</td><td>"
      . gettext("Calling Cards in use")
      . "</td><td>"
      . gettext("DIDs")
      . "</td><td>"
      . gettext("Unbilled CDRS")
      . "</td></tr>
"
      . "<tr class=\"rowone\"><td> \$"
      . sprintf( "%.2f", $total_balance / 10000 )
      . "</td><td>"
      . $calling_cards_inuse
      . "</td><td>"
      . $total_dids
      . "</td><td>"
      . $unbilled_cdrs
      . "</td></tr>
"
      . "</table>
";
    return $body;
}

sub build_account_info() {
    my (
        $total,         $body,             $status,  $description,
        $pricelists,     $chargeid,         $tmp,     $number,
        $pagesrequired, $results_per_page, $results, $pageno
    );
    return gettext("Cannot view account until database is configured")
      unless $astpp_db;
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
    return gettext("Cannot view account until pricelists configured")
      unless @pricelists;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ($params->{logintype} == 1) {
        $reseller = $params->{username};
    } else {
        $reseller = "";
    }
    my @accountlist = &list_accounts_selective($astpp_db,$reseller,"-1");
    push( @accountlist, "" );
    my $count = 0;
    if ( $params->{accountnum} && !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    $body = start_form
      . "<table class=\"default\"><tr class=\"header\"><td>"
      . gettext("Either select or enter the account number:")
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
"
      . "<tr class=\"rowone\"><td>"
      . hidden(
        -name  => "mode",
        -value => gettext("Accounts")
      )
      . popup_menu(
        -name   => "numberlist",
        -values => \@accountlist
      )
      . textfield(
        -name    => 'accountnum',
        -size    => 20,
        -default => $params->{accountnum}
      )
      . "</td><td>"
      . submit(
        -name  => "action",
        -value => gettext("Information...")
      )
      . "</td></tr";
    if ( $params->{action} eq gettext("Post Charge...") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        my $timestamp = &prettytimestamp;
        if ($params->{logintype} == 1) {
                $accountinfo = &get_account($astpp_db, $number);
                if ($accountinfo->{reseller} eq $params->{username}) {
        		&write_account_cdr( $astpp_db, $number, $params->{amount} * 10000,
            			$params->{desc}, $timestamp, 0 );
		}
	} else {
        &write_account_cdr( $astpp_db, $number, $params->{amount} * 10000,
            $params->{desc}, $timestamp, 0 );
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add Charge...") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        if ( $params->{id} ne "" ) {
            $chargeid = $params->{id};
        }
        else {
            $chargeid = $params->{id_list};
        }
        if ($params->{logintype} == 1) {
                $accountinfo = &get_account($astpp_db, $number);
                if ($accountinfo->{reseller} eq $params->{username}) {
        $tmp =
            "INSERT INTO charge_to_account (charge_id,cardnum,status) VALUES ("
          . $astpp_db->quote($chargeid) . ", "
          . $astpp_db->quote($number) . ", " . "1)";
        $astpp_db->do($tmp);
		}
	} else {
        $tmp =
            "INSERT INTO charge_to_account (charge_id,cardnum,status) VALUES ("
          . $astpp_db->quote($chargeid) . ", "
          . $astpp_db->quote($number) . ", " . "1)";
        $astpp_db->do($tmp);
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Remove Charge") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        if ($params->{logintype} == 1) {
                $accountinfo = &get_account($astpp_db, $number);
                if ($accountinfo->{reseller} eq $params->{username}) {
        $tmp =
          "DELETE FROM charge_to_account WHERE id = "
          . $astpp_db->quote( $params->{chargeid} );
        $astpp_db->do($tmp);
	}
	} else {
        $tmp =
          "DELETE FROM charge_to_account WHERE id = "
          . $astpp_db->quote( $params->{chargeid} );
        $astpp_db->do($tmp);
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Purchase DID") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $tmp =
            "UPDATE dids SET account = "
          . $astpp_db->quote($number)
          . " WHERE number = "
          . $astpp_db->quote( $params->{did_list} );
        $astpp_db->do($tmp);
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Map ANI") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $tmp =
            "INSERT INTO ani_map (number,account) VALUES ("
          . $astpp_db->quote( $params->{ANI} ) . ", "
          . $astpp_db->quote($number) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("ANI") . " '"
              . $params->{ANI} . "' "
              . gettext("has been added!") . "<br>";
        }
        else {
            $status .=
                gettext("ANI") . " '"
              . $params->{ANI} . "' "
              . gettext("FAILED to create!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Remove ANI") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $tmp =
            "DELETE FROM ani_map WHERE number = "
          . $astpp_db->quote( $params->{ANI} )
          . " AND account = "
          . $astpp_db->quote($number);
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("ANI") . " '"
              . $params->{ANI} . "' "
              . gettext("has been dropped!") . "<br>";
        }
        else {
            $status .=
                gettext("ANI") . " '"
              . $params->{ANI} . "' "
              . gettext("FAILED to remove!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Map IP") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $tmp =
            "INSERT INTO ip_map (ip,account) VALUES ("
          . $astpp_db->quote( $params->{ip} ) . ", "
          . $astpp_db->quote($number) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("IP") . " '"
              . $params->{ip} . "' "
              . gettext("has been added!") . "<br>";
        }
        else {
            $status .=
                gettext("IP") . " '"
              . $params->{ip} . "' "
              . gettext("FAILED to create!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Remove IP") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $tmp =
            "DELETE FROM ip_map WHERE ip = "
          . $astpp_db->quote( $params->{ip} )
          . " AND account = "
          . $astpp_db->quote($number);
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("IP") . " '"
              . $params->{ip} . "' "
              . gettext("has been dropped!") . "<br>";
        }
        else {
            $status .=
                gettext("IP") . " '"
              . $params->{ip} . "' "
              . gettext("FAILED to remove!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Information...") ) {
        my ($balance);
        if ( $number ne "" ) {
            $accountinfo = &get_account( $astpp_db, $number );
        }
        elsif ( $params->{accountnum} ne "" ) {
            $accountinfo = &get_account( $astpp_db, $params->{accountnum} );
        }
        else {
            $accountinfo = &get_account( $astpp_db, $params->{numberlist} );
        }
        $balance = &accountbalance( $astpp_db, $accountinfo->{number} );
        $balance = sprintf( "%.2f", $balance / 10000 );
        if ( $accountinfo->{number} ) {
            $status .=
                "<table class=\"default\"><tr><td colspan=2>"
              . $accountinfo->{first_name} . " "
              . $accountinfo->{middle_name} . " "
              . $accountinfo->{last_name}
              . "</td></tr>
<tr><td width=400>"
              . $accountinfo->{company_name}
              . "</td><td>Phone: "
              . $accountinfo->{telephone_1}
              . "</td></tr>
<tr><td>"
              . $accountinfo->{address_1}
              . "</td><td>Phone 2: "
              . $accountinfo->{telephone_2}
              . "</td></tr>
<tr><td>"
              . $accountinfo->{address_2}
              . "</td><td>Facsimile: "
              . $accountinfo->{fascimilie}
              . "</td></tr>
<tr><td>"
              . $accountinfo->{address_3}
              . "</td><td>Email: "
              . $accountinfo->{email}
              . "</td></tr>
<tr><td colspan=2>"
              . $accountinfo->{city} . ", "
              . $accountinfo->{postal_code} . ", "
              . $accountinfo->{country}
              . "</td></tr>
</table";
            $status .=
                "<br>Account&nbsp;&nbsp;</i><b>"
              . $accountinfo->{'number'}
              . "&nbsp;&nbsp;</b><i>"
              . gettext("balance: ")
              . "</i><b>$balance $currency[0] </b></i>"
              . gettext("Max Channels:") . " "
              . $accountinfo->{maxchannels} . "\n";
            $status .=
              gettext("with a credit limit of")
              . " </i><b>\$$accountinfo->{'credit_limit'} $currency[0]</b></i>\n";
        }
        else {
            $status .=
                gettext("No such account number")
              . " '$accountinfo->{number}' "
              . gettext("found!") . "\n";
        }
        $body .=
           "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=5>"
          . gettext("Charges")
          . "</td></tr>"
          . "<tr class=\"header\"><td>"
          . gettext("Action")
          . "</td><td>"
          . gettext("Id")
          . "</td><td>"
          . gettext("Description")
          . "</td><td>"
          . gettext("Cycle")
          . "</td><td>"
          . gettext("Amount")
          . "</td></tr>
";
        my @account_charge_list =
          &list_account_charges( $astpp_db, $accountinfo->{number} );
        my @pricelist_charge_list =
          &list_pricelist_charges( $astpp_db, $accountinfo->{pricelist} );
        foreach my $charge (@account_charge_list) {
            my ( $sweep, $cost );
            $count++;
            print STDERR "CHARGE_ID $charge->{charge_id}\n"
              if $config->{debug} == 1;
            my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
            print STDERR "SWEEP: " . $chargeinfo->{sweep}
              if $config->{debug} == 1;
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
                "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("View Details")
              . "&chargeid="
              . $charge->{id}
              . "&accountnum="
              . $accountinfo->{number}
              . "&action="
              . gettext("Remove Charge") . "\">"
              . gettext("Remove Charge") . "</a>"
              . "</td><td>"
              . $charge->{id}
              . "</td><td>"
              . $chargeinfo->{description}
              . "</td><td>"
              . $sweep
              . "</td><td> \$"
              . $cost
              . "</td></tr>
";
        }
        foreach my $charge (@pricelist_charge_list) {
            my ($cost);
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
              . "</td></tr>
";
        }
        my %applyablecharges = &list_applyable_charges($astpp_db);
       $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
	  "<td colspan=2>"
          . popup_menu(
            -name   => "id_list",
            -values => \%applyablecharges
          )
          . "</td><td>"
          . textfield( -name => "id", -size => 5 )
          . "</td><td colspan=2>"
          . submit(
            -name  => "action",
            -value => gettext("Add Charge...")
          )
	  . "</td></tr></table>";
	$count = 0;
        $body .=
           "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("DIDs")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Number")
          . "</td><td>"
          . gettext("Monthly Fee")
          . "</td></tr>
";
        my @did_list =
          &list_dids_account( $astpp_db, $accountinfo->{'number'} );
        foreach my $did_info (@did_list) {
            my $cost = sprintf( "%.2f", $did_info->{monthlycost} / 10000 );
       $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
            $body .= "<td>"
              . $did_info->{number}
              . "</td><td> \$"
              . $cost
              . "</td></tr>
";
        }
        my @availabledids = &list_dids_number_account( $astpp_db, "" );
       $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
          "<td>"
          . popup_menu(
            -name   => "did_list",
            -values => \@availabledids
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Purchase DID")
          )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
	  . gettext("ANI Mapping")
          . "</td></tr>"
          . "<tr class=\"header\"><td>"
          . gettext("Action")
          . "</td><td>"
          . gettext("ANI")
          . "</td></tr>
\n";
        my $tmp =
            "SELECT number FROM ani_map WHERE account = "
          . $astpp_db->quote( $accountinfo->{'number'} )
          . " ORDER BY number";
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
            $body .=
                "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("View Details")
              . "&accountnum=$accountinfo->{'number'}"
              . "&ANI=$record->{number}&action="
              . gettext("Remove ANI") . " \">"
              . gettext("Remove ANI") . "</a>"
              . "<td>$record->{number}</td></tr>
";
        }
       $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }

        $body .=
          "<td>"
          . textfield(
            -name  => "ANI",
            -width => 20
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Map ANI")
          )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
	  . gettext("IP Address Mapping")
          . "</td></tr>"
          . "<tr class=\"header\"><td>"
          . gettext("Action")
          . "</td><td>"
          . gettext("IP")
          . "</td></tr>
\n";
        $tmp =
            "SELECT ip FROM ip_map WHERE account = "
          . $astpp_db->quote( $accountinfo->{'number'} )
          . " ORDER BY ip";
        $sql = $astpp_db->prepare($tmp);
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
            $body .=
                "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("View Details")
              . "&accountnum=$accountinfo->{'number'}"
              . "&ip=$record->{ip}&action="
              . gettext("Remove IP") . " \">"
              . gettext("Remove IP") . "</a>"
              . "<td>$record->{ip}</td></tr>
";
        }
       $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
          "<td>"
          . textfield(
            -name  => "ip",
            -width => 16
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Map IP")
          )
          . "</td></tr>
"
          . "<tr class=\"header\">"
          . "<td colspan=2>"
          . gettext("Post Charge to Account")
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Description")
          . "</td><td>"
          . gettext("Charge in: ")
          . $currency[0]
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . textfield(
            -name  => "desc",
            -width => 16
          )
          . "</td><td>"
          . textfield(
            -name  => "amount",
            -width => 16
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Post Charge...")
          )
          . "</td></tr>
"
          . "</table>
"
          . $status
          . "<br><table class=\"default\">"
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
          . "</td></tr>
\n";
        $tmp =
            "SELECT * FROM cdrs WHERE cardnum ="
          . $astpp_db->quote( $accountinfo->{'number'} )
          . "and status IN (NULL, '', 0, 1)"
          . " ORDER BY callstart DESC";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        $results       = $sql->rows;
        $pagesrequired = ceil( $results / $results_per_page );
        $tmp           =
            "SELECT * FROM cdrs WHERE cardnum ="
          . $astpp_db->quote( $accountinfo->{'number'} )
          . "and status IN (NULL, '', 0, 1)"
          . " ORDER BY callstart DESC "
          . " limit $params->{limit} , $results_per_page";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        $count = 0;

        while ( my $record = $sql->fetchrow_hashref ) {
            my ( $debit, $credit );
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
              . "$debit</td><td>$credit</td><td>$record->{notes}</td></tr>
";
        }
    }
    $body .= "</table>
";
    for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
        if ( $i == 0 ) {
            if ( $params->{limit} != 0 ) {
                $body .=
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("View Details")
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
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("View Details")
                  . "&action="
                  . gettext("Information...")
                  . "&accountnum="
                  . $accountinfo->{number}
                  . "&limit=";
                $body .= ( $i * $results_per_page );
                $body .= "\">\n";
                $body .= $i + 1 . "</a>";
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

sub build_list_accounts() {
    my ( $total, $count, $reseller );
    my $yes = gettext("YES");
    my $no  = gettext("NO");
    return gettext("Cannot view account until database is configured")
      unless $astpp_db;
    my $accountcount = 0;
    my $totalbalance = 0;
    if ($params->{logintype} == 1) {
	$reseller = $params->{username};
    } else {
	$reseller = "";
    }
#    if ( !$params->{type} ) {
#        $params->{type} = -1;
#    }
    $types{-1} = gettext("All");
    my $body = start_form
      . "<table class=\"default\"><tr class=\"header\"><td colspan=9>"
      . hidden(
        -name  => "mode",
        -value => gettext("List Accounts")
      )
      . radio_group(
        -name      => "type",
        -default   => $params->{type},
        -linebreak => 'true',
        -columns   => 8,
        -rows      => 1,
        -values    => \%types
      )
      . submit( -name => gettext("Refresh") )
      . "</td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Card Number")
      . "</td><td>"
      . gettext("Account Number")
      . "</td><td>"
      . "<acronym title=\""
      . gettext("Increment") . "\">"
      . gettext("INC")
      . "</acronym>"
      . "</td><td>"
      . gettext("Pricelist")
      . "</td><td>"
      . gettext("Balance")
      . "</td><td>"
      . gettext("Credit Limit")
      . "</td><td>"
      . "<acronym title=\""
      . gettext(
"Billing Cylce (How frequently this customer is billed.  Only applies to postpaid accounts."
      )
      . "\">"
      . gettext("Cycle")
      . "</acronym>"
      . "</td><td>"
      . "<acronym title=\""
      . gettext(
"Post To External (This would be for postpaid customers who's cdrs are to be posted to an external billing application such as oscommerce at the intervals specified in the cycle field."
      )
      . "\">"
      . gettext("P.T.E.")
      . "</acronym>"
      . "</td><td>"
      . gettext("Reseller")
      . "</td></tr>
\n";
    my @accountlist =
      &list_accounts_selective( $astpp_db, $reseller, $params->{type} );
    foreach my $account (@accountlist) {
        $accountcount++;
        my $accountinfo   = &get_account( $astpp_db,    $account );
        my $balance       = &accountbalance( $astpp_db, $account );
        my $pricelistinfo = &get_pricelist( $astpp_db,  $account );
        $balance = $balance / 10000;
        $balance =
          sprintf( "%." . $config->{decimalpoints} . "f", $balance );
        $totalbalance = $totalbalance + $balance;
        if ( $accountcount % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
            "<td>$accountinfo->{cc}</td><td>"
          . "<a href=\"astpp-admin.cgi?mode="
          . gettext("View Details")
          . "&accountnum=$account&action="
          . gettext("Information...")
          . "\">$account</a></td><td>$pricelistinfo->{inc}"
          . "</td><td>$accountinfo->{pricelist}</td><td>\$"
          . sprintf( "%.2f", $balance )
          . "</td><td>\$$accountinfo->{credit_limit}"
          . "</td><td>"
          . $sweeplist{ $accountinfo->{sweep} }
          . "</td><td>"
          . $yesno{ $accountinfo->{posttoexternal} }
          . "</td><td>$accountinfo->{reseller}</td></tr>
\n";
    }
    $body .=
        "<tr bgcolor=ff8800><td colspan=3>"
      . gettext("Number of Accounts:")
      . " $accountcount</td><td colspan=6>"
      . gettext("Total Owing:")
      . " \$ $totalbalance</td></tr>
</ERROR></table>
";
    return $body;
}

sub generatecallingcards() {
    my ( $params, $config, $enh_config ) = @_;
    my ( $status, $description, $pricelistdata, $number, $count );
    $status      = "";
    $description = gettext("Account Setup");
    $count       = 0;
    if ( $config->{email} eq "YES" ) {
        open( EMAIL, "| $enh_config->{mailprog} -t" )
          || die "Error - could not write to $enh_config->{mailprog}\n";
        print EMAIL"From: $config->{company_email}\n";
        print EMAIL "Subject: $config->{company_name} New Account Created\n";
        print EMAIL"To: $config->{emailadd}\n";
        print EMAIL"Content-type: text/plain\n\n";
        print EMAIL
"You have added $params->{count} calling cards in the amount of $params->{value} cents. \n\n";
    }
    my $brandinfo = &get_cc_brand( $astpp_db, $params->{brand} );
    print STDERR "BRAND: $params->{brand}\n" if $config->{debug} == 1;
    while ( $count < $params->{count} ) {
        my ( $number, $pin ) =
          &add_callingcard( $astpp_db, $config, $enh_config, $brandinfo,
            $params->{status}, $params->{value} * 100,
            $params->{account}, $brandinfo->{pin} );
        $count++;
        if ( $config->{email} eq "YES" ) {
            print EMAIL"Account: $number Pin: $pin \n";
        }
        my $cardinfo = &get_callingcard( $astpp_db, $number );
        $status .=
"Calling Card: $number Pin: $pin Sequence: $cardinfo->{id} added successfully <br>";
    }
    if ( $config->{email} eq "YES" ) {
        close(EMAIL);
    }
    return $status;
}

sub generate_accounts() {
    my ( $params, $config, $enh_config ) = @_;
    my ( $status, $description, $pricelistdata, $cardlist );
    $status      = "";
    $description = gettext("Account Setup");
    $cardlist    = &get_account_including_closed( $astpp_db, $params->{number} );
    if (!$cardlist->{number}) {
        &addaccount( $astpp_db, $config, $enh_config, $params );
	if ($params->{accounttype} == 1) {
	    	&add_pricelist( $astpp_db, $params->{number}, 6, 0, $params->{number} ); 
		&add_reseller( $astpp_db, $config, $enh_config, $params->{number}, $params->{posttoexternal} );
	}
        if ( $config->{email} == 1 && $params->{accounttype} == 0) {
			&email_add_user($astpp_db, '',$config, $params, $enh_config );
	}
        my $timestamp = &prettytimestamp;
        $astpp_db->do(
"INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
             . $astpp_db->quote( $params->{number} ) . ","
             . $astpp_db->quote($description) . ","
             . $astpp_db->quote( $params->{pennies} * 100 ) . ","
             . $astpp_db->quote($timestamp)
             . ")" );
        $status .= "Account $params->{number} added successfully" ."<br>";
    }
    elsif ( $cardlist->{status} != 1 ) {
        $astpp_db->do( "UPDATE accounts SET status = 1 WHERE number ="
              . $astpp_db->quote( $params->{number} ) );
        $status .=
            gettext("Account:")
          . " $params->{number} "
          . gettext("has been (re)activated")
          . "<br>\n";
	if ($cardlist->{type} == 1) {
          $astpp_db->do( "UPDATE resellers SET status = 1 WHERE name ="
               . $astpp_db->quote( $params->{number} ) );
	}
        if ( $config->{email} == 1 ) {
		&email_reactivate_account($astpp_db, '', $config, $params, $enh_config );
	}
    }
    else {
        $status .=
            gettext("Account:")
          . " $params->{number} "
          . gettext("exists already!!")
          . "<br>\n";
    }
    return $status;
}

sub build_create_account() {
    my ( @pricelists, $status, $body );
    return gettext("Database is NOT configured!") . " \n"
      unless $astpp_db;
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
    return gettext("Please configure 'Pricelists'") . "\n"
      unless @pricelists;
    if ( $params->{action} eq gettext("Generate Account") ) {
        if ($params->{logintype} == 1) {
                $params->{pricelist} = $params->{username};
                $params->{reseller} = $params->{username};
        }
        $params->{count}   = 1;
        $params->{pennies} = 0;
        $params->{number}  = $params->{customnum};
        $status .= &generate_accounts( $params, $config, $enh_config );
	###  Here we add support to add IAX and SIP devices at account creation.
	if ($params->{SIP}) {
		my $name = &finduniquesip_rt($params->{number});
        	$enh_config->{rt_sip_type} = $params->{devicetype};
        	$config->{ipaddr}          = $params->{ipaddr};
		if ($enh_config->{users_dids_rt} == 1) {
        	$status                    .=
          		&add_sip_user_rt( $rt_db, $config, $enh_config, $name,
            		$params->{accountpassword}, $params->{context}, $params->{number}, $params );
		}
		if ($config->{openser} == 1) {
        	$status                    .=
          		&add_sip_user_openser( $openser_db, $config, $enh_config, $name,
            		$params->{accountpassword}, $params->{context}, $params->{number}, $params );
		}
	}
	if ($params->{IAX2}) {
        	my $name = &finduniqueiax_rt($params->{number});
        	$enh_config->{rt_iax_type} = $params->{devicetype};
        	$config->{ipaddr}          = $params->{ipaddr};
        	$status                    .=
          		&add_iax_user_rt( $rt_db, $config, $enh_config, $name,
            		$params->{accountpassword}, $params->{context}, $params->{number}, $params );
	}
	###  End of Device creation support
    }
    $body = start_multipart_form;
    $body .= "<table class=\"default\">";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Account Number")
      . "</td><td>"
      . gettext("Password")
      . "</td><td>"
      . gettext("Pricelist")
      . "</td><td>"
      . gettext("Billing Schedule")
      . "</td><td colspan=2>"
      . gettext("Credit Limit in")
      . "$currency[0]</td></tr>"
      . "<tr class=\"rowone\"><td>"
      . hidden( -name => "mode", -value => gettext("Create Account") )
      . textfield( -name => "customnum", -size => 20 )
      . "</td><td>"
      . textfield( -name => "accountpassword", -size => 20 )
      . "</td><td>"
      . popup_menu(
        -name   => "pricelist",
        -values => \@pricelists
      )
      . "</td><td>"
      . popup_menu( -name => "sweep", -values => \%sweeplist )
      . "</td><td>"
      . textfield( -name => "credit_limit", -size => 6 )
      . "</td><td>"
      . "</td></tr>
<tr class=\"header\"><td>"
      . gettext("Post Charges to External App?")
      . "</td><td>"
      . gettext("First Name")
      . "</td><td>"
      . gettext("Middle Name")
      . "</td><td>"
      . gettext("Last Name")
      . "</td><td>"
      . gettext("Add VOIP Friend")
      . "</td></tr>
<tr class=\"rowtwo\"><td>"
      . popup_menu(
        -name   => "posttoexternal",
        -values => \%yesno,
	-default => 0
      )
      . "</td><td>"
      . textfield( -name => "firstname", -size => 20 )
      . "</td><td>"
      . textfield( -name => "middlename", -size => 20 )
      . "</td><td>"
      . textfield( -name => "lastname", -size => 20 )
      . "</td><td>"
      . checkbox(  -name => "SIP", -label => "SIP")
      . checkbox(  -name => "IAX2", -label => "IAX2")
      . "</td></tr>
<tr class=\"header\"><td>"
      . gettext("Company")
      . "</td><td>"
      . gettext("Address 1")
      . "</td><td>"
      . gettext("Address 2")
      . "</td><td>"
      . gettext("City")
      . "</td><td>"
      . gettext("Context")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . textfield( -name => "company", -size => 20 )
      . "</td><td>"
      . textfield( -name => "address1", -size => 20 )
      . "</td><td>"
      . textfield( -name => "address2", -size => 20 )
      . "</td><td>"
      . textfield( -name => "city", -size => 20 )
      . "</td><td>"
. textfield(
            -name    => "context",
            -size    => 20,
            -default => $config->{default_context}
          )
      . "</td></tr>
<tr class=\"header\"><td>"
      . gettext("Zip/Postal Code")
      . "</td><td>"
      . gettext("Province/State")
      . "</td><td colspan=2>"
      . gettext("Country")
      . "</td><td>"
      . gettext("Device Type")
      . "</td></tr>
<tr class=\"rowtwo\"><td>"
      . textfield( -name => "postal_code", -size => 20 )
      . "</td><td>"
      . textfield( -name => "province", -size => 20 )
      . "</td><td colspan =2>"
      . popup_menu(
        -name   => "country",
        -values => \@countries
      )
      . "</td><td>"
      . popup_menu(
            -name    => "devicetype",
            -values  => \@devicetypes
          )
      . "</td></tr>
<tr class=\"header\"><td>"
      . gettext("Telephone #1")
      . "</td><td>"
      . gettext("Telephone #2")
      . "</td><td>"
      . gettext("Fascimile")
      . "</td><td>"
      . gettext("Email")
      . "</td><td>"
      . gettext("IP Address")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . textfield( -name => "telephone1", -size => 20 )
      . "</td><td>"
      . textfield( -name => "telephone2", -size => 20 )
      . "</td><td>"
      . textfield( -name => "facsimile", -size => 20 )
      . "</td><td>"
      . textfield( -name => "email", -size => 20 )
      . "</td><td>"
	. textfield(
            -name    => "ipaddr",
            -size    => 20,
            -default => $config->{ipaddr}
          )
      . "</td></tr>
<tr class=\"header\"><td>"
      . gettext("Currency")
      . "</td><td>"
      . gettext("Account Type")
      . "</td><td>"
      . gettext("Language")
      . "</td><td>"
      . gettext("Max Channels")
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
<tr class=\"rowtwo\"><td>"
      . popup_menu(
        -name   => "currency",
        -values => \@currency
      )
      . "</td><td>"
      . popup_menu(
        -name   => "accounttype",
        -values => \%types,
	-default => 0
      )
      . "</td><td>"
      . popup_menu(
        -name    => "language",
        -values  => \@language,
        -default => $config->{language}
      )
      . "</td><td>"
      . textfield( -name => "maxchannels", -size => 4 )
      . "</td><td>"
      . submit(
        -name  => 'action',
        -value => gettext("Generate Account")
      )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_cc_brands() {
    my (
        @pricelists, $status,   $body, $number,        $inuse,
        $cardstat,  $cardinfo, $sql,  $pagesrequired, $pageno
    );
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
    return gettext("Database is NOT configured!") . "\n"
      unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }

    if ( $params->{action} eq gettext("Delete...") ) {
        my $tmp =
          "DELETE FROM callingcardbrands WHERE name = "
          . $astpp_db->quote( $params->{name} );
        my $sql = $astpp_db->prepare($tmp);
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Brand Deleted!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Brand Deletion Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        my $sql =
"INSERT INTO callingcardbrands (name,pricelist,language,pin,validfordays,maint_fee_pennies,"
          . "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies,status) VALUES ("
          . $astpp_db->quote( $params->{brandname} ) . ", "
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . $astpp_db->quote( $params->{language} ) . ", "
          . $astpp_db->quote( $params->{pin} ) . ", "
          . $astpp_db->quote( $params->{validdays} ) . ", "
          . $astpp_db->quote( $params->{maint_fee_pennies} ) . ", "
          . $astpp_db->quote( $params->{maint_fee_days} ) . ", "
          . $astpp_db->quote( $params->{disconnect_fee_pennies} ) . ", "
          . $astpp_db->quote( $params->{minute_fee_minutes} ) . ", "
          . $astpp_db->quote( $params->{minute_fee_pennies} ) . ", 1)";
        print STDERR "sql" if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Brand Added!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Brand Creation Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        $body = start_multipart_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("CC Pricelist Name")
          . "</td><td>"
          . gettext("Pin Required")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Days Valid For")
          . "</td><td>"
          . gettext("Language")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . hidden( -name => "mode", -value => gettext("CC Brands") )
          . textfield( -name => "brandname", -size => 20 )
          . "</td><td>"
          . popup_menu( -name => "pin", -values => \%yesno )
          . "</td><td>"
          . popup_menu(
            -name   => "pricelist",
            -values => \@pricelists
          )
          . "</td><td>"
          . textfield( -name => "validdays", -size => 8 )
          . "</td><td>"
          . popup_menu(
            -name    => "language",
            -values  => \@language,
            -default => $config->{language}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Maintenance Fee")
          . "</td><td>"
          . gettext("Days between Maint fee")
          . "</td><td>"
          . gettext("Disconnect Fee")
          . "</td><td>"
          . gettext("Charge after X minutes")
          . "</td><td>"
          . gettext("Minutes used before charge")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . textfield( -name => "maint_fee_pennies", -size => 8 )
          . "</td><td>"
          . textfield( -name => "maint_fee_days", -size => 8 )
          . "</td><td>"
          . textfield(
            -name => "disconnect_fee_pennies",
            -size => 8
          )
          . "</td><td>"
          . textfield( -name => "minute_fee_minutes", -size => 8 )
          . "</td><td>"
          . textfield( -name => "minute_fee_pennies", -size => 8 )
          . "</td><td>"
          . "</td></tr>
<tr><td>"
          . submit(
            -name  => 'action',
            -value => gettext("Insert...")
          )
          . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $sql =
            "UPDATE callingcardbrands SET"
          . " pricelist="
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . " language="
          . $astpp_db->quote( $params->{language} ) . ", " . " pin="
          . $astpp_db->quote( $params->{pin} ) . ", "
          . " validfordays="
          . $astpp_db->quote( $params->{validdays} ) . ", "
          . " maint_fee_pennies="
          . $astpp_db->quote( $params->{maint_fee_pennies} ) . ", "
          . " maint_fee_days="
          . $astpp_db->quote( $params->{maint_fee_days} ) . ", "
          . " disconnect_fee_pennies="
          . $astpp_db->quote( $params->{disconnect_fee_pennies} ) . ", "
          . " minute_fee_minutes="
          . $astpp_db->quote( $params->{minute_fee_minutes} ) . ", "
          . " minute_fee_pennies="
          . $astpp_db->quote( $params->{minute_fee_pennies} )
          . ", status=1 "
          . " WHERE name ="
          . $astpp_db->quote( $params->{name} );
        print STDERR "$sql" if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Brand Updated!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Brand Update Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Edit...") ) {
        my $brandinfo = &get_cc_brand( $astpp_db, $params->{name} );
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . gettext("CC Pricelist Name")
          . "</td><td>"
          . gettext("Pin Required")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Days Valid For")
          . "</td><td>"
          . gettext("Language")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . hidden( -name => "mode", -value => gettext("CC Brands") )
          . hidden( -name => "name", -value => $params->{name} )
          . $params->{name}
          . "</td><td>"
          . popup_menu(
            -name    => "pin",
            -values  => \%yesno,
            -default => $brandinfo->{pin}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "pricelist",
            -values  => \@pricelists,
            -default => $brandinfo->{pricelist}
          )
          . "</td><td>"
          . textfield(
            -name    => "validdays",
            -size    => 8,
            -default => $brandinfo->{validfordays}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "language",
            -values  => \@language,
            -default => $config->{language}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Maintenance Fee")
          . "</td><td>"
          . gettext("Days between Maint fee")
          . "</td><td>"
          . gettext("Disconnect Fee")
          . "</td><td>"
          . gettext("Charge after X minutes")
          . "</td><td>"
          . gettext("Minutes used before charge")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "maint_fee_pennies",
            -size    => 8,
            -default => $brandinfo->{maint_fee_pennies}
          )
          . "</td><td>"
          . textfield(
            -name   => "maint_fee_days",
            -size   => 8,
            default => $brandinfo->{maint_fee_days}
          )
          . "</td><td>"
          . textfield(
            -name => "disconnect_fee_pennies",
            -size => 8 -default => $brandinfo->{disconnect_fee_pennies}
          )
          . "</td><td>"
          . textfield(
            -name    => "minute_fee_minutes",
            -size    => 8,
            -default => $brandinfo->{minute_fee_minutes}
          )
          . "</td><td>"
          . textfield(
            -name    => "minute_fee_pennies",
            -size    => 8,
            -default => $brandinfo->{minute_fee_pennies}
          )
          . "</td><td>"
          . "</td></tr>
<tr><td>"
          . submit(
            -name  => 'action',
            -value => gettext("Save...")
          )
          . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .=
            "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("CC Brands") )
          . submit(
            -name  => 'action',
            -value => gettext("Add...")
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("CC Brand Name")
          . "</td><td>"
          . gettext("Pin Required")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Days Valid For")
          . "</td><td>"
          . gettext("Maintenance Fee(pennies)")
          . "</td><td>"
          . gettext("Days between Maint fee")
          . "</td><td>"
          . gettext("Disconnect Fee(Pennies)")
          . "</td><td>"
          . gettext("Charge after X minutes")
          . "</td><td>"
          . gettext("Minutes used before charge")
          . "</td><td>"
          . gettext("Status")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $sql =
          $astpp_db->prepare(
            'SELECT name FROM callingcardbrands WHERE status < 2');
        $sql->execute
          || return gettext(
            "Something is wrong with the callingcards database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print "Pages Required: $pagesrequired\n"
          if ( $config->{debug} eq "YES" );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM callingcardbrands WHERE status < 2 ORDER BY name limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext(
            "Something is wrong with the callingcards database!")
          . "\n";
        my $count = 0;

        while ( my $brandinfo = $sql->fetchrow_hashref ) {
            my ( $pins, $brandstat );
            $count++;
            if ( !( $count % 2 ) ) {
                $body .= "<tr class=\"rowone\">";
            }
            else {
                $body .= "<tr class=\"rowtwo\">";
            }
            if ( $brandinfo->{pin} == 0 ) {
                $pins = $no;
            }
            elsif ( $brandinfo->{pin} == 1 ) {
                $pins = $yes;
            }
            if ( $brandinfo->{status} == 0 ) {
                $brandstat = $inactive;
            }
            elsif ( $brandinfo->{status} == 1 ) {
                $brandstat = $active;
            }
            elsif ( $brandinfo->{status} == 2 ) {
                $brandstat = $deleted;
            }
            $body .=
                "<td>$brandinfo->{name}"
              . "</td><td>$pins "
              . "</td><td>$brandinfo->{pricelist}"
              . "</td><td>$brandinfo->{daysvalid}"
              . "</td><td>$brandinfo->{maint_fee_pennies}"
              . "</td><td>$brandinfo->{maint_fee_days}"
              . "</td><td>$brandinfo->{disconnect_fee_pennies}"
              . "</td><td>$brandinfo->{minute_fee_pennies}"
              . "</td><td>$brandinfo->{minute_fee_minutes}"
              . "</td><td>$brandstat"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("CC Brands")
              . "&name=$brandinfo->{name}&action="
              . gettext("Edit...") . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("CC Brands")
              . "&name=$brandinfo->{name}&action="
              . gettext("Delete...") . "\">"
              . gettext("Delete...")
              . "</a></td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("CC Brands")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("CC Brands")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
                }
                else {
                    $pageno = $i + 1;
                    $body .= " |";
                }
            }
        }
    }
    $body .= "";
    $body .= "Page $pageno of $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_create_card() {
    my ( @pricelists, $status, $body, @brands );
    return gettext("Database is NOT configured!") . " \n" unless $astpp_db;
    @brands = &list_cc_brands($astpp_db);
    return gettext("Please configure 'CC Brands'") . "\n" unless @brands;
    if ( $params->{action} eq gettext("Generate Card(s)") ) {
        if ( $params->{status} eq gettext("ACTIVE") ) {
            $params->{status} = 1;
        }
        elsif ( $params->{status} eq gettext("INACTIVE") ) {
            $params->{status} = 0;
        }
        elsif ( $params->{status} eq gettext("DELETED") ) {
            $params->{status} = 2;
        }
        $status .= &generatecallingcards( $params, $config, $enh_config );
    }
    $body = start_multipart_form;
    $body .= "<table class=\"default\">";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Account Number")
      . "</td><td>"
      . gettext("Brand")
      . "</td><td>"
      . gettext("Value in pennies")
      . "</td><td>"
      . gettext("Quantity")
      . "</td><td>"
      . gettext("Status")
      . "</td></tr>
"
      . "<tr class=\"rowone\"><td>"
      . hidden( -name => "mode", -value => gettext("Create Card") )
      . textfield( -name => "account", -size => 20 )
      . "</td><td>"
      . popup_menu( -name => "brand", -values => \@brands )
      . "</td><td>"
      . textfield( -name => "value", -size => 8 )
      . "</td><td>"
      . textfield( -name => "count", -size => 8 )
      . "</td><td>"
      . popup_menu(
        -name   => "status",
        -values => \@cardstatus
      )
      . "</td><td>"
      . "</td></tr>
<tr><td>"
      . submit(
        -name  => 'action',
        -value => gettext("Generate Card(s)")
      )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_update_card_status() {
    my ( @pricelists, $status, $body, $count, $sql );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
    return gettext("Please configure 'Pricelists'!") . "\n"
      unless @pricelists;
    if ( $params->{action} eq gettext("Update Status on Card(s)") ) {
        my $sequence = $params->{starting};
        my $ending   = $params->{ending};
        my ($active);
        if ( $params->{status} eq gettext("ACTIVE") ) {
            $active = 1;
        }
        elsif ( $params->{status} eq gettext("INACTIVE") ) {
            $active = 0;
        }
        elsif ( $params->{status} eq gettext("DELETED") ) {
            $active = 2;
        }
        while ( $sequence <= $ending ) {
            $sql =
                "UPDATE callingcards SET status ="
              . $astpp_db->quote($active)
              . "WHERE id ="
              . $astpp_db->quote($sequence);
            $astpp_db->do($sql) || print "$sql failed";
            $status .=
              "$sequence " . gettext("Set To") . " $params->{status}<br>\n";
            $sequence++;
        }
    }
    $body = start_multipart_form;
    $body .= "<table class=\"default\">";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Starting Sequence Number")
      . "</td><td>"
      . gettext("Ending Sequence Number")
      . "</td><td>"
      . gettext("New Status")
      . gettext("Action")
      . "</td><td>"
      . "</tr>
"
      . "<tr class=\"rowone\"><td>"
      . hidden( -name => "mode", -value => gettext("Active Cards") )
      . textfield( -name => "starting", -size => 10 )
      . "</td><td>"
      . textfield( -name => "ending", -size => 10 )
      . "</td><td>"
      . popup_menu(
        -name   => "status",
        -values => \@cardstatus
      )
      . "</td><td>"
      . submit(
        -name  => 'action',
        -value => gettext("Update Status on Card(s)")
      )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>

		 ";
    return $body;
}

sub build_reset_card_inuse() {
    my ( @pricelists, $status, $body, $count );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{action} eq gettext("Reset") ) {
        my $sql =
          "UPDATE callingcards SET inuse = 0 WHERE cardnumber ="
          . $astpp_db->quote( $params->{cardnumber} );
        $astpp_db->do($sql) || print "$sql failed";
        $status .=
          "$params->{cardnumber} " . gettext("In Use Reset") . "<br>\n";
    }
    $body = start_form;
    $body .= "<table class=\"default\"";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Card Number")
      . "</td></tr>
"
      . "<tr><td>"
      . hidden( -name => "mode", -value => gettext("Reset InUse") )
      . textfield( -name => "cardnumber", -size => 10 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Reset") )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>

		 ";
    return $body;
}

sub update_balance() {
    my ( $cardinfo, $charge ) = @_;
    my $sql =
        "UPDATE callingcards SET value = "
      . $astpp_db->quote( ($charge) + $cardinfo->{value} )
      . " WHERE cardnumber = "
      . $astpp_db->quote( $cardinfo->{cardnumber} );
    $astpp_db->do($sql) || print "$sql " . gettext("FAILED");
}

sub build_refill_card() {
    my ( @pricelists, $status, $body, $count, $cardinfo );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{action} eq gettext("Refill") ) {
        $cardinfo = &get_callingcard( $astpp_db, $params->{cardnumber} );
        &update_balance( $cardinfo, $params->{pennies} * 100 );
        $status .= "$params->{cardnumber} " . gettext("Refilled") . "<br>\n";
    }
    $body = start_form;
    $body .= "<table class=\"default\">";
    $body .=
        "<tr class=\"header\"><td colspan=3>"
      . gettext("Refill Calling Card")
      . "</td></tr>
";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Card Number")
      . "</td><td>"
      . gettext("Amount in Pennies")
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
"
      . "<tr class=\"rowone\"><td>"
      . hidden( -name => "mode", -value => gettext("Refill Card") )
      . textfield( -name => "cardnumber", -size => 10 )
      . "</td><td>"
      . textfield( -name => "pennies", -size => 5 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Refill") )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>

		 ";
    return $body;
}

sub build_delete_cards() {
    my ( @pricelists, $status, $body, $count );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{action} eq gettext("Delete") ) {
        my $sql =
          "UPDATE callingcards SET status = 2 WHERE cardnumber ="
          . $astpp_db->quote( $params->{cardnumber} );
        $astpp_db->do($sql) || print "$sql " . gettext("FAILED");
        $status .= "$params->{cardnumber} " . gettext("Deleted") . "<br>\n";
    }
    $body = start_form;
    $body .= "<table class=\"default\">";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Delete Calling Card")
      . "</td></tr>
";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Card Number")
      . "</td></tr>
"
      . "<tr><td>"
      . hidden( -name => "mode", -value => gettext("Delete Card") )
      . textfield( -name => "cardnumber", -size => 10 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Delete") )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>

		 ";
    return $body;
}

sub build_list_cards() {
    my ( @pricelists, $status, $body, $number, $inuse, $cardstat, $cardinfo,
        $count, $sql, $pageno, $results, $results_per_page, $pagesrequired );
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status           = "&nbsp;";
    $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    $body = "<table class=\"default\"><tr class=\"header\"><td>";
    $body .=
        gettext("Account Number")
      . "</td><td>"
      . gettext("Sequence")
      . "</td><td>"
      . gettext("Card Number")
      . "</td><td>"
      . gettext("Pin")
      . "</td><td>"
      . gettext("Pricelist")
      . "</td><td>"
      . gettext("Value")
      . " $currency[0]"
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
      . "</td></tr>
\n";
    $sql =
      $astpp_db->prepare(
        'SELECT cardnumber FROM callingcards WHERE status < 2');
    $sql->execute
      || return gettext("Something is wrong with the callingcards database!")
      . "\n";
    $results       = $sql->rows;
    $pagesrequired = ceil( $results / $results_per_page );
    print gettext("Pages Required:") . " $pagesrequired\n"
      if ( $config->{debug} eq "YES" );
    $sql->finish;
    $sql =
      $astpp_db->prepare(
"SELECT * FROM callingcards WHERE status < 2 ORDER BY id limit $params->{limit} , $results_per_page"
      );
    $sql->execute
      || return gettext("Something is wrong with the callingcards database!")
      . "\n";

    while ( $cardinfo = $sql->fetchrow_hashref ) {
        $count++;
        my ( $value, $used );
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
        $value = sprintf( "%." . $config->{decimalpoints} . "f", $value );
        $used  = $cardinfo->{used} / 10000;
        $used  = sprintf( "%." . $config->{decimalpoints} . "f", $used );
        if ( !( $count % 2 ) ) {
            $body .= "<tr class=\"rowone\">";
        }
        else {
            $body .= "<tr class=\"rowtwo\">";
        }
        $body .=
            "<td>$cardinfo->{account}"
          . "</td><td>$cardinfo->{id}"
          . "</td><td><a href=\"astpp-admin.cgi?mode="
          . gettext("View Card")
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
            $body .=
                "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Reset InUse")
              . "&cardnumber=$cardinfo->{cardnumber}&action="
              . gettext("Reset")
              . "\">$yes</a>";
        }
        else {
            $body .= "</td><td> $no ";
        }
        $body .= "</td><td>$cardstat" . "</td></tr>
\n";
    }
    $body .= "</table>
";
    for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
        if ( $i == 0 ) {
            if ( $params->{limit} != 0 ) {
                $body .=
                    "<a href=\"astpp-admin.cgi?mode="
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
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("List Cards")
                  . "&limit=";
                $body .= ( $i * $results_per_page );
                $body .= "\">\n";
                $body .= $i + 1 . "</a>";
            }
            else {
                $pageno = $i + 1;
                $body .= " |";
            }
        }
    }
    $body .= "";
    $body .= gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_view_card() {
    my ( @pricelists, $status, $body, $count, $inuse, $cardstat, $value, $used );
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    $body = start_form;
    $body .= "<table class=\"default\">";
    $body .= "<tr class=\"header\"><td>";
    $body .=
        gettext("Card Number")
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
"
      . "<tr><td>"
      . hidden( -name => "mode", -value => gettext("View Card") )
      . textfield( -name => "number", -size => 20 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("View Card") )
      . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>

		 ";

    if ( $params->{action} eq gettext("View Card") ) {
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
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Value")
          . " $currency[0]"
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
          . "</td></tr>
";
        my $sql =
          $astpp_db->prepare( "SELECT * FROM callingcards WHERE cardnumber = "
              . $astpp_db->quote( param('number') ) );
        $sql->execute
          || return gettext(
            "Something is wrong with the callingcards database!")
          . "\n";
        my $cardinfo = $sql->fetchrow_hashref;
        $sql->finish;
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
        $value = sprintf( "%." . $config->{decimalpoints} . "f", $value );
        $used  = $cardinfo->{used} / 10000;
        $used  = sprintf( "%." . $config->{decimalpoints} . "f", $used );
        $body .=
            "<tr class=\"rowone\"><td>$cardinfo->{account}"
          . "</td><td>$cardinfo->{id}"
          . "</td><td>$cardinfo->{cardnumber}"
          . "</td><td>$cardinfo->{pin}"
          . "</td><td>$cardinfo->{brand}"
          . "</td><td>$value "
          . "</td><td>$used "
          . "</td><td>$cardinfo->{validfordays}"
          . "</td><td>$cardinfo->{created}"
          . "</td><td>$cardinfo->{firstused}"
          . "</td><td>$cardinfo->{expiry}"
          . "</td><td>$inuse"
          . "</td><td>$cardstat"
          . "</td></tr>
</table>
";
        $body .=
            "<table class=\"default\"><tr class=\"header\"><td>"
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
          . "($currency[0]) </td></tr>
";
        $sql =
          $astpp_db->prepare(
            "SELECT * FROM callingcardcdrs WHERE cardnumber = "
              . $astpp_db->quote( param('number') ) );
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
            $body .=
                "<td>$cdrinfo->{destination}"
              . "</td><td>$cdrinfo->{disposition}"
              . "</td><td>$cdrinfo->{clid}"
              . "</td><td>$cdrinfo->{callstart}"
              . "</td><td>$cdrinfo->{seconds}"
              . "</td><td>"
              . $cdrinfo->{debit} / 10000
              . "</td></tr>
";
        }
    }
    return $body;
}

sub build_list_errors() {
    my ( $results, $body, $status, $count, $pageno, $pagesrequired );
    $status .= "&nbsp;";
    $count  = 0;
    $cdr_db = &cdr_connect_db( $config, $enh_config, @output );
    return gettext("Cannot list errors until database is configured!") . "\n"
      unless $cdr_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    my $sql =
      $cdr_db->prepare( "SELECT * FROM cdr WHERE cost = 'error' OR "
          . "accountcode IN (NULL,'') AND cost ='none'" );
    $sql->execute
      || return gettext("Something is wrong with the cdr database!") . "\n";
    $results       = $sql->rows;
    $pagesrequired = ceil( $results / $results_per_page );
    print gettext("Pages Required:") . " $pagesrequired\n"
      if ( $config->{debug} eq "YES" );
    $sql->finish;
    $sql =
      $cdr_db->prepare( " SELECT * FROM cdr WHERE cost = 'error' OR "
          . "accountcode IN (NULL,'') AND cost ='none' ORDER BY calldate "
          . "limit $params->{limit} , $results_per_page" );
    $sql->execute
      || return gettext("Something is wrong with the cdr database!") . "\n";
    $body =
        "<table class=\"viewcdrs\"><tr><td colspan=7>"
      . start_form
      . "<i>$status</i></td></tr>
"
      . "<tr class=\"header\"><td>"
      . gettext("Date")
      . "</td><td>"
      . gettext("CallerID") . "</td>" . "<td>"
      . gettext("Source")
      . "</td><td>"
      . gettext("Dest") . "</td>" . "<td>"
      . gettext("Dest.Context")
      . "</td><td>"
      . gettext("Channel") . "</td>" . "<td>"
      . gettext("Dest.Channel")
      . "</td><td>"
      . gettext("Last App")
      . " </td>" . "<td>"
      . gettext("Last Data")
      . "</td><td>"
      . gettext("Duration") . "</td>" . "<td>"
      . gettext("BillSec")
      . "</td><td>"
      . gettext("Disposition") . "</td>" . "<td>"
      . gettext("AMAFlags")
      . "</td><td>"
      . gettext("AccountCode") . "</td>" . "<td>"
      . gettext("UniqueID")
      . "</td><td>"
      . gettext("UserField") . "</td>" . "<td>"
      . gettext("Cost")
      . "</td></tr>
\n";

    while ( my $record = $sql->fetchrow_hashref ) {
        $count++;
        if ( $count % 2 == 0 ) {
            $body .= "<tr class=\"rowtwo\">";
        }
        else {
            $body .= "<tr class=\"rowone\">";
        }
        $body .=
"<td>$record->{calldate}</td><td>$record->{clid}</td><td>$record->{src}</td>"
          . "<td>$record->{dst}</td><td>$record->{dcontext}</td><td>$record->{channel}</td>"
          . "<td>$record->{dstchannel}</td><td>$record->{lastapp}</td><td>$record->{lastdata}</td>"
          . "<td>$record->{duration}</td><td>$record->{billsec}</td><td>$record->{disposition}</td>"
          . "<td>$record->{amaflags}</td><td>$record->{accountcode}</td><td>$record->{uniqueid}</td>"
          . "<td>$record->{userfield}</td><td>$record->{cost}</td></tr>
\n";
    }
    $body .= "</table>
";
    $sql->finish;
    for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
        if ( $i == 0 ) {
            if ( $params->{limit} != 0 ) {
                $body .=
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("List Errors")
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
                    "<a href=\"astpp-admin.cgi?mode="
                  . gettext("List Errors")
                  . "&limit=";
                $body .= ( $i * $results_per_page );
                $body .= "\">\n";
                $body .= $i + 1 . "</a>";
            }
            else {
                $pageno = $i + 1;
                $body .= " |";
            }
        }
    }
    $body .= "";
    $body .= gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_statistics() {
    my ( $body, $status );
    my $astman = new Asterisk::Manager;
    $astman->user( $config->{astman_user} );
    $astman->secret( $config->{astman_secret} );
    $astman->host( $config->{astman_host} );
    $astman->connect || die $astman->error . "\n";
    my %sip_show_peers = (
        'Action'  => 'Command',
        'Command' => 'sip show peers'
    );
    my %sip_show_users = (
        'Action'  => 'Command',
        'Command' => 'sip show users'
    );
    my %iax2_show_peers = (
        'Action'  => 'Command',
        'Command' => 'iax2 show peers'
    );
    my %iax2_show_users = (
        'Action'  => 'Command',
        'Command' => 'iax2 show users'
    );
    my %command_show_channels = (
        'Action'  => 'Command',
        'Command' => 'show channels'
    );
    my %sip_peers     = $astman->sendcommand( %sip_show_peers,        0 );
    my %sip_users     = $astman->sendcommand( %sip_show_users,        0 );
    my %iax2_peers    = $astman->sendcommand( %iax2_show_peers,       0 );
    my %iax2_users    = $astman->sendcommand( %iax2_show_users,       0 );
    my %show_channels = $astman->sendcommand( %command_show_channels, 0 );
    $astman->disconnect;
    $body = "<table class=\"default\">";
    $body .= "<tr class=\"header\"><td colspan = 8>" . $status . " </td></tr>
";
    $body .= "<tr></tr>
";
    $body .= "<tr class=\"header\"><td colspan = 8> SIP PEERS </td></tr>
";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Name/Username")
      . "</td><td>"
      . gettext("Host")
      . "</td><td>"
      . gettext("Dyn")
      . "</td><td>"
      . gettext("NAT")
      . "</td><td>"
      . gettext("ACL")
      . "</td><td>"
      . gettext("Mask")
      . "</td><td>"
      . gettext("Port")
      . "</td><td>"
      . gettext("Status")
      . "</td></tr>
";
    my $count = 0;

    foreach my $record (%sip_peers) {
        my $length = length($record);
        my $name   = substr( $record, 0, 16 );
        my $host   = substr( $record, 17, 16 );
        my $dyn    = substr( $record, 35, 3 );
        my $acl    = substr( $record, 39, 3 );
        my $mask   = substr( $record, 43, 17 );
        my $port   = substr( $record, 61, 8 );
        my $status = substr( $record, 68, $length - 68 );
        my $nat;

        if (   $name !~ "^--END COMMAND--.*"
            && $name !~ "^Name.*"
            && $name !~ "^Response.*"
            && $name !~ "^Privilege.*"
            && $name !~ "^Command.*"
            && $name !~ "^Follows.*" )
        {
            $count++;
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>" . $name
              . "</td><td>"
              . $host
              . "</td><td>"
              . $dyn
              . "</td><td>"
              . $nat
              . "</td><td>"
              . $acl
              . "</td><td>"
              . $mask
              . "</td><td>"
              . $port
              . "</td><td>"
              . $status
              . "</td></tr>
";
        }
    }
    $body .= "<tr></tr>
";
    $body .= "<tr class=\"header\"><td colspan = 6> SIP USERS </td></tr>
";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Username")
      . "</td><td>"
      . gettext("Secret")
      . "</td><td>"
      . gettext("Accountcode")
      . "</td><td>"
      . gettext("context")
      . "</td><td>"
      . gettext("ACL")
      . "</td><td>"
      . gettext("NAT")
      . "</td></tr>
";
    foreach my $record (%sip_users) {
        my $length      = length($record);
        my $name        = substr( $record, 0, 16 );
        my $secret      = substr( $record, 17, 16 );
        my $accountcode = substr( $record, 33, 15 );
        my $context     = substr( $record, 45, 16 );
        my $acl         = substr( $record, 61, 4 );
        my $nat         = substr( $record, 66, $length - 66 );
        if (   $name !~ "^--END COMMAND--.*"
            && $name !~ "^Username.*"
            && $name !~ "^Response.*"
            && $name !~ "^Privilege.*"
            && $name !~ "^Command.*"
            && $name !~ "^Follows.*" )
        {
            $count++;
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>" . $name
              . "</td><td>"
              . $secret
              . "</td><td>"
              . $accountcode
              . "</td><td>"
              . $context
              . "</td><td>"
              . $acl
              . "</td><td>"
              . $nat
              . "</td></tr>
";
        }
    }
    $body .= "<tr></tr>
";
    $body .=
        "<tr class=\"header\"><td colspan = 6>"
      . gettext("IAX2 Peers")
      . "</td></tr>
";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Name/Username")
      . "</td><td>"
      . gettext("Host")
      . "</td><td>"
      . gettext("Dyn")
      . "</td><td>"
      . gettext("Mask")
      . "</td><td>"
      . gettext("Port")
      . "</td><td>"
      . gettext("Status")
      . "</td></tr>
";
    $count = 0;
    foreach my $record (%iax2_peers) {
        my $length = length($record);
        my $name   = substr( $record, 0, 16 );
        my $host   = substr( $record, 17, 16 );
        my $dyn    = substr( $record, 33, 3 );
        my $mask   = substr( $record, 38, 17 );
        my $port   = substr( $record, 55, 8 );
        my $status = substr( $record, 64, $length - 64 );
        if (   $name !~ "^--END COMMAND--.*"
            && $name !~ "^Name.*"
            && $name !~ "^Response."
            && $name !~ "^Privilege.*"
            && $name !~ "^Command.*"
            && $name !~ "^Follows.*" )
        {
            $count++;
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>" . $name
              . "</td><td>"
              . $host
              . "</td><td>"
              . $dyn
              . "</td><td>"
              . $mask
              . "</td><td>"
              . $port
              . "</td><td>"
              . $status
              . "</td></tr>
";
        }
    }
    $body .= "<tr></tr>
";
    $body .= "<tr class=\"header\"><td colspan = 5> IAX2 Users </td></tr>
";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Username")
      . "</td><td>"
      . gettext("Secret")
      . "</td><td>"
      . gettext("Authen")
      . "</td><td>"
      . gettext("context")
      . "</td><td>"
      . gettext("A/C")
      . "</td></tr>
";
    foreach my $record (%iax2_users) {
        my ( $name, $secret, $authen, $context, $ac );
        print STDERR $record . "\n" if $config->{debug} == 1;
        my $length = length($record);
        if ( $length < 55 ) {
            $name    = substr( $record, 0,  16 );
            $secret  = substr( $record, 17, 15 );
            $authen  = "";
            $context = substr( $record, 17, 16 );
            $ac = substr( $record, 34, );
        }
        else {
            $name    = substr( $record, 0,  16 );
            $secret  = substr( $record, 17, 21 );
            $authen  = substr( $record, 41, 14 );
            $context = substr( $record, 56, 16 );
            $ac      = substr( $record, 73, $length - 73 );
        }
        if (   $name !~ "^--END COMMAND--.*"
            && $secret !~ "^Key.*"
            && $name !~ "^Username.*"
            && $name !~ "^Response.*"
            && $name !~ "^Command.*"
            && $name !~ "^Privilege.*"
            && $name !~ "^Follows.*" )
        {
            $count++;
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>" . $name
              . "</td><td>"
              . $secret
              . "</td><td>"
              . $authen
              . "</td><td>"
              . $context
              . "</td><td>"
              . $ac
              . "</td></tr>
";
        }
    }
    $count = 0;
    $body .= "<tr class=\"header\"><td colspan = 5> Channels In Use </td></tr>
";
    $body .=
        "<tr class=\"header\"><td>"
      . gettext("Channel")
      . "</td><td>"
      . gettext("Location")
      . "</td><td>"
      . gettext("State")
      . "</td><td>"
      . gettext("Application(Data)")
      . "</td></tr>
";
    foreach my $record (%show_channels) {
        my $length = length($record);
        print STDERR $record . "\n" if $config->{debug} == 1;
        my $channel     = substr( $record, 0,  20 );
        my $location    = substr( $record, 21, 21 );
        my $state       = substr( $record, 42, 8 );
        my $application = substr( $record, 50, $length - 50 );
        if (   $channel !~ "^--END COMMAND--.*"
            && $channel !~ "^Channel.*"
            && $channel !~ "^Privilege.*"
            && $channel !~ "^Response.*"
            && $channel !~ "^Command.*"
            && $channel !~ "^Follows.*" )
        {
            $count++;
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>" . $channel
              . "</td><td>"
              . $location
              . "</td><td>"
              . $state
              . "</td><td>"
              . $application
              . "</td></tr>
";
        }
    }
    $count = 0;
    $body .= "</table>
";
    return $body;
}

sub build_edit_account() {
    my ( $valid, $body, $tmp, $sql, $status, $number, @accountlist, @pricelists );
    if ( $params->{action} eq gettext("Save...") ) {
	if ($params->{logintype} == 1) {
		my $accountinfo = &get_account($astpp_db, $params->{item});
		if ($accountinfo->{reseller} eq $params->{username}) {
			$valid = 1;
			$params->{reseller} = $params->{username};
		}	
	} else {
#		$params->{reseller} = "";
		$valid = 1;
	}
	if ($valid == 1) {
        $tmp =
            "UPDATE accounts SET "
          . "reseller = "
          . $astpp_db->quote( $params->{reseller} ) . ","
          . "pricelist = "
          . $astpp_db->quote( $params->{pricelist} ) . ","
          . "sweep = "
          . $astpp_db->quote( $params->{sweep} ) . ","
          . "credit_limit = "
          . $astpp_db->quote( $params->{credit_limit} ) . ","
          . "posttoexternal = "
          . $astpp_db->quote( $params->{posttoexternal} ) . ","
          . "password = "
          . $astpp_db->quote( $params->{newpassword} ) . ","
          . "first_name = "
          . $astpp_db->quote( $params->{firstname} ) . ","
          . "middle_name = "
          . $astpp_db->quote( $params->{middlename} ) . ","
          . "last_name = "
          . $astpp_db->quote( $params->{lastname} ) . ","
          . "company_name = "
          . $astpp_db->quote( $params->{company} ) . ","
          . "address_1 = "
          . $astpp_db->quote( $params->{address1} ) . ","
          . "address_2 = "
          . $astpp_db->quote( $params->{address2} ) . ","
          . "address_3 = "
          . $astpp_db->quote( $params->{address3} ) . ","
          . "postal_code = "
          . $astpp_db->quote( $params->{postal_code} ) . ","
          . "province = "
          . $astpp_db->quote( $params->{province} ) . ","
          . "city = "
          . $astpp_db->quote( $params->{city} ) . ","
          . "country = "
          . $astpp_db->quote( $params->{country} ) . ","
          . "telephone_1 = "
          . $astpp_db->quote( $params->{telephone1} ) . ","
          . "telephone_2 = "
          . $astpp_db->quote( $params->{telephone2} ) . ","
          . "fascimile = "
          . $astpp_db->quote( $params->{facsimile} ) . ","
          . "email = "
          . $astpp_db->quote( $params->{email} ) . ","
          . "language = "
          . $astpp_db->quote( $params->{language} ) . ","
          . "maxchannels = "
          . $astpp_db->quote( $params->{maxchannels} ) . ","
          . "currency = "
          . $astpp_db->quote( $params->{currency} )
          . " WHERE number = "
          . $astpp_db->quote( $params->{item} );
	}
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Account") . " "
              . $params->{item} . " "
              . gettext("Successfully Updated!") . "\n";
        }
        else {
            $status .=
                gettext("Account") . " "
              . $params->{item} . " "
              . gettext("Failed To Update!") . "\n";
            print "$tmp failed";
        }
	if ($params->{type} == 1) {
		my $tmp = "UPDATE resellers SET posttoexternal = "
          . $astpp_db->quote( $params->{posttoexternal} )
          . " WHERE name ="
          . $astpp_db->quote( $params->{item} );
		if ($astpp_db->do($tmp)) {
			$status .= gettext("Reseller Updated Successfully!");
		} else {
			$status .= gettext("Reseller Update Failed!");
		}
	}
   }
    elsif ( param('action') eq gettext("Edit...") ) {
        if ( $params->{number} eq "" ) {
            $number = $params->{accountlist};
        }
        else {
            $number = $params->{number};
        }
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
        my @resellerlist = &list_resellers($astpp_db);
        unshift( @resellerlist, "" );
        $accountinfo = &get_account( $astpp_db, $number );
        $body = start_form
          . "<table class=\"default\"><tr class=\"header\"><td colspan=4>"
          . hidden( -name => "mode", -value => gettext("Edit Account") )
          . hidden( -name => "item", -value => $number )
          . gettext("Account Number")
          . "</td><td colspan=2>"
          . gettext("Action")
          . "</td></tr>
<tr class=\"rowone\"><td colspan=4>"
          . $number
          . "</td><td colspan=2>"
          . submit( -name => "action", -value => gettext("Save...") )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Reseller")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Cycle")
          . "</td><td>"
          . gettext("Credit Limit")
          . "</td><td>"
          . gettext("Post To External App?")
          . "</td></tr>
<tr class=\"rowone\"><td>";
    	if ($params->{logintype} == 1) {
		$body .= $accountinfo->{reseller}
	} else {
		$body .=
          popup_menu(
            -name    => "reseller",
            -values  => \@resellerlist,
            -default => $accountinfo->{reseller}
          );
	  }
          $body .= "</td><td>"
          . popup_menu(
            -name    => "pricelist",
            -values  => \@pricelists,
            -default => $accountinfo->{pricelist}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "sweep",
            -values  => \%sweeplist,
            -default => $accountinfo->{sweep}
          )
          . "</td><td>"
          . textfield(
            -name    => "credit_limit",
            -size    => 5,
            -default => $accountinfo->{credit_limit}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "posttoexternal",
            -values  => \%yesno,
            -default => $accountinfo->{posttoexternal}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Password")
          . "</td><td>"
          . gettext("First Name")
          . "</td><td>"
          . gettext("Middle Name")
          . "</td><td>"
          . gettext("Last Name")
          . "</td><td>"
          . gettext("Company Name")
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . password_field(
            -name    => "newpassword",
            -size    => 10,
            -default => $accountinfo->{password}
          )
          . "</td><td>"
          . textfield(
            -name    => "firstname",
            -size    => 10,
            -default => $accountinfo->{first_name}
          )
          . "</td><td>"
          . textfield(
            -name    => "middlename",
            -size    => 10,
            -default => $accountinfo->{middle_name}
          )
          . "</td><td>"
          . textfield(
            -name    => "lastname",
            -size    => 10,
            -default => $accountinfo->{last_name}
          )
          . "</td><td>"
          . textfield(
            -name    => "company",
            -size    => 20,
            -default => $accountinfo->{company_name}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Address 1")
          . "</td><td>"
          . gettext("Address 2")
          . "</td><td>"
          . gettext("Address 3")
          . "</td><td>"
          . gettext("Postal Code")
          . "</td><td>"
          . gettext("Province/State")
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "address1",
            -size    => 25,
            -default => $accountinfo->{address_1}
          )
          . "</td><td>"
          . textfield(
            -name    => "address2",
            -size    => 25,
            -default => $accountinfo->{address_2}
          )
          . "</td><td>"
          . textfield(
            -name    => "address3",
            -size    => 25,
            -default => $accountinfo->{address_3}
          )
          . "</td><td>"
          . textfield(
            -name    => "postal_code",
            -size    => 10,
            -default => $accountinfo->{postal_code}
          )
          . "</td><td>"
          . textfield(
            -name    => "province",
            -size    => 25,
            -default => $accountinfo->{province}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("City")
          . "</td><td colspan=2>"
          . gettext("Country")
          . "</td><td>"
          . gettext("Telephone 1")
          . "</td><td>"
          . gettext("Telephone 2")
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "city",
            -size    => 25,
            -default => $accountinfo->{city}
          )
          . "</td><td colspan=2>"
          . popup_menu(
            -name    => "country",
            -values  => \@countries,
            -default => $accountinfo->{country}
          )
          . "</td><td>"
          . textfield(
            -name    => "telephone1",
            -size    => 12,
            -default => $accountinfo->{telephone_1}
          )
          . "</td><td>"
          . textfield(
            -name    => "telephone2",
            -size    => 12,
            -default => $accountinfo->{telephone_2}
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Facsimile")
          . "</td><td>"
          . gettext("Email")
          . "</td><td>"
          . gettext("Language")
          . "</td><td>"
          . gettext("Currency")
          . "</td><td>"
          . gettext("Max Channels")
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "facsimile",
            -size    => 12,
            -default => $accountinfo->{fascimile}
          )
          . "</td><td>"
          . textfield(
            -name    => "email",
            -size    => 20,
            -default => $accountinfo->{email}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "language",
            -values  => \@language,
            -default => $accountinfo->{language}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "currency",
            -values  => \@currency,
            -default => $accountinfo->{currency}
          )
          . "</td><td>"
          . textfield(
            -name    => "maxchannels",
            -size    => 4,
            -default => $accountinfo->{maxchannels}
          )
          . "</td></tr>"
	  . "<tr><td>"
          . $status
          . "</td></tr>
</table>
";
    }
    if ($params->{logintype} == 1) {
    @accountlist = &list_accounts($astpp_db, $params->{username}, -1);
    } else {
    @accountlist = &list_accounts($astpp_db);
    }
    @accountlist = sort @accountlist;
    $body .= start_form;
    $body .=
        "<table class=\"default\"><tr class=\"header\"><td colspan=3>"
      . hidden( -name => "mode", -value => gettext("Edit Account") )
      . "Edit Account"
      . "</td></tr>
<tr class=\"header\"><td>"
      . "Either select or enter the account number to edit"
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . popup_menu(
        -name   => "accountlist",
        -values => \@accountlist
      )
      . textfield( -name => "number", -size => 20 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Edit...") )
      . "</td></tr>
<tr><td colspan = 2> $status </td></tr>
</table>
";
    return $body;
}

sub build_periodic_charges() {
    my (
        @pricelists,   $status, $body,
        $number,      $inuse,  $accountstat,
        $accountinfo, $pageno, $pagesrequired
    );
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");
    return gettext("Database is NOT configured!") . "\n"
      unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }

    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Insert...") ) {
        my $sql =
"INSERT INTO charges (pricelist,description,charge,sweep,status) VALUES ("
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . $astpp_db->quote( $params->{desc} ) . ", "
          . $astpp_db->quote( $params->{charge} ) . ", "
          . $astpp_db->quote( $params->{sweep} ) . ", 1)";
        print STDERR "sql" if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Added!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Periodic Charge Creation Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
        return gettext("Please configure 'Pricelists'!") . "\n"
          unless @pricelists;
        push @pricelists, "";
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Description")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Rate in 100ths/pennies")
          . "</td><td>"
          . gettext("Cycle")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
"
          . "<tr><td>"
          . hidden(
            -name  => "mode",
            -value => gettext("Periodic Charges")
          )
          . textfield( -name => "desc", -size => 20 )
          . "</td><td>"
          . popup_menu(
            -name   => "pricelist",
            -values => \@pricelists
          )
          . "</td><td>"
          . textfield( -name => "charge", -size => 8 )
          . "</td><td>"
          . popup_menu(
            -name   => "sweep",
            -values => \%sweeplist
          )
          . "</td><td>"
          . submit(
            -name  => 'action',
            -value => gettext("Insert...")
          )
          . "</td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Edit...") ) {
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
        return gettext("Please configure 'Pricelists'!") . "\n"
          unless @pricelists;
        push @pricelists, "";
        @pricelists = sort @pricelists;
        $body      = start_form;
        $body .= "<table class=\"default\">";
        my $chargeinfo = &get_charge( $astpp_db, $params->{chargeid} );
        $body .= "<tr class=\"header\"><td>"
          . hidden(
            -name  => "mode",
            -value => gettext("Periodic Charges")
          )
          . hidden( -name => "chargeid", -value => $params->{chargeid} )
          . gettext("ID")
          . "</td><td>"
          . gettext("Description")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Rate in 100ths/pennies")
          . "</td><td>"
          . gettext("Cycle")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
"
          . "<tr><td>"
          . $params->{chargeid}
          . "</td><td>"
          . textfield(
            -name    => "desc",
            -size    => 20,
            -default => $chargeinfo->{description}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "pricelist",
            -values  => \@pricelists,
            -default => $chargeinfo->{pricelist}
          )
          . "</td><td>"
          . textfield(
            -name    => "charge",
            -size    => 8,
            -default => $chargeinfo->{charge}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "sweep",
            -values  => \%sweeplist,
            -default => $chargeinfo->{sweep}
          )
          . "</td><td>"
          . submit(
            -name  => 'action',
            -value => gettext("Save...")
          )
          . "</td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $sql =
            "UPDATE charges SET "
          . "pricelist = "
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . "description = "
          . $astpp_db->quote( $params->{desc} ) . ", "
          . "charge = "
          . $astpp_db->quote( $params->{charge} ) . ", "
          . "sweep = "
          . $astpp_db->quote( $params->{sweep} )
          . ", status = '1' WHERE id = "
          . $astpp_db->quote( $params->{chargeid} );
        print STDERR "sql" if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Updated!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Periodic Charge Update Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Delete...") ) {
        my $sql =
          "DELETE FROM charges WHERE id = "
          . $astpp_db->quote( $params->{chargeid} );
        print STDERR "sql" if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Deleted!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Periodic Charge Delete Failed!");
        }
        $sql =
          "DELETE FROM charge_to_account WHERE charge_id = "
          . $astpp_db->quote( $params->{chargeid} );
        print STDERR $sql if $config->{debug} == 1;
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Deleted!");
        }
        else {
            print "$sql failed";
            $status .= gettext("Periodic Charge Delete Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body =
          start_form . "<table class=\"default\">" . "<tr class=\"header\"><td>"
          . hidden(
            -name  => "mode",
            -value => gettext("Periodic Charges")
          )
          . submit(
            -name  => 'action',
            -value => gettext("Add...")
          )
          . "</td></tr>
<tr class=\"header\"><td>"
          . gettext("Id")
          . "</td><td>"
          . gettext("Description")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Charge")
          . "</td><td>"
          . gettext("Cycle")
          . "</td><td>"
          . gettext("Status")
          . "</td><td colspan=2>"
          . gettext("Action")
          . "</td></tr>
";
        my $sql =
          $astpp_db->prepare(
            'SELECT description FROM charges WHERE status < 2');
        $sql->execute
          || return gettext("Something is wrong with the charge database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print "Pages Required: $pagesrequired\n"
          if ( $config->{debug} eq "YES" );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM charges WHERE status < 2 ORDER BY description limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the charges database!")
          . "\n";
        my $count = 0;

        while ( my $chargeinfo = $sql->fetchrow_hashref ) {
            $count++;
            my ($chargestat);
            if ( $chargeinfo->{status} == 0 ) {
                $chargestat = $inactive;
            }
            elsif ( $chargeinfo->{status} == 1 ) {
                $chargestat = $active;
            }
            elsif ( $chargeinfo->{status} == 2 ) {
                $chargestat = $deleted;
            }
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
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $body .=
                "<td>$chargeinfo->{id}"
              . "</td><td>$chargeinfo->{description}"
              . "</td><td>$chargeinfo->{pricelist}"
              . "</td><td>$chargeinfo->{charge}"
              . "</td><td>$chargeinfo->{sweep}"
              . "</td><td>$chargestat"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Periodic Charges")
              . "&chargeid=$chargeinfo->{id}&action="
              . gettext("Edit...") . "\">"
              . "<img src=\"/_astpp/edit.jpg\" alt=" . gettext("Edit...") . "></a>"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Periodic Charges")
              . "&chargeid=$chargeinfo->{id}&action="
              . gettext("Delete...") . "\">"
              . "<img src=\"/_astpp/deactivate.jpg\" alt=" . gettext("Delete...") . "></a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Periodic Charges")
                      . "&action="
                      . gettext("Information...")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Periodic Charges")
                      . "&action="
                      . gettext("Information...")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
                }
                else {
                    $pageno = $i + 1;
                    $body .= " |";
                }
            }
        }
        $body .= "";
        $body .= "Page $pageno of $pagesrequired";
    }
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_import_outbound_routes() {
    my ( $body, $status );
    return gettext(
        "Cannot import outbound routes until database is configured!")
      unless $astpp_db;
    if ( $params->{action} eq gettext("Import...") ) {
        my $csv     = Text::CSV->new();
        my $prepend = "^";
        my $append  = ".*";
        $uploaded = upload('rateimport');
        my ( @data, $record );
        while ( my $record = <$uploaded> ) {
            chomp;
            push @data, $record;
        }
        foreach my $temp (@data) {
            if ( $csv->parse($temp) ) {
                @columns = $csv->fields();
                my $pattern =
                  $prepend . $columns[0] . $columns[2] . $columns[3] . $append;
                if (
                    $astpp_db->do(
                            "DELETE FROM outbound_routes WHERE pattern = "
                          . $astpp_db->quote($pattern)
                          . "AND trunk = "
                          . $astpp_db->quote( $columns[9] )
                    )
                  )
                {
                    $status .=
                      gettext("Dropped route") . " '" . $pattern . "'. <br>";
                }
                else {
                    $status .=
                      gettext("Unable to drop route") . " '" . $pattern
                      . "'.<br>";
                }
                my $tmp =
"INSERT INTO outbound_routes (pattern,comment,connectcost,includedseconds,"
                  . "cost,inc,trunk,prepend,status) VALUES ("
                  . $astpp_db->quote($pattern) . ","
                  . $astpp_db->quote( $columns[4] ) . ","
                  . $astpp_db->quote( $columns[5] * 10000 ) . ","
                  . $astpp_db->quote( $columns[6] ) . ","
                  . $astpp_db->quote( $columns[7] * 10000 ) . ","
                  . $astpp_db->quote( $columns[8] ) . ","
                  . $astpp_db->quote( $columns[9] ) . ","
                  . $astpp_db->quote( $columns[1] ) . ", 1)";
                if ( $astpp_db->do($tmp) ) {
                    $status .=
                        gettext("Pattern: ") . " '" . $pattern . "' "
                      . gettext("has been created.") . "<br>";
                }
                else {
                    $status .=
                        gettext("Pattern: ") . " '" . $pattern . "' "
                      . gettext("FAILED to create")
                      . " ($tmp)!<br>";
                }
            }
            else {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }
    else {
        $body .=
            "<table class=\"default\"><tr><td>"
          . start_multipart_form
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Import Rate File")
          . "</td><td>"
          . gettext("File must be in the following format:")
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"LD PREPEND CODE ie. 00 or 011(We add this one),Outgoing LD PREPEND (Only used for dialing out)"
              . "CountryCode,Area Code,Comment,Connect Cost,"
              . "Included Seconds,Per Minute Cost,Increment,Trunk" )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"The file shall have the text fields escaped with quotation marks and the fields seperated by commas."
          )
          . "</td></tr>"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"You do not need to have the patterns setup as regex patterns.  Your pattern will have \"^\" prepended and \".*\" appended."
          )
          . "</td></tr>"
          . "<tr></tr>"
          . "<tr class=\"header\"><td>"
          . gettext("Select the file:")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr class=\"rowone\"><td>"
          . hidden(
            -name  => "mode",
            -value => "Import Outbound Routes"
          )
          . filefield(
            -name => "rateimport",
            -size => 40
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Import...")
          )
          . "</td></tr>
"
          . "<td><br><hr> $status </td>";
        $body .= "</table>
";
    }
    $body .= $status;
    return $body;
}

sub build_import_routes() {
    my ( $body, $status,$reseller );
    return gettext("Cannot import routes until database is configured!")
      unless $astpp_db;
    my ($uploaded);
    if ( param('action') eq gettext("Import...") ) {
        my $csv     = Text::CSV->new();
        my $prepend = "^";
        my $append  = ".*";
        $uploaded = upload('rateimport');
        my ( @data, $record );
        while ( my $record = <$uploaded> ) {
            chomp;
            push @data, $record;
        }
	if ($params->{logintype} == 1) {
		$reseller = $params->{username};
	} else {
		$reseller = "";
	}
        foreach my $temp (@data) {
            if ( $csv->parse($temp) ) {
		my $tmp;
                @columns = $csv->fields();
                my $pattern =
                  $prepend . $columns[0] . $columns[1] . $columns[2] . $append;
                $status .=
"$pattern $columns[3] $columns[6] $columns[4] $columns[5] $columns[7]<br>";
		if ($params->{logintype} == 1) {
			my $pricelistdata = &get_pricelist($astpp_db, $columns[7]);
			if ($pricelistdata->{reseller} eq $params->{username}) {
				$valid = 1;
			}
		} else {
			$valid = 1;
		}
		if ($valid == 1) {
                $tmp = "DELETE FROM routes WHERE pattern = "
                          . $astpp_db->quote($pattern)
                          . "AND pricelist = "
                          . $astpp_db->quote( $columns[7] );
		} else {
			$tmp = "";
		}
                if (
                    $astpp_db->do($tmp)
                  )
                {
                    $status .=
                      gettext("Dropped route") . " '" . $pattern . "'.<br>";
                }
                else {
                    $status .=
                      gettext("Unable to drop route") . " '" . $pattern
                      . "'.(" . $tmp . ")<br>";
                }
		if ($valid == 1) {
                $tmp =
"INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost,inc,reseller,status) VALUES ("
                  . $astpp_db->quote($pattern) . ","
                  . $astpp_db->quote( $columns[3] ) . ","
                  . $astpp_db->quote( $columns[7] ) . ","
                  . $astpp_db->quote( $columns[4] * 10000 ) . ","
                  . $astpp_db->quote( $columns[5] ) . ","
                  . $astpp_db->quote( $columns[6] * 10000 ) . ","
                  . $astpp_db->quote( $columns[8] ) . ", "
		  . $astpp_db->quote( $reseller) . ", 1)";
		} else {
			$tmp = "";
		}
                if ( $astpp_db->do($tmp) ) {
                    $status .=
                        gettext("Pattern") . " '" . $pattern . "' "
                      . gettext("has been created.") . "<br>";
                }
                else {
                    $status .=
                        gettext("Pattern") . " '" . $pattern . "' "
                      . gettext("FAILED to create")
                      . " ($tmp)!<br>";
                }
            }
            else {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }
    else {
        $body .=
            "<table class=\"default\"><tr><td>"
          . start_multipart_form
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Import Rate File")
          . "<br></td><td>"
          . gettext("File must be in the following format:")
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"LD PREPEND CODE ie. 00 or 011,CountryCode,Area Code,Comment,Connect Cost,Included Seconds,Per Minute Cost,Pricelist,Increment"
          )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"The file shall have the text fields escaped with quotation marks and the fields seperated by commas."
          )
          . "</td></tr>"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"You do not need to have the patterns setup as regex patterns.  Your pattern will have \"^\" prepended and \".*\" appended."
          )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Select the file:")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . hidden(
            -name  => "mode",
            -value => "Import Routes"
          )
          . filefield(
            -name => "rateimport",
            -size => 40
          )
          . "&nbsp;&nbsp;&nbsp;&nbsp;</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Import...")
          )
          . "</td></tr>
"
          . "<td><br><hr> $status </td>";
        $body .= "</table>
";
    }
    $body .= $status;
    return $body;
}

sub build_import_dids() {
    my ( $body, $status );
    return gettext(
        "Cannot import dids until database is configured!")
      unless $astpp_db;
    if ( $params->{action} eq gettext("Import...") ) {
        my $csv     = Text::CSV->new();
        $uploaded = upload('didimport');
        my ( @data, $record );
        while ( my $record = <$uploaded> ) {
            chomp;
            push @data, $record;
        }

# Database Format:
#number CHAR(40) NOT NULL PRIMARY KEY, account CHAR(40) NOT NULL DEFAULT '',
#connectcost INTEGER NOT NULL DEFAULT 0, includedseconds INTEGER NOT NULL DEFAULT 0,
#monthlycost INTEGER NOT NULL DEFAULT 0, cost INTEGER NOT NULL DEFAULT 0,
#extensions CHAR(180) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL,
#provider CHAR(40) NOT NULL DEFAULT '', country CHAR (80)NOT NULL DEFAULT '',
#province CHAR (80) NOT NULL DEFAULT '', city CHAR (80) NOT NULL DEFAULT ''
        foreach my $temp (@data) {
            if ( $csv->parse($temp) ) {
                @columns = $csv->fields();
                if (
                    $astpp_db->do(
                            "DELETE FROM dids WHERE number = "
                          . $astpp_db->quote($columns[0])
                    )
                  )
                {
                    $status .=
                      gettext("Dropped DID: ") . " '" . $columns[0] . "'. <br>";
                }
                else {
                    $status .=
                      gettext("Unable to drop DID: ") . " '" . $columns[0]
                      . "'.<br>";
                }
                my $tmp =
"INSERT INTO dids (number,account,connectcost,includedseconds,"
                  . "monthlycost,cost,extensions,status,provider,country,province,city) VALUES ("
                  . $astpp_db->quote($columns[0]) . ","
                  . $astpp_db->quote( $columns[1] ) . ","
                  . $astpp_db->quote( $columns[2] * 10000 ) . ","
                  . $astpp_db->quote( $columns[3] ) . ","
                  . $astpp_db->quote( $columns[4] * 10000 ) . ","
                  . $astpp_db->quote( $columns[5] * 10000 ) . ","
                  . $astpp_db->quote( $columns[6] ) . ","
		. $astpp_db->quote( $columns[7] ) . ","
		. $astpp_db->quote( $columns[8] ) . ","
		. $astpp_db->quote( $columns[9] ) . ","
		. $astpp_db->quote( $columns[10] ) . ","
		. $astpp_db->quote( $columns[11] ) . ")";
                if ( $astpp_db->do($tmp) ) {
                    $status .=
                        gettext("DID: ") . " '" . $columns[0] . "' "
                      . gettext("has been created.") . "<br>";
                }
                else {
                    $status .=
                        gettext("DID: ") . " '" . $columns[0] . "' "
                      . gettext("FAILED to create")
                      . " ($tmp)!<br>";
                }
        $tmp = "DELETE FROM routes WHERE pattern = "
                . $astpp_db->quote( "^" . $params->{number} . "\$" )
                . " AND pricelist = "
                . $astpp_db->quote( $config->{default_brand} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("The old pattern for") . " '"
              . $params->{number} . "' "
              . gettext("has been removed.");
        }
        else {
            $status .=
                gettext("The old pattern for") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to remove!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $tmp =
"INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost) VALUES ("
          . $astpp_db->quote( "^" . $params->{number} . "\$" ) . ","
          . $astpp_db->quote( $params->{country} . ","
              . $params->{province} . ","
              . $params->{city} )
          . ","
          . $astpp_db->quote( $config->{default_brand} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("Failed to create.");
	}

            }
            else {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }
    else {
        $body .=
            "<table class=\"default\"><tr><td>"
          . start_multipart_form
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Import DID File")
          . "</td><td>"
          . gettext("File must be in the following format:")
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"number, account, connectcost, includedseconds, monthlycost, cost,"
. "extensions, status, provider, country, province, city")
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext(
"The file shall have the text fields escaped with quotation marks and the fields seperated by commas."
          )
          . "</td></tr>
"
          . "<tr></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Select the file:")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
"
          . "<tr class=\"rowone\"><td>"
          . hidden(
            -name  => "mode",
            -value => "Import DIDs"
          )
          . filefield(
            -name => "didimport",
            -size => 40
          )
          . "</td><td>"
          . submit(
            -name  => "action",
            -value => gettext("Import...")
          )
          . "</td></tr>
"
          . "<td><br><hr> $status </td>";
        $body .= "</table>
";
    }
    $body .= $status;
    return $body;
}


sub build_remove_account() {
    my ( $body, $tmp, $sql, $status, $number, @accountlist, @pricelists, $accountinfo );
    if ( $params->{action} eq gettext("Deactivate...") ) {
        if ( $params->{number} ne "" ) {
            $number = $params->{number};
        }
        else {
            $number = $params->{accountlist};
        }
	$accountinfo = &get_account($astpp_db,$number);
	if ($params->{logintype} == 1) {
        $tmp =
            "UPDATE accounts SET "
          . "status = 2 WHERE number = "
          . $astpp_db->quote($number)
	  . " AND reseller = "
          . $astpp_db->quote($params->{username});
	} else {
        $tmp =
            "UPDATE accounts SET "
          . "status = 2 WHERE number ="
          . $astpp_db->quote($number);
	}
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Account") . " " . $number . " "
              . gettext("Successfully $accountinfo->{number} Deactivated $accountinfo->{type}!") . "\n";
		if ($accountinfo->{type} == 1) {
			my $tmp = "UPDATE resellers SET status = 2 WHERE name = "
			. $astpp_db->quote($number);
			$astpp_db->do($tmp);
		}
        }
        else {
            $status .=
                gettext("Account") . " " . $number . " "
              . gettext("Failed To Deactivate!") . "\n";
            print "$tmp failed";
        }
    }
    @accountlist =  &list_accounts_selective( $astpp_db, $reseller, "-1" );
    @accountlist = sort @accountlist;
    $body .= start_form
      . "<table class=\"default\"><tr class=\"header\"><td colspan=3>"
      . hidden( -name => "mode", -value => gettext("Remove Account") )
      . "Remove Account"
      . "</td></tr>
<tr class=\"header\"><td colspan = 2>"
      . "Either select or enter the account number to remove"
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . popup_menu(
        -name   => "accountlist",
        -values => \@accountlist
      )
      . "</td><td>"
      . textfield( -name => "number", -size => 20 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Deactivate...") )
      . "</td></tr>
<tr><td colspan = 2> $status </td></tr>
</table>
";
    return $body;
}

sub build_process_payment() {
    my ( $status, $body, $number, $reseller );
    return gettext("Database not configured!") unless $astpp_db;
    if ($params->{logintype} == 1) {
	$reseller = $params->{username};
    } else {
	$reseller = "";
    }
    my @accountlist = &list_accounts_selective($astpp_db,$reseller,"-1");
    unshift( @accountlist, "" );
    if ( $params->{action} eq gettext("Refill...") ) {
        if ( param('number') ne "" ) {
            $number = param('number');
        }
        else {
            $number = param('accountlist');
        }
	if ($params->{logintype} == 1) {
		$accountinfo = &get_account($astpp_db, $number);
		if ($accountinfo->{reseller} eq $params->{username}) {
        		$status .=
				&refill_account( $astpp_db, $number,
            			$params->{refilldollars} * 10000 );
		}
	} else {
        $status .=
          &refill_account( $astpp_db, $number,
            $params->{refilldollars} * 10000 );
	}
    }
    $body .= start_form
      . "<table class=\"default\"><tr class=\"header\"><td colspan=4>"
      . hidden( -name => "mode", -value => gettext("Process Payment") )
      . "Process Payment"
      . "</td></tr>
<tr class=\"header\"><td colspan = 2>"
      . "Either select or enter the account number to apply payment"
      . "</td><td>"
      . gettext("Payment in")
      . " $currency[0]</td><td>"
      . gettext("Action")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . popup_menu(
        -name   => "accountlist",
        -values => \@accountlist
      )
      . "</td><td>"
      . textfield( -name => "number", -size => 20 )
      . "</td><td>"
      . textfield( -name => "refilldollars", -size => 8 )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Refill...") )
      . "</td></tr>
<tr><td colspan = 2> $status </td></tr>
</table>
";
    return $body;
}

sub build_pricelists() {
    my ( $sql, $record, $valid, $count, $tmp, $pagesrequired, $pageno );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Edit...") ) {
        $body = start_form;
        my $pricelistinfo = &get_pricelist( $astpp_db, $params->{name} );
	if ($params->{logintype} == 1) {
		if ($pricelistinfo->{reseller} eq $params->{username}) {	
			$valid = 1;
		} else {
			$valid = 0;
		}
	} else {
		$valid = 1;
	}
	if ($valid == 1) {
   	     $body .=
	            "<table class=\"default\">"
	          . "<tr class=\"header\"><td>"
	          . hidden( -name => "mode", -value => gettext("Pricelists") )
	       	   . gettext("Pricelist Name")
	          . "</td><td>"
	          . gettext("Default Increment")
	          . "</td><td>"
	          . gettext("Markup in 1/100 of 1\%")
	          . "</td><td>"
	          . gettext("Action")
	          . "</td></tr>";
	        $body .= "<tr class=\"rowone\"><td>"
	          . textfield(
	            -name    => 'name',
	            -size    => 20,
	            -default => $pricelistinfo->{name}
	          )
	          . "</td><td>"
	          . textfield(
	            -name    => 'inc',
	            -size    => 4,
	            -default => $pricelistinfo->{inc}
	          )
	          . "</td><td>"
	          . textfield(
	            -name    => 'markup',
	            -size    => 6,
	            -default => $pricelistinfo->{markup}
	          )
	          . "</td><td>"
	          . hidden( -name => 'oldname', -value => $params->{name} )
	          . submit( -name => 'action', -value => gettext("Save...") )
	          . "</td></tr>
		</table>
		";
	} else {
		$body = "<table><tr><td>"
		 . gettext("You do not own this pricelist.")
		. "</td></tr></table>";
	}
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        my $pricelistinfo = &get_pricelist( $astpp_db, $params->{name} );
        if ( $pricelistinfo->{name} ) {
		if ($params->{logintype} == 1) {
			if ($pricelistinfo->{reseller} eq $params->{username}) {	
				$valid = 1;
			} else {
				$valid = 0;
			}
		} else {
			$valid = 1;
		}
		if ($valid == 1){
	            my $tmp =
	              "UPDATE pricelists SET status = 1 WHERE name = "
	              . $astpp_db->quote( $params->{name} );
	            if ( $astpp_db->do($tmp) ) {
	                $status .=
	                    gettext("Pricelist: ")
	                  . $params->{name}
	                  . gettext(" Reactivated Successfully!");
	            }
	            else {
       		         $status .=
	                    gettext("Pricelist: ")
	                  . $params->{name}
	                  . gettext(" Failed to Reactivate!");
	                print STDERR $tmp if $config->{debug} == 1;
	            }
		} else {
			$status .= gettext("You do not own this pricelist");
		}
	} 
        else {
		if ($params->{logintype} == 1) {
			$params->{reseller} = $params->{username};
		}
		if ($params->{reseller}) {
	        $tmp =
                "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
              . $astpp_db->quote( $params->{name} ) . ", "
              . $astpp_db->quote( $params->{inc} ) . ", "
              . $astpp_db->quote( $params->{markup} ) . ", 1, "
              . $astpp_db->quote( $params->{reseller} ) . ")";
		} else {
	        $tmp =
                "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
              . $astpp_db->quote( $params->{name} ) . ", "
              . $astpp_db->quote( $params->{inc} ) . ", "
              . $astpp_db->quote( $params->{markup} ) . ", 1, NULL)";
		}
            if ( $astpp_db->do($tmp) ) {
                $status .=
                    gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Added Successfully!");
            }
            else {
                $status .=
                    gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Failed to Add!");
                print STDERR $tmp if $config->{debug} == 1;
            }
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
	if ($params->{logintype} == 1) {
        	my $pricelistinfo = &get_pricelist( $astpp_db, $params->{oldname} );
		if ($pricelistinfo->{reseller} eq $params->{username}) {	
			$valid = 1;
			$params->{reseller} = $params->{username};
		} else {
			$valid = 0;
		}
	} else {
		$valid = 1;
	}
	if ($valid == 1) {
	        $tmp =
	            "UPDATE pricelists SET name = "
	          . $astpp_db->quote( $params->{name} ) . ", " 
		  . " inc = "
	          . $astpp_db->quote( $params->{inc} ) . ", "
	          . " markup = "
	          . $astpp_db->quote( $params->{markup} ) . ", "
		  . " reseller = "
	          . $astpp_db->quote( $params->{reseller} )
	          . " WHERE name = "
	          . $astpp_db->quote( $params->{oldname} );
		 if ( $astpp_db->do($tmp) ) {
	            $status .=
	                gettext("Pricelist: ")
	              . $params->{name}
	              . gettext(" Updated Successfully!");
	        }
	        else {
	            $status .=
	                gettext("Pricelist: ")
	              . $params->{name}
	              . gettext(" Failed to Update!");
	            print STDERR $tmp if $config->{debug} == 1;
		}
	}
        else {
            $status .=
                gettext("Pricelist: ")
              . $params->{name}
              . gettext(" Does Not Belong to You!");
	}
       	$params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
	if ($params->{logintype} == 1) {
        	my $pricelistinfo = &get_pricelist( $astpp_db, $params->{name} );
		if ($pricelistinfo->{reseller} eq $params->{username}) {	
			$valid = 1;
		} else {
			$valid = 0;
		}
	} else {
		$valid = 1;
	}
	if ($valid == 1){
        my $tmp =
          "UPDATE pricelists SET status = 2 WHERE name = "
          . $astpp_db->quote( $params->{name} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pricelist: ")
              . $params->{name}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("Pricelist: ")
              . $params->{name}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Pricelists") )
          . gettext("Pricelist Name")
          . "</td><td>"
          . gettext("Default Increment")
          . "</td><td>"
          . gettext("Markup in 1/100 of 1\%")
          . "</td><td>"
	  . gettext("Reseller")
	  . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr class=\"rowone\"><td>"
          . textfield(
            -name => 'name',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'inc',
            -size => 4
          )
          . "</td><td>"
          . textfield(
            -name => 'markup',
            -size => 6
          )
          . "</td><td>";
	  if ($params->{logintype} == 1) {
		$body .= $params->{username};
	  } else {
          $body .= textfield(
            -name => 'reseller',
            -size => 20
          ); }
	  $body .=
           "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Pricelists") )
          . gettext("Pricelist Name")
          . "</td><td>"
          . gettext("Default Increment")
          . "</td><td>"
          . gettext("Markup in 1/100 of 1\%")
          . "</td><td>"
	  . gettext("Reseller")
	  . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
	if ($params->{logintype} == 1) {
        $tmp = "SELECT * FROM pricelists WHERE status < 2 AND reseller = "
		. $astpp_db->quote($params->{username});
	} else {
        $tmp = "SELECT * FROM pricelists WHERE status < 2";
	}
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
	if ($params->{logintype} == 1) {
          $astpp_db->prepare(
"SELECT * FROM pricelists WHERE status < 2 AND reseller = "
. $astpp_db->quote($params->{username}) . "ORDER BY name limit"
. " $params->{limit} , $results_per_page");
	} else {
        $sql =
          $astpp_db->prepare(
"SELECT * FROM pricelists WHERE status < 2 ORDER BY name limit $params->{limit} , $results_per_page"
          );
	}
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $pricelistinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$pricelistinfo->{name}"
              . "</td><td>$pricelistinfo->{inc}"
              . "</td><td>$pricelistinfo->{markup}"
              . "</td><td>$pricelistinfo->{reseller}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Pricelists")
              . "&action="
              . gettext("Edit...")
              . "&name="
              . $pricelistinfo->{name} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Pricelists")
              . "&action="
              . gettext("Deactivate...")
              . "&name="
              . $pricelistinfo->{name} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Pricelists")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Pricelists")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_dids() {
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired,@accountlist,@providerlist );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Edit...") ) {
        @providerlist = &list_providers($astpp_db);
        return gettext(
            "Please define at least one provider before creating DIDs!")
          . "\n"
          unless @providerlist;
         @accountlist = &list_accounts($astpp_db);
        push @accountlist, "";
        my $didinfo = &get_did( $astpp_db, $params->{number} );
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode',   -value => gettext("DIDs") )
          . hidden( -name => 'number', -value => $params->{number} )
          . gettext("Number")
          . "</td><td>"
          . gettext("Country")
          . "</td><td>"
          . gettext("Province")
          . "</td><td>"
          . gettext("City")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Account")
          . "</td><td>"
          . gettext("Dialstring")
          . "</td><td>"
      . "<acronym title=\""
      . gettext("This fee is charged monthly") . "\">"
      . gettext("Monthly")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"") . "\">"
      . gettext("Connect")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Number of seconds included in the connection fee.") . "\">"
      . gettext("Included")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Cost per minute.") . "\">"
      . gettext("Cost")
      . "</acronym>"
          . gettext("Cost per additional minute")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>" . $didinfo->{number} . "</td><td>"
          . textfield(
            -name    => 'country',
            -size    => 20,
            -default => $didinfo->{country}
          )
          . "</td><td>"
          . textfield(
            -name    => 'province',
            -size    => 20,
            -default => $didinfo->{province}
          )
          . "</td><td>"
          . textfield(
            -name    => 'city',
            -size    => 20,
            -default => $didinfo->{city}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'provider',
            -values  => \@providerlist,
            -default => $didinfo->{provider}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'account',
            -values  => \@accountlist,
            -default => $didinfo->{account}
          )
          . "</td><td>"
          . textfield(
            -name    => 'extension',
            -size    => 20,
            -default => $didinfo->{extensions}
          )
          . "</td><td>"
          . textfield(
            -name    => 'monthlycost',
            -size    => 20,
            -default => $didinfo->{monthlycost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'connectcost',
            -size    => 20,
            -default => $didinfo->{connectcost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'included',
            -size    => 20,
            -default => $didinfo->{included}
          )
          . "</td><td>"
          . textfield(
            -name    => 'cost',
            -size    => 20,
            -default => $didinfo->{cost}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        $tmp =
"INSERT INTO dids (number,account,monthlycost,connectcost,includedseconds,cost,extensions,provider,country,city,province,status) VALUES ("
          . $astpp_db->quote( $params->{number} ) . ","
          . $astpp_db->quote( $params->{account} ) . ","
          . $astpp_db->quote( $params->{monthlycost} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{extension} ) . ","
          . $astpp_db->quote( $params->{provider} ) . ","
          . $astpp_db->quote( $params->{country} ) . ","
          . $astpp_db->quote( $params->{city} ) . ","
          . $astpp_db->quote( $params->{province} ) . ", 1)";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("DID.") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("DID") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to create!");
            print STDERR $tmp if $config->{debug} == 1;
        }
	$tmp = "DELETE FROM routes WHERE pattern = "
		. $astpp_db->quote( "^" . $params->{number} . "\$" )
		. " AND pricelist = "
		. $astpp_db->quote( $config->{default_brand} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("The old pattern for") . " '"
              . $params->{number} . "' "
              . gettext("has been removed.");
        }
        else {
            $status .=
                gettext("The old pattern for") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to remove!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $tmp =
"INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost) VALUES ("
          . $astpp_db->quote( "^" . $params->{number} . "\$" ) . ","
          . $astpp_db->quote( $params->{country} . ","
              . $params->{province} . ","
              . $params->{city} )
          . ","
          . $astpp_db->quote( $config->{default_brand} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to create!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        $tmp =
            "UPDATE dids SET"
          . " account="
          . $astpp_db->quote( $params->{account} ) . ","
          . " monthlycost="
          . $astpp_db->quote( $params->{monthlycost} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{included} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . ","
          . " extensions="
          . $astpp_db->quote( $params->{extension} ) . ","
          . " provider="
          . $astpp_db->quote( $params->{provider} ) . ","
          . " country="
          . $astpp_db->quote( $params->{country} ) . ","
          . " city="
          . $astpp_db->quote( $params->{city} ) . ","
          . " province="
          . $astpp_db->quote( $params->{province} )
          . ", status=1 WHERE number="
          . $astpp_db->quote( $params->{number} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("DID") . " '"
              . $params->{number} . "' "
              . gettext("has been updated.");
        }
        else {
            $status .=
                gettext("DID") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to update!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $tmp =
            "UPDATE routes SET"
          . " comment = "
          . $astpp_db->quote( $params->{country} . ","
              . $params->{province} . ","
              . $params->{city} )
          . ","
          . " pricelist="
          . $astpp_db->quote( $config->{default_brand} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{included} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} )
          . " WHERE pattern = "
          . $astpp_db->quote( "^" . $params->{number} . "\$" );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to create!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
        my $tmp =
          "DELETE FROM dids WHERE number = "
          . $astpp_db->quote( $params->{number} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("DID: ")
              . $params->{number}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("DID: ")
              . $params->{number}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        @providerlist = &list_providers($astpp_db);
        return gettext(
            "Please define at least one provider before creating DIDs!")
          . "\n"
          unless @providerlist;
        @accountlist = &list_accounts($astpp_db);
        push @accountlist, "";
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Manage DIDs") )
          . gettext("Number")
          . "</td><td>"
          . gettext("Country")
          . "</td><td>"
          . gettext("Province")
          . "</td><td>"
          . gettext("City")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Account")
          . "</td><td>"
          . gettext("Dialstring")
          . "</td><td>"
      . "<acronym title=\""
      . gettext("This fee is charged monthly") . "\">"
      . gettext("Monthly")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"") . "\">"
      . gettext("Connect")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Number of seconds included in the connection fee.") . "\">"
      . gettext("Included")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Cost per minute.") . "\">"
      . gettext("Cost")
      . "</acronym>"

          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>"
          . textfield(
            -name => 'number',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'country',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'province',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'city',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'provider',
            -values => \@providerlist
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'account',
            -values => \@accountlist
          )
          . "</td><td>"
          . textfield(
            -name => 'extension',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'monthlycost',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'connectcost',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'included',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'cost',
            -size => 20
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Manage DIDs") )
          . gettext("Number")
          . "</td><td>"
          . gettext("Country")
          . "</td><td>"
          . gettext("Province")
          . "</td><td>"
          . gettext("City")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Account")
          . "</td><td>"
          . gettext("Dialstring")
          . "</td><td>"
      . "<acronym title=\""
      . gettext("This fee is charged monthly") . "\">"
      . gettext("Monthly")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"") . "\">"
      . gettext("Connect")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Number of seconds included in the connection fee.") . "\">"
      . gettext("Included")
      . "</acronym>"
          . "</td><td>"
      . "<acronym title=\""
      . gettext("Cost per minute.") . "\">"
      . gettext("Cost")
      . "</acronym>"

          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $tmp = "SELECT * FROM dids WHERE status < 2";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM dids WHERE status < 2 ORDER BY number limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $record = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$record->{number}</td><td>$record->{country}</td>"
              . "<td>$record->{province}</td><td>$record->{city}</td>"
              . "<td>$record->{provider}</td><td>$record->{account}</td>"
              . "<td>$record->{extensions}</td><td>$record->{monthlycost}</td>"
              . "<td>$record->{connectcost}</td><td>$record->{includedseconds}</td>"
              . "<td>$record->{cost}</td>"
              . "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("Manage DIDs")
              . "&action="
              . gettext("Edit...")
              . "&number="
              . $record->{number} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Manage DIDs")
              . "&action="
              . gettext("Deactivate...")
              . "&number="
              . $record->{number} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Manage DIDs")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Manage DIDs")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_routes() {
    my ( @pricelists,$sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ($params->{logintype} == 1) {
	@pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
	@pricelists = &list_pricelists($astpp_db);
    }
    return gettext("Pricelists Do NOT Exist!") . "\n" unless @pricelists;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }

    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Edit...") ) {
	if ($params->{logintype} == 1) {
        $tmp =
          "SELECT * FROM routes WHERE id = "
          . $astpp_db->quote( $params->{id} )
	  . " AND pricelist = "
          . $astpp_db->quote( $params->{username} );
	} else {
        $tmp =
          "SELECT * FROM routes WHERE id = "
          . $astpp_db->quote( $params->{id} );
	}
	print STDERR $tmp if $config->{debug} == 1;
        my $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td><td colspan=8>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Routes") )
          . hidden( -name => 'id',   -value => $params->{id} )
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Pricelist(s)")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost Per Add. Minute")
          . "</td><td>"
          . gettext("Increments")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr class=\"rowone\"><td>"
          . textfield(
            -name    => 'pattern',
            -size    => 20,
            -default => $record->{pattern}
          )
          . "</td><td>"
          . textfield(
            -name    => 'comment',
            -size    => 20,
            -default => $record->{comment}
          )
          . "</td><td>";
	  if ($params->{logintype} == 1) {
		$body .= $params->{username};
	  } else {
          	$body .= popup_menu(
            	-name    => 'pricelist',
            	-values  => \@pricelists,
            	-default => $record->{pricelist}
          	);
	  }
          $body .= "</td><td>"
          . textfield(
            -name    => 'connectcharge',
            -size    => 8,
            -default => $record->{connectcost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'incseconds',
            -size    => 8,
            -default => $record->{includedseconds}
          )
          . "</td><td>"
          . textfield(
            -name    => 'cost',
            -size    => 8,
            -default => $record->{cost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'inc',
            -size    => 8,
            -default => $record->{inc}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
	my $reseller;
	if ($params->{logintype} == 1) {
		my $pricelistinfo = &get_pricelist($astpp_db, $params->{pricelist});
		if ($pricelistinfo->{reseller} ne $params->{username}) {
			$params->{pricelist} = $params->{username};
		}
		$reseller = $params->{username};
	} else {
		$reseller = "";
	}
        $tmp =
"INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost,inc,reseller) VALUES ("
          . $astpp_db->quote( $params->{pattern} ) . ","
          . $astpp_db->quote( $params->{comment} ) . ","
          . $astpp_db->quote( $params->{pricelist} ) . ","
          . $astpp_db->quote( $params->{connectcharge} ) . ","
          . $astpp_db->quote( $params->{incseconds} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote( $reseller ) . ")";
	print STDERR $tmp if $config->{debug} == 1;
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{pattern} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("Pattern") . " '"
              . $params->{pattern} . "' "
              . gettext("FAILED to create!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
	if ($params->{logintype} == 1) {
		$params->{pricelist} = $params->{username};
	}
        $tmp =
            "UPDATE routes SET"
          . " pattern="
          . $astpp_db->quote( $params->{pattern} ) . ","
          . " comment="
          . $astpp_db->quote( $params->{comment} ) . ","
          . " pricelist="
          . $astpp_db->quote( $params->{pricelist} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcharge} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{incseconds} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . "," . " inc="
          . $astpp_db->quote( $params->{inc} )
          . " WHERE id = "
          . $astpp_db->quote( $params->{id} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{id} . "' "
              . gettext("has been saved.");
        }
        else {
            $status .=
                gettext("Pattern") . " '"
              . $params->{id} . "' "
              . gettext("FAILED to saved!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
	if ($params->{logintype} == 1) {
        $tmp =
          "UPDATE routes SET status = 2 WHERE id = "
          . $astpp_db->quote( $params->{id} )
	  . " AND pricelist = "
          . $astpp_db->quote( $params->{username} );
	} else {
        $tmp =
          "UPDATE routes SET status = 2 WHERE id = "
          . $astpp_db->quote( $params->{id} );
	}
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Route: ")
              . $params->{id}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("Route: ")
              . $params->{id}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
    if ($params->{logintype} == 1) {
	@pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
	@pricelists = &list_pricelists($astpp_db);
    }
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td><td colspan=8>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Routes") )
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Pricelist(s)")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost Per Add. Minute")
          . "</td><td>"
          . gettext("Increments")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr class=\"rowone\"><td>"
          . textfield(
            -name => 'pattern',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'comment',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'pricelist',
            -values => \@pricelists
	  )
          . "</td><td>"
          . textfield(
            -name => 'connectcharge',
            -size => 8
          )
          . "</td><td>"
          . textfield(
            -name => 'incseconds',
            -size => 8
          )
          . "</td><td>"
          . textfield(
            -name => 'cost',
            -size => 8
          )
          . "</td><td>"
          . textfield(
            -name => 'inc',
            -size => 8
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=4>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td><td colspan=5>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Routes") )
          . gettext("ID")
          . "</td><td>"
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Pricelist(s)")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost Per Add. Minute")
          . "</td><td>"
          . gettext("Increments")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
    if ($params->{logintype} == 1) {
        $tmp = "SELECT * FROM routes WHERE status < 2 AND reseller IN (NULL,'')"
          . " AND pricelist = " . $astpp_db->quote( $params->{username} )
          . " UNION SELECT * FROM routes WHERE status < 2 "
	  . " AND reseller = " . $astpp_db->quote($params->{username});
    } else {
        $tmp = "SELECT * FROM routes WHERE status < 2 AND reseller IN (NULL,'')";
    }
	print STDERR $tmp if ($config->{debug} == 1);
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
    if ($params->{logintype} == 1) {
        $sql =
          $astpp_db->prepare(
"SELECT * FROM routes WHERE reseller IN (NULL,'') AND pricelist = "
          . $astpp_db->quote( $params->{username} )
          . " UNION SELECT * FROM routes WHERE status < 2 "
	  . " AND reseller = " . $astpp_db->quote($params->{username})
. " AND status < 2 ORDER BY comment limit $params->{limit} , $results_per_page"
          );
    } else {
        $sql =
          $astpp_db->prepare(
"SELECT * FROM routes WHERE reseller IN (NULL,'') AND status < 2 ORDER BY comment limit $params->{limit} , $results_per_page"
          );
    }
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $record = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
"<td>$record->{id}</td><td>$record->{pattern}</td><td>$record->{comment}</td>"
              . "<td>$record->{pricelist}</td>"
              . "<td>$record->{connectcost}</td><td>$record->{includedseconds}</td>"
              . "<td>$record->{cost}</td><td>$record->{inc}</td>"
              . "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("Routes")
              . "&action="
              . gettext("Edit...") . "&id="
              . $record->{id} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Routes")
              . "&action="
              . gettext("Deactivate...") . "&id="
              . $record->{id} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Routes")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Routes")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_packages() {
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Edit...") ) {
    if ($params->{logintype} == 1) {
        @pricelists = &list_pricelists($astpp_db,$params->{username});
    } else {
        @pricelists = &list_pricelists($astpp_db);
    }
        my $tmp        =
          "SELECT * FROM packages WHERE id = "
          . $astpp_db->quote( $params->{id} );
        my $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        my $record = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Packages") )
          . hidden( -name => "id",   -value => $params->{id} )
          . gettext("Counter Name")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>"
          . textfield(
            -name    => 'name',
            -size    => 20,
            -default => $record->{name}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'pricelist',
            -values  => \@pricelists,
            -default => $record->{pricelist}
          )
          . "</td><td>"
          . textfield(
            -name    => 'pattern',
            -size    => 6,
            -default => $record->{pattern}
          )
          . "</td><td>"
          . textfield(
            -name    => 'includedseconds',
            -size    => 6,
            -default => $record->{includedseconds}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        my $tmp =
"INSERT INTO packages (name,pricelist,pattern,includedseconds,status) VALUES ("
          . $astpp_db->quote( $params->{name} ) . ", "
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . $astpp_db->quote( $params->{pattern} ) . ", "
          . $astpp_db->quote( $params->{includedseconds} ) . ",1)";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Package: ")
              . $params->{name}
              . gettext(" Added Successfully!");
        }
        else {
            $status .=
                gettext("Package: ")
              . $params->{name}
              . gettext(" Failed to Add!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $tmp =
            "UPDATE pricelists SET" . " name="
          . $astpp_db->quote( $params->{name} ) . ", " . " inc="
          . $astpp_db->quote( $params->{inc} ) . ", "
          . " markup="
          . $astpp_db->quote( $params->{markup} )
          . " WHERE id = "
          . $astpp_db->quote( $params->{id} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Package: ")
              . $params->{name}
              . gettext(" Updated Successfully!");
        }
        else {
            $status .=
                gettext("Package: ")
              . $params->{name}
              . gettext(" Failed to Update!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
        my $tmp =
          "UPDATE packages SET status = 2 WHERE id = "
          . $astpp_db->quote( $params->{id} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Package: ")
              . $params->{id}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("Package: ")
              . $params->{id}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
    if ($params->{logintype} == 1) {
	@pricelists = ($params->{username});
    } else {
	@pricelists = &list_pricelists($astpp_db);
    }
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Packages") )
          . gettext("Counter Name")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>"
          . textfield(
            -name => 'name',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'pricelist',
            -values => \@pricelists
          )
          . "</td><td>"
          . textfield(
            -name => 'pattern',
            -size => 6
          )
          . "</td><td>"
          . textfield(
            -name => 'includedseconds',
            -size => 6
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Packages") )
          . gettext("Counter Name")
          . "</td><td>"
          . gettext("Pricelist")
          . "</td><td>"
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $tmp = "SELECT * FROM packages WHERE status < 2";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM packages WHERE status < 2 ORDER BY name limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $packageinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$packageinfo->{name}"
              . "</td><td>$packageinfo->{pricelist}"
              . "</td><td>$packageinfo->{pattern}"
              . "</td><td>$packageinfo->{includedseconds}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Packages")
              . "&action="
              . gettext("Edit...") . "&id="
              . $packageinfo->{id} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Packages")
              . "&action="
              . gettext("Deactivate...") . "&id="
              . $packageinfo->{id} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Packages")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Packages")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_trunks() {
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    my @providerlist = &list_providers($astpp_db);
    return gettext("No Providers Exist!") . "\n" unless @providerlist;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }

    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Edit...") ) {
        my $tmp =
          "SELECT * FROM trunks WHERE name = "
          . $astpp_db->quote( $params->{name} );
        my $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        my $record = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Trunks") )
          . hidden( -name => "name", -value => $params->{name} )
          . gettext("Trunk Name")
          . "</td><td>"
          . gettext("Protocol")
          . "</td><td>"
          . gettext("Peer Name")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Max Channels")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>" . $record->{name} . "</td><td>"
          . popup_menu(
            -name    => 'tech',
            -values  => \@techs,
            -default => $record->{tech}
          )
          . "</td><td>"
          . textfield(
            -name    => 'path',
            -size    => 20,
            -default => $record->{path}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'provider',
            -values  => \@providerlist,
            -default => $record->{provider}
          )
          . "</td><td>"
          . textfield(
            -name    => 'maxchannels',
            -size    => 4,
            -default => $record->{maxchannels}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        my $tmp =
            "INSERT INTO trunks (name,tech,path,maxchannels,provider) VALUES ("
          . $astpp_db->quote( $params->{name} ) . ", "
          . $astpp_db->quote( $params->{tech} ) . ", "
          . $astpp_db->quote( $params->{path} ) . ", "
          . $astpp_db->quote( $params->{maxchannels} ) . ", "
          . $astpp_db->quote( $params->{provider} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Trunk: ")
              . $params->{name}
              . gettext(" Added Successfully!");
        }
        else {
            $status .=
              gettext("Trunk: ") . $params->{name} . gettext(" Failed to Add!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $tmp =
            "UPDATE trunks SET tech = "
          . $astpp_db->quote( $params->{tech} ) . ", "
          . " path = "
          . $astpp_db->quote( $params->{path} ) . ", "
          . " provider = "
          . $astpp_db->quote( $params->{provider} ) . ", "
          . " maxchannels = "
          . $astpp_db->quote( $params->{maxchannels} )
          . " WHERE name = "
          . $astpp_db->quote( $params->{name} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Trunk: ")
              . $params->{name}
              . gettext(" Updated Successfully!");
        }
        else {
            $status .=
                gettext("Trunk: ")
              . $params->{name}
              . gettext(" Failed to Update!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
        my $tmp =
          "DELETE FROM trunks WHERE name = "
          . $astpp_db->quote( $params->{name} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Trunk: ")
              . $params->{name}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("Trunk: ")
              . $params->{name}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $tmp =
          "DELETE FROM routes WHERE trunk = "
          . $astpp_db->quote( $params->{name} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Related Routes Removed Successfully!");
        }
        else {
            $status .= gettext("Related Routes Failed to Remove!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Trunks") )
          . gettext("Trunk Name")
          . "</td><td>"
          . gettext("Protocol")
          . "</td><td>"
          . gettext("Peer Name")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Max Channels")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $body .= "<tr><td>"
          . textfield(
            -name => 'name',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'tech',
            -values => \@techs
          )
          . "</td><td>"
          . textfield(
            -name => 'path',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'provider',
            -values => \@providerlist
          )
          . "</td><td>"
          . textfield(
            -name => 'maxchannels',
            -size => 4
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Trunks") )
          . gettext("Trunk Name")
          . "</td><td>"
          . gettext("Protocol")
          . "</td><td>"
          . gettext("Peer/Trunk")
          . "</td><td>"
          . gettext("Provider")
          . "</td><td>"
          . gettext("Max Channels")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $tmp = "SELECT * FROM trunks";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM trunks ORDER BY name limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $trunkinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .= "<td>$trunkinfo->{name}" . "</td><td>$trunkinfo->{tech}";
            if (   $trunkinfo->{tech} eq "SIP"
                && $enh_config->{users_dids_rt} == 1 )
            {
                $body .=
                    "</td><td><a href=\"astpp-admin.cgi?mode="
                  . gettext("SIP Devices")
                  . "&action="
                  . gettext("Edit...")
                  . "&devicenumber="
                  . $trunkinfo->{path} . "\">"
                  . $trunkinfo->{path} . "</a>";
            }
            else {
                $body .= "</td><td>$trunkinfo->{path}";
            }
            $body .= "</td><td>"
              . $trunkinfo->{provider}
              . "</td><td>"
              . $trunkinfo->{maxchannels}
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Trunks")
              . "&action="
              . gettext("Edit...")
              . "&name="
              . $trunkinfo->{name} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Trunks")
              . "&action="
              . gettext("Deactivate...")
              . "&name="
              . $trunkinfo->{name} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Trunks")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Trunks")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_providers() {
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Add...") ) {
        $params->{mode} = gettext("Create Account");
        return &build_create_account();
    }
    elsif ( $params->{action} eq gettext("Generate Account") ) {
        $params->{count}   = 1;
        $params->{pennies} = 0;
        $params->{number}  = $params->{customnum};
        $status .= &generate_accounts( $params, $config, $enh_config );
        $params->{action} = gettext("Information...");
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Providers") )
          . gettext("Provider Name")
          . "</td><td>"
          . gettext("Credit Limit")
          . "</td><td>"
          . gettext("Balance")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        $tmp = "SELECT * FROM accounts WHERE type = '3'";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $astpp_db->prepare(
"SELECT * FROM accounts WHERE type = '3' AND status = '1' ORDER BY number limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $providerinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$providerinfo->{number}"
              . "</td><td>$providerinfo->{credit_limit}"
              . "</td><td>$providerinfo->{balance}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Edit Account")
              . "&action="
              . gettext("Edit...")
              . "&number="
              . $providerinfo->{number} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Remove Account")
              . "&action="
              . gettext("Deactivate...")
              . "&number="
              . $providerinfo->{number} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Providers")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Providers")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_outbound_routes() {
    my ( $sql, $record, $count, $tmp, $tot_count, $pageno, $pagesrequired,
        @trunklist );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) {
        $params->{action} = gettext("Information...");
    }
    if ( $params->{logintype} == 3 ) {
        my $sql =
          $astpp_db->prepare( "SELECT * FROM trunks WHERE provider = "
              . $astpp_db->quote( $params->{username} ) );
        $sql->execute;
        while ( my $record = $sql->fetchrow_hashref ) {
            push @trunklist, $record->{name};
        }
        $sql->finish;
    }
    else {
        @trunklist = &list_trunks($astpp_db);
    }
    if ( $params->{action} eq gettext("Edit...") ) {
        my $tmp =
          "SELECT * FROM outbound_routes WHERE id = "
          . $astpp_db->quote( $params->{id} );
        my $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td colspan=2><td>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Routes") )
          . hidden( -name => 'id',   -value => $params->{id} )
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Prepend")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Trunk")
          . "</td><td>"
          . gettext("Increment")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost per Additional Minute")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr class=\"rowone\"><td>"
          . textfield(
            -name    => 'pattern',
            -size    => 20,
            -default => $record->{pattern}
          )
          . "</td><td>"
          . textfield(
            -name    => 'prepend',
            -size    => 20,
            -default => $record->{prepend}
          )
          . "</td><td>"
          . textfield(
            -name    => 'comment',
            -size    => 20,
            -default => $record->{comment}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'trunk',
            -values  => \@trunklist,
            -default => $record->{trunk}
          )
          . "</td><td>"
          . textfield(
            -name    => 'inc',
            -size    => 4,
            -default => $record->{inc}
          )
          . "</td><td>"
          . textfield(
            -name    => 'connectcost',
            -size    => 20,
            -default => $record->{connectcost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'includedseconds',
            -size    => 20,
            -default => $record->{includedseconds}
          )
          . "</td><td>"
          . textfield(
            -name    => 'cost',
            -size    => 20,
            -default => $record->{cost}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    elsif ( $params->{action} eq gettext("Insert...") ) {
        $tmp =
"INSERT INTO outbound_routes (pattern,comment,connectcost,includedseconds,cost,inc,trunk,prepend) VALUES ("
          . $astpp_db->quote( $params->{pattern} ) . ","
          . $astpp_db->quote( $params->{comment} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{includedseconds} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote( $params->{trunk} ) . ","
          . $astpp_db->quote( $params->{prepend} ) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Outbound Route:") . " '"
              . $params->{pattern} . "' "
              . gettext("has been created.");
        }
        else {
            $status .=
                gettext("Outbound Route: ") . " '"
              . $params->{pattern} . "' "
              . gettext("FAILED to create!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        $tmp =
            "UPDATE outbound_routes SET"
          . " pattern="
          . $astpp_db->quote( $params->{pattern} ) . ","
          . " comment="
          . $astpp_db->quote( $params->{comment} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcharge} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{includedseconds} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . "," . " inc="
          . $astpp_db->quote( $params->{inc} ) . ","
          . " trunk="
          . $astpp_db->quote( $params->{trunk} ) . ","
          . " prepend="
          . $astpp_db->quote( $params->{prepend} )
          . " WHERE id = "
          . $astpp_db->quote( $params->{id} );
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Outbound Route: ") . " '"
              . $params->{id} . "' "
              . gettext("has been saved.");
        }
        else {
            $status .=
                gettext("Outbound Route:") . " '"
              . $params->{id} . "' "
              . gettext("FAILED to saved!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Deactivate...") ) {
        my $tmp =
          "UPDATE outbound_routes SET status = 2 WHERE id = "
          . $astpp_db->quote( $params->{id} );
        print STDERR $tmp if $config->{debug} == 1;
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Outbound Route: ")
              . $params->{id}
              . gettext(" Deactivated Successfully!");
        }
        else {
            $status .=
                gettext("Outbound Route: ")
              . $params->{id}
              . gettext(" Failed to Deactivate!");
            print STDERR $tmp if $config->{debug} == 1;
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td colspan=2><td>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Routes") )
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Prepend")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Trunk")
          . "</td><td>"
          . gettext("Increment")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost per Additional Minute")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr class=\"rowone\"><td>"
          . textfield(
            -name => 'pattern',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'prepend',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'comment',
            -size => 20
          )
          . "</td><td>"
          . popup_menu(
            -name   => 'trunk',
            -values => \@trunklist
          )
          . "</td><td>"
          . textfield(
            -name => 'inc',
            -size => 4
          )
          . "</td><td>"
          . textfield(
            -name => 'connectcost',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'includedseconds',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'cost',
            -size => 20
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
</table>
";
        return $body;
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td><td colspan=2>"
          . gettext("Pattern is a Regular Expression")
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . hidden( -name => 'mode', -value => gettext("Outbound Routes") )
          . gettext("ID")
          . "</td><td>"
          . gettext("Pattern")
          . "</td><td>"
          . gettext("Prepend")
          . "</td><td>"
          . gettext("Comment")
          . "</td><td>"
          . gettext("Trunk")
          . "</td><td>"
          . gettext("Increment")
          . "</td><td>"
          . gettext("Connect Charge")
          . "</td><td>"
          . gettext("Included Seconds")
          . "</td><td>"
          . gettext("Cost Per Add. Minute")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        if ( $params->{logintype} == 3 ) {
            $tot_count = @trunklist;
            $count     = 0;
            $tmp       =
              "SELECT * FROM outbound_routes WHERE status < 2 AND trunk IN (";
            foreach my $trunk (@trunklist) {
                $tmp .= "'" . $trunk . "'";
                $count++;
                if ( $count < $tot_count ) {
                    $tmp .= ",";
                }
            }
            $tmp .= ")";
            $sql = $astpp_db->prepare($tmp);

        }
        else {
            $tmp = "SELECT * FROM outbound_routes WHERE status < 2";
            $sql = $astpp_db->prepare($tmp);
        }
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        if ( $params->{logintype} == 3 ) {
            $tot_count = @trunklist;
            $count     = 0;
            $tmp       =
              "SELECT * FROM outbound_routes WHERE status < 2 AND trunk IN (";
            foreach my $trunk (@trunklist) {
                $tmp .= "'" . $trunk . "'";
                $count++;
                if ( $count < $tot_count ) {
                    $tmp .= ",";
                }
            }
            $tmp .=
              ") ORDER BY comment limit $params->{limit} , $results_per_page";
            $sql = $astpp_db->prepare($tmp);

        }
        else {
            $sql =
              $astpp_db->prepare(
"SELECT * FROM outbound_routes WHERE status < 2 ORDER BY comment limit $params->{limit} , $results_per_page"
              );
        }
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";
        $count = 0;

        while ( my $record = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
"<td>$record->{id}</td><td>$record->{pattern}</td><td>$record->{prepend}</td>"
              . "<td>$record->{comment}</td><td>$record->{trunk}</td>"
              . "<td>$record->{inc}</td>"
              . "<td>$record->{connectcost}</td><td>$record->{includedseconds}</td>"
              . "<td>$record->{cost}</td>"
              . "<td><a href=\"astpp-admin.cgi?mode="
              . gettext("Outbound Routes")
              . "&action="
              . gettext("Edit...") . "&id="
              . $record->{id} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Outbound Routes")
              . "&action="
              . gettext("Deactivate...") . "&id="
              . $record->{id} . "\">"
              . gettext("Deactivate...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Outbound Routes")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Outbound Routes")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
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
        $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    return $body;
}

sub build_calc_charge() {
    my ( $status, $body, $cost, $length, $increment );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ($params->{logintype} == 1) {
	@pricelists = ($params->{username});
    } else {
	@pricelists = &list_pricelists($astpp_db);
    }
    if ( $params->{action} eq gettext("Price Call...") ) {
        my $branddata = &get_pricelist( $astpp_db, $params->{pricelist} );
        my $numdata = &get_route( $astpp_db, $config, $params->{phonenumber},
            $params->{pricelist} );
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
        print STDERR "$numdata->{connectcost}, $numdata->{cost}, "
          . $params->{length} * 60
          . ", $increment, $numdata->{includedseconds}"
          if $config->{debug} == 1;
        my $cost = &calc_call_cost(
            $numdata->{connectcost}, $numdata->{cost},
            $params->{length} * 60,  $increment,
            $numdata->{includedseconds}
        );
        $cost = $cost / 10000;
        $cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
        foreach my $handle (@output) {
            print $handle "Matching pattern is $numdata->{pattern}\n";
        }
        $status .=
            gettext("Call to: ")
          . $params->{phonenumber}
          . gettext(" will cost: ")
          . $cost
          . gettext(" for a call lasting ")
          . $params->{length}
          . gettext(" minutes.");
    }
    $body = start_form
      . "<table class=\"default\">"
      . "<tr class=\"header\"><td>"
      . hidden( -name => 'mode', -default => gettext("Calc Charge") )
      . gettext("Phone Number")
      . "</td><td>"
      . gettext("Length (Minutes)")
      . "</td><td>"
      . gettext("Pricelist")
      . "</td><td>"
      . gettext("Action")
      . "</td></tr>
<tr class=\"rowone\"><td>"
      . textfield(
        -name => 'phonenumber',
        -size => 20
      )
      . "</td><td>"
      . textfield(
        -name => 'length',
        -size => 4
      )
      . "</td><td>"
      . popup_menu(
        -name    => 'pricelist',
        -values  => \@pricelists,
        -default => $config->{default_brand}
      )
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Price Call...") )
      . "</td></tr>
</table>
";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub build_configure() {
    my ( $body, $status, %newconfig );
    $body = start_form;
    if ( $params->{action} eq gettext("Save...") ) {
        %newconfig = (
            'dbuser'           => $params->{dbuser},
            'dbpass'           => $params->{dbpass},
            'dbname'           => $params->{dbname},
            'dbhost'           => $params->{dbhost},
            'cdr_dbuser'       => $params->{cdr_dbuser},
            'cdr_dbpass'       => $params->{cdr_dbpass},
            'cdr_dbname'       => $params->{cdr_dbname},
            'cdr_dbhost'       => $params->{cdr_dbhost},
            'cardlength'       => $params->{cardlength},
            'startingdigit'    => $params->{startingdigit},
            'email'            => $params->{email},
            'emailadd'         => $params->{emailadd},
            'default_brand'    => $params->{default_brand},
            'currency_name'    => $params->{currency_name},
            'company_website'  => $params->{company_website},
            'company_name'     => $params->{company_name},
            'company_email'    => $params->{company_email},
            'asterisk_server'  => $params->{asterisk_server},
            'astpp_dir'        => $params->{astpp_dir},
            'new_user_brand'   => $params->{new_user_brand},
            'user_email'       => $params->{user_email},
            'debug'            => $params->{debug},
            'asterisk_dir'     => $params->{asterisk_dir},
            'default_context'  => $params->{default_context},
            'reg_seconds'      => $params->{reg_seconds},
            'sip_port'         => $params->{sip_port},
            'iax_port'         => $params->{iax_port},
            'ipaddr'           => $params->{ipaddr},
            'codecs'           => $params->{codecs},
            'type'             => $params->{type},
            'key'              => $params->{key},
            'remote_incoming'  => $params->{remote_incoming},
            'key_home'         => $params->{key_home},
            'astman_user'      => $params->{astman_user},
            'astman_secret'    => $params->{astman_secret},
            'astman_host'      => $params->{astman_host},
            'enablelcr'        => $params->{enablelcr},
            'language'         => $params->{language}
        );
        if ( !&save_config(%newconfig) ) {
            $status .= gettext("Configuration Saved...");
        }
        else {
            $status .= gettext("Configuration Not Saved...");
        }
    }
    $body = start_form
      . "<table class=\"default\" border=\"1\" cellpadding=\"2\" cellspacing=\"2\"><tr><td colspan=4>"
      . hidden( -name => 'mode', -default => gettext("Configure") )
      . $status
      . "</td></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("ASTPP Database Configuration")
      . "</td></tr>
<tr><td>"
      . gettext("Host Name/IP")
      . "</td><td>"
      . textfield(
        -name    => 'dbhost',
        -default => $config->{dbhost}
      )
      . "</td></tr>
<tr><td>"
      . gettext("User Name")
      . "</td><td>"
      . textfield(
        -name    => 'dbuser',
        -default => $config->{dbuser}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Password")
      . "</td><td>"
      . textfield(
        -name    => 'dbpass',
        -default => $config->{dbpass}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Database Name")
      . "</td><td>"
      . textfield(
        -name    => 'dbname',
        -default => $config->{dbname}
      )
      . "</td></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("Asterisk(tm) CDR Database Configuration")
      . "</td></tr>
<tr><td>"
      . gettext("Host Name/IP")
      . "</td><td>"
      . textfield(
        -name    => 'cdr_dbhost',
        -default => $config->{cdr_dbhost}
      )
      . "</td></tr>
<tr><td>"
      . gettext("User Name")
      . "</td><td>"
      . textfield(
        -name    => 'cdr_dbuser',
        -default => $config->{cdr_dbuser}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Password")
      . "</td><td>"
      . textfield(
        -name    => 'cdr_dbpass',
        -default => $config->{cdr_dbpass}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Database Name")
      . "</td><td>"
      . textfield(
        -name    => 'cdr_dbname',
        -default => $config->{cdr_dbname}
      )
      . "</td></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("General Options")
      . "</td></tr>
<tr><td>"
      . gettext("Card Number Length (4-20)")
      . "</td><td>"
      . textfield(
        -name    => 'cardlength',
        -default => $config->{cardlength}
      )
      . "</td></tr>
<tr><td colspan=2>"
      . gettext("Card Starting Number Enter 0 if not required")
      . "</td><td>"
      . "</td><td>"
      . "</tr>
<tr><td></td><td>"
      . textfield(
        -name    => 'cardlength',
        -default => $config->{cardlength}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Email New Account Info to admin?")
      . "</td><td>"
      . popup_menu(
        -name    => 'email',
        -values  => \%yesno,
        -default => $config->{email}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Admin Email Address")
      . "</td><td>"
      . textfield(
        -name    => 'emailadd',
        -default => $config->{emailadd}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Currency")
      . "</td><td>"
      . popup_menu(
        -name    => 'currency_name',
        -values  => \@currency,
        -default => $config->{currency_name}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Language")
      . "</td><td>"
      . popup_menu(
        -name    => 'language',
        -values  => \@language,
        -default => $config->{language}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Company Name (No Spaces)")
      . "</td><td>"
      . textfield(
        -name    => 'company_name',
        -default => $config->{company_name}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Company Website")
      . "</td><td>"
      . textfield(
        -name    => 'company_website',
        -default => $config->{company_website}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Customer Service Email Address")
      . "</td><td>"
      . textfield(
        -name    => 'company_email',
        -default => $config->{company_email}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Asterisk Server (DNS/IP)")
      . "</td><td>"
      . textfield(
        -name    => 'asterisk_server',
        -default => $config->{asterisk_server}
      )
      . "</td></tr>
<tr><td>"
      . gettext("New User Default Pricelist")
      . "</td><td>"
      . textfield(
        -name    => 'new_user_brand',
        -default => $config->{new_user_brand}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Email Users on action?")
      . "</td><td>"
      . popup_menu(
        -name    => 'user_email',
        -values  => \%yesno,
        -default => $config->{user_email}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Debug Mode")
      . "</td><td>"
      . popup_menu(
        -name    => 'debug',
        -values  => \%yesno,
        -default => $config->{debug}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Enable LCR (Least Cost Routing)?")
      . "</td><td>"
      . popup_menu(
        -name    => 'enablelcr',
        -values  => \%yesno,
        -default => $config->{enablelcr}
      )
      . "</td></tr>
<tr></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("Rating Engine Configuration")
      . "</td></tr>
<tr><td>"
      . "</td></tr>
<tr><td colspan=2>"
      . gettext(
        "The Default Pricelist is used during billing.  If it cannot find")
      . "</td></tr>
<tr><td colspan=2>"
      . gettext(
        "a cost under the cards brand it looks under the default brand.")
      . "</td></tr>
<tr><td>"
      . gettext("Default Pricelist:")
      . "</td><td>"
      . textfield(
        -name    => 'default_brand',
        -default => $config->{default_brand}
      )
      . "</td></tr>
<tr></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("Asterisk(tm) Related Configuration")
      . "</td></tr>
<tr><td>"
      . gettext("Asterisk Configuration Directory")
      . "</td><td>"
      . textfield(
        -name    => 'asterisk_dir',
        -default => $config->{asterisk_dir}
      )
      . "</td></tr>
<tr><td>"
      . gettext("New User Context")
      . "</td><td>"
      . textfield(
        -name    => 'default_context',
        -default => $config->{default_context}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Default Reg Seconds")
      . "</td><td>"
      . textfield(
        -name    => 'reg_seconds',
        -default => $config->{reg_seconds}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Sip Port")
      . "</td><td>"
      . textfield(
        -name    => 'sip_port',
        -default => $config->{sip_port}
      )
      . "</td></tr>
<tr><td>"
      . gettext("IAX Port")
      . "</td><td>"
      . textfield(
        -name    => 'iax_port',
        -default => $config->{iax_port}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Client IP Address (dynamic usually)")
      . "</td><td>"
      . textfield(
        -name    => 'ipaddr',
        -default => $config->{ipaddr}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Codecs usually (all)")
      . "</td><td>"
      . textfield(
        -name    => 'codecs',
        -default => $config->{codecs}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Default Client Type (friend/user/peer)")
      . "</td><td>"
      . textfield(
        -name    => 'type',
        -default => $config->{type}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Outoing Key Name")
      . "</td><td>"
      . textfield(
        -name    => 'key',
        -default => $config->{key}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Remote Incoming User Name")
      . "</td><td>"
      . textfield(
        -name    => 'remote_incoming',
        -default => $config->{remote_incoming}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Key Home  (http://keylocation)")
      . "</td><td>"
      . textfield(
        -name    => 'key_home',
        -default => $config->{key_home}
      )
      . "</td></tr>
<tr></tr>
<tr style=\"header\"><td colspan=2>"
      . gettext("Asterisk(tm) Manager Configuration")
      . "</td></tr>
<tr><td>"
      . gettext("User")
      . "</td><td>"
      . textfield(
        -name    => 'astman_user',
        -default => $config->{astman_user}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Password")
      . "</td><td>"
      . password_field(
        -name    => 'astman_secret',
        -default => $config->{astman_secret}
      )
      . "</td></tr>
<tr><td>"
      . gettext("Host")
      . "</td><td>"
      . textfield(
        -name    => 'astman_host',
        -default => $config->{astman_host}
      )
      . "</td></tr>
<tr><td>"
      . "</td></tr>
<tr><td>"
      . gettext("Action")
      . "</td><td>"
      . submit( -name => 'action', -value => gettext("Save...") )
      . "</td></tr>
</table>
";
    return $body;
}

sub initialize() {
    $config     = &load_config();
    $status .= gettext("Main Configuration Unavailable!") unless $config;
    $enh_config = &load_config_enh();
    $status .= gettext("Enhanced Configuration Unavailable!") unless $enh_config;
    $astpp_db   = &connect_db( $config, $enh_config, @output );
    $status .= gettext("ASTPP Database Unavailable!") unless $astpp_db;
    $cdr_db     = &cdr_connect_db( $config, $enh_config, @output );
    $status .= gettext("Asterisk CDR Database Unavailable!") unless $cdr_db;
    $config 	= &load_config_db($astpp_db,$config) if $astpp_db;
    if ( $config->{enablelcr} == 1 ) {
        push @modes, gettext("LCR");
    }
    if ( $enh_config->{users_dids_rt} == 1 ) {
        $rt_db = &rt_connect_db( $config, $enh_config, @output );
        $status .= gettext("Realtime Database Unavailable!") unless $rt_db;
        push @modes, gettext("Switch Config");
    }
    if ( $enh_config->{callingcards} == 1 ) {
        push @modes, gettext("Calling Cards");
    }
    @modes = sort @modes;
}
############### Integration with Realtime starts here #######################
sub build_sip_devices() {
    my (
        @pricelists, $pageno,   $status,   $body, $number,
        $inuse,     $cardstat, $cardinfo, $pagesrequired
    );
    my ( $deviceinfo, $sql );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }

    if ( $params->{action} eq gettext("Insert...") ) {
        if ( param('number') ne "" ) {
            $number = param('number');
        }
        else {
            $number = param('numberlist');
        }
        my $name = &finduniquesip_rt($number);
        $enh_config->{rt_sip_type} = $params->{devicetype};
        $config->{ipaddr}          = $params->{ipaddr};
	if ($enh_config->{users_dids_rt} == 1) {
        $status                    =
          &add_sip_user_rt( $rt_db, $config, $enh_config, $name,
            $params->{secret}, $params->{context}, $number, $params );
	}
	if ($config->{openser} == 1) {
        $status                    =
          &add_sip_user_openser( $openser_db, $config, $enh_config, $name,
            $params->{secret}, $params->{context}, $number, $params );
	}
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Delete...") ) {
        if (
            &del_sip_user_rt(
                $rt_db, $config, $enh_config, $params->{devicename}
            )
          )
        {
            $status .=
              gettext("Removed Device:") . " $params->{devicename} ". gettext("from -Realtime") . "<br>";
        }
        else {
            $status .= gettext("Unable to remove device from -Realtime!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $tmp =
            "UPDATE $enh_config->{rt_sip_table} SET"
          . " callerid="
          . $rt_db->quote( $params->{clid} ) . ", "
          . " accountcode="
          . $rt_db->quote( $params->{accountcode} ) . ", "
          . " canreinvite="
          . $rt_db->quote( $params->{rt_sip_canreinvite} ) . ", "
          . " context="
          . $rt_db->quote( $params->{context} ) . ", "
          . " ipaddr="
          . $rt_db->quote( $params->{ipaddr} ) . ", "
          . " host="
          . $rt_db->quote( $params->{host} ) . ", "
          . " insecure="
          . $rt_db->quote( $params->{rt_sip_insecure} ) . ", "
          . " mailbox="
          . $rt_db->quote( $params->{mailbox} ) . ", " 
          . " nat="
          . $rt_db->quote( $params->{rt_sip_nat} ) . ", "
          . " port="
          . $rt_db->quote( $params->{sip_port} ) . ", "
          . " qualify="
          . $rt_db->quote( $params->{qualify} ) . ", "
          . " secret="
          . $rt_db->quote( $params->{secret} ) . ", "
          . " type="
          . $rt_db->quote( $enh_config->{rt_sip_type} ) . ", "
          . " name="
          . $rt_db->quote( $params->{name} ) . ", "
          . " username="
          . $rt_db->quote( $params->{name} ) . ", "
          . " disallow="
          . $rt_db->quote( $params->{rt_codec_disallow} ) . ", "
          . " allow="
          . $rt_db->quote( $params->{rt_codec_allow} ) . ", "
          . " cancallforward="
          . $rt_db->quote( $params->{rt_sip_cancallforward} )
          . " WHERE id = "
          . $rt_db->quote( $params->{devicenumber} );
        if ( $config->{debug} == 1 ) {
            print STDERR " $tmp \n" if $config->{debug} == 1;
        }
        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Updated Device:") . " $params->{name}<br>";
        }
        else {
            $status .= gettext("Unable to update device!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        my @accountlist = &list_accounts($astpp_db);
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Account Number")
          . "</td><td>"
          . gettext("Device Password")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Device Type")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("SIP Devices") )
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . popup_menu(
            -name   => "numberlist",
            -values => \@accountlist
          )
          . textfield( -name => "number", -size => 20 )
          . "</td><td>"
          . textfield( -name => "secret", -size => 20 )
          . "</td><td>"
          . textfield(
            -name    => "context",
            -size    => 20,
            -default => $config->{default_context}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "devicetype",
            -values  => \@devicetypes,
            -default => $enh_config->{rt_sip_type}
          )
          . "</td><td>"
          . textfield(
            -name    => "ipaddr",
            -size    => 20,
            -default => $config->{ipaddr}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Edit...") ) {
        my @accountlist = &list_accounts($astpp_db);
        $sql =
          $rt_db->prepare(
            "SELECT * FROM $enh_config->{rt_sip_table} WHERE name = "
              . $rt_db->quote( $params->{devicename} ) );
        $sql->execute
          || return gettext("Something is wrong with the SIP users database!")
          . "\n";
        my $deviceinfo = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("Device Number")
          . "</td><td colspan=2>"
	  . textfield(
            -name    => "name",
            -size    => 20,
            -default => $deviceinfo->{name}
          )
          . "</td></tr>
";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Account Number")
          . "</td><td>"
          . gettext("Device Password")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Device Type")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . gettext("Host")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("SIP Devices") )
          . hidden(
            -name  => "devicenumber",
            -value => $deviceinfo->{id}
          )
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "accountcode",
            -size    => 20,
            -default => $deviceinfo->{accountcode}
          )
          . "</td><td>"
          . textfield(
            -name    => "secret",
            -size    => 20,
            -default => $deviceinfo->{secret}
          )
          . "</td><td>"
          . textfield(
            -name    => "context",
            -size    => 20,
            -default => $deviceinfo->{context}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "devicetype",
            -values  => \@devicetypes,
            -default => $deviceinfo->{rt_sip_type}
          )
          . "</td><td>"
          . textfield(
            -name    => "ipaddr",
            -size    => 20,
            -default => $deviceinfo->{ipaddr}
          )
          . "</td><td>"
          . textfield(
            -name    => "host",
            -size    => 20,
            -default => $deviceinfo->{host}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("CallerID")
          . "</td><td>"
          . gettext("Re-Invite")
          . "</td><td>"
          . gettext("Insecure")
          . "</td><td>"
          . gettext("Port")
          . "</td><td>"
          . gettext("Mailbox")
          . "</td><td>"
          . gettext("NAT")
          . "</td></tr>
<tr><td>"
          . textfield(
            -name    => "clid",
            -size    => 20,
            -default => $deviceinfo->{callerid}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_sip_canreinvite",
            -size    => 20,
            -default => $deviceinfo->{canreinvite}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_sip_insecure",
            -size    => 20,
            -default => $deviceinfo->{insecure}
          )
          . "</td><td>"
          . textfield(
            -name    => "sip_port",
            -size    => 20,
            -default => $deviceinfo->{port}
          )
          . "</td><td>"
          . textfield(
            -name    => "mailbox",
            -size    => 20,
            -default => $deviceinfo->{mailbox}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_sip_nat",
            -size    => 20,
            -default => $deviceinfo->{nat}
          )
          . "</td>"
          . "</tr>
<tr class=\"header\"><td>"
          . gettext("Allow")
          . "</td><td>"
          . gettext("Disallow")
          . "</td><td>"
          . gettext("Qualify")
          . "</td><td>"
          . gettext("Call Forward")
          . "</td></tr>
"
          . "<tr><td>"
          . textfield(
            -name    => "rt_codec_allow",
            -size    => 20,
            -default => $deviceinfo->{allow}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_codec_disallow",
            -size    => 20,
            -default => $deviceinfo->{disallow}
          )
          . "</td><td>"
          . textfield(
            -name    => "qualify",
            -size    => 20,
            -default => $deviceinfo->{qualify}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_sip_cancallforward",
            -size    => 20,
            -default => $deviceinfo->{cancallforward}
          )
          . "</td></tr>
"
          . "<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("SIP Devices") )
          . submit( -name => "action", -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Account Number")
          . "</td><td>"
          . gettext("Name")
          . "</td><td>"
          . gettext("Device ID Number")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . gettext("Codecs Allow")
          . "</td><td>"
          . gettext("DTMF Mode")
          . "</td><td>"
          . gettext("Type")
          . "</td><td>"
          . gettext("Secret")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        my $tmp = "SELECT name FROM " . $enh_config->{rt_sip_table};
        $sql = $rt_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the SIP users database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $rt_db->prepare(
"SELECT * FROM $enh_config->{rt_sip_table} ORDER BY accountcode limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the SIP users database!")
          . "\n";
        my $count = 0;

        while ( $deviceinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$deviceinfo->{accountcode}"
              . "</td><td>$deviceinfo->{name}"
              . "</td><td>$deviceinfo->{username}"
              . "</td><td>$deviceinfo->{context}"
              . "</td><td>$deviceinfo->{host}"
              . "</td><td>$deviceinfo->{allow}"
              . "</td><td>$deviceinfo->{dtmfmode}"
              . "</td><td>$deviceinfo->{type}"
              . "</td><td>$deviceinfo->{secret}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("SIP Devices")
              . "&action="
              . gettext("Edit...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("SIP Devices")
              . "&action="
              . gettext("Delete...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . gettext("Delete...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("SIP Devices")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("SIP Devices")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
                }
                else {
                    $pageno = $i + 1;
                    $body .= " |";
                }
            }
        }
    }
    $body .= "";
    $body .= gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub finduniquesip_rt() {
    my ($name) = @_;
    my ( $cc, $sql, $count, $sipid, $record );
#    $name =~ s/^a-zA-Z0-9/;
    for ( ; ; ) {
        $count = 1;
        $sipid =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        $sipid = substr( $sipid, 0, 5 );
        $sipid = $name . $sipid;
        print STDERR "SIPID: $sipid\n" if $config->{debug} == 1;
        $sql =
          $rt_db->prepare(
            "SELECT COUNT(*) FROM $enh_config->{rt_sip_table} WHERE name = "
              . $rt_db->quote($sipid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $sipid if ( $count == 0 );
    }
}

sub build_iax_devices() {
    my (
        @pricelists, $pageno,   $status,   $body, $number,
        $inuse,     $cardstat, $cardinfo, $pagesrequired
    );
    my ( $deviceinfo, $sql );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status .= "&nbsp;";
    my $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }

    if ( $params->{action} eq gettext("Insert...") ) {
        if ( param('number') ne "" ) {
            $number = param('number');
        }
        else {
            $number = param('numberlist');
        }
        my $name = &finduniqueiax_rt($number);
        $enh_config->{rt_iax_type} = $params->{devicetype};
        $config->{ipaddr}          = $params->{ipaddr};
        $status                    =
          &add_iax_user_rt( $rt_db, $config, $enh_config, $name,
            $params->{secret}, $params->{context}, $number, $params );
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Delete...") ) {
        if (
            &del_sip_user_rt(
                $rt_db, $config, $enh_config, $params->{devicename}
            )
          )
        {
            $status .=
              gettext("Removed Device:") . " $params->{devicename}<br>";
        }
        else {
            $status .= gettext("Unable to remove device!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $tmp =
            "UPDATE $enh_config->{rt_iax_table} SET"
          . " callerid="
          . $rt_db->quote( $params->{clid} ) . ", "
          . " accountcode="
          . $rt_db->quote( $params->{accountcode} ) . ", "
          . " context="
          . $rt_db->quote( $params->{context} ) . ", "
          . " ipaddr="
          . $rt_db->quote( $params->{ipaddr} ) . ", "
          . " host="
          . $rt_db->quote( $params->{host} ) . ", "
          . " mailbox="
          . $rt_db->quote( $params->{mailbox} ) . ", "
          . " port="
          . $rt_db->quote( $params->{sip_port} ) . ", "
          . " qualify="
          . $rt_db->quote( $params->{qualify} ) . ", "
          . " secret="
          . $rt_db->quote( $params->{secret} ) . ", "
          . " type="
          . $rt_db->quote( $params->{devicetype} ) . ", "
          . " username="
          . $rt_db->quote( $params->{name} ) . ", "
	  . " name="
          . $rt_db->quote( $params->{name} ) . ", "
          . " disallow="
          . $rt_db->quote( $params->{rt_codec_disallow} ) . ", "
          . " allow="
          . $rt_db->quote( $params->{rt_codec_allow} )
          . " WHERE name = "
          . $rt_db->quote( $params->{devicenumber} );
        if ( $config->{debug} == 1 ) {
            print STDERR " $tmp \n" if $config->{debug} == 1;
        }
        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Updated Device:") . " $params->{devicenumber}<br>";
        }
        else {
            $status .= gettext("Unable to update device!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        my @accountlist = &list_accounts($astpp_db);
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Account Number")
          . "</td><td>"
          . gettext("Device Password")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Device Type")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("IAX Devices") )
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . popup_menu(
            -name   => "numberlist",
            -values => \@accountlist
          )
          . textfield( -name => "number", -size => 20 )
          . "</td><td>"
          . textfield( -name => "secret", -size => 20 )
          . "</td><td>"
          . textfield(
            -name    => "context",
            -size    => 20,
            -default => $config->{default_context}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "devicetype",
            -values  => \@devicetypes,
            -default => $enh_config->{rt_iax_type}
          )
          . "</td><td>"
          . textfield(
            -name    => "ipaddr",
            -size    => 20,
            -default => $config->{ipaddr}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Edit...") ) {
        my @accountlist = &list_accounts($astpp_db);
        $sql =
          $rt_db->prepare(
            "SELECT * FROM $enh_config->{rt_iax_table} WHERE name = "
              . $rt_db->quote( $params->{devicename} ) );
        $sql->execute
          || return gettext("Something is wrong with the IAX users database!")
          . "\n";
        my $deviceinfo = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form;
        $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=2>"
          . gettext("Device Number")
          . "</td><td colspan=2>"
          . textfield(
            -name    => "name",
            -size    => 20,
            -default => $deviceinfo->{name}
          )
          . "</td></tr>
";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Account Number")
          . "</td><td>"
          . gettext("Device Password")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Device Type")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . gettext("Host")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("IAX Devices") )
          . hidden(
            -name  => "devicenumber",
            -value => $deviceinfo->{name}
          )
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name    => "accountcode",
            -size    => 20,
            -default => $deviceinfo->{accountcode}
          )
          . "</td><td>"
          . textfield(
            -name    => "secret",
            -size    => 20,
            -default => $deviceinfo->{secret}
          )
          . "</td><td>"
          . textfield(
            -name    => "context",
            -size    => 20,
            -default => $deviceinfo->{context}
          )
          . "</td><td>"
          . popup_menu(
            -name    => "devicetype",
            -values  => \@devicetypes,
            -default => $deviceinfo->{type}
          )
          . "</td><td>"
          . textfield(
            -name    => "ipaddr",
            -size    => 20,
            -default => $deviceinfo->{ipaddr}
          )
          . "</td><td>"
          . textfield(
            -name    => "host",
            -size    => 20,
            -default => $deviceinfo->{host}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("CallerID")
          . "</td><td>"
          . gettext("Port")
          . "</td><td>"
          . gettext("Mailbox")
          . "</td></tr>
<tr><td>"
          . textfield(
            -name    => "clid",
            -size    => 20,
            -default => $deviceinfo->{callerid}
          )
          . "</td><td>"
          . textfield(
            -name    => "iax_port",
            -size    => 20,
            -default => $deviceinfo->{port}
          )
          . "</td><td>"
          . textfield(
            -name    => "mailbox",
            -size    => 20,
            -default => $deviceinfo->{mailbox}
          )
          . "</td>"
          . "</tr>
<tr class=\"header\"><td>"
          . gettext("Allow")
          . "</td><td>"
          . gettext("Disallow")
          . "</td><td>"
          . gettext("Qualify")
          . "</td></tr>
"
          . "<tr><td>"
          . textfield(
            -name    => "rt_codec_allow",
            -size    => 20,
            -default => $deviceinfo->{allow}
          )
          . "</td><td>"
          . textfield(
            -name    => "rt_codec_disallow",
            -size    => 20,
            -default => $deviceinfo->{disallow}
          )
          . "</td><td>"
          . textfield(
            -name    => "qualify",
            -size    => 20,
            -default => $deviceinfo->{qualify}
          )
          . "</td></tr>
"
          . "<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("IAX Devices") )
          . submit( -name => "action", -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("Account Number")
          . "</td><td>"
          . gettext("Name")
          . "</td><td>"
          . gettext("Device ID Number")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("IP Address")
          . "</td><td>"
          . gettext("Codecs Allow")
          . "</td><td>"
          . gettext("Type")
          . "</td><td>"
          . gettext("Secret")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        my $tmp = "SELECT name FROM " . $enh_config->{rt_iax_table};
        $sql = $rt_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the IAX users database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $rt_db->prepare(
"SELECT * FROM $enh_config->{rt_iax_table} ORDER BY accountcode limit $params->{limit} , $results_per_page"
          );
        $sql->execute
          || return gettext("Something is wrong with the IAX users database!")
          . "\n";
        my $count = 0;

        while ( $deviceinfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$deviceinfo->{accountcode}"
              . "</td><td>$deviceinfo->{name}"
              . "</td><td>$deviceinfo->{username}"
              . "</td><td>$deviceinfo->{context}"
              . "</td><td>$deviceinfo->{host}"
              . "</td><td>$deviceinfo->{allow}"
              . "</td><td>$deviceinfo->{type}"
              . "</td><td>$deviceinfo->{secret}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("IAX Devices")
              . "&action="
              . gettext("Edit...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("IAX Devices")
              . "&action="
              . gettext("Delete...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . gettext("Delete...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("IAX Devices")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("IAX Devices")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
                }
                else {
                    $pageno = $i + 1;
                    $body .= " |";
                }
            }
        }
    }
    $body .= "";
    $body .= gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}

sub finduniqueiax_rt() {
    my ($name) = @_;
    my ( $cc, $sql, $count, $iaxid, $record );
    for ( ; ; ) {
        $count = 1;
        $iaxid =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        $iaxid = substr( $iaxid, 0, 5 );
        $iaxid = $name . $iaxid;
        print STDERR "IAXID: $iaxid\n" if $config->{debug} == 1;
        $sql =
          $rt_db->prepare(
            "SELECT COUNT(*) FROM $enh_config->{rt_iax_table} WHERE name = "
              . $rt_db->quote($iaxid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $iaxid if ( $count == 0 );
    }
}

sub build_dialplan() {
    my (
        @pricelists,        $pageno,        $status,     $body,
        $number,           $inuse,         $cardstat,   $cardinfo,
        $results_per_page, $pagesrequired, $deviceinfo, $sql
    );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    $status           = "&nbsp;";
    $results_per_page = $enh_config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }

    if ( $params->{action} eq gettext("Insert...") ) {
        my $tmp =
            "INSERT INTO $enh_config->{rt_extensions_table} (context,"
          . "exten,priority,app,appdata) VALUES ("
          . $rt_db->quote( $params->{context} ) . ", "
          . $rt_db->quote( $params->{exten} ) . ", "
          . $rt_db->quote( $params->{priority} ) . ", "
          . $rt_db->quote( $params->{app} ) . ", "
          . $rt_db->quote( $params->{appdata} ) . ")";
        if ( $rt_db->do($tmp) ) {
            $status .=
                gettext("Added Dialplan Entry:") . " "
              . $params->{context} . "/"
              . $params->{exten} . "/"
              . $params->{priority} . "<br>";
        }
        else {
            $status .= gettext("Unable to add dialplan entry!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Delete...") ) {
        my $tmp =
          "DELETE FROM $enh_config->{rt_extensions_table} WHERE " . "id = "
          . $rt_db->quote( $params->{id} );
        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Deleted Dialplan Entry:") . " $params->{id}<br>";
        }
        else {
            $status .= gettext("Unable to delete dialplan entry!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Save...") ) {
        my $tmp =
            "UPDATE $enh_config->{rt_extensions_table} SET"
          . " context="
          . $rt_db->quote( $params->{context} ) . ", "
          . " exten="
          . $rt_db->quote( $params->{exten} ) . ", "
          . " priority="
          . $rt_db->quote( $params->{priority} ) . ", " . " app="
          . $rt_db->quote( $params->{app} ) . ", "
          . " appdata="
          . $rt_db->quote( $params->{appdata} )
          . " WHERE id = "
          . $rt_db->quote( $params->{id} );
        if ( $config->{debug} == 1 ) {
            print STDERR " $tmp \n" if $config->{debug} == 1;
        }
        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Updated Dialplan Entry:") . " $params->{id}<br>";
        }
        else {
            $status .= gettext("Unable to update dialplan!") . "<br>";
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( $params->{action} eq gettext("Add...") ) {
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("Context")
          . "</td><td>"
          . gettext("Extension")
          . "</td><td>"
          . gettext("Priority")
          . "</td><td>"
          . gettext("Application")
          . "</td><td>"
          . gettext("App Data")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("Dialplan") )
          . "<td></tr>
<tr class=\"rowone\"><td>"
          . textfield(
            -name => "context",
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => "exten",
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => "priority",
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => "app",
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => "appdata",
            -size => 20
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr>
<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    elsif ( $params->{action} eq gettext("Edit...") ) {
        $sql =
          $rt_db->prepare(
            "SELECT * FROM $enh_config->{rt_extensions_table} WHERE id = "
              . $rt_db->quote( $params->{id} ) );
        $sql->execute
          || return gettext("Something is wrong with the dialplan database!")
          . "\n";
        my $exteninfo = $sql->fetchrow_hashref;
        $sql->finish;
        $body = start_form;
        $body .= "<table class=\"default\">";
        $body .= "<tr class=\"header\"><td>";
        $body .=
            gettext("ID")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Extension")
          . "</td><td>"
          . gettext("Priority")
          . "</td><td>"
          . gettext("Application")
          . "</td><td>"
          . gettext("App Data")
          . "</td><td>"
          . hidden( -name => "mode", -value => gettext("Dialplan") )
          . hidden(
            -name  => "id",
            -value => $params->{id}
          )
          . "</td></tr>
<tr class=\"rowone\"><td>"
          . $exteninfo->{id}
          . "</td><td>"
          . textfield(
            -name    => "context",
            -size    => 20,
            -default => $exteninfo->{context}
          )
          . "</td><td>"
          . textfield(
            -name    => "exten",
            -size    => 20,
            -default => $exteninfo->{exten}
          )
          . "</td><td>"
          . textfield(
            -name    => "priority",
            -size    => 20,
            -default => $exteninfo->{priority}
          )
          . "</td><td>"
          . textfield(
            -name    => "app",
            -size    => 20,
            -default => $exteninfo->{app}
          )
          . "</td><td>"
          . textfield(
            -name    => "appdata",
            -size    => 20,
            -default => $exteninfo->{appdata}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr>
"
          . "<tr></tr>
<tr><td colspan = 4> $status </td></tr>
</table>
";
    }
    if ( $params->{action} eq gettext("Information...") ) {
        $body = start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td>"
          . hidden( -name => "mode", -value => gettext("Dialplan") )
          . submit( -name => "action", -value => gettext("Add...") )
          . "</td></tr>
"
          . "<tr class=\"header\"><td>"
          . gettext("ID")
          . "</td><td>"
          . gettext("Context")
          . "</td><td>"
          . gettext("Extension")
          . "</td><td>"
          . gettext("Priority")
          . "</td><td>"
          . gettext("Application")
          . "</td><td>"
          . gettext("App Data")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>
";
        my $tmp = "SELECT id FROM " . $enh_config->{rt_extensions_table};
        $sql = $rt_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the SIP users database!")
          . "\n";
        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;
        $sql =
          $rt_db->prepare(
"SELECT * FROM $enh_config->{rt_extensions_table} ORDER BY context, exten, priority "
              . "limit $params->{limit} , $results_per_page" );
        $sql->execute
          || return gettext("Something is wrong with the dialplan database!")
          . "\n";
        my $count = 0;

        while ( my $exteninfo = $sql->fetchrow_hashref ) {
            if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }
            $count++;
            $body .=
                "<td>$exteninfo->{id}"
              . "</td><td>$exteninfo->{context}"
              . "</td><td>$exteninfo->{exten}"
              . "</td><td>$exteninfo->{priority}"
              . "</td><td>$exteninfo->{app}"
              . "</td><td>$exteninfo->{appdata}"
              . "</td><td><a href=\"astpp-admin.cgi?mode="
              . gettext("Dialplan")
              . "&action="
              . gettext("Edit...") . "&id="
              . $exteninfo->{id} . "\">"
              . gettext("Edit...") . "</a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Dialplan")
              . "&action="
              . gettext("Delete...") . "&id="
              . $exteninfo->{id} . "\">"
              . gettext("Delete...") . "</a>"
              . "</td></tr>
";
        }
        $body .= "</table>
";
        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
            if ( $i == 0 ) {
                if ( $params->{limit} != 0 ) {
                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Dialplan")
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
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Dialplan")
                      . "&limit=";
                    $body .= ( $i * $results_per_page );
                    $body .= "\">\n";
                    $body .= $i + 1 . "</a>";
                }
                else {
                    $pageno = $i + 1;
                    $body .= " |";
                }
            }
        }
    }
    $body .= "";
    $body .= gettext("Page") . " $pageno " . gettext("of") . " $pagesrequired";
    $body .= "<table class=\"default\"><tr><td colspan = 4> $status </td></tr>
</table>
";
    return $body;
}
###################Start of Application ###################
&initialize();
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n" if $config->{debug} == 1;
}
if (!$params->{mode}) {
	$params->{mode} = gettext("Home");
}

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
if ( $loginstat == 1 ) {
	# In here we setup privileges for the different account levels
    print STDERR "LOGIN TYPE = $params->{logintype}\n" if $config->{debug} == 1;
        if ( $params->{logintype} == 0 )
        {    # User Login - Not allowed to do anything
		print STDERR "ASTPP USER LOGIN - DISABLED\n";
            @modes = ();
        }
        elsif ( $params->{logintype} == 1 ) { # Reseller Login
	    # We reload the astpp-enh-config to the resellers copy.  We will also
	    # reload databases other than the astpp one.
            $enh_config = &load_config_reseller($params->{username});
 	    $enh_config	= &load_config_db($astpp_db,$enh_config);
            $config = $enh_config;
            $freepbx_db = &freepbx_connect_db($config, $enh_config, @output)
           	if $enh_config->{users_dids_amp} == 1;
            $rt_db = &rt_connect_db( $config, $enh_config, @output )
           	if $enh_config->{users_dids_rt} == 1;
	    print STDERR "ASTPP RESELLER LOGIN\n";
            @modes = ( 
		gettext("Accounts"), gettext("Rates"),
		gettext("Logout")
            );
	   @Accounts = (
    gettext("Create Account"), gettext("Process Payment"),
    gettext("Remove Account"), gettext("Edit Account"),
    gettext("List Accounts"),  gettext("View Details")
    );
my @Rates = (
    gettext("Pricelists"),       gettext("Calc Charge"),
    gettext("Routes"),           gettext("Import Routes")
);
            @modes = sort @modes;
%types = (
    '0' => gettext("User"),
    '1' => gettext("Reseller")
);
        }
        if ( $params->{logintype} == 2) {
		print STDERR "ASTPP ADMINISTRATOR LOGIN\n";
            @modes = (
                gettext("Accounts"), gettext("Rates"),
                gettext("DIDs"),     gettext("Statistics"),
                gettext("System"),   gettext("Home")
            );
            if ( $config->{enablelcr} == 1 ) {
                push @modes, gettext("LCR");
            }
            if ( $enh_config->{users_dids_rt} == 1 ) {
                push @modes, gettext("Switch Config");
            }
            if ( $enh_config->{callingcards} == 1 ) {
                push @modes, gettext("Calling Cards");
            }
            @modes = sort @modes;
%types = (
    '0' => gettext("User"),
    '1' => gettext("Reseller"),
    '2' => gettext("Administrator"),
    '3' => gettext("Vendor"),
    '4' => gettext("Customer Service")
);
        }
        elsif ( $params->{logintype} == 3 )
        { # Vendor Login - Vendors are only allowed to look at stuff that pertains to them.
		print STDERR "ASTPP VENDOR LOGIN\n";
            @modes = (
                gettext("Trunk Statistics"), gettext("View CDRs"),
                gettext("Logout"),           gettext("Home"),
                gettext("Outbound Routes")
            );
        }
        elsif ( $params->{logintype} == 4 ) {    # Customer Service Login
		print STDERR "ASTPP CUSTOMER SERVICE LOGIN\n";
            @modes = (
                gettext("Accounts"),   gettext("DIDs"),
                gettext("Statistics"), gettext("Home"),
		gettext("Logout")
            );
            if ( $enh_config->{callingcards} == 1 ) {
                push @modes, gettext("Calling Cards");
            }
            @modes = sort @modes;
%types = (
    '0' => gettext("User"),
    '1' => gettext("Reseller"),
    '3' => gettext("Vendor"),
    '4' => gettext("Customer Service")
);

        }
#        else {
#            @modes = ();
#        }
    $msg  = gettext("Database Not Available!") unless $astpp_db;
    $body = &build_body( $params->{mode} );
    $menu = &build_menu_ts(@modes, $params->{mode} );
    print "<title>ASTPP - "
      . gettext("ASTPP - Open Source VOIP Billing Admin")
      . "</title>\n"
      . "<script src =\"/_astpp/menu.js\" type=\"text/javascript\"> </script>\n"
      . "<STYLE TYPE=\"text/css\">\n"
      . "<!--\n"
      . "  \@import url(/_astpp/style.css); \n" . "-->\n"
      . "</STYLE>\n"
      . "<BODY>\n"
      . "<table class=\"default\"><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
      . "<td align=center><H2>ASTPP - Open Source Voip Billing: $params->{mode}</H2></td>"
      . "</tr>
</table>
<table class=\"default\" width=100\%><tr><td>"
      . $menu
      . "</td></tr>
"
      . "<tr><td>"
      . $msg
      . "</td></tr>
</table>
"
      . $body . "<hr>"
      . $status
      . "<table class=\"default\" align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr>
</table>
"
      . "</BODY>";
}
elsif (!$astpp_db) { 
    print "<title>"
      . gettext("ASTPP - Open Source Voip Billing Login")
      . "</title>\n"
      . "<STYLE TYPE=\"text/css\">\n"
      . "<!--\n"
      . " \@import url(/_astpp/style.css); \n" . "-->\n"
      . "</STYLE>\n"
      . "<BODY>\n"
      . "<table class=\"default\"><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
      . "<td align=center><H2>"
      . gettext("ASTPP - Open Source VOIP Billing Login")
      . "</H2></td></tr>\n</table>"
	. "<table><tr><td>"
	. gettext("ASTPP - UNAVAILABLE - Please see your system administrator or visit www.astpp.org")
	. "</td></tr>"
	. "<tr><td>"
	. gettext("This message will come up on a new installation that has not been completely initialized.")
	. "</td></tr></table>";
print end_html;

}
else {
    print "<title>"
      . gettext("ASTPP - Open Source Voip Billing Login")
      . "</title>\n"
      . "<STYLE TYPE=\"text/css\">\n"
      . "<!--\n"
      . " \@import url(/_astpp/style.css); \n" . "-->\n"
      . "</STYLE>\n"
      . "<BODY>\n"
      . "<table class=\"default\"><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
      . "<td align=center><H2>"
      . gettext("ASTPP - Open Source VOIP Billing Login")
      . "</H2></td></tr>
\n</table>
\n"
      . "<table class=\"default\" width=100\%><tr><td colspan=2 align=center>$status</td></tr>
\n"
      . "<tr><td colspan=2 align=center>"
      . gettext("Please Login Now")
      . "</td></tr>
\n"
      . startform
      . "<tr><td width=50\% align=right>"
      . gettext("Username:")
      . "</td><td width=50\%>"
      . textfield('username')
      . "</td></tr>
\n"
      . "<tr><td align=right width=50\%>"
      . gettext("Password:")
      . "</td><td width=50\%>"
      . password_field('password')
      . "</td></tr>
\n"
      . "<tr><td colspan=2 align=center>"
      . submit( -name => 'mode', -value => gettext("Login") )
      . reset()
      . "</td></tr>
\n";
}
print end_html;
