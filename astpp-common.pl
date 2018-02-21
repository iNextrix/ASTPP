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
use Text::Template;
use POSIX;
use POSIX qw(strftime);
use DBI;
use strict;
use warnings;
use Locale::gettext_pp qw(:locale_h);
use Mail::Sendmail;

$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");

sub load_config {
    my $config;
    open( CONFIG, "</var/lib/astpp/astpp-config.conf" );
    while (<CONFIG>) {
        chomp;            # no newline
        s/#.*//;          # no comments
        s/^\s+//;         # no leading white
        s/\s+$//;         # no trailing white
        next unless length;    # anything left?
        my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
        $config->{$var} = $value;
    }
    close(CONFIG);
    return $config;
}

sub load_config_enh() {
    my $enh_config;
    open( CONFIG, "</var/lib/astpp/astpp-enh-config.conf" );
    while (<CONFIG>) {
        chomp;                 # no newline
        s/#.*//;               # no comments
        s/^\s+//;              # no leading white
        s/\s+$//;              # no trailing white
        next unless length;    # anything left?
        my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
        $enh_config->{$var} = $value;
    }
    close(CONFIG);
    return $enh_config;
}

sub load_config_reseller() {
    my ($reseller) = @_;
    my $reseller_config;
    open( CONFIG, "</var/lib/astpp/astpp-$reseller-config.conf" );
    while (<CONFIG>) {
        chomp;       # no newline
        s/#.*//;     # no comments
        s/^\s+//;    # no leading white
        s/\s+$//;    # no trailing white
        next unless length;    # anything left?
        my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
        $reseller_config->{$var} = $value;
    }
    close(CONFIG);
    return $reseller_config;
}

sub save_config() {
    my (%config) = @_;
    open( CONFIG, ">/var/lib/astpp/astpp-config.conf" );
    print CONFIG ";\n; Automatically created by astpp-config.cgi.\n;\n";
    foreach my $tmp ( keys %config ) {
        print CONFIG "$tmp = " . $config{$tmp} . "\n";
    }
    close(CONFIG);
}

sub add_callingcard() {
    my ( $astpp_db, $config, $enh_config, $branddata, $status, $pennies,
        $account, $pins )
      = @_;
    my ( $cc, $pin, $sql );
    $cc = &finduniquecallingcard( $astpp_db, $config, $enh_config );
    if ($pins) {
        $pin =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        $pin = substr( $pin, 0, $config->{pinlength} );
    }
    $sql =
"INSERT INTO callingcards (cardnumber,brand,status,value,account,pin,validfordays,created,firstused,expiry,maint_fee_pennies,"
      . "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies) VALUES ("
      . $astpp_db->quote($cc) . ","
      . $astpp_db->quote( $branddata->{name} ) . ","
      . $astpp_db->quote($status) . ","
      . $astpp_db->quote($pennies) . ","
      . $astpp_db->quote($account) . ","
      . $astpp_db->quote($pin) . ","
      . $astpp_db->quote( $branddata->{validfordays} )
      . ", NOW(), '0000-00-00 00:00:00', '0000-00-00 00:00:00', "
      . $astpp_db->quote( $branddata->{maint_fee_pennies} ) . ","
      . $astpp_db->quote( $branddata->{maint_fee_days} ) . ","
      . $astpp_db->quote( $branddata->{disconnect_fee_pennies} ) . ","
      . $astpp_db->quote( $branddata->{minute_fee_minutes} ) . ","
      . $astpp_db->quote( $branddata->{minute_fee_pennies} ) . ")";
    print STDERR "$sql";
    $astpp_db->do($sql) || print "$sql failed";
    return ( $cc, $pin );
}

sub add_pricelist() {
    my ( $astpp_db, $name, $inc, $markup, $reseller ) = @_;
    my ( $sql, $tmp, $pricelist, $status );
    $sql =
      $astpp_db->prepare(
        "SELECT name FROM pricelists WHERE name = " . $astpp_db->quote($name) );
    $sql->execute;
    $pricelist = $sql->fetchrow_hashref;
    if ( $pricelist->{name} eq "" ) {
	if (!$reseller) {
        $tmp =
            "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
          . $astpp_db->quote($name) . ", "
          . $astpp_db->quote($inc) . ", "
          . $astpp_db->quote($markup) . ", 1, NULL)";
	} else {
        $tmp =
            "INSERT INTO pricelists (name,inc,markup,status,reseller) VALUES ("
          . $astpp_db->quote($name) . ", "
          . $astpp_db->quote($inc) . ", "
          . $astpp_db->quote($markup) . ", 1," .  $astpp_db->quote($reseller) . ")";
	}
        if ( $astpp_db->do($tmp) ) {
            $status .=
              gettext("Pricelist") . " '$name' " . gettext("has been created");
        }
        else {
            $status .=
                gettext("Pricelist")
              . " '$name' "
              . gettext("FAILED to create!")
              . "($tmp)";
        }
    }
    else {
        $astpp_db->do( "UPDATE pricelists SET status = 1 WHERE name = "
              . $astpp_db->quote($name) );
        $status .=
          gettext("Pricelist") . " '$name' " . gettext("has been reactivated.");
    }
    return $status;
}

sub addaccount() {
    my ( $astpp_db, $config, $enh_config, $accountinfo ) = @_;
    $accountinfo->{reseller} = $accountinfo->{username}
      if ( $accountinfo->{logintype} == 1 );
    $accountinfo->{reseller} = ""
      if ( $accountinfo->{logintype} != 1 );
    $accountinfo->{accounttype} = 0
      if ( $accountinfo->{accounttype} eq gettext("User") );
    $accountinfo->{accounttype} = 1
      if ( $accountinfo->{accounttype} eq gettext("Reseller") );
    $accountinfo->{accounttype} = 2
      if ( $accountinfo->{accounttype} eq gettext("Admin") );
    $accountinfo->{posttoexternal} = 0  if ( !$accountinfo->{posttoexternal} );
    $accountinfo->{firstname}      = "" if ( !$accountinfo->{firstname} );
    $accountinfo->{lastname}       = "" if ( !$accountinfo->{lastname} );
    $accountinfo->{middlename}     = "" if ( !$accountinfo->{middlename} );
    $accountinfo->{reseller}       = "" if ( !$accountinfo->{reseller} );
    $accountinfo->{company}        = "" if ( !$accountinfo->{company} );
    $accountinfo->{address1}       = "" if ( !$accountinfo->{address1} );
    $accountinfo->{address2}       = "" if ( !$accountinfo->{address2} );
    $accountinfo->{postal_code}    = "" if ( !$accountinfo->{postal_code} );
    $accountinfo->{province}       = "" if ( !$accountinfo->{province} );
    $accountinfo->{city}           = "" if ( !$accountinfo->{city} );
    $accountinfo->{country}        = "" if ( !$accountinfo->{country} );
    $accountinfo->{telephone1}     = "" if ( !$accountinfo->{telephone1} );
    $accountinfo->{telephone2}     = "" if ( !$accountinfo->{telephone2} );
    $accountinfo->{facsimile}      = "" if ( !$accountinfo->{facsimile} );
    $accountinfo->{email}          = "" if ( !$accountinfo->{email} );
    $accountinfo->{currency}       = "" if ( !$accountinfo->{currency} );
    my $cc = &finduniquecc( $astpp_db, $config, $enh_config );
    my $tmp =
"INSERT INTO accounts (cc,number,pricelist,status,sweep,credit_limit,posttoexternal,password,"
      . "first_name, middle_name, last_name, company_name, address_1, address_2,"
      . "postal_code, province, city, country, telephone_1, telephone_2, fascimile,"
      . "email, language, currency, reseller, type"
      . ") VALUES ("
      . $astpp_db->quote($cc) . ","
      . $astpp_db->quote( $accountinfo->{number} ) . ","
      . $astpp_db->quote( $accountinfo->{pricelist} ) . ", 1,"
      . $astpp_db->quote( $accountinfo->{sweep} ) . ","
      . $astpp_db->quote( $accountinfo->{credit_limit} ) . ","
      . $astpp_db->quote( $accountinfo->{posttoexternal} ) . ","
      . $astpp_db->quote( $accountinfo->{accountpassword} ) . ","
      . $astpp_db->quote( $accountinfo->{firstname} ) . ","
      . $astpp_db->quote( $accountinfo->{middlename} ) . ","
      . $astpp_db->quote( $accountinfo->{lastname} ) . ","
      . $astpp_db->quote( $accountinfo->{company} ) . ","
      . $astpp_db->quote( $accountinfo->{address1} ) . ","
      . $astpp_db->quote( $accountinfo->{address2} ) . ","
      . $astpp_db->quote( $accountinfo->{postal_code} ) . ","
      . $astpp_db->quote( $accountinfo->{province} ) . ","
      . $astpp_db->quote( $accountinfo->{city} ) . ","
      . $astpp_db->quote( $accountinfo->{country} ) . ","
      . $astpp_db->quote( $accountinfo->{telephone1} ) . ","
      . $astpp_db->quote( $accountinfo->{telephone2} ) . ","
      . $astpp_db->quote( $accountinfo->{facsimile} ) . ","
      . $astpp_db->quote( $accountinfo->{email} ) . ","
      . $astpp_db->quote( $accountinfo->{language} ) . ","
      . $astpp_db->quote( $accountinfo->{currency} ) . ","
      . $astpp_db->quote( $accountinfo->{reseller} ) . ","
      . $astpp_db->quote( $accountinfo->{accounttype} ) . ")";

    if ( $astpp_db->do($tmp) ) {
        my $status = gettext("Account Added!");
        return $status;
    }
    else {
        my $status = $tmp . gettext("FAILED!");
        return $status;
    }
}

sub add_reseller() {
    my ( $astpp_db, $config, $enh_config, $name, $posttoexternal ) = @_;
    my ( $resellerlist, $tmp, $sql, $status );
    my $configfile = $enh_config->{astpp_dir} . "/astpp-" . $name . "-config.conf";
    $sql =
      $astpp_db->prepare(
        "SELECT name FROM resellers WHERE name = " . $astpp_db->quote($name) );
    $sql->execute;
    $resellerlist = $sql->fetchrow_hashref;
    if ( $resellerlist->{name} eq "" ) {
        $tmp =
"INSERT INTO resellers (name,status,config_file,posttoexternal) VALUES ("
          . $astpp_db->quote($name) . ", 1,"
          . $astpp_db->quote($configfile) . ","
          . $astpp_db->quote($posttoexternal) . ")";
        if ( $astpp_db->do($tmp) ) {
            $status .=
              gettext("Reseller") . " '$name' " . gettext("has been created");
            system(
"cp $enh_config->{astpp_dir}/sample.reseller-config.conf $configfile"
            );
        }
        else {
            $status .=
                gettext("Reseller")
              . " '$name' "
              . gettext("FAILED to create!")
              . " ($tmp)";
        }
    }
    else {
        $astpp_db->do( "UPDATE resellers SET status = 1 WHERE name = "
              . $astpp_db->quote($name) );
        $status .=
          gettext("Reseller") . " '$name' " . gettext("has been reactivated.");
        system("mv $enh_config->{astpp_dir}/$name-config.conf.old $configfile");
    }
    return $status;
}

sub get_trunk() {
    my ( $astpp_db, $trunk ) = @_;
    my ( $sql, $trunkdata, $dialstring );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM trunks WHERE name = " . $astpp_db->quote($trunk) );
    $sql->execute;
    $trunkdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $trunkdata;
}

sub get_dial_string() {
    my ( $astpp_db, $route, $phone ) = @_;
    my ( $sql, $trunkdata, $dialstring );
    $sql =
      $astpp_db->prepare( "SELECT * FROM trunks WHERE name = "
          . $astpp_db->quote( $route->{trunk} ) );
    $sql->execute;
    $trunkdata = $sql->fetchrow_hashref;
    $route->{prepend} = "" if !$route->{prepend};
    $sql->finish;
    if ( $trunkdata->{tech} eq "Local" ) {
        $dialstring = "Local/"
          . $route->{prepend}
          . $phone . "\@"
          . $trunkdata->{path} . "/n";
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "IAX2" ) {
        $dialstring =
          "IAX2/" . $trunkdata->{path} . "/" . $route->{prepend} . $phone;
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "Zap" ) {
        $dialstring =
          "Zap/" . $trunkdata->{path} . "/" . $route->{prepend} . $phone;
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "SIP" ) {
        $dialstring =
          "SIP/" . $route->{prepend} . $phone . "\@" . $trunkdata->{path};
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "OH323" ) {
        $dialstring =
          "OH323/" . $trunkdata->{path} . "/" . $route->{prepend} . $phone;
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "OOH323C" ) {
        $dialstring =
          "OOH323C/" . $route->{prepend} . $phone . "\@" . $trunkdata->{path};
        return $dialstring;
    }
    elsif ( $trunkdata->{tech} eq "H323" ) {
        $dialstring =
          "H323/" . $route->{prepend} . $phone . "\@" . $trunkdata->{path};
        return $dialstring;
    }
}

sub get_outbound_routes() {
    my ( $astpp_db, $number ) = @_;
    my ( @routelist, $record, $sql );
    $sql =
      $astpp_db->prepare( "SELECT * FROM outbound_routes WHERE "
          . $astpp_db->quote($number)
          . " RLIKE pattern AND status = 1 ORDER by LENGTH(pattern) DESC, cost"
      );
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @routelist, $record;
    }
    $sql->finish;
    return @routelist;
}

sub email_refill_account() {
    my ( $astpp_db,$reseller,$config, $vars, $enh_config ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'VOIP Account Refilled',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your VOIP account with $config->{company_name} has been refilled.\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_reactivate_account() {
    my ( $astpp_db,$reseller,$config, $vars, $enh_config ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'VOIP Account Reactivated',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your VOIP account with $config->{company_name} has been reactivated.\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_add_user() {
    my ( $astpp_db,$reseller,$config, $vars, $enh_config ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'VOIP Account Created',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your VOIP account with $config->{company_name} has been added.\n"
      . "Your Username is -- $vars->{extension} --.\n"
      . "Your Password is -- $vars->{secret} --.\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_add_device() {
    my ( $astpp_db,$reseller, $config, $vars, $enh_config ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'VOIP Account Created',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "A new device has been enabled on your account. Here\n"
      . "is the necessary configuration information.\n\n"
      . "-------  $config->{company_name} Configuration Info --------\n";

    if ( $vars->{type} eq "SIP" ) {
        $mail{'Message : '} .=
            "In sip.conf:\n"
          . "[$config->{company_name}-in]\n"
          . "type=user\n"
          . "username=$config->{company_name}-in\n"
          . "auth=rsa\n"
          . "inkeys=$config->{key} ;This key may be downloaded from $config->{key_home}\n"
          . "host=$config->{asterisk_server}\n"
          . "context=from-pstn\n"
          . "accountcode=$config->{company_name}  	;for call tracking in the cdr\n\n"
          . "\[$config->{company_name}\]\n"
          . ";to simplify and config outgoing calls\n"
          . "type=peer\n"
          . "username=$vars->{extension}\n"
          . "secret=$vars->{secret}\n"
          . "host=$config->{asterisk_server}\n"
          . "callerid=\"Some name\" \<555-555-5555\>   ;only the number will really be used\n"
          . "qualify=yes\n"
          . "accountcode=$config->{company_name}   ; for call tracking in the cdr\n\n\n"
          . "In the [globals] section add:\n"
          . "register \=\> $vars->{user}:password\@$config->{asterisk_server}\n";
    }
    if ( $vars->{type} eq "IAX" ) {
        $mail{'Message : '} .=
            "In iax.conf:\n"
          . "At the bottom of the file add:\n"
          . "[$config->{company_name}-in]\n"
          . "; to allow incoming iax2 calls\n"
          . ";trunk=yes   ;optional .. only works if you have a zaptel or ztdummy driver running\n"

          . "type=user\n"
          . "username=$config->{company_name}-in\n"
          . "auth=rsa\n"
          . "inkeys=$config->{key}  ;This key may be downloaded from $config->{key_home}\n"
          . "host=$config->{asterisk_server}\n"
          . "context=incoming\n"
          . "accountcode=$config->{company_name}  	;for call tracking in the cdr\n\n"
          . "\[$config->{company_name}\]\n"
          . ";to simplify and config outgoing calls\n"
          . ";trunk=yes   ;optional .. only works if you have a zaptel driver running\n"
          . "type=peer\n"
          . "username=$vars->{extension}\n"
          . "secret=$vars->{secret}\n"
          . "host=$config->{asterisk_server}\n"
          . "callerid=\"Some name\" \<555-555-5555\>   ;only the number will really be used\n"
          . "qualify=yes\n"
          . "accountcode=$config->{company_name}   ; for call tracking in the cdr\n\n\n";
    }
    $mail{'Message : '} .=
      "Thanks,\n" . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_del_user() {
    my ( $astpp_db,$reseller,$config, $vars, $enh_config ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'VOIP Account Removed',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your VOIP Termination with $config->{company_name} has been removed\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_add_callingcard() {
    my ( $astpp_db,$reseller,$config, $vars, $enh_config, $cc, $pin ) = @_;
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => 'New Calling Card',
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
"You have added a $vars->{pricelist} callingcard in the amount of $vars->{pennies} cents. \n\n"
      . "Card Number $cc Pin: $pin "
      . "Thanks for your patronage."
      . "Thanks,\n"
      . "The $config->{company_name} sales team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;

}

sub email_add_did() {
    my ( $astpp_db, $reseller,$vars, $did, $config, $enh_config, $email ) = @_;
    $email = $config->{company_email} if ( $email eq "" );
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "DID: $did added to your account",
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your DID with $config->{company_name} has been added\n"
      . "The number is: $did\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n"
      . "Here is a sample setup which would call a few sip phones with incoming calls:\n\n"
      . "[incoming]\n"
      . "exten => _1$did,1,Wait(2)\n"
      . "exten => _1$did,2,Dial(SIP/2201&SIP/2202,15,Ttm)  ; dial a couple of phones for 15 secs\n"
      . "exten => _1$did,3,Voicemail(u1000)   ; go to unavailable voicemail (vm box 1000)\n"
      . "exten => _1$did,103,Voicemail(b1000) ; go to busy voicemail (vm box 1000)";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_del_did() {
    my ( $astpp_db, $reseller,$vars, $did, $config, $enh_config, $email ) = @_;
    $email = $config->{company_email} if ( $email eq "" );
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "DID: $did removed from your account",
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
        "Attention: $vars->{title} $vars->{first} $vars->{last}\n"
      . "Your DID with $config->{company_name} has been removed\n"
      . "The number was: $did\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_new_invoice() {
    my ( $astpp_db,$reseller,$config, $enh_config, $email, $invoice, $total ) = @_;
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "Subject: Invoice $invoice Added",
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
"Invoice # $invoice in the amount of \$$total has been added to your account.\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub email_low_balance() {
    my ( $astpp_db, $reseller,$config, $enh_config, $email, $balance ) = @_;
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "$config->{company_name} Low Balance Alert",
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} =
"Your VOIP account with $config->{company_name} has a balance of \$$balance .\n"
      . "Please visit our website to refill your account to ensure uninterrupted service.\n"
      . "For information please visit $config->{company_website} or \n"
      . "contact our support department at $config->{company_email}\n"
      . "Thanks,\n"
      . "The $config->{company_name} support team\n\n";
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

sub timestamp() {
    my $now = strftime "%Y%m%d%H%M%S", gmtime;
    return $now;
}

sub prettytimestamp() {
    my $now = strftime "%Y-%m-%d %H:%M:%S", gmtime;
    return $now;
}

sub debug() {
    my ( $input, @output ) = @_;
    require Data::Dumper;
    print Data::Dumper->Dump( [$input] );
}

sub agile_connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dsn, $dbh, $handle );
    if ( $enh_config->{agile_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$enh_config->{agile_db};host=$enh_config->{agile_host}";
    }
    elsif ( $enh_config->{agile_dbengine} eq "Pgsql" ) {
        $dsn =
"DBI:Pg:database=$enh_config->{agile_db};host=$enh_config->{agile_host}";
    }
    $dbh =
      DBI->connect( $dsn, $enh_config->{agile_user},
        $enh_config->{agile_pass} );
    if ( !$dbh ) {
        print STDERR "AGILE DATABASE IS DOWN\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to Agilebill Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub create_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ($drh);
    if ( $enh_config->{astpp_dbengine} eq "MySQL" ) {
        $drh = DBI->install_driver("mysql");
        if ( !$drh ) {
            print STDERR "ASTPP DEBUG\n";
            print STDERR "COULD NOT INSTALL DATABASE DRIVER!\n";
            return 1;
        }
        if (
            !$drh->func(
                'createdb',        $config->{dbname},
                $config->{dbhost}, $config->{dbuser},
                $config->{dbpass}, 'admin'
            )
          )
        {
            print STDERR "ASTPP DEBUG\n";
            print STDERR "COULD NOT CREATE DATABASE!\n";
            print STDERR "DATABASE: $config->{dbname}\n";
            print STDERR "HOST:     $config->{dbhost}\n";
            print STDERR "USERNAME: $config->{dbuser}\n";
            print STDERR "PASSWORD: $config->{dbpass}\n";
        }
        else {
            return 0;
        }
    }
    elsif ( $enh_config->{astpp_dbengine} eq "Pgsql" ) {
        $drh = DBI->install_driver("Pg");
        if ( !$drh ) {
            print STDERR "ASTPP DEBUG\n";
            print STDERR "COULD NOT INSTALL DATABASE DRIVER!\n";
            return 1;
        }
        if (
            !$drh->func(
                'createdb',        $config->{dbname}, $config->{dbhost},
                $config->{dbuser}, $config->{dbpass}
            )
          )
        {
            print STDERR "ASTPP DEBUG\n";
            print STDERR "COULD NOT CREATE DATABASE!\n";
            print STDERR "DATABASE: $config->{dbname}\n";
            print STDERR "HOST:     $config->{dbhost}\n";
            print STDERR "USERNAME: $config->{dbuser}\n";
            print STDERR "PASSWORD: $config->{dbpass}\n";
        }
        else {
            return 0;
        }
    }
}

sub load_config_db() {
    my ($astpp_db, $config) = @_;
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}

sub load_config_db_reseller() {
    my ($astpp_db, $config,$reseller) = @_;
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system WHERE reseller = " . $astpp_db->quote($reseller);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}


sub populate_db() {
    my ($astpp_db) = @_;
    my $now = &timestamp();
    return -1
      unless $astpp_db->do(
"CREATE TABLE routes (pattern CHAR(40), id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
comment CHAR(80), connectcost INTEGER NOT NULL, includedseconds INTEGER NOT NULL,
cost INTEGER NOT NULL, pricelist CHAR(80), inc INTEGER, reseller CHAR(50) default NULL,
status INTEGER NOT NULL DEFAULT 1)"
      );
    return -1
	unless $astpp_db->do(
"CREATE TABLE configuration (reseller CHAR(50) default NULL, `key` CHAR(50) NOT NULL, value CHAR(50) default NULL)");
    return -1
      unless $astpp_db->do(
"CREATE TABLE pricelists (name CHAR(40) PRIMARY KEY, markup INTEGER NOT NULL DEFAULT 0, inc INTEGER NOT NULL DEFAULT 0, "
          . "status INTEGER DEFAULT 1 NOT NULL, reseller CHAR(50) default NULL)" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE callingcardbrands (name CHAR(40) PRIMARY KEY, language CHAR(10) NOT NULL DEFAULT '', "
          . "pricelist CHAR(40) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, validfordays CHAR(4) NOT NULL DEFAULT '', "
          . "pin INTEGER NOT NULL DEFAULT 0, maint_fee_pennies INTEGER NOT NULL DEFAULT 0, "
          . "maint_fee_days INTEGER NOT NULL DEFAULT 0, disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0, "
          . "minute_fee_minutes INTEGER NOT NULL DEFAULT 0, minute_fee_pennies INTEGER NOT NULL DEFAULT 0)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE callingcardcdrs (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, cardnumber CHAR(50) NOT NULL DEFAULT '', "
          . "clid CHAR(80) NOT NULL DEFAULT '', destination CHAR(40) NOT NULL DEFAULT '', disposition CHAR(20)NOT NULL DEFAULT '', "
          . "callstart CHAR(40) NOT NULL DEFAULT '', seconds INTEGER NOT NULL DEFAULT 0, debit DECIMAL(20,6) NOT NULL DEFAULT 0, "
          . "credit DECIMAL(20,6) NOT NULL DEFAULT 0, status INTEGER DEFAULT 0 NOT NULL, notes CHAR(80) NOT NULL DEFAULT '')"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE trunks (name VARCHAR(30) PRIMARY KEY, tech CHAR(10) NOT NULL DEFAULT '', path CHAR(40) NOT NULL DEFAULT '', "
          . "provider CHAR(100) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, maxchannels INTEGER DEFAULT 1 NOT NULL)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE outbound_routes (pattern CHAR(40), id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, "
          . "comment CHAR(80) NOT NULL DEFAULT '', connectcost INTEGER NOT NULL DEFAULT 0, "
          . "includedseconds INTEGER NOT NULL DEFAULT 0, cost INTEGER NOT NULL DEFAULT 0, trunk CHAR(80) NOT NULL DEFAULT '', "
          . "inc CHAR(10) NOT NULL DEFAULT '', strip CHAR(40) NOT NULL DEFAULT '', prepend CHAR(40) NOT NULL DEFAULT '', "
          . "status INTEGER DEFAULT 1 NOT NULL)" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE dids (number CHAR(40) NOT NULL PRIMARY KEY, account CHAR(50) NOT NULL DEFAULT '', connectcost INTEGER NOT NULL DEFAULT 0, "
          . "includedseconds INTEGER NOT NULL DEFAULT 0, monthlycost INTEGER NOT NULL DEFAULT 0, cost INTEGER NOT NULL DEFAULT 0, extensions CHAR(180) NOT NULL DEFAULT '', "
          . "status INTEGER DEFAULT 1 NOT NULL, provider CHAR(40) NOT NULL DEFAULT '', country CHAR (80)NOT NULL DEFAULT '', "
          . "province CHAR (80) NOT NULL DEFAULT '', city CHAR (80) NOT NULL DEFAULT '')"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE accounts (cc CHAR(20) NOT NULL DEFAULT '', number CHAR(50) PRIMARY KEY, reseller CHAR(40) NOT NULL DEFAULT '', "
          . "pricelist CHAR(24) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, credit INTEGER NOT NULL DEFAULT 0, "
          . "sweep INTEGER NOT NULL DEFAULT 0, creation TIMESTAMP, pin INTEGER NOT NULL DEFAULT 0, "
          . "credit_limit INTEGER NOT NULL DEFAULT 0, posttoexternal INTEGER NOT NULL DEFAULT 0, "
          . "balance DECIMAL(20,6) NOT NULL DEFAULT 0, password CHAR(80) NOT NULL DEFAULT '', "
          . "first_name CHAR(40) NOT NULL DEFAULT '', middle_name CHAR(40) NOT NULL DEFAULT '', "
          . "last_name CHAR(40) NOT NULL DEFAULT '', company_name CHAR(40) NOT NULL DEFAULT '', "
          . "address_1 CHAR(80) NOT NULL DEFAULT '', address_2 CHAR(80) NOT NULL DEFAULT '', "
          . "address_3 CHAR(80) NOT NULL DEFAULT '', postal_code CHAR(12) NOT NULL DEFAULT '', "
          . "province CHAR(40) NOT NULL DEFAULT '', city CHAR(80) NOT NULL DEFAULT '', country CHAR(40) NOT NULL DEFAULT '', "
          . "telephone_1 CHAR(40) NOT NULL DEFAULT '', telephone_2 CHAR(40) NOT NULL DEFAULT '', fascimile CHAR(40) NOT NULL DEFAULT '', "
          . "email CHAR(80) NOT NULL DEFAULT '', language CHAR(2) NOT NULL DEFAULT '',"
          . "currency CHAR(3) NOT NULL DEFAULT '', maxchannels INTEGER DEFAULT 1 NOT NULL, type INTEGER DEFAULT 0)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE counters (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, package CHAR(40) NOT NULL DEFAULT '', "
          . "account VARCHAR(50) NOT NULL, seconds INTEGER NOT NULL DEFAULT 0)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE callingcards (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, cardnumber CHAR(20) NOT NULL DEFAULT '', "
          . "language CHAR(10) NOT NULL DEFAULT '', value INTEGER NOT NULL DEFAULT 0, used INTEGER NOT NULL DEFAULT 0, "
          . "brand VARCHAR(20) NOT NULL DEFAULT '', created DATETIME, firstused DATETIME, expiry DATETIME, "
          . "validfordays CHAR(4) NOT NULL DEFAULT '', inuse INTEGER NOT NULL DEFAULT 0, pin CHAR(20), "
          . "account VARCHAR(50) NOT NULL DEFAULT '', maint_fee_pennies INTEGER NOT NULL DEFAULT 0, "
          . "maint_fee_days INTEGER NOT NULL DEFAULT 0, maint_day INTEGER NOT NULL DEFAULT 0, "
          . "disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0, minute_fee_minutes INTEGER NOT NULL DEFAULT 0, "
          . "minute_fee_pennies INTEGER NOT NULL DEFAULT 0, timeused INTEGER NOT NULL DEFAULT 0, "
          . "invoice CHAR(20) NOT NULL DEFAULT 0, status INTEGER DEFAULT 1 NOT NULL)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE charge_to_account (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, charge_id INTEGER NOT NULL DEFAULT 0,"
          . "cardnum CHAR(50) NOT NULL DEFAULT '', status INTEGER NOT NULL DEFAULT 1)"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE queue_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, queue_id INTEGER NOT NULL DEFAULT 0,"
          . "cardnum CHAR(20) NOT NULL DEFAULT '')" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE pbx_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, pbx_id INTEGER NOT NULL DEFAULT 0,"
          . "cardnum CHAR(20) NOT NULL DEFAULT '')" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE extension_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, extension_id INTEGER NOT NULL DEFAULT 0,"
          . "cardnum CHAR(20) NOT NULL DEFAULT '')" );
    return -1
      unless $astpp_db->do("ALTER TABLE outbound_routes ADD INDEX (trunk)");
    return -1
      unless $astpp_db->do("ALTER TABLE accounts ADD INDEX (pricelist)");
    $astpp_db->do(
"CREATE TABLE cdrs (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, uniqueid varchar(32) NOT NULL DEFAULT '', "
          . "cardnum CHAR(50), callerid CHAR(80), callednum varchar(80) NOT NULL DEFAULT '', billseconds INT DEFAULT 0 NOT NULL, trunk VARCHAR(30), "
          . "disposition varchar(45) NOT NULL DEFAULT '', callstart varchar(80) NOT NULL DEFAULT '', "
          . "debit DECIMAL (20,6) NOT NULL DEFAULT 0, credit DECIMAL (20,6) NOT NULL DEFAULT 0, "
          . "status INTEGER DEFAULT 0 NOT NULL, notes CHAR(80), provider CHAR(50), cost DECIMAL(20,6) NOT NULL DEFAULT 0)"
    );
    return -1
      unless $astpp_db->do(
"CREATE TABLE resellers (name CHAR(50) PRIMARY KEY, status INTEGER DEFAULT 1 NOT NULL, "
          . "posttoexternal INTEGER NOT NULL DEFAULT 0, agile_site_id INTEGER NOT NULL DEFAULT 0, "
          . "config_file CHAR(80) NOT NULL DEFAULT '')" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE packages (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, name CHAR(40) NOT NULL DEFAULT '', "
          . "pricelist CHAR(40) NOT NULL DEFAULT '', pattern CHAR(40) NOT NULL DEFAULT '', includedseconds INTEGER, "
          . "status INTEGER DEFAULT 1 NOT NULL)" );
    return -1
      unless $astpp_db->do(
"CREATE TABLE ani_map (number CHAR(20) NOT NULL PRIMARY KEY, account CHAR(50) NOT NULL DEFAULT '', status INTEGER DEFAULT 0 NOT NULL)"
      );

    return -1
      unless $astpp_db->do(
"CREATE TABLE ip_map ("
. "ip char(15) NOT NULL default '', "
. "account char(20) NOT NULL default '', "
. "PRIMARY KEY (`ip`) )"
      );
    return -1
      unless $astpp_db->do(
"CREATE TABLE charges ("
. "id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, "
. "pricelist CHAR(40) NOT NULL DEFAULT '', "
. "description VARCHAR(80) NOT NULL DEFAULT '', "
. "charge INTEGER NOT NULL DEFAULT 0, "
. "sweep INTEGER NOT NULL DEFAULT 0, "
. "status INTEGER NOT NULL DEFAULT 1)" );
   return -1 
      unless $astpp_db->do(
"CREATE TABLE system ( "
. "name VARCHAR(48) NULL, "
. "value VARCHAR(255) NULL, "
. "comment VARCHAR(255) NULL, "
. "timestamp DATETIME NULL, "
. "PRIMARY KEY (`name`)");
	# Here we insert some default values into the new 'system' table which 
	# stores system wide configuration.
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'callout_accountcode', "
      . "'admin',"
      . "'Call Files: What accountcode should we use?', "
      . $astpp_db->quote($now) . ")");    
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'lcrcontext', "
      . "'astpp-outgoing',"
      . "'This is the Local context we use to route our outgoing calls through esp for callbacks', "
      . $astpp_db->quote($now) . ")");  
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'maxretries', "
      . "'3',"
      . "'Call Files: How many times do we retry?', "
      . $astpp_db->quote($now) . ")");        
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'retrytime', "
      . "'30',"
      . "'Call Files: How long do we wait between retries?', "
      . $astpp_db->quote($now) . ")");        
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'waittime', "
      . "'15',"
      . "'Call Files: How long do we wait before the initial call?', "
      . $astpp_db->quote($now) . ")");  
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'clidname', "
      . "'Private',"
      . "'Call Files: Outgoing CallerID Name', "
      . $astpp_db->quote($now) . ")");  
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'clidnumber', "
      . "'0000000000',"
      . "'Call Files: Outgoing CallerID Number', "
      . $astpp_db->quote($now) . ")");                    
#	return -1
#		unless $astpp_db->do(
#"INSERT INTO system (name, value, comment, timestamp) VALUES ("
#      . "'', "
#      . "'',"
#      . "'', "
#      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'callingcards_callback_context', "
      . "'astpp-callingcards',"
      . "'Call Files: For callingcards what context do we end up in?', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'callingcards_callback_extension', "
      . "'s',"
      . "'Call Files: For callingcards what extension do we use?', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'', "
      . "'',"
      . "'', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'', "
      . "'',"
      . "'', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'', "
      . "'',"
      . "'', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'', "
      . "'',"
      . "'', "
      . $astpp_db->quote($now) . ")"); 
	return -1
		unless $astpp_db->do(
"INSERT INTO system (name, value, comment, timestamp) VALUES ("
      . "'', "
      . "'',"
      . "'', "
      . $astpp_db->quote($now) . ")");                                           
      
    return 0;
}

sub connect_openserdb() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dbh, $handle, $dsn );
    if ( $config->{openser_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{openser_dbname};host=$config->{openser_dbhost}";
    }
    elsif ( $enh_config->{openser_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{openser_dbname};host=$config->{openser_dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{openser_dbuser}, $config->{openser_dbpass} );
    if ( !$dbh ) {
        print STDERR "OPENSER DATABASE IS DOWN\n";
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to OPENSER Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}


sub connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dbh, $handle, $dsn );
    if ( $enh_config->{astpp_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{dbname};host=$config->{dbhost}";
    }
    elsif ( $enh_config->{astpp_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{dbname};host=$config->{dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{dbuser}, $config->{dbpass} );
    if ( !$dbh ) {
        print STDERR "ASTPP DATABASE IS DOWN\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to ASTPP Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub freepbx_connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dbh, $handle, $dsn );
    if ( $enh_config->{freepbx_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$enh_config->{freepbx_db};host=$enh_config->{freepbx_host}";
    }
    elsif ( $enh_config->{freepbx_dbengine} eq "Pgsql" ) {
        $dsn =
"DBI:Pg:database=$enh_config->{freepbx_db};host=$enh_config->{freepbx_host}";
    }
    $dbh = DBI->connect(
        $dsn,
        $enh_config->{freepbx_user},
        $enh_config->{freepbx_pass}
    );
    if ( !$dbh ) {
        print STDERR "FREEPBX DATABASE IS DOWN\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to FreePBX Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub cdr_connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dsn, $dbh, $handle );
    if ( $enh_config->{cdr_dbengine} eq "MySQL" ) {
        $dsn =
          "DBI:mysql:database=$config->{cdr_dbname};host=$config->{cdr_dbhost}";
    }
    elsif ( $enh_config->{cdr_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$config->{cdr_dbname};host=$config->{cdr_dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{cdr_dbuser}, $config->{cdr_dbpass} );
    if ( !$dbh ) {
        print STDERR "CDR DATABASE IS DOWN\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to CDR Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub rt_connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dsn, $dbh, $handle );
    if ( $enh_config->{rt_dbengine} eq "MySQL" ) {
        $dsn =
          "DBI:mysql:database=$enh_config->{rt_db};host=$enh_config->{rt_host}";
    }
    elsif ( $enh_config->{rt_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$enh_config->{rt_db};host=$enh_config->{rt_host}";
    }
    $dbh->disconnect if $dbh;
    $dbh = DBI->connect( $dsn, $enh_config->{rt_user}, $enh_config->{rt_pass} );
    if ( !$dbh ) {
        print STDERR gettext("Asterisk Realtime DATABASE IS DOWN!") . "\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to Realtime Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub osc_connect_db() {
    my ( $config, $enh_config, @output ) = @_;
    my ( $dsn, $dbh, $handle );
    if ( $enh_config->{osc_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$enh_config->{osc_db};host=$enh_config->{osc_host}"
          ,;
    }
    elsif ( $enh_config->{osc_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$enh_config->{osc_db};host=$enh_config->{osc_host}",;
    }
    $dbh->disconnect if $dbh;
    $dbh =
      DBI->connect( $dsn, $enh_config->{osc_user}, $enh_config->{osc_pass} );
    if ( !$dbh ) {
        print STDERR gettext("OSCOMMERCE DATABASE IS DOWN") . "\n";
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to OSCommerce Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

sub calc_call_cost() {
    my ( $connect, $cost, $answeredtime, $increment, $inc_seconds ) = @_;
    print STDERR "Connect: $connect Cost: $cost Answered: $answeredtime \n";
    print STDERR " Inc: $increment included: $inc_seconds \n";
    my $total_seconds = ( $answeredtime - $inc_seconds ) / $increment;
    if ( $total_seconds < 0 ) {
        $total_seconds = 0;
    }
    my $bill_increments = ceil($total_seconds);
    my $billseconds     = $bill_increments * $increment;
    $cost = ( $billseconds / 60 ) * $cost + $connect;
    print STDERR "AnsweredTime: $answeredtime Included Sec: $inc_seconds\n";
    print STDERR "Increment: $increment Total Increments: $total_seconds\n";
    print STDERR "Bill Seconds: $billseconds  Total cost is $cost\n";
    return $cost;
}

sub list_trunks() {
    my ($astpp_db) = @_;
    my ( $sql, @trunklist, $record );
    $sql = $astpp_db->prepare("SELECT name FROM trunks WHERE status = 1");
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @trunklist, $record->{name};
    }
    $sql->finish;
    return @trunklist;
}

sub purchase_did() {
	my ($astpp_db,$did,$account) = @_;
	my $tmp =
            "UPDATE dids SET account = "
          . $astpp_db->quote($account)
          . " WHERE number = "
          . $astpp_db->quote($did);
        $astpp_db->do($tmp);
}

sub list_providers() {
    my ($astpp_db) = @_;
    my ( $sql, @providerlist, $record );
    $sql =
      $astpp_db->prepare(
        "SELECT number FROM accounts WHERE status = 1 AND type = 3");
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @providerlist, $record->{number};
    }
    $sql->finish;
    return @providerlist;
}

sub list_accounts_selective() {
    my ( $astpp_db, $reseller, $type ) = @_;
    my ( $sql, @accountlist, $row, $tmp );
    if ( $type == -1 ) {
        $tmp =
            "SELECT number FROM accounts WHERE status < 2 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR $tmp;
    }
    elsif ( $type == 0 || !$type ) {
        $tmp =
"SELECT number FROM accounts WHERE status < 2 AND type = 0 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR $tmp;
    }
    elsif ( $type > 0 ) {
        $tmp =
"SELECT number FROM accounts WHERE status < 2 AND type = '$type' AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR $tmp;
    }
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}

sub list_accounts() {
    my ( $astpp_db, $reseller ) = @_;
    my ( $sql, @accountlist, $row, $tmp );
    if ( !$reseller ) {
        $reseller = "";
    }
    $tmp =
        "SELECT number FROM accounts WHERE status < 2 AND reseller = "
      . $astpp_db->quote($reseller)
      . " ORDER BY number";
    print STDERR $tmp;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}

sub list_pricelists() {
    my ( $astpp_db, $reseller ) = @_;
    my ( $sql, @pricelistlist, $row, $tmp );
    if ( !$reseller ) {
        $tmp =
"SELECT name FROM pricelists WHERE status < 2 AND reseller IS NULL ORDER BY name";
    }
    else {
        $tmp =
            "SELECT name FROM pricelists WHERE status < 2 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY name";
    }
############DEBUG
    print STDERR $tmp;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @pricelistlist, $row->{name};
    }
    $sql->finish;
    return @pricelistlist;
}

sub get_astpp_cdr() {
    my ( $astpp_db, $id ) = @_;
    my ( $sql, $cdrdata );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM cdrs WHERE id = " . $astpp_db->quote($id) );
    $sql->execute;
    $cdrdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $cdrdata;
}

sub get_cdr() {
    my ( $cdr_db, $uniqueid ) = @_;
    my ( $sql, $cdrdata );
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM cdr WHERE uniqueid = " . $cdr_db->quote($uniqueid) );
    $sql->execute;
    $cdrdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $cdrdata;
}

sub save_ast_cdr() {
    my ( $cdr_db, $uniqueid, $cost ) = @_;
    $cdr_db->do( "UPDATE cdr SET cost = "
          . $cdr_db->quote($cost)
          . "WHERE uniqueid = "
          . $cdr_db->quote($uniqueid) );
}

sub list_cdrs_status() {
    my ( $cdr_db, $default ) = @_;
    my ( $sql, @cdrlist, $row );
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM cdr WHERE cost = " . $cdr_db->quote($default) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cdrlist, $row->{uniqueid};
    }
    return @cdrlist;
}

sub save_ast_cdr_vendor() {
    my ( $cdr_db, $uniqueid, $cost ) = @_;
    $cdr_db->do( "UPDATE cdr SET vendor = "
          . $cdr_db->quote($cost)
          . "WHERE uniqueid = "
          . $cdr_db->quote($uniqueid) );
}

sub list_cdrs_status_vendor() {
    my ( $cdr_db, $default ) = @_;
    my ( $sql, @cdrlist, $row );
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM cdr WHERE vendor = " . $cdr_db->quote($default) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cdrlist, $row->{uniqueid};
    }
    return @cdrlist;
}

sub get_did() {
    my ( $astpp_db, $did ) = @_;
    my ( $sql, $diddata );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM dids WHERE number = " . $astpp_db->quote($did) );
    $sql->execute;
    $diddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $diddata;
}

sub update_astpp_balance() {
    my ( $astpp_db, $account, $balance ) = @_;
    $astpp_db->do( "UPDATE accounts SET balance = "
          . $astpp_db->quote($balance)
          . " WHERE number = "
          . $astpp_db->quote($account) );
}

sub get_account_including_closed() {
    my ( $astpp_db, $accountno ) = @_;
    my ( $sql, $accountdata );
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE number = "
          . $astpp_db->quote($accountno));
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE cc = "
          . $astpp_db->quote($accountno));
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
    }
}


sub get_account() {
    my ( $astpp_db, $accountno ) = @_;
    my ( $sql, $accountdata );
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE number = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE cc = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
    }
}

sub get_account_cc() {
    my ( $astpp_db, $accountno ) = @_;
    my ( $sql, $accountdata );
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE cc = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
}

sub get_pricelist() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, $pricelistdata, $tmp );
    $tmp =
      "SELECT * FROM pricelists WHERE name = " . $astpp_db->quote($pricelist);
    $sql = $astpp_db->prepare($tmp);
    print STDERR $tmp . "\n";
    $sql->execute;
    $pricelistdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $pricelistdata;
}

sub get_cc_brand() {
    my ( $astpp_db, $brand ) = @_;
    my ( $sql, $tmp, $branddata );
    $tmp =
        "SELECT * FROM callingcardbrands WHERE name = "
      . $astpp_db->quote($brand)
      . " AND status = 1";
    print STDERR $tmp . "\n";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $branddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $branddata;
}

sub get_route() {
    my ( $astpp_db, $config, $destination, $pricelist,$carddata ) = @_;
    my ( $record,   $sql,    $tmp );

    #    my @pricelists = split ( /|/, $pricelist );
    #    $tmp = "SELECT * FROM routes WHERE"
    #        . $astpp_db->quote($destination)
    #	. " RLIKE pattern AND pricelist IN (";
    #	my $count = 0;
    #	my $totcount = 0;
    #	foreach my $pricelistname (@pricelists) {
    #		$totcount++;
    #	}
    #    foreach my $pricelistname (@pricelists) {
    #   			$count++;
    #			$tmp .= $astpp_db->quote($pricelistname);
    #			$tmp .= ", " if $count < $totcount;
    #	}
    #    $tmp .= ") ORDER BY LENGTH(pattern) DESC,cost LIMIT 1";
    #    $sql = $astpp_db->prepare($tmp);

    $sql =
      $astpp_db->prepare( "SELECT * FROM routes WHERE "
          . $astpp_db->quote($destination)
          . " RLIKE pattern AND pricelist = "
          . $astpp_db->quote($pricelist)
          . " ORDER BY LENGTH(pattern) DESC" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
    while ( !$record->{pattern} && $carddata->{reseller} ) {
	$carddata = &get_account($astpp_db, $carddata->{reseller});	
    	$sql =
	      $astpp_db->prepare( "SELECT * FROM routes WHERE "
	      . $astpp_db->quote($destination)
	      . " RLIKE pattern AND pricelist = "
	      . $astpp_db->quote($carddata->{pricelist})
	      . " ORDER BY LENGTH(pattern) DESC" );
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};

    }

    if ( !$record->{pattern} ) { #If we have not found a route yet then we look in the "Default" pricelist.
        $pricelist = $config->{default_brand};
        $sql       =
          $astpp_db->prepare( "SELECT * FROM routes WHERE "
              . $astpp_db->quote($destination)
              . " RLIKE pattern AND pricelist = "
              . $astpp_db->quote($pricelist)
              . " ORDER BY LENGTH(pattern) DESC" );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
    }
    if ( $record->{inc} ) {
        return $record;
    }
    else {
        my $branddata = &get_pricelist( $astpp_db, $pricelist );
        $record->{inc} = $branddata->{inc};
    }
    return $record;
}

sub get_charge() {
    my ( $astpp_db, $chargeid ) = @_;
    my ( $sql, $chargedata, $tmp );
    $tmp =
        "SELECT * FROM charges WHERE id = "
      . $astpp_db->quote($chargeid)
      . " AND status < 2 LIMIT 1";
    print STDERR $tmp . "\n";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $chargedata = $sql->fetchrow_hashref;
    $sql->finish;
    return $chargedata;
}

sub list_account_charges() {
    my ( $astpp_db, $number ) = @_;
    my ( $sql, @chargelist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM charge_to_account WHERE status < 2 AND cardnum = "
          . $astpp_db->quote($number) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @chargelist, $row;
    }
    return @chargelist;
}

sub list_applyable_charges() {
    my ($astpp_db) = @_;
    my ( $sql, %chargelist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM charges WHERE status < 2 AND pricelist = ''");
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        if ( $row->{charge} > 0 ) {
            $row->{charge} = $row->{charge} / 10000;
            $row->{charge} = sprintf( "%.4f", $row->{charge} );
        }
        $chargelist{ $row->{id} } =
          $row->{description} . " - \$" . $row->{charge};
        print STDERR "CHARGEID: $row->{id}\n";
        print STDERR "CHARGE: %chargelist->{$row->{id}}\n";
        print STDERR "CHARGE: $row->{description} - $row->{charge}\n";
    }
    return %chargelist;
}

sub list_pricelist_charges() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, @chargelist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT id FROM charges WHERE status < 2 AND pricelist = "
          . $astpp_db->quote($pricelist) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @chargelist, $row->{id};
    }
    return @chargelist;
}

sub finduniquecc() {
    my ( $astpp_db, $config, $enh_config ) = @_;
    my ( $cc, $count, $sql, $startingdigit, $record );
    $config = &load_config();
    for ( ; ; ) {
        $count = 1;
        $cc    =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        if (   $config->{startingdigit} ne ""
            && $config->{startingdigit} ne "0" )
        {
            $startingdigit = substr( $cc, 0, 1 );
            if ( $startingdigit == $config->{startingdigit} ) {
                $cc = substr( $cc, 0, $config->{cardlength} );
                $sql =
                  $astpp_db->prepare(
                    "SELECT COUNT(*) FROM accounts WHERE cc = $cc");
                $sql->execute;
                $record = $sql->fetchrow_hashref;
                $count  = $record->{"COUNT(*)"};
                $sql->finish;
            }
        }
        else {
            print STDERR "DEBUG:" . $config->{cardlength} . " " . $cc;
            $cc = substr( $cc, 0, $config->{cardlength} );
            $sql =
              $astpp_db->prepare(
                "SELECT COUNT(*) FROM accounts WHERE cc = $cc");
            $sql->execute;
            $record = $sql->fetchrow_hashref;
            $count  = $record->{"COUNT(*)"};
            $sql->finish;
        }
        return $cc if ( $count == 0 );
    }
}

sub finduniquecallingcard() {
    my ( $astpp_db, $config, $enh_config ) = @_;
    my ( $cc, $count, $startingdigit, $sql, $record );
    for ( ; ; ) {
        $count = 1;
        $cc    =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
        if (   $config->{startingdigit} ne ""
            && $config->{startingdigit} ne "0" )
        {
            $startingdigit = substr( $cc, 0, 1 );
            if ( $startingdigit == $config->{startingdigit} ) {
                $cc = substr( $cc, 0, $config->{cardlength} );
                $sql =
                  $astpp_db->prepare(
                    "SELECT COUNT(*) FROM callingcards WHERE cardnumber = "
                      . $astpp_db->quote($cc) );
                $sql->execute;
                $record = $sql->fetchrow_hashref;
                $count  = $record->{"COUNT(*)"};
                $sql->finish;
            }
        }
        else {
            $cc = substr( $cc, 0, $config->{cardlength} );
            $sql =
              $astpp_db->prepare(
                "SELECT COUNT(*) FROM callingcards WHERE cardnumber = "
                  . $astpp_db->quote($cc) );
            $sql->execute;
            $record = $sql->fetchrow_hashref;
            $count  = $record->{"COUNT(*)"};
            $sql->finish;
        }
        return $cc if ( $count == 0 );
    }
}

sub refill_account() {
    my ( $astpp_db, $account, $amount ) =
      @_;    # The amount shall be passed in 100ths of a penny.
    my ( $sql, $status );
    my $description = gettext("Refill Account");
    my $uniqueid    = gettext("N/A");
    my $timestamp   = &prettytimestamp;
    my $tmp         =
"INSERT INTO cdrs (uniqueid, cardnum, callednum, credit, callstart) VALUES ("
      . $astpp_db->quote($uniqueid) . ", "
      . $astpp_db->quote($account) . ","
      . $astpp_db->quote($description) . ", "
      . $astpp_db->quote($amount) . ", "
      . $astpp_db->quote($timestamp) . ")";
    print STDERR $tmp;
    if ( $astpp_db->do($tmp) ) {
        $status =
            gettext("Refilled account:")
          . " $account "
          . gettext("in the amount of:")
          . $amount / 10000 . "\n";
        return $status;
    }
    else {
        $status = "$tmp " . gettext("FAILED!");
        return $status;
    }
}

sub write_account_cdr() {
    my ( $astpp_db, $account, $amount, $description, $timestamp, $answeredtime )
      = @_;    # The amount shall be passed in 100ths of a penny.
    my ( $sql, $status );
    $description  = ""  if !$timestamp;
    $answeredtime = "0" if !$answeredtime;
    my $uniqueid = "N/A";
    $timestamp = &prettytimestamp if !$timestamp;
    my $tmp =
"INSERT INTO cdrs (uniqueid, cardnum, callednum, debit, billseconds, callstart) VALUES ("
      . $astpp_db->quote($uniqueid) . ", "
      . $astpp_db->quote($account) . ","
      . $astpp_db->quote($description) . ", "
      . $astpp_db->quote($amount) . ", "
      . $astpp_db->quote($answeredtime) . ", "
      . $astpp_db->quote($timestamp) . ")";
    if ( $astpp_db->do($tmp) ) {
        $status =
          "POSTED CDR: $account in the amount of: " . $amount / 10000 . "\n";
        return $status;
    }
    else {
        $status = "$tmp FAILED!";
        return $status;
    }
}

sub accounts_total_balance() {
    my ($astpp_db) = @_;
    my ( $tmp, $sql, $row, $debit, $credit, $balance, $posted_balance );
    $tmp = "SELECT SUM(debit) FROM cdrs WHERE status NOT IN (1, 2)";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row   = $sql->fetchrow_hashref;
    $debit = $row->{"SUM(debit)"};
    $tmp   = "SELECT SUM(credit) FROM cdrs WHERE status NOT IN (1, 2)";
    $sql   = $astpp_db->prepare($tmp);
    $sql->execute;
    $row   = $sql->fetchrow_hashref;
    $debit = $row->{"SUM(credit)"};
    $tmp   = "SELECT SUM(balance) FROM accounts";
    $sql   = $astpp_db->prepare($tmp);
    $sql->execute;
    $row            = $sql->fetchrow_hashref;
    $posted_balance = $row->{"SUM(balance)"};
    $sql->finish;
    if ( !$credit )         { $credit         = 0; }
    if ( !$debit )          { $debit          = 0; }
    if ( !$posted_balance ) { $posted_balance = 0; }
    $balance = ( $debit - $credit + $posted_balance );
    return $balance;
}

sub accountbalance() {
    my ( $astpp_db, $account ) = @_;
    my ( $tmp, $sql, $row, $debit, $credit, $balance, $posted_balance );
    $tmp =
        "SELECT SUM(debit) FROM cdrs WHERE cardnum= "
      . $astpp_db->quote($account)
      . " AND status NOT IN (1, 2)";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row   = $sql->fetchrow_hashref;
    $debit = $row->{"SUM(debit)"};
    $sql->finish;
    $tmp =
        "SELECT SUM(credit) FROM cdrs WHERE cardnum= "
      . $astpp_db->quote($account)
      . " AND status NOT IN (1, 2)";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row    = $sql->fetchrow_hashref;
    $credit = $row->{"SUM(credit)"};
    $sql->finish;
    $tmp =
      "SELECT * FROM accounts WHERE number = " . $astpp_db->quote($account);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row            = $sql->fetchrow_hashref;
    $posted_balance = $row->{balance};
    $sql->finish;
    if ( !$credit )         { $credit         = 0; }
    if ( !$debit )          { $debit          = 0; }
    if ( !$posted_balance ) { $posted_balance = 0; }
    $balance = ( $debit - $credit + $posted_balance );
    return $balance;
}

sub list_pricelist_accounts() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, @accountlist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT number FROM accounts WHERE status < 2 AND pricelist = "
          . $astpp_db->quote($pricelist) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    return @accountlist;
}

sub list_dids_account() {
    my ( $astpp_db, $account ) = @_;
    my ( $sql, @didlist, $row );
    $sql =
      $astpp_db->prepare( "SELECT * FROM dids WHERE status = 1 AND account = "
          . $astpp_db->quote($account) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @didlist, $row;
    }
    $sql->finish;
    return @didlist;
}

sub list_dids_number_account() {
    my ( $astpp_db, $account ) = @_;
    my ( $sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT number FROM dids WHERE status = 1 AND account = "
      . $astpp_db->quote($account);
    print STDERR $tmp;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @didlist, $row->{number};
    }
    $sql->finish;
    @didlist = sort @didlist;
    return @didlist;
}

sub post_cdr() {
    my (
        $astpp_db,     $enh_config, $uniqueid, $account, $clid,
        $dest,         $disp,       $seconds,  $cost,    $callstart,
        $postexternal, $trunk,      $notes
      )
      = @_;

    # The cost is passed in 100ths of a penny.
    my ( $tmp, $status );
    $trunk    = gettext("N/A") if ( !$trunk );
    $uniqueid = gettext("N/A") if ( !$uniqueid );
    $status   = 0;
    $tmp      =
"INSERT INTO cdrs(uniqueid,cardnum,callerid,callednum,trunk,disposition,billseconds,"
      . "debit,callstart,status,notes) VALUES ("
      . $astpp_db->quote($uniqueid) . ", "
      . $astpp_db->quote($account) . ", "
      . $astpp_db->quote($clid) . ", "
      . $astpp_db->quote($dest) . ", "
      . $astpp_db->quote($trunk) . ", "
      . $astpp_db->quote($disp) . ", "
      . $astpp_db->quote($seconds) . ", "
      . $astpp_db->quote($cost) . ", "
      . $astpp_db->quote($callstart) . ", "
      . $astpp_db->quote($status) . ", "
      . $astpp_db->quote($notes) . ")";
    print STDERR $tmp;
    $astpp_db->do($tmp);
}

############### Integration with AgileBill starts here ##################

sub agilesavecdr() {
    my (
        $agile_db, $astpp_db, $config,  $enh_config, @output,
        $carddata, $billcost, $site_id, $cdrinfo,    $dbprefix
      )
      = @_;
    my $uniqueid   = &agile_findunique( $agile_db, $dbprefix );
    my $now        = time;
    my $table      = $dbprefix . "charge";
    my $attributes =
"Date==$cdrinfo->{calldate}\r\nCID==$cdrinfo->{src}\r\nDest==$cdrinfo->{dst}\r\nSec==$cdrinfo->{billsec}";
    my $account_info =
      &agile_account( $agile_db, $carddata->{number}, $dbprefix );
    if ( $account_info->{id} ne "" ) {
        $agile_db->do(
"INSERT INTO $table (ID, SITE_ID, DATE_ORIG, STATUS, SWEEP_TYPE, ACCOUNT_ID, AMOUNT, QUANTITY, TAXABLE, ATTRIBUTES) VALUES ("
              . $agile_db->quote($uniqueid) . ", "
              . $agile_db->quote($site_id) . ", "
              . $agile_db->quote($now) . ", "
              . $agile_db->quote( $enh_config->{agile_charge_status} ) . ", "
              . $agile_db->quote( $carddata->{sweep} ) . ", "
              . $agile_db->quote( $account_info->{id} ) . ", "
              . $agile_db->quote($billcost) . ", 1, "
              . $agile_db->quote( $enh_config->{agile_taxable} ) . ", "
              . $agile_db->quote($attributes)
              . ")" )
          || &agile_unbill;
    }
    else {
        &agile_unbill( $agile_db, $astpp_db, $config, $enh_config, @output,
            $cdrinfo->{uniqueid}, $uniqueid );

    }
}

sub agile_unbill() {
    my ( $agile_db, $astpp_db, $config, $enh_config, @output, $uniqueid,
        $agile_uniqueid )
      = @_;
    foreach my $handle (@output) {
        print $handle
"Due to an AgileBill error, I'm removing and unbilling this uniqeid $uniqueid\n";
    }
    &saveastcdr( $uniqueid, "error" ) if $enh_config->{astcdr} == 1;
    $astpp_db->do(
        "DELETE FROM cdrs WHERE uniqueid = " . $astpp_db->quote($uniqueid) );
    $agile_db->(
        "DELETE FROM charges WHERE id= " . $astpp_db->quote($agile_uniqueid) );
}

sub agile_isunique() {
    my ( $agile_db, $number, $dbprefix ) = @_;
    my $clause = "WHERE id = " . $agile_db->quote($number);
    my $count = &agile_count_cards( $agile_db, $clause, $dbprefix );
    return 1 if $count == "0";
    return 0;
}

sub agile_findunique() {
    my ( $agile_db, $dbprefix ) = @_;
    my $number;
    $number =
        int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 )
      . int( rand() * 9000 + 1000 );
    return $number if ( &agile_isunique( $agile_db, $number, $dbprefix ) );
}

sub agile_count_cards() {
    my ( $agile_db, $clause, $dbprefix ) = @_;
    my ( $row, $count, $sql );
    my $table = $dbprefix . "charge";
    $sql = $agile_db->prepare("SELECT COUNT(*) FROM $table $clause");
    $sql->execute;
    $row   = $sql->fetchrow_hashref;
    $count = $row->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub agile_service() {
    my ( $agile_db, $servicenum, $dbprefix ) = @_;
    my ( $sql, $record );
    my $table = $dbprefix . "service";
    $sql =
      $agile_db->prepare(
        "SELECT * FROM $table WHERE id = " . $agile_db->quote($servicenum) );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub agile_account() {
    my ( $agile_db, $username, $dbprefix ) = @_;
    my $table = $dbprefix . "account";
    my ( $sql, $record );
    $sql =
      $agile_db->prepare( "SELECT id FROM $table WHERE username = "
          . $agile_db->quote($username) );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub agile_service_account() {
    my ( $agile_db, $usernum, $dbprefix ) = @_;
    my $table = $dbprefix . "service";
    my ( $sql, $record );
    $sql =
      $agile_db->prepare( "SELECT * FROM $table WHERE account_id = "
          . $agile_db->quote($usernum) );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

##################### Realtime stuff starts here #######################33
sub add_sip_user_rt() {
    my ( $rt_dbh, $config, $enh_config, $name, $secret, $context, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
#    $name =~ s/
#	 $username =~ s/
    my $mailbox = $name . "\@" . $enh_config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";
	if (!$config->{sip_port}) {
		$config->{sip_port} = 5060;
	}
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n";
        print STDERR "NAME: $name\n";
        print STDERR "USERNAME: $username\n";
        print STDERR "CANREINVITE: $enh_config->{rt_sip_canreinvite} \n";
        print STDERR "CONTEXT: $context\n";
        print STDERR "INSECURE:  $enh_config->{rt_sip_insecure} \n";
        print STDERR "MAILBOX: $mailbox \n";
        print STDERR "NAT:  $enh_config->{rt_sip_nat}\n";
        print STDERR "SIP PORT: $config->{sip_port}\n";
        print STDERR "SIP QUALIFY: $enh_config->{rt_sip_qualify} \n";
        print STDERR "SECRET: $secret\n";
        print STDERR "SIP TYPE: $enh_config->{rt_sip_type}\n";
        print STDERR "CODEC DISALLOW: $enh_config->{rt_codec_disallow}\n";
        print STDERR "CODEC ALLOW: $enh_config->{rt_codec_allow}\n";
        print STDERR "CANCALLFORWARD: $enh_config->{rt_sip_cancallforward}\n";
    }
    $tmp =
        "INSERT INTO $enh_config->{rt_sip_table} (callerid,name,accountcode,"
      . "canreinvite,context,host,insecure,mailbox,"
      . "nat,port,qualify,secret,type,username,disallow,allow,"
      . "cancallforward) VALUES ("
      . $rt_dbh->quote($clid) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote($cc) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_canreinvite} ) . ", "
      . $rt_dbh->quote($context) . ", "
      . $rt_dbh->quote( $config->{ipaddr} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_insecure} ) . ", "
      . $rt_dbh->quote($mailbox) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_nat} ) . ", "
      . $rt_dbh->quote( $config->{sip_port} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_qualify} ) . ", "
      . $rt_dbh->quote($secret) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_type} ) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote( $enh_config->{rt_codec_disallow} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_codec_allow} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_cancallforward} ) . ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    if ( !$rt_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("SIP Device Creation Failed!");
    }
    else {
        return gettext("SIP Device Added!");
    }
}

sub add_iax_user_rt() {
    my ( $rt_dbh, $config, $enh_config, $name, $secret, $context, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
#    $name =~ s/
#	 $username =~ s/
    my $mailbox = $name . "\@" . $enh_config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";

    #    my $clid = "<$name>";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n";
        print STDERR "NAME: $name\n";
        print STDERR "USERNAME: $username\n";
        print STDERR "CANREINVITE: $enh_config->{rt_sip_canreinvite} \n";
        print STDERR "CONTEXT: $context\n";
        print STDERR "INSECURE:  $enh_config->{rt_sip_insecure} \n";
        print STDERR "MAILBOX: $mailbox \n";
        print STDERR "NAT:  $enh_config->{rt_sip_nat}\n";
        print STDERR "SIP PORT: $config->{sip_port}\n";
        print STDERR "SIP QUALIFY: $enh_config->{rt_sip_qualify} \n";
        print STDERR "SECRET: $secret\n";
        print STDERR "SIP TYPE: $enh_config->{rt_sip_type}\n";
        print STDERR "CODEC DISALLOW: $enh_config->{rt_codec_disallow}\n";
        print STDERR "CODEC ALLOW: $enh_config->{rt_codec_allow}\n";
    }
    $tmp =
        "INSERT INTO $enh_config->{rt_iax_table} (callerid,name,accountcode,"
      . "context,host,mailbox,"
      . "port,qualify,secret,type,username,disallow,allow"
      . ") VALUES ("
      . $rt_dbh->quote($clid) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote($cc) . ", "
      . $rt_dbh->quote($context) . ", "
      . $rt_dbh->quote( $config->{ipaddr} ) . ", "
      . $rt_dbh->quote($mailbox) . ", "
      . $rt_dbh->quote( $config->{iax_port} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_qualify} ) . ", "
      . $rt_dbh->quote($secret) . ", "
      . $rt_dbh->quote( $enh_config->{rt_sip_type} ) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote( $enh_config->{rt_codec_disallow} ) . ", "
      . $rt_dbh->quote( $enh_config->{rt_codec_allow} ) . ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    if ( !$rt_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("IAX2 Device Creation Failed!");
    }
    else {
        return gettext("IAX2 Device Added!");
    }
}

sub update_context_sip_user_rt {
    my ( $rt_db, $enh_config, $name, $context ) = @_;
    $rt_db->do( "UPDATE $enh_config->{rt_sip_table} SET context = "
          . $rt_db->quote($context)
          . " WHERE name = "
          . $rt_db->quote($name) );
}

sub update_context_iax_user_rt {
    my ( $rt_db, $enh_config, $name, $context ) = @_;
    $rt_db->do( "UPDATE $enh_config->{rt_iax_table} SET context = "
          . $rt_db->quote($context)
          . " WHERE name = "
          . $rt_db->quote($name) );
}

sub del_sip_user_rt() {
    my ( $rt_db, $config, $enh_config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n";
    $tmp =
      "DELETE FROM $enh_config->{rt_sip_table} WHERE name = "
      . $rt_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    $rt_db->do($tmp) || print "$tmp failed";
}

sub del_iax_user_rt() {
    my ( $rt_db, $config, $enh_config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n";
    $tmp =
      "DELETE FROM $enh_config->{rt_iax_table} WHERE name = "
      . $rt_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    $rt_db->do($tmp) || print "$tmp failed";
}

#######  Realtime Integration Ends ################
#######  OpenSER Integration Starts ###############
sub add_sip_user_openser() {
    my ( $openser_dbh, $config, $enh_config, $name, $secret, $context, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
    my $datetime = &prettytimestamp();
#    $name =~ s/
#	 $username =~ s/
    $tmp =
        "INSERT INTO subscribers (username,domain,password,firstname,lastname,emailaddress,datetime_created"
      . ") VALUES ("
      . $openser_dbh->quote($name) . ", "
      . $openser_dbh->quote($config->{openser_domain}) . ", "
      . $openser_dbh->quote($secret) . ", "
      . $openser_dbh->quote($params->{first}) . ", "
      . $openser_dbh->quote($params->{last}) . ", "
      . $openser_dbh->quote($params->{email}) . ", "
      . $openser_dbh->quote($datetime) . ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    if ( !$openser_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("SIP Device Creation Failed!");
    }
    else {
        return gettext("SIP Device Added!");
    }
}

sub del_sip_user_openser() {
    my ( $openser_db, $config, $enh_config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n";
    $tmp =
      "DELETE FROM subscribers WHERE username = "
      . $openser_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    $openser_db->do($tmp) || print "$tmp failed";
}

#######  OpenSER Integration Ends ###############
#######  FreePBX subroutines start here ###########
sub add_sip_user_freepbx() {
    my (
        $freepbx_db, $config,  $enh_config, $name,
        $secret,     $context, $username,   $params, $cc
      )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
#    $name =~ s/
#	 $username =~ s/    
    my $mailbox = $name . "\@" . $enh_config->{freepbx_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n";
        print STDERR "NAME: $name\n";
        print STDERR "USERNAME: $username\n";
        print STDERR "CANREINVITE: $enh_config->{rt_sip_canreinvite} \n";
        print STDERR "CONTEXT: $context\n";
        print STDERR "INSECURE:  $enh_config->{rt_sip_insecure} \n";
        print STDERR "MAILBOX: $mailbox \n";
        print STDERR "NAT:  $enh_config->{rt_sip_nat}\n";
        print STDERR "SIP PORT: $config->{sip_port}\n";
        print STDERR "SIP QUALIFY: $enh_config->{rt_sip_qualify} \n";
        print STDERR "SECRET: $secret\n";
        print STDERR "SIP TYPE: $enh_config->{rt_sip_type}\n";
        print STDERR "CODEC DISALLOW: $enh_config->{rt_codec_disallow}\n";
        print STDERR "CODEC ALLOW: $enh_config->{rt_codec_allow}\n";
        print STDERR "CANCALLFORWARD: $enh_config->{rt_sip_cancallforward}\n";
    }
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'account', "
      . $freepbx_db->quote($name) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'accountcode', "
      . $freepbx_db->quote($cc) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'secret', "
      . $freepbx_db->quote($secret) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'canreinvite', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_canreinvite} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'context', "
      . $freepbx_db->quote($context) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'dtmfmode', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_dtmfmode} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'host', "
      . $freepbx_db->quote( $config->{ipaddr} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'type', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_type} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'mailbox', "
      . $freepbx_db->quote($mailbox) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'nat', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_nat} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'port', "
      . $freepbx_db->quote( $config->{sip_port} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'qualify', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_qualify} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callgroup', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_callgroup} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'pickupgroup', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_pickupgroup} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'disallow', "
      . $freepbx_db->quote( $enh_config->{freepbx_codec_allow} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'allow', "
      . $freepbx_db->quote( $enh_config->{freepbx_codec_allow} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callerid', "
      . $freepbx_db->quote($clid) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
        "INSERT INTO devices (id,tech,dial,devicetype,user) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'SIP', 'SIP/$name', 'FIXED', "
      . $freepbx_db->quote($name) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
}

sub add_iax_user_freepbx() {
    my (
        $freepbx_db, $config,  $enh_config, $name,
        $secret,     $context, $username,   $params, $cc
      )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
#    $name =~ s/
#	 $username =~ s/
    my $mailbox = $name . "\@" . $enh_config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";

    #    my $clid = "<$name>";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n";
        print STDERR "NAME: $name\n";
        print STDERR "USERNAME: $username\n";
        print STDERR "CANREINVITE: $enh_config->{rt_sip_canreinvite} \n";
        print STDERR "CONTEXT: $context\n";
        print STDERR "INSECURE:  $enh_config->{rt_sip_insecure} \n";
        print STDERR "MAILBOX: $mailbox \n";
        print STDERR "NAT:  $enh_config->{rt_sip_nat}\n";
        print STDERR "SIP PORT: $config->{sip_port}\n";
        print STDERR "SIP QUALIFY: $enh_config->{rt_sip_qualify} \n";
        print STDERR "SECRET: $secret\n";
        print STDERR "SIP TYPE: $enh_config->{rt_sip_type}\n";
        print STDERR "CODEC DISALLOW: $enh_config->{rt_codec_disallow}\n";
        print STDERR "CODEC ALLOW: $enh_config->{rt_codec_allow}\n";
    }
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'account', "
      . $freepbx_db->quote($name) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'accountcode', "
      . $freepbx_db->quote($cc) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'secret', "
      . $freepbx_db->quote($secret) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'notansfer', "
      . $freepbx_db->quote( $enh_config->{freepbx_iax_notansfer} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'context', "
      . $freepbx_db->quote($context) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'username', "
      . $freepbx_db->quote($name) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'host', "
      . $freepbx_db->quote( $config->{ipaddr} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'type', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_type} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'mailbox', "
      . $freepbx_db->quote($mailbox) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'port', "
      . $freepbx_db->quote( $config->{iax_port} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'qualify', "
      . $freepbx_db->quote( $enh_config->{freepbx_sip_qualify} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'disallow', "
      . $freepbx_db->quote( $enh_config->{freepbx_codec_allow} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'allow', "
      . $freepbx_db->quote( $enh_config->{freepbx_codec_allow} ) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $enh_config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callerid', "
      . $freepbx_db->quote($clid) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
        "INSERT INTO devices (id,tech,dial,devicetype,user) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'IAX2', 'IAX2/$name', 'FIXED', "
      . $freepbx_db->quote($name) . ")";
    print STDERR $tmp if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";

}

sub update_context_sip_user_freepbx {
    my ( $freepbx_db, $enh_config, $name, $context ) = @_;
    $freepbx_db->do( "UPDATE $enh_config->{freepbx_sip_table} SET data = "
          . $freepbx_db->quote($context)
          . " WHERE id = "
          . $freepbx_db->quote($name)
          . "AND keyword = 'context'" );
}

sub update_context_iax_user_freepbx {
    my ( $freepbx_db, $enh_config, $name, $context ) = @_;
    $freepbx_db->do( "UPDATE $enh_config->{freepbx_iax_table} SET data = "
          . $freepbx_db->quote($context)
          . " WHERE id = "
          . $freepbx_db->quote($name)
          . "AND keyword = 'context'" );
}

sub del_sip_user_freepbx() {
    my ( $freepbx_db, $config, $enh_config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n";
    $tmp =
      "DELETE FROM $enh_config->{freepbx_sip_table} WHERE id = "
      . $freepbx_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    $freepbx_db->do($tmp) || print "$tmp failed";
}

sub del_iax_user_freepbx() {
    my ( $freepbx_db, $config, $enh_config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n";
    $tmp =
      "DELETE FROM $enh_config->{freepbx_iax_table} WHERE id = "
      . $freepbx_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n";
    }
    $freepbx_db->do($tmp) || print "$tmp failed";
}

########  FreePBX Integration Ends #################

sub get_reseller() {
    my ( $astpp_db, $reseller ) = @_;
    my ( $record, $sql );
    $sql =
      $astpp_db->prepare( "SELECT * FROM resellers WHERE name= "
          . $astpp_db->quote($reseller)
          . " AND status = 1" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub get_reseller_including_closed() {
    my ( $astpp_db, $reseller ) = @_;
    my ( $record, $sql );
    $sql =
      $astpp_db->prepare( "SELECT * FROM resellers WHERE name= "
          . $astpp_db->quote($reseller));
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub get_provider() {
    my ( $astpp_db, $provider ) = @_;
    my ( $record, $sql );
    $sql =
      $astpp_db->prepare( "SELECT * FROM providers WHERE name= "
          . $astpp_db->quote($provider)
          . " AND status = 1" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub get_counter() {
    my ( $astpp_db, $package, $cardnum ) = @_;
    my ( $sql, $row );
    $sql =
      $astpp_db->prepare( "SELECT * FROM counters WHERE package = "
          . $astpp_db->quote($package)
          . " AND account = "
          . $astpp_db->quote($cardnum) );
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub get_package() {
    my ( $astpp_db, $carddata, $number ) = @_;
    my ( $sql, $record );
    $sql =
      $astpp_db->prepare( "SELECT * FROM packages WHERE "
          . $astpp_db->quote($number)
          . " RLIKE pattern AND pricelist = "
          . $astpp_db->quote( $carddata->{pricelist} )
          . " ORDER BY LENGTH(pattern) DESC" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub count_dids() {
    my ( $astpp_db, $test ) = @_;
    my ( $sql, $count, $record );
    $sql = $astpp_db->prepare("SELECT COUNT(*) FROM dids $test");
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub count_callingcards() {
    my ( $astpp_db, $test ) = @_;
    my ( $sql, $count, $record );
    $sql = $astpp_db->prepare("SELECT COUNT(*) FROM callingcards $test");
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub count_accounts() {
    my ( $astpp_db, $test ) = @_;
    my ( $sql, $count, $record );
    $sql = $astpp_db->prepare("SELECT COUNT(*) FROM accounts $test");
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub count_unbilled_cdrs() {
    my ($cdr_db) = @_;
    my ( $sql, $count, $record );
    $sql =
      $cdr_db->prepare( "SELECT COUNT(*) FROM cdr WHERE cost = 'error' OR "
          . "accountcode IN (NULL,'') AND cost ='none'" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

sub list_callingcards() {
    my ($astpp_db) = @_;
    my ( $sql, @cardlist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT cardnumber FROM callingcards WHERE status < 2");
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cardlist, $row->{cardnumber};
    }
    $sql->finish;
    @cardlist = sort @cardlist;
    return @cardlist;
}

sub list_callingcards_account() {
    my ( $astpp_db, $account ) = @_;
    my ( $sql, @cardlist, $row );
    $sql =
      $astpp_db->prepare( "SELECT cardnumber FROM callingcards WHERE status < 2"
          . " AND account = "
          . $astpp_db->quote($account) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cardlist, $row->{cardnumber};
    }
    $sql->finish;
    @cardlist = sort @cardlist;
    return @cardlist;
}

sub list_cc_brands() {
    my ($astpp_db) = @_;
    my ( $sql, @brandlist, $result );
    $sql =
      $astpp_db->prepare("SELECT name FROM callingcardbrands WHERE status = 1");
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @brandlist, $result->{name};
    }
    $sql->finish;
    @brandlist = sort @brandlist;
    return @brandlist;
}

sub list_resellers() {
    my ($astpp_db) = @_;
    my ( $sql, @resellers, $result );
    $sql = $astpp_db->prepare("SELECT name FROM resellers WHERE status = 1");
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @resellers, $result->{name};
    }
    $sql->finish;
    @resellers = sort @resellers;
    return @resellers;
}

sub testunique() {
    my ( $astpp_db, $number ) = @_;
    my $test = "WHERE number = " . $astpp_db->quote($number);
    my $count = &count_cards( $astpp_db, $test );
    return $count;
}

sub get_callingcard() {
    my ( $astpp_db, $cardno ) = @_;
    my ( $sql, $carddata );
    $sql =
      $astpp_db->prepare( "SELECT * FROM callingcards WHERE cardnumber = "
          . $astpp_db->quote($cardno) );
    $sql->execute;
    $carddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $carddata;
}

################# Optigold Integration ################################
#sub ogsavecdr() {
#    my ( $cardnum, $dispostion, $callstart, $dst, $billseconds, $billcost,
#        $src ) = @_;
#    my $login       = uri_escape("Login=$account");
#    my $description =
#      uri_escape(
#"Description=Date==$callstart Source==$src Destination==$dst Seconds==$billseconds"
#      );
#    my $product_id = uri_escape("Product ID=$enh_config->{product}");
#    my $unit_price = uri_escape("Unit Price=$billcost");
#    foreach $handle (@output) {
#        print $handle "ASTPP Card Number: $cardnum\n";
#    }
#    my $account_info = &agile_account($agile_db, $cardnum);
#    $account = $account_info->{id};
#    foreach $handle (@output) {
#        print $handle "Agile Account ID: $account\n";
#    }
#    if ( $account ne "" ) {
#        my $status =
#          get(  "http://"
#              . $enh_config->{og_host}
#              . "/FMPro?-db+Charges_.fp5-format="
#              . $enh_config->{og_format_file} . "&"
#              . $login . "&"
#              . $product_id . "&"
#              . $description . "&"
#              . $unit_price
#              . "&Quantity=1&-New" );
#        foreach $handle (@output) {
#            print $handle "Status: $status";
#        }
#    }
#    else {
#        &og_unbill($uniqueid);
#
#    }
#}
#
#sub og_unbill() {
#    my ($uniqueid) = @_;
#    foreach $handle (@output) {
#        print $handle
#"Due to an optigold error, I'm removing and unbilling this uniqeid $uniqueid\n";
#    }
#    &save_ast_cdr( $cdr_db, $uniqueid, "error" ) if $enh_config->{astcdr} == 1;
#    $astpp_db->do(
#        "DELETE FROM cdrs WHERE uniqueid = " . $astpp_db->quote($uniqueid) );
#}

sub transfer_funds() { #Transfer funds from one callingcard to another
	my ($astpp_db, $sourcecard, $sourcecardpin, $destcard, $destcardpin) = @_;
	my $sourcecardinfo = &get_callingcard( $astpp_db, $sourcecard );
	my $sourcecardstatus = &check_card_status( $astpp_db, $sourcecardinfo );
	# This subroutine returns the status of the card:
	if ( $sourcecardstatus != 0 ) {
		return 1;
	} elsif ( $sourcecardinfo->{pin} != $sourcecardpin) {
		return 1;
	}
	my $destcardinfo = &get_callingcard( $astpp_db, $destcard );
	my $destcardstatus = &check_card_status( $astpp_db, $destcardinfo );

	# If we get this far that means that both the source and the destination card are ok.
	if ( $destcardstatus != 0 ) {
		return 1;
	} elsif ( $destcardinfo->{pin} != $destcardpin) {
		return 1;
	}
	
	$astpp_db->do("UPDATE callingcards SET used = "
	          . $astpp_db->quote( $sourcecardinfo->{value} )
	          . " WHERE cardnumber = "
	          . $astpp_db->quote( $sourcecardinfo->{cardnumber} ));

	$astpp_db->do("UPDATE callingcards SET status = '2'"
	          . " WHERE cardnumber = "
	          . $astpp_db->quote( $sourcecardinfo->{cardnumber} ));

	$astpp_db->do("UPDATE callingcards SET value = "
	          . $astpp_db->quote( ($sourcecardinfo->{value} - $sourcecardinfo->{used}) + $destcardinfo->{value} )
	          . " WHERE cardnumber = "
	          . $astpp_db->quote( $destcardinfo->{cardnumber} ));
	return 0;
}



sub max_length() {
	my ($astpp_db, $config, $enh_config, $carddata, $phoneno) = @_;
	my ($branddata, $numdata, $credit, $credit_limit, $maxlength);
	$branddata = &get_pricelist( $astpp_db, $carddata->{pricelist} );	# Fetch all the brand info from the db.
	$numdata = &get_route( $astpp_db, $config, $phoneno, $carddata->{pricelist} );    # Find the appropriate rate to charge the customer.
	if ( !$numdata->{pattern} ){  # If the pattern doesn't exist, we don't know what to charge the customer	
		# and therefore must exit.
		print STDERR "CALLSTATUS 1\n" if $config->{debug} == 1;
		print STDERR "INVALID PHONE NUMBER\n" if $config->{debug} == 1;
		return (1,0);
	}
	print STDERR "Found pattern: $numdata->{pattern}\n" if $config->{debug} == 1;
	$credit = &accountbalance( $astpp_db, $carddata->{number} ); # Find the available credit to the customer.
	print STDERR "Account Balance: $credit" if $config->{debug} == 1;
	$credit_limit = $carddata->{credit_limit} * 10000;
	print STDERR "Credit Limit: $credit_limit" if $config->{debug} == 1;
	$credit = ($credit * -1) + ($carddata->{credit_limit} * 10000);         # Add on the accounts credit limit.
	$credit = $credit / $carddata->{maxchannels} if $carddata->{maxchannels} > 0;
	if ($branddata->{markup} > 0) {
		$numdata->{connectcost} =
		$numdata->{connectcost} * ( ( $branddata->{markup} / 10000 ) + 1 );
		$numdata->{cost} =
		$numdata->{cost} * ( ( $branddata->{markup} / 10000 ) + 1 );
	}
	if ( $numdata->{connectcost} > $credit ) {   # If our connection fee is higher than the available money we can't connect.
		return (0,0);
	}
	if ( $numdata->{cost} > 0 ) {
		$maxlength = ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
	}
	else {
		$maxlength = $config->{max_free_length};    # If the call is set to be free then assign a max length.
	}
	if ( $numdata->{cost} > 0 ) {
		$maxlength = ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
	}
	else {
		$maxlength = $config->{max_free_length};	# If the call is set to be free then assign a max length.
	}
	return (1, $maxlength);
}

sub perform_callout() {
    my ($astpp_db,$config,$enh_config,$number,$lcrcontext,$accountcode,$maxretries,$waittime,$retrytime,$clidname,$clidnumber,$context,$extension,%variables) = @_;
    my ($actionid,%callout);
    my $sql = $astpp_db->prepare("INSERT INTO manager_action_variables (id,name,value) VALUES('','$number','$extension')");
    $sql->execute;
    $actionid = $sql->{'mysql_insertid'};
    foreach my $key (keys %variables) {
	my $sql = $astpp_db->prepare("INSERT INTO manager_action_variables (callid,name,value) VALUES("
		. $astpp_db->quote($actionid) . ", "
		. $astpp_db->quote($key) . ", "
		. $astpp_db->quote($variables{$key}) . ")");
	$sql->execute;
    }
    use Asterisk::Manager;
    my $astman = new Asterisk::Manager;
    $astman->user($config->{astman_user});
    $astman->secret($config->{astman_secret});
    $astman->host($config->{astman_host});
    $astman->connect || die $astman->error . "\n";


    %callout = ( Action => 'Originate',
                                       Channel => 'Local/' . $number . '@' . $lcrcontext,
				       MaxRetries =>  $maxretries,
				       RetryTime => $retrytime,
			    	       WaitTime => $waittime,
                                       Exten => $extension,
                                       Context => $context,
				       Account => $accountcode,
				       CallerID => "<$clidname>" . $clidnumber,
				       ActionID => $actionid,	
				       Variable => "ACTIONID=$actionid",	
                                       Priority => '1' );
#    foreach my $variable (@variables) {
#		print STDERR "SET: $variable->{name} - $variable->{value} \n";
#		%callout->{Variable} = "$variable->{name}=$variable->{value}";
#    }	
    foreach my $key (keys %callout) {
   	 print STDERR "Key: $key Value: " . $callout{$key} . "\n";   
    }
   
print STDERR $astman->sendcommand(%callout);


#    use Asterisk::Outgoing;
#    my $out = new Asterisk::Outgoing;
#    my $channel = "Local\/" . $number . "\@$lcrcontext";
#    $out->setvariable( 'Channel',  $channel );
#    $out->setvariable( 'MaxRetries', $maxretries );
#    $out->setvariable( 'RetryTime',  $retrytime );
#    $out->setvariable( 'WaitTime',   $waittime );
#    $out->setvariable( "context",    $context );
#    $out->setvariable( "extension",  $extension );
#    $out->setvariable( "CallerID",   "<$clidname> $clidnumber" );
#    $out->setvariable( "SetVar", "ACCOUNTCODE = $accountcode ");
#    foreach my $variable (@variables) {
#		print STDERR "SET: $variable->{name} - $variable->{value} /n";
#		$out->setvariable( "Set", "$variable->{name} = $variable->{value}" );
#		$out->setvariable( "SetVar", "$variable->{name} = $variable->{value}" );
#    }	
#    $out->outtime( time() + 15 );
#    $out->create_outgoing;
#	print STDERR "Created Call to: $number\n";
}

sub check_card_status() {    # Check a few things before saying the card is ok.
	# This subroutine returns the status of the card:
	# Status 0 means the card is ok,
	# Status 1 means the card is in use.
	# Status 2 means the card has expired.
	# Status 3 means the card is empty.
	my ($astpp_db,$cardinfo) = @_;
	my $now = $astpp_db->selectall_arrayref("SELECT NOW() + 0")->[0][0];
	print STDERR "Present Time: $now\n";
	print STDERR "Expiration Date: $cardinfo->{expiry}\n";
	print STDERR "Valid for Days: $cardinfo->{validfordays}\n";
	print STDERR "First Use: $cardinfo->{firstuse}\n";
	if ( $cardinfo->{inuse} != 0 )
	{                
		return 1; #Status 1 means card is in use.
	}
	if ( $cardinfo->{validfordays} > 0 ) {
		$now = $astpp_db->selectall_arrayref("SELECT NOW() + 0")->[0][0];
		if ( $now gt $cardinfo->{expiry} && $cardinfo->{expiry} ne "0000-00-00 00:00:00" ) {
			my $sql =
			  "UPDATE callingcards SET status = 2 WHERE cardnumber = "
			  . $astpp_db->quote( $cardinfo->{cardnumber} );
			$astpp_db->do($sql);
			$sql =
			  "DELETE FROM ani_map WHERE account = "
			  . $astpp_db->quote( $cardinfo->{cardnumber} );
			$astpp_db->do($sql);
			return 2; #Status 2 means card has expired
		}
	}
	if ( $cardinfo->{value} - $cardinfo->{used} < 100 )
	{    # don't allow this if the card is down to the last penny.
		return 3; #Status 3 means card is empty
	}
	return 0;
}

sub define_sounds() {
	my ($astpp_db) = @_;
	my $sound;
	$sound->{no_responding} =
  "astpp-down";    #The calling card platform is down, please try again later.
$sound->{cardnumber} =
  "astpp-accountnum";    #Please enter your card number followed by pound.
$sound->{cardnumber_incorrect} = "astpp-badaccount";    #Incorrect card number.
$sound->{pin} = "astpp-pleasepin";    #Please enter your pin followed by pound.
$sound->{pin_incorrect} = "astpp-invalidpin";    #Incorrect pin.
$sound->{goodbye}       = "goodbye";          #Goodbye.
$sound->{destination}   =
  "astpp-phonenum"; #Please enter the number you wish to dial followed by pound.
$sound->{destination_incorrect} = "astcc-badphone";    #Phone number not found!
$sound->{card_inuse}     = "astpp-in-use";   #This card is presently being used.
$sound->{call_will_cost} = "astpp-willcost"; #This call will cost:
$sound->{main_currency}  = "astpp-dollar";   #Dollar
$sound->{sub_currency}   = "astpp-cent";     #Cent
$sound->{main_currency_plural}     = "astpp-dollars";       #Dollars
$sound->{sub_currency_plural}      = "astpp-cents";         #cents
$sound->{per}                      = "astpp-per";           #per
$sound->{minute}                   = "astpp-minute";        #Minute
$sound->{minutes}                  = "astpp-minutes";       #Minutes
$sound->{second}                   = "astpp-second";        #Second
$sound->{seconds}                  = "astpp-seconds";       #Seconds
$sound->{a_connect_charge}         = "astpp-connectcharge"; #A connect charge of
$sound->{will_apply}               = "astpp-willapply";     #Will apply
$sound->{please_wait_will_connect} =
  "astpp-please-wait-while-i-connect";    #Please wait while I connect your call
$sound->{card_is_empty}       = "astpp-card-is-empty";    #This card is empty.
$sound->{card_has_balance_of} =
  "astpp-this-card-has-a-balance-of";    #Card has a balance of:
$sound->{card_has_expired} = "astpp-card-has-expired";   #This card has expired.
$sound->{call_will_last}    = "astpp-this-call-will-last"; #This call will last:
$sound->{not_enough_credit} =
  "astpp-not-enough-credit";    #You do not have enough credit
$sound->{call_completed} =
  "astpp-call-completed";       #This call has been completed.
$sound->{astpp_callingcard_menu} =
  "astpp-callingcard-menu"
  ; #Press one if you wish to place another call, press 2 for your card balance, or press 3 to hangup
$sound->{busy} = "astpp-busy-tryagain";  #Number was busy, Press 1 to try again.
$sound->{congested} =
  "astpp-congested-tryagain";    #Number was congested, Press 1 to try again.
$sound->{noanswer} =
  "astpp-noanswer-tryagain";     #There was no answer, Press 1 to try again.
$sound->{badnumber} =
  "astpp-badnumber";          # "Calls from this location are blocked!"
$sound->{used_elsewhere} =
  "astpp-used-elsewhere";     # "This location has been used already."
$sound->{goodbye}            = "goodbye";    # "Goodbye"
$sound->{callback_performed} =
  "astpp-callback-performed";    # "This callback has been performed please disconnect now"
$sound->{cardnumber} =
  "astpp-accountnum";    #Please enter your card number followed by pound.
$sound->{cardnumber_incorrect} = "astpp-badaccount";    #Incorrect card number.
$sound->{pin} = "astpp-pleasepin";    #Please enter your pin followed by pound.
$sound->{pin_incorrect} = "astpp-invalidpin";    #Incorrect pin.
$sound->{card_inuse}    = "";
$sound->{register_ani}  =
  "astpp-register"
  ;    # "Register ANI to this card? Press 1 for yes or any other key for no."
$sound->{card_has_expired} = "astpp_expired";    #"This card has expired"
$sound->{card_is_empty}    = "astpp-empty";      #This card is empty
$sound->{where_to_call}    =
  "astpp-where-to-call"
  ;    #Press 1 to receive a call at the number you called from or registered
       #Otherwise enter the number you wish to be called at.
$sound->{number_to_register} =
  "astpp-number-to-register";  #Press 1 to register the number you called from.
                               #Otherwise enter the number you wish to register.
$sound->{card_has_been_refilled} = "astpp-card-has-been-refilled"; # Your card has been refilled.                          
$sound->{card_to_refill} = "astpp-card-to-refill"; #please enter the card number you wish to refill followed
							# by the pound sign.
$sound->{card_to_empty} = "astpp-card-to-empty"; #please enter the card number you wish to empty into your card
							# followed by the pound sign.
$sound->{astpp_please_pin_card_empty} = "astpp-please-pin-card-empty"; #please enter the pin number for the card
									# you wish to empty followed by the pound
									# sign.
return $sound;	
}

1;
