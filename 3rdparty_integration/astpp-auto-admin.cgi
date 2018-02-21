#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
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
# 1 - Reseller (Has login permissions for astpp-users.cgi and astpp-resellers.cgi)
# 2 - Admin (Has login permissions everywhere)
#
# This program can be called from a web browser to perform automated actions such as adding and removing
# accounts. All requests must include the paramenter "auth" which is an authorization code stored in
# /var/lib/astpp/astpp-enh-config.conf.
#
# List of functions and required parameters:
# "create_cc" Generate Calling Card
# "brand" - Calling Card Brand
# "value" - Value of Card in 100ths Pennies
# "email" - Email Address of person purchasing the card
# "user" - ASTPP account name, if available, of person purchasing card
# "pins" - 1 or 0 1 is YES and 0 is NO
# "status" - Calling card status - 0 = inactive, 1 = active, 2 = deleted
# "quantity" - How many of these. If not set we presume 1.
#
# "reset_cc" Reset the inuse field on a calling card
# "cardnumber" - The calling card number to reset
#
# "refill_cc" Refill Calling Card
# "destcard" - The calling card number to refill
# "destcardpin" - The pin for the calling card number to refill
# "emptycard" - The calling card number to empty
# "emptycardpin" - The pin for the calling card number to empty
#
# "add_account" -Add an ASTPP account
# "brand"
# "user" - User name
# "amount" - Value of new card in 100ths of a penny.
# "posttoexternal" - 1 is YES and 0 is NO
# "accountpassword" - Account login password
# "creditlimit" - Account credit limit
# "language" - This should be passed in 2 letter style. If blank it reverts
# To the default
#
# "refill_card"
# "amount" - Amount to refill in 100ths of a penny
# "user" - ASTPP account name/number
# "quantity" - How many of these. If not set we presume 1.
#
# "add_device" - Add a device to asterisk -realtime
# "type" - SIP or IAX
# "amount" - Amount to credit ASTPP account with in pennies
# "user" - ASTPP account name/number
# "email" - Users Email Address
# "brand" - Brand/Pricelist
# "context" - Context for the asterisk user
# "service" - A unique number assigned by the billing application
# to this particular entity. This service number is optional.
# "quantity" - How many of these. If not set we presume 1.
#
# "delete_device" - Delete device from asterisk -realtime
# "service"
# "type"
# "suspend_device" - Suspend device in asterisk
# "service"
# "type"
# "unsuspend_device" - Unsuspend device in asterisk
# "service"
# "type"
# "add_did"
# "user"
# "did"
# "delete_did"
# "did"
# "suspend_did"
# "did"
#
# You are required to have a "suspended" context in your asterisk dialplan
# [suspended]
# exten => .,1,Congestion
######################################################################################################
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Getopt::Long;
use Asterisk::Manager;
use lib './lib', '../lib';
use ASTPP;
# use strict;
use vars qw($config $astpp_db $agile_db $params @output
$ASTPP $fs_db $cdr_db $rt_db $freepbx_db $status);
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en"; # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{LANGUAGE}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
$ASTPP = ASTPP->new;
$ASTPP->set_verbosity(4); #Tell ASTPP debugging how verbose we want to be.

#$ASTPP->set_asterisk_agi($AGI);
$ASTPP->set_pagination_script("astpp-admin.cgi");

sub assign_did() {
    my ( $account, $did ) = @_;
    my ($sql);
    $astpp_db->do( "UPDATE dids SET account = "
          . $astpp_db->quote($account)
          . " WHERE number = "
          . $astpp_db->quote($did) );
}

sub generate_card() {
    my ( $pricelist, $servicenum, $customnum, $amount, $posttoexternal, $sweep,
        $password, $creditlimit )
      = @_;
    my ( $mail, $msg );
    print STDERR "ASTPP New Card Pricelist: $pricelist\n";
    print STDERR "ASTPP New Card Number: $customnum\n";
    print STDERR "ASTPP New Card Value: $amount\n";
    my $status = "";
    my $description = "Initialize Account";
    if ( $creditlimit eq "" ) {
        $creditlimit = $config->{credit_limit};
    }
    my $cardinfo = &get_account( $astpp_db, $customnum );
    if ( $cardinfo->{number} eq "" ) {
        if ( $sweep eq "daily" ) {
            $sweep = 0;
        }
        elsif ( $sweep eq "weekly" ) {
            $sweep = 1;
        }
        elsif ( $sweep eq "monthly" ) {
            $sweep = 2;
        }
        elsif ( $sweep eq "quarterly" ) {
            $sweep = 3;
        }
        elsif ( $sweep eq "semi-annually" ) {
            $sweep = 4;
        }
        elsif ( $sweep eq "annually" ) {
            $sweep = 5;
        }
        $params->{number} = $customnum;
        $params->{pricelist} = $pricelist;
        $params->{posttoexternal} = $posttoexternal;
        $params->{sweep} = $sweep;
        $params->{credit_limit} = $creditlimit;
        $params->{password} = $password;
        &addaccount( $astpp_db, $config, $params );
        &email_add_user( $astpp_db, '', $config, $params );
    }
    else {
        $astpp_db->do( "UPDATE accounts SET status = 1 WHERE number = "
              . $astpp_db->quote($customnum) );
        $astpp_db->do( "UPDATE accounts SET password = "
              . $astpp_db->quote($customnum)
              . " WHERE number = "
              . $astpp_db->quote($customnum) );
        &email_refill_account( $astpp_db, '', $config, $params );
    }
    my $timestamp = &prettytimestamp;
    $astpp_db->do(
            "INSERT INTO cdrs (cardnum, callednum, credit, callstart) VALUES ("
          . $astpp_db->quote($customnum) . ","
          . $astpp_db->quote($description) . ", "
          . $astpp_db->quote($amount) . ", "
          . $astpp_db->quote($timestamp)
          . ")" );
    $status .= "$customnum";
    return $status;
}

sub initialize() {
    my ($reseller) = @_;
    $config = &load_config;
    $astpp_db = &connect_db( $config, @output );
    $config = &load_config_db( $astpp_db, $config );
    $config =
      &load_config_reseller_db( $astpp_db, $config, $params->{username} )
      if ($reseller);
    $freepbx_db = &freepbx_connect_db( $config, @output )
      if $config->{users_dids_amp} == 1;
    $rt_db = &rt_connect_db( $config, @output )
      if $config->{users_dids_rt} == 1;

    if ( $config->{users_dids_freeswitch} == 1 ) {
        $fs_db = &connect_freeswitch_db( $config, @output );
        $ASTPP->set_freeswitch_db($fs_db);
    }
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
############## Asterisk Manager Handling ############
sub astmanager_reload() {
    my $astman = new Asterisk::Manager;
    $astman->user( $config->{astman_user} );
    $astman->secret( $config->{astman_secret} );
    $astman->host( $config->{astman_host} );
    $astman->connect || die $astman->error . "\n";
    if ( $config->{debug} == 1 ) {
        print STDERR $astman->command('reload');
    }
    else {
        $astman->command('reload');
    }
    $astman->disconnect;
}

sub reload_asterisk() {
    &astmanager_reload;
    print "SUCCESS";
}
################################################
# Start of Program
################################################
print header;
CGI->new();
foreach my $var ( param() ) {
    $params->{$var} = param($var);
    print STDERR "$var $params->{$var}\n";
}

my $count = 0;

&initialize( $params->{reseller} );
if ( $config->{debug} == 1 ) {
    &debug( $params, @output );
}
if ( $params->{quantity} < 1 || !$params->{quantity} ) {
    $params->{quantity} = 1;
}
if ( $params->{prepend} ne "" ) {
    $config->{service_prepend} = $params->{prepend};
}
if ( $params->{brand} ne "" ) {
    $config->{default_brand} = $params->{brand};
}
if ( $params->{context} ne "" ) {
    $config->{default_context} = $params->{context};
}
if ( !$params->{user} && $params->{account} ) {
    $params->{user} = $params->{account};
}
if ( !$params->{language} ) {
    $params->{language} = $config->{language};
}

# OSCommerce specific adjustments
if ( $config->{externalbill} eq "oscommerce" ) {
    $params->{service} = $params->{invoice};
    $params->{email} = $params->{emailadd};
}

if ( !$params->{service} ) {
    $params->{service} =
        int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 );
}
if ( $config->{service_prepend} eq "" ) {
    my $passedlength = length( $params->{service} );
    my $requiredchars = $config->{service_length} - $passedlength;
    my $prepend = substr( $config->{service_filler}, 0, $requiredchars );
    $params->{extension} = $prepend . $params->{service};
}
else {
    $params->{extension} = $config->{service_prepend} . $params->{service};
}

# We use a random number 5 digis long for a password
$params->{secret} =
    int( rand() * 9000 + 1000 )
  . int( rand() * 9000 + 1000 )
  . int( rand() * 9000 + 1000 )
  . int( rand() * 9000 + 1000 )
  . int( rand() * 9000 + 1000 );
$params->{secret} = substr( $params->{secret}, 0, 5 );

print STDERR gettext("Secret") . ": $params->{secret}\n";

if ( $params->{reseller} ne "null" && $params->{reseller} ) {
    my $config_reseller =
      &load_config_reseller_db( $astpp_db, $config, $params->{username} );
    $config->{default_context} = $config_reseller->{default_context};
    $config->{default_brand} = $config_reseller->{new_user_brand};
    $params->{user} = $config_reseller->{account_prepend} . $params->{user};
    $config->{auth} = $config_reseller->{auth};
    $config->{company_email} = $config_reseller->{company_name};
    $config->{company_name} = $config_reseller->{company_name};
    $config->{company_website} = $config_reseller->{company_website};
    $config->{emailadd} = $config_reseller->{emailadd};
    $config->{user_email} = $config_reseller->{user_email};
}
print STDERR gettext("Email User?") . " $config->{user_email}\n";
$params->{pricelist} = $params->{default_brand} if !$params->{pricelist};

# If we don't receive a password with the POST we set it to a random number.
if ( !$params->{accountpassword} ) {
    $params->{accountpassword} = $params->{secret};
}

########## Check for the correct Auth Code###########
if ( $params->{auth} ne $config->{auth} ) {
    print STDERR gettext("INVALID AUTH KEY");
    print "/n" . gettext("Received Authkey:") . "'" . $params->{auth} . "'";
    exit(0);
}
else {
    print STDERR gettext("AUTHORIZATION SUCCESSFUL!") . "/n";
}

############## Here is where we actually start working #######################
############## Add and remove SIP & IAX devices ##############################
if ( $params->{function} eq "add_device" ) {
    while ( $count < $params->{quantity} ) {
        $count++;
        if ( $config->{users_dids_rt} == 1 ) {
            &add_sip_user_rt( $rt_db, $config, $params->{extension},
                $params->{secret}, $config->{default_context},
                $params->{user}, $params )
              if $params->{type} eq "SIP";
            &add_iax_user_rt( $rt_db, $config, $params->{extension},
                $params->{secret}, $config->{default_context},
                $params->{user} )
              if $params->{type} eq "IAX";
        }
        elsif ( $config->{users_dids_amp} == 1 ) {
            &add_sip_user_freepbx( $freepbx_db, $config, $params->{extension},
                $params->{secret}, $config->{default_context},
                $params->{user}, $params )
              if $params->{type} eq "SIP";

            &add_iax_user_freepbx( $freepbx_db, $config, $params->{extension},
                $params->{secret}, $config->{default_context},
                $params->{user} )
              if $params->{type} eq "IAX";
        }

        if ( $config->{users_dids_freeswitch} == 1 ) {
            my ( $failure, $name );
            ( $failure, $status, $name ) = $ASTPP->fs_add_sip_user(
                accountcode => $params->{user},
                freeswitch_domain => $config->{freeswitch_domain},
                freeswitch_context => $config->{freeswitch_context},
                vm_password => $params->{secret},
                password => $params->{secret},
                sip_ext_prepend => $config->{sip_ext_prepend},
            );

  # if ( $config->{email} == 1 && $params->{accounttype} == 0 ) {
  # $params->{extension} = $name;
  # $params->{secret} = $params->{accountpassword};
  # &email_add_device( $astpp_db, '', $config, $params );
  # print STDERR "Sent Device Generation Email\n";
  # }
        }

        &email_add_user( $astpp_db, '', $config, $params, $params->{type},
            $params->{secret}, $params->{extension} )
          if $config->{user_email} == 1;
    }
}
elsif ( $params->{function} eq "unsuspend" ) {
    if ( $config->{users_dids_rt} == 1 ) {
        &update_context_sip_user_rt( $rt_db, $config, $params->{extension},
            i $config->{default_context} )
          if $params->{type} eq "SIP";
        &update_context_iax_user_rt( $rt_db, $config, $params->{extension},
            $config->{default_context} )
          if $params->{type} eq "IAX";
    }
    elsif ( $config->{users_dids_amp} == 1 ) {
        &update_context_sip_user_freepbx( $freepbx_db, $config,
            $params->{extension}, $config->{default_context} )
          if $params->{type} eq "SIP";
        &update_context_iax_user_freepbx( $freepbx_db, $config,
            $params->{extension}, $config->{default_context} )
          if $params->{type} eq "IAX";
    }
}
elsif ( $params->{function} eq "delete" ) {
    if ( $config->{users_dids_rt} == 1 ) {
        &del_sip_user_rt( $rt_db, $config, $params->{extension} )
          if $params->{type} eq "SIP";
        &del_iax_user_rt( $rt_db, $config, $params->{extension} )
          if $params->{type} eq "IAX";
    }
    elsif ( $config->{users_dids_amp} == 1 ) {
        &del_sip_user_freepbx( $freepbx_db, $config, $params->{extension} )
          if $params->{type} eq "SIP";
        &del_iax_user_freepbx( $freepbx_db, $config, $params->{extension} )
          if $params->{type} eq "IAX";
    }
    &email_del_user( $astpp_db, '', $config, $params )
      if $config->{user_email} == 1;
}
elsif ( $params->{function} eq "suspend" ) {
    if ( $config->{users_dids_rt} == 1 ) {
        &update_context_sip_user_rt( $rt_db, $config, $params->{extension},
            "suspended" )
          if $params->{type} eq "SIP";
        &update_context_iax_user_rt( $rt_db, $config, $params->{extension},
            "suspended" )
          if $params->{type} eq "IAX";
    }
    elsif ( $config->{users_dids_amp} == 1 ) {
        &update_context_sip_user_freepbx( $freepbx_db, $config,
            $params->{extension}, "suspended" )
          if $params->{type} eq "SIP";
        &update_context_iax_user_freepbx( $freepbx_db, $config,
            $params->{extension}, "suspended" )
          if $params->{type} eq "IAX";
    }
    &email_del_user( $astpp_db, '', $config, $params )
      if $config->{user_email} == 1;
}
################# DID Handling Support ###############################
elsif ($params->{function} eq "add_did"
    || $params->{function} eq "unsuspend_did" )
{
    &assign_did( $params->{user}, $params->{did} );
}
elsif ($params->{function} eq "delete_did"
    || $params->{function} eq "suspend_did" )
{
    &assign_did( "", $params->{did} );
}
################ Refill ASTPP Users Account ########################
elsif ( $params->{function} eq "add_account" ) {
    print STDERR gettext("ADD ACCOUNT");
    while ( $count < $params->{quantity} ) {
        print $count++;
        &generate_card(
            $config->{default_brand}, $params->{extension},
            $params->{user}, $params->{amount},
            $params->{posttoexternal}, 2,
            $params->{pass}, $params->{creditlimit}
        );
    }
}
elsif ( $params->{function} eq "refill_card" ) {
    print STDERR gettext("REFILL ACCOUNT");
    while ( $count < $params->{quantity} ) {
        $count++;
        $status .=
          &refill_account( $astpp_db, $params->{user}, $params->{amount} );
        &email_refill_account( $astpp_db, '', $config, $params );
    }
    print $status;
}
elsif ( $params->{function} eq "unsuspend_account" ) {
    print STDERR gettext("UNSUSPEND ACCOUNT");
    if (
        $astpp_db->do(
            "UPDATE cards SET status = 0 WHERE number = "
              . $astpp_db->quote( $params->{user} )
        )
      )
    {
        print STDERR "Card $params->{user} successfully unsuspended.\n";
    }
    else {
        print STDERR "Card $params->{user} was NOT successfully unsuspended.\n";
    }
################# Calling Card Support Starts Here ################
}
elsif ( $params->{function} =~ /create_cc/ ) {
    print STDERR gettext("ADDING CALLING CARD");
    my $branddata = &get_cc_brand( $astpp_db, $params->{brand} );
    if ( $config->{debug} == 1 ) {
        print STDERR "BRAND NAME: $branddata->{name} \n";
    }
    while ( $count < $params->{quantity} ) {
        $count++;
        my ( $cc, $pin ) =
          &add_callingcard( $astpp_db, $config, $branddata, $params->{status},
            $params->{value}, $params->{user}, $params->{pins} );
        print gettext("Calling Card:") . " $cc " . gettext("Pin:") . " $pin \n";
        $params->{pricelist} = $params->{brand};
        $params->{pennies} = $params->{value};
        $params->{emailadd} = $params->{account};
        &email_add_callingcard( $astpp_db, '', $config, $params, $cc, $pin );
    }
}
elsif ( $params->{function} =~ /refill_cc/ ) {
    print STDERR gettext("REFILLING CALLING CARD");
    $status =
      &transfer_funds( $astpp_db, $config, $params->{emptycard},
        $params->{emptycardpin},
        $params->{destcard}, $params->{destcardpin} );
    if ( $status == 0 ) {
        $status = gettext("Funds Successfully Transfered");
    }
    else {
        $status = gettext(
"Please ensure that the card numbers and pins are correct. Transfer NOT Successfuly."
        );
    }

}
elsif ( $params->{function} =~ /reset_cc/ ) {
    print STDERR gettext("RESETTING CALLING CARD");
    my $sql = "UPDATE callingcards SET inuse = 0 WHERE cardnumber ="
      . $astpp_db->quote( $params->{cardnumber} );
    $astpp_db->do($sql) || print "$sql failed";
}
################# Null is for testing ###########################
elsif ( $params->{function} eq "null" ) {
    print gettext("SUCCESS");
    print STDERR gettext("SUCCESS");
}
else {
    print "Function '$params->{function}' is INVALID\n";
    print STDERR "Function '$params->{function}' is INVALID\n";
    exit(0);
}
