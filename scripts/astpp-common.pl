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
############################################################
use Text::Template;
use POSIX;
use POSIX qw(strftime);
use DBI;
use strict;
# use warnings;
use Locale::gettext_pp qw(:locale_h);
use Mail::Sendmail;

$ENV{LANGUAGE} = "en";    # de, es, br - whatever
# print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n" if $config->{debug} == 1;
bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");

# Load Basic Configuration File
no warnings 'redefine';
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

sub load_config_reseller_db() { #First load reseller config from the reseller config file then overwrite from database.
    my ($astpp_db, $config, $reseller) = @_;
    open( CONFIG, "</var/lib/astpp/astpp-$reseller-config.conf" );
    while (<CONFIG>) {
        chomp;       # no newline
        s/#.*//;     # no comments
        s/^\s+//;    # no leading white
        s/\s+$//;    # no trailing white
        next unless length;    # anything left?
        my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
        $config->{$var} = $value;
    }
    close(CONFIG);
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system WHERE reseller = " . $astpp_db->quote($reseller);
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
	print STDERR $row->{name} . "," . $row->{value} if $config->{debug} == 1;
    }
    $sql->finish;
    return $config;
}

# This is to save the configuration to astpp-config.conf but I don't believe it's used anymore.
sub save_config() {
    my (%config) = @_;
    open( CONFIG, ">/var/lib/astpp/astpp-config.conf" );
    print CONFIG ";\n; Automatically created by astpp-config.cgi.\n" if $config->{debug} == 1;
    foreach my $tmp ( keys %config ) {
        print CONFIG "$tmp = " . $config{$tmp} . "\n" if $config->{debug} == 1;
    }
    close(CONFIG);
}

sub callingcard_update_balance() {  #Update the available credit on the calling card.
        my ( $astpp_db, $cardinfo, $charge ) = @_;
        my $sql =
            "UPDATE callingcards SET used = "
          . $astpp_db->quote( ($charge) + $cardinfo->{used} )
          . " WHERE cardnumber = "
          . $astpp_db->quote( $cardinfo->{cardnumber} );
        $astpp_db->do($sql);
}


sub callingcard_set_in_use() {  # Set the "inuse" flag on the calling cards.  This prevents multiple people from
# using the same card.
        my ( $astpp_db, $cardinfo, $status ) = @_;
        my $sql;
        $sql =
            "UPDATE callingcards SET inuse = "
          . $astpp_db->quote($status)
          . " WHERE cardnumber = "
          . $astpp_db->quote( $cardinfo->{cardnumber} );
	print STDERR $sql ."\n" if $config->{debug} == 1;
        $astpp_db->do($sql);
}


# Add a calling card. 
sub add_callingcard() {
    my ( $astpp_db, $config, $branddata, $status, $pennies,
        $account, $pins )
      = @_;
    my ( $cc, $pin, $sql );
    $cc = &finduniquecallingcard( $astpp_db, $config );
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
	$pin = $config->{pin_cc_prepend} . $pin;
        $pin = substr( $pin, 0, $config->{pinlength} );
    }
    $sql =
"INSERT INTO callingcards (cardnumber,brand,status,value,account,pin,validfordays,created,firstused,expiry,maint_fee_pennies,"
      . "maint_fee_days, disconnect_fee_pennies, minute_fee_minutes, minute_fee_pennies,min_length_minutes,min_length_pennies) VALUES ("
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
      . $astpp_db->quote( $branddata->{minute_fee_pennies} ) . ","
      . $astpp_db->quote( $branddata->{min_length_minutes} ) . ","
      . $astpp_db->quote( $branddata->{min_length_pennies} ) . ")";
    print STDERR "$sql";
    $astpp_db->do($sql) || print "$sql failed";
    return ( $cc, $pin );
}

# Add a pricelist.  If the pricelist exists already then update it.
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

# Add an account.  This applys to user accounts as well as reseller accounts.
sub addaccount() {
    my ( $astpp_db, $config, $accountinfo ) = @_;
    if ($accountinfo->{logintype} == 1 || $accountinfo->{logintype} == 5) {
	$accountinfo->{reseller} = $accountinfo->{username};
    } else {
	$accountinfo->{reseller} = "";
    }
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
    $accountinfo->{maxchannels}    = "" if ( !$accountinfo->{maxchannels} );
    $accountinfo->{timezone}       = "" if ( !$accountinfo->{timezone} );
    my $cc = &finduniquecc( $astpp_db, $config );
    my $pin = &finduniquepin( $astpp_db, $config );
    my $tmp =
"INSERT INTO accounts (cc,pin,number,pricelist,sweep,credit_limit,posttoexternal,password,"
      . "first_name, middle_name, last_name, company_name, address_1, address_2,"
      . "postal_code, province, city, country, telephone_1, telephone_2, fascimile,"
      . "email, language, currency, reseller, tz, maxchannels, status, type"
      . ") VALUES ("
      . $astpp_db->quote($cc) . ","
      . $astpp_db->quote($pin) . ","
      . $astpp_db->quote( $accountinfo->{number} ) . ","
      . $astpp_db->quote( $accountinfo->{pricelist} ) . ","
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
      . $astpp_db->quote( $accountinfo->{timezone} ) . ","
      . $astpp_db->quote( $accountinfo->{maxchannels} ) . ","
      . $astpp_db->quote( $accountinfo->{status} ) . ","
      . $astpp_db->quote( $accountinfo->{accounttype} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    if ( $astpp_db->do($tmp) ) {
        my $status = gettext("Account Added!");
        return $status;
    }
    else {
        my $status = $tmp . gettext("FAILED!");
        return $status;
    }
}

# Create the reseller configfile as well as add the reseller to the reseller table.
sub add_reseller() {
    my ( $astpp_db, $config, $name, $posttoexternal ) = @_;
    my ( $resellerlist, $tmp, $sql, $status );
    my $configfile = $config->{astpp_dir} . "/astpp-" . $name . "-config.conf";
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
"cp $config->{astpp_dir}/sample.reseller-config.conf $configfile"
            );
	    $status .= gettext("Please be sure to update this file:") . " $configfile";
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
        system("mv $config->{astpp_dir}/$name-config.conf.old $configfile");
    }
    return $status;
}

# Return a trunk.  Used in LCR.
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

sub get_invoice() {
    my ( $astpp_db, $invoiceid ) = @_;
    my ( $sql, $invoicedata );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM invoice_list_view WHERE invoiceid = " . $astpp_db->quote($invoiceid) );
    $sql->execute;
    $invoicedata = $sql->fetchrow_hashref;
    $sql->finish;
    return $invoicedata;
}

# This is used by calling cards as well as lcr.  Pass on the phone number as well as the trunk to use.  It will return the dialstring
# for Asterisk(tm).
# This is used by calling cards as well as lcr.  Pass on the phone number as well as the trunk to use.  It will return the dialstring
# for Asterisk(tm).
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
    if ($trunkdata->{dialed_modify} && $trunkdata->{dialed_modify} ne "") {
	my @regexs = split(m/","/m, $trunkdata->{dialed_modify});
	foreach my $regex (@regexs) {
                $regex =~ s/"//g;				#Strip off quotation marks
		my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
                print STDERR "Grab: $grab\n" if $config->{debug} == 1;
                print STDERR "Replacement: $replace\n" if $config->{debug} == 1;
                print STDERR "Phone Before: $phone\n" if $config->{debug} == 1;
                $phone =~ s/$grab/$replace/is;
		print STDERR "Phone After: $phone\n" if $config->{debug} == 1;
	}
    }
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

# Return the list of outbound routes you should use based either on cost or precedence.
sub get_outbound_routes() {
	my ( $astpp_db, $number, $accountinfo,$routeinfo, @reseller_list ) = @_;
	my ( @routelist, @outbound_route_list, $record, $sql );
	if ($accountinfo->{routing_technique} && $accountinfo->{routing_technique} != 0) {
		$sql =
			$astpp_db->prepare( "SELECT * FROM outbound_routes WHERE "
			. $astpp_db->quote($number)
			. " RLIKE pattern AND status = 1 AND precedence <= "
			. $astpp_db->quote($accountinfo->{routing_technique}) 
			. " ORDER BY cost,precedence"
			# . "ORDER by LENGTH(pattern) DESC, precedence, cost"
		);	
		$sql->execute;
		while ( $record = $sql->fetchrow_hashref ) {
			push @routelist, $record;
		}
		$sql->finish;
	} elsif ($routeinfo->{precedence} && $routeinfo->{precedence} != 0 ){
		$sql =
			$astpp_db->prepare( "SELECT * FROM outbound_routes WHERE "
			. $astpp_db->quote($number)
			. " RLIKE pattern AND status = 1 AND precedence <= " 
			. $astpp_db->quote($routeinfo->{precedence}) 
			. " ORDER BY cost,precedence"
			# . "ORDER by LENGTH(pattern) DESC, precedence, cost"
		);
		$sql->execute;
		while ( $record = $sql->fetchrow_hashref ) {
			push @routelist, $record;
		}
		$sql->finish;
	} else {
		$sql =
			$astpp_db->prepare( "SELECT * FROM outbound_routes WHERE "
			. $astpp_db->quote($number)
			. " RLIKE pattern AND status = 1 "
			. " ORDER BY cost,precedence"
			#."ORDER by LENGTH(pattern) DESC, cost"
		);	
		$sql->execute;
		while ( $record = $sql->fetchrow_hashref ) {
			push @routelist, $record;
		}
		$sql->finish;
	}
	if (@reseller_list) {
		print STDERR "CHECKING LIST OF RESELLERS AGAINST TRUNKS\n"  if $config->{debug} == 1;
		foreach my $route (@routelist) {
			print STDERR "CHECKING ROUTE: $route->{name}\n"  if $config->{debug} == 1;
			my $trunkdata = &get_trunk($astpp_db, $route->{trunk});
			if (defined $trunkdata->{resellers} && $trunkdata->{resellers} ne "") {
				print STDERR "ROUTE RESELLER DATA = $trunkdata->{resellers}\n"  if $config->{debug} == 1;
				foreach my $reseller (@reseller_list) {
					print STDERR "Checking Reseller: $reseller against trunk: $route->{name}\n"  if $config->{debug} == 1;
					if ($trunkdata->{resellers} =~ m/'$reseller'/) {
						push @outbound_route_list, $route;
					}
				}
			} else {
				print STDERR "ROUTE RESELLER DATA = $trunkdata->{resellers}\n"   if $config->{debug} == 1;
				push @outbound_route_list, $route;
			}
		}
	} else {
		print STDERR "WE DID NOT RECEIVE A LIST OF RESELLERS TO CHECK AGAINST.\n"   if $config->{debug} == 1;
		@outbound_route_list = @routelist;
	}
	return @outbound_route_list;
}

# Send an email after refilling an account.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_refill_account() {
    my ( $astpp_db,$reseller,$config, $vars ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
    
    $accountdata = &get_account($astpp_db,$vars->{username});
      
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'voip_account_refilled' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
            
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};        
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email after reactivating an account.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_reactivate_account() {
    my ( $astpp_db,$reseller,$config, $vars ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
    
    $accountdata = &get_account($astpp_db,$vars->{username});
      
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'voip_reactivate_account' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};        
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when an account is added.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_add_user() {
    my ( $astpp_db,$reseller,$config, $vars ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
    
    $accountdata = &get_account($astpp_db,$vars->{username});
      
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_add_user' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};        
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when a device is added.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_add_device() {
    my ( $astpp_db,$reseller, $config, $vars ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record,%mail );
    
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
           
    $accountdata = &get_account($astpp_db,$vars->{username});
    
    if ( $vars->{type} eq "SIP" ) {
	
	$sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'add_sip_device' AND accountid='".$accountdata->{accountid}."'");    
    	$sql -> execute;
    	$record = $sql->fetchrow_hashref;
     	$sql->finish;
	
	%mail = (
	    To         => $vars->{email},
	    From       => $config->{company_email},
	    Bcc        => $config->{company_email},
	    Subject    => $record->{'subject'},
	    'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
	);
      
        $mail{'Message : '} = $record->{'template'};         
    }
    if ( $vars->{type} eq "IAX" ) {
        $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'add_iax_device' AND accountid='".$accountdata->{accountid}."'");    
    	$sql -> execute;
    	$record = $sql->fetchrow_hashref;
     	$sql->finish;
	
	%mail = (
	    To         => $vars->{email},
	    From       => $config->{company_email},
	    Bcc        => $config->{company_email},
	    Subject    => $record->{'subject'},
	    'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
	);
      
        $mail{'Message : '} = $record->{'template'};         
    }    
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when an account is removed.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_del_user() {
    my ( $astpp_db,$reseller,$config, $vars) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
    
    $accountdata = &get_account($astpp_db,$vars->{username});
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_remove_user' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};      
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
    print STDERR $mail{'Message : '}; 
}

# Send an email when on calling card creation.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_add_callingcard() {
    my ( $astpp_db,$reseller,$config, $vars, $cc, $pin ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );    
    $vars->{email} = $config->{company_email} if $config->{user_email} == 0;
    
    $accountdata = &get_account($astpp_db,$vars->{username});
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_calling_card' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $vars->{email},
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;

}

# Send an email when a did is tagged to an account.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_add_did() {
    my ( $astpp_db, $reseller,$vars, $did, $config, $email ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
     
    $email = $config->{company_email} if $config->{user_email} == 0;
    $accountdata = &get_account($astpp_db,$vars->{username});
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_add_did' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
	
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "DID: $did ".$record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};        
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when a did is removed from an account.  This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_del_did() {
    my ( $astpp_db, $reseller,$vars, $did, $config, $email ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record );
    
    $email = $config->{company_email} if $config->{user_email} == 0;
    $accountdata = &get_account($astpp_db,$vars->{username});
    
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_remove_did' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => "DID: $did ".$record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};      
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when an invoice is created in AgileBill(tm) or OSCommerce. 
# This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_new_invoice() {
    my ( $astpp_db,$reseller,$config, $email, $invoice, $total ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record,$vars );
    
    $email = $config->{company_email} if $config->{user_email} == 0;
    $accountdata = &get_account($astpp_db,$vars->{username});
    
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_new_invoice' AND accountid='".$accountdata->{accountid}."'");    
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
	
    
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Send an email when an account balance gets low. 
# This needs to be updated and moved to templates which reside in the database and can be
# configured per reseller.
sub email_low_balance() {
#     my ( $astpp_db, $reseller,$config, $email, $balance ) = @_;
    my ( $config, $email, $balance ) = @_;
    my ( $sql,$subject,$mail,$accountdata,$record,$vars );
    $email = $config->{company_email} if $config->{user_email} == 0;    
    $accountdata = &get_account($astpp_db,$vars->{username});
    
    $sql = $astpp_db->prepare("SELECT * FROM templates WHERE name = 'email_low_balance' AND accountid='".$accountdata->{accountid}."'");       
    $sql -> execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    
    my %mail = (
        To         => $email,
        From       => $config->{company_email},
        Bcc        => $config->{company_email},
        Subject    => $record->{'subject'},
        'X-Mailer' => "Mail::Sendmail version $Mail::Sendmail::VERSION",
    );
    $mail{'Message : '} = $record->{'template'};	
    if ( sendmail %mail ) { print STDERR "Mail sent OK.\n" }
    else { print STDERR "Error sending mail: $Mail::Sendmail::error \n" }
    print STDERR "\n\$Mail::Sendmail::log says:\n", $Mail::Sendmail::log;
}

# Returns a timestamp which is in a format easy to work with.
sub timestamp() {
    my $now = strftime "%Y%m%d%H%M%S", localtime;
    return $now;
}

# Returns a timestamp in a human friendly format.
sub prettytimestamp() {
    my $now = strftime "%Y-%m-%d %H:%M:%S", localtime;
    return $now;
}

# For debugging output send the variable you want dumped here along with where you want it dumped to.
sub debug() {
    my ( $input ) = @_;
    require Data::Dumper;
    print Data::Dumper->Dump( [$input] );
}

# Connect to AgileBill(tm) database.
sub agile_connect_db() {
    my ( $config, @output ) = @_;
    my ( $dsn, $dbh);
    if ( $config->{agile_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$config->{agile_db};host=$config->{agile_host}";
    }
    elsif ( $config->{agile_dbengine} eq "Pgsql" ) {
        $dsn =
"DBI:Pg:database=$config->{agile_db};host=$config->{agile_host}";
    }
    $dbh =
      DBI->connect( $dsn, $config->{agile_user},
        $config->{agile_pass} );
    if ( !$dbh ) {
        print STDERR "AGILE DATABASE IS DOWN\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to Agilebill Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Create ASTPP database.  This is deprecated and should be removed.
sub create_db() {
    my ( $config, @output ) = @_;
    my ($drh);
    if ( $config->{astpp_dbengine} eq "MySQL" ) {
        $drh = DBI->install_driver("mysql");
        if ( !$drh ) {
            print STDERR "ASTPP DEBUG\n" if $config->{debug} == 1;
            print STDERR "COULD NOT INSTALL DATABASE DRIVER!\n" if $config->{debug} == 1;
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
            print STDERR "ASTPP DEBUG\n" if $config->{debug} == 1;
            print STDERR "COULD NOT CREATE DATABASE!\n" if $config->{debug} == 1;
            print STDERR "DATABASE: $config->{dbname}\n" if $config->{debug} == 1;
            print STDERR "HOST:     $config->{dbhost}\n" if $config->{debug} == 1;
            print STDERR "USERNAME: $config->{dbuser}\n" if $config->{debug} == 1;
            print STDERR "PASSWORD: $config->{dbpass}\n" if $config->{debug} == 1;
        }
        else {
            return 0;
        }
    }
    elsif ( $config->{astpp_dbengine} eq "Pgsql" ) {
        $drh = DBI->install_driver("Pg");
        if ( !$drh ) {
            print STDERR "ASTPP DEBUG\n" if $config->{debug} == 1;
            print STDERR "COULD NOT INSTALL DATABASE DRIVER!\n" if $config->{debug} == 1;
            return 1;
        }
        if (
            !$drh->func(
                'createdb',        $config->{dbname}, $config->{dbhost},
                $config->{dbuser}, $config->{dbpass}
            )
          )
        {
            print STDERR "ASTPP DEBUG\n" if $config->{debug} == 1;
            print STDERR "COULD NOT CREATE DATABASE!\n" if $config->{debug} == 1;
            print STDERR "DATABASE: $config->{dbname}\n" if $config->{debug} == 1;
            print STDERR "HOST:     $config->{dbhost}\n" if $config->{debug} == 1;
            print STDERR "USERNAME: $config->{dbuser}\n" if $config->{debug} == 1;
            print STDERR "PASSWORD: $config->{dbpass}\n" if $config->{debug} == 1;
        }
        else {
            return 0;
        }
    }
}

# Load configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
sub load_config_db() {
    my ($astpp_db, $config) = @_;
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system WHERE reseller IS NULL";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}

# Load reseller configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
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

# Load brand specific configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
sub load_config_db_brand() {
    my ($astpp_db, $config,$brand) = @_;
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system WHERE brand = " . $astpp_db->quote($brand);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}

# Connect to opensips database.
sub connect_opensipsdb() {
    my ( $config, @output ) = @_;
    my ( $dbh, $dsn );
    if ( $config->{opensips_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{opensips_dbname};host=$config->{opensips_dbhost}";
    }
    elsif ( $config->{opensips_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{opensips_dbname};host=$config->{opensips_dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{opensips_dbuser}, $config->{opensips_dbpass} );
    if ( !$dbh ) {
        print STDERR "opensips DATABASE IS DOWN\n" if $config->{debug} == 1;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to opensips Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Connect to ASTPP database.
sub connect_db() {
    my ( $config, @output ) = @_;
    my ( $dbh, $dsn );
    if ( $config->{astpp_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{dbname};host=$config->{dbhost}";
    }
    elsif ( $config->{astpp_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{dbname};host=$config->{dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{dbuser}, $config->{dbpass} );
    if ( !$dbh ) {
        print STDERR "ASTPP DATABASE IS DOWN\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to ASTPP Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Connect to FreePBX database.
sub freepbx_connect_db() {
    my ( $config, @output ) = @_;
    my ( $dbh, $dsn );
    if ( $config->{freepbx_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$config->{freepbx_db};host=$config->{freepbx_host}";
    }
    elsif ( $config->{freepbx_dbengine} eq "Pgsql" ) {
        $dsn =
"DBI:Pg:database=$config->{freepbx_db};host=$config->{freepbx_host}";
    }
    $dbh = DBI->connect(
        $dsn,
        $config->{freepbx_user},
        $config->{freepbx_pass}
    );
    if ( !$dbh ) {
        print STDERR "FREEPBX DATABASE IS DOWN\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to FreePBX Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Connect to Asterisk(tm) cdr database.
sub cdr_connect_db() {
    my ( $config, @output ) = @_;
    my ( $dsn, $dbh);
    if ( $config->{cdr_dbengine} eq "MySQL" ) {
        $dsn =
          "DBI:mysql:database=$config->{cdr_dbname};host=$config->{cdr_dbhost}";
    }
    elsif ( $config->{cdr_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$config->{cdr_dbname};host=$config->{cdr_dbhost}";
    }
    print STDERR $dsn . "\n" if $config->{debug} == 1;
    $dbh = DBI->connect( $dsn, $config->{cdr_dbuser}, $config->{cdr_dbpass} );
    if ( !$dbh ) {
        print STDERR "CDR DATABASE IS DOWN\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to CDR Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Connect to Asterisk realtime database.
sub rt_connect_db() {
    my ( $config, @output ) = @_;
    my ( $dsn, $dbh);
    if ( $config->{rt_dbengine} eq "MySQL" ) {
        $dsn =
          "DBI:mysql:database=$config->{rt_db};host=$config->{rt_host}";
    }
    elsif ( $config->{rt_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$config->{rt_db};host=$config->{rt_host}";
    }
    print STDERR $dsn if $config->{debug} == 1;
    $dbh->disconnect if $dbh;
    $dbh = DBI->connect( $dsn, $config->{rt_user}, $config->{rt_pass} );
    if ( !$dbh ) {
        print STDERR gettext("Asterisk Realtime DATABASE IS DOWN!") . "\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to Realtime Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Connect to OSCommerce database.
sub osc_connect_db() {
    my ( $config, @output ) = @_;
    my ( $dsn, $dbh );
    if ( $config->{osc_dbengine} eq "MySQL" ) {
        $dsn =
"DBI:mysql:database=$config->{osc_db};host=$config->{osc_host}";
    }
    elsif ( $config->{osc_dbengine} eq "Pgsql" ) {
        $dsn =
          "DBI:Pg:database=$config->{osc_db};host=$config->{osc_host}";
    }
    print STDERR $dsn if $config->{debug} == 1;
    $dbh->disconnect if $dbh;
    $dbh =
      DBI->connect( $dsn, $config->{osc_user}, $config->{osc_pass} );
    if ( !$dbh ) {
        print STDERR gettext("OSCOMMERCE DATABASE IS DOWN") . "\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to OSCommerce Database!") . "\n" if $config->{debug} == 1;
        return $dbh;
    }
}

# Connect to Freeswitch(TM) database.
sub connect_freeswitch_db() {
    my ( $config, @output ) = @_;
    my ( $dbh, $dsn );
#    if ( $config->{astpp_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{freeswitch_dbname};host=$config->{freeswitch_dbhost}";
#    }
#    elsif ( $config->{astpp_dbengine} eq "Pgsql" ) {
#        $dsn = "DBI:Pg:database=$config->{dbname};host=$config->{dbhost}";
#    }
    $dbh = DBI->connect( $dsn, $config->{freeswitch_dbuser}, $config->{freeswitch_dbpass} );
    if ( !$dbh ) {	
        print STDERR "FREESWITCH(TM) DATABASE IS DOWN\n" if $config->{debug} == 1;
	return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        print STDERR gettext("Connected to Freeswitch(TM) Database!") . "\n"
          if ( $config->{debug} == 1 );
        return $dbh;
    }
}

# Calculate the cost of a call based on the data received.
sub calc_call_cost() {
    my ( $connect, $cost, $answeredtime, $increment, $inc_seconds ) = @_;
    print STDERR "Connect: $connect Cost: $cost Answered: $answeredtime \n" if $config->{debug} == 1;
    print STDERR " Inc: $increment included: $inc_seconds \n" if $config->{debug} == 1;
    if (!$increment || $increment == 0) {
	$increment = 1;
    }
    if ($answeredtime > 0) {
    my ($total_seconds);
    $total_seconds = ( $answeredtime - $inc_seconds ) / $increment if $inc_seconds;
    $total_seconds = ( $answeredtime ) / $increment if !$inc_seconds;
    if ( $total_seconds < 0 ) {
        $total_seconds = 0;
    }
    my $bill_increments = ceil($total_seconds);
    my $billseconds     = $bill_increments * $increment;
    $cost = ( $billseconds / 60 ) * $cost + $connect;
    print STDERR "AnsweredTime: $answeredtime Included Sec: $inc_seconds\n" if $config->{debug} == 1;
    print STDERR "Increment: $increment Total Increments: $total_seconds\n" if $config->{debug} == 1;
    print STDERR "Bill Seconds: $billseconds  Total cost is $cost\n" if $config->{debug} == 1;
    return $cost;
    } else {
	print STDERR "NO CHARGE - ANSWEREDTIME = 0\n" if $config->{debug} == 1;
	return 0;
   }
}

# Return a list of all trunk names.  Used mostly for menus.
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

# Remove DID from customers account and charge them as necessary.
sub remove_did() {
	my ($astpp_db,$config,$did,$account) = @_;
	my $callstart = &prettytimestamp();
	my ($didinfo, $status);
	my $notes = '';
	my $dest = gettext("DID:") . $did . gettext(" disconnection fee");
	my $accountdata = &get_account($astpp_db,$account);
	if ($accountdata->{reseller} ne '') {
		while ($accountdata->{reseller} ne '') {
			$didinfo = &get_did_reseller($astpp_db,$did,$accountdata->{reseller});
        		if ($didinfo->{disconnectionfee} != 0) {
				$dest = gettext("DID:") . $did . gettext(" disconnection fee");
				&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{disconnectionfee},$callstart,$accountdata->{postexternal},'',$notes,'','','');
			}
			$accountdata = &get_account($astpp_db, $accountdata->{reseller});
		}
		$didinfo = &get_did($astpp_db,$did,$account);
        	if ($didinfo->{disconnectionfee} != 0) {
			$dest = gettext("DID:") . $did . gettext(" disconnection fee");
			&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{disconnectionfee},$callstart,$accountdata->{postexternal},'',$notes,'','','');
		}
		$accountdata = &get_account($astpp_db,$account);
	} else {
		$didinfo = &get_did($astpp_db,$did,$account);
        	if ($didinfo->{disconnectionfee} != 0) {
			$dest = gettext("DID:") . $did . gettext(" disconnection fee");
			&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{disconnectionfee},$callstart,$accountdata->{postexternal},'',$notes,'','','');
		}
	}
	# If we got this far the cdrs have been posted and we're ready to cancel the DID. 
	my $tmp =
	           "UPDATE dids SET allocation_bill_status = 0, extensions = '', account = '' "
	         . " WHERE number = "
	         . $astpp_db->quote($did);
	print STDERR "$tmp\n" if $config->{debug} == 1;
	if ($astpp_db->do($tmp)) {
		$status .= gettext("DID unmapped successfully!");
	} else {
		$status .= gettext("DID failed to unmap!");
	}	
	return $status;
}

# This subroutine is used for DIDs that are set to only start the monthly billing after the first ANSWERED call.
#
sub apply_did_activated_charges() {
	my ($astpp_db,$config,$did,$account) = @_;
	my ($status,$cost,$start_date,$end_date,$null);
	my $didinfo = &get_did($astpp_db,$did);
	my $accountinfo = &get_account($astpp_db,$account);
#	if ($didinfo->{account} ne $accountinfo->{reseller} && $didinfo->{account} ne "") {
#		return gettext("This DID is owned by another customer already!");
#	}
	if ($accountinfo->{reseller} && $accountinfo->{reseller} ne "") {
		my $accountdata = &get_account($astpp_db,$account);
		while ($accountdata->{reseller} ne "") {
			$didinfo = &get_did_reseller($astpp_db,$did,$accountdata->{reseller});
			if ($didinfo->{prorate} == 1) {
				($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
			} else {
				($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
			}
			my $callstart = &prettytimestamp();
			my $notes = '';
			if ($didinfo->{chargeonallocation} == 0) {
			my $dest = gettext("DID:") . $did . gettext(" from ")
				. $start_date . gettext(" to ") . $end_date;
        		&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$cost,$callstart,$accountinfo->{postexternal},'',$notes,'','','');
			}
			$accountdata = &get_account($astpp_db,$accountdata->{reseller});
		}
		$didinfo = &get_did($astpp_db,$did);
		if ($didinfo->{prorate} == 1) {
			($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
		} else {
			($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
		}
		my $callstart = &prettytimestamp();
		my $notes = '';
		if ($didinfo->{chargeonallocation} == 0) {
			my $dest = gettext("DID:") . $did . gettext(" from ") . $start_date . gettext(" to ") . $end_date;
        		&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$cost,$callstart,$accountdata->{postexternal},'',$notes,'','','');
		}
	} else {
		my ($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
		my $callstart = &prettytimestamp();
		my $notes = '';
		if ($didinfo->{chargeonallocation} == 0) {
			my $dest = gettext("DID:") . $did . gettext(" from ") . $start_date . gettext(" to ") . $end_date;
        		&post_cdr($astpp_db,$config,'',$accountinfo->{number},'',$dest,'','',$cost,$callstart,$accountinfo->{postexternal},'',$notes,'','','');
		}
	}
	$astpp_db->do("UPDATE dids SET allocation_bill_status = 1 WHERE number = " . $astpp_db->quote($did));
}

# Apply DID to customers account and charge them and the resellers as appropriate.
sub purchase_did() {
	my ($astpp_db,$config,$did,$account) = @_;
	my ($status,$cost,$dest,$start_date,$end_date,$null);
	my $didinfo = &get_did($astpp_db,$did);
	my $accountinfo = &get_account($astpp_db,$account);
	if ($didinfo->{account} ne $accountinfo->{reseller} && $didinfo->{account} ne "") {
		return gettext("This DID is owned by another customer already!");
	}
	if ($accountinfo->{reseller} ne "") {
		my $accountdata = &get_account($astpp_db,$account);
		while ($accountdata->{reseller}) {
			my $didinfo = &get_did_reseller($astpp_db,$did,$accountdata->{reseller});
			my $did_min_available = $didinfo->{monthlycost} + $didinfo->{setup} + $didinfo->{disconnectionfee};
			my $credit = &accountbalance( $astpp_db, $accountdata->{number} ); # Find the available credit to the customer.
			print STDERR "Account Balance: " . $credit . "\n"  if $config->{debug} == 1;
			$credit = ($credit * -1) + ($accountdata->{credit_limit});  # Add on the accounts credit limit.
			if ($credit < $did_min_available) {
				return gettext ("Account: " . $accountdata->{number} . " does not have enough funds available.");
			} else {
				$accountdata = &get_account($astpp_db,$accountdata->{reseller});
			}
		}
		$accountdata = &get_account($astpp_db,$account);
		while ($accountdata->{reseller} ne "") {
			$didinfo = &get_did_reseller($astpp_db,$did,$accountdata->{reseller});
			if ($didinfo->{prorate} == 1) {
				($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
			} else {
				($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
			}
			my $callstart = &prettytimestamp();
			my $notes = '';
			if ($didinfo->{chargeonallocation} == 1) {
			my $dest = gettext("DID:") . $did . gettext(" from ")
				. $start_date . gettext(" to ") . $end_date;
        		&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$cost,$callstart,$accountinfo->{postexternal},'',$notes,'','','');
			}
        		if ($didinfo->{setup} != 0) {
				my $dest = gettext("DID:") . $did . gettext(" setup fee");
				&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{setup},$callstart,$accountinfo->{postexternal},'',$notes,'','','');
			}
			$accountdata = &get_account($astpp_db,$accountdata->{reseller});
		}
		$didinfo = &get_did($astpp_db,$did);
		if ($didinfo->{prorate} == 1) {
			($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
		} else {
			($null,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
		}
		my $callstart = &prettytimestamp();
		my $notes = '';
		if ($didinfo->{chargeonallocation} == 1) {
		my $dest = gettext("DID:") . $did . gettext(" from ")
			. $start_date . gettext(" to ") . $end_date;
        	&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$cost,$callstart,$accountdata->{postexternal},'',$notes,'','','');
		}
        	if ($didinfo->{setup} != 0) {
			$dest = gettext("DID:") . $did . gettext(" setup fee");
			&post_cdr($astpp_db,$config,'',$accountdata->{number},'',$dest,'','',$didinfo->{setup},$callstart,$accountdata->{postexternal},'',$notes,'','','');
		}
	} else {
		my $did_min_available = $didinfo->{monthlycost} + $didinfo->{setup} + $didinfo->{disconnectionfee};
		my $credit = &accountbalance( $astpp_db, $accountinfo->{number} ); # Find the available credit to the customer.
		print STDERR "Account Balance: " . $credit . "\n" if $config->{debug} == 1;
		$credit = ($credit * -1) + ($accountinfo->{credit_limit});  # Add on the accounts credit limit.
		if ($credit < $did_min_available) {
			return gettext ("Account: " . $accountinfo->{number} . " does not have enough funds available.");
		} else {
			my ($cost,$start_date,$end_date) = &prorate($didinfo->{monthlycost});
			my $callstart = &prettytimestamp();
			my $notes = '';
			if ($didinfo->{chargeonallocation} == 1) {
			my $dest = gettext("DID:") . $did . gettext(" from ")
				. $start_date . gettext(" to ") . $end_date;
        		&post_cdr($astpp_db,$config,'',$accountinfo->{number},'',$dest,'','',$cost,$callstart,$accountinfo->{postexternal},'',$notes,'','','');
			}
        		if ($didinfo->{setup} != 0) {
				$dest = gettext("DID:") . $did . gettext(" setup fee");
				&post_cdr($astpp_db,$config,'',$accountinfo->{number},'',$dest,'','',$didinfo->{setup},$callstart,$accountinfo->{postexternal},'',$notes,'','','');
			}
		}
	}
	# If we got this far the cdrs have been posted and we're ready 
	my $tmp =
	           "UPDATE dids SET extensions = "
	         . $astpp_db->quote($accountinfo->{extension})
	         . " WHERE number = "
	         . $astpp_db->quote($did);
	if ($astpp_db->do($tmp)) {
		$status .= gettext("DID mapped to extension successfully!");
	} else {
		$status .= gettext("DID failed to map to extension!");
	}	
	$tmp =
	           "UPDATE dids SET account = "
	         . $astpp_db->quote($account)
	         . " WHERE number = "
	         . $astpp_db->quote($did);
	if ($astpp_db->do($tmp)) {
		$status .= gettext("DID Assigned Successfully!");
	} else {
		$status .= gettext("DID Failed to Assign!");
	}	
	return $status;
}

# Find the appropriate charge based on the day of the month.  This is mostly used for DID setup fees.
sub prorate() {
	my ($amount) = @_;
	my $current_year = strftime "%Y", gmtime;
	my $current_month = strftime "%m", gmtime;
	my $current_day = strftime "%d", gmtime;
	use Time::DaysInMonth;
	my $days = days_in($current_year, $current_month);
	print STDERR "DAYS IN MONTH: $days \n" if $config->{debug} == 1;
	print STDERR "TODAY: $current_day \n" if $config->{debug} == 1;
	my $start_date = $current_year . "-" . $current_month . "-" . $current_day;
	my $end_date = $current_year . "-" . $current_month . "-" . $days;
	my $daily_charge = ($amount / $days);
	my $prorated = ($daily_charge * ($days - $current_day));
	return ($prorated,$start_date,$end_date);
}

# Return a list of voip provider accounts.  Used mostly for menus.
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

# Return a list of accounts.  This is currently only(I think) used by the "List Accounts" page.
sub list_accounts_selective() {
    my ( $astpp_db, $reseller, $type ) = @_;
    my ( $sql, @accountlist, $row, $tmp );
    $reseller = "" if !$reseller;
    if ( $type == -1 ) {
        $tmp =
            "SELECT number FROM accounts WHERE status < 2 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR "$tmp\n" if $config->{debug} == 1;
    }
    elsif ( $type == 0 || !$type ) {
        $tmp =
"SELECT number FROM accounts WHERE status < 2 AND type = 0 AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR "$tmp\n" if $config->{debug} == 1;
    }
    elsif ( $type > 0 ) {
        $tmp =
"SELECT number FROM accounts WHERE status < 2 AND type = '$type' AND reseller = "
          . $astpp_db->quote($reseller)
          . " ORDER BY number";
        print STDERR "$tmp\n" if $config->{debug} == 1;
    }
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}

# Return a list of all accounts either for the appropriate reseller or without reseller.
# Used for menuing
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
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}

sub list_all_accounts() {
    my ( $astpp_db ) = @_;
    my ( $sql, @accountlist, $row, $tmp );
    $tmp =
        "SELECT number FROM accounts WHERE status < 2"
      . " ORDER BY number";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}

# Return a list of all pricelists either for the appropriate reseller or without reseller.
# Used for menuing
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
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @pricelistlist, $row->{name};
    }
    $sql->finish;
    return @pricelistlist;
}

# Select a specific cdr from the astpp cdrs table.
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

# Select a specific cdr from the Asterisk(tm) cdr table.
sub get_cdr() {
    my ( $config, $cdr_db, $uniqueid,$rating,$dst ) = @_;
    my ( $sql, $cdrdata );
    if ($dst) {
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM $config->{cdr_table} WHERE uniqueid = " . $cdr_db->quote($uniqueid) . " AND dst = " . $cdr_db->quote($dst) . " ORDER BY cost DESC LIMIT 1" );
    }
    elsif ($rating) {
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM $config->{cdr_table} WHERE uniqueid = " . $cdr_db->quote($uniqueid) . " AND cost in ('error','none') ORDER BY cost DESC LIMIT 1" );
    } else {
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM $config->{cdr_table} WHERE uniqueid = " . $cdr_db->quote($uniqueid) . " ORDER by cost DESC LIMIT 1" );
    }
    $sql->execute;
    $cdrdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $cdrdata;
}

# Update the cost of a cdr in the Asterisk(tm) cdr table.  This is used to denote a cdr that has been rated.
sub save_cdr() {
    my ( $config, $cdr_db, $uniqueid, $cost,$dst ) = @_;
    $cdr_db->do( "UPDATE $config->{cdr_table} SET cost = "
          . $cdr_db->quote($cost)
          . "WHERE uniqueid = "
          . $cdr_db->quote($uniqueid)
	  . " AND dst = "
          . $cdr_db->quote($dst) 
	  . " LIMIT 1"); 
}

# Select all cdrs in the Asterisk(tm) cdr database which have a specified value in the cost field.  This is 
# used to select cdrs that have not been billed which have value "none" or those that the rating engine ran
# into a problem with which are marked "error".
sub list_cdrs_status() {
    my ( $config, $cdr_db, $default ) = @_;
    my ( $sql, @cdrlist, $row );
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM $config->{cdr_table} WHERE cost = " . $cdr_db->quote($default) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cdrlist, $row->{uniqueid};
    }
    return @cdrlist;
}

# Select all the unbilled CDRS belonging to a specific account.  This is mostly used by booths.
sub list_cdrs_account() {
    my ( $cdr_db,$config, $account, $cc ) = @_;
    my ( $sql, @cdrlist, $row );
    my $tmp = "SELECT * FROM $config->{asterisk_cdr_table} WHERE cost IN ('none', 'error') AND accountcode IN (" . $cdr_db->quote($account) . ", " . $cdr_db->quote($cc) . ") AND disposition REGEXP 'ANSWE.*'";
    print STDERR $tmp ."\n" if $config->{debug} == 1;
    $sql = $cdr_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cdrlist, $row->{uniqueid};
    }
    return @cdrlist;
}

# Update the cost of a cdr in the Asterisk(tm) cdr table.  This is used to denote a cdr that has been tagged
# to a vendor.
sub save_cdr_vendor() {
    my ( $config, $cdr_db, $uniqueid, $cost,$dst ) = @_;
    my $tmp =  "UPDATE $config->{cdr_table} SET vendor = "
          . $cdr_db->quote($cost)
          . " WHERE uniqueid = "
          . $cdr_db->quote($uniqueid)
	  . " AND dst = "
          . $cdr_db->quote($dst) 
	  . " LIMIT 1"; 
    print STDERR $tmp . "\n" if $config->{debug} ==1;
    $cdr_db->do($tmp);
}

# Select all cdrs in the Asterisk(tm) cdr database which have a specified value in the vendor field.  This is 
# used to select cdrs that have not been tagged to a vendor which have value "none" or those that the rating engine ran
# into a problem with which are marked "error".
sub list_cdrs_status_vendor() {
    my ( $config, $cdr_db, $default ) = @_;
    my ( $sql, @cdrlist, $row );
    $sql =
      $cdr_db->prepare(
        "SELECT * FROM $config->{cdr_table} WHERE vendor = " . $cdr_db->quote($default) );
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cdrlist, $row->{uniqueid};
    }
    return @cdrlist;
}

# Return the details on a specified DID from the ASTPP did table.
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

# Return information on a DID is the customer belongs to a reseller.
sub get_did_reseller() {
    my ( $astpp_db, $did,$reseller ) = @_;
    my ( $sql, $diddata );
    my $tmp = 
        "SELECT dids.number AS number, "
	. "reseller_pricing.monthlycost AS monthlycost, "
	. "reseller_pricing.prorate AS prorate, "
	. "reseller_pricing.setup AS setup, "
	. "reseller_pricing.cost AS cost, "
	. "reseller_pricing.connectcost AS connectcost, "
	. "reseller_pricing.includedseconds AS includedseconds, "
	. "reseller_pricing.inc AS inc, "
	. "reseller_pricing.disconnectionfee AS disconnectionfee, "
	. "dids.provider AS provider, "
	. "dids.country AS country, "
	. "dids.city AS city, "
	. "dids.province AS province, "
	. "dids.extensions AS extensions, "
	. "dids.account AS account, "
	. "dids.variables AS variables, "
	. "dids.options AS options, "
	. "dids.maxchannels AS maxchannels, "
	. "dids.chargeonallocation AS chargeonallocation, "
	. "dids.allocation_bill_status AS allocation_bill_status, "
	. "dids.limittime AS limittime, "
	. "dids.dial_as AS dial_as, "
	. "dids.status AS status "
	. "FROM dids, reseller_pricing "
	. "WHERE dids.number = " . $astpp_db->quote($did)
	. " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
	. $astpp_db->quote($reseller) . " AND reseller_pricing.note = "
	. $astpp_db->quote($did);
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql =
      $astpp_db->prepare($tmp);
    $sql->execute;
    $diddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $diddata;
}

# Update ASTPP account balance.
sub update_astpp_balance() {
    my ( $astpp_db, $account, $balance ) = @_;
    $astpp_db->do( "UPDATE accounts SET balance = "
          . $astpp_db->quote($balance)
          . " WHERE number = "
          . $astpp_db->quote($account) );
}

# Go looking for an account and even return accounts which have been closed.
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
    }
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE accountid = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
    }

}


# Go looking for an account and only return open accounts.
sub get_account() {
    my ( $astpp_db, $accountno,$allow_deactivated ) = @_;
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
    }
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $astpp_db->prepare( "SELECT * FROM accounts WHERE accountid = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
    }

}

# Go looking for account by CC number.
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

# Return data on specified pricelist
sub get_pricelist() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, $pricelistdata, $tmp );
    $tmp =
      "SELECT * FROM pricelists WHERE name = " . $astpp_db->quote($pricelist);
    $sql = $astpp_db->prepare($tmp);
    print STDERR "$tmp\n"  if $config->{debug} == 1;
    $sql->execute;
    $pricelistdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $pricelistdata;
}

# Return data on specified calling card brand
sub get_cc_brand() {
    my ( $astpp_db, $brand ) = @_;
    my ( $sql, $tmp, $branddata );
    $tmp =
        "SELECT * FROM callingcardbrands WHERE name = "
      . $astpp_db->quote($brand)
      . " AND status = 1";
    print STDERR "$tmp\n"  if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $branddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $branddata;
}

sub search_for_route(){
	my ($astpp_db,$config,$destination,$pricelist) = @_;
	my ($tmp,$sql,$record);
    $tmp = "SELECT * FROM routes WHERE "
          . $astpp_db->quote($destination)
          . " RLIKE pattern AND status = 1 AND pricelist = "
          . $astpp_db->quote($pricelist)
          . " ORDER BY LENGTH(pattern) DESC LIMIT 1";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql =
      $astpp_db->prepare($tmp); 
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
};

# Return the appropriate "route" to use for determining costing on a call.
sub get_route() {
    my ( $astpp_db, $config, $destination, $pricelist, $carddata, $type ) = @_;
    my ( $record,   $sql,    $tmp );
    if (defined $type && $type =~ /ASTPP-DID/) {
    	print STDERR "Call belongs to a DID.\n" if $config->{debug} == 1;
	$record = &get_did_reseller($astpp_db,$destination,$carddata->{reseller}) if $carddata->{reseller} ne "";
	$record = &get_did($astpp_db,$destination) if $carddata->{reseller} eq "";
	$record->{comment} = $record->{city} . "," . $record->{province} . "," . $record->{country};
	$record->{pattern} = "DID:" . $destination;
	$record->{pricelist} = $pricelist;
        my $branddata = &get_pricelist( $astpp_db, $pricelist);
	print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
    }
    elsif ($config->{thirdlane_mods} == 1 && $type =~ m/.\d\d\d-IN/) {
    	print STDERR "Call belongs to a Thirdlane(tm) DID.\n" if $config->{debug} == 1;
	($destination = $type) =~ s/-IN//g;
	print STDERR "Destination: $destination \n" if $config->{debug} == 1;
	$record = &get_did_reseller($astpp_db,$destination,$carddata->{reseller}) if $carddata->{reseller} ne "";
	$record = &get_did($astpp_db,$destination) if $carddata->{reseller} eq "";
	$record->{comment} = $record->{city} . "," . $record->{province} . "," . $record->{country};
	$record->{pattern} = "DID:" . $destination;
        my $branddata = &get_pricelist( $astpp_db, $pricelist);
	print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
    }
    else {
    	my @pricelists = split ( m/,/m, $pricelist );
    	foreach my $pricelistname (@pricelists) {
                $pricelistname =~ s/"//g;				#Strip off quotation marks
		print STDERR "Pricelist: $pricelistname \n"  if $config->{debug}==1;
    		$record = &search_for_route($astpp_db,$config,$destination,$pricelist);
    		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
		last if $record->{pattern}; #Returnes if we've found a match.
    	}

	while ( !$record->{pattern} && $carddata->{reseller} ) {
		$carddata = &get_account($astpp_db, $carddata->{reseller});	
	        $record = &search_for_route($astpp_db,$config,$destination,$pricelist);
		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
	}
	if (!$record->{pattern}) { #If we still haven't found a match then we modify the dialed number as per the regular expressions set
    				# in the account.
		my @regexs = split(m/","/m, $carddata->{dialed_modify});
		foreach my $regex (@regexs) {
	                $regex =~ s/"//g;				#Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
	                print STDERR "Grab: $grab\n" if $config->{debug} == 1;
	                print STDERR "Replacement: $replace\n" if $config->{debug} == 1;
	                print STDERR "Phone Before: $destination\n" if $config->{debug} == 1;
	                $destination =~ s/$grab/$replace/is;
			print STDERR "Phone After: $destination\n" if $config->{debug} == 1;
		}
	        $record = &search_for_route($astpp_db,$config,$destination,$config->{default_brand});
	        print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
	}
	if ( !$record->{pattern} ) { #If we have not found a route yet then we look in the "Default" pricelist.
	        $record = &search_for_route($astpp_db,$config,$destination,$config->{default_brand});
	        print STDERR "pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
	}
	print STDERR "Route: $record->{comment} Cost: $record->{cost} Pricelist: $record->{pricelist} Pattern: $record->{pattern}\n" if $record->{pattern} && $config->{debug}==1;
    } 
    if ($record->{inc} &&( $record->{inc} eq "" || $record->{inc} == 0 )) {
        my $branddata = &get_pricelist( $astpp_db, $pricelist);
        $record->{inc} = $branddata->{inc};
    }
    return $record;
}


# Return a specific charge from the Charge list.  Only look for those that are not deleted.
sub get_charge() {
    my ( $astpp_db, $chargeid ) = @_;
    my ( $sql, $chargedata, $tmp );
    $tmp =
        "SELECT * FROM charges WHERE id = "
      . $astpp_db->quote($chargeid)
      . " AND status < 2 LIMIT 1";
    print STDERR "$tmp\n" . "\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $chargedata = $sql->fetchrow_hashref;
    $sql->finish;
    return $chargedata;
}

# List the periodic charges tagged to a specific account.
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

# List the charges which could be tagged to any account.  This is only use for menuing.
sub list_applyable_charges() {
    my ($astpp_db) = @_;
    my ( $sql, %chargelist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT * FROM charges WHERE status < 2 AND pricelist = ''");
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        if ( $row->{charge} > 0 ) {
            $row->{charge} = $row->{charge} / 1;
            $row->{charge} = sprintf( "%.4f", $row->{charge} );
        }
        $chargelist{ $row->{id} } =
          $row->{description} . " - \$" . $row->{charge};
        print STDERR "CHARGEID: $row->{id}\n" if $config->{debug} == 1;
        print STDERR "CHARGE: %chargelist->{$row->{id}}\n" if $config->{debug} == 1;
        print STDERR "CHARGE: $row->{description} - $row->{charge}\n" if $config->{debug} == 1;
    }
    return %chargelist;
}

# List the periodic charges tagged to a specific pricelist.
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

# Check to see if the pin tagged to an account is unique.
sub finduniquepin() {
    my ( $astpp_db, $config ) = @_;
    my ( $pin, $count, $sql, $startingdigit, $record );
    for ( ; ; ) {
        $count = 1;
        $pin    =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
	$pin = $config->{pin_act_prepend} . $pin;
        if (   $config->{startingdigit} ne ""
            && $config->{startingdigit} ne "0" )
        {
            $startingdigit = substr( $pin, 0, 1 );
            if ( $startingdigit == $config->{startingdigit} ) {
                $pin = substr( $pin, 0, $config->{pinlength} );
                $sql =
                  $astpp_db->prepare(
                    "SELECT COUNT(*) FROM accounts WHERE pin = $pin");
                $sql->execute;
                $record = $sql->fetchrow_hashref;
                $count  = $record->{"COUNT(*)"};
                $sql->finish;
            }
        }
        else {
            print STDERR "DEBUG:" . $config->{cardlength} . " " . $pin;
            $pin = substr( $pin, 0, $config->{cardlength} );
            $sql =
              $astpp_db->prepare(
                "SELECT COUNT(*) FROM accounts WHERE pin = $pin");
            $sql->execute;
            $record = $sql->fetchrow_hashref;
            $count  = $record->{"COUNT(*)"};
            $sql->finish;
        }
        return $pin if ( $count == 0 );
    }
}

# Find unique CC number.  Each CC number must be unique.
sub finduniquecc() {
    my ( $astpp_db, $config ) = @_;
    my ( $cc, $count, $sql, $startingdigit, $record );
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
	$cc = $config->{cc_prepend} . $cc;
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

# Find unique callingcard number
sub finduniquecallingcard() {
    my ( $astpp_db, $config ) = @_;
    my ( $cc, $count, $startingdigit, $sql, $record,$account_count );
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
	$cc = $config->{cc_prepend} . $cc;
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
            $sql =
              $astpp_db->prepare(
                "SELECT COUNT(*) FROM accounts WHERE cc = "
                  . $astpp_db->quote($cc) );
            $sql->execute;
            $record = $sql->fetchrow_hashref;
            $account_count = $record->{"COUNT(*)"};
            $sql->finish;
        return $cc if ( $count == 0 && $account_count == 0 );
    }
}

# Refill and ASTPP account.
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
      . $astpp_db->quote($amount) . ", NOW())";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    if ( $astpp_db->do($tmp) ) {
        $status =
            gettext("Refilled account:")
          . " $account "
          . gettext("in the amount of:")
          . $amount / 1 . "\n" if $config->{debug} == 1;
        return $status;
    }
    else {
        $status = "$tmp " . gettext("FAILED!");
        return $status;
    }
}

sub write_callingcard_cdr() { # Write the callingcardcdr record if this is a calling card.
        my ($astpp_db, $config, $cardinfo,  $clid,   $destination, $status, $callstart, $charge, $answeredtime,$uid,$pricelist,$note,$pattern ) = @_;
        my ($sql);
        if (!$status) {$status = gettext("N/A"); }
        $sql =
"INSERT INTO callingcardcdrs (cardnumber,clid,destination,disposition,callstart,seconds,"
          . "debit,uniqueid,pricelist,notes,pattern) VALUES ("
	  . $astpp_db->quote( $cardinfo->{cardnumber} ) . ", "
          . $astpp_db->quote($clid) . ", "
          . $astpp_db->quote($destination) . ", "
          . $astpp_db->quote($status) . ", "
          . $astpp_db->quote($callstart) . ", "
          . $astpp_db->quote($answeredtime) . ", "
          . $astpp_db->quote($charge) . ","
	  . $astpp_db->quote($uid).","
	  . $astpp_db->quote($pricelist).","
	  . $astpp_db->quote($note).","
	  . $astpp_db->quote($pattern).")";
        $astpp_db->do($sql);
        print STDERR $sql . "\n" if $config->{debug} == 1;
}

# Write cdr to the ASTPP cdr database.  This is also used to apply charges and credits to an account.
sub write_account_cdr() {
    my ( $astpp_db, $account, $amount, $description, $timestamp, $answeredtime, $uniqueid, $clid, $pricelist, $pattern )
      = @_;    # The amount shall be passed in 100ths of a penny.
    my ( $sql, $status );
    $description  = ""  if !$timestamp;
    $pricelist  = ""  if !$pricelist;
    $pattern  = ""  if !$pattern;
    $answeredtime = "0" if !$answeredtime;
    $uniqueid = "N/A" if $uniqueid eq "" || !$uniqueid;
    $clid = "N/A" if $clid eq "" || !$clid;
    $timestamp = &prettytimestamp if !$timestamp;
    my $tmp =
"INSERT INTO cdrs (uniqueid, cardnum, callednum, debit, billseconds, callstart,callerid,pricelist,pattern) VALUES ("
      . $astpp_db->quote($uniqueid) . ", "
      . $astpp_db->quote($account) . ","
      . $astpp_db->quote($description) . ", "
      . $astpp_db->quote($amount) . ", "
      . $astpp_db->quote($answeredtime) . ", "
	. $astpp_db->quote($timestamp) . ", "
      . $astpp_db->quote($clid) . ","
      . $astpp_db->quote($pricelist) . ","
      . $astpp_db->quote($pattern) . ")";
    if ( $astpp_db->do($tmp) ) {
        $status =
          "POSTED CDR: $account in the amount of: " . $amount / 1 . "\n" if $config->{debug} == 1;
        return $status;
    }
    else {
        $status = "$tmp FAILED!";
        return $status;
    }
}

# Find the total balance of all accounts in the system.
sub accounts_total_balance() {
    my ($astpp_db,$reseller) = @_;
    my ( $tmp, $sql, $row, $debit, $credit, $balance, $posted_balance );
    if (!$reseller || $reseller eq "") {
      $tmp = "SELECT SUM(debit) FROM cdrs WHERE status NOT IN (1, 2)";
      $sql = $astpp_db->prepare($tmp);
      $sql->execute;
      $row   = $sql->fetchrow_hashref;
      $debit = $row->{"SUM(debit)"};
      $tmp   = "SELECT SUM(credit) FROM cdrs WHERE status NOT IN (1, 2)";
      $sql   = $astpp_db->prepare($tmp);
      $sql->execute;
      $row   = $sql->fetchrow_hashref;
      $credit = $row->{"SUM(credit)"};
      $tmp   = "SELECT SUM(balance) FROM accounts WHERE reseller = ''";
    } else {
    	$tmp   = "SELECT SUM(balance) FROM accounts WHERE reseller = " . $astpp_db->quote($reseller);
    }
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

# Return the balance for a specific ASTPP account.
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

# Return a list of accounts belonging to a specific pricelist.
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

# List all DIDs tagged to a specific account.
# Used in menuing
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

# List all DIDs tagged to a specific account.
# Used in menuing - Only returns the number.
sub list_dids_number_account() {
    my ( $astpp_db, $account ) = @_;
    my ( $sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT number FROM dids WHERE status = 1 AND account = "
      . $astpp_db->quote($account);
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @didlist, $row->{number};
    }
    $sql->finish;
    @didlist = sort @didlist;
    return @didlist;
}

# Provide a list of a available DIDs.
sub list_available_dids() {
	my ( $astpp_db, $account ) = @_;
	my $accountinfo = &get_account($astpp_db, $account);
	my @didlist = &list_dids_number_account($astpp_db, "");
	my @resellerdidlist = &list_dids_number_account($astpp_db, $accountinfo->{reseller});
	push (@didlist, @resellerdidlist);
	if ($accountinfo->{reseller} ne "") {
	my (@availabledids,$sql);
	foreach my $did (@didlist) {
      my $tmp =
        "SELECT dids.number AS number, "
        . "reseller_pricing.monthlycost AS monthlycost, "
        . "reseller_pricing.prorate AS prorate, "
        . "reseller_pricing.setup AS setup, "
        . "reseller_pricing.cost AS cost, "
        . "reseller_pricing.disconnectionfee AS disconnectionfee "
        . "FROM dids,reseller_pricing "
        . "WHERE dids.number = " . $astpp_db->quote($did)
        . " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
        . $astpp_db->quote($accountinfo->{reseller}) . " AND reseller_pricing.note = "
        . $astpp_db->quote($did);	
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( my $row = $sql->fetchrow_hashref ) {
        push @availabledids, $row->{number};
    }
    $sql->finish;
	}
    return @availabledids;
	} else {
		return @didlist;
	}
}

# Write ASTPP cdr.  I think this one is mostly deprecated but should probably be completely removed.
sub post_cdr() {
	my (
			$astpp_db,     $config, $uniqueid, $account, $clid,
			$dest,         $disp,       $seconds,  $cost,    $callstart,
			$postexternal, $trunk,      $notes,$pricelist,$pattern,$calltype,$provider
	   ) = @_;

# The cost is passed in 100ths of a penny.
	my ( $tmp, $status );
	$trunk    = gettext("N/A") if ( !$trunk );
	$uniqueid = gettext("N/A") if ( !$uniqueid );
	$pricelist = gettext("N/A") if ( !$pricelist );
	$pattern = gettext("N/A") if ( !$pattern );
	$provider = gettext("N/A") if ( !$provider );
	$status   = 0;
	$tmp      =
		"INSERT INTO cdrs(uniqueid,cardnum,callerid,callednum,trunk,disposition,billseconds,"
		. "debit,callstart,status,notes,pricelist,pattern,calltype,provider) VALUES ("
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
		. $astpp_db->quote($notes) . ","
		. $astpp_db->quote($pricelist) . ","
		. $astpp_db->quote($pattern) . ","
		. $astpp_db->quote($calltype) . ","
		. $astpp_db->quote($provider) . ")";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$astpp_db->do($tmp);
}

############### Integration with AgileBill starts here ##################
# I'm not commenting all of this as it could probably be deprecated.  I'm
# not sure we should be encouraging AgileBill usage as our OSCommerce support 
# is getting better.


# Save a call to Agilebill(tm) for external billing.
sub agilesavecdr() {
    my (
        $agile_db, $astpp_db, $config,
        $carddata, $billcost, $site_id, $cdrinfo,    $dbprefix, @output
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
              . $agile_db->quote( $config->{agile_charge_status} ) . ", "
              . $agile_db->quote( $carddata->{sweep} ) . ", "
              . $agile_db->quote( $account_info->{id} ) . ", "
              . $agile_db->quote($billcost) . ", 1, "
              . $agile_db->quote( $config->{agile_taxable} ) . ", "
              . $agile_db->quote($attributes)
              . ")" )
          || &agile_unbill;
    }
    else {
        &agile_unbill( $agile_db, $astpp_db, $config, 
            $cdrinfo->{uniqueid}, $uniqueid, @output );

    }
}

# Mark a call in AgileBill as not billed.
sub agile_unbill() {
    my ( $agile_db, $astpp_db, $config, $uniqueid,
        $agile_uniqueid, @output )
      = @_;
    print STDERR "Due to an AgileBill error, I'm removing and unbilling this uniqeid $uniqueid\n" if $config->{debug} == 1;
    &saveastcdr( $uniqueid, "error" ) if $config->{astcdr} == 1;
    $astpp_db->do(
        "DELETE FROM cdrs WHERE uniqueid = " . $astpp_db->quote($uniqueid) );
    $agile_db->(
        "DELETE FROM charges WHERE id= " . $astpp_db->quote($agile_uniqueid) );
}

# Ensure that the id used for each cdr in AgileBill(tm) is unique.
sub agile_isunique() {
    my ( $agile_db, $number, $dbprefix ) = @_;
    my $clause = "WHERE id = " . $agile_db->quote($number);
    my $count = &agile_count_cards( $agile_db, $clause, $dbprefix );
    return 1 if $count == "0";
    return 0;
}

# Ensure that the id used for each cdr in AgileBill(tm) is unique.
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

# Return a list of sip devices belong to a specific account
sub list_sip_account_rt() {
    my ( $rt_db, $config, $name, $cc) = @_;
    my ($tmp,$row, $sql, @devicelist);
    if ($config->{debug} == 1) {
	print STDERR "NAME: $name";
	print STDERR "CC: $cc";
    }
    $tmp = "SELECT name FROM $config->{rt_sip_table} WHERE accountcode IN (" 
		. $rt_db->quote($name) . ","
		. $rt_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $rt_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @devicelist, $row->{name};
    }
    $sql->finish;
    return @devicelist;
}

sub get_sip_account_rt() {
	my ($rt_db,$config,$name) = @_;
    my $tmp = "SELECT * FROM $config->{rt_sip_table} WHERE name = " 
		. $rt_db->quote($name) . " LIMIT 1";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    my $sql = $rt_db->prepare($tmp);
    $sql->execute;
    my $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

sub get_iax_account_rt() {
	my ($rt_db,$config,$name) = @_;
    my $tmp = "SELECT * FROM $config->{rt_iax_table} WHERE name = " 
		. $rt_db->quote($name) . " LIMIT 1";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    my $sql = $rt_db->prepare($tmp);
    $sql->execute;
    my $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

# Add a SIP user to the asterisk realtime DB.
sub add_sip_user_rt() {
    my ( $rt_dbh, $config, $name, $secret, $context, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
	$name =~ s/\W//mg;
	$username =~ s/\W//mg;
    my $mailbox = $name . "\@" . $config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";
	if (!$config->{sip_port}) {
		$config->{sip_port} = 5060;
	}
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n" if $config->{debug} == 1;
        print STDERR "NAME: $name\n" if $config->{debug} == 1;
        print STDERR "USERNAME: $username\n" if $config->{debug} == 1;
        print STDERR "CANREINVITE: $config->{rt_sip_canreinvite} \n" if $config->{debug} == 1;
        print STDERR "CONTEXT: $context\n" if $config->{debug} == 1;
        print STDERR "INSECURE:  $config->{rt_sip_insecure} \n" if $config->{debug} == 1;
        print STDERR "MAILBOX: $mailbox \n" if $config->{debug} == 1;
        print STDERR "NAT:  $config->{rt_sip_nat}\n" if $config->{debug} == 1;
        print STDERR "SIP PORT: $config->{sip_port}\n" if $config->{debug} == 1;
        print STDERR "SIP QUALIFY: $config->{rt_sip_qualify} \n" if $config->{debug} == 1;
        print STDERR "SECRET: $secret\n" if $config->{debug} == 1;
        print STDERR "SIP TYPE: $config->{rt_sip_type}\n" if $config->{debug} == 1;
        print STDERR "CODEC DISALLOW: $config->{rt_codec_disallow}\n" if $config->{debug} == 1;
        print STDERR "CODEC ALLOW: $config->{rt_codec_allow}\n" if $config->{debug} == 1;
        print STDERR "CANCALLFORWARD: $config->{rt_sip_cancallforward}\n" if $config->{debug} == 1;
    }
    $tmp =
        "INSERT INTO $config->{rt_sip_table} (callerid,name,accountcode,"
      . "canreinvite,context,host,insecure,mailbox,"
      . "nat,port,qualify,secret,type,username,disallow,allow,"
      . "cancallforward) VALUES ("
      . $rt_dbh->quote($clid) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote( $config->{rt_sip_canreinvite} ) . ", "
      . $rt_dbh->quote($context) . ", "
      . $rt_dbh->quote( $config->{ipaddr} ) . ", "
      . $rt_dbh->quote( $config->{rt_sip_insecure} ) . ", "
      . $rt_dbh->quote($mailbox) . ", "
      . $rt_dbh->quote( $config->{rt_sip_nat} ) . ", "
      . $rt_dbh->quote( $config->{sip_port} ) . ", "
      . $rt_dbh->quote( $config->{rt_sip_qualify} ) . ", "
      . $rt_dbh->quote($secret) . ", "
      . $rt_dbh->quote( $config->{rt_sip_type} ) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote( $config->{rt_codec_disallow} ) . ", "
      . $rt_dbh->quote( $config->{rt_codec_allow} ) . ", "
      . $rt_dbh->quote( $config->{rt_sip_cancallforward} ) . ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    if ( !$rt_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("SIP Device Creation Failed!");
    }
    else {
        return gettext("SIP Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    }
}

# Return a list of iax devices belong to a specific account
sub list_iax_account_rt() {
    my ( $rt_db, $config, $name, $cc) = @_;
    my ($tmp, $row,$sql, @devicelist);
#    if ($config->{debug} == 1) {
	print STDERR "NAME: $name";
	print STDERR "CC: $cc";
#    }
    $tmp = "SELECT name FROM $config->{rt_iax_table} WHERE accountcode IN (" 
		. $rt_db->quote($name) . ","
		. $rt_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $rt_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @devicelist, $row->{name};
    }
    $sql->finish;
    return @devicelist;
}


# Add an IAX2 device to the asterisk realtime DB.
sub add_iax_user_rt() {
    my ( $rt_dbh, $config, $name, $secret, $context, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
	$name =~ s/\W//mg;
	$username =~ s/\W//mg;
    my $mailbox = $name . "\@" . $config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";

    #    my $clid = "<$name>";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n" if $config->{debug} == 1;
        print STDERR "NAME: $name\n" if $config->{debug} == 1;
        print STDERR "USERNAME: $username\n" if $config->{debug} == 1;
        print STDERR "CANREINVITE: $config->{rt_sip_canreinvite} \n" if $config->{debug} == 1;
        print STDERR "CONTEXT: $context\n" if $config->{debug} == 1;
        print STDERR "INSECURE:  $config->{rt_sip_insecure} \n" if $config->{debug} == 1;
        print STDERR "MAILBOX: $mailbox \n" if $config->{debug} == 1;
        print STDERR "NAT:  $config->{rt_sip_nat}\n" if $config->{debug} == 1;
        print STDERR "SIP PORT: $config->{sip_port}\n" if $config->{debug} == 1;
        print STDERR "SIP QUALIFY: $config->{rt_sip_qualify} \n" if $config->{debug} == 1;
        print STDERR "SECRET: $secret\n" if $config->{debug} == 1;
        print STDERR "IAX TYPE: $config->{rt_iax_type}\n" if $config->{debug} == 1;
        print STDERR "CODEC DISALLOW: $config->{rt_codec_disallow}\n" if $config->{debug} == 1;
        print STDERR "CODEC ALLOW: $config->{rt_codec_allow}\n" if $config->{debug} == 1;
    }
    $tmp =
        "INSERT INTO $config->{rt_iax_table} (callerid,name,accountcode,"
      . "context,host,mailbox,"
      . "port,qualify,secret,type,username,disallow,allow"
      . ") VALUES ("
      . $rt_dbh->quote($clid) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote($context) . ", "
      . $rt_dbh->quote( $config->{ipaddr} ) . ", "
      . $rt_dbh->quote($mailbox) . ", "
      . $rt_dbh->quote( $config->{iax_port} ) . ", "
      . $rt_dbh->quote( $config->{rt_sip_qualify} ) . ", "
      . $rt_dbh->quote($secret) . ", "
      . $rt_dbh->quote( $config->{rt_iax_type} ) . ", "
      . $rt_dbh->quote($name) . ", "
      . $rt_dbh->quote( $config->{rt_codec_disallow} ) . ", "
      . $rt_dbh->quote( $config->{rt_codec_allow} ) . ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    if ( !$rt_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("IAX2 Device Creation Failed!");
    }
    else {
        return gettext("IAX2 Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    }
}

#Update the context on a SIP device in Asterisk(tm) realtime.
sub update_context_sip_user_rt {
    my ( $rt_db, $config, $name, $context ) = @_;
    $rt_db->do( "UPDATE $config->{rt_sip_table} SET context = "
          . $rt_db->quote($context)
          . " WHERE name = "
          . $rt_db->quote($name) );
}

#Update the context on an IAX2 device in Asterisk(tm) realtime.
sub update_context_iax_user_rt {
    my ( $rt_db, $config, $name, $context ) = @_;
    $rt_db->do( "UPDATE $config->{rt_iax_table} SET context = "
          . $rt_db->quote($context)
          . " WHERE name = "
          . $rt_db->quote($name) );
}

# Remove SIP device from Asterisk(tm) realtime.
sub del_sip_user_rt() {
    my ( $rt_db, $config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n" if $config->{debug} == 1;
    $tmp =
      "DELETE FROM $config->{rt_sip_table} WHERE name = "
      . $rt_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    $rt_db->do($tmp) || print "$tmp failed";
}

# Remove IAX2 device from Asterisk(tm) realtime.
sub del_iax_user_rt() {
    my ( $rt_db, $config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n" if $config->{debug} == 1;
    $tmp =
      "DELETE FROM $config->{rt_iax_table} WHERE name = "
      . $rt_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    $rt_db->do($tmp) || print "$tmp failed";
}

#######  Realtime Integration Ends ################
#######  opensips Integration Starts ###############
# Add SIP device to opensips database.
sub add_sip_user_opensips() {
    my ( $opensips_dbh, $config, $name, $secret, $accountcode,$params)= @_;
    my ( $md5secret, $tmp, $id, $appdata );
#     my $datetime = &prettytimestamp();
# 	$name =~ s/\W//mg;
# 	$username =~ s/\W//mg;
    $tmp =
        "INSERT INTO subscriber (username,domain,password,accountcode"
      . ") VALUES ("
      . $opensips_dbh->quote($name) . ", "
      . $opensips_dbh->quote($config->{opensips_domain}) . ", "
      . $opensips_dbh->quote($secret) . ", "      
      . $opensips_dbh->quote($accountcode) . ")";
      print STDERR " $tmp \n" if $config->{debug} == 1;
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    if ( !$opensips_dbh->do($tmp) ) {
        print "$tmp failed";
        return gettext("SIP Device Creation Failed!");
    }
    else {
        return gettext("SIP Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    }
}

# Remove SIP user from opensips.
sub del_sip_user_opensips() {
    my ( $opensips_db, $config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n" if $config->{debug} == 1;
    $tmp =
      "DELETE FROM subscriber WHERE username = "
      . $opensips_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    $opensips_db->do($tmp) || print "$tmp failed";
}

#######  opensips Integration Ends ###############
#######  Freeswitch Integration Starts ###############

sub get_sip_account_freeswitch(){
        my ($fs_db,$config,$directory_id) = @_;
        my ($tmp,$record,$sql,$deviceinfo);
        $tmp = "SELECT var_value FROM directory_vars WHERE directory_id = " 
		. $fs_db->quote($directory_id)
		. " AND var_name = 'user_context'";
        print STDERR "$tmp\n" if $config->{debug} == 1;
        $sql = $fs_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $deviceinfo->{context} = $record->{var_value};

        $tmp = "SELECT param_value FROM directory_params WHERE directory_id = "
		. $fs_db->quote($directory_id)
                . " AND param_name = 'password' LIMIT 1";
        print STDERR "$tmp\n" if $config->{debug} == 1;
        $sql = $fs_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $deviceinfo->{password} = $record->{param_value};

        $tmp = "SELECT param_value FROM directory_params WHERE directory_id = "
		. $fs_db->quote($directory_id)
                . " AND param_name = 'vm-password' LIMIT 1";
        print STDERR "$tmp\n" if $config->{debug} == 1;
        $sql = $fs_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $deviceinfo->{vmpassword} = $record->{param_value};

        $tmp = "SELECT var_value FROM directory_vars WHERE directory_id = "
		. $fs_db->quote($directory_id)
                . " AND var_name = 'accountcode' LIMIT 1";
        print STDERR "$tmp\n" if $config->{debug} == 1;
        $sql = $fs_db->prepare($tmp);
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $sql->finish;
        $deviceinfo->{accountcode} = $record->{var_value};


        return $deviceinfo;
}


# Return a list of sip devices belong to a specific account
sub list_sip_account_freeswitch() {
    my ( $fs_db, $config, $name, $cc) = @_;
    my ($tmp, $row, $sql, @devicelist);
    if ($config->{debug} == 1) {
	print STDERR "NAME: $name";
	print STDERR "CC: $cc";
    }
    $tmp = "select directory_id from directory_vars where var_name = 'accountcode' and var_value IN (" 
		. $fs_db->quote($name) . ","
		. $fs_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $fs_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @devicelist, $row->{directory_id};
    }
    $sql->finish;
    return @devicelist;
}

# Check to see if a SIP account already exists in ATSPP.  The first 5 digits of the device ID are random followed by a dash
# and then the accountcode.
sub finduniquesip_freeswitch() {
    my ($fs_db, $config, $name) = @_;
    my ( $cc, $sql, $count, $sipid, $record );
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
        print STDERR "SIPID: $sipid\n" if $config->{debug} == 1;
        $sql =
          $fs_db->prepare(
            "SELECT COUNT(*) FROM directory WHERE username = "
              . $fs_db->quote($sipid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $sipid if ( $count == 0 );
    }
}


# Add a SIP user to the FreeSwitch DB.
sub add_sip_user_freeswitch() {
    my ( $fs_db, $config, $name, $secret, $username,
        $params,$cc )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
	$name =~ s/\W//mg;
	$username =~ s/\W//mg;
    if ( $config->{debug} == 1 ) {
        print STDERR "NAME: $name\n" if $config->{debug} == 1;
        print STDERR "USERNAME: $username\n" if $config->{debug} == 1;
        print STDERR "SECRET: $secret\n" if $config->{debug} == 1;
    }
    $tmp =
        "INSERT INTO directory (username,domain) VALUES ("
      . $fs_db->quote($name) . ", "
      . $fs_db->quote($config->{freeswitch_domain}). ")";
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    my $sql = $fs_db->prepare($tmp);
    if ( !$sql->execute ) {
        print "$tmp failed";
        return gettext("SIP Device Creation Failed!");
    }
    else {
	my $directory_id = $sql->{'mysql_insertid'};
	$fs_db->do("INSERT INTO directory_vars (directory_id,var_name,var_value) VALUES ("
		. $fs_db->quote($directory_id) . ","
		. "'accountcode',"
		. $fs_db->quote($username) . ")");

	$fs_db->do("INSERT INTO directory_vars (directory_id,var_name,var_value) VALUES ("
		. $fs_db->quote($directory_id) . ","
		. "'user_context',"
		. $fs_db->quote($config->{freeswitch_context}) . ")");

	$fs_db->do("INSERT INTO directory_params (directory_id,param_name,param_value) VALUES ("
		. $fs_db->quote($directory_id) . ","
		. "'vm-password',"
		. $fs_db->quote($secret) . ")");

	$fs_db->do("INSERT INTO directory_params (directory_id,param_name,param_value) VALUES ("
		. $fs_db->quote($directory_id) . ","
		. "'password',"
		. $fs_db->quote($secret) . ")");

        return gettext("SIP Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    }
}

#######  Freeswitch Integration ends ###############
#######  FreePBX subroutines start here ###########

sub get_iax_account_freepbx(){
	my ($freepbx_db,$config,$name) = @_;
	my ($tmp,$record,$sql,$deviceinfo);
	$tmp = "SELECT value FROM $config->{freepbx_iax_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'context' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{context} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_iax_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'secret' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{secret} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_iax_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'type' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{type} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_iax_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'username' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{username} = $record;
	return $deviceinfo;
}

sub get_sip_account_freepbx(){
	my ($freepbx_db,$config,$name) = @_;
	my ($tmp,$record,$sql,$deviceinfo);
	$tmp = "SELECT value FROM $config->{freepbx_sip_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'context' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{context} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_sip_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'secret' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{secret} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_sip_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'type' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{type} = $record;
	$tmp = "SELECT value FROM $config->{freepbx_sip_table} WHERE id = "		  . $freepbx_db->quote($name) 
		. " AND keyword = 'username' LIMIT 1)";
	print STDERR "$tmp\n" if $config->{debug} == 1;
	$sql = $freepbx_db->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{username} = $record;
	return $deviceinfo;
}


# Return a list of sip devices belong to a specific account
sub list_sip_account_freepbx() {
    my ( $freepbx_db, $config, $name, $cc) = @_;
    my ($tmp, $row, $sql, @devicelist);
#    if ($config->{debug} == 1) {
	print STDERR "NAME: $name";
	print STDERR "CC: $cc";
#    }
    $tmp = "SELECT id FROM $config->{freepbx_sip_table} WHERE keyword = 'accountcode' AND value IN (" 
		. $freepbx_db->quote($name) . ","
		. $freepbx_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $freepbx_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @devicelist, $row->{id};
    }
    $sql->finish;
    return @devicelist;
}

# Check to see if a SIP account already exists in ATSPP.  The first 5 digits of the device ID are random followed by a dash
# and then the accountcode.
sub finduniquesip_freepbx() {
    my ($freepbx_db, $config, $name) = @_;
    my ( $cc, $sql, $count, $sipid, $record );
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
        print STDERR "SIPID: $sipid\n" if $config->{debug} == 1;
        $sql =
          $freepbx_db->prepare(
            "SELECT COUNT(*) FROM $config->{freepbx_sip_table} WHERE id = "
              . $freepbx_db->quote($sipid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $sipid if ( $count == 0 );
    }
}

# Check to see if an IAX2 account already exists in ATSPP.  The first 5 digits of the device ID are random followed by a dash
# and then the accountcode.
sub finduniqueiax_freepbx() {
    my ($freepbx_db, $config, $name) = @_;
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
        print STDERR "IAXID: $iaxid\n" if $config->{debug} == 1;
        $sql =
          $freepbx_db->prepare(
            "SELECT COUNT(*) FROM $config->{freepbx_iax_table} WHERE id = "
              . $freepbx_db->quote($iaxid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $iaxid if ( $count == 0 );
    }
}


# Check to see if a SIP account already exists in ATSPP.  The first 5 digits of the device ID are random followed by a dash
# and then the accountcode.
sub finduniquesip_opensips() {
    my ($opensips_db, $config, $name) = @_;
    my ( $cc, $sql, $count, $sipid, $record );
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
        print STDERR "SIPID: $sipid\n" if $config->{debug} == 1;
        $sql =
          $opensips_db->prepare(
            "SELECT COUNT(*) FROM subscriber WHERE username = "
              . $opensips_db->quote($sipid) );
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $count  = $record->{"COUNT(*)"};
        $sql->finish;
        return $sipid if ( $count == 0 );
    }
}

# Add a SIP user to FreePBX.
sub add_sip_user_freepbx() {
    my (
        $freepbx_db, $config,  $name,
        $secret,     $context, $username,   $params, $cc
      )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
	$name =~ s/\W//mg;
	$username =~ s/\W//mg;
    my $mailbox = $name . "\@" . $config->{freepbx_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n" if $config->{debug} == 1;
        print STDERR "NAME: $name\n" if $config->{debug} == 1;
        print STDERR "USERNAME: $username\n" if $config->{debug} == 1;
        print STDERR "CANREINVITE: $config->{rt_sip_canreinvite} \n" if $config->{debug} == 1;
        print STDERR "CONTEXT: $context\n" if $config->{debug} == 1;
        print STDERR "INSECURE:  $config->{rt_sip_insecure} \n" if $config->{debug} == 1;
        print STDERR "MAILBOX: $mailbox \n" if $config->{debug} == 1;
        print STDERR "NAT:  $config->{rt_sip_nat}\n" if $config->{debug} == 1;
        print STDERR "SIP PORT: $config->{sip_port}\n" if $config->{debug} == 1;
        print STDERR "SIP QUALIFY: $config->{rt_sip_qualify} \n" if $config->{debug} == 1;
        print STDERR "SECRET: $secret\n" if $config->{debug} == 1;
        print STDERR "SIP TYPE: $config->{rt_sip_type}\n" if $config->{debug} == 1;
        print STDERR "CODEC DISALLOW: $config->{rt_codec_disallow}\n" if $config->{debug} == 1;
        print STDERR "CODEC ALLOW: $config->{rt_codec_allow}\n" if $config->{debug} == 1;
        print STDERR "CANCALLFORWARD: $config->{rt_sip_cancallforward}\n" if $config->{debug} == 1;
    }
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'account', "
      . $freepbx_db->quote($name) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'accountcode', "
      . $freepbx_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'secret', "
      . $freepbx_db->quote($secret) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'canreinvite', "
      . $freepbx_db->quote( $config->{freepbx_sip_canreinvite} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'context', "
      . $freepbx_db->quote($context) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'dtmfmode', "
      . $freepbx_db->quote( $config->{freepbx_sip_dtmfmode} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'host', "
      . $freepbx_db->quote( $config->{ipaddr} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'type', "
      . $freepbx_db->quote( $config->{freepbx_sip_type} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'mailbox', "
      . $freepbx_db->quote($mailbox) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'nat', "
      . $freepbx_db->quote( $config->{freepbx_sip_nat} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'port', "
      . $freepbx_db->quote( $config->{sip_port} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'qualify', "
      . $freepbx_db->quote( $config->{freepbx_sip_qualify} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callgroup', "
      . $freepbx_db->quote( $config->{freepbx_sip_callgroup} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'pickupgroup', "
      . $freepbx_db->quote( $config->{freepbx_sip_pickupgroup} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'disallow', "
      . $freepbx_db->quote( $config->{freepbx_codec_disallow} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'allow', "
      . $freepbx_db->quote( $config->{freepbx_codec_allow} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_sip_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callerid', "
      . $freepbx_db->quote($clid) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
        "INSERT INTO devices (id,tech,dial,devicetype,user) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'SIP', 'SIP/$name', 'FIXED', "
      . $freepbx_db->quote($name) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    if ($freepbx_db->do($tmp)) {
        return gettext("SIP Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    } else {
        return gettext("SIP Device Creation Failed!");
    }
}

# Return a list of sip devices belong to a specific account
sub list_iax_account_freepbx() {
    my ( $freepbx_db, $config, $name, $cc) = @_;
    my ($tmp,$row, $sql, @devicelist);
#    if ($config->{debug} == 1) {
	print STDERR "NAME: $name";
	print STDERR "CC: $cc";
#    }
    $tmp = "SELECT id FROM $config->{freepbx_iax_table} WHERE keyword = 'accountcode' AND value IN (" 
		. $freepbx_db->quote($name) . ","
		. $freepbx_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $freepbx_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @devicelist, $row->{id};
    }
    $sql->finish;
    return @devicelist;
}

# Add an IAX2 user to FreePBX.
sub add_iax_user_freepbx() {
    my (
        $freepbx_db, $config,  $name,
        $secret,     $context, $username,   $params, $cc
      )
      = @_;
    my ( $md5secret, $tmp, $id, $appdata );
	$name =~ s/\W//mg;
	$username =~ s/\W//mg;
    my $mailbox = $name . "\@" . $config->{rt_mailbox_group};
    my $clid    = "$params->{first} $params->{last} <$name> ";

    #    my $clid = "<$name>";
    if ( $config->{debug} == 1 ) {
        print STDERR "CLID: $clid \n" if $config->{debug} == 1;
        print STDERR "NAME: $name\n" if $config->{debug} == 1;
        print STDERR "USERNAME: $username\n" if $config->{debug} == 1;
        print STDERR "CANREINVITE: $config->{rt_sip_canreinvite} \n" if $config->{debug} == 1;
        print STDERR "CONTEXT: $context\n" if $config->{debug} == 1;
        print STDERR "INSECURE:  $config->{rt_sip_insecure} \n" if $config->{debug} == 1;
        print STDERR "MAILBOX: $mailbox \n" if $config->{debug} == 1;
        print STDERR "NAT:  $config->{rt_sip_nat}\n" if $config->{debug} == 1;
        print STDERR "SIP PORT: $config->{sip_port}\n" if $config->{debug} == 1;
        print STDERR "SIP QUALIFY: $config->{rt_sip_qualify} \n" if $config->{debug} == 1;
        print STDERR "SECRET: $secret\n" if $config->{debug} == 1;
        print STDERR "SIP TYPE: $config->{rt_sip_type}\n" if $config->{debug} == 1;
        print STDERR "CODEC DISALLOW: $config->{rt_codec_disallow}\n" if $config->{debug} == 1;
        print STDERR "CODEC ALLOW: $config->{rt_codec_allow}\n" if $config->{debug} == 1;
    }
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'account', "
      . $freepbx_db->quote($name) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'accountcode', "
      . $freepbx_db->quote($cc) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'secret', "
      . $freepbx_db->quote($secret) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'notansfer', "
      . $freepbx_db->quote( $config->{freepbx_iax_notansfer} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'context', "
      . $freepbx_db->quote($context) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'username', "
      . $freepbx_db->quote($name) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'host', "
      . $freepbx_db->quote( $config->{ipaddr} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'type', "
      . $freepbx_db->quote( $config->{freepbx_sip_type} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'mailbox', "
      . $freepbx_db->quote($mailbox) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'port', "
      . $freepbx_db->quote( $config->{iax_port} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'qualify', "
      . $freepbx_db->quote( $config->{freepbx_sip_qualify} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'disallow', "
      . $freepbx_db->quote( $config->{freepbx_codec_disallow} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'allow', "
      . $freepbx_db->quote( $config->{freepbx_codec_allow} ) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
      "INSERT INTO $config->{freepbx_iax_table} (id,keyword,data) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'callerid', "
      . $freepbx_db->quote($clid) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $freepbx_db->do($tmp) || print "$tmp failed";
    $tmp =
        "INSERT INTO devices (id,tech,dial,devicetype,user) VALUES ("
      . $freepbx_db->quote($name)
      . ", 'IAX2', 'IAX2/$name', 'FIXED', "
      . $freepbx_db->quote($name) . ")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    if ($freepbx_db->do($tmp)) {
        return gettext("IAX2 Device Added!") . gettext("Username:") . " " . $name . " " . gettext("Password:") . " " . $secret;
    } else {
        return gettext("IAX2 Device Creation Failed!");
    }
}

# Update the context on a SIP account in FreePBX.
sub update_context_sip_user_freepbx {
    my ( $freepbx_db, $config, $name, $context ) = @_;
    $freepbx_db->do( "UPDATE $config->{freepbx_sip_table} SET data = "
          . $freepbx_db->quote($context)
          . " WHERE id = "
          . $freepbx_db->quote($name)
          . "AND keyword = 'context'" );
}

# Update the context on an IAX2 account in FreePBX.
sub update_context_iax_user_freepbx {
    my ( $freepbx_db, $config, $name, $context ) = @_;
    $freepbx_db->do( "UPDATE $config->{freepbx_iax_table} SET data = "
          . $freepbx_db->quote($context)
          . " WHERE id = "
          . $freepbx_db->quote($name)
          . "AND keyword = 'context'" );
}

# Delete a SIP account from FreePBX.
sub del_sip_user_freepbx() {
    my ( $freepbx_db, $config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n" if $config->{debug} == 1;
    $tmp =
      "DELETE FROM $config->{freepbx_sip_table} WHERE id = "
      . $freepbx_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    $freepbx_db->do($tmp) || print "$tmp failed";
}

# Delete and IAX2 account from FreePBX.
sub del_iax_user_freepbx() {
    my ( $freepbx_db, $config, $name ) = @_;
    my $tmp;
    print STDERR "Deleting $name\n" if $config->{debug} == 1;
    $tmp =
      "DELETE FROM $config->{freepbx_iax_table} WHERE id = "
      . $freepbx_db->quote($name);
    if ( $config->{debug} == 1 ) {
        print STDERR " $tmp \n" if $config->{debug} == 1;
    }
    $freepbx_db->do($tmp) || print "$tmp failed";
}

########  FreePBX Integration Ends #################

# Go find a reseller and return if the account is open.
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

# Go find a reseller even if the account is closed.
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

# Return a specific provider.
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

# Get the counter belonging to a specific package and customer.  Counters are used to track how many "package" seconds a customer has had so far this month.
sub get_counter() {
    my ( $astpp_db, $package, $cardnum ) = @_;
    my ( $sql, $row );
    $sql =
      $astpp_db->prepare( "SELECT * FROM counters WHERE package = "
          . $astpp_db->quote($package)
          . " AND account = "
          . $astpp_db->quote($cardnum) 
	  . " AND status = 1" );
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

# Get the "package" that matches a specified number.  Packages are used to provide X free minutes per customer per month.
sub get_package() {
    my ( $astpp_db, $carddata, $number ) = @_;
    my ( $sql, $record );
    $sql =
      $astpp_db->prepare( "SELECT * FROM packages WHERE "
          . $astpp_db->quote($number)
          . " RLIKE pattern AND pricelist = "
          . $astpp_db->quote( $carddata->{pricelist} )
          . " ORDER BY LENGTH(pattern) DESC LIMIT 1" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

# Return a count on the number of dids that match specific criteria which you must pass to this subroutine.
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

# Return a count on the number of callingcards that match specific criteria which you must pass to this subroutine.
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

# Return a count on the number of accounts that match specific criteria which you must pass to this subroutine.
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

# Return a list of unbilled cdrs from the Asterisk(TM) cdr database.
sub count_unbilled_cdrs() {
    my ($config, $cdr_db,$account) = @_;
    my ( $sql, $count, $record );
    $sql =
      $cdr_db->prepare( "SELECT COUNT(*) FROM $config->{cdr_table} WHERE cost = 'error' OR "
          . "accountcode IN (" . $account . "0) AND cost ='none'" );      
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

# Return a list of all calling cards.
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

# Return a list of all calling cards tagged to a specific customers account.
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

# List all calling card brands that do not belong to a reseller.
sub list_cc_brands() {
    my ($astpp_db) = @_;
    my ( $sql, @brandlist, $result );
    $sql =
      $astpp_db->prepare("SELECT name FROM callingcardbrands WHERE status = 1 AND (reseller IS NULL OR reseller = '')");
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @brandlist, $result->{name};
    }
    $sql->finish;
    @brandlist = sort @brandlist;
    return @brandlist;
}

# List calling card brands belonging to a specific reseller.
sub list_cc_brands_reseller() {
    my ($astpp_db,$reseller) = @_;
    my ( $sql, @brandlist, $result );
    $sql =
      $astpp_db->prepare("SELECT name FROM callingcardbrands WHERE status = 1 AND reseller = " . $astpp_db->quote($reseller));
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @brandlist, $result->{name};
    }
    $sql->finish;
    @brandlist = sort @brandlist;
    return @brandlist;
}

# List all active resellers.
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

# List unrated CDRS per account
sub count_unrated_cdrs_account() {
    my ( $config, $cdr_db, $account, $cc ) = @_;
    my ( $sql, @cdrlist, $record, $count );
    $sql =
      $cdr_db->prepare(
        "SELECT COUNT(*) FROM $config->{asterisk_cdr_table} WHERE cost IN ('none', 'error') AND accountcode IN (" . $cdr_db->quote($account) . ", " . $cdr_db->quote($cc) . ")");
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $count  = $record->{"COUNT(*)"};
    $sql->finish;
    return $count;
}

# List all active booths
sub list_booths() {
    my ($astpp_db, $callshop) = @_;
    my ( $sql, @resellers, $result );
    $sql = $astpp_db->prepare("SELECT * FROM booths WHERE status = 1");
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @resellers, $result->{name};
    }
    $sql->finish;
    @resellers = sort @resellers;
    return @resellers;
}

# Deprecated I believe
sub testunique() {
    my ( $astpp_db, $number ) = @_;
    my $test = "WHERE number = " . $astpp_db->quote($number);
    my $count = &count_cards( $astpp_db, $test );
    return $count;
}

# Return data on a specific calling card.
sub get_callingcard() {
    my ( $astpp_db, $cardno, $config ) = @_;
    my ( $sql,$tmp,$carddata );
    $tmp =
       "SELECT * FROM callingcards WHERE cardnumber = "
          . $astpp_db->quote($cardno);
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $carddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $carddata;
}

# Transfer funds from one callingcard to another
sub transfer_funds() {
	my ($astpp_db, $config, $sourcecard, $sourcecardpin, $destcard, $destcardpin) = @_;
	my $sourcecardinfo = &get_callingcard( $astpp_db, $sourcecard, $config );
	my $sourcecardstatus = &check_card_status( $astpp_db, $sourcecardinfo );
	# This subroutine returns the status of the card:
	if ( $sourcecardstatus != 0 ) {
		return 1;
	} elsif ( $sourcecardinfo->{pin} != $sourcecardpin) {
		return 1;
	}
	my $destcardinfo = &get_callingcard( $astpp_db, $destcard, $config );
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


# Calculate the maximum length of a call.
sub max_length() {
	my ($astpp_db, $config, $carddata, $phoneno) = @_;
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
	########
	$credit = $credit * 1;
	########
	print STDERR "Account Balance: " . $credit * 1 . "\n"  if $config->{debug} == 1;
	$credit_limit = $carddata->{credit_limit} * 1;
	print STDERR "Credit Limit: " . $credit_limit . "\n" if $config->{debug} == 1;
	$credit = ($credit * -1) + ($credit_limit);         # Add on the accounts credit limit.
	#$credit = $credit / $carddata->{maxchannels} if $carddata->{maxchannels} > 0;
	print STDERR "Credit: " . $credit .  "\n" if $config->{debug} == 1;
	if ($branddata->{markup} > 0) {
		$numdata->{connectcost} =
		$numdata->{connectcost} * ( ( $branddata->{markup} / 1 ) + 1 );
		$numdata->{cost} =
		$numdata->{cost} * ( ( $branddata->{markup} / 1 ) + 1 );
	}
	if ( $numdata->{connectcost} > $credit ) {   # If our connection fee is higher than the available money we can't connect.
		return (0,0);
	}
	if ( $numdata->{cost} > 0 ) {
		$maxlength = ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
		if ($config->{call_max_length} && ($maxlength > $config->{call_max_length} / 1000)){
			print STDERR "LIMITING CALL TO CONFIG MAX LENGTH \n" if $config->{debug} == 1;
		        $maxlength = $config->{call_max_length} / 1000 / 60;
		}
	}
	else {
		print STDERR "CALL IS FREE - ASSIGNING MAX LENGHT \n" if $config->{debug} == 1;
		$maxlength = $config->{max_free_length};    # If the call is set to be free then assign a max length.
	}
	return (1, $maxlength);
}

sub hangup_call() {
	my ($astpp_db,$config,$channel) = @_;
	use Asterisk::Manager;
	my $astman = new Asterisk::Manager;
	$astman->user($config->{astman_user});
	$astman->secret($config->{astman_secret});
	$astman->host($config->{astman_host});
	$astman->connect || die $astman->error . "\n" if $config->{debug} == 1;
	my %callout = ( Action => 'Hangup', Channel => $channel);
	print STDERR $astman->sendcommand(%callout);
}

# Perfrom callout using the Asterisk Manager Interface.
sub perform_callout() {
    my ($astpp_db,$config,$number,$lcrcontext,$accountcode,$maxretries,$waittime,$retrytime,$clidname,$clidnumber,$context,$extension,%variables) = @_;
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
    $astman->connect || die $astman->error . "\n" if $config->{debug} == 1;


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
    foreach my $key (keys %callout) {
   	 print STDERR "Key: $key Value: " . $callout{$key} . "\n" if $config->{debug} == 1;
    }
   
	print STDERR $astman->sendcommand(%callout);
}

# Check to see if a calling card is ok to use.
sub check_card_status() {    # Check a few things before saying the card is ok.
	# This subroutine returns the status of the card:
	# Status 0 means the card is ok,
	# Status 1 means the card is in use.
	# Status 2 means the card has expired.
	# Status 3 means the card is empty.
	my ($astpp_db,$cardinfo) = @_;
	my $now = $astpp_db->selectall_arrayref("SELECT NOW()")->[0][0];
	print STDERR "Present Time: $now\n" if $config->{debug} == 1;
	print STDERR "Expiration Date: $cardinfo->{expiry}\n" if $config->{debug} == 1;
	print STDERR "Valid for Days: $cardinfo->{validfordays}\n" if $config->{debug} == 1;
	print STDERR "First Use: $cardinfo->{firstused}\n" if $config->{debug} == 1;
	if ( $cardinfo->{inuse} != 0 )
	{                
		return 1; #Status 1 means card is in use.
	}
	if ( $cardinfo->{validfordays} > 0 ) {
		$now = $astpp_db->selectall_arrayref("SELECT NOW()")->[0][0];
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

#Map sound files to variable names.  Ultimately this should probably be done in the database.
sub define_sounds() {
	my ($astpp_db,$location) = @_;
	$location = "" if !$location;
	my $sound;
	$sound->{no_responding} = $location . "astpp-down.wav";    #The calling card platform is down, please try again later.
$sound->{cardnumber} = $location . "astpp-accountnum.wav" ;    #Please enter your card number followed by pound.
$sound->{cardnumber_incorrect} = $location .  "astpp-badaccount.wav";    #Incorrect card number.
$sound->{pin} = $location . "astpp-pleasepin.wav";    #Please enter your pin followed by pound.
$sound->{pin_incorrect} = $location . "astpp-invalidpin.wav";    #Incorrect pin.
$sound->{goodbye}       = $location . "astpp-goodbye.wav";          #Goodbye.
$sound->{destination}   = $location . "astpp-phonenum.wav"; #Please enter the number you wish to dial followed by pound.
$sound->{destination_incorrect} = $location . "astpp-badphone.wav";    #Phone number not found!
$sound->{card_inuse}     = $location . "astpp-in-use.wav";   #This card is presently being used.
$sound->{call_will_cost} = $location . "astpp-willcost.wav"; #This call will cost:
$sound->{main_currency}  = $location . "astpp-dollar.wav";   #Dollar
$sound->{sub_currency}   = $location . "astpp-cent.wav";     #Cent
$sound->{main_currency_plural}     = $location . "astpp-dollars.wav";       #Dollars
$sound->{sub_currency_plural}      = $location . "astpp-cents.wav";         #cents
$sound->{per}                      = $location . "astpp-per.wav";           #per
$sound->{minute}                   = $location . "astpp-minute.wav";        #Minute
#Changed By Joseph Watson
# $sound->{minutes}                  = $location . "astpp-minutes.wav";       #Minutes
$sound->{minutes}                  = $location . "minutes.wav";       #Minutes
$sound->{second}                   = $location . "astpp-second.wav";        #Second
$sound->{seconds}                  = $location . "astpp-seconds.wav";       #Seconds
$sound->{a_connect_charge}         = $location . "astpp-connectcharge.wav"; #A connect charge of
$sound->{will_apply}               = $location . "astpp-willapply.wav";     #Will apply
$sound->{please_wait_will_connect} = $location . "astpp-please-wait-while-i-connect.wav";    #Please wait while I connect your call
$sound->{card_is_empty}       = $location . "astpp-card-is-empty.wav";    #This card is empty.
$sound->{card_has_balance_of} = $location . "astpp-this-card-has-a-balance-of.wav";    #Card has a balance of:
$sound->{card_has_expired} = $location . "astpp-card-has-expired.wav";   #This card has expired.
$sound->{call_will_last}   = $location . "astpp-this-call-will-last.wav"; #This call will last:
$sound->{not_enough_credit} = $location . "astpp-not-enough-credit.wav";    #You do not have enough credit
$sound->{call_completed} = $location . "astpp-call-completed.wav";       #This call has been completed.
$sound->{astpp_callingcard_menu} = $location . "astpp-callingcard-menu.wav"
  ; #Press one if you wish to place another call, press 2 for your card balance, or press 3 to hangup
$sound->{busy} = $location .  "astpp-busy-tryagain.wav";  #Number was busy, Press 1 to try again.
$sound->{cancelled} = $location . "astpp-cancelled-tryagain.wav";  #Call was cancelled.
$sound->{congested} = $location . "astpp-congested-tryagain.wav";    #Number was congested, Press 1 to try again.
$sound->{noanswer} = $location . "astpp-noanswer-tryagain.wav";     #There was no answer, Press 1 to try again.
$sound->{badnumber} = $location . "astpp-badnumber.wav";          # "Calls from this location are blocked!"
$sound->{used_elsewhere} = $location . "astpp-used-elsewhere.wav";     # "This location has been used already."
$sound->{goodbye}            = $location . "astpp-goodbye.wav";    # "Goodbye"
$sound->{callback_performed} = $location . "astpp-callback-performed.wav";    # "This callback has been performed please disconnect now"
$sound->{cardnumber} = $location . "astpp-accountnum.wav";    #Please enter your card number followed by pound.
$sound->{cardnumber_incorrect} = $location . "astpp-badaccount.wav";    #Incorrect card number.
$sound->{pin} = $location . "astpp-pleasepin.wav";    #Please enter your pin followed by pound.
$sound->{pin_incorrect} = $location . "astpp-invalidpin.wav";    #Incorrect pin.
$sound->{point} = $location .  "astpp-point.wav";    #point.
$sound->{register_ani}  = $location . "astpp-register.wav";    # "Register ANI to this card? Press 1 for yes or any other key for no."
$sound->{card_has_expired} = $location .  "astpp_expired.wav";    #"This card has expired"
$sound->{card_is_empty}    = $location . "astpp-card-is-empty.wav";      #This card is empty
$sound->{where_to_call}    = $location . "astpp-where-to-call.wav";    
	#Press 1 to receive a call at the number you called from or registered
       #Otherwise enter the number you wish to be called at.
$sound->{number_to_register} = $location . "astpp-number-to-register.wav";  #Press 1 to register the number you called from.
                               #Otherwise enter the number you wish to register.
$sound->{card_has_been_refilled} = $location . "astpp-card-has-been-refilled.wav"; # Your card has been refilled.                          
$sound->{card_to_refill} = $location . "astpp-card-to-refill.wav"; #please enter the card number you wish to refill followed
							# by the pound sign.
$sound->{card_to_empty} = $location . "astpp-card-to-empty.wav"; #please enter the card number you wish to empty into your card
							# followed by the pound sign.
$sound->{astpp_please_pin_card_empty} = $location . "astpp-please-pin-card-empty.wav"; #please enter the pin number for the card
									# you wish to empty followed by the pound
									# sign.
return $sound;	
}

################### CallShop Support Begins ######################33

# Return a list of all callshops.
sub list_callshops() {
    my ( $astpp_db ) = @_;
    my ( $sql, @chargelist, $row );
    $sql =
      $astpp_db->prepare(
        "SELECT number FROM accounts WHERE status < 2 AND type = 5 ORDER BY number");
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @chargelist, $row->{number};
    }
    return @chargelist;
}

# Return a list of all callshops belonging to a specific reseller.
sub list_callshops_reseller() {
    my ($astpp_db,$reseller) = @_;
    my ( $sql, @callshoplist, $result );
    $sql =
      $astpp_db->prepare("SELECT number FROM accounts WHERE status = 1 AND type = 5 AND reseller = " . $astpp_db->quote($reseller) . " ORDER BY number");
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @callshoplist, $result->{nunmber};
    }
    $sql->finish;
    return @callshoplist;
}

# List booths belonging to a specific callshop.
sub list_booths_callshop() {
    my ($astpp_db,$reseller,$config) = @_;
    my ( $tmp, $sql, @boothlist, $result );
    $tmp = "SELECT number FROM accounts WHERE type = 6 AND reseller = " . $astpp_db->quote($reseller) . " AND status < 2 ORDER by number";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql =
      $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $result = $sql->fetchrow_hashref ) {
        push @boothlist, $result->{number};
    }
    $sql->finish;
    return @boothlist;
}


######## Call Rating ################################
sub cleanup_cdrs_fs() {
	my ($cdr_db, $config) = @_;
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'ORIGINATOR_CANCEL'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'CALL_REJECTED'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'USER_NOT_REGISTERED'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'CHAN_NOT_IMPLEMENTED'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'INVALID_NUMBER_FORMAT'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'NORMAL_TEMPORARY_FAILURE'");
	$cdr_db->do("UPDATE $config->{freeswitch_cdr_table} SET cost = '0',vendor='0' WHERE duration = '0' and billsec='0'");
}
sub cleanup_cdrs() {
	my ($cdr_db, $config) = @_;
	# First we cleanup all calls that are not answered
	if ($config->{astcdr} == 1) {
		$cdr_db->do("UPDATE $config->{asterisk_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'NO ANSWER'");
		$cdr_db->do("UPDATE $config->{asterisk_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'BUSY'");
		$cdr_db->do("UPDATE $config->{asterisk_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'FAILED'");
		$cdr_db->do("UPDATE $config->{asterisk_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'CONGESTION'");
		$cdr_db->do("UPDATE $config->{asterisk_cdr_table} SET cost = '0',vendor='0' WHERE disposition REGEXP 'CANCEL'");
		if ($config->{thirdlane_mods} == 1) {
        		$cdr_db->do("UPDATE $config->{cdr_table} SET accountcode = userfield WHERE (accountcode IS NULL or accountcode = '')");
		}
	}
}

sub print_csv {  # Print CDRS on rated calls.
	my ( $config, $cardno, $disposition, $calldate, $dst, $billsec, $cost, $reseller,
		$cdrinfo, @output )
	  = @_;
	my ( $outfile );
	print STDERR "Reseller: $reseller \n" if $config->{debug} == 1;
	if ( $reseller eq "" ) {
		$outfile = $config->{rate_engine_csv_file};
	}
	else {
		$outfile = $config->{csv_dir} . $reseller . ".csv";
	}	
	$outfile = "/var/log/astpp/astpp.csv" if !$outfile;
	my $notes = "Notes: " . $cdrinfo->{accountcode};
	open(OUTFILE,">>$outfile") || print STDERR "CSV Error - could not open $outfile for writing\n";
print OUTFILE << "ending_print_tag";
$cardno,$cost,$cdrinfo->{disposition},$cdrinfo->{calldate},$cdrinfo->{dst},$billsec,$notes
ending_print_tag
# 	close(OUTFILE);
}

sub rating() {  # This routine recieves a specific cdr and takes care of rating it and of marking it as rated.  It bills resellers as appropriate.
	my ( $astpp_db, $cdr_db, $config, $cdrinfo, $carddata, $vars, @output ) = @_;
	my ( $increment, $numdata, $package, $notes, $status );
	$status = 0;
	print STDERR "----------------------------------------------------------------\n" if $config->{debug} == 1;
	print STDERR
		"uniqueid: $cdrinfo->{uniqueid}, cardno: $carddata->{number}, phoneno: $cdrinfo->{dst}, Userfield: $cdrinfo->{userfield}\n" if $config->{debug} == 1;
	print STDERR
		"disposition: $cdrinfo->{disposition} Pricelist: $carddata->{pricelist} reseller: $carddata->{reseller}\n" if $config->{debug} == 1;
	if ( $cdrinfo->{disposition} =~ /^ANSWERED$/ || $cdrinfo->{disposition} eq "NORMAL_CLEARING") {
#    		if ($config->{thirdlane_mods} == 1 && $cdrinfo->{userfield} =~ m/.\d\d\d-IN/) {
#    			print STDERR "Call belongs to a Thirdlane(tm) DID.\n" if $config->{debug} == 1;
#			$cdrinfo->{dst} =~ s/-IN//g;
#			print STDERR "Destination: $cdrinfo->{dst} \n" if $config->{debug} == 1;
#		}
		$numdata = &get_route( $astpp_db, $config, $cdrinfo->{dst}, $carddata->{pricelist}, $carddata, $cdrinfo->{userfield} );
		if ( !$numdata->{pattern} ) {
			&save_cdr( $config, $cdr_db, $cdrinfo->{uniqueid}, "error",$cdrinfo->{dst} ) if !$vars && $config->{astcdr} == 1;
			print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n" if $config->{debug} == 1;
			print STDERR "NO MATCHING PATTERN\n" if $config->{debug} == 1;
			print STDERR "----------------------------------------------------------------\n" if $config->{debug} == 1;
		}
		else {
			print STDERR "FOUND A MATCHING PATTERN: $numdata->{pattern}" if $config->{debug} == 1;
			my $branddata = &get_pricelist( $astpp_db, $carddata->{pricelist} );
			print STDERR
				"pricelistData: $branddata->{name} $branddata->{markup} $branddata->{inc} $branddata->{status}\n" if $config->{debug} == 1;

			$package = &get_package( $astpp_db, $carddata, $cdrinfo->{dst} );
			if ($package->{id}) {
				my $counter = &get_counter( $astpp_db, $package->{id}, $carddata->{number} );
				my $difference;
				if ( !$counter->{id}) {
					my $tmp = "INSERT INTO counters (package,account) VALUES ("
						. $astpp_db->quote( $package->{id} ) . ", "
						. $astpp_db->quote( $carddata->{number} ) . ")";
					print STDERR "/n" . $tmp . "/n" if $config->{debug} == 1;
					$astpp_db->do($tmp);
					$counter = &get_counter( $astpp_db, $package->{id}, $carddata->{number} );
					print STDERR "JUST CREATED COUNTER: $counter->{id}\n" if $config->{debug} == 1;
				}
				if ( $package->{includedseconds} > $counter->{seconds}) {
					my $availableseconds = $package->{includedseconds} - $counter->{seconds};
					my $freeseconds;
					if ($availableseconds >= $cdrinfo->{billsec}) {
						$freeseconds = $cdrinfo->{billsec};
						$cdrinfo->{billsec} = 0;
					} else {
						$freeseconds = $availableseconds;
						$cdrinfo->{billsec} = $cdrinfo->{billsec} - $availableseconds;
					}
					my $tmp = "UPDATE counters SET seconds = "
						. $astpp_db->quote( $counter->{seconds} + $freeseconds )
						. " WHERE id = "
						. $astpp_db->quote( $counter->{id} );
					print STDERR $tmp . "/n" if $config->{debug} == 1;
					$astpp_db->do($tmp);
				}
			}

			if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
				$numdata->{connectcost} = $numdata->{connectcost} * ( ( $branddata->{markup} / 1 ) + 1 );
				$numdata->{cost} = $numdata->{cost} * ( ( $branddata->{markup} / 1 ) + 1 );
			}
			if ( $numdata->{inc} > 0 ) {
				$increment = $numdata->{inc};
			}
			else {
				$increment = $branddata->{inc};
			}
			print STDERR
				"$numdata->{connectcost}, $numdata->{cost}, $cdrinfo->{billsec}, $increment, $numdata->{includedseconds}\n" if $config->{debug} == 1;
			my $cost = &calc_call_cost(
					$numdata->{connectcost}, $numdata->{cost},
					$cdrinfo->{billsec},     $increment,
					$numdata->{includedseconds}
					);

			$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
			print STDERR "Matching pattern is $numdata->{pattern}\n" if $config->{debug} == 1;


			#Blocks all signals so that the program cannot be killed while writing costs.
			my $sigset = POSIX::SigSet->new;
			my $blockset = POSIX::SigSet->new( SIGINT, SIGQUIT, SIGCHLD );
			sigprocmask( SIG_BLOCK, $blockset, $sigset ) or die "Could not block INT,QUIT,CHLD signals: $!\n" if $config->{debug} == 1;
			&save_cdr( $config, $cdr_db, $cdrinfo->{uniqueid}, $cost,$cdrinfo->{dst} ) if !$vars && $config->{astcdr} == 1;
			if ( $cdrinfo->{accountcode} ne $carddata->{number} && $cdrinfo->{accountcode} ne $carddata->{cc}) {
				$notes = $cdrinfo->{accountcode} . "|" . $numdata->{comment} . "|" . $numdata->{pattern};
			}
			else {
				$notes = "|" . $numdata->{comment} . "|" . $numdata->{pattern};
			}
			&post_cdr(
					$astpp_db,               $config,
					$cdrinfo->{uniqueid},    $carddata->{number},
					$cdrinfo->{src},         $cdrinfo->{dst},
					$cdrinfo->{disposition}, $cdrinfo->{billsec},
					$cost,                   $cdrinfo->{calldate},
					"",                      $cdrinfo->{trunk},
					$notes,$numdata->{pricelist}, $numdata->{pattern},
					$cdrinfo->{userfield}, $cdrinfo->{provider}
					)
				if $config->{posttoastpp} == 1;
			&print_csv(
					$config, $carddata->{number},   $cdrinfo->{disposition},
					$cdrinfo->{calldate},  $cdrinfo->{dst},
					$cdrinfo->{billsec},   $cost,
					$carddata->{reseller}, $cdrinfo, @output
					);
			sigprocmask( SIG_SETMASK, $sigset ) # Restore the passing of signals
				or die "Could not restore INT,QUIT,CHLD signals: $!\n" if $config->{debug} == 1;    #
				$status = 1;
		}
	}
	else {
		&save_cdr( $config, $cdr_db,$cdrinfo->{uniqueid}, "error",$cdrinfo->{dst} ) if !$vars && $config->{astcdr} == 1;
		print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n" if $config->{debug} == 1;
		print STDERR "DISPOSITION: $cdrinfo->{disposition} \n" if $config->{debug} == 1;
		print STDERR "UNIQUEID: $cdrinfo->{uniqueid} \n" if $config->{debug} == 1;
		print STDERR "----------------------\n\n" if $config->{debug} == 1;
		$status = 0;
	}
	return $status;
}

sub catch_zap { # This subroutine will not allow you to kill the process while it's in the most vital portion of rating a call.
	my $shucks = 0;
	my $signame = shift;
	$shucks++;
	die "Somebody sent me a SIG$signame!";
}

sub vendor_not_billed() {  # Prints the information on calls where the "vendor" field is either none or error.
	my ($config, $cdr_db) = @_;
	my $tmp = "SELECT * FROM $config->{cdr_table} WHERE vendor IN ('none','error')";
	my $sql = $cdr_db->prepare($tmp);
	$sql->execute;
	while ( my $cdr = $sql->fetchrow_hashref ) {
		print STDERR "----------------------\n" if $config->{debug} == 1;
		print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n" if $config->{debug} == 1;
		print STDERR gettext("Destination: ") . $cdr->{dst} . "\n" if $config->{debug} == 1;
		print STDERR gettext("Trunk: ") . $cdr->{dstchannel} . "\n" if $config->{debug} == 1;
		print STDERR gettext("Date/Time: ") . $cdr->{calldate} . "\n" if $config->{debug} == 1;
		print STDERR gettext("Disposition: ") . $cdr->{disposition} . "\n" if $config->{debug} == 1;
		print STDERR gettext("Source: ") . $cdr->{src} . "\n" if $config->{debug} == 1;
		print STDERR gettext("UniqueID: ") . $cdr->{uniqueid} . "\n" if $config->{debug} == 1;
		print STDERR gettext("Destination: ") . $cdr->{dst} . "\n" if $config->{debug} == 1;
		print STDERR "----------------------\n\n" if $config->{debug} == 1;
	}
}


sub processlist() {  # Deal with a list of calls which have not been rated so far.
	my ($astpp_db, $cdr_db, $config, $chargelist, $vars) = @_;
	my ( $status, $cdrinfo);
	foreach my $uniqueid (@$chargelist) {
		print STDERR gettext("Processing Uniqueid: ") . $uniqueid . "\n" if $config->{debug} == 1;
		$cdrinfo  = &get_cdr( $config, $cdr_db, $uniqueid );
		my $savedcdrinfo = $cdrinfo;
#		if(!$vars) {
#			my $tmp = "UPDATE $config->{cdr_table} SET cost = 'rating' WHERE uniqueid = " 
#				. $cdr_db->quote($uniqueid) 
#				. " AND cost = 'none'"
#				. " AND dst = "
#				. $cdr_db->quote($cdrinfo->{dst})
#				. " LIMIT 1";
#			$cdr_db->do($tmp);
#		} else {
#			$cdrinfo->{'cost'} = 'rating';
#		}
		if ( $cdrinfo->{accountcode} ) {
    			if ($config->{thirdlane_mods} == 1 && $cdrinfo->{accountcode} =~ m/.\d\d\d-IN/) {
			    	print STDERR "Call belongs to a Thirdlane(tm) DID.\n" if $config->{debug} == 1;
				my ($did,$record);
				($did = $cdrinfo->{accountcode}) =~ s/-IN//g;
				print STDERR "DID: $did \n" if $config->{debug} == 1;
				$record = &get_did($astpp_db,$did);
				$cdrinfo->{userfield} = $cdrinfo->{accountcode};
				$cdrinfo->{accountcode} = $record->{account};
				$cdrinfo->{dst} = $did;
			}
			if ($config->{cdr_regex_accountcode}) {
				print STDERR "Modify Accountcode\n" if $config->{debug} == 1;
				print STDERR "Original: " . $cdrinfo->{accountcode};
				$cdrinfo->{accountcode} =~ s/$config->{cdr_regex_accountcode}//mg;
				print STDERR "Modified: " . $cdrinfo->{accountcode};
			}

			my $carddata = &get_account( $astpp_db, $cdrinfo->{accountcode} );
			if ($carddata->{number}) {
				if ( $cdrinfo->{lastapp} eq "MeetMe" ) {    # There is an issue with calls that come out of meetmee
					$cdrinfo->{billsec} = $cdrinfo->{duration};
				}
				if ( $cdrinfo->{billsec} <= 0 ) {			# not having the right billable seconds.
					&save_cdr( $config, $cdr_db, $uniqueid, 0,$cdrinfo->{dst} ) if !$vars && $config->{astcdr} == 1;
					&save_cdr_vendor( $config, $cdr_db, $uniqueid, 0,$cdrinfo->{dst} )
						if $config->{astcdr} == 1;
					print STDERR "\n----------------------\n" if $config->{debug} == 1;
					print STDERR "CDR Written - No Billable Seconds\n" if $config->{debug} == 1;
					print STDERR
						"uniqueid $cdrinfo->{uniqueid}, cardno $cdrinfo->{accountcode}, phoneno $cdrinfo->{dst}\n" if $config->{debug} == 1;
					print STDERR "disposition $cdrinfo->{disposition}\n" if $config->{debug} == 1;
					print STDERR "----------------------\n" if $config->{debug} == 1;
					
					if($carddata->{maxchannels} ne '0' && $cdrinfo->{userfield} eq 'STANDARD')
					{					
					      &update_inuse($astpp_db,$carddata->{number},'accounts','-1');
					}
					if($cdrinfo->{userfield} eq 'DID')
					{					
					      &update_inuse($astpp_db,$cdrinfo->{dst},'dids','-1');
					}
					while ( $carddata->{reseller} ne "" ) {
					    $carddata = &get_account( $astpp_db, $carddata->{reseller} );
					    #Calculating in use count for account 
					    if($carddata->{maxchannels} ne '0' && $cdrinfo->{userfield} eq 'STANDARD')
					    {
						  &update_inuse($astpp_db,$carddata->{number},'accounts','-1');
					    }
					}
				}
				elsif ( $cdrinfo->{accountcode} ) {
					$status = 0;
					$status = &rating( $astpp_db, $cdr_db,$config, $cdrinfo, $carddata, $vars);
					#Calculating in use count for account 
					if($carddata->{maxchannels} ne '0' && $cdrinfo->{userfield} eq 'STANDARD')
					{					
					      &update_inuse($astpp_db,$carddata->{number},'accounts','-1');
					}
					if($cdrinfo->{userfield} eq 'DID')
					{					
					      &update_inuse($astpp_db,$cdrinfo->{dst},'dids','-1');
					}
					$cdrinfo  = &get_cdr( $config, $cdr_db, $uniqueid ) if !$vars;
					if ( $status == 1 ) {
						my $previous_account = $carddata->{number};
						while ( $carddata->{reseller} ne "" ) {
							$cdrinfo  = &get_cdr( $config, $cdr_db, $uniqueid ) if !$vars;
							print STDERR "Charge $uniqueid to $carddata->{reseller}\n" if $config->{debug} == 1;
							$carddata = &get_account( $astpp_db, $carddata->{reseller} );
							$status = &rating( $astpp_db, $cdr_db, $config, $cdrinfo, $carddata, $vars);
							my $tmp = "SELECT id FROM cdrs WHERE uniqueid = '" . $uniqueid 
								. "' AND cardnum = '" . $previous_account . "' LIMIT 1";
							print STDERR "$tmp\n" if $config->{debug} == 1;
							my $sql = $astpp_db->prepare($tmp);
							$sql->execute;
							my $previous_data = $sql->fetchrow_hashref;
							$sql->finish;

							$tmp = "SELECT id,debit,credit FROM cdrs WHERE uniqueid = '" . $uniqueid 
								. "' AND cardnum = '" . $carddata->{number} . "' LIMIT 1";
							print STDERR "$tmp\n" if $config->{debug} == 1;
							$sql = $astpp_db->prepare($tmp);
							$sql->execute;
							my $cdrdata = $sql->fetchrow_hashref;
							$sql->finish;

							$cdrdata->{cost} = $cdrdata->{debit} - $cdrdata->{credit};
							$tmp = "UPDATE cdrs SET cost = " . $cdrdata->{cost} 
							. " WHERE id = " . $previous_data->{id} . " AND callednum = " . $cdr_db->quote($cdrinfo->{dst});
							print STDERR "$tmp\n" if $config->{debug} == 1;
							$astpp_db->do($tmp);
							
							#Calculating in use count for account 
							if($carddata->{maxchannels} ne '0' && $cdrinfo->{userfield} eq 'STANDARD')
							{
							      &update_inuse($astpp_db,$carddata->{number},'accounts','-1');
							}
							
							$previous_account = $carddata->{number};
						}
					}
				}
			}
			else {
				print STDERR "----------------------\n" if $config->{debug} == 1;
				print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n" if $config->{debug} == 1;
				print STDERR "NO ACCOUNT EXISTS IN ASTPP\n" if $config->{debug} == 1;
				print STDERR
					"uniqueid: $uniqueid, Account: $cdrinfo->{accountcode}, phoneno: $cdrinfo->{dst}\n" if $config->{debug} == 1;
				print STDERR "disposition: $cdrinfo->{disposition}\n" if $config->{debug} == 1;
				print STDERR "----------------------\n" if $config->{debug} == 1;
				if(!$vars) {
					my $tmp = "UPDATE $config->{freeswitch_cdr_table} SET cost = 'error' WHERE uniqueid = " 
						. $cdr_db->quote($uniqueid) 
						. " AND cost = 'rating' LIMIT 1";
					$cdr_db->do($tmp);
				} else {
					$cdrinfo->{cost} = 'error';
				}
			}
		}
		else {
			print STDERR "----------------------\n" if $config->{debug} == 1;
			print STDERR "ERROR - ERROR - ERROR - ERROR - ERROR \n" if $config->{debug} == 1;
			print STDERR "NO ACCOUNTCODE IN DATABASE\n" if $config->{debug} == 1;
			print STDERR
				"uniqueid: $cdrinfo->{uniqueid}, cardno: $cdrinfo->{accountcode}, phoneno: $cdrinfo->{dst}\n" if $config->{debug} == 1;
			print STDERR "disposition: $cdrinfo->{disposition}\n" if $config->{debug} == 1;
			print STDERR "----------------------\n" if $config->{debug} == 1;
			if(!$vars) {
				my $tmp = "UPDATE $config->{freeswitch_cdr_table} SET cost = 'none' WHERE uniqueid = " 
					. $cdr_db->quote($uniqueid) 
					. " AND cost = 'rating' LIMIT 1";
				$cdr_db->do($tmp);
			} else {
				$cdrinfo->{cost} = 'none';
			}
		}
		my $phrase = "none";
		if ($config->{trackvendorcharges} == 1) {
			print STDERR gettext("Vendor Rating Starting") . "/n" if $config->{debug} == 1;
			&vendor_process_rating( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars ) if $config->{softswitch} == 0;
			&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars ) if $config->{softswitch} == 1;
		}
	}
}


sub vendor_process_rating_fs() {  #Rate Vendor calls.
	my ( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars ) = @_;
	my ($sql,$tmp);
	print STDERR "Vendor Rating Uniqueid: " . $uniqueid . "\n" if $config->{debug} == 1;
	if($uniqueid) {
	    $tmp = "SELECT * FROM $config->{freeswitch_cdr_table} WHERE uniqueid = '$uniqueid'";
	} else {
	    $tmp = "SELECT * FROM $config->{freeswitch_cdr_table} WHERE vendor IN ('error','none')";
	}
	print STDERR $tmp . "\n" if $config->{debug} == 1;
	$sql = $cdr_db->prepare($tmp);
	$sql->execute;
	while ( my $cdrinfo = $sql->fetchrow_hashref ) {
				my $tmp = "SELECT * FROM outbound_routes WHERE id = "
					. $astpp_db->quote( $cdrinfo->{outbound_route} );
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
					$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
					&post_cdr(
							$astpp_db,               $config,
							$cdrinfo->{uniqueid},    $cdrinfo->{provider},
							$cdrinfo->{src},         $cdrinfo->{dst},
							$cdrinfo->{disposition}, $cdrinfo->{billsec},
							$cost * -1,              $cdrinfo->{calldate},
							"",                      $cdrinfo->{trunk},
							$pricerecord->{comment},$pricerecord->{name},$pricerecord->{pattern},
							$cdrinfo->{userfield}, $cdrinfo->{provider}
							) if $config->{posttoastpp} == 1;
					&save_cdr_vendor( $config, $cdr_db, $cdrinfo->{uniqueid}, $cost,$cdrinfo->{dst} );
					my $tmp = "UPDATE cdrs SET cost = '" . $cost . "' WHERE uniqueid = '" .
						$cdrinfo->{uniqueid} . "' AND cost = 0 "
						. " AND cardnum != '" . $cdrinfo->{provider} . "' AND callednum = "
						. $astpp_db->quote($cdrinfo->{dst}) . " LIMIT 1";
					print STDERR "$tmp\n" if $config->{debug} == 1;
					$astpp_db->do($tmp);
				} else {
					&save_cdr_vendor( $config, $cdr_db, $cdrinfo->{uniqueid}, "error",$cdrinfo->{dst} );
				}
			}
}

sub vendor_process_rating() {  #Rate Vendor calls.
	my ( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars ) = @_;
	my $tmp = "SELECT * FROM trunks ORDER BY LENGTH(path)";
	my $sql = $astpp_db->prepare($tmp);
	print STDERR "$tmp\n" . "\n" if $config->{debug} == 1;
	$sql->execute;
	while ( my $trunk = $sql->fetchrow_hashref ) {
		my $tmp;
		if(!$vars) {
			if ( $uniqueid ne "" ) {
				$tmp =
					"SELECT * FROM $config->{asterisk_cdr_table} where lastapp = 'Dial'"
					. " AND vendor = "
					. $cdr_db->quote($phrase)
					. " AND (dstchannel LIKE '$trunk->{tech}/$trunk->{path}%'"
					. " OR dstchannel LIKE '$trunk->{tech}\[$trunk->{path}\]%'"
					. " OR lastdata LIKE '$trunk->{tech}/$trunk->{path}%'"
					. " OR lastdata LIKE '$trunk->{tech}\[$trunk->{path}\]%')"
					. " AND uniqueid = "
					. $cdr_db->quote($uniqueid)
					. " AND disposition = 'ANSWERED'";
			} else {
				$tmp = "SELECT * FROM $config->{asterisk_cdr_table} where lastapp = 'Dial'"
					. " AND vendor = "
					. $cdr_db->quote($phrase)
					. " AND (dstchannel LIKE '$trunk->{tech}/$trunk->{path}%'"
					. " OR dstchannel LIKE '$trunk->{tech}\[$trunk->{path}\]%'"
					. " OR lastdata LIKE '$trunk->{tech}/$trunk->{path}%'"
					. " OR lastdata LIKE '$trunk->{tech}\[$trunk->{path}\]%')"
					. " AND disposition = 'ANSWERED'";
			}
			print STDERR "$tmp\n" . "\n" if $config->{debug} == 1;
			my $sql1 = $cdr_db->prepare($tmp);
			$sql1->execute;
			while ( my $cdrinfo = $sql1->fetchrow_hashref ) {
				my $tmp = "SELECT * FROM outbound_routes WHERE "
					. $astpp_db->quote( $cdrinfo->{dst} )
					. " RLIKE pattern AND status = 1 AND trunk = "
					. $astpp_db->quote( $trunk->{name} )
					. " ORDER by LENGTH(pattern) DESC, cost";
				my $sql2 = $astpp_db->prepare($tmp);
				$sql2->execute;
				print STDERR "$tmp\n" . "\n" if $config->{debug} == 1;
				my $pricerecord = $sql2->fetchrow_hashref;
				$sql2->finish;
				if ( $pricerecord->{id} ) {
					my $cost = &calc_call_cost(
							$pricerecord->{connectcost}, $pricerecord->{cost},
							$cdrinfo->{billsec},     $pricerecord->{inc},
							$pricerecord->{includedseconds}
							);
					$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
					&post_cdr(
							$astpp_db,               $config,
							$cdrinfo->{uniqueid},    $trunk->{provider},
							$cdrinfo->{src},         $cdrinfo->{dst},
							$cdrinfo->{disposition}, $cdrinfo->{billsec},
							$cost * -1,              $cdrinfo->{calldate},
							"",                      $cdrinfo->{trunk},
							$pricerecord->{comment},$pricerecord->{name},$pricerecord->{pattern},
							$cdrinfo->{userfield}, $cdrinfo->{provider}
							) if $config->{posttoastpp} == 1;
					&save_cdr_vendor( $config, $cdr_db, $cdrinfo->{uniqueid}, $cost,$cdrinfo->{dst} );
					my $tmp = "UPDATE cdrs SET cost = '" . $cost . "' WHERE uniqueid = '" .
						$cdrinfo->{uniqueid} . "' AND cost = 0 "
						. " AND cardnum != '" . $trunk->{provider} . "' AND dst = "
						. $astpp_db->quote($cdrinfo->{dst}) . " LIMIT 1";
					print STDERR "$tmp\n" if $config->{debug} == 1;
					$astpp_db->do($tmp);
				} else {
					&save_cdr_vendor( $config, $cdr_db, $cdrinfo->{uniqueid}, "error",$cdrinfo->{dst} );
				}
			}
		} else {
			my $cdrinfo = $vars;
			my $tmp = "SELECT * FROM outbound_routes WHERE "
				. $astpp_db->quote( $cdrinfo->{dst} )
				. " RLIKE pattern AND status = 1 AND trunk = "
				. $astpp_db->quote( $trunk->{name} )
				. " ORDER by LENGTH(pattern) DESC, cost";
			my $sql2 = $astpp_db->prepare($tmp);
			$sql2->execute;
			print STDERR "$tmp\n" . "\n" if $config->{debug} == 1;
			my $pricerecord = $sql2->fetchrow_hashref;
			$sql2->finish;
			if ( $pricerecord->{id} ) {
				my $cost = &calc_call_cost(
						$pricerecord->{connectcost}, $pricerecord->{cost},
						$cdrinfo->{billsec},     $pricerecord->{inc},
						$pricerecord->{includedseconds}
						);
				$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
				&post_cdr(
						$astpp_db,               $config,
						$cdrinfo->{uniqueid},    $trunk->{provider},
						$cdrinfo->{src},         $cdrinfo->{dst},
						$cdrinfo->{disposition}, $cdrinfo->{billsec},
						$cost * -1,              $cdrinfo->{calldate},
						"",                      $cdrinfo->{trunk},
						$pricerecord->{comment},$pricerecord->{name},$pricerecord->{pattern},
						$cdrinfo->{userfield}, $cdrinfo->{provider}
						) if $config->{posttoastpp} == 1;
				my $tmp = "UPDATE cdrs SET cost = '" . $cost . "' WHERE uniqueid = '" .
					$cdrinfo->{uniqueid} . "' AND cost = 0 "
					. " AND cardnum != '" . $trunk->{provider} . "' AND dst = "
					. $astpp_db->quote($cdrinfo->{dst}) . " LIMIT 1";
				print STDERR "$tmp\n" if $config->{debug} == 1;
				$astpp_db->do($tmp);
			}
		}
	}
	$sql->finish;
}

#  This is all stuff out of the old astpp-update-balance.pl.  This is part of the project to seperate the posting of periodic charges with the creation
# of invoices in external applications.

sub update_list_cards() {
    my ($astpp_db, $config, $sweep) = @_;
    my ( $sql, @cardlist, $row );
    print $sweep."\n" if $config->{debug} == 1;
    
    if ($sweep eq "") {
        $sql =
#           $astpp_db->prepare("SELECT number FROM accounts WHERE status < 2 AND (reseller IS NULL OR reseller = '') AND posttoexternal = 0 ");
	  $astpp_db->prepare("SELECT number FROM accounts WHERE status < 2 AND posttoexternal = 0 ");
    }
    else 
    {
        $sql =
#           $astpp_db->prepare("SELECT number FROM accounts WHERE status < 2 AND ( reseller IS NULL OR reseller = '') AND sweep = ". $astpp_db->quote($sweep) );
	  $astpp_db->prepare("SELECT number FROM accounts WHERE status < 2 AND sweep = ". $astpp_db->quote($sweep) );
    }
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cardlist, $row->{number};
    }
    $sql->finish;
    return @cardlist;
}

sub markbilled() {  # Update the status of a CDR in ASTPP.  Mostly used in external invoicing.
    my ( $astpp_db, $id, $status ) = @_;
    $astpp_db->do("UPDATE cdrs SET status = $status WHERE id = $id LIMIT 1");
}


######## OSCOmmerce Invoice Generation Support ###########################3
sub osc_order_total() {
    my ($osc_db, $config, $invoice_id) = @_;
    my ( $sql, $tmp, $row );
    $tmp =
      "SELECT SUM(value) FROM orders_total WHERE orders_id = "
      . $osc_db->quote($invoice_id);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row->{"SUM(value)"};
}

sub osc_post_total() {
    my ( $osc_db, $config, $invoice_id, $title, $text, $value, $sort, $class ) = @_;
    my ( $sql, $tmp );
    $tmp =
"INSERT INTO `orders_total` (`orders_id`,`title`,`text`,`value`,`class`,`sort_order`) VALUES ("
      . $osc_db->quote($invoice_id) . ", "
      . $osc_db->quote($title) . ", "
      . $osc_db->quote($text) . ", "
      . $osc_db->quote($value) . ", "
      . $osc_db->quote($class) . ", "
      . $osc_db->quote($sort) . ")";
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
}

sub osc_tax_list() {
    my ( $osc_db, $config, $zone_id, $zone_country_id ) = @_;
    my ( $tmp, $row, $sql, @taxes, @tax_list, $count, $tot_count, $tax_list );
    $tmp =
        "SELECT * FROM zones_to_geo_zones WHERE zone_id IN ("
      . $osc_db->quote($zone_id)
      . ", '0') OR zone_id IS NULL AND zone_country_id = "
      . $osc_db->quote($zone_country_id);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $tot_count = 0;
    while ( $row = $sql->fetchrow_hashref ) {
        push @tax_list, $row->{'geo_zone_id'};
        print STDERR "GEO ZONE ID: $row->{'geo_zone_id'} \n" if $config->{debug} == 1;
        $tot_count++;
    }
    $sql->finish;
    if ( $tot_count != 0 ) {
        $count    = 0;
        $tax_list = "IN (";
        foreach my $number (@tax_list) {
            print STDERR "SQL COMMAND GEO ZONE ID: $number \n" if $config->{debug} == 1;
            $tax_list .= $number;
            $count++;
            if ( $count < $tot_count ) {
                $tax_list .= ",";
            }
        }
        $tax_list .= ")";
    }
    else {
        $tax_list = "IS NULL";
    }
    $tmp =
        "SELECT * FROM tax_rates WHERE tax_zone_id "
      . $tax_list
      . " ORDER BY tax_priority DESC";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @taxes, $row;
    }
    $sql->finish;
    return @taxes;
}

sub osc_order_subtotal() {
    my ($osc_db, $config, $invoice_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT SUM(final_price) FROM orders_products WHERE orders_id="
      . $osc_db->quote($invoice_id);
    print STDERR "$tmp";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row->{"SUM(final_price)"};
}

sub osc_get_accountinfo() {
    my ($osc_db, $config, $account) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `customers` WHERE `customers_email_address` = "
      . $osc_db->quote($account);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;

    if ( $row->{customers_email_address} ) {
    	print STDERR "OSCommerce Account: $row->{customers_id}";
        return (1,$row);
    }
    
    $tmp =
      "SELECT * FROM `customers` WHERE `customers_firstname` = "
      . $osc_db->quote($account);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    if ( $row->{customers_email_address} ) {
    	print STDERR "OSCommerce Account: $row->{customers_id}";
        return (1,$row);
    }

	# Here we check to ensure that "customers_username"
	#  exists as it only exists if the appropriate add on is used.
    $tmp = "SELECT * FROM customers LIMIT 1";
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
	    $tmp =
	      "SELECT * FROM `customers` WHERE `customers_username` = "
	      . $osc_db->quote($account);
	    print STDERR "$tmp \n" if $config->{debug} == 1;
	    $sql = $osc_db->prepare($tmp);
	    $sql->execute;
	    $row = $sql->fetchrow_hashref;
	    $sql->finish;
	    if ( $row->{customers_email_address} ) {
	    	print STDERR "OSCommerce Account: $row->{customers_id}";
	        return (1,$row);
	    }
    else {
        print STDERR "OSCommerce Account: $account NOT FOUND!!!\n" if $config->{debug} == 1;
        return (0,"");
        #exit(0);
    }
}

sub osc_get_addressinfo() {
    my ($osc_db, $config, $address_book_id) = @_;
    my ( $sql, $row );
    $sql =
      $osc_db->prepare(
        "SELECT * FROM `address_book` WHERE `address_book_id` = "
          . $osc_db->quote($address_book_id) );
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_get_country() {
    my ($osc_db, $config, $country_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `countries` WHERE `countries_id` = "
      . $osc_db->quote($country_id);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_get_zone() {
    my ($osc_db, $config, $zone_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `zones` WHERE `zone_id` = " . $osc_db->quote($zone_id);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_create_invoice() {
    my ($astpp_db, $osc_db, $config, $account) = @_;
    my ( $status, $customer_info, $address_info, $customers_name );
    ($status, $customer_info) = &osc_get_accountinfo($osc_db, $config, $account);
    if ($status == 1) {
    $address_info  =
      &osc_get_addressinfo($osc_db, $config, $customer_info->{customers_default_address_id} );
    $customers_name =
        $customer_info->{customers_firstname} . " "
      . $customer_info->{customers_lastname};
    my $country_info   = &osc_get_country($osc_db, $config,  $address_info->{entry_country_id} );
    my $state_info     = &osc_get_zone($osc_db, $config, $address_info->{entry_zone_id} );
    my $date_purchased = &prettytimestamp;
    my $tmp = 
            "INSERT INTO orders (customers_id,customers_name, "
          . "customers_company,customers_street_address,customers_suburb,customers_city, "
          . "customers_postcode, customers_state, customers_country, customers_telephone, "
          . "customers_email_address, customers_address_format_id, delivery_name, delivery_company, "
          . "delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, "
          . "delivery_state, delivery_country, delivery_address_format_id, billing_name, "
          . "billing_company, billing_street_address, billing_suburb, billing_city, "
          . "billing_postcode, billing_state, billing_country, billing_address_format_id , "
          . "payment_method, cc_type, cc_owner, cc_number, cc_expires, last_modified, "
          . "date_purchased, orders_status, orders_date_finished, currency, currency_value) "
          . " VALUES ( "
          . $osc_db->quote( $customer_info->{customers_id} )
          . ", "                                      # customers_id
          . $osc_db->quote($customers_name) . ", "    #customers_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                      #customers_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                      #customers_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                      #customers_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", " #customers_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "    #customers_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", " #customers_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                              #customers_country
          . $osc_db->quote( $customer_info->{customers_telephone} )
          . ", "    #customers_telephone
          . $osc_db->quote( $customer_info->{customers_email_address} )
          . ", "    #customers_email_address
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "                                    #customers_address_format_id
          . $osc_db->quote($customers_name) . ", "  #delivery_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                    #delivery_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                    #delivery_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                    #delivery_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", "  #delivery_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "                                              #delivery_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", " #delivery_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                              #delivery_country
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "                                     #delivery_address_format_id
          . $osc_db->quote($customers_name) . ", "   #billing_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                     #billing_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                     #billing_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                                 #billing_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", " #billing_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "                                               #billing_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", "  #billing_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                               #billing_country
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "    #billing_address_format_id
          . $osc_db->quote( $config->{osc_payment_method} )
          . ", "                                      #payment_method
          . "'', "                                    #cc_type
          . "'', "                                    #cc_owner
          . "'', "                                    #cc_number
          . "'', "                                    #cc_expires
          . $osc_db->quote($date_purchased) . ", "    #last_modified
          . $osc_db->quote($date_purchased) . ", "    #date_purchased
          . $osc_db->quote( $config->{osc_order_status} )
          . ", "                                      #orders_status
          . "'',"                                     #orders_date_finished
          . "'USD',"                                  #currency
          . "1)";				      #currency_value	
    my $sql            = $osc_db->prepare($tmp);                                                
    $sql->execute;
    my $invoice = $sql->{'mysql_insertid'};
    $sql->finish;
    print STDERR "$tmp" if $config->{debug} == 1;
    print STDERR "OSCommerce Invoice Number: $invoice";
    return (
        $invoice,
        $address_info->{entry_country_id},
        $address_info->{entry_zone_id}
    );
    }
}

sub osc_post_charge() {
    my ($osc_db, $config, $invoice_id, $row ) = @_;
    my ( $sql, $desc, $tmp, $price );
    $desc  = "$row->{callstart} SRC: $row->{callerid} DST: $row->{callednum} SEC:$row->{billseconds} $row->{notes}";
    $price = $row->{debit} / 1;
    if($config->{osc_post_nc} == 1 || $price != 0) { 
       $tmp   =
"INSERT INTO `orders_products` (`orders_products_id`,`orders_id`,`products_id`,`products_name`,`products_price`,"
         . "`final_price`,`products_tax`,`products_quantity`) VALUES ('',"
         . "$invoice_id, "
         . $osc_db->quote( $config->{osc_product_id} ) . ", "
         . $osc_db->quote($desc) . ", "
         . "$price, $price,0,1)";
       $sql = $osc_db->prepare($tmp);
       $sql->execute;
    }
}

sub osc_charges() {
    my ($astpp_db, $osc_db, $config, $account, $params) = @_;
    my ( $invoice_id, $country_id, $zone_id, $tmp, $sql, $row, $cdr_count );
    if ($params->{startdate} && $params->{enddate}) {
    $tmp =
        "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")";
    } elsif ($params->{startdate}) {
    $tmp =
        "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")";
    } elsif ($params->{enddate}) {
    $tmp =
        "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")";
    } else {
    $tmp =
        "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0";
    }

    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row       = $sql->fetchrow_hashref;
    $cdr_count = $row->{"COUNT(*)"};
    $sql->finish;
    print STDERR "OSC_COUNT: $cdr_count \n" if $config->{debug} == 1;

    if ( $cdr_count > 0 ) {
        my (
            $tmp,  $subtotal,  @taxes,
            $sort, $tax_count, $total,      $tax_priority, $tax
        );
        ( $invoice_id, $country_id, $zone_id ) =
          &osc_create_invoice($astpp_db, $osc_db, $config, $account);
	  if ($invoice_id) {
    if ($params->{startdate} && $params->{enddate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")"
      . " ORDER BY callstart";
    } elsif ($params->{startdate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")"
      . " ORDER BY callstart";
    } elsif ($params->{enddate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")"
      . " ORDER BY callstart";
    } else {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " ORDER BY callstart";
    }
	print STDERR $tmp;
        $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        while ( $row = $sql->fetchrow_hashref ) {
            &osc_post_charge($osc_db, $config, $invoice_id, $row );
            &markbilled( $astpp_db, $row->{id}, 1 );
        }
        $subtotal = &osc_order_subtotal($osc_db, $config, $invoice_id);
        $subtotal = sprintf( "%." . $config->{decimalpoints_total} . "f", $subtotal );
        print STDERR "ORDER $invoice_id SUBTOTAL: $subtotal";
        $sort      = 1;
        $tax_count = 1;
        &osc_post_total( $osc_db, $config, $invoice_id, "Sub-Total:", "\$$subtotal", $subtotal,
            $sort, "ot_subtotal" );
        @taxes = &osc_tax_list($osc_db, $config, $zone_id, $country_id );
        foreach $tax (@taxes) {
            my ($tax_amount);
            if ( $tax_count == 1 ) {
                $tax_priority = $tax->{tax_priority};
                $tax_amount = $subtotal * ( $tax->{tax_rate} / 1 );
                $sort++;
                $tax_amount = sprintf( "%." . $config->{decimalpoints_tax} . "f", $tax_amount );
                &osc_post_total( $osc_db, $config, $invoice_id, $tax->{tax_description},
                    "\$$tax_amount", $tax_amount, $sort, "ot_tax" );
                $tax_count++;
            }
            else {
                if ( $tax->{tax_priority} > $tax_priority ) {
                    $subtotal = &osc_order_total($osc_db, $config, $invoice_id);
                }
                $tax_priority = $tax->{tax_priority};
                $tax_amount = $subtotal * ( $tax->{tax_rate} / 1 );
                $sort++;
                $tax_amount = sprintf( "%." . $config->{decimalpoints_tax} . "f", $tax_amount );
                &osc_post_total($osc_db, $config, $invoice_id, $tax->{tax_description},
                    "\$$tax_amount", $tax_amount, $sort, "ot_tax" );
            }
        }
        $total = &osc_order_total($osc_db, $config, $invoice_id);
        $sort++;
        $total = sprintf( "%." . $config->{decimalpoints_total} . "f", $total );
        &osc_post_total($osc_db, $config, $invoice_id, "Total:", "<b>\$$total</b>", $total,
            $sort, "ot_total" );
        &email_new_invoice( $astpp_db, "", $config, $account, $invoice_id,
            $total );
	    }
    }
    return $invoice_id
}

sub osc_get_account() {
    my ($osc_db, $config, $account) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `customers` WHERE `customers_email_address` = "
      . $osc_db->quote($account) . " OR WHERE 'customers_username' = "
      . $osc_db->quote($account);
    print STDERR "$tmp \n" if $config->{debug} == 1;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}


##################Periodic Billing Issues####################3
# This will need to be commented yet.


sub calc_charges() {
    my ($astpp_db, $config, $cardno, @output) = @_;
    my $cost = 0;
    my @chargelist = &get_charges($astpp_db, $config, $cardno);
    foreach my $id (@chargelist) {
        print STDERR "ID: $id\n" if $config->{debug} == 1;
        my $chargeinfo = &get_astpp_cdr( $astpp_db, $id );
        $cost = $cost + $chargeinfo->{debit} if $chargeinfo->{debit};
        print STDERR "Debit: $chargeinfo->{debit}  Credit: $chargeinfo->{credit}\n" if $config->{debug} == 1;
        $cost = $cost - $chargeinfo->{credit} if $chargeinfo->{credit};
        &markbilled( $astpp_db, $id, 1 );
    }
    return $cost;
}

sub get_charges() {
    my ($astpp_db, $config, $account,$params) = @_;
    my ( $tmp,$sql, @chargelist, $record );
    if ($params->{startdate} && $params->{enddate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")"
      . " ORDER BY callstart";
    } elsif ($params->{startdate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart >= DATE(" . $astpp_db->quote($params->{startdate}) . ")"
      . " ORDER BY callstart";
    } elsif ($params->{enddate}) {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " AND callstart <= DATE(" . $astpp_db->quote($params->{enddate}) . ")"
      . " ORDER BY callstart";
    } else {
    $tmp =
        "SELECT * FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0"
      . " ORDER BY callstart";
    }
    $sql =
      $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @chargelist, $record->{id};
    }
    $sql->finish;
    return @chargelist;
}

#Return Callshop Data
sub get_callshop() {
    my ( $astpp_db, $accountno ) = @_;
    my ( $sql, $accountdata );
    $sql =
      $astpp_db->prepare( "SELECT * FROM callshops WHERE name = "
          . $astpp_db->quote($accountno)
          . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
}

#Return ANI Data
sub get_ani_map() {
    my ( $astpp_db, $ani_number, $config ) = @_;
    my ( $sql,$tmp,$anidata );
    $tmp =
       "SELECT * FROM ani_map WHERE number = "
          . $astpp_db->quote($ani_number);
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $anidata = $sql->fetchrow_hashref;
    $sql->finish;
    return $anidata;
}


#Return callingcard cardnumber. Using in ANI Based authentications.
sub get_cardnumber(){
    my ( $astpp_db, $account,$number, $config ) = @_;
    my ( $sql,$tmp,$ccdata );
    $tmp =
       "SELECT * FROM callingcards WHERE (account = "
          . $astpp_db->quote($account)." OR account = ".$astpp_db->quote($number).")";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $ccdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $ccdata;
}

#Return Currency list 
sub get_all_currency() {
    my ( $astpp_db, $config ) = @_;
    my ( $sql,$tmp,@currencydata,$record );
    $tmp =
       "SELECT * FROM currency WHERE Currency!="
            . $astpp_db->quote($config->{base_currency});
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    
    while ( $record = $sql->fetchrow_hashref ) {
        push @currencydata, $record->{Currency};
    }
    $sql->finish;
    return @currencydata;        
}

#Return List of all ips
sub get_all_ip_map() {
    my ( $astpp_db) = @_;
    my ( $sql, @iplist, $row, $tmp );
    $tmp = "SELECT * FROM ip_map";
    print STDERR "$tmp\n" if $config->{debug} == 1;
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @iplist, $row->{ip};
    }
    $sql->finish;    
    return @iplist;
}

#Update account/DID inuse count
sub update_inuse() {  
        my ( $astpp_db, $cardnumber,$table , $count ) = @_;
        my $sql = "UPDATE $table SET inuse = inuse $count WHERE number = ". $astpp_db->quote( $cardnumber );
	print STDERR "$sql\n"  if $config->{debug}==1;
        $astpp_db->do($sql);
}

#Get account / Calling card outbound callerid number to override
sub get_outbound_callerid()
{
    my ( $astpp_db,$accountid,$table,$field) = @_;
    my ( $sql, $row, $tmp );
    $tmp = "SELECT * FROM $table where $field='".$accountid."' AND status=1";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;    
    $sql->finish;    
    return $row;
}

#Add Account callerid
sub add_callerid() {
    my ( $astpp_db, $params) = @_;
    my ($callstatus,$tmp,$status);
      
      if($params->{status} eq "on"){
		$callstatus = "1";
      }
      else {$callstatus = "0"; }

      $tmp = "INSERT INTO accounts_callerid (accountid,callerid_name,callerid_number,status ) VALUES ("
	    . $astpp_db->quote( $params->{accountid} ) . ", "
	    . $astpp_db->quote( $params->{callerid_name} ) . ", "
	    . $astpp_db->quote( $params->{callerid_number} )
	    . "," .$callstatus.")";
      if ( $astpp_db->do($tmp) ) {
	  $status = gettext("Account CallerID Added Successfully!");
      }
      else 
      {
	  $status = gettext("Failed to Add Account CallerID!");
      }
}

#Update Account callerid
sub edit_callerid() {

    my ( $astpp_db, $params) = @_;
    my ($callstatus,$tmp,$status);

    if($params->{status} eq "on"){
	$callstatus = "1";
    }
    else { $callstatus = "0"; }

    $tmp = "update accounts_callerid SET accountid =  ".$astpp_db->quote( $params->{accountid} ). ", "
	    . " callerid_name = " . $astpp_db->quote( $params->{callerid_name} ) . ", "
	    . " callerid_number =" . $astpp_db->quote( $params->{callerid_number} ) . ", "
	    . " status = " .$callstatus." WHERE accountid ="
	    . $astpp_db->quote( $params->{accountid} );

    if ( $astpp_db->do($tmp) ) {
	$status = gettext("Account CallerID Updated Successfully!");
    }
    else 
    {
	$status = gettext("Failed to Add Account CallerID!");
    }
}

#Add Callingcard callerid
sub add_cc_callerid() {
    my ( $astpp_db, $params) = @_;
    my ($callstatus,$tmp,$status);
      
    if($params->{status} eq "on"){
	      $callstatus = "1";
    }
    else { $callstatus = "0"; }

    $tmp = "INSERT INTO callingcards_callerid (cardnumber ,callerid_name,callerid_number,status ) VALUES ("
	  . $astpp_db->quote( $params->{cardnumber} ) . ", "
	  . $astpp_db->quote( $params->{callerid_name} ) . ", "
	  . $astpp_db->quote( $params->{callerid_number} )
	  . "," .$callstatus.")";

    if ( $astpp_db->do($tmp) ) {
	$status = gettext("Calling Card CallerID Added Successfully!");
    }
    else 
    {
	$status = gettext("Failed to Add Calling Card CallerID!");
    }
}

#Update callingcard callerid
sub edit_cc_callerid() {

    my ( $astpp_db, $params) = @_;
    my ($callstatus,$tmp,$status);

    if($params->{status} eq "on"){
	$callstatus = "1";
    }
    else { $callstatus = "0"; }

    $tmp = "update callingcards_callerid SET cardnumber  =  ".$astpp_db->quote( $params->{cardnumber } ). ", "
	    . " callerid_name = " . $astpp_db->quote( $params->{callerid_name} ) . ", "
	    . " callerid_number =" . $astpp_db->quote( $params->{callerid_number} ) . ", "
	    . " status = " .$callstatus." WHERE cardnumber  ="
	    . $astpp_db->quote( $params->{cardnumber} );

    if ( $astpp_db->do($tmp) ) {
	$status = gettext("Calling Card CallerID Updated Successfully!");
    }
    else 
    {
	$status = gettext("Failed to Add Calling Card CallerID!");
    }
}

#Add Email default email templates when new reseller, callshop is creating
sub addaccountemailtemplate()
{
    my ( $astpp_db, $config ,$params) = @_;
    my($accountid,$tmp,$sql,$accrecord,$temp,$record); 
    
    $temp = "SELECT accountid FROM accounts WHERE number = "
	  . $astpp_db->quote($params->{customnum});
    $sql = $astpp_db->prepare($temp);
    $sql->execute;
    $accrecord = $sql->fetchrow_hashref();			   	   
      
    $sql = $astpp_db->prepare("select * from default_templates");    
    $sql->execute;

    while ( $record = $sql->fetchrow_hashref() ) {
	$astpp_db->do(
	  "INSERT INTO templates (name,subject,accountid,template,modified_date) VALUES ("
	  . $astpp_db->quote( $record->{name} ) . ","
	  . $astpp_db->quote( $record->{subject} ) . ","
	  . $astpp_db->quote( $accrecord->{accountid} ) . ","
	  . $astpp_db->quote( $record->{template} ) . ","
	  . 'now()'
	  . ")" );
    }    
}

#Add Default Invoice configuration
sub addinvoiceconf()
{
    my ( $astpp_db, $config ,$params) = @_;
    my($accountid,$tmp,$sql,$accrecord,$temp,$record); 
    
    $temp = "SELECT accountid FROM accounts WHERE number = "
	  . $astpp_db->quote($params->{customnum});
    $sql = $astpp_db->prepare($temp);
    $sql->execute;
    $accrecord = $sql->fetchrow_hashref();
    
    $astpp_db->do(
	"INSERT INTO invoice_conf (accountid, company_name, address, city, province, country, zipcode, telephone, fax, emailaddress, website) VALUES ("
	.$astpp_db->quote( $accrecord->{accountid}).", 'Company name', 'Address', 'City', 'Province', 'Country', 'Zipcode', 'Telephone', 'Fax', 'Email Address', 'Website')" );
}