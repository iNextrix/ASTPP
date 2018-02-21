#!/usr/bin/perl -w
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2006, Aleph Communications
#
# ASTPP Team (info@astpp.org)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
# Account Status Info
# 0 = Account InActive
# 1 = Account Active
# 2 = Account Deactivated
#
# CDR Status Info
# 0 - New line
# 1 - Billed Line
# 2 - Deactivated Line
#
#
# Account Type
# 0 - Regular User (Has login permissions for astpp-users.cgi)
# 1 - Reseller (Has login permissions for astpp-users.cgi and has reduced permissions for astpp-admin.cgi)
# 2 - Admin (Has login permissions everywhere)
# 3 - Provider (Has reduced login permissions in astpp-admin.cgi)
# 4 - Customer Service (Has reduced login permissions in astpp-admin.cgi)
# 5 - Call shop (Has reduced login permissions in astpp-admin.cgi)
# 6 - Booth (No login permissions)
###############################################################################
use CGI::Carp qw(fatalsToBrowser);
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
use HTML::Template;
use HTML::Template::Expr;
use Time::HiRes qw( gettimeofday tv_interval );
use Data::Paginate;
use DateTime;
use DateTime::TimeZone;
use ASTPP;
# use strict;
;    # We use DateTime::TimeZone to show users cdrs in their own timezones.
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
# print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");
use vars qw(@output $astpp_db $params $config
  $status $config $limit $accountinfo
  $osc_db $freepbx_db $rt_db $fs_db $opensips_db $ASTPP);
my $starttime = [gettimeofday];

$ASTPP = ASTPP->new;
$ASTPP->set_verbosity(0);    #Tell ASTPP debugging how verbose we want to be.
#$ASTPP->set_asterisk_agi($AGI);
$ASTPP->set_pagination_script("astpp-wraper.cgi");

my %types;
# my @Home     = ( gettext("Home Page") );
my @Accounts = (
    gettext("Create Account"), gettext("Process Payment"),
    gettext("Remove Account"), gettext("Edit Account"),
    gettext("List Accounts"),  gettext("View Details")
);



my @Rates = (
    gettext("Pricelists"),       gettext("Calc Charge"),
    gettext("Routes"),           gettext("Import Routes"),
    gettext("Periodic Charges"), gettext("Packages"),
    gettext("Counters")
);

my @DIDs = ( gettext("Manage DIDs"), gettext("Import DIDs") );

my @LCR = (
    gettext("Providers"), gettext("Trunks"),
    gettext("Outbound Routes"),
    gettext("Import Outbound Routes")
);

my @System = ( gettext("Purge Deactivated"), gettext("Configuration"), gettext("Taxes") );

my @Statistics = (
    gettext("List Errors"),
    gettext("Trunk stats"),    gettext("View CDRs")
#     ,gettext("LCR Tables")
);

my @Callingcards = (
    gettext("List Cards"),  gettext("Add Cards"),
    gettext("Delete Card"), gettext("Refill Card"),
    gettext("View Card"),   gettext("Update Card(s) Status"),
	gettext("Reset InUse"), gettext("CC Brands"),
    gettext("Callingcard CDRs")
);

my @SwitchConfig = ();

my @CallShops    = ( gettext("Create CallShop"), gettext("Remove CallShop") );

my @Booths       = (
    gettext("Create Booth"), gettext("Remove Booth"),
    gettext("List Booths"),  gettext("View Booth")
);

my @AdminReports = ( gettext("Reseller Report"), gettext("Provider Report") );

my @CallShopReports = ( gettext("Booth Report") );

my @ResellerReports = (
    #	gettext("Brand Report"),
    gettext("CallShop Report"),
    gettext("Reseller Report"),
    gettext("User Report")
);

my %menumap = (
    gettext('Accounts')         => \@Accounts,
    gettext('Rates')            => \@Rates,
    gettext('DIDs')             => \@DIDs,
    gettext('Statistics')       => \@Statistics,
    gettext('System')           => \@System,
    gettext('LCR')              => \@LCR,
    gettext('Calling Cards')    => \@Callingcards,
    gettext('Switch Config')    => \@SwitchConfig,
    gettext('Booths')           => \@Booths,
    gettext('Call Shops')       => \@CallShops,
    gettext('Admin Reports')    => \@AdminReports,
    gettext('CallShop Reports') => \@CallShopReports,
    gettext('Reseller Reports') => \@ResellerReports
);

my @months = (
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
);

my @techs = ( "SIP", "IAX2", "Zap", "Local", "OH323", "OOH323C" );
my @incs = ( "1", "6", "30", "60" );

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

my @output   = ("STDERR");           # "LOGFILE" );
my @language = all_language_codes;
@language = sort @language;
my @currency;

my @deviceprotocol = ("SIP");
my @countries      = all_country_names();
@countries = sort @countries;

my @timezones = DateTime::TimeZone->all_names;

@timezones = sort(@timezones);

my (
    $rt_db,  $astpp_db,  $config, $params, $param,
    $cdr_db, $agile_db,  $body,   $menu,   $status,
    $msg,    $loginstat, @modes,  $opensips_db
);

$params->{mode}     = "";
$params->{username} = "";
$params->{password} = "";
$loginstat          = 0;

sub login() {
    my ( $sql, $count, $record, $cookie, $cookie1, $accountinfo );
    $sql =
      $astpp_db->prepare( "SELECT COUNT(*) FROM accounts WHERE number = "
          . $astpp_db->quote( $params->{username} )
          . " AND password = "
          . $astpp_db->quote( $params->{password} )
          . " AND status = 1 AND type IN (1,2,3,4,5,0)" );
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

    if ( $count == 1 && !$params->{logintype}) {
        $status .= gettext("Successful Login!") . "<br>";
        print header( -cookie => [ $cookie, $cookie1, ] );
        $accountinfo = &get_account( $astpp_db, $params->{username} );
        $params->{logintype} = $accountinfo->{type};
    }

#   if ( $params->{username} eq 'admin' && $config->{auth} eq $params->{password} ) {

    if ( !$params->{username} && $config->{auth} eq $params->{password} ) {
        $status .= gettext("Successful Login!") . "<br>";
        $count = 1;
        print header( -cookie => [ $cookie, $cookie1 ] );
        $params->{logintype} = 2;
    }

    if ( $count == 0 && $params->{password} eq "" ) {
        $params->{mode} = "";
        $status .= gettext("Please Login") . "<br>";
        print header();
    }
    elsif ( $count == 0 ) {
        $params->{mode} = "";
        $status .= gettext("Login Failed") . "<br>";
        print header();
    }

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-USER:") . " $params->{username}"
    );


    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-PASS:") . " $params->{password}"
    );

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-AUTHCODE:") . " $config->{auth}"
    );

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-USER-COUNT:") . " $count"
    );

    return ( $params->{mode}, $count );
}


sub verify_login() {
    my ( $sql, $count, $record, $username, $password );
    $params->{username} = cookie('ASTPP_User');
    $params->{password} = cookie('ASTPP_Password');
    if ( !$params->{username} ) {
        $params->{username} = "";
    }

    if ( !$params->{password} ) {
        $params->{password} = "";
    }

    if ( $params->{username} && $params->{password} ) {
        $sql =
          $astpp_db->prepare( "SELECT COUNT(*) FROM accounts WHERE number = "
              . $astpp_db->quote( $params->{username} )
              . " AND password = "
              . $astpp_db->quote( $params->{password} )
              . " AND status = 1 AND type IN (1,2,3,4,5,0)" );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        $count = 0 if !$count;

        if ( $count == 1 ) {
            $accountinfo = &get_account( $astpp_db, $params->{username} );
            $params->{logintype} = $accountinfo->{type};
        }
    }

    if ( !$params->{username} && $config->{auth} eq $params->{password} ) {
        $count = 1;
        $params->{logintype} = 2;
    }

    if ( $count != 1 && !$params->{password} ) {
        $params->{mode} = "";
        $status .= "" . "<br>";
        $count = 0;
    }
    elsif ( $count != 1 ) {
        $params->{mode} = "";
        $status .= gettext("Login Failed") . "<br>";
        $count = 0;
    }

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-USER:") . " $params->{username}"
    ) if $config->{debug} == 1 && $params->{username};

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-PASS:") . " $params->{password}"
    ) if $config->{debug} == 1 && $params->{password};

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-AUTHCODE:") . " $config->{auth}"
    ) if $config->{debug} == 1;

    $ASTPP->debug(
        user  => $params->{username},
        debug => gettext("ASTPP-USER-COUNT:") . " $count"
    ) if $config->{debug} == 1;

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

sub build_menu() {
	my (@items) = @_;
	my @menu_list;
# 	my $template = HTML::Template->new(
# 	        filename          => '/var/lib/astpp/templates/sub_menu.tpl',
# 	        die_on_bad_params => $config->{template_die_on_bad_params}
# 	);
	foreach my $tmp (@items) {
		my %row;
	        $row{value} = $tmp;
	        push( @menu_list,  \%row );
	}
#         $template->param( menu => \@menu_list );
# 	return $template->output;
}



sub build_body() {
		
        return &build_homepage()

          if $params->{mode} eq gettext("Home Page")

              || $params->{mode} eq gettext("Home")

              || $params->{mode} eq ""

              || $params->{mode} eq gettext("Login")

              || $params->{mode} eq gettext("Logout");

	# Make sure that the main menus are covered

        return &build_menu(@Accounts) if $params->{mode} eq gettext("Accounts");

        return &build_menu(@AdminReports) if $params->{mode} eq gettext("Admin Reports");

        return &build_menu(@CallShops) if $params->{mode} eq gettext("Call Shops");

        return &build_menu(@Callingcards) if $params->{mode} eq gettext("Calling Cards");

        #return &build_menu(@DIDs) if $params->{mode} eq gettext("DIDs");

        return &build_menu(@LCR) if $params->{mode} eq gettext("LCR");

        return &build_menu(@Rates) if $params->{mode} eq gettext("Rates");

        return &build_menu(@Statistics) if $params->{mode} eq gettext("Statistics");

        return &build_menu(@SwitchConfig) if $params->{mode} eq gettext("Switch Config");

        return &build_menu(@System) if $params->{mode} eq gettext("System");

        return &build_menu(@CallShops) if $params->{mode} eq gettext("CallShops");

        return &build_menu(@Booths) if $params->{mode} eq gettext("Booths");

	# Cover all the submenus

    if ( $params->{logintype} == 2 ) {    #Admin Login

        return &build_providers() if $params->{mode} eq gettext("Providers");

        return &build_trunks()    if $params->{mode} eq gettext("Trunks");

        return &build_outbound_routes()

          if $params->{mode} eq gettext("Outbound Routes");

        return &build_pricelists() if $params->{mode} eq gettext("Pricelists");

        return &build_routes()     if $params->{mode} eq gettext("Routes");

        return &build_calc_charge()

          if $params->{mode} eq gettext("Calc Charge");

        return &build_list_errors()

          if $params->{mode} eq gettext("List Errors");

        return &build_lcr_tables()

          if $params->{mode} eq gettext("LCR Tables");

        return &build_dids() if $params->{mode} eq gettext("Manage DIDs");

        return &build_import_dids()

          if $params->{mode} eq gettext("Import DIDs");

        return &build_purge_deactivated()

          if $params->{mode} eq gettext("Purge Deactivated");

        return &build_create_account()

          if $params->{mode} eq gettext("Create Account");

        return &build_remove_account()

          if $params->{mode} eq gettext("Remove Account");

        return &build_process_payment()

          if $params->{mode} eq gettext("Process Payment");
	  
      return &build_add_callerID()

	  if $params->{mode} eq gettext("Add CallerID");

	return &build_cc_add_callerID()

	  if $params->{mode} eq gettext("Add CC CallerID");

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

        return &logout()

          if $params->{mode} eq gettext("Logout");

        return &build_packages()

          if $params->{mode} eq gettext("Packages");

        return &build_counters()

          if $params->{mode} eq gettext("Counters");

        return &build_statistics() 

          if $params->{mode} eq gettext("Asterisk Stats");

        return &build_create_card()

          if $params->{mode} eq gettext("Add Cards");

        return &build_list_cards()

          if $params->{mode} eq gettext("List Cards");

        return &build_list_cards()

          if $params->{mode} eq gettext("Calling Cards");

        return &build_update_card_status()

          if $params->{mode} eq gettext("Update Card(s) Status");

        return &build_reset_card_inuse()

          if $params->{mode} eq gettext("Reset InUse");

        return &build_view_card() if $params->{mode} eq gettext("View Card");

        return &build_cc_brands()

          if $params->{mode} eq gettext("CC Brands");

        return &build_callingcard_cdrs()

          if $params->{mode} eq gettext("Callingcard CDRs");

        return &build_delete_cards()

          if $params->{mode} eq gettext("Delete Card");

        return &build_refill_card()

          if $params->{mode} eq gettext("Refill Card");

        return &build_sip_devices()

          if $params->{mode} eq gettext("Asterisk(TM) SIP Devices");

        return &build_iax_devices()

          if $params->{mode} eq gettext("Asterisk(TM) IAX Devices");

        return &build_dialplan()

          if $params->{mode} eq gettext("Asterisk(TM) Dialplan");

        return &build_freeswitch_sip_devices()

          if $params->{mode} eq gettext("Freeswitch(TM) SIP Devices");

        return &build_stats_acd()

          if $params->{mode} eq gettext("Trunk stats");

        return &build_stats_acd()

          if $params->{mode} eq gettext("Statistics");

        return &build_periodic_charges()

          if $params->{mode} eq gettext("Periodic Charges");

        return &build_view_cdrs() if $params->{mode} eq gettext("View CDRs");

        return &build_view_cdrs_asterisk() if $params->{mode} eq gettext("View Asterisk(TM) CDRs");

        return &build_view_cdrs_freeswitch() if $params->{mode} eq gettext("View FreeSwitch(TM) CDRs");

        return &build_configuration()

          if $params->{mode} eq gettext("Configuration");

        return &build_view_invoice()

          if $params->{mode} eq gettext("View Invoice");

        return &build_add_callshop()

          if $params->{mode} eq gettext("Create CallShop");

        return &build_remove_callshop()

          if $params->{mode} eq gettext("Remove CallShop");

        return &build_admin_reseller_report()

          if $params->{mode} eq gettext("Reseller Report");

        return &build_admin_vendor_report()

          if $params->{mode} eq gettext("Provider Report");

        return &build_taxes()

          if $params->{mode} eq gettext("Taxes");

        return gettext("Not Available!") . "\n";

    }

    elsif ( $params->{logintype} == 3 ) {    #Provider Login

        return &build_stats_acd()

          if $params->{mode} eq gettext("Trunk Statistics");

        return &logout()          if $params->{mode} eq gettext("Logout");

        return &build_view_cdrs() if $params->{mode} eq gettext("View CDRs");

        return &build_view_cdrs_asterisk() if $params->{mode} eq gettext("View Asterisk CDRs");

        return &build_view_cdrs_freeswitch() if $params->{mode} eq gettext("View FreeSwitch CDRs");

        return &build_outbound_routes()

          if $params->{mode} eq gettext("Outbound Routes");

        $params->{mode} = gettext("Home");

        return gettext("Welcome to ASTPP!") . "\n"

          if $params->{mode} eq gettext("Home");

    }

    elsif ( $params->{logintype} == 4 ) {    #Customer Service Login

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

        return &build_dids()

          if $params->{mode} eq gettext("Manage DIDs");

        return &build_sip_devices()

          if $params->{mode} eq gettext("Asterisk(TM) SIP Devices");

        return &build_iax_devices()

          if $params->{mode} eq gettext("Asterisk(TM) IAX Devices");

        return &build_packages() if $params->{mode} eq gettext("Packages");

        return &build_counters() if $params->{mode} eq gettext("Counters");

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

        return &build_pricelists() if $params->{mode} eq gettext("Pricelists");

        return &build_routes()     if $params->{mode} eq gettext("Routes");

        return &build_create_account()

          if $params->{mode} eq gettext("Create Account");

        return &build_remove_account()

          if $params->{mode} eq gettext("Remove Account");

        return &build_process_payment()

          if $params->{mode} eq gettext("Process Payment");
	  
	return &build_add_callerID()

	  if $params->{mode} eq gettext("Add CallerID");

	return &build_cc_add_callerID()

	  if $params->{mode} eq gettext("Add CC CallerID");  

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

        return &build_dids_reseller()

          if $params->{mode} eq gettext("Manage DIDs");

        return &build_add_callshop()

          if $params->{mode} eq gettext("Create CallShop");

        return &build_remove_callshop()

          if $params->{mode} eq gettext("Remove CallShop");

        return &build_reseller_callshop_report()

          if $params->{mode} eq gettext("CallShop Report");

        return &build_reseller_reseller_report()

          if $params->{mode} eq gettext("Reseller Report");

        return &build_reseller_user_report()

          if $params->{mode} eq gettext("User Report");

        return &build_packages()

          if $params->{mode} eq gettext("Packages");

        return &build_counters()

          if $params->{mode} eq gettext("Counters");

        return gettext("Not Available!") . "\n";

    }

    elsif ( $params->{logintype} == 5 ) {    #Call Shop Login

        return &build_pricelists() if $params->{mode} eq gettext("Pricelists");

        return &build_routes()     if $params->{mode} eq gettext("Routes");

        return &build_add_booth() if $params->{mode} eq gettext("Create Booth");

        return &build_remove_booth() if $params->{mode} eq gettext("Remove Booth");

        return &build_list_booths() if $params->{mode} eq gettext("List Booths");

        return &build_view_booth() if $params->{mode} eq gettext("View Booth");

        return &build_create_card() if $params->{mode} eq gettext("Add Cards");

        return &build_list_cards()  if $params->{mode} eq gettext("List Cards");

        return &build_list_cards() if $params->{mode} eq gettext("Calling Cards");

        return &build_update_card_status() if $params->{mode} eq gettext("Update Card(s) Status");

        return &build_reset_card_inuse()

          if $params->{mode} eq gettext("Reset InUse");

        return &build_view_card() if $params->{mode} eq gettext("View Card");

        return &build_cc_brands() if $params->{mode} eq gettext("CC Brands");

        return &build_delete_cards()

          if $params->{mode} eq gettext("Delete Card");

        return &build_refill_card()

          if $params->{mode} eq gettext("Refill Card");

        return &build_callshop_callshop_report()

          if $params->{mode} eq gettext("Booth Report");

        return &build_list_booths()

          if $params->{mode} eq gettext("Home Page")

              || $params->{mode} eq gettext("Home")

              || $params->{mode} eq ""

              || $params->{mode} eq gettext("Login")

              || $params->{mode} eq gettext("Logout");

        return gettext("Not Available!") . "\n";

    }	
    elsif( $params->{logintype} == 0) { # User Login
	return &build_dids_user() if $params->{mode} eq gettext("DIDs"); 
	return &build_ani_map()   if $params->{mode} eq gettext("ANI Mapping");
    }
	
    else 
    {

        $params->{mode} = gettext("Home");
        return gettext("Not Available!") . "\n";

    }

}




sub build_purge_deactivated() {

    my ( $status, $body );
    return gettext("Cannot drop items until database is configured") unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Yes, Drop Them") ) {
        if ( $astpp_db->do("DELETE FROM outbound_routes WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated outbound routes.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated outbound routes.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM routes WHERE status = 2") ) {
            $status .= gettext("Dropped deactivated routes.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated routes.") . "<br>";
        }
        
        if ( $astpp_db->do("DELETE FROM accounts WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated accounts.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated accounts.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM pricelists WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated pricelists.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated pricelists.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM cdrs WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated cdrs.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated cdrs.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM trunks WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated trunks.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated trunks.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM dids WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated DIDs.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated DIDs.") . "<br>";
        }

        if ( $astpp_db->do("DELETE FROM packages WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated packages.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated packages.") . "<br>";
        }
        
        if ( $astpp_db->do("DELETE FROM callshops WHERE status = 2") ) 
        {
            $status .= gettext("Dropped deactivated callshops.") . "<br>";
        }
        else 
        {
            $status .= gettext("Unable to drop deactivated callshops.") . "<br>";
        }
    }

    return $status;
}

########  Reporting Module ################

sub build_filter($$) {
    my ( $additional_fields, $submit_title ) = @_;
    my ($body);
    return gettext("ASTPP Database Not Available!") . "<br>" unless $astpp_db;
    $cdr_db = &cdr_connect_db( $config, @output );
    return gettext("Asterisk CDR Database Not Available!") . "<br>"
      unless $cdr_db;
    # Set the defaults for the date options
    my ( undef, undef, undef, $current_day, $current_month, $current_year ) =
      localtime();
    my (
        $start_year,   $start_month,  $start_day,  $start_hour,
        $start_minute, $start_second, $end_year,   $end_month,
        $end_day,      $end_hour,     $end_minute, $end_second
    );

    my $count = 0;
    if ( !$params->{start_month} ) {
        $start_year   = $current_year + 1900;
        $start_month  = sprintf( "%02d", $current_month + 1 );
        $start_day    = "01";
        $start_hour   = "00";
        $start_minute = "00";
        $start_second = "00";
        $end_year     = $current_year + 1900;
        $end_month    = sprintf( "%02d", $current_month + 1 );
        $end_day      = $current_day;
        $end_hour     = "23";
        $end_minute   = "59";
        $end_second   = "59";
    }
    else {
        $start_year   = $params->{start_year};
        $start_month  = sprintf( "%02d", $params->{start_month} + 1 );
        $start_day    = sprintf( "%02d", $params->{start_day} );
        $start_hour   = sprintf( "%02d", $params->{start_hour} );
        $start_minute = sprintf( "%02d", $params->{start_minute} );
        $start_second = sprintf( "%02d", $params->{start_second} );
        $end_year     = $params->{end_year};
        $end_month    = sprintf( "%02d", $params->{end_month} + 1 );
        $end_day      = sprintf( "%02d", $params->{end_day} );
        $end_hour     = sprintf( "%02d", $params->{end_hour} );
        $end_minute   = sprintf( "%02d", $params->{end_minute} );
        $end_second   = sprintf( "%02d", $params->{end_second} );
    }


    $body .=
        "<form method=get><input type=hidden name=mode value=\""
      . param('mode')
      . "\"><table class=\"default\" width=100%>";

    $body .=
        "<tr class='rowone'><td width=50%  align='right'>"
      . gettext("Start date:")
      . "</td><td><input type=text name=start_year value=\"$start_year\" size=5><select name=start_month>";


    for ( my $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $start_month - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
   }

    $body .=
"</select><input type=text name=start_day value=\"$start_day\" size=3></td></tr>";
    $body .=
        "<tr class='rowone'><td align='right'>"
      . gettext("Start time:")
      . "</td><td><input type=text name=start_hour value=\"$start_hour\" size=3>"
      . "<input type=text name=start_minute value=\"$start_minute\" size=3>"
      . "<input type=text name=start_second value=\"$start_second\" size=3></td></tr>";

    $body .=
        "<tr  class='rowone'><td align='right'>"
      . gettext("End date:")
      . "</td><td><input type=text name=end_year value=\"$end_year\" size=5><select name=end_month>";

    for ( my $id = 0 ; $id < 12 ; $id++ ) {
        if ( $id == ( $end_month - 1 ) ) {
            $body .= "<option value=$id selected>$months[$id]";
        }
        else {
            $body .= "<option value=$id>$months[$id]";
        }
    }

    $submit_title = "Filter!" if !$submit_title;

    $body .=
"</select><input type=text name=end_day value=\"$end_day\" size=3></td></tr>";
    $body .=
        "<tr class='rowone'><td align='right'>"
      . gettext("End time:")
      . "</td><td><input type=text name=end_hour value=\"$end_hour\" size=3>"
      . "<input type=text name=end_minute value=\"$end_minute\" size=3>"
      . "<input type=text name=end_second value=\"$end_second\" size=3></td></tr>\n";

    $body .= $additional_fields if $additional_fields;
    $body .=
"<tr><td align=center colspan=2><input type=submit value=$submit_title></td></tr>";
    $body .= "</table>\n</form>";


    my %report_filter = (
        'start_year'   => $start_year,
        'start_month'  => $start_month,
        'start_day'    => $start_day,
        'start_hour'   => $start_hour,
        'start_minute' => $start_minute,
        'start_second' => $start_second,
        'end_year'     => $end_year,
        'end_month'    => $end_month,
        'end_day'      => $end_day,
        'end_hour'     => $end_hour,
        'end_minute'   => $end_minute,
        'end_second'   => $end_second,
        'start_date' =>
		"$start_year-$start_month-$start_day $start_hour:$start_minute:$start_second",
        'end_date' =>
          "$end_year-$end_month-$end_day $end_hour:$end_minute:$end_second",
        'form_body' => $body
    );

    return \%report_filter;
}



sub build_list_box($$) {
    my ( $in, $selected ) = @_;
    my $body = "";
    my %list;
#   undef %list;
    @list{@$in} = ();
    my @out = sort keys %list;    # remove sort if undesired
    for ( my $i = 0 ; $i < @out ; $i++ ) {
        if ( $out[$i] eq $selected ) {
            $body .= "<option value='$out[$i]' selected>$out[$i]</option>\n";
       }
        else {
            $body .= "<option value='$out[$i]'>$out[$i]</option>\n";
        }
    }
    return $body;
}



sub build_admin_report() {
    my ($body);
    return gettext("Cannot display reports until database is configured")
      unless $astpp_db;
    $cdr_db = &cdr_connect_db( $config, @output );
    return gettext("Cannot display reports until database is configured")
      unless $cdr_db;
    return gettext("Comming Soon!");
}

sub build_reseller_report() {
    my ($body);
    return gettext("Cannot display reports until database is configured")
      unless $astpp_db;
    $cdr_db = &cdr_connect_db( $config, @output );
    return gettext("Cannot display reports until database is configured")
      unless $cdr_db;
    return gettext("Coming Soon!");
}

#################### Stats stuff ###########################



#################### Stats stuff ###########################
sub build_view_cdrs_asterisk() {
	return &build_view_cdrs();
}

sub build_view_cdrs_freeswitch() {
	$config->{cdr_table} = $config->{freeswitch_cdr_table};
	return &build_view_cdrs();
}




sub build_homepage() {

    my $template = HTML::Template->new(
        filename          => '/var/lib/astpp/templates/home.tpl',
        die_on_bad_params => $config->{template_die_on_bad_params}
    );

    if (   $params->{logintype} == 1
        || $params->{logintype} == 5
        || $params->{logintype} == 5 && $astpp_db && $cdr_db )
    {
        my @accountlist =
          &list_accounts_selective( $astpp_db, $params->{username}, "-1" );
        my $accounts;
        my $tot_count = scalar @accountlist;
        my $count = 0;
        foreach (@accountlist) {
            $count++;
            $accounts .= "'" . $_ . "',";
            if ( $count < $tot_count ) {
                $accounts .= ",";
            }
        }

        $ASTPP->debug( user => $params->{username}, debug => $accounts );

        $template->param(
            customer_count => &count_accounts(
                $astpp_db, "WHERE type = 0 AND reseller = '$params->{username}'"
            )
        );

        $template->param(
            reseller_count => &count_accounts(
                $astpp_db, "WHERE type = 1 AND reseller = '$params->{username}'"
            )
        );

        $template->param(
            vendor_count => &count_accounts(
                $astpp_db, "WHERE type = 3 AND reseller = '$params->{username}'"
            )
        );

        $template->param(
            admin_count => &count_accounts(
                $astpp_db, "WHERE type = 2 AND reseller = '$params->{username}'"
            )
        );

        $template->param(
            callshop_count => &count_accounts(
                $astpp_db,
                "WHERE type = 5  AND reseller = '$params->{username}'"
            )
        );

        $template->param( total_owing =>
              &accounts_total_balance( $astpp_db, $params->{username} ) /
              1 );

        $template->param(
            total_due => $ASTPP->accountbalance( account => $params->{username} ) / 1 );
        $template->param(
            calling_cards_in_use => &count_callingcards(
                $astpp_db,
"WHERE inuse = 1 AND status = 1  AND reseller = '$params->{username}'"
            )
        );

        $template->param( dids => &count_dids( $astpp_db, "" ) );
        $template->param(
            unbilled_cdrs => &count_unbilled_cdrs( $config, $cdr_db, $accounts )
        );
    }
    elsif ( $params->{logintype} == 2 && $astpp_db && $cdr_db ) {
        $template->param(
            customer_count => &count_accounts( $astpp_db, "WHERE type = 0" ) );
        $template->param(
            reseller_count => &count_accounts( $astpp_db, "WHERE type = 1" ) );
        $template->param(
            vendor_count => &count_accounts( $astpp_db, "WHERE type = 3" ) );
        $template->param(
            admin_count => &count_accounts( $astpp_db, "WHERE type = 2" ) );
        $template->param(
            callshop_count => &count_accounts( $astpp_db, "WHERE type = 5" ) );
        $template->param(
            calling_cards_in_use => &count_callingcards(
                $astpp_db, "WHERE inuse = 1 AND status = 1"
            )
        );

        $template->param(
            total_owing => &accounts_total_balance( $astpp_db, "" ) / 1 );
        $template->param( dids => &count_dids( $astpp_db, "" ) );
        $template->param(
            unbilled_cdrs => &count_unbilled_cdrs( $config, $cdr_db, "NULL,''" )
        );

    }
    return $template->output;
}


sub build_account_info() {
    my ($total,$body,$status,  $description,$pricelists,$chargeid,$tmp,$number,
        $pagesrequired, $results_per_page, $results, $pageno, $reseller
    );

    return gettext("Cannot view account until database is configured") unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Post Charge...") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }
        my $timestamp = &prettytimestamp;

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {

            $accountinfo = &get_account( $astpp_db, $number );
            if ( $accountinfo->{reseller} eq $params->{username} ) {
                &write_account_cdr( $astpp_db, $number,
                    $params->{amount} * 1,
                    $params->{desc}, $timestamp, 0 );
                $status .= "Charge Posted";
            }
        }
        else {
            &write_account_cdr( $astpp_db, $number, $params->{amount} * 1,$params->{desc}, $timestamp, 0 );
            $status .= "Charge Posted";
        }



    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Add Charge...") ) {
        if ( $params->{accountnum} ne "" ) 
        {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }
        if ( $params->{id} ne "" ) 
        {
            $chargeid = $params->{id};
        }
        else 
        {
            $chargeid = $params->{id_list};
        }

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            $accountinfo = &get_account( $astpp_db, $number );
            if ( $accountinfo->{reseller} eq $params->{username} ) {
                $tmp ="INSERT INTO charge_to_account (charge_id,cardnum,status) VALUES ("
                  . $astpp_db->quote($chargeid) . ", "
                  . $astpp_db->quote($number) . ", " . "1)";
                $astpp_db->do($tmp);
            }
        }
        else 
        {
            $tmp = "INSERT INTO charge_to_account (charge_id,cardnum,status) VALUES ("
            	. $astpp_db->quote($chargeid).", "
                . $astpp_db->quote($number) . ", " . "1)";

            $astpp_db->do($tmp);
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Remove Charge...") ) 
    {
        if ( $params->{accountnum} ne "" ) 
        {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            $accountinfo = &get_account( $astpp_db, $number );
            if ( $accountinfo->{reseller} eq $params->{username} ) {
                $tmp = "DELETE FROM charge_to_account WHERE id = "
                  . $astpp_db->quote( $params->{chargeid} );

                $astpp_db->do($tmp);
            }
        }
        else 
        {
            $tmp = "DELETE FROM charge_to_account WHERE id = "
              . $astpp_db->quote( $params->{chargeid} );
            $astpp_db->do($tmp);
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Remove DID") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $status .= &remove_did( $astpp_db, $config, $params->{DID}, $number );
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Purchase DID") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else {
            $number = $params->{numberlist};
        }
        $status .= &purchase_did( $astpp_db, $config, $params->{did_list}, $number );

    }

    elsif ( defined($params->{action}) && $params->{action} eq gettext("Map ANI") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }

        $tmp ="INSERT INTO ani_map (number,account,context) VALUES ("
          . $astpp_db->quote( $params->{ANI} ) . ", "
          . $astpp_db->quote($number) . ", "
          . $astpp_db->quote( $params->{context} ) . ")";

        if ( $astpp_db->do($tmp) ) {
            $status .=gettext("ANI") . " '" . $params->{ANI} . "' ". gettext("has been added!") . "<br>";
        }
        else 
        {
            $status .=gettext("ANI") . " '". $params->{ANI} . "' ". gettext("FAILED to create!") . "<br>";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Remove ANI") ) {

        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }

        $tmp ="DELETE FROM ani_map WHERE number = "
          . $astpp_db->quote( $params->{ANI} )
          . " AND account = ". $astpp_db->quote($number);

        if ( $astpp_db->do($tmp) )         
        {
            $status .= gettext("ANI") . " '". $params->{ANI} . "' ". gettext("has been dropped!") . "<br>";
        }
        else 
        {
            $status .=gettext("ANI") . " '". $params->{ANI} . "' ". gettext("FAILED to remove!") . "<br>";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Map IP") )     
    {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }

        $tmp ="INSERT INTO ip_map (ip,account,prefix,context,created_date) VALUES ("
          . $astpp_db->quote( $params->{ip} ) . ", "
          . $astpp_db->quote($number) . ", "
          . $astpp_db->quote( $params->{prefix} ) . ", "
          . $astpp_db->quote( $params->{ipcontext} ) . ", "
 	  . "NOW()" . ")";

        $ASTPP->debug( user => $params->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("IP") . " '". $params->{ip} . "' ". gettext("has been added!") . "<br>";
        }
        else 
        {
            $status .=gettext("IP") . " '". $params->{ip} . "' ". gettext("FAILED to create!") . "<br>";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Remove IP") ) {
        if ( $params->{accountnum} ne "" ) {
            $number = $params->{accountnum};
        }
        else 
        {
            $number = $params->{numberlist};
        }
        $tmp ="DELETE FROM ip_map WHERE ip = ". $astpp_db->quote( $params->{ip} ). " AND account = ". $astpp_db->quote($number)
          . " AND prefix = "
          . $astpp_db->quote( $params->{prefix} );
        if ( $astpp_db->do($tmp) ) {
            $status .=gettext("IP") . " '". $params->{ip} . "' ". gettext("has been dropped!") . "<br>";
        }
        else 
        {
            $status .= gettext("IP") . " '". $params->{ip} . "' ". gettext("FAILED to remove!") . "<br>";
        }

    }

    if ( $number ne "" ) 
    {
        $accountinfo = &get_account( $astpp_db, $number );
    }
    elsif ( $params->{accountnum} ne "" ) 
    {
        $accountinfo = &get_account( $astpp_db, $params->{accountnum} );
    }
    else 
    {
        $accountinfo = &get_account( $astpp_db, $params->{numberlist} );
    }

    return $status;
}




sub generatecallingcards() {
    my ( $params, $config ) = @_;
    my ( $status, $description, $pricelistdata, $number, $count );
    $status      = "";
    $description = gettext("Account Setup");
    $count       = 0;
    if ( $config->{email} eq "YES" ) {
        open( EMAIL, "| $config->{mailprog} -t" )
          || die "Error - could not write to $config->{mailprog}\n";
        print EMAIL"From: $config->{company_email}\n";
        print EMAIL "Subject: $config->{company_name} New Account Created\n";
        print EMAIL"To: $config->{emailadd}\n";
        print EMAIL"Content-type: text/plain\n\n";
        print EMAIL
"You have added $params->{count} calling cards in the amount of $params->{value} cents. \n\n";
    }
    my $brandinfo = &get_cc_brand( $astpp_db, $params->{brand} );
    $ASTPP->debug(
        user  => $params->{username},
        debug => "BRAND: $params->{brand}"
    );

    while ( $count < $params->{count} ) {
        my ( $number, $pin ) =
          &add_callingcard( $astpp_db, $config, $brandinfo, $params->{status},
            $params->{value},
            $params->{account}, $brandinfo->{pin} );
        $count++;

        if ( $config->{email} eq "YES" ) {
            print EMAIL"Account: $number Pin: $pin \n";
        }
        my $cardinfo = &get_callingcard( $astpp_db, $number, $config );

        $status .=
"Calling Card: $number Pin: $pin Sequence: $cardinfo->{id} added successfully <br>";
    }

    if ( $config->{email} eq "YES" ) {
       close(EMAIL);
    }
    return $status;
}


sub generate_accounts() {
    my ( $params, $config ) = @_;
    my ( $status, $description, $pricelistdata, $cardlist );
    $description = gettext("Account Setup");
    $cardlist = &get_account_including_closed( $astpp_db, $params->{number} );
	
    if ( !$cardlist->{number} ) {
        &addaccount( $astpp_db, $config, $params );
        if ( $params->{accounttype} == 1 ) {
            &add_pricelist( $astpp_db, $params->{number}, 6, 0,
                $params->{number} );
            &add_reseller( $astpp_db, $config, $params->{number},
                $params->{posttoexternal} );
        }

        if ( $params->{accounttype} == 5 ) {
            &add_pricelist( $astpp_db, $params->{number}, 6, 0,
                $params->{number} );
            &add_reseller( $astpp_db, $config, $params->{number},
                $params->{posttoexternal} );
        }

        if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
	    $params->{extension} = $params->{number};
	    $params->{secret} = $params->{accountpassword};
            &email_add_user( $astpp_db, '', $config, $params );
        }
        my $timestamp = &prettytimestamp;
        $astpp_db->do(
            "INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
              . $astpp_db->quote( $params->{number} ) . ","
              . $astpp_db->quote($description) . ","
              . $astpp_db->quote( $params->{pennies} * 100 ) . ","
              . $astpp_db->quote($timestamp)
              . ")" );
		
        $cardlist =  &get_account_including_closed( $astpp_db, $params->{number} );
		
		if ( $cardlist->{number} ) {
            $status .= "Account $params->{number} added successfully" . "<br>";
        }
        else {
            $status .= "Account $params->{number} Failed to Add!" . "<br>";
        }

    }
    elsif ( $cardlist->{status} != 1 ) {
        if (
            $astpp_db->do(
                "UPDATE accounts SET status = 1 WHERE number ="
                  . $astpp_db->quote( $params->{number} )

            )

          )

        {



            $status .=
                gettext("Account:")
              . " $params->{number} "
              . gettext("has been (re)activated")
              . "<br>\n";
        }
        else {
            $status .=
                gettext("Account:")
              . " $params->{number} "
              . gettext("failed to (re)activate!")
              . "<br>\n";
        }
        if ( $cardlist->{type} == 1 ) {
            $astpp_db->do( "UPDATE resellers SET status = 1 WHERE name ="
                  . $astpp_db->quote( $params->{number} ) );
        }

        if ( $config->{email} == 1 ) {
            &email_reactivate_account( $astpp_db, '', $config, $params, );
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
    return gettext("Database is NOT configured!") . " \n" unless $astpp_db;
    @pricelists = $ASTPP->list_pricelists( reseller => $params->{logged_in_reseller} );
    return gettext("Please configure 'Pricelists'") . "\n" unless @pricelists;

    if ( defined($params->{action}) && $params->{action} eq gettext("Generate Account") ) {
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
           my $pricelistinfo = &get_pricelist( $astpp_db, $params->{pricelist} );
            $params->{pricelist} = $params->{username}
              if $pricelistinfo->{reseller} ne $params->{username};
            $params->{reseller} = $params->{username};
        }
        $params->{count}   = 1;
        $params->{pennies} = 0;
        $params->{number}  = $params->{customnum};
	if ($params->{number} ne "") {
        $status .= &generate_accounts( $params, $config );
	&addaccountemailtemplate($astpp_db,$config,$params);
	&addinvoiceconf($astpp_db,$config,$params);
        my $accountinfo = &get_account( $astpp_db, $params->{number} );
       
        ##return $accountinfo;
        ##  Here we add support to add IAX and SIP devices at account creation.
	
        if ( $params->{SIP} ) {
		
            $config->{rt_sip_type} = $params->{devicetype};
            $config->{ipaddr}      = $params->{ipaddr};
			
            if ( $config->{users_dids_rt} == 1 ) {
                my $name = &finduniquesip_rt( $params->{number} );
                $status .= &add_sip_user_rt(
                    $rt_db,             $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );
		
               $status .= "<br>";
        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
            		&email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
        	}
            }
            if ( $config->{users_dids_amp} == 1 ) {
                my $name =
                  &finduniquesip_freepbx( $freepbx_db, $config,
                    $params->{number} );
                $status .= &add_sip_user_freepbx($freepbx_db,$config,$name,$params->{accountpassword},
				$params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );
                $status .= "<br>";
        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
			&email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
       		}

            }

            if ( $config->{opensips} == 1 ) {		
		my $name = &finduniquesip_opensips( $opensips_db, $config,
                    $params->{number} );  
                $status .= 
                  &add_sip_user_opensips( $opensips_db, $config, $name,
                    $params->{accountpassword},
                    $params->{number}, $params );
		    $status .= "<br>";

        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
			&email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
        	}
            }
		
		
           if ( $config->{users_dids_freeswitch} == 1 ) {
		my ($failure,$name);
                ($failure, $status, $name) = $ASTPP->fs_add_sip_user(
			accountcode 	=> $params->{number},
			freeswitch_domain => $config->{freeswitch_domain},
			freeswitch_context => $config->{freeswitch_context},
			vm_password	=> $params->{accountpassword},
			password	=> $params->{accountpassword},
			sip_ext_prepend	=> $config->{sip_ext_prepend},
		    );
		
                $status .= "<br>";
        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
            		&email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
        	}
            }
        }

        if ( $params->{IAX2} ) {
            $config->{rt_iax_type} = $params->{devicetype};
            $config->{ipaddr}      = $params->{ipaddr};
            if ( $config->{users_dids_amp} == 1 ) {
                my $name =
                  &finduniqueiax_freepbx( $freepbx_db, $config,
                    $params->{number} );
                $status .= &add_iax_user_freepbx(
                    $freepbx_db,        $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );

                $status .= "<br>";

        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {

			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
            		&email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
        	}
            }
            if ( $config->{users_dids_rt} == 1 ) {
                my $name = &finduniqueiax_rt( $params->{number} );
                $status .= &add_iax_user_rt(
                    $rt_db,             $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );

                $status .= "<br>";

        	if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
			$params->{extension} = $name;
			$params->{secret} = $params->{accountpassword};
            &email_add_device( $astpp_db, '', $config, $params );
			print STDERR "Sent Device Generation Email\n";
        	}

           }
        }
        ###  End of Device creation support

} else {
		$status = "No account number entered!";
	}
    }
    return 1;
}







sub build_cc_brands() {
    my ( @pricelists,$status,$body, $number,$inuse,$cardstat, $cardinfo, $sql,$pagesrequired, $pageno,$tmp);
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");

    if ( defined($params->{action}) && $params->{action} eq gettext("Delete...") ) {
        my ( $tmp, $sql );
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            $tmp = "DELETE FROM callingcardbrands WHERE name = ". $astpp_db->quote( $params->{name} )
            . " AND reseller = ". $astpp_db->quote( $params->{username} );
        }
        else 
        {
            $tmp = "DELETE FROM callingcardbrands WHERE name = "
              . $astpp_db->quote( $params->{name} );
        }
        $ASTPP->debug( user => $params->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Brand Deleted!");
        }
        else 
        {
            print "$tmp failed";
            $status .= gettext("Brand Deletion Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my ($sql);
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
             $sql = "INSERT INTO callingcardbrands (name,reseller,pricelist,language,pin,validfordays,maint_fee_pennies,"
              . "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies,status,min_length_minutes,min_length_pennies) VALUES ("
              . $astpp_db->quote( $params->{brandname} ) . ", "
              . $astpp_db->quote( $params->{username} ) . ", "
              . $astpp_db->quote( $params->{pricelist} ) . ", "
              . $astpp_db->quote( $params->{language} ) . ", "
              . $astpp_db->quote( $params->{pin} ) . ", "
              . $astpp_db->quote( $params->{validdays} ) . ", "
              . $astpp_db->quote( $params->{maint_fee_pennies} ) . ", "
              . $astpp_db->quote( $params->{maint_fee_days} ) . ", "
              . $astpp_db->quote( $params->{disconnect_fee_pennies} ) . ", "
              . $astpp_db->quote( $params->{minute_fee_minutes} ) . ", "
              . $astpp_db->quote( $params->{minute_fee_pennies} ) . ", 1,"
              . $astpp_db->quote( $params->{min_length_minutes} ) . ", "
              . $astpp_db->quote( $params->{min_length_pennies} ) . ")";
        }
        else 
        {
             $sql = "INSERT INTO callingcardbrands (name,pricelist,language,pin,validfordays,maint_fee_pennies,"
              . "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies,status,min_length_minutes,min_length_pennies) VALUES ("
              . $astpp_db->quote( $params->{brandname} ) . ", "
              . $astpp_db->quote( $params->{pricelist} ) . ", "
              . $astpp_db->quote( $params->{language} ) . ", "
              . $astpp_db->quote( $params->{pin} ) . ", "
              . $astpp_db->quote( $params->{validdays} ) . ", "
              . $astpp_db->quote( $params->{maint_fee_pennies} ) . ", "
              . $astpp_db->quote( $params->{maint_fee_days} ) . ", "
              . $astpp_db->quote( $params->{disconnect_fee_pennies} ) . ", "
              . $astpp_db->quote( $params->{minute_fee_minutes} ) . ", "
              . $astpp_db->quote( $params->{minute_fee_pennies} ) . ", 1,"
              . $astpp_db->quote( $params->{min_length_minutes} ) . ", "
              . $astpp_db->quote( $params->{min_length_pennies} ) . ")";
        }
        $ASTPP->debug( user => $params->{username}, debug => "sql" );
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Brand Added!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Brand Creation Failed!");
        }
        $params->{action} = gettext("Information...");
    }    
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {
        my ($sql);
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            $sql = "UPDATE callingcardbrands SET". " pricelist=". $astpp_db->quote( $params->{pricelist} ) . ", "
			. " language=". $astpp_db->quote( $params->{language} ) . ", " . " pin="
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
              . $astpp_db->quote( $params->{minute_fee_pennies} ) . ", "
              . " min_length_minutes="
              . $astpp_db->quote( $params->{min_length_minutes} ) . ", "
              . " min_length_pennies="
              . $astpp_db->quote( $params->{min_length_pennies} ) . ", "
              . "status=1 "
              . " WHERE name = "
              . $astpp_db->quote( $params->{name} )
              . " AND reseller = "
              . $astpp_db->quote( $params->{username} );

        }
        else 
        {
            $sql ="UPDATE callingcardbrands SET". " pricelist=". $astpp_db->quote( $params->{pricelist} ) . ", "
              . " language="
              . $astpp_db->quote( $params->{language} ) . ", " . " pin="
              . $astpp_db->quote( $params->{pin} ) . ", "
              . " validfordays=". $astpp_db->quote( $params->{validdays} ) . ", "
              . " maint_fee_pennies="
              . $astpp_db->quote( $params->{maint_fee_pennies} ) . ", "
              . " maint_fee_days="
              . $astpp_db->quote( $params->{maint_fee_days} ) . ", "
              . " disconnect_fee_pennies="
              . $astpp_db->quote( $params->{disconnect_fee_pennies} ) . ", "
              . " minute_fee_minutes="
              . $astpp_db->quote( $params->{minute_fee_minutes} ) . ", "
              . " minute_fee_pennies="
              . $astpp_db->quote( $params->{minute_fee_pennies} ) . ", "
              . " min_length_minutes="
              . $astpp_db->quote( $params->{min_length_minutes} ) . ", "
              . " min_length_pennies="
              . $astpp_db->quote( $params->{min_length_pennies} ) . ", "
              . "status=1 "
              . " WHERE name ="
              . $astpp_db->quote( $params->{name} );
        }
        $ASTPP->debug( user => $params->{username}, debug => $sql );
        if ( $astpp_db->do($sql) ) 
        {
            $status .= gettext("Brand Updated!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Brand Update Failed!");
        }
        $params->{action} = gettext("Information...");
    }
    return 1;
}



sub build_create_card() {

    my ( @pricelists, $status, $body, @brands );

    return gettext("Database is NOT configured!") . " \n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Generate Card(s)") ) {

        $status .= &generatecallingcards( $params, $config );

    }

    return $status;

}

sub build_update_card_status() {

    my (@brands,$brandsql, @pricelists, $status, $body, $count, $sql );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {

        @brands = &list_cc_brands_reseller( $astpp_db, $params->{username} );
    }
    else 
    {
        @brands = &list_cc_brands($astpp_db);
    }

    @pricelists = $ASTPP->list_pricelists( reseller => $params->{logged_in_reseller} );

    return gettext("Please configure 'Pricelists'!") . "\n" unless @pricelists;

    if ( defined($params->{action}) && $params->{action} eq gettext("Update Status on Card(s)") ) {

        my $sequence = $params->{starting};

        my $ending   = $params->{ending};
        my $active = $params->{status};

        while ( $sequence <= $ending ) {

            my $brandssql;

            if ( scalar(@brands) >= 2 ) {

                my $count = 0;

                $brandsql = " IN (";

                foreach my $brand (@brands) {

                    $brandsql .= "'$brand'";

                    $count++;

                    $brandsql .= "," if $count < scalar(@brands);

                }

                $brandsql .= ")";

            }

            else 
            {

                $brandsql = " = '$brands[0]'";

            }

            $sql = "UPDATE callingcards SET status ="

              . $astpp_db->quote($active)

              . "WHERE id ="

              . $astpp_db->quote($sequence)

              . " AND brand "

              . $brandsql;

            $astpp_db->do($sql) || print "$sql failed";

            $status .= "$sequence " . gettext("Set To") . " $params->{status}<br>\n";

            $sequence++;

        }

    }

    return $status;

}


sub build_reset_card_inuse() {

    my ( @brands,$brandsql,@pricelists, $status, $body, $count );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Reset") ) {

        my $brandssql;

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            @brands = &list_cc_brands_reseller( $astpp_db, $params->{username} );
        }
        else 
        {
            @brands = &list_cc_brands($astpp_db);
        }
        
        if ( scalar(@brands) >= 2 ) 
        {

            my $count = 0;

            $brandsql = " IN (";

            foreach my $brand (@brands) {

                $brandsql .= "'$brand'";

                $count++;

                $brandsql .= "," if $count < scalar(@brands);

        	}
        

        	$brandsql .= ")";

        }

        else 
        {

            $brandsql = " = '$brands[0]'";

        }
        

        my $sql = "UPDATE callingcards SET inuse = 0 WHERE cardnumber ="

          . $astpp_db->quote( $params->{cardnumber} )

          . " AND brand "

          . $brandsql;

        $astpp_db->do($sql) || print "$sql failed";

        $status .= "$params->{cardnumber} " . gettext("In Use Reset") . "<br>\n";

    }

    return $status;

}

sub update_balance() {

    my ( $cardinfo, $charge ) = @_;

    my (@brands,$brandsql);

    if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {

        @brands = &list_cc_brands_reseller( $astpp_db, $params->{username} );

    }

    else 
    {

        @brands = &list_cc_brands($astpp_db);

    }

    if ( scalar(@brands) >= 2 ) 
    {

        my $count = 0;

        $brandsql = " IN (";

        foreach my $brand (@brands) {

            $brandsql .= "'$brand'";

            $count++;

            $brandsql .= "," if $count < scalar(@brands);

        }

        $brandsql .= ")";

    }

    else {

        $brandsql = " = '$brands[0]'";

    }

    my $sql = "UPDATE callingcards SET value = "

      . $astpp_db->quote( ($charge) + $cardinfo->{value} )

      . " WHERE cardnumber = "

      . $astpp_db->quote( $cardinfo->{cardnumber} )

      . " AND brand "

      . $brandsql;

    $ASTPP->debug( user => $params->{username}, debug => $sql );

    $astpp_db->do($sql) || print "$sql " . gettext("FAILED");

}


sub build_refill_card() {

    my ( @pricelists, $status, $body, $count, $cardinfo );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Refill") ) {

        $cardinfo = &get_callingcard( $astpp_db, $params->{cardnumber}, $config );

        $ASTPP->debug(

            user  => $params->{username},

            debug => "CARDNUMBER: " . $cardinfo->{cardnumber}

        );

        &update_balance( $cardinfo, $params->{pennies});

        $status .= "$params->{cardnumber} " . gettext("Refilled") . "<br>\n";

    }

    return $status;

}

sub build_delete_cards() {

    my ( @brands,$brandsql,@pricelists, $status, $body, $count );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Delete") ) {

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {

            @brands = &list_cc_brands_reseller( $astpp_db, $params->{username} );

        }

        else 
        {

            @brands = &list_cc_brands($astpp_db);

        }

        if ( scalar(@brands) >= 2 ) {

            my $count = 0;

            $brandsql = " IN (";

            foreach my $brand (@brands) {

                $brandsql .= "'$brand'";

                $count++;

                $brandsql .= "," if $count < scalar(@brands);

            }

            $brandsql .= ")";

        }

        else 
        {

            $brandsql = " = '$brands[0]'";

        }

        my $sql = "UPDATE callingcards SET status = 2 WHERE cardnumber ="

          . $astpp_db->quote( $params->{cardnumber} )

          . " AND brand "

          . $brandsql;

        $astpp_db->do($sql) || print "$sql " . gettext("FAILED");

        $status .= "$params->{cardnumber} " . gettext("Deleted") . "<br>\n";

    }

    return $status;

}






############## Begin Code from convergence.com.pk

sub build_lcr_tables() {

    my ($body);
    # Convergence OpenPBX Tools Version 7.0 : AGI
    # (c) MMVI Convergence. All rights reserved.
    # <info@convergence.com.pk> http://www.convergence.com.pk
    #
    # This program is free software, distributed under the terms of
    # the GNU General Public License. http://www.gnu.org/licenses.html
    #

#     use strict;
    use DBI();
    use Time::HiRes qw( gettimeofday tv_interval );
    my $t0 = [gettimeofday];

    my $target = shift @ARGV;

    if ( !$target ) { print "no target entered\n"; exit 1; }
    print "$t0 $target\n";
    my $ltable = "s_$target";    # temp table
    my $mysel  = shift @ARGV;

    if ( !$mysel ) {
        $mysel = "WHERE comment LIKE 'Pa%'";
        $mysel = "WHERE comment LIKE 'Mold%'";
        print "no specific pattern entered\n";
    }
    elsif ( $mysel eq "NONE" ) {
        print "\nWHOA!!! doing all routes, baby! \n\n";
        $mysel = "";
    }
    else {
        $mysel = "WHERE comment like \'$mysel%\'";
    }

    $mysel = "SELECT pattern FROM $ltable " . "$mysel";
    print "using $mysel\n";
    my $td  = 0;
    my $tn  = 0;
    my $avg = 1;

    # full table for customer with all workings

    my $ztable = "x_$target";

    # final a-z forcustomer
    my $otable = "z_$target";

    # our master costs table
    my $btable = "costs";

    # our ordered costs table
    my $ttable = "t_$btable";

    # temp table in memory
    my $ptable = "t_$target";

    # minimum delta
    my $minvar = 21;

    # standard markup
    my $defmarkup = 20;
    my $counter = 0;
    $td = tv_interval($t0);
    my $sp1 = $astpp_db->prepare(

"select distinct pricelist from $btable where status=1 and pricelist != '$target'"
    );

    $sp1->execute();
    my $tt1 = $astpp_db->prepare("DROP TABLE IF EXISTS `$ttable`");
    $tt1->execute();
    $tt1 = $astpp_db->prepare(
"create table $ttable select * from $btable where status=1 order by pattern desc"
    );

   $tt1->execute();
    $tt1 = $astpp_db->prepare(
        "alter table $ttable ADD PRIMARY KEY ( `pattern`,`pricelist` )");
    $tt1->execute();

    my @pls = ();
    push( @pls, $target );

    while ( my $rpl = $sp1->fetchrow_hashref() ) {
        my $pricelist = $rpl->{'pricelist'};
        push( @pls, $pricelist );
        $td = tv_interval($t0);
        print "$td\tgot pricelist $pricelist\n";
    }

    $sp1->finish();

    my $npls = @pls;
    my $mpls = $npls;
    my $sta  = "";

    while ( $mpls > 0 ) {
        $mpls--;
        $sta = join ' ', "$sta", "`$pls[$mpls]` int( 11 ) default NULL,";
    }

    $tt1 = $astpp_db->prepare("DROP TABLE IF EXISTS `$ltable`");

    $tt1->execute();
    $tt1 = $astpp_db->prepare( "
	 CREATE TABLE `$ltable` (

		 `pattern` char( 40 ) NOT NULL default '',
		 `comment` char( 80 ) default NULL ,
		 $sta
		 `highest` char( 80 ) default NULL ,
		 `lowest` char( 80 ) default NULL ,
		 `spread` int( 11 ) default NULL ,
		 `delta` int( 11 ) default NULL ,
		 `markup` int( 11 ) default NULL ,
		 `offer` int( 11 ) default NULL ,
		 `win` char( 8 ) default NULL ,
		 PRIMARY KEY ( `pattern` )
		 ) TYPE = HEAP" );

    $tt1->execute();
    $td = tv_interval($t0);
    print "$td\tcreated $ltable\n";

    my $sdp = $astpp_db->prepare(
"insert into $ltable(pattern,comment) select pattern,comment from $ttable where status = 1 group by pattern having count(pattern)>=1"
    );
    $sdp->execute();
    $sdp->finish();

    $td = tv_interval($t0);
    print "$td\tpopulated patterns in $ltable\n";

    $sdp = $astpp_db->prepare($mysel);

    $sdp->execute();
    $td = tv_interval($t0);

    print
"$td\tcalculating LCR ... please wait 0.0577 seconds per route in $btable\n\n";

    while ( my $rdp = $sdp->fetchrow_hashref() ) {
        $counter++;
        my $pattern = $rdp->{'pattern'};
        $pattern =~ m/\)(\d+)\./;
        my $pat = $1;

        $mpls = $npls;
        if ( $config->{debug} ) { &pt($t0, "enter $pat"); }

        my $highest   = 0;
        my $lowest    = -1;
        my $offer     = 0;
        my $markup    = 0;
        my $low       = "";
        my $high      = "";
        my $spread    = 1;
        my $gottarget = 0;
        my $delta     = 0;
        my $stm =
"SELECT comment,cost,pricelist from $ttable where $pat RLIKE pattern group by pricelist";

       my $spl = $astpp_db->prepare($stm);
        $spl->execute();

        if ( $config->{debug} ) { &pt("exec select"); }

        while ( my $rdl = $spl->fetchrow_hashref() ) {

            if ( $config->{debug} ) { &pt("enter insert"); }

            my $cost      = $rdl->{'cost'};

            my $pricelist = $rdl->{'pricelist'};

           my $stm =
              "UPDATE $ltable set $pricelist='$cost' where pattern='$pattern'";
            my $il = $astpp_db->prepare($stm);

           $il->execute();

            if ( $pricelist ne $target ) {
                if ( $lowest == -1 or $cost < $lowest ) {
                    $lowest = $cost;
                    $low    = $pricelist;
                }

               if ( $cost > $highest ) {
                    $highest = $cost;
                    $high    = $pricelist;
                }
                $delta = $spread = $highest - $lowest;
            }
           else {
                $gottarget = $cost;
            }
        }

        my $win = "";
        if ($gottarget) {
            $delta = $gottarget - $lowest;
            if ( $delta < $minvar ) {
                $offer = $lowest + $defmarkup;
                $win   = "lose";
            }
        }
        else {
            if ( $lowest > 0 and $lowest < 201 ) { $markup = $lowest * 0.15; }
            elsif ( $lowest > 200 and $lowest < 401 ) {

                $markup = $lowest * 0.1;
            }
            elsif ( $lowest > 400 and $lowest < 601 ) {
                $markup = $lowest * 0.075;
            }
            elsif ( $lowest > 600 and $lowest < 901 ) {
                $markup = $lowest * 0.06;
            }
            elsif ( $lowest > 1000 and $lowest < 1501 ) {
                $markup = $lowest * 0.05;
            }
            #elsif ($lowest > 1500) { $markup=$lowest*.04; }
            else { $markup = $lowest * 0.04; }
        }

        if ( $markup < $defmarkup ) { $markup = $defmarkup; }
        $offer = $lowest + $markup;
        if ( $config->{debug} ) { &pt("exit insert"); }
        $stm =
"UPDATE $ltable set highest='$high',lowest='$low',spread='$spread',delta='$delta',markup='$markup',offer='$offer',win='$win' where pattern='$pattern'";

        my $ul = $astpp_db->prepare($stm);
        $ul->execute();
        print "$counter $stm\n";
        $spl->finish();

   }

    if ( $config->{debug} ) { &pt("exit select"); }

    if ( $config->{debug} ) {

        $td  = tv_interval($t0);

        $avg = $td / $counter;
        
		$tn  = $tn + $td;

        print "$counter routes \tt $tn \taveraged $avg\n";
    }

    $td = tv_interval($t0);

    print "\n$td\twhee ... got total $counter\n";

    $sdp->finish();

    &mktb($t0, $ztable,

        "select * from $ltable where offer is not null order by pattern" );

    &mktb($t0, $otable, "select pattern,comment,offer from $ztable" );

    return $body;

}


sub mktb {
	my ($t0, $table, $select) = @_;
    my $tt1    = $astpp_db->prepare("DROP TABLE IF EXISTS `$table`");
    $tt1->execute();
    $tt1 = $astpp_db->prepare("create table $table $select");
    $tt1->execute();
    $tt1 = $astpp_db->prepare("alter table $table ADD PRIMARY KEY ( `pattern` )");
    $tt1->execute();

    my $td = tv_interval($t0);
    print "$td\tmade table $table\n";
}


sub pt {
	my ($t0,$p);
    my $td = tv_interval($t0);
    print "$p\t$td\n";
}


### End of Code from convergence.com.pk




sub build_list_errors() {
    my ( @cdrlist, $results, $body, $status, $count, $pageno, $pagesrequired );
    $cdr_db = &cdr_connect_db( $config, @output );
    return gettext("Cannot list errors until database is configured!") . "\n" unless $cdr_db;
    if ( $params->{action} eq "Deactivate..." || $params->{uniqueid} ) {
        $cdr_db->do("UPDATE " . $config->{cdr_table} 
        . " SET cost = 'dropped' AND vendor = 'dropped' WHERE uniqueid = "
        . $cdr_db->quote($params->{uniqueid}) );
    }
    return 1;
}


sub default_callback {

    my (%stuff) = @_;

    foreach ( keys %stuff ) {

        $ASTPP->debug(
            user  => $params->{username},
            debug => "$_: " . $stuff{$_}
        );
    }

}




sub build_edit_account() {
    my ( $valid, $body, $tmp, $sql, $status, $number, @accountlist,@pricelists );
    if ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            my $accountinfo = &get_account( $astpp_db, $params->{item} );
            if ( $accountinfo->{reseller} eq $params->{username} ) {
                $valid = 1;
                $params->{reseller} = $params->{username};
            }
        }
        else 
        {
            $valid = 1;
        }


        if ( $valid == 1 ) {

           $tmp ="UPDATE accounts SET "
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
              . $astpp_db->quote( $params->{accountpassword} ) . ","
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
              . $astpp_db->quote( $params->{fascimile} ) . ","
              . "email = "
              . $astpp_db->quote( $params->{email} ) . ","
              . "language = "
              . $astpp_db->quote( $params->{language} ) . ","
              . "maxchannels = "
              . $astpp_db->quote( $params->{maxchannels} ) . ","
              . "dialed_modify = "
              . $astpp_db->quote( $params->{dialed_modify} ) . ","                      
	      . "status = "
              . $astpp_db->quote( $params->{status} ) . ","
              . "tz = "
              . $astpp_db->quote( $params->{timezone} ) . ","
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
        else
        {

            $status .=
                gettext("Account") . " "
              . $params->{item} . " "
              . gettext("Failed To Update!") . "\n";
            print "$tmp failed";
        }

       if ( $params->{type} == 1 ) {
            my $tmp =
                "UPDATE resellers SET posttoexternal = "
              . $astpp_db->quote( $params->{posttoexternal} )
              . " WHERE name ="
              . $astpp_db->quote( $params->{item} );

            if ( $astpp_db->do($tmp) ) {
                $status .= gettext("Reseller Updated Successfully!");
            }
            else 
            {
                $status .= gettext("Reseller Update Failed!");
            }
        }      
    }
    return 1;
}


sub build_periodic_charges() {
    my (@pricelists,  $status, $body,$number,      $inuse,  $accountstat, $accountinfo, $pageno, $pagesrequired);
    my $no       = gettext("NO");
    my $yes      = gettext("YES");
    my $active   = gettext("ACTIVE");
    my $inactive = gettext("INACTIVE");
    my $deleted  = gettext("DELETED");
    return gettext("Database is NOT configured!") . "\n"
    unless $astpp_db;
    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my $sql ="INSERT INTO charges (pricelist,description,charge,sweep,status) VALUES ("
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . $astpp_db->quote( $params->{desc} ) . ", "
          . $astpp_db->quote( $params->{charge} ) . ", "
          . $astpp_db->quote( $params->{sweep} ) . ", 1)";

        $ASTPP->debug( user => $params->{username}, debug => $sql );
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Added!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Periodic Charge Creation Failed!");
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {
        my $sql ="UPDATE charges SET ". "pricelist = ". $astpp_db->quote( $params->{pricelist} ) . ", "
          . "description = ". $astpp_db->quote( $params->{desc} ) . ", "
          . "charge = " . $astpp_db->quote( $params->{charge} ) . ", "
          . "sweep = ". $astpp_db->quote( $params->{sweep} )
          . ", status = '1' WHERE id = ". $astpp_db->quote( $params->{id} );

        $ASTPP->debug( user => $params->{username}, debug => $sql );
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Updated!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Periodic Charge Update Failed!");
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Delete...") ) {
        my $sql = "DELETE FROM charges WHERE id = ". $astpp_db->quote( $params->{id} );
        $ASTPP->debug( user => $params->{username}, debug => $sql );
        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Deleted!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Periodic Charge Delete Failed!");
        }
        $sql = "DELETE FROM charge_to_account WHERE charge_id = ". $astpp_db->quote( $params->{id} );
        $ASTPP->debug( user => $params->{username}, debug => $sql );

        if ( $astpp_db->do($sql) ) {
            $status .= gettext("Periodic Charge Deleted!");
        }
        else 
        {
            print "$sql failed";
            $status .= gettext("Periodic Charge Delete Failed!");
        }
    }
    return $status;
}


sub build_import_outbound_routes() {
    my ( $body, $status );
    return gettext("Cannot import outbound routes until database is configured!")unless $astpp_db;
    if (defined($params->{action}) && $params->{action} eq gettext("Import...") ) 
    {
        my $csv     = Text::CSV->new();
        my $prepend = "^";
        my $append  = ".*";
        my $uploaded = upload('rateimport');
        my ( @data, $record );
        while ( my $record = <$uploaded> ) 
        {
            $ASTPP->debug( user => $params->{username}, debug => $record );
            chomp;            
            push @data, $record;
        }
		
        foreach my $temp (@data) 
        {
            if ( $csv->parse($temp) ) 
            {
                my @columns = $csv->fields();
                my $pattern = $prepend . $columns[0] . $columns[2] . $columns[3] . $append;
                if ($astpp_db->do("DELETE FROM outbound_routes WHERE pattern = ". $astpp_db->quote($pattern)
                          . "AND trunk = "
                          . $astpp_db->quote( $columns[9] )))
                {
                    $status .= gettext("Dropped route") . " '" . $pattern . "'. <br>";
                }
                else 
                {
                    $status .= gettext("Unable to drop route") . " '" 
                      . $pattern
                      . "'.<br>";
                }
                
                if ( !$columns[10] || $columns[10] eq "" ) 
                {
                    $columns[10] = 0;
                }

                my $tmp = "INSERT INTO outbound_routes (pattern,comment,connectcost,includedseconds,"
                  . "cost,inc,trunk,prepend,status,precedence) VALUES ("
                  . $astpp_db->quote($pattern) . ","
                  . $astpp_db->quote( $columns[4] ) . ","
                  . $astpp_db->quote( $columns[5] * 1 ) . ","
                  . $astpp_db->quote( $columns[6] ) . ","
                  . $astpp_db->quote( $columns[7] * 1 ) . ","
                  . $astpp_db->quote( $columns[8] ) . ","
                  . $astpp_db->quote( $columns[9] ) . ","
                  . $astpp_db->quote( $columns[1] ) . ", 1,"
                  . $astpp_db->quote( $columns[10] ) . ")";

                $ASTPP->debug( user => $params->{username}, debug => $tmp );
                if ( $astpp_db->do($tmp) ) 
                {
                    $status .= gettext("Pattern: ") . " '" 
                      . $pattern . "' "
                      . gettext("has been created.") . "<br>";
                }
                else 
                {
                    $status .= gettext("Pattern: ") . " '" 
                      . $pattern . "' "
                      . gettext("FAILED to create")
                      . " ($tmp)!<br>";
                }
            }
            else 
            {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }
    
    return $status;
}

sub build_import_routes() {
    my ( $body, $status, $reseller );
    return gettext("Cannot import routes until database is configured!") unless $astpp_db;

    my ($uploaded);
    if ( param('action') eq gettext("Import...") ) {    
    	      
        my $csv     = Text::CSV->new();
        my $prepend = "^";
        my $append  = ".*";       
        $uploaded = upload('rateimport');
        my ( @data, $record );
        while ( my $record = <$uploaded> ) {
            $ASTPP->debug( user => $params->{username}, debug => $record );
            chomp;           
            push @data, $record;
        }
        
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            $reseller = $params->{username};
        }
        else 
        {
            $reseller = "";
        }

        foreach my $temp (@data) {
            if ( $csv->parse($temp) ) 
            {
                my ($tmp,$valid);
                my @columns = $csv->fields();
                my $pattern = $prepend . $columns[0] . $columns[1] . $columns[2] . $append;
                $status .= "$pattern $columns[3] $columns[6] $columns[4] $columns[5] $columns[7]<br>";
                if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
                {
                    my $pricelistdata = &get_pricelist( $astpp_db, $columns[7] );
                    if ( $pricelistdata->{reseller} eq $params->{username} ) 
                    {
                        $valid = 1;
                    }
                    
                    $reseller = $params->{username};
                    my $resellerdata = &get_account( $astpp_db, $reseller );

                    my $tmp = "SELECT * FROM routes WHERE pattern = "
                      . $astpp_db->quote( $params->{pattern} )
                      . " AND pricelist = "
                      . $astpp_db->quote( $resellerdata->{pricelist} )
                      . " LIMIT 1";

                    $ASTPP->debug( user => $params->{username}, debug => $tmp );
                    my $sql = $astpp_db->prepare($tmp);
		    		my ($routeinfo,$row);
                    $sql->execute;

                    while ( $row = $sql->fetchrow_hashref ) 
                    {
                        $routeinfo = $row;
                    }

                    $sql->finish;

                    if ($routeinfo->{cost}&& $routeinfo->{cost} > $columns[6] * 1 )
                    {
                        $status .= gettext("Pattern: ") 
                          . $pattern
                          . gettext(" belonging to pricelist: ")
                          . $columns[7]
                          . gettext(" has been adjusted from ")
                          . $columns[6] * 1
                          . gettext(" to ")
                          . $routeinfo->{cost};
                        
                        $columns[6] = $routeinfo->{cost} / 1;
                    }
                }
                else 
                {
                    $valid = 1;
                }
                if ( $valid == 1 ) 
                {
                    $tmp = "DELETE FROM routes WHERE pattern = "
                      . $astpp_db->quote($pattern)
                      . "AND pricelist = "
                      . $astpp_db->quote( $columns[7] );
                }
                else 
                {
                    $tmp = "";
                }
                
                if ( $astpp_db->do($tmp) ) 
                {
                    $status .= gettext("Dropped route") . " '" . $pattern . "'.<br>";
                }
                else 
                {
                    $status .= gettext("Unable to drop route") . " '" 
                      . $pattern . "'.("
                      . $tmp . ")<br>";
                }

                if ( $valid == 1 ) 
                {

                    if ( !$columns[9] || $columns[9] eq "" ) 
                    {
                        $columns[9] = 0;
                    }

                    $tmp = "INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost,inc,precedence,reseller,status) VALUES ("
                      . $astpp_db->quote($pattern) . ","
                      . $astpp_db->quote( $columns[3] ) . ","
                      . $astpp_db->quote( $columns[7] ) . ","
                      . $astpp_db->quote( $columns[4] * 1 ) . ","
                      . $astpp_db->quote( $columns[5] ) . ","
                      . $astpp_db->quote( $columns[6] * 1 ) . ","
                      . $astpp_db->quote( $columns[8] ) . ", "
                      . $astpp_db->quote( $columns[9] ) . ", "
                      . $astpp_db->quote($reseller) . ", 1)";
                }
                else 
                {
                    $tmp = "";
                }
                
                if ( $astpp_db->do($tmp) ) 
                {
                    $status .= gettext("Pattern") . " '" 
                      . $pattern . "' "
                      . gettext("has been created.") . "<br>";
                }
                else 
                {
                    $status .= gettext("Pattern") . " '" 
                    .$pattern . "' "
                    .gettext("FAILED to create")
                    ." ($tmp)!<br>";
                }
            }
            else 
            {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }
       
    return $status;
}

sub build_import_dids() {
    my ( $body, $status );
    return gettext("Cannot import dids until database is configured!")unless $astpp_db;
    if ( defined($params->{action}) && $params->{action} eq gettext("Import...") ) {
        my $csv = Text::CSV->new();
        my $uploaded = upload('didimport');
        my ( @data, $record );

        while ( my $record = <$uploaded> ) 
        {
            chomp;
            push @data, $record;
        }

        foreach my $temp (@data) 
        {
            if ( $csv->parse($temp) ) 
            {
                my @columns = $csv->fields();
                if ($astpp_db->do("DELETE FROM dids WHERE number = "
                          . $astpp_db->quote( $columns[0] )))
                {
                    $status .= gettext("Dropped DID: ") . " '" . $columns[0] . "'. <br>";
                }
                else 
                {
                    $status .= gettext("Unable to drop DID: ") . " '"
                      . $columns[0]
                      . "'.<br>";
                }
                
                my $tmp = "INSERT INTO dids"
                  . "(number,account,connectcost,includedseconds,"
                  . "monthlycost,cost,inc,extensions,status,provider,country,province,"
                  . "city,prorate,allocation_bill_status,maxchannels,limittime) "
                  . " VALUES ("
                  . $astpp_db->quote( $columns[0] ) . ","
                  . $astpp_db->quote( $columns[1] ) . ","
                  . $astpp_db->quote( $columns[2] * 1 ) . ","
                  . $astpp_db->quote( $columns[3] ) . ","
                  . $astpp_db->quote( $columns[4] * 1 ) . ","
                  . $astpp_db->quote( $columns[5] * 1 ) . ","
                  . $astpp_db->quote( $columns[16] ) . ","
                  . $astpp_db->quote( $columns[6] ) . ","
                  . $astpp_db->quote( $columns[7] ) . ","
                  . $astpp_db->quote( $columns[8] ) . ","
                  . $astpp_db->quote( $columns[9] ) . ","
                  . $astpp_db->quote( $columns[10] ) . ","
                  . $astpp_db->quote( $columns[11] ) . ","
                  . $astpp_db->quote( $columns[12] ) . ","
                  . $astpp_db->quote( $columns[13] ) . ","
                  . $astpp_db->quote( $columns[14] ) . ","
                  . $astpp_db->quote( $columns[15] ) . ")";
                  
                $ASTPP->debug( user => $params->{username}, debug => $tmp );
                
                if ( $astpp_db->do($tmp) ) 
                {
                    $status .= gettext("DID: ") . " '"
                      . $columns[0] . "' "
                      . gettext("has been created.") . "<br>";
                }
                else 
                {
                    $status .= gettext("DID: ") . " '"
                      . $columns[0] . "' "
                      . gettext("FAILED to create")
                      . " ($tmp)!<br>";
                }

                $tmp = "DELETE FROM routes WHERE pattern = "
                  . $astpp_db->quote( "^" . $params->{number} . "\$" )
                  . " AND pricelist = "
                  . $astpp_db->quote( $config->{default_brand} );
                  
                if ($astpp_db->do($tmp) ) 
                {                
                	$status .= gettext("The old pattern for") . " '"
                      . $params->{number} . "' "
                      . gettext("has been removed.");
                }
                else 
                {
                    $status .= gettext("The old pattern for") . " '"
                      . $params->{number} . "' "
                      . gettext("FAILED to remove!");

                    $ASTPP->debug( user => $params->{username}, debug => $tmp );
                }

                $tmp = "INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost) VALUES ("
                  . $astpp_db->quote( "^" . $params->{number} . "\$" ) . ","
                  . $astpp_db->quote( $params->{country} . ","
                  . $params->{province} . ","
                  . $params->{city} ). ","
                  . $astpp_db->quote( $config->{default_brand} ) . ","
                  . $astpp_db->quote( $params->{connectcost} ) . ","
                  . $astpp_db->quote( $params->{included} ) . ","
                  . $astpp_db->quote( $params->{cost} ) . ")";

                if ( $astpp_db->do($tmp) ) 
                {
                    $status .= gettext("Pattern") . " '". $params->{number} . "' ". gettext("has been created.");
                }
                else 
                {
                    $status .= gettext("Pattern") . " '"
                      . $params->{number} . "' "
                      . gettext("Failed to create.");
                }
            }
            else 
            {
                my $error = $csv->error_input;
                $status .= "pars() failed on argument: " . $error . "<br>";
            }
        }
    }

    return $status;
}


sub build_remove_account() {

    my ( $reseller, $body, $tmp, $sql, $status, $number, @accountlist, @pricelists, $accountinfo );

    if ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) {

        if ( $params->{number} ne "" ) {

            $number = $params->{number};

        }

        else {

            $number = $params->{accountlist};

        }

        $accountinfo = &get_account( $astpp_db, $number );

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {

            $tmp ="UPDATE accounts SET "
              . "status = 2 WHERE number = "
              . $astpp_db->quote($number)
              . " AND reseller = "
              . $astpp_db->quote( $params->{username} );
        }
        else {
            $tmp = "UPDATE accounts SET "
              . "status = 2 WHERE number ="
              . $astpp_db->quote($number);
        }

        if ( $astpp_db->do($tmp) ) {

            $status .=
                gettext("Account") . " " 
              . $number . " "
              . gettext("Successfully $accountinfo->{number} Deactivated $accountinfo->{type}!") . "\n";

            if ( $accountinfo->{type} == 1 ) {

                my $tmp = "UPDATE resellers SET status = 2 WHERE name = "
                  . $astpp_db->quote($number);
                $astpp_db->do($tmp);

            }

        }

        else {

            $status .=
                gettext("Account") . " " 
              . $number . " "
              . gettext("Failed To Deactivate!") . "\n";
            print "$tmp failed";
        }

    }
   
    return 1;

}


sub build_process_payment() {

    my ( $status, $body, $number, $reseller );

#     my $template = HTML::Template->new(
#         filename => '/var/lib/astpp/templates/account-process-payment.tpl',
#         die_on_bad_params => $config->{template_die_on_bad_params}
#     );


    return gettext("Database not configured!") unless $astpp_db;
    if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
        $reseller = $params->{username};
    }
    else {
        $reseller = "";
    }

    my @accountlist = &list_accounts_selective( $astpp_db, $reseller, "-1" );
    unshift( @accountlist, "" );

    if ( defined($params->{action}) && $params->{action} eq gettext("Refill...") ) {
        if ( param('number') ne "" ) {
            $number = param('number');
        }
        else {
            $number = param('accountlist');
        }

        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            $accountinfo = &get_account( $astpp_db, $number );
            if ( $accountinfo->{reseller} eq $params->{username} ) {
                $status .=
                  &refill_account( $astpp_db, $number,
                    $params->{refilldollars});
            }
        }
        else {
            $status .=
             &refill_account( $astpp_db, $number,
                $params->{refilldollars} );
        }
    }

    my $accountmenu = popup_menu(
        -name   => 'accountlist',
        -values => \@accountlist,
    );

    $template->param( accountlist => $accountmenu );
    $template->param( currency    => $config->{currency} );
    $template->param( status      => $status );
    return $template->output;
}


sub build_pricelists() {
    my ( $sql, $record, $valid, $count, $tmp, $pagesrequired, $pageno );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my $pricelistinfo = &get_pricelist( $astpp_db, $params->{name} );
        if ( $pricelistinfo->{name} ) {
            if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
                if ( $pricelistinfo->{reseller} eq $params->{username} ) 
                {
                    $valid = 1;
                }
                else 
                {
                    $valid = 0;
                }
            }
            else 
            {
                $valid = 1;
            }

            if ( $valid == 1 ) 
            {
                my $tmp = "UPDATE pricelists SET status = 1 WHERE name = "
                  . $astpp_db->quote( $params->{name} );

                if ( $astpp_db->do($tmp) ) 
                {
                    $status .=  gettext("Pricelist: "). $params->{name}
                      . gettext(" Reactivated Successfully!");
                }
                else 
                {
                    $status .= gettext("Pricelist: ")
                      . $params->{name}
                      . gettext(" Failed to Reactivate!");
                    $ASTPP->debug( user => $params->{username}, debug => $tmp );
                }
            }
            else 
            {
                $status .= gettext("You do not own this pricelist");
            }
        }
        else 
        {
            if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
            {
                $params->{reseller} = $params->{username};
            }
            
            if ( $params->{reseller} ) 
            {
                $tmp = "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
                  . $astpp_db->quote( $params->{name} ) . ", "
                  . $astpp_db->quote( $params->{inc} ) . ", "
                  . $astpp_db->quote( $params->{markup} ) . ", 1, "
                  . $astpp_db->quote( $params->{reseller} ) . ")";
            }
            else 
            {
                $tmp = "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
                  . $astpp_db->quote( $params->{name} ) . ", "
                  . $astpp_db->quote( $params->{inc} ) . ", "
                  . $astpp_db->quote( $params->{markup} )
                  . ", 1, NULL)";
            }

            if ( $astpp_db->do($tmp) ) {
                $status .= gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Added Successfully!");
            }
            else 
            {
                $status .=gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Failed to Add!");
                $ASTPP->debug( user => $params->{username}, debug => $tmp );
            }
        }        
    }
    elsif(defined($params->{action}) && $params->{action} eq gettext("Save...") ) 
    {
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            my $pricelistinfo = &get_pricelist( $astpp_db, $params->{oldname} );
            if ( $pricelistinfo->{reseller} eq $params->{username} ) {
                $valid = 1;
                $params->{reseller} = $params->{username};
            }
            else 
            {
                $valid = 0;
            }
        }
        else 
        {
            $valid = 1;
        }
        
        if ( $valid == 1 ) 
        {
            $tmp = "UPDATE pricelists SET name = "
              . $astpp_db->quote( $params->{name} ) . ", "
              . " inc = "
              . $astpp_db->quote( $params->{inc} ) . ", "
              . " markup = "
              . $astpp_db->quote( $params->{markup} )    #. ", "
              . " WHERE name = " . $astpp_db->quote( $params->{oldname} );
            if ( $astpp_db->do($tmp) ) 
            {
                $status .= gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Updated Successfully!");
            }
            else 
            {
                $status .= gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Failed to Update!");
                $ASTPP->debug( user => $params->{username}, debug => $tmp );
            }
        }
        else 
        {
            $status .= gettext("Pricelist: ")
              . $params->{name}
              . gettext(" Does Not Belong to You!");
        }        
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) 
    {	
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            my $pricelistinfo = &get_pricelist( $astpp_db, $params->{name} );
            if ( $pricelistinfo->{reseller} eq $params->{username} ) 
            {
                $valid = 1;
            }
            else 
            {
                $valid = 0;
            }
        }
        else 
        {
            $valid = 1;
        }
        
        if ( $valid == 1 ) {
            my $tmp = "UPDATE pricelists SET status = 2 WHERE name = "
              . $astpp_db->quote( $params->{name} );
            if ( $astpp_db->do($tmp) ) 
            {
                $status .= gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Deactivated Successfully!");
            }
            else 
            {
                $status .= gettext("Pricelist: ")
                  . $params->{name}
                  . gettext(" Failed to Deactivate!");
                $ASTPP->debug( user => $params->{username}, debug => $tmp );
            }
        }       
    }
    return $status;
}



sub build_dids() {

    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired, @accountlist,@providerlist );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
	if($params->{action} eq gettext("Insert...") && $params->{number} ne "" )
    {
        if ( !$params->{setup} ) {
            $params->{setup} = 0;
        }
        if ( !$params->{prorate} ) {
            $params->{prorate} = 0;
        }
        $params->{extension} =~ s/###/%/; 
        $tmp = "INSERT INTO dids (number,account,monthlycost,connectcost,includedseconds,cost,inc,extensions,provider,country,city,province,status,setup,prorate,disconnectionfee,limittime,maxchannels,chargeonallocation,variables,dial_as) VALUES ("
          . $astpp_db->quote( $params->{number} ) . ","
          . $astpp_db->quote( $params->{account} ) . ","
          . $astpp_db->quote( $params->{monthlycost} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote( $params->{extension} ) . ","
          . $astpp_db->quote( $params->{provider} ) . ","
          . $astpp_db->quote( $params->{country} ) . ","
          . $astpp_db->quote( $params->{city} ) . ","
          . $astpp_db->quote( $params->{province} ) . ", 1,"
          . $astpp_db->quote( $params->{setup} ) . ","
          . $astpp_db->quote( $params->{prorate} ) . ","
          . $astpp_db->quote( $params->{disconnectionfee} ) . ","
          . $astpp_db->quote( $params->{limittime} ) . ","
          . $astpp_db->quote( $params->{maxchannels} ) . ","
          . $astpp_db->quote( $params->{chargeonallocation} ) . ","
          . $astpp_db->quote( $params->{variables} ) . ","
          . $astpp_db->quote( $params->{dial_as} ) . ")";
        
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("DID.") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }

        else 
        {

            $status .= gettext("DID") . " '".$params->{number} . "' "
              . gettext("FAILED to create!");
            $ASTPP->debug( user => $params->{username}, debug => $tmp );

        }

        $tmp = "DELETE FROM routes WHERE pattern = "
          . $astpp_db->quote( "^" . $params->{number} . "\$" )
          . " AND pricelist = "
          . $astpp_db->quote( $config->{default_brand} );
         

        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("The old pattern for") . " '"
              . $params->{number} . "' "
              . gettext("has been removed.");
        }
        else 
        {
            $status .= gettext("The old pattern for") . " '"
              . $params->{number} . "' "
             . gettext("FAILED to remove!");
            $ASTPP->debug( user => $params->{username}, debug => $tmp );
        }


        $tmp = "INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost,inc,reseller) VALUES ("
          . $astpp_db->quote( "^" . $params->{number} . "\$" ) . ","
          . $astpp_db->quote("DID: " . $params->{country} . ","
          . $params->{province} . ","
          . $params->{city} ). ","
          . $astpp_db->quote( $config->{default_brand} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ",'')";
        $ASTPP->debug( user => $params->{username}, debug => $tmp );

        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else 
	     {
            $status .=gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("FAILED to create!");
            $ASTPP->debug( user => $params->{username}, debug => $tmp );
        }

        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {
      
	$params->{extension} =~ s/###/%/; 
        $tmp = "UPDATE dids SET". " account="
          . $astpp_db->quote( $params->{account} ) . ","
          . " monthlycost="
          . $astpp_db->quote( $params->{monthlycost} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{included} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . "," 
          . " inc="
          . $astpp_db->quote( $params->{inc} ) . ","
          . " extensions="
          . $astpp_db->quote( $params->{extension} ) . ","
          . " provider="
          . $astpp_db->quote( $params->{provider} ) . ","
          . " country="
          . $astpp_db->quote( $params->{country} ) . ","
          . " city="
          . $astpp_db->quote( $params->{city} ) . ","
          . " prorate="
          . $astpp_db->quote( $params->{prorate} ) . ","
          . " limittime="
          . $astpp_db->quote( $params->{limittime} ) . ","
          . " setup="
          . $astpp_db->quote( $params->{setup} ) . ","
          . " disconnectionfee = "
          . $astpp_db->quote( $params->{disconnectionfee} ) . ","
          . " province = "
          . $astpp_db->quote( $params->{province} ) . ","
          . " variables = "
          . $astpp_db->quote( $params->{variables} ) . ","
          . " maxchannels = "
          . $astpp_db->quote( $params->{maxchannels} ) . ","
          . " chargeonallocation = "
          . $astpp_db->quote( $params->{chargeonallocation} ) . ","
          . " dial_as = "
          . $astpp_db->quote( $params->{dial_as} ) . ","
          . " status=1 WHERE number="
          . $astpp_db->quote( $params->{number} );

        if ( $astpp_db->do($tmp) ) {
            $status .=
               gettext("DID") . " '"
              . $params->{number} . "' "
              . gettext("has been updated.");
        }
        else 
        {
            $status .= gettext("DID") . " '". $params->{number} . "' ". gettext("FAILED to update!");
            $ASTPP->debug( user => $params->{username}, debug => $tmp );
        }

        $tmp = "UPDATE routes SET". " comment = "
          . $astpp_db->quote("DID: " . $params->{country} . ","
          . $params->{province} . ","
          . $params->{city} ) . ","
          . " pricelist="
          . $astpp_db->quote( $config->{default_brand} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{included} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . ","
          . " inc="
          . $astpp_db->quote( $params->{inc} ) . ","
	  	  . " status=1"
          . " WHERE pattern = "
          . $astpp_db->quote( "^" . $params->{number} . "\$" );
          
        $ASTPP->debug( user => $param->{username}, debug => $tmp );


        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Pattern") . " '"
              . $params->{number} . "' "
              . gettext("has been created.");
        }
        else 
        {
            $status .= gettext("Pattern") . " '". $params->{number} . "' ". gettext("FAILED to create!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) {

        my $tmp = "DELETE FROM dids WHERE number = ". $astpp_db->quote( $params->{number} );

        if ( $astpp_db->do($tmp) ) {

            $status .= gettext("DID: "). $params->{number}. gettext(" Deactivated Successfully!");
        }
        else 
        {
            $status .= gettext("DID: ") . $params->{number} . gettext(" Failed to Deactivate!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
        $params->{action} = gettext("Information...");
    }

}


sub build_dids_user() {
	
    my ( $sql, $record, $body, $status, $count, $tmp, $pageno, $pagesrequired, @accountlist,@providerlist );
	
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
	if ( $params->{action} eq gettext("Purchase DID") ) {
		$status .= &purchase_did($astpp_db,$config,$params->{did_list},$params->{username});
	}
	elsif ($params->{action} eq gettext("Remove...")) {
		$status .= &remove_did($astpp_db,$config,$params->{did},$params->{username});        
	}
	elsif($params->{action} eq gettext("Edit..."))
	{	
	    $astpp_db->do( "UPDATE dids SET extensions=".$astpp_db->quote( $params->{extension})." where number=".$astpp_db->quote($params->{did}));
	}

		
	return $status;

}

sub build_ani_map() {
	
	 my ( $sql, $record, $status, $count, $tmp, $pageno, $pagesrequired, @accountlist,@providerlist );
	
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
	
	return $status;
}


######################## Reseller DID Support ######################################

sub build_dids_reseller() {

	   my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired, @accountlist,

        @providerlist );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }

    my $results_per_page = $config->{results_per_page};

    if ( $results_per_page eq "" ) { $results_per_page = 25; }

    if ( !$params->{action} ) {

        $params->{action} = gettext("Information...");

    }

    if ( defined($params->{action}) && $params->{action} eq gettext("Edit...") ) {

        my ( $didinfo, $reseller_didinfo, $accountinfo );

        $reseller_didinfo =
          &get_did_reseller( $astpp_db, $params->{number},

            $params->{username} );

        $accountinfo = &get_account( $astpp_db, $params->{username} );

        if ( $accountinfo->{reseller} ne "" ) {

            $didinfo =
              &get_did_reseller( $astpp_db, $params->{number},
                $accountinfo->{reseller} );
        }
        else {
            $didinfo = &get_did( $astpp_db, $params->{number} );
        }

       $body = start_form;
       $body .=
            "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>"
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
          . gettext("Setup Fee")
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged when the DID is disconnected from a customers account")
          . "\">"
          . gettext("Disconnect Fee")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged monthly") . "\">"
          . gettext("Monthly")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("Do we prorate the monthly fee on setup?  If set to 'NO' we charge the full amount even if the first month is partial.")
          . "\">"
          . gettext("Prorate")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"")
          . "\">"
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
          . gettext("Increment")
          . "</td><td>"
          . gettext("Dial As")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr><td>"
          . $didinfo->{number}
          . "</td><td>"
          . $didinfo->{country}
          . "</td><td>"
          . $didinfo->{province}
          . "</td><td>"
          . $didinfo->{city}
          . "</td><td>"
          . $didinfo->{provider}
          . "</td><td>"
          . $didinfo->{account}
          . "</td><td>"
          . $didinfo->{extensions}
          . "</td><td>"
          . $didinfo->{setup}
          . "</td><td>"
          . $didinfo->{disconnectionfee}
          . "</td><td>"
          . $didinfo->{monthlycost}
          . "</td><td>"
          . $yesno{ $didinfo->{prorate} }
          . "</td><td>"
          . $didinfo->{connectcost}
          . "</td><td>"
          . $didinfo->{includedseconds}
          . "</td><td>"
          . $didinfo->{cost}
          . "</td><td>"
          . $didinfo->{increment}
          . "</td><td>"
          . $didinfo->{dial_as}
          . "</td><td></td></tr>"
          . "<tr><td></td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . textfield(
            -name    => 'extension',
            -size    => 20,
            -default => $reseller_didinfo->{extensions}
          )
          . "</td><td>"
          . textfield(
            -name    => 'setup',
            -size    => 20,
            -default => $reseller_didinfo->{setup}
          )
          . "</td><td>"
          . textfield(
            -name    => 'disconnectionfee',
            -size    => 20,
            -default => $reseller_didinfo->{disconnectionfee}
          )
          . "</td><td>"
          . textfield(
            -name    => 'monthlycost',
            -size    => 20,
            -default => $reseller_didinfo->{monthlycost}
          )
          . "</td><td>"
          . popup_menu(
            -name    => 'prorate',
            -values  => \%yesno,
            -default => $reseller_didinfo->{prorate}
          )
          . "</td><td>"
          . textfield(
            -name    => 'connectcost',
            -size    => 20,
            -default => $reseller_didinfo->{connectcost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'included',
            -size    => 20,
            -default => $reseller_didinfo->{includedseconds}
          )
         . "</td><td>"
          . textfield(
            -name    => 'cost',
            -size    => 20,
            -default => $reseller_didinfo->{cost}
          )
          . "</td><td>"
          . textfield(
            -name    => 'inc',
            -size    => 3,
            -default => $reseller_didinfo->{inc}
          )
          . "</td><td>"
          . textfield(
            -name    => 'dial_as',
            -size    => 20,
            -default => $reseller_didinfo->{dial_as}
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Save...") )
          . "</td></tr></table>";
        return $body;
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {

        $tmp =
            "DELETE FROM reseller_pricing WHERE reseller = "
          . $astpp_db->quote( $params->{username} )
          . " AND type = '1' AND note = "
          . $astpp_db->quote( $params->{number} );

        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        $astpp_db->do($tmp);

        $tmp =
"INSERT INTO reseller_pricing (reseller,type,note,monthlycost,prorate,setup,cost,inc,disconnectionfee,connectcost,includedseconds,status) VALUES ("
          . $astpp_db->quote( $params->{username} )
          . ", '1', "

          . $astpp_db->quote( $params->{number} ) . ","
          . $astpp_db->quote( $params->{monthlycost} ) . ", "
          . $astpp_db->quote( $params->{prorate} ) . ","
          . $astpp_db->quote( $params->{setup} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote( $params->{disconnectionfee} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} )
          . ", '1')";

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

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
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }

	$astpp_db->do( "UPDATE dids SET dial_as = ". $astpp_db->quote( $params->{dial_as} ) . ",extensions="
	    . $astpp_db->quote( $params->{extension})." where number=". $params->{number} );

        $tmp =
            "DELETE FROM routes WHERE pattern = "
          . $astpp_db->quote( "^" . $params->{number} . "\$" )
          . " AND pricelist = "
          . $astpp_db->quote( $params->{username} );

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

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
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }

        $tmp =
"INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost) VALUES ("
          . $astpp_db->quote( "^" . $params->{number} . "\$" ) . ","
          . $astpp_db->quote( $params->{country} . ","
              . $params->{province} . ","
              . $params->{city} ). ","
          . $astpp_db->quote( $params->{username} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{included} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ")";

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

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

            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }

        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) {
        my $tmp =
            "DELETE FROM reseller_pricing WHERE note = "
          . $astpp_db->quote( $params->{number} )
          . " AND type = '1' AND reseller = "
          . $astpp_db->quote( $params->{username} );

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

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
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Add...") ) {
	my ($didinfo);
        my $resellerinfo = &get_account( $astpp_db, $params->{username} );

        if ( $resellerinfo->{reseller} ) {

            $didinfo =
              &get_did_reseller( $astpp_db, $params->{number},
                $resellerinfo->{reseller} );
        }
        else {
            $didinfo = &get_did( $astpp_db, $params->{number} );
        }

        $body =
            start_form
          . "<table class=\"default\">"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>"
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
          . gettext("Setup Fee")
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged monthly") . "\">"
          . gettext("Monthly")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged when the DID is disconnected from a customers account")
          . "\">"
          . gettext("Disconnect Fee")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"")
          . "\">"
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
          . gettext("Increment")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>"
          . "<tr><td>"
          . $didinfo->{number}
          . "</td><td>"
          . $didinfo->{country}
          . "</td><td>"
          . $didinfo->{province}
          . "</td><td>"
          . $didinfo->{city}
          . "</td><td>"
          . $didinfo->{provider}
          . "</td><td>"
          . $didinfo->{account}
          . "</td><td>"
          . $didinfo->{extensions}
          . "</td><td>"
          . $didinfo->{setup}
          . "</td><td>"
          . $didinfo->{monthlycost}
          . "</td><td>"
          . $didinfo->{disconnectionfee}
          . "</td><td>"
          . $didinfo->{connectcost}
          . "</td><td>"
          . $didinfo->{included}
          . "</td><td>"
          . $didinfo->{cost}
          . "</td><td>"
          . $didinfo->{inc}
          . "</td><td>"
          . $didinfo->{dial_as}
          . "</td><td></td></tr>"
          . "<tr><td>"
          . textfield(
            -name => 'number',
            -size => 20
          )
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . "</td><td>"
          . textfield(
            -name => 'extension',
            -size => 20
          )
          . "</td><td>"
         . textfield(
            -name => 'setup',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'monthlycost',
            -size => 20
          )
          . "</td><td>"
          . textfield(
            -name => 'disconnectionfee',
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
          . textfield(
            -name => 'inc',
            -size => 3
          )
          . "</td><td>"
          . textfield(
            -name => 'dial_as',
            -size => 20
          )
          . "</td><td>"
          . submit( -name => 'action', -value => gettext("Insert...") )
          . "</td></tr></table>";
        return $body;
    }
    if ( defined($params->{action}) && $params->{action} eq gettext("Information...") ) {
        $body =

            start_form
          . "<table class=\"default\">"
          . "<tr><td>"
          . submit( -name => 'action', -value => gettext("Add...") )
          . "</td></tr>"
          . "<tr class=\"header\"><td colspan=12>"
          . gettext("All costs are in 1/100 of a penny")
          . "</td></tr>"
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
          . gettext("Setup Fee")
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged when the DID is disconnected from a customers account")
          . "\">"
          . gettext("Disconnect Fee")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("This fee is charged monthly") . "\">"
          . gettext("Monthly")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("Do we prorate the monthly fee on setup?  If set to 'NO' we charge the full amount even if the first month is partial.")
          . "\">"
          . gettext("Prorate")
          . "</acronym>"
          . "</td><td>"
          . "<acronym title=\""
          . gettext("Connection Fee:  The connection fee is the price charged for the \"Included Seconds\"")
          . "\">"
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
          . gettext("Increment")
          . "</td><td>"
          . gettext("Dial As")
          . "</td><td>"
          . gettext("Action")
          . "</td></tr>";
		  
        $tmp = "SELECT number FROM dids WHERE status < 2";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";

        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );
        $sql->finish;

        $sql = $astpp_db->prepare(
"SELECT number FROM dids WHERE status < 2 ORDER BY number limit $params->{limit} , $results_per_page"
        );

        $sql->execute
          || return gettext("Something is wrong with the ASTPP database!")
          . "\n";

        $count = 0;
        while ( my $did = $sql->fetchrow_hashref ) {
            my ( $record, $didinfo );

            $didinfo = &get_did( $astpp_db, $did->{number} );

            my $success;

            if ( $didinfo->{account} ne "" ) {
                my $accountinfo =
                  &get_account( $astpp_db, $didinfo->{account} );
                if (   $accountinfo->{reseller} eq $params->{username}
                    || $didinfo->{account} eq $params->{username} )
                {
                    $record =
                      &get_did_reseller( $astpp_db, $did->{number},
                        $params->{username} );
                    $success = 1;
                }
            }
           else {
                $record =
                  &get_did_reseller( $astpp_db, $did->{number},
                    $params->{username} );
                $success = 1;
            }

           if ( $count % 2 == 0 ) {
                $body .= "<tr class=\"rowtwo\">";
            }
            else {
                $body .= "<tr class=\"rowone\">";
            }

            if ( $success == 1 ) {
                $count++;
                $body .=
                    "<td>$didinfo->{number}</td><td>$didinfo->{country}</td>"
                  . "<td>$didinfo->{province}</td><td>$didinfo->{city}</td>"
                  . "<td>$didinfo->{provider}</td><td>$record->{account}</td>"
                  . "<td>$record->{extensions}</td><td>$record->{setup}</td>"
                  . "<td>$record->{disconnectionfee}</td><td>$record->{monthlycost}</td>"
                  . "<td>"
                  . $yesno{ $record->{prorate} } . "</td>"
                  . "<td>$record->{connectcost}</td><td>$record->{includedseconds}</td>"
                  . "<td>$record->{cost}</td><td>$record->{inc}</td><td>$record->{dial_as}</td>"
                  . "<td><a href=\"astpp-admin.cgi?mode="
                  . gettext("Manage DIDs")
                  . "&action="
                  . gettext("Edit...")
                  . "&number="
                  . $did->{number} . "\">"
                  . "<img alt='Edit' src='../../_astpp/edit.gif'></a>"
                  . "  <a href=\"astpp-admin.cgi?mode="
                  . gettext("Manage DIDs")
                  . "&action="
                  . gettext("Deactivate...")
                  . "&number="
                  . $did->{number} . "\">"
                  . "<img alt='Edit' src='../../_astpp/delete.gif'></a>"
                  . "</td></tr>";
            }
        }

        $body .= "</table>";
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

    }
    return $body;
}


sub build_routes() {
    my ( $pageno, @pricelists, $sql, $count, $tmp );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    @pricelists = $ASTPP->list_pricelists( reseller => $params->{logged_in_reseller} );
    return gettext("Pricelists Do NOT Exist!") . "\n" unless @pricelists;

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my $reseller;
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            my $pricelistinfo = &get_pricelist( $astpp_db, $params->{pricelist} );
            if ( $pricelistinfo->{reseller} ne $params->{username} ) {
                $params->{pricelist} = $params->{username};
            }
            
            $reseller = $params->{username};
            my $resellerdata = &get_account( $astpp_db, $reseller );
            my $tmp = "SELECT * FROM routes WHERE pattern = "
              . $astpp_db->quote( $params->{pattern} )
              . " AND pricelist = "
              . $astpp_db->quote( $resellerdata->{pricelist} )
              . " LIMIT 1";
              
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
            my $sql = $astpp_db->prepare($tmp);
            $sql->execute;
	    	
	    	my ($row, $routeinfo);
            while ( $row = $sql->fetchrow_hashref ) {
                $routeinfo = $row;
            }
            $sql->finish;
            if ( $routeinfo->{cost} && $routeinfo->{cost} > $params->{cost} ) {
                $status .="<br><b>". gettext("Pattern: ")
                  . $params->{pattern}
                  . gettext(" belonging to pricelist: ")
                  . $params->{pricelist}
                  . gettext(" has been adjusted from ")
                  . $params->{cost}
                  . gettext(" to ")
                  . $routeinfo->{cost} . ".  "
                  . gettext("Please be sure to get the most recent version of your costs!"
                  ) . "</b><br>";
                $params->{cost} = $routeinfo->{cost};
            }
        }
        else {
            $reseller = "";
        }
        $tmp ="INSERT INTO routes (pattern,comment,pricelist,connectcost,includedseconds,cost,inc,reseller) VALUES ("
          . $astpp_db->quote( $params->{pattern} ) . ","
          . $astpp_db->quote( $params->{comment} ) . ","
          . $astpp_db->quote( $params->{pricelist} ) . ","
          . $astpp_db->quote( $params->{connectcharge} ) . ","
          . $astpp_db->quote( $params->{incseconds} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote($reseller) . ")";
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Pattern") . " '". $params->{pattern} . "' " . gettext("has been created.");
        }
        else 
        {
            $status .=gettext("Pattern") . " '". $params->{pattern} . "' ". gettext("FAILED to create!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }
    elsif( defined($params->{action}) && $params->{action} eq gettext("Save...") ) 
    {
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
        {
            my $pricelist = &get_pricelist( $astpp_db, $params->{pricelist} );
            if ( $pricelist->{reseller} ne $params->{username} ) {
                $params->{pricelist} = $params->{username};
            }

            my $reseller = $params->{username};
            my $resellerdata = &get_account( $astpp_db, $reseller );

            my $tmp ="SELECT * FROM routes WHERE pattern = "
              . $astpp_db->quote( $params->{pattern} )
              . " AND pricelist = "
              . $astpp_db->quote( $resellerdata->{pricelist} )
              . " LIMIT 1";
              
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
            my $sql = $astpp_db->prepare($tmp);
            $sql->execute;
	   		my ($routeinfo, $row);
            while ( $row = $sql->fetchrow_hashref ) {
                $routeinfo = $row;
            }
            $sql->finish;
            $ASTPP->debug(
                user  => $param->{username},
                debug => "Reseller Cost = $routeinfo->{cost}"
            );

            if ( $routeinfo->{cost} > $params->{cost} ) {
                $status .="<br><b>". gettext("Pattern: "). $params->{pattern}
                  . gettext(" belonging to pricelist: ")
                  . $params->{pricelist}
                  . gettext(" has been adjusted from ")
                  . $params->{cost}
                  . gettext(" to ")
                  . $routeinfo->{cost} . ".  "
                  . gettext("Please be sure to get the most recent version of your costs!") . "</b><br>";
                $params->{cost} = $routeinfo->{cost};
            }
        }
        $tmp = "UPDATE routes SET". " pattern=". $astpp_db->quote( $params->{pattern} ) . ","
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
            $status .=gettext("Pattern") . " '"
              . $params->{id} . "' "
              . gettext("has been saved.");
        }
        else 
        {
            $status .=gettext("Pattern") . " '". $params->{id} . "' ". gettext("FAILED to saved!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) 
    {
        if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) {
            $tmp ="UPDATE routes SET status = 2 WHERE id = "
              . $astpp_db->quote( $params->{id} )
              . " AND pricelist = "
              . $astpp_db->quote( $params->{username} );
        }
        else 
        {
            $tmp = "UPDATE routes SET status = 2 WHERE id = ". $astpp_db->quote( $params->{id} );
        }

        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Route: "). $params->{id}. gettext(" Deactivated Successfully!");
        }
        else 
        {
            $status .=gettext("Route: "). $params->{id}. gettext(" Failed to Deactivate!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }

    }

    return $status;
}





sub build_packages() {
    my ( $sql_select, $sql_count, $pagination, $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my $tmp ="INSERT INTO packages (name,pricelist,pattern,includedseconds,status) VALUES ("
          . $astpp_db->quote( $params->{name} ) . ", "
          . $astpp_db->quote( $params->{pricelist} ) . ", "
          . $astpp_db->quote( $params->{pattern} ) . ", "
          . $astpp_db->quote( $params->{includedseconds} ) . ",1)";

        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Package: "). $params->{name}. gettext(" Added Successfully!");
        }
        else 
        {
            $status .=gettext("Package: "). $params->{name}. gettext(" Failed to Add!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) 
    {
        my $tmp = "UPDATE packages SET name= ". $astpp_db->quote( $params->{name} )
          . ", pricelist = ". $astpp_db->quote( $params->{pricelist} )
          . ", pattern=". $astpp_db->quote( $params->{pattern} )
          . ", includedseconds=". $astpp_db->quote( $params->{includedseconds})
          . " WHERE id = ". $astpp_db->quote( $params->{id} );

        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) 
        {
            $status .=gettext("Package: "). $params->{name}. gettext(" Updated Successfully!");
        }
        else 
        {
            $status .=gettext("Package: "). $params->{name}. gettext(" Failed to Update!");
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) 
    {
        my $tmp = "UPDATE packages SET status = 2 WHERE id = ". $astpp_db->quote( $params->{id} );
        if ( $astpp_db->do($tmp) ) 
        {
            $status .= gettext("Package: ") . $params->{id}. gettext(" Deactivated Successfully!");
        }
        else 
        {
            $status .= gettext("Package: "). $params->{id}. gettext(" Failed to Deactivate!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }
    return $status;
}




sub build_trunks() 
{
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    my @providerlist = &list_providers($astpp_db);
    return gettext("No Providers Exist!") . "\n" unless @providerlist;

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) 
    {
        my @resellers    = &list_resellers($astpp_db);
        my $resellerlist = "";
        foreach my $reseller (@resellers) {
            my $resellerparam = "reseller-" . $reseller;
            print "RESELLER: $reseller PARAM = $params->{$resellerparam}"
            if $config->{debug} == 1;
            if ( $params->{$resellerparam} == 1 ) {
                $resellerlist .= "'" . $reseller . "',";
            }
        }
        my $tmp = "INSERT INTO trunks (name,tech,path,maxchannels,dialed_modify,precedence,resellers,provider) VALUES ("
          . $astpp_db->quote( $params->{name} ) . ", "
          . $astpp_db->quote( $params->{tech} ) . ", "
          . $astpp_db->quote( $params->{path} ) . ", "
          . $astpp_db->quote( $params->{maxchannels} ) . ", "
          . $astpp_db->quote( $params->{dialed_modify} ) . ", "
          . $astpp_db->quote( $params->{precedence} ) . ", "
          . $astpp_db->quote($resellerlist) . ", "
          . $astpp_db->quote( $params->{provider} ) . ")";
          
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        
        if ( $astpp_db->do($tmp) ) 
        {        	
            $status .= gettext("Trunk: "). $params->{name}. gettext(" Added Successfully!");
        }
        else 
        {
            $status .= gettext("Trunk: ") . $params->{name} . gettext(" Failed to Add!");
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) 
    {
        my ( $resellerlist, $count );
        $count = 0;
        my @resellers = &list_resellers($astpp_db);

        foreach my $reseller (@resellers) 
        {
            my $resellerparam = "reseller-" . $reseller;
            print "RESELLER: $reseller PARAM = $params->{$resellerparam}"
            if $config->{debug} == 1;

            if ( $params->{$resellerparam} == 1 ) 
            {
                $resellerlist .= "'" . $reseller . "',";
            }
        }

        my $tmp = "UPDATE trunks SET tech = ". $astpp_db->quote( $params->{tech} ) . ", "
          . " path = ". $astpp_db->quote( $params->{path} ) . ", "
          . " provider = ". $astpp_db->quote( $params->{provider} ) . ", "
          . " dialed_modify = ". $astpp_db->quote( $params->{dialed_modify} ) . ", "
          . " precedence = ". $astpp_db->quote( $params->{precedence} ) . ", "
          . " resellers = ". $astpp_db->quote($resellerlist) . ", "
          . " maxchannels = ". $astpp_db->quote( $params->{maxchannels} )
          . " WHERE name = ". $astpp_db->quote( $params->{name} );
          
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        
        if ( $astpp_db->do($tmp) ) 
        {
            $status .= gettext("Trunk: "). $params->{name}. gettext(" Updated Successfully!");
        }
        else 
        {
            $status .= gettext("Trunk: "). $params->{name}. gettext(" Failed to Update!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) 
    {
        my $tmp = "UPDATE trunks SET status = 2 WHERE name = ". $astpp_db->quote( $params->{name} );
        if ( $astpp_db->do($tmp) ) 
        {
            $status .= gettext("Trunk: "). $params->{name}. gettext(" Deactivated Successfully!");
        }
        else 
        {
            $status .= gettext("Trunk: "). $params->{name}. gettext(" Failed to Deactivate!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }

    return $status;
}



sub build_providers() {
    my ( $sql, $record, $count, $tmp, $pageno, $pagesrequired );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Generate Account") ) 
    {
        $params->{count}   = 1;
        $params->{pennies} = 0;
		# 	$params->{accounttype} = 3;
        $params->{number}  = $params->{customnum};
        $status .= &generate_accounts( $params, $config );
    }
    return $status;
}


sub build_outbound_routes() {
    my ( $sql, $record, $count, $tmp, $tot_count, $pageno, $pagesrequired,@trunklist );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
        my @resellers = &list_resellers($astpp_db);
        my $resellerlist;
        foreach my $reseller (@resellers) {
            my $resellerparam = "reseller-" . $reseller;
            print "RESELLER: $reseller PARAM = $params->{$resellerparam}"
              if $config->{debug} == 1;
            if ( $params->{$resellerparam} == 1 ) {
                $resellerlist .= "'" . $reseller . "',";
            }
        }
        $tmp ="INSERT INTO outbound_routes (pattern,comment,connectcost,includedseconds,cost,inc,trunk,prepend,precedence,resellers,status) VALUES ("
          . $astpp_db->quote( $params->{pattern} ) . ","
          . $astpp_db->quote( $params->{comment} ) . ","
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . $astpp_db->quote( $params->{includedseconds} ) . ","
          . $astpp_db->quote( $params->{cost} ) . ","
          . $astpp_db->quote( $params->{inc} ) . ","
          . $astpp_db->quote( $params->{trunk} ) . ","
          . $astpp_db->quote( $params->{prepend} ) . ","
          . $astpp_db->quote( $params->{precedence} ) . "," . "\""
          . $resellerlist . "\"" . ",1)";
          
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Outbound Route:") . " '"
              . $params->{pattern} . "' "
              . gettext("has been created.");
        }
        else 
        {
            $status .=
                gettext("Outbound Route: ") . " '"
              . $params->{pattern} . "' "
              . gettext("FAILED to create!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }       
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {
        my @resellers = &list_resellers($astpp_db);
        my $resellerlist;
        foreach my $reseller (@resellers) {
            my $resellerparam = "reseller-" . $reseller;
            print "RESELLER: $reseller PARAM = $params->{$resellerparam}"
              if $config->{debug} == 1;
            if ( $params->{$resellerparam} == 1 ) {
                $resellerlist .= "'" . $reseller . "',";
            }
        }
        $tmp = "UPDATE outbound_routes SET"
          . " pattern="
          . $astpp_db->quote( $params->{pattern} ) . ","
          . " comment="
          . $astpp_db->quote( $params->{comment} ) . ","
          . " connectcost="
          . $astpp_db->quote( $params->{connectcost} ) . ","
          . " includedseconds="
          . $astpp_db->quote( $params->{includedseconds} ) . ","
          . " cost="
          . $astpp_db->quote( $params->{cost} ) . "," . " inc="
          . $astpp_db->quote( $params->{inc} ) . ","
          . " trunk="
          . $astpp_db->quote( $params->{trunk} ) . ","
          . " prepend="
          . $astpp_db->quote( $params->{prepend} ) . ","
          . " precedence="
          . $astpp_db->quote( $params->{precedence} ) . ","
          . " resellers="
          . $astpp_db->quote($resellerlist)
          . " WHERE id = "
          . $astpp_db->quote( $params->{id} );
          
        if ( $astpp_db->do($tmp) ) {
            $status .=
                gettext("Outbound Route: ") . " '"
              . $params->{id} . "' "
              . gettext("has been saved.");
        }
        else 
        {
            $status .=
                gettext("Outbound Route:") . " '"
              . $params->{id} . "' "
              . gettext("FAILED to saved!");
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Deactivate...") ) {
        my $tmp = "UPDATE outbound_routes SET status = 2 WHERE id = "
          . $astpp_db->quote( $params->{id} );
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
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
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
        }

    }

    return $status;
}



sub build_calc_charge() {
    my ( $status, $body, $cost, $length, $increment );
    if ( defined($params->{action}) && $params->{action} eq gettext("Price Call...") ) 
    {
        my $branddata = &get_pricelist( $astpp_db, $params->{pricelist} );
        my $numdata = &get_route( $astpp_db, $config, $params->{phonenumber},$params->{pricelist} );
        if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
            $numdata->{connectcost} =$numdata->{connectcost} *( ( $branddata->{markup} / 1 ) + 1 );
            $numdata->{cost} = $numdata->{cost} * ( ( $branddata->{markup} / 1 ) + 1 );
        }

        if ($numdata->{inc} > 0 ) 
        {
            $increment = $numdata->{inc};
        }
        else 
        {
            $increment = $branddata->{inc};
        }


        $ASTPP->debug(
            user  => $param->{username},
            debug => "$numdata->{connectcost}, $numdata->{cost}, "
              . $params->{length} * 60
              . ", $increment, $numdata->{includedseconds}"
        );

        my $cost = &calc_call_cost($numdata->{connectcost}, $numdata->{cost},
						        $params->{length} * 60,  
						        $increment,
						        $numdata->{includedseconds}
								);
        $cost = $cost / 1;
        $cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
        foreach my $handle (@output) {
            print $handle "Matching pattern is $numdata->{pattern}\n";
        }

#         $status .= gettext("Call to: "). $params->{phonenumber}
#         .gettext(" will cost: ").$cost
#         .gettext(" for a call lasting "). $params->{length}
#         .gettext(" minutes.");
	 $status .=  $cost;
    }
    return $status;
}

sub build_taxes() {

    my ( $action, @brands, $tmp, $template, @taxes_list );

    if ( defined($params->{action}) && $params->{action} eq "Save Item" ) {

        $tmp = "UPDATE taxes SET taxes_priority = ". $astpp_db->quote( $params->{taxes_priority} ) . ","
		. " taxes_amount = ". $astpp_db->quote( $params->{taxes_amount} ) . ","
		. " taxes_rate = ". $astpp_db->quote( $params->{taxes_rate} ) . ","
		. " taxes_description = ". $astpp_db->quote( $params->{taxes_description} ) . ","
		. " last_modified = NOW() ". " WHERE taxes_id = ". $astpp_db->quote( $params->{taxes_id} );

        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) {
            $status .= "Saved Tax!";
        }
        else 
        {
            $status .= "Failed to Save Tax!";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Add Item" ) {
        $tmp = "INSERT INTO taxes (taxes_priority,taxes_amount,taxes_rate,taxes_description,date_added) VALUES ("
          . $astpp_db->quote( $params->{taxes_priority} ) . ","
          . $astpp_db->quote( $params->{taxes_amount} ) . ","
          . $astpp_db->quote( $params->{taxes_rate} ) . ","
          . $astpp_db->quote( $params->{taxes_description} ) .","
          ."NOW()".")";

        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) 
        {
            $status .= "Added Tax!";
        }
        else 
        {
            $status .= "Failed to Add Tax!";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Delete" ) {
        $tmp = "DELETE FROM taxes WHERE taxes_id = ". $astpp_db->quote( $params->{taxes_id} );
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        
        if ( $astpp_db->do($tmp) ) {
            $status .= "Removed Tax!";
        }
        else 
        {
            $status .= "Failed to Remove Tax!";
        }
    }

    return $status;
}


sub build_configuration() {
    my ( $action, @brands, @resellerlist, $tmp, $template, @configuration_list );

    if ( defined($params->{action}) && $params->{action} eq "Save Item" ) {

        if ( $params->{logintype} == 1 ) 
        {
            $params->{reseller} = $params->{username};
		}

	$tmp = "UPDATE system SET ";
	if ($params->{resller}) 
	{
	  $tmp .= " reseller = ". $astpp_db->quote( $params->{reseller} ) . ","
	}
	if ($params->{brand}) 
	{
	      $tmp .= " brand = ". $astpp_db->quote( $params->{brand} ) . ","
	}
	$tmp .= " name = ". $astpp_db->quote( $params->{name} ) . ","
	. " value = ". $astpp_db->quote( $params->{value} ) . ","
	. " comment = ". $astpp_db->quote( $params->{comment} )
	. " WHERE id = ". $astpp_db->quote( $params->{id} );
	$ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $astpp_db->do($tmp) ) 
        {
            $status .= "Saved Configuration Item!";
        }
        else 
        {
            $status .= "Failed to Save Configuration Item!";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Add Item" ) 
    {
        if ( $params->{logintype} == 1 ) 
        {
            $params->{reseller} = $params->{username};
        }
        
       print $tmp = "INSERT INTO system (reseller,brand,name,value,comment) VALUES ("
          . $astpp_db->quote( $params->{reseller} ) . ","
          . $astpp_db->quote( $params->{brand} ) . ","
          . $astpp_db->quote( $params->{name} ) . ","
          . $astpp_db->quote( $params->{value} ) . ","
          . $astpp_db->quote( $params->{comment} ) . ")";


        $ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $astpp_db->do($tmp) ) {
            $status .= "Added Configuration Item!";
        }
        else 
        {
            $status .= "Failed to Add Configuration Item!";
        }
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Delete" ) 
    {
        if ( $params->{logintype} == 2 ) 
        {
            if ( $params->{reseller} ne "" ) 
            {            
                $tmp = "DELETE FROM system WHERE id = ". $astpp_db->quote( $params->{id} ). " AND reseller = "
                  . $astpp_db->quote( $params->{reseller} )
                  . " LIMIT 1";
            }
            else 
            {
                $tmp ="DELETE FROM system WHERE id = "
                  . $astpp_db->quote( $params->{id} )
                  . " AND reseller IS NULL LIMIT 1";
            }
     }
     elsif ( $params->{logintype} == 1 ) 
     {
            $tmp = "DELETE FROM system WHERE id = "
              . $astpp_db->quote( $params->{id} )
              . " AND reseller = "
              . $astpp_db->quote( $params->{username} )
              . " LIMIT 1";
     }

     $ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $astpp_db->do($tmp) ) 
        {
            $status .= "Removed Configuration Item!";
        }
        else 
        {
            $status .= "Failed to Remove Configuration Item!";
        }        
    }
    return $status;
}







sub initialize() {



    $config = &load_config();

   $status .= gettext("Main Configuration Unavailable!") unless $config;

    $astpp_db = &connect_db( $config, @output );

    $status .= gettext("ASTPP Database Unavailable!") unless $astpp_db;

    $config = &load_config_db( $astpp_db, $config ) if $astpp_db;

    $cdr_db = &cdr_connect_db( $config, @output );

    $status .= gettext("Asterisk CDR Database Unavailable!") unless $cdr_db;
    
    if ( $config->{opensips} == 1 ) {
	$opensips_db = &connect_opensipsdb( $config, @output );
    }


    if ( $config->{enablelcr} == 1 ) {
        push @modes, gettext("LCR");
    }

    if ( $config->{users_dids_rt} == 1 ) {
        $rt_db = &rt_connect_db( $config, @output );
    }

    if ( $config->{users_dids_amp} == 1 ) {
        $freepbx_db = &freepbx_connect_db( $config, @output );
    }

    if ( $config->{users_dids_freeswitch} == 1 ) {
        $fs_db = &connect_freeswitch_db( $config, @output );
        $ASTPP->set_freeswitch_db($fs_db);
    }
    if ( $config->{callingcards} == 1 ) {
        push @modes, gettext("Calling Cards");
    }
    @modes = sort @modes;
    push @currency, $config->{currency};
    $ASTPP->set_astpp_db($astpp_db);
    $ASTPP->set_cdr_db($cdr_db);
    if ($config->{softswitch} == 0) {
        $config->{cdr_table} = $config->{asterisk_cdr_table};
    } elsif ($config->{softswitch} == 1) {
        $config->{cdr_table} = $config->{freeswitch_cdr_table};
    } else {
        $config->{cdr_table} = $config->{asterisk_cdr_table};
    }
}







############### Freeswitch SIP Device handling #########################

sub build_freeswitch_sip_devices() {
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;
    my ( @device_list, $count, @sip_devices );

    if ( defined($params->{action}) && $params->{action} eq "Delete..." ) {
        $ASTPP->fs_delete_sip_user( id => $params->{directory_id} );
        $status =
            gettext("SIP Device:") . " "
          . $params->{directory_id} . " "
          . gettext("Removed Successfully!");
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Save..." ) 
    {
        my $failure;
        $ASTPP->debug( user => $param->{username}, debug => "Directory ID: " . $params->{directory_id});
        if (!$params->{directory_id} || $params->{directory_id} == 0 || $params->{directory_id} eq "") {
        	$ASTPP->debug( user => $param->{username}, debug => "Adding User");
			$params->{domain} = $config->{freeswitch_domain} if !$params->{domain};
            $params->{context} = $config->{freeswitch_context} if !$params->{context};
			my ($failure,$name);
        ($failure, $status, $name) = $ASTPP->fs_add_sip_user(
        				username	=> $params->{fs_username},
                        accountcode     => $params->{accountcode},
                        freeswitch_domain => $params->{domain},
                        freeswitch_context => $params->{context},
                        vm_password     => $params->{vm_password},
                        password        => $params->{fs_password},
                        sip_ext_prepend => $config->{sip_ext_prepend},
        );
	} 
	else 
	{
        	$ASTPP->debug( user => $param->{username}, debug => "Saving User");
        	$ASTPP->fs_save_sip_user( directory_id => $params->{directory_id},
			username	=> $params->{fs_username},
            accountcode     => $params->{accountcode},
            freeswitch_domain => $params->{domain},
            freeswitch_context => $params->{context},
            vm_password     => $params->{vm_password},
            password        => $params->{fs_password},);
	}
        $status .= "<br>";
    }
    elsif ( defined($params->{action}) && $params->{action} eq "Edit..." ) 
    {
		my $deviceinfo = $ASTPP->fs_retrieve_sip_user(
			directory_id => $params->{directory_id}
		);
	     
	     $status = $params->{directory_id}."###"
	    .$deviceinfo->{accountcode}."###"
	    .$deviceinfo->{context}."###"
	    .$deviceinfo->{password}."###"
	    .$deviceinfo->{vm_password}."###"
	    .$deviceinfo->{username};

    }
    return $status;
}







############### Integration with Realtime starts here #######################

sub build_sip_devices() {

    my (
        @pricelists, $pageno,   $status,   $body, $number,
        $inuse,      $cardstat, $cardinfo, $pagesrequired
    );

   my ( $deviceinfo, $sql );
    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

    if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    my $results_per_page = $config->{results_per_page};

    if ( $results_per_page eq "" ) { $results_per_page = 25; }

    if ( !$params->{action} ) { $params->{action} = gettext("Information..."); }

    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {

        if ( param('number') ne "" ) {
            $number = param('number');
        }
        else {
            $number = param('numberlist');
        }

       my $name = &finduniquesip_rt($number);

        $config->{rt_sip_type} = $params->{devicetype};

        $config->{ipaddr}      = $params->{ipaddr};

	    if ( $config->{users_dids_rt} == 1 ) {
		$status .=
		  &add_sip_user_rt( $rt_db, $config, $name, $params->{secret},
		    $params->{context}, $number, $params );
		$status .= "<br>";
	    }

	    

# 	    if ( $config->{opensips} == 1 ) {
# 		$status .=
# 		  &add_sip_user_opensips( $opensips_db, $config, $name, $params->{accountpassword}, $number, $params,$params->{customnum});
# 		$status .= "<br>";
# 	    }

           if ( $config->{users_dids_freeswitch} == 1 ) {
	     
	     my $name =
	      &finduniquesip_freeswitch( $fs_db, $config,$params->{number} );
	      
                $status .=
                  &add_sip_user_freeswitch( $fs_db, $config, $name,
                   $params->{accountpassword},
                    $params->{number}, $params, $accountinfo->{cc} );
                $status .= "<br>";
            }
        $params->{action} = gettext("Information...");
    }

    elsif ( defined($params->{action}) && $params->{action} eq gettext("Delete...") ) {
        if ( &del_sip_user_rt( $rt_db, $config, $params->{devicename} ) ) {

            $status .=
               gettext("Removed Device:")
             . " $params->{devicename} "
              . gettext("from -Realtime") . "<br>";
        }

       else {
            $status .=
              gettext("Unable to remove device from -Realtime!") . "<br>";
        }

        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {

        my $tmp =
            "UPDATE $config->{rt_sip_table} SET"
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
          . $rt_db->quote( $params->{mailbox} ) . ", " . " nat="
          . $rt_db->quote( $params->{rt_sip_nat} ) . ", "
          . " port="
          . $rt_db->quote( $params->{sip_port} ) . ", "
          . " qualify="
          . $rt_db->quote( $params->{qualify} ) . ", "
          . " secret="
          . $rt_db->quote( $params->{secret} ) . ", "
          . " type="
          . $rt_db->quote( $config->{rt_sip_type} ) . ", "
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
        $ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $rt_db->do($tmp) ) {

            $status .= gettext("Updated Device:") . " $params->{name}<br>";
        }
        else {
            $status .= gettext("Unable to update device!") . "<br>";
        }

        $params->{action} = gettext("Information...");
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Add...") ) {

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
          . hidden(
            -name  => "mode",
            -value => gettext("Asterisk(TM) SIP Devices")
          )
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
            -default => $config->{rt_sip_type}
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
		  </table>";

    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Edit...") ) {

        my @accountlist = &list_accounts($astpp_db);

        $sql =
          $rt_db->prepare( "SELECT * FROM $config->{rt_sip_table} WHERE name = "
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
          . "</td></tr>";

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
          . hidden(
            -name  => "mode",
            -value => gettext("Asterisk(TM) SIP Devices")
          )
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
          . "</td></tr>"
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
          . "</td>" . "</tr>
		  <tr class=\"header\"><td>"
          . gettext("Allow")
          . "</td><td>"
          . gettext("Disallow")
          . "</td><td>"
          . gettext("Qualify")
          . "</td><td>"
          . gettext("Call Forward")
          . "</td></tr>"
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
          . "</td></tr>"
          . "<tr></tr>
		  </table>";
    }



    if ( defined($params->{action}) && $params->{action} eq gettext("Information...") ) {
        $body =

            start_form 
          . "<table class=\"default\">" 
          . "<tr class=\"header\"><td>"
          . hidden(
            -name  => "mode",
            -value => gettext("Asterisk(TM) SIP Devices")
          )
          . submit( -name => "action", -value => gettext("Add...") )
          . "</td></tr>"
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
          . "</td></tr>";


        my $tmp = "SELECT name FROM " . $config->{rt_sip_table};

        $sql = $rt_db->prepare($tmp);
        $sql->execute
          || return gettext("Something is wrong with the SIP users database!")
          . "\n";

        my $results       = $sql->rows;
        my $pagesrequired = ceil( $results / $results_per_page );
        print gettext("Pages Required:") . " $pagesrequired\n"
          if ( $config->{debug} == 1 );

        $sql->finish;

        $sql = $rt_db->prepare(
"SELECT * FROM $config->{rt_sip_table} ORDER BY accountcode limit $params->{limit} , $results_per_page"
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
              . gettext("Asterisk(TM) SIP Devices")
              . "&action="
              . gettext("Edit...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . "<img alt='Edit' src='../../_astpp/edit.gif'></a>"
              . "  <a href=\"astpp-admin.cgi?mode="
              . gettext("Asterisk(TM) SIP Devices")
              . "&action="
              . gettext("Delete...")
              . "&devicename="
              . $deviceinfo->{name} . "\">"
              . "<img alt='Edit' src='../../_astpp/delete.gif'></a>"
              . "</td></tr>";
        }

        $body .= "</table>";

        for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {

            if ( $i == 0 ) {

                if ( $params->{limit} != 0 ) {

                    $body .=
                        "<a href=\"astpp-admin.cgi?mode="
                      . gettext("Asterisk(TM) SIP Devices")
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
                      . gettext("Asterisk(TM) SIP Devices")
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

        $sipid = $config->{sip_ext_prepend} . $sipid;

        $sipid = substr( $sipid, 0, 5 );

        $sipid = $name . $sipid;

        $ASTPP->debug( user => $param->{username}, debug => "SIPID: $sipid" );

        $sql = $rt_db->prepare(

            "SELECT COUNT(*) FROM $config->{rt_sip_table} WHERE name = "
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
        $inuse,      $cardstat, $cardinfo, $pagesrequired
    );

    my ( $deviceinfo, $sql );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;


    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {

        if ( param('number') ne "" ) {

            $number = param('number');
        }
        else {
            $number = param('numberlist');
        }

        my $name = &finduniqueiax_rt($number);

        $config->{rt_iax_type} = $params->{devicetype};

        $config->{ipaddr}      = $params->{ipaddr};

        $status .= &add_iax_user_rt( $rt_db, $config, $name, $params->{secret}, $params->{context}, $number, $params );

   

    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Delete...") ) {

        if ( &del_sip_user_rt( $rt_db, $config, $params->{devicename} ) ) {

            $status .=   gettext("Removed Device:") . " $params->{devicename}<br>";

        }
        else {
            $status .= gettext("Unable to remove device!") . "<br>";

        }
       

    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {

        my $tmp =
            "UPDATE $config->{rt_iax_table} SET"
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

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $rt_db->do($tmp) ) {
            $status .= gettext("Updated Device:") . " $params->{devicenumber}<br>";
        }
        else {
            $status .= gettext("Unable to update device!") . "<br>";
        }
      
   }    
  
    return $status;
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

        $iaxid = $config->{iax2_ext_prepend} . $iaxid;

        $iaxid = substr( $iaxid, 0, 5 );
        $iaxid = $name . $iaxid;
        $ASTPP->debug( user => $param->{username}, debug => "IAXID: $iaxid" );

        $sql = $rt_db->prepare(

            "SELECT COUNT(*) FROM $config->{rt_iax_table} WHERE name = "
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
       @pricelists,       $pageno,        $status,     $body,
        $number,           $inuse,         $cardstat,   $cardinfo,
        $results_per_page, $pagesrequired, $deviceinfo, $sql
    );

    return gettext("Database is NOT configured!") . "\n" unless $astpp_db;

   
    if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {


        my $tmp =
            "INSERT INTO $config->{rt_extensions_table} (context,"
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
       
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Delete...") ) {
        my $tmp = "DELETE FROM $config->{rt_extensions_table} WHERE " . "id = "
          . $rt_db->quote( $params->{id} );
        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Deleted Dialplan Entry:") . " $params->{id}<br>";
        }
        else {
            $status .= gettext("Unable to delete dialplan entry!") . "<br>";
        }
        
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Save...") ) {

        my $tmp =

            "UPDATE $config->{rt_extensions_table} SET"
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
        $ASTPP->debug( user => $param->{username}, debug => $tmp );

        if ( $rt_db->do($tmp) ) {
            $status .=
              gettext("Updated Dialplan Entry:") . " $params->{id}<br>";
        }
        else {
            $status .= gettext("Unable to update dialplan!") . "<br>";
        }     

    }
	
     
    return $status;
}



########### Start on callshop support ###########################
sub build_add_callshop(){
    if ( defined($params->{action}) && $params->{action} eq gettext("Add...") ) 
    {
         if ( $params->{logintype} == 1 || $params->{logintype} == 5 ) 
         {
            $params->{pricelist} = $params->{username};
            $params->{reseller}  = $params->{username};
         }
         else 
         {
            $params->{pricelist} = $config->{new_user_brand};
         }

         $params->{accounttype} = 5;
         $params->{number}      = $params->{callshop_name};
         $status .= &generate_accounts( $params, $config );

	     my $sql = $astpp_db->prepare("SELECT COUNT(*) FROM callshops WHERE name = ". $astpp_db->quote( $params->{callshop_name} ) );
         $sql->execute;
         my $record = $sql->fetchrow_hashref;
         my $count  = $record->{"COUNT(*)"};
         $sql->finish;

         if ( $count == 0 ) {
            my $tmp ="INSERT INTO callshops (name,osc_dbname,osc_dbpass, osc_dbuser,osc_dbhost"
              . ",osc_site,status) VALUES ("
              . $astpp_db->quote( $params->{callshop_name} ) . ", "
              . $astpp_db->quote( $params->{osc_dbname} ) . ", "
              . $astpp_db->quote( $params->{osc_dbpass} ) . ", "
              . $astpp_db->quote( $params->{osc_dbuser} ) . ", "
              . $astpp_db->quote( $params->{osc_dbhost} ) . ", "
              . $astpp_db->quote( $params->{osc_site} ) . ", 1)";
              
            $ASTPP->debug( user => $param->{username}, debug => $tmp );
            if ( $astpp_db->do($tmp) ) 
            {
                $status .= gettext("CallShop Added Successfully!");
            }
            else 
            {
                $status .= gettext("CallShop Failed To Add!");
            }
        }
        else 
        {
            $status .= gettext("CallShop Exists Already!");
        }
    }
    return $status;
}


sub build_remove_callshop() {
    if (defined($params->{action}) && $params->{action} eq gettext("Remove CallShop") ) {
        if ( !$params->{callshop} ) {
            $params->{callshop} = $params->{callshop_list};
        }

        $tmp = "DELETE FROM callshops WHERE name = ". $astpp_db->quote( $params->{callshop} );
        $ASTPP->debug( user => $param->{username}, debug => $tmp );
        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("CallShop"). " $params->{callshop} ". gettext("removed successfully!") . "<br>";
        }
        
        $tmp = "DELETE FROM accounts WHERE number = "
          . $astpp_db->quote( $params->{callshop} );
          
        if ( $astpp_db->do($tmp) ) {
            $status .=gettext("Account"). " $params->{callshop} ". gettext("removed successfully!") . "<br>";
        }

        $tmp ="DELETE FROM accounts WHERE reseller = ". $astpp_db->quote( $params->{callshop} ). " AND type = 6";

        if ( $astpp_db->do($tmp) ) {
            $status .= gettext("Booths belonging to"). " $params->{callshop} ". gettext("removed successfully!") . "<br>";
        }
    }

    return $status;
}

sub build_add_booth() {
    my ( $template, $currency, $pricelist, $language, $status );
     $params->{reseller} = $params->{username};
    if ( $params->{reseller} eq "" ) 
    {
        $status .= gettext("No Reseller Name Set!  Error!");
    }
    elsif( defined($params->{action}) && $params->{action} eq gettext("Generate Booth") ) 
    {
		
        $status .= &generate_accounts( $params, $config );
        my $accountinfo = &get_account( $astpp_db, $params->{number} );
        $astpp_db->do( "UPDATE cdrs SET status = 1 WHERE cardnum = "
              . $astpp_db->quote( $params->{number} )
              . " LIMIT 1" );
        if ( $params->{SIP} ) {
            $ASTPP->debug(user  => $param->{username},debug => gettext("Adding SIP Device!"));
            $config->{rt_sip_type} = "friend";           #$params->{devicetype};
            $config->{ipaddr}      = $params->{ipaddr};
            if ( $config->{users_dids_rt} == 1 ) {
                my $name = &finduniquesip_rt( $params->{number} );
                $status .= &add_sip_user_rt(
                    $rt_db,             $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );

                $status .= "<br>";
            }

            if ( $config->{users_dids_amp} == 1 ) {
                my $name = &finduniquesip_freepbx( $freepbx_db, $config,$params->{number} );
                &add_sip_user_freepbx(
                    $freepbx_db,        $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );

                $status .= "<br>";
            }

	if ( $config->{opensips} == 1 ) {

	  my $name = $params->{number};  # TEMPORARY PATCH
	  $status .= &add_sip_user_opensips( $opensips_db, $config, $name,$params->{accountpassword},$params->{context}, $params->{number}, $params );
		  $status .= "<br>";
	}
	if ( $config->{users_dids_freeswitch} == 1 ) {

                my $name = &finduniquesip_freeswitch( $fs_db, $config,$params->{number} );
                $status .= &add_sip_user_freeswitch( $fs_db, $config, $name,
                    $params->{accountpassword},
                    $params->{number}, $params, $accountinfo->{cc} );
                $status .= "<br>";
            }
        }
        if ( $params->{IAX2} ) {
            $ASTPP->debug(user  => $param->{username},debug => gettext("Adding IAX2 Device!"));
            $config->{rt_iax_type} = "friend";           #$params->{devicetype};
            $config->{ipaddr}      = $params->{ipaddr};
            if ( $config->{users_dids_amp} == 1 ) {
                my $name = &finduniqueiax_freepbx( $freepbx_db, $config, $config,$params->{number} );
                $status .= &add_iax_user_freepbx(
                    $freepbx_db,        $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );
                $status .= "<br>";
            }

            if ( $config->{users_dids_rt} == 1 ) {
                my $name = &finduniqueiax_rt( $params->{number} );
                $status .= &add_iax_user_rt(
                    $rt_db,             $config,
                    $name,              $params->{accountpassword},
                    $params->{context}, $params->{number},
                    $params,            $accountinfo->{cc}
                );
                $status .= "<br>";
            }
        }
		
        if ($osc_db) {
            my ( $status, $accountinfo ) =
              &osc_get_accountinfo( $osc_db, $config, $params->{number} );
            if ( $status == 1 ) {
                $status .= gettext("OSCommerce user exists already.") . "<br>";
            }
            else 
            {
                my $reseller_info = &get_account( $astpp_db, $params->{username} );
                my $tmp = "SELECT countries_id FROM countries WHERE countries_name LIKE "
                  . $osc_db->quote( $reseller_info->{country} );
                my $sql = $osc_db->prepare($tmp);
                $sql->execute;
                my $record = $sql->fetchrow_hashref;
                $tmp ="INSERT INTO customers (customers_gender,customers_firstname,customers_lastname,"
                  . "customers_email_address,"
                  . "customers_telephone,customers_password,customers_default_address_id) VALUES ("
                  . "'m',"
                  . $osc_db->quote( $params->{number} ) . ",'',"
                  . $osc_db->quote( $config->{emailadd} ) . ",'',"
                  . $osc_db->quote( $params->{accountpassword} ) . ",'')";
                $ASTPP->debug( user => $param->{username}, debug => $tmp );
                $sql = $osc_db->prepare($tmp);
                $sql->execute;
                my $customerid = $sql->{'mysql_insertid'};
                $sql->finish;
                $tmp = "INSERT INTO customers_info (customers_info_id) VALUES ("
                  . $osc_db->quote($customerid) . ")";
                $ASTPP->debug( user => $param->{username}, debug => $tmp );
                $sql = $osc_db->prepare($tmp);
                $sql->execute;
                $sql->finish;
                $tmp = "INSERT INTO address_book (customers_id,entry_gender,entry_firstname,entry_lastname,"
                  . "entry_street_address,entry_postcode,entry_city,entry_state,entry_country_id,entry_zone_id)"
                  . " VALUES("
                  . $osc_db->quote($customerid) . ",'m',"
                  . $osc_db->quote( $params->{number} ) . ",'',"
                  . "'Address','Postal Code','City','State',"
                  . $osc_db->quote( $record->{countries_id} ) . ",'0')";
                $ASTPP->debug( user => $param->{username}, debug => $tmp );
                $sql = $osc_db->prepare($tmp);
                $sql->execute;
                my $addressid = $sql->{'mysql_insertid'};
                $sql->finish;
                $osc_db->do("UPDATE customers SET customers_default_address_id = "
                      . $osc_db->quote($addressid)
                      . " WHERE customers_id = "
                      . $osc_db->quote($customerid) );
                if ( $customerid && $addressid ) {
                    $status .= gettext("User added to OSCommerce!") . "<br>";
                }
                else 
                {
                    $status .= gettext("User NOT added to OSCommerce!") . "<br>";
                }
            }
        }
    }
	
    return $status;
}




sub build_remove_booth() {

#     my $template = HTML::Template->new(
#         filename          => '/var/lib/astpp/templates/booth-remove.tpl',
#         die_on_bad_params => $config->{template_die_on_bad_params}
# 
#     );

    my ( @booth_list, $accountinfo );

    ########



# Decide which booth name to use.  The one in the box has priority over the selected list one.

    if ( $params->{booth_name} ne "" ) {

        $params->{booth_name} = $params->{booth_name};
    }
    else {
        $params->{booth_name} = $params->{booth_list};
    }

    ########
	
    if ( defined($params->{action}) && $params->{action} eq gettext("Remove Booth") && $params->{booth_name} )
    {

        my $sql =
            "UPDATE accounts SET status = 2 WHERE number = "
          . $astpp_db->quote( $params->{booth_name} )
          . " AND reseller = "
          . $astpp_db->quote( $params->{username} );
        $ASTPP->debug( user => $param->{username}, debug => $sql );

        if ( $astpp_db->do($sql) ) {
            $status .=
                gettext("Booth:") . " "
              . $params->{booth_name} . " "
              . gettext("Removed Successfully!") . "<br>";
        }
        else {

            $status .=
                gettext("Booth:") . " "
              . $params->{booth_name} . " "
              . gettext("Failed To Removed!") . "<br>";
        }


        #If this account doesn't exist we don't go through the rest

        if ( $accountinfo->{number} ) {
            if ( $config->{users_dids_rt} == 1 ) {
                $ASTPP->debug(
                    user => $param->{username},
                    debug =>
                      "NUMBER: $accountinfo->{number} CC: $accountinfo->{cc}"
                );

                my @iax_devicelist =
                  &list_iax_account_rt( $rt_db, $config, $accountinfo->{number},
                    $accountinfo->{cc} );

                foreach my $name (@iax_devicelist) {

                    &del_iax_user_rt( $rt_db, $config, $name );
                    $status .=
                      gettext("Removing IAX device:") . " " . $name . "<br>";
                }

                my @sip_devicelist =
                  &list_sip_account_rt( $rt_db, $config, $accountinfo->{number},
                    $accountinfo->{cc} );

                foreach my $name (@sip_devicelist) {

                    &del_sip_user_rt( $rt_db, $config, $name );

                    $status .=
                      gettext("Removing SIP device:") . " " . $name . "<br>";
                }
            }

            if ( $config->{users_dids_amp} == 1 ) {

                $ASTPP->debug(

                   user => $param->{username},
                    debug =>
                      "NUMBER: $accountinfo->{number} CC: $accountinfo->{cc}"
                );

                my @iax_devicelist =

                  &list_iax_account_amp( $freepbx_db, $config,
                    $accountinfo->{number},
                    $accountinfo->{cc} );

                foreach my $name (@iax_devicelist) {

                    &del_iax_user_rt( $freepbx_db, $config, $name );

                    $status .=
	                     gettext("Removing IAX device:") . " " . $name . "<br>";
                }

                my @sip_devicelist =
                  &list_sip_account_amp( $freepbx_db, $config,
                    $accountinfo->{number},
                    $accountinfo->{cc} );

                foreach my $name (@sip_devicelist) {

                    &del_sip_user_rt( $freepbx_db, $config, $name );

                    $status .=
                      gettext("Removing SIP device:") . " " . $name . "<br>";
                }
            }
        }
    }

    @booth_list =
      &list_booths_callshop( $astpp_db, $params->{username}, $config );

    my $booths = popup_menu(
        -name   => "booth_list",
        -values => \@booth_list
    );


#     $template->param( status => $status );

#     $template->param( booths => $booths );

#     return $template->output;

}


sub build_list_booths() {

    my (@booth_list, $booth_status);

    my @booths = ();

    @booth_list =
      &list_booths_callshop( $astpp_db, $params->{username}, $config );

    my $accountinfo = &get_account( $astpp_db, $params->{username} );

    #	my $now = $astpp_db->selectall_arrayref("SELECT NOW() + 0")->[0][0];

    if ( defined($params->{action}) && $params->{action} eq gettext("Deactivate Booth") ) {

        $astpp_db->do( "UPDATE accounts SET status = 0 WHERE number = "

              . $astpp_db->quote( $params->{booth_name} ) );

        &hangup_call( $astpp_db, $config, $params->{channel} );
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Restore Booth") ) {
        $astpp_db->do( "UPDATE accounts SET status = 1 WHERE number = "
              . $astpp_db->quote( $params->{booth_name} ) );
    }
    elsif ( defined($params->{action}) && $params->{action} eq gettext("Hangup Call") ) {

        &hangup_call( $astpp_db, $config, $params->{channel} );
    }

    foreach my $name (@booth_list) {

        my ( $sip_login, $iax2_login, %row, $balance, $tmp, $sql );

        my $boothinfo = &get_account_including_closed( $astpp_db, $name );

	$balance = $ASTPP->accountbalance( account => $name ); 

        $tmp =
            "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
          . $astpp_db->quote($name)
          . " AND status = 0";

        $sql = $astpp_db->prepare($tmp);
        $sql->execute;

        my $record = $sql->fetchrow_hashref;
        $row{name} = $name;

        $row{balance}    = sprintf( "%.2f", $balance / 1 );
        $row{call_count} = $record->{"COUNT(*)"};

        $row{currency}   = $boothinfo->{currency};

        if ( $config->{users_dids_rt} == 1 ) {

            my $tmp =
                "SELECT name FROM "
              . $config->{rt_sip_table}
              . " WHERE accountcode = "
              . $rt_db->quote( $boothinfo->{cc} )
              . " LIMIT 1";

            $ASTPP->debug( user => $param->{username}, debug => $tmp );
            my $sql = $rt_db->prepare($tmp);
            $sql->execute;
            $sip_login = $sql->fetchrow_hashref;
            $sql->finish;
            $tmp =
                "SELECT name FROM "
              . $config->{rt_iax_table}
              . " WHERE accountcode = "
              . $rt_db->quote( $boothinfo->{cc} )
              . " LIMIT 1";

          $ASTPP->debug( user => $param->{username}, debug => $tmp );
            $sql = $rt_db->prepare($tmp);
            $sql->execute;
            $iax2_login = $sql->fetchrow_hashref;

            $sql->finish;

            $tmp = "";

            if ( $sip_login->{name} && $iax2_login->{name} ) {
                $tmp =
                    "SELECT * FROM extensions_status WHERE extension IN("
                  . $astpp_db->quote( $sip_login->{name} ) . ","
                  . $astpp_db->quote( $iax2_login->{name} ) . ")"
                  . " ORDER BY id DESC LIMIT 1";
            }
            elsif ( $sip_login->{name} ) {

                $tmp =
"SELECT * FROM extensions_status WHERE tech = 'SIP' and extension = "

                  . $astpp_db->quote( $sip_login->{name} )
                  . " ORDER BY id DESC LIMIT 1";
            }
            elsif ( $iax2_login->{name} ) {

                $tmp =
"SELECT * FROM extensions_status WHERE tech = 'SIP' and extension = "
                  . $astpp_db->quote( $iax2_login->{name} )
                  . " ORDER BY id DESC LIMIT 1";
            }

            if ( $tmp ne "" ) {

                $ASTPP->debug( user => $param->{username}, debug => $tmp );

                $sql = $astpp_db->prepare($tmp);
                $sql->execute;
                $booth_status = $sql->fetchrow_hashref;
                $sql->finish;
                $row{callstart} = $booth_status->{timestamp};

                if ( $row{callstart} ne "" && $booth_status->{status} eq "Dial"
                    || $booth_status->{status} eq "Answer" )
                {
                    $row{channel} = $booth_status->{Channel};
                    $row{number}  = $booth_status->{number};
                    my $tmp =
                      "SELECT TIMEDIFF(NOW(),'$row{callstart}') AS length";
                    $ASTPP->debug( user => $param->{username}, debug => $tmp );
                    $sql = $astpp_db->prepare($tmp);
                    $sql->execute;
                    my $record = $sql->fetchrow_hashref;
                    $sql->finish;
                    $row{duration} = $record->{length};
                }
            }

            if ( $boothinfo->{status} != 1 ) {
                $row{status} = gettext("Blocked");
            }
            elsif ($boothinfo->{status} == 1
                && $booth_status->{status} eq "Answer" )
            {
                $row{status} = gettext("Inuse");
                $row{in_use} = 1;
            }
            elsif ($boothinfo->{status} == 1
                && $booth_status->{status} eq "Dial" )
            {
                $row{status} = gettext("Inuse");
                $row{in_use} = 1;
            }
            elsif ($boothinfo->{status} == 1
                && $booth_status->{status} ne "Dial"
                && $booth_status->{status} ne "UnRegistered"
                && $row{balance} == 0
                && $booth_status->{status} )
            {

                $row{status} = gettext("Active");
            }
            elsif ($boothinfo->{status} == 1
                && $booth_status->{status} ne "Dial"
                && $row{balance} != 0 )
            {
                $row{status} = gettext("Due");
            }
            elsif ( $boothinfo->{status} == 1 && !$booth_status->{status} ) {
                $row{status} = gettext("Faulty");
            }
            else {
                $row{status} = gettext("Unknown");
            }
        }

        if ( !$row{status} ) {

           if ( $boothinfo->{status} != 1 ) {

                $row{status} = gettext("Blocked");

           }
            else {
                $row{status} = gettext("Active");
            }
        }

        push( @booths, \%row );

        $sql->finish;

        $ASTPP->debug( user => $param->{username}, debug => $tmp );

#        $ASTPP->debug(
#            user  => $param->{username},
#            debug => $record->{"COUNT(*)"}
#        );

      $ASTPP->debug( user => $param->{username}, debug => $balance / 1 );

    }


#     my $template = HTML::Template::Expr->new(
#         filename          => '/var/lib/astpp/templates/booths-list.tpl',
#         die_on_bad_params => $config->{template_die_on_bad_params}
#     );
#     $template->param( booth_list => \@booths );
#     return $template->output;
}


sub build_view_booth() {

    my ( $sql, @cdrs, $sip_login, $iax2_login );

    # Prepare the booth list for the menu.
    my @booth_list =
     &list_booths_callshop( $astpp_db, $params->{username}, $config );

    my $booths = popup_menu(
        -name    => "booth_list",
        -values  => \@booth_list,
        -default => $params->{booth_name}
   );

 ########
# Decide which booth name to use.  The one in the box has priority over the selected list one.

    if ( !$params->{booth_name} || $params->{booth_name} eq "" ) {
        $params->{booth_name} = $params->{booth_list};
    }

    ########
    # If the action parameter is set to "View Booths" we look them up.
    if (   $params->{action} eq gettext("Generate Invoice")
        && $params->{booth_name} )
    {

        my $accountinfo = &get_account( $astpp_db, $params->{booth_name} );
        my ( $invoiceno, $callshop_data );

        $callshop_data = &get_callshop( $astpp_db, $accountinfo->{reseller} );
        $config->{osc_db}   = $callshop_data->{osc_dbname};
        $config->{osc_user} = $callshop_data->{osc_dbuser};
        $config->{osc_host} = $callshop_data->{osc_dbhost};
        $config->{osc_pass} = $callshop_data->{osc_dbpass};
        $params->{action}   = gettext("View Booths");

        if ( $config->{externalbill} eq "oscommerce" ) {

            if ( $accountinfo->{posttoexternal} == 1 ) {
                $ASTPP->debug(
                    user  => $param->{username},
                    debug => gettext("Connecting to OSCommerce.")
                );
                my $osc_db = &osc_connect_db($config);

                $ASTPP->debug(
                    user  => $param->{username},
                    debug => gettext("Generating Invoice")
                );

                $invoiceno =
                  &osc_charges( $astpp_db, $osc_db, $config,
                    $params->{booth_name} );
            }

        }

        if ( !$invoiceno ) {
            $status .= gettext("Invoice NOT generated") . "<br>";
        }
        else {
            $status .=
                gettext("Invoice:")
              . " $invoiceno "
              . gettext("generated") . "<br>";
        }

        $status .=
            "<a href=\""
          . $callshop_data->{osc_site}
          . "/admin/invoice.php?oID="
          . $invoiceno . "\">"
          . gettext("View Invoice") . "</a>";

    }
    elsif ($params->{action} eq gettext("Remove CDRs")
        && $params->{booth_name} )
    {

        my $sql =
 
          $astpp_db->prepare( "UPDATE cdrs SET status = 1 WHERE cardnum = "
              . $astpp_db->quote( $params->{booth_name} ) );

        if ( $sql->execute ) {

            $status .= gettext("CDRS Marked As Billed!") . "<br>";
        }

        $params->{action} = gettext("View Booths");
    }

    if ( defined($params->{action}) && $params->{action} eq gettext("View Booth") ) {

        my $accountinfo = &get_account( $astpp_db, $params->{booth_name} );

        if ( $config->{users_dids_rt} == 1 ) {

            my $tmp =
                "SELECT name,secret FROM "
              . $config->{rt_sip_table}
              . " WHERE accountcode = "
              . $rt_db->quote( $accountinfo->{cc} )
              . " LIMIT 1";

            $ASTPP->debug( user => $param->{username}, debug => $tmp );

            my $sql = $rt_db->prepare($tmp);

            $sql->execute;

            $sip_login = $sql->fetchrow_hashref;

           $sql->finish;

            $tmp =
                "SELECT name,secret FROM "
              . $config->{rt_iax_table}
              . " WHERE accountcode = "
              . $rt_db->quote( $accountinfo->{cc} )
              . " LIMIT 1";

            $ASTPP->debug( user => $param->{username}, debug => $tmp );

            $sql = $rt_db->prepare($tmp);

            $sql->execute;

            $iax2_login = $sql->fetchrow_hashref;

            $sql->finish;

        }

        my @chargelist =

          &list_cdrs_account( $cdr_db,$config, $accountinfo->{number},

            $accountinfo->{cc} );

       &processlist( $astpp_db, $cdr_db, $config,

            $config->{cdr_table},
            \@chargelist );    # Bill as many calls as we can.

        $status .= gettext("We rated as many CDRS as we could") . "<br>";

        my $tmp =
"SELECT uniqueid,callstart,callerid,callednum,disposition,billseconds,debit,credit,notes,cost FROM cdrs WHERE cardnum = "
          . $astpp_db->quote( $params->{booth_name} )
          . "and status = 0"
          . " ORDER BY callstart DESC";
        $ASTPP->debug( user => $param->{username}, debug => $tmp );

        $sql = $astpp_db->prepare($tmp);

        $sql->execute;


        while ( my $record = $sql->fetchrow_hashref ) {
            $record->{callerid} = gettext("unknown") unless $record->{callerid};
            $record->{uniqueid} = gettext("N/A")     unless $record->{uniqueid};
            $record->{disposition} = gettext("N/A")
              unless $record->{disposition};
            $record->{notes}       = "" unless $record->{notes};
            $record->{callstart}   = "" unless $record->{callstart};
            $record->{callednum}   = "" unless $record->{callednum};
            $record->{billseconds} = "" unless $record->{billseconds};
            if ( $record->{debit} ) {
                $record->{debit} = $record->{debit} / 1;
                $record->{debit} = sprintf( "%.6f", $record->{debit} );
            }
            else {
                $record->{debit} = "-";
            }

            if ( $record->{credit} ) {
                $record->{credit} = $record->{credit} / 1;
                $record->{credit} = sprintf( "%.6f", $record->{credit} );
            }
            else {
                $record->{credit} = "-";
            }

            if ( $record->{cost} ) {

                $record->{cost} = $record->{cost} / 1;
                $record->{cost} = sprintf( "%.6f", $record->{cost} );
            }
            else {

                $record->{credit} = "-";

            }

            $ASTPP->debug( user => $param->{username}, debug => $record->{id} );
            $record->{profit} = ( $record->{debit} - $record->{cost} );
            push( @cdrs, $record );
        }
    }


#     my $template = HTML::Template->new(
#         filename          => '/var/lib/astpp/templates/booth-view.tpl',
#         die_on_bad_params => $config->{template_die_on_bad_params}
#     );
# 
# 
# 
#     $template->param( booth_name => $params->{booth_name} );
    my $balance = $ASTPP->accountbalance( account => $params->{booth_name} ) / 1;

    my $unrated =
     &count_unrated_cdrs_account( $config, $cdr_db, $accountinfo->{number},
        $accountinfo->{cc} );
    $ASTPP->debug( user => $param->{username}, debug => $balance );
#     $template->param( unrated_cdrs  => $unrated );
#     $template->param( booths        => $booths );
#     $template->param( balance       => $balance );
#     $template->param( cdr_list      => \@cdrs );
#     $template->param( sip_username  => $sip_login->{name} );
#     $template->param( sip_password  => $sip_login->{secret} );
#     $template->param( iax2_username => $iax2_login->{name} );
#     $template->param( iax2_password => $iax2_login->{secret} );
#     return $template->output;
}


###################Start of Application ###################


&initialize();

if ( !$config->{template_die_on_bad_params} ) {

    $config->{template_die_on_bad_params} = 0;
}

# my $template = HTML::Template->new(
#     filename          => '/var/lib/astpp/templates/main.tpl',
#     die_on_bad_params => $config->{template_die_on_bad_params}
# );

my $log_call = "astpp-wraper.cgi,";

foreach my $param ( param() ) {
    $params->{$param} = param($param);
    $ASTPP->debug(
        user  => $params->{username},
        debug => "$param $params->{$param}"
    );

    $log_call .= "$param=$params->{$param},";
}


$ASTPP->debug( debug => $log_call );
    # In here we setup privileges for the different account levels

    $ASTPP->debug(
        user  => $param->{username},
       debug => "LOGIN TYPE = $params->{logintype}"
    );

#     if ( $params->{logintype} == 0 ) { # User Login - Not allowed to do anything
#        $ASTPP->debug(
#            user  => $param->{username},
#            debug => "ASTPP USER LOGIN - DISABLED"
# 
#         );
# 
#         @modes = ();
# 
#     }
    if ( $params->{logintype} == 1 ) {    # Reseller Login

           # We reload the astpp-enh-config to the resellers copy.  We will also

           # reload databases other than the astpp one.

        $config = &load_config_reseller_db( $astpp_db, $config, $params->{username} );

        $freepbx_db = &freepbx_connect_db( $config, @output )

          if $config->{users_dids_amp} == 1;

        $rt_db = &rt_connect_db( $config, @output )

          if $config->{users_dids_rt} == 1;

        $ASTPP->debug(
            user  => $param->{username},
            debug => "ASTPP RESELLER LOGIN"
        );

        @modes = (
            gettext("Accounts"),   gettext("Rates"),
            gettext("DIDs"),       gettext("Logout"),
	            gettext("Call Shops"),    
            gettext("Reseller Reports")
        );

        @Accounts = (
            gettext("Create Account"), gettext("Process Payment"),
            gettext("Remove Account"), gettext("Edit Account"),
            gettext("List Accounts"),  gettext("View Details")
        );

        my @Rates = (
            gettext("Pricelists"), gettext("Calc Charge"),
            gettext("Routes"),     gettext("Import Routes")
        );

        my @DIDs = ( gettext("Manage DIDs") );

        if ( $config->{callingcards} == 1 ) {
            push @modes, gettext("Calling Cards");
        }

        @modes = sort @modes;

        %types = (
            '0' => gettext("User"),
            '1' => gettext("Reseller"),
            '5' => gettext("CallShop")
        );

        $params->{logged_in_reseller} = $params->{username};

    }
    elsif ( $params->{logintype} == 2 ) {

        $ASTPP->debug(
            user  => $param->{username},
            debug => "ASTPP ADMINISTRATOR LOGIN"
        );

        @modes = (
            gettext("Accounts"), gettext("Rates"),
            gettext("DIDs"),     gettext("Statistics"),
			gettext("System"),	  
            gettext("Admin Reports")

        );

        if ( $config->{enablelcr} == 1 ) {
            push @modes, gettext("LCR");
        }

        if (   $config->{users_dids_rt} == 1 || $config->{users_dids_freeswitch} == 1 )
        {
           push @modes, gettext("Switch Config");
            if ( $config->{users_dids_rt} == 1 ) {
                push @SwitchConfig,
                  (
                    gettext("Asterisk(TM) IAX Devices"),
                    gettext("Asterisk(TM) SIP Devices"),
                    gettext("Asterisk(TM) Dialplan")
                  );

		push @Statistics,(
				gettext("Asterisk Stats"),
				gettext("View Asterisk(TM) CDRs")
		);

            }

            if ( $config->{users_dids_freeswitch} == 1 ) {
                push @SwitchConfig, ( gettext("Freeswitch(TM) SIP Devices") );

		push @Statistics,(				  
				gettext("View FreeSwitch(TM) CDRs")
		);

            }
        }

        if ( $config->{callingcards} == 1 ) {
            push @modes, gettext("Calling Cards");
        }

        push @modes, gettext("Call Shops");

        @modes = sort @modes;

        %types = (
            '0' => gettext("User"),
            '1' => gettext("Reseller"),
            '2' => gettext("Administrator"),
            '3' => gettext("Provider"),
            '4' => gettext("Customer Service"),
            '5' => gettext("CallShop")
        );
        $params->{logged_in_reseller} = "";
    }
    elsif ( $params->{logintype} == 3 )
    { 
    	# Provider Login - Providers are only allowed to look at stuff that pertains to them.

        $ASTPP->debug(
            user  => $param->{username},
            debug => "ASTPP VENDOR LOGIN"
        );

        @modes = (

            gettext("Trunk Statistics"), gettext("View CDRs"),
#           gettext("Home"),             gettext("Outbound Routes")
		    gettext("Outbound Routes")    
        );
    }

    elsif ( $params->{logintype} == 4 ) {    # Customer Service Login
        $ASTPP->debug(
            user  => $param->{username},
            debug => "ASTPP CUSTOMER SERVICE LOGIN"
        );

        @modes = (
            gettext("Accounts"),   gettext("DIDs"),
		    gettext("Statistics")
        );

        if ( $config->{callingcards} == 1 ) {
            push @modes, gettext("Calling Cards");
        }

        @modes = sort @modes;

        %types = (
            '0' => gettext("User"),
            '1' => gettext("Reseller"),
            '3' => gettext("Provider"),
            '4' => gettext("Customer Service"),
            '5' => gettext("CallShop")
        );
    }
    elsif ( $params->{logintype} == 5 ) {    # CallShop Login
        $config =
          &load_config_reseller_db( $astpp_db, $config, $params->{username} );
        $freepbx_db = &freepbx_connect_db( $config, @output )
          if $config->{users_dids_amp} == 1;
        $rt_db = &rt_connect_db( $config, @output )
          if $config->{users_dids_rt} == 1;
        my $osc_db = &osc_connect_db( $config, $config, @output )
          if $config->{externalbill} eq "oscommerce";
        $ASTPP->debug(
            user  => $param->{username},
            debug => "ASTPP CALLSHOP LOGIN"
        );

        @modes = (
	  gettext("Booths"), gettext("Routes"),
            gettext("Pricelists"), gettext("CallShop Reports")
        );
        if ( $config->{callingcards} == 1 ) {
            push @modes, gettext("Calling Cards");
        }

        @modes = sort @modes;

        %types = (
            '0' => gettext("User"),
            '1' => gettext("Reseller"),
            '3' => gettext("Provider"),
            '4' => gettext("Customer Service"),
            '5' => gettext("CallShops")
        );
        $params->{logged_in_reseller} = $params->{username};
    }
    else {
        @modes = ();
    }

sub build_add_callerID()  {

      my( $status, $tmp, $callstatus);

	if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
	    
	    my @callerid = &add_callerid($astpp_db, $params);
	    $ASTPP->debug( user => $params->{username}, debug => $tmp );
	}

	if(  defined($params->{action}) && $params->{action} eq gettext("Save...") ){
	    my @callerid = &edit_callerid($astpp_db, $params);
		$ASTPP->debug( user => $params->{username}, debug => $tmp );
	    }
}

sub build_cc_add_callerID()  {

      my( $status, $tmp, $callstatus);

	if ( defined($params->{action}) && $params->{action} eq gettext("Insert...") ) {
	    
	    my @callerid = &add_cc_callerid($astpp_db, $params);
	    $ASTPP->debug( user => $params->{username}, debug => $tmp );
	}

	if(  defined($params->{action}) && $params->{action} eq gettext("Save...") ){
	    my @callerid = &edit_cc_callerid($astpp_db, $params);
		$ASTPP->debug( user => $params->{username}, debug => $tmp );
	    }
}


print header();
$msg  = gettext("Database Not Available!") unless $astpp_db;
print $body = &build_body( $params->{mode} );
print end_html;