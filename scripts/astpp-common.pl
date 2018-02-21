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
# use Text::Template;
use POSIX;
use POSIX qw(strftime);
use DBI;
# use strict;
# use warnings;
use Locale::gettext_pp qw(:locale_h);
use Mail::Sendmail;

$ENV{LANGUAGE} = "en";    # de, es, br - whatever
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
    $tmp = "SELECT name,value FROM system WHERE reseller = " . $astpp_db->quote($reseller);
    $ASTPP->debug(debug =>"$tmp");
     
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
	print STDERR $row->{name} . "," . $row->{value} if $config->{debug} == 1;
    }
    $sql->finish;
    return $config;
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
	$ASTPP->debug(debug =>"$sql");
        $astpp_db->do($sql);
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


# Return the list of outbound routes you should use based either on cost or precedence.
sub get_outbound_routes() {
	my ( $astpp_db, $number, $accountinfo,$routeinfo, @reseller_list ) = @_;
	my ( @routelist, @outbound_route_list, $record, $sql,$maxlen_pattern,@outbound_routes_list );
	
	# Samir Doshi - Added prepend field in query 
	my $tmp = "SELECT 
			trunks.id as trunk_id, 
			trunks.name as name,
			trunks.tech as tech,    
			gateways.name as path,
			trunks.provider_id,
			trunks.status as status,
			trunks.dialed_modify as dialed_modify,
			trunks.reseller_id as trunks_reseller, 
			trunks.precedence as trunk_precedence,
			trunks.maxchannels as maxchannels,
			outbound_routes.pattern as pattern,
			outbound_routes.id as outbound_route_id,
			outbound_routes.connectcost as connectcost,
			outbound_routes.includedseconds as includedseconds,
			outbound_routes.cost as cost,
			outbound_routes.inc as inc,
			outbound_routes.reseller_id as outbound_route_resellers,
			outbound_routes.prepend
		    FROM 
			outbound_routes,trunks,gateways
		    WHERE
			gateways.id= trunks.gateway_id
		    AND trunks.id= outbound_routes.trunk_id 
		    AND ". $astpp_db->quote($number). " RLIKE outbound_routes.pattern ";
	if ($routeinfo->{precedence} && $routeinfo->{precedence} > 0 ){
		$tmp .= " AND outbound_routes.precedence <= ".$astpp_db->quote($routeinfo->{precedence})."";
	} 
 	$tmp .= " AND outbound_routes.status = 1  
			    ORDER BY 
				outbound_routes.precedence DESC, 
				outbound_routes.cost,
				outbound_routes.precedence,
				trunks.precedence ";
	$sql = $astpp_db->prepare($tmp);
	$sql->execute;
	while ( $record = $sql->fetchrow_hashref ) {
		push @routelist, $record;
	}
	$sql->finish;
		
	if (@reseller_list) {
		$ASTPP->debug(debug =>"CHECKING LIST OF RESELLERS AGAINST TRUNKS");
		foreach my $route (@routelist) {			
			$ASTPP->debug(debug =>"CHECKING ROUTE: $route->{trunks_reseller}");
			
			if ($route->{trunks_reseller} ne "") {				
			  
				$ASTPP->debug(debug =>"ROUTE RESELLER DATA If no reseller = $route->{trunks_reseller}");
				foreach my $reseller (@reseller_list) {					
					$ASTPP->debug(debug =>"Checking Reseller: $reseller against trunk: $route->{name}");
					if ($route->{trunks_reseller} =~ m/'$reseller'/) {					
						push @outbound_route_list, $route;
					}
				}				
			} else {
				$ASTPP->debug(debug =>"ROUTE RESELLER DATA If no reseller = $route->{trunks_reseller}");
				push @outbound_route_list, $route;
			}
		}
	} else {		
		$ASTPP->debug(debug =>"WE DID NOT RECEIVE A LIST OF RESELLERS TO CHECK AGAINST.");
		@outbound_route_list = @routelist;
	}

	return @outbound_route_list;
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

# Load configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
sub load_config_db() {
    my ($astpp_db, $config) = @_;
    my ($sql, @didlist, $row, $tmp );
    
    $tmp =
      "SELECT name,value FROM system WHERE reseller_id='0'";
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
      "SELECT name,value FROM system WHERE reseller_id = " . $astpp_db->quote($reseller);
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
      "SELECT name,value FROM system WHERE brand_id = " . $astpp_db->quote($brand);
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
	$ASTPP->debug(debug =>"opensips DATABASE IS DOWN");
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
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
        return $dbh;
    }
}


# Calculate the cost of a call based on the data received.
sub calc_call_cost() {
    my ( $connect, $cost, $answeredtime, $increment, $inc_seconds ) = @_;    
    $ASTPP->debug(debug =>"Connect: $connect , Cost: $cost , Answered: $answeredtime , Inc: $increment , Included: $inc_seconds");    
    if (!$increment || $increment == 0) {
	$increment = 1;
    }
    if (!$inc_seconds) {
	$inc_seconds = 0;
    }
    #print STDERR "ANSWERED TIME : ". $answeredtime."\n";        
    if ($answeredtime > 0) {
	my ($total_seconds);
	$total_seconds = ( $answeredtime - $inc_seconds ) / $increment;
	if ( $total_seconds < 0 ) {
	    $total_seconds = 0;
	}
	my $bill_increments = ceil($total_seconds);
	my $billseconds = $bill_increments * $increment;	
	$cost = ( $billseconds / 60 ) * $cost + $connect;
	
	$ASTPP->debug(debug =>"COST : ".$cost);
	$ASTPP->debug(debug =>"AnsweredTime: $answeredtime Included Sec: $inc_seconds");
	$ASTPP->debug(debug =>"Increment: $increment Billing Increments: $bill_increments");
	$ASTPP->debug(debug =>"Bill Seconds: $billseconds  Total cost is $cost");
	
	return $cost;
    } else {
	print STDERR "NO CHARGE - ANSWEREDTIME = 0\n" if $config->{debug} == 1;
	return 0;
   }
}


# Find the appropriate charge based on the day of the month.  This is mostly used for DID setup fees.
sub prorate() {
	my ($amount) = @_;
	my $current_year = strftime "%Y", gmtime;
	my $current_month = strftime "%m", gmtime;
	my $current_day = strftime "%d", gmtime;
	use Time::DaysInMonth;
	my $days = days_in($current_year, $current_month);
	
	$ASTPP->debug(debug =>"DAYS IN MONTH: $days");
	$ASTPP->debug(debug =>"TODAY: $current_day");
	
	my $start_date = $current_year . "-" . $current_month . "-" . $current_day;
	my $end_date = $current_year . "-" . $current_month . "-" . $days;
	my $daily_charge = ($amount / $days);
	my $prorated = ($daily_charge * ($days - $current_day));
	return ($prorated,$start_date,$end_date);
}


# Return the details on a specified DID from the ASTPP did table.
sub get_did() {
    my ( $astpp_db, $did ) = @_;
    my ( $sql, $diddata,$tmp );
    $tmp = "select 
			      accounts.id,
			      accounts.number as account_code,
			      dids.number as did_number, 
			      connectcost,
			      includedseconds,
			      monthlycost,
			      cost,
			      inc,
			      extensions,
			      provider_id,
			      setup,
			      limittime,
			      disconnectionfee,
			      variables,
			      options,
			      dids.maxchannels,
			      dial_as,
			      dids.call_type
			  from
			      dids,
			      accounts 
			  where 
			      accounts.id=dids.accountid
			  AND accounts.status=1    
			  AND dids.number = " . $astpp_db->quote($did);
    $sql = $astpp_db->prepare($tmp);
    $ASTPP->debug(debug =>"DID SQL : $tmp");
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
    $ASTPP->debug(debug =>"DID SQL : $tmp");
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
    } 
    else {
	$sql =
	  $astpp_db->prepare( "SELECT * FROM accounts WHERE id = "
	      . $astpp_db->quote($accountno)
	      . " AND status = 1" );        
	$sql->execute;
	$accountdata = $sql->fetchrow_hashref;
	$sql->finish;
	return $accountdata;
    }
}

# Return data on specified pricelist
sub get_pricelist() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, $pricelistdata, $tmp );
    $tmp = "SELECT * FROM pricelists WHERE id = " . $astpp_db->quote($pricelist);    
    $sql = $astpp_db->prepare($tmp);    
    $ASTPP->debug(debug =>"SQL : $tmp");
    $sql->execute;
    $pricelistdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $pricelistdata;
}

# Return data on specified pricelist
sub get_pricelist_by_name() {
    my ( $astpp_db, $pricelist ) = @_;
    my ( $sql, $pricelistdata, $tmp );
    $tmp = "SELECT id FROM pricelists WHERE name = " . $astpp_db->quote($pricelist);
    $sql = $astpp_db->prepare($tmp);
    $ASTPP->debug(debug =>"SQL : $tmp");
    $sql->execute;
    $pricelistdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $pricelistdata->{id};
}

# Return data on specified calling card brand
sub get_cc_brand() {
    my ( $astpp_db, $brand ) = @_;
    my ( $sql, $tmp, $branddata );
    $tmp =
        "SELECT * FROM callingcardbrands WHERE id = "
      . $astpp_db->quote($brand)
      . " AND status = 1";
    $ASTPP->debug(debug =>"SQL : $tmp");
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
          . " RLIKE pattern AND status = 1 AND pricelist_id = "
          . $astpp_db->quote($pricelist)
          . " ORDER BY LENGTH(pattern) DESC LIMIT 1";
    $ASTPP->debug(debug =>"SQL : $tmp");
    $sql =
      $astpp_db->prepare($tmp); 
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
};

# Return the appropriate "route" to use for determining costing on a call.
sub get_route() {
    my ( $astpp_db, $config, $destination, $pricelist, $carddata, $type,$pricelistdata ) = @_;
    my ( $record,   $sql,    $tmp );
    if (defined $type && $type =~ /ASTPP-DID/) {    	
	$ASTPP->debug(debug =>"Call belongs to a DID.");
	$record = &get_did_reseller($astpp_db,$destination,$carddata->{reseller_id}) if $carddata->{reseller_id} ne '0';
	$record = &get_did($astpp_db,$destination) if $carddata->{reseller_id} eq "0";
	$record->{comment} = $record->{city} . "," . $record->{province} . "," . $record->{country};
	$record->{pattern} = "DID:" . $destination;
	$record->{pricelist} = $pricelist;
        my $branddata = &get_pricelist( $astpp_db, $pricelist);
	$ASTPP->debug(debug =>"pattern: $record->{pattern}");
    }    
    else 
    {
	
	$record = &search_for_route($astpp_db,$config,$destination,$pricelist);
	$ASTPP->debug(debug =>"pattern: $record->{pattern}");
	
	while (!$record->{pattern} && $carddata->{reseller_id} ) {
		$carddata = &get_account($astpp_db, $carddata->{reseller_id});	
	        $record = &search_for_route($astpp_db,$config,$destination,$pricelist);
		$ASTPP->debug(debug =>"pattern: $record->{pattern}");
	}
	if ( !$record->{pattern} ) { #If we have not found a route yet then we look in the "Default" pricelist.
	        $record = &search_for_route($astpp_db,$config,$destination,&get_pricelist_by_name($astpp_db,$config->{default_brand}));
	        $ASTPP->debug(debug =>"pattern: $record->{pattern}");
	}	
	$ASTPP->debug(debug =>"Route: $record->{comment} Cost: $record->{cost} Pricelist: $record->{name} Pattern: $record->{pattern}");
    } 
    if ($record->{inc} &&( $record->{inc} eq "" || $record->{inc} == 0 )) {        
        $record->{inc} = $pricelistdata->{inc};
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
    $ASTPP->debug(debug =>$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $chargedata = $sql->fetchrow_hashref;
    $sql->finish;
    return $chargedata;
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
        $chargelist{ $row->{id} } = $row->{description} . " - \$" . $row->{charge};
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

sub write_callingcard_cdr() { # Write the callingcardcdr record if this is a calling card.
        my ($astpp_db, $config, $cardinfo,  $clid,   $destination, $status, $callstart, $charge, $answeredtime,$uid,$pricelist,$note,$pattern ) = @_;
        my ($sql);
        if (!$status) {$status = gettext("N/A"); }
        $sql = "INSERT INTO callingcardcdrs (callingcard_id,clid,destination,disposition,callstart,seconds,"
          . "debit,uniqueid,pricelist_id,notes,pattern) VALUES ("
	  . $astpp_db->quote( $cardinfo->{id} ) . ", "
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
	$ASTPP->debug(debug =>"SQL : ".$sql);
        $astpp_db->do($sql);
	
}

# Write ASTPP cdr.  I think this one is mostly deprecated but should probably be completely removed.
sub post_cdr() {
	my (
			$astpp_db,     $config, $uniqueid, $account, $clid,
			$dest,         $disp,       $seconds,  $cost,    $callstart,
			$postexternal, $trunk,      $notes,$pricelist,$pattern,$calltype,$provider, $callerip, $trunkip,$accountname
	   ) = @_;

# The cost is passed in 100ths of a penny.
	my ( $tmp, $status );
	$trunk    = gettext("N/A") if ( !$trunk );
	$uniqueid = gettext("N/A") if ( !$uniqueid );
	$pricelist = gettext("N/A") if ( !$pricelist );
	$pattern = gettext("N/A") if ( !$pattern );
	$provider = gettext("N/A") if ( !$provider );
	$callerip = gettext("N/A") if ( !$callerip );
	$trunkip = gettext("N/A") if ( !$trunkip );	
	$accountname = gettext("N/A") if ( !$accountname );
	$status   = 0;
	$tmp    = "INSERT INTO cdrs(uniqueid,accountid,callerid,callednum,trunk_id,disposition,billseconds,"
		. "debit,callstart,status,notes,pricelist_id,pattern,calltype,provider_id,callerip,trunkip,accountname) VALUES ("
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
		. $astpp_db->quote($provider) . ","
		. $astpp_db->quote($callerip) . ","
		. $astpp_db->quote($trunkip) .  ","
		. $astpp_db->quote($accountname) .  ")";
	$ASTPP->debug(debug =>"SQL : ".$tmp);
	$astpp_db->do($tmp);
	return $astpp_db->{mysql_insertid};
}


# Get the counter belonging to a specific package and customer.  Counters are used to track how many "package" seconds a customer has had so far this month.
sub get_counter() {
    my ( $astpp_db, $package, $cardnum ) = @_;
    my ( $sql, $row );
    $sql =
      $astpp_db->prepare( "SELECT * FROM counters WHERE package_id = "
          . $astpp_db->quote($package)
          . " AND accountid = "
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
      $astpp_db->prepare( "SELECT * FROM packages inner join package_patterns on packages.id = package_patterns.package_id WHERE "
          . $astpp_db->quote($number)
          . " RLIKE package_patterns.patterns AND pricelist_id = "
          . $astpp_db->quote( $carddata->{pricelist_id} )
          . " ORDER BY LENGTH(package_patterns.patterns) DESC LIMIT 1" );
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
}

# Return data on a specific calling card.
sub get_callingcard() {
    my ( $astpp_db, $cardno, $config ) = @_;
    my ( $sql,$tmp,$carddata );
    $tmp =
       "SELECT * FROM callingcards WHERE cardnumber = " . $astpp_db->quote($cardno);
    $ASTPP->debug(debug =>"SQL : ".$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $carddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $carddata;
}


# Return data on a specific calling card.
sub get_callingcard_by_pin() {
    my ( $astpp_db, $pin, $config ) = @_;
    my ( $sql,$tmp,$carddata );
    $tmp =
       "SELECT * FROM callingcards WHERE pin = "
          . $astpp_db->quote($pin);
    $ASTPP->debug(debug =>"SQL : ".$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $carddata = $sql->fetchrow_hashref;
    $sql->finish;    
    return $carddata;
}


# Calculate the maximum length of a call.
sub max_length() {
	my ($astpp_db, $config, $carddata, $phoneno) = @_;
	my ($branddata, $numdata, $credit, $credit_limit, $maxlength);
	$branddata = &get_pricelist( $astpp_db, $carddata->{pricelist_id} );	# Fetch all the pricelist info from the db.
	$numdata = &get_route( $astpp_db, $config, $phoneno, $carddata->{pricelist_id},'','',$branddata);    # Find the appropriate rate to charge the customer.
	if ( !$numdata->{pattern} ){  # If the pattern doesn't exist, we don't know what to charge the customer	
		# and therefore must exit.						
		$ASTPP->debug(debug =>"INVALID PHONE NUMBER");
		return (1,0);
	}	
	$ASTPP->debug(debug =>"Found pattern: $numdata->{pattern}");
	$ASTPP->debug(debug =>"Account Balance: " . $carddata->{balance});	
	$credit_limit = $carddata->{credit_limit};	
	$ASTPP->debug(debug =>"Credit Limit: " . $credit_limit);
			
	$credit = ($carddata->{balance} * -1) + ($credit_limit);         # Add on the accounts credit limit.	

	$ASTPP->debug(debug =>"Credit: " . $credit);
	
	if($credit <= 0 || $numdata->{connectcost} > $credit)
	{
	    return (0,0);
	}
	if ($branddata->{markup} > 0) {
		$numdata->{cost} = $numdata->{cost} + ( ($branddata->{markup} * $numdata->{cost}) / 100 )
	}	
	if ( $numdata->{cost} > 0 ) {
		$maxlength = int ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
		if ($config->{call_max_length} && ($maxlength > $config->{call_max_length} / 1000)){
			$ASTPP->debug(debug =>"LIMITING CALL TO CONFIG MAX LENGTH \n");
		        $maxlength = $config->{call_max_length} / 1000 / 60;
		}
	}
	else {		
		$ASTPP->debug(debug =>"CALL IS FREE - ASSIGNING MAX LENGHT \n");
		$maxlength = $config->{max_free_length};    # If the call is set to be free then assign a max length.
	}	
	return (1, sprintf( "%." . $config->{decimalpoints} . "f", $maxlength),$branddata,$numdata);
}


# Calculate the maximum length of a call.
sub max_length_cc() {
	my ($astpp_db, $config, $carddata, $phoneno) = @_;
	my ($branddata, $numdata, $credit, $credit_limit, $maxlength);
	$branddata = &get_pricelist( $astpp_db, $carddata->{pricelist_id} );	# Fetch all the pricelist info from the db.
	$numdata = &get_route( $astpp_db, $config, $phoneno, $carddata->{pricelist_id},'','',$branddata);    # Find the appropriate rate to charge the customer.
	if ( !$numdata->{pattern} ){  # If the pattern doesn't exist, we don't know what to charge the customer	
		# and therefore must exit.			
		$ASTPP->debug(debug =>"INVALID PHONE NUMBER");
		return (1,0);
	}	
	$ASTPP->debug(debug =>"Found pattern: $numdata->{pattern}");
	$credit = $cardinfo->{value} - $cardinfo->{used} ;
	$ASTPP->debug(debug =>"Credit: " . $credit);
	
	if($credit <= 0 || $numdata->{connectcost} > $credit)
	{
	    return (0,0);
	}
	if ($branddata->{markup} > 0) {
		$numdata->{cost} = $numdata->{cost} + ( ($branddata->{markup} * $numdata->{cost}) / 100 )
	}	
	if ( $numdata->{cost} > 0 ) {
		$maxlength = int ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
		if ($config->{call_max_length} && ($maxlength > $config->{call_max_length} / 1000)){
			$ASTPP->debug(debug =>"LIMITING CALL TO CONFIG MAX LENGTH");
		        $maxlength = $config->{call_max_length} / 1000 / 60;
		}
	}
	else {		
		$ASTPP->debug(debug =>"CALL IS FREE - ASSIGNING MAX LENGHT");
		$maxlength = $config->{max_free_length};    # If the call is set to be free then assign a max length.
	}	
	return (1, sprintf( "%." . $config->{decimalpoints} . "f", $maxlength),$branddata,$numdata);
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


######## Call Rating ################################

sub print_csv {  # Print CDRS on rated calls.
	my ( $config, $carddata,$cdrinfo,$numdata,$cost, @output ) = @_;
	my ( $outfile );	
	if ( $config->{rate_engine_csv_file} eq "" ) {
		$outfile = $config->{rate_engine_csv_file};
	}	
	$outfile = "/var/log/astpp/astpp.csv" if !$outfile;
	my $notes = "Notes: " . $cdrinfo->{accountcode};
	open(OUTFILE,">>$outfile") || print STDERR "CSV Error - could not open $outfile for writing\n";
print OUTFILE << "ending_print_tag";
$carddata->{number},$cdrinfo->{src},$cdrinfo->{dst},$cdrinfo->{disposition}, $cdrinfo->{billsec},$cost,$cdrinfo->{answer_stamp},$cdrinfo->{trunk},$numdata->{comment},$numdata->{pricelist_id}, $numdata->{pattern},$cdrinfo->{calltype}, $cdrinfo->{provider},$cdrinfo->{callerip},$cdrinfo->{trunkip}
ending_print_tag
# 	close(OUTFILE);
}

sub rating() {  # This routine recieves a specific cdr and takes care of rating it and of marking it as rated.  It bills resellers as appropriate.
	my ( $astpp_db, $cdr_db, $config, $cdrinfo, $carddata, $vars,$pricelist_id, @output ) = @_;	
	my ( $increment, $numdata, $package, $notes, $status,$cost,$cdr_lastid );
	$status = 0;	
	$ASTPP->debug(debug =>"----------------------------------------------------------------");
	$ASTPP->debug(debug =>"uniqueid: $cdrinfo->{uniqueid}, cardno: $carddata->{number}, phoneno: $cdrinfo->{dst}, Calltype: $cdrinfo->{calltype}\n");
	$ASTPP->debug(debug =>"disposition: $cdrinfo->{disposition} Pricelist: $pricelist_id reseller: $carddata->{reseller}");
	
		if ( $carddata->{dialed_modify} && ($cdrinfo->{calltype} ne "ASTPP-DID")) {
		my @regexs = split( m/,/m, $carddata->{dialed_modify} );	
		foreach my $regex (@regexs) {	    
		    $regex =~ s/"//g;  
		    my ( $grab, $replace ) = split( m!/!i, $regex );		    
		    $ASTPP->debug( debug => "Grab: $grab" );
		    $ASTPP->debug( debug => "Replacement: $replace" );
		    $ASTPP->debug( debug => "Phone Before: $cdrinfo->{dst}" );
		    $cdrinfo->{dst} =~ s/$grab/$replace/is;
		    $ASTPP->debug( debug => "Phone After: $cdrinfo->{dst}" );
		  }
		}

		$numdata = &get_route( $astpp_db, $config, $cdrinfo->{dst}, $pricelist_id, $carddata, $cdrinfo->{calltype} );
		if ( !$numdata->{pattern} ) {
			$ASTPP->debug(debug =>"ERROR - ERROR - ERROR - ERROR - ERROR");
			$ASTPP->debug(debug =>"NO MATCHING PATTERN\n");
			$ASTPP->debug(debug =>"----------------------------------------------------------------");
		}
		else {			
			$ASTPP->debug(debug =>"FOUND A MATCHING PATTERN: $numdata->{pattern}");
			my $branddata = &get_pricelist( $astpp_db, $carddata->{pricelist_id} );			
			$ASTPP->debug(debug =>"pricelist Data: $branddata->{name} $branddata->{markup} $branddata->{inc} $branddata->{status}");

			$package = &get_package( $astpp_db, $carddata, $cdrinfo->{dst} );
			if ($package->{id}) {
				my $counter = &get_counter( $astpp_db, $package->{id}, $carddata->{id} );
				my $difference;
				if ( !$counter->{id}) {
					my $tmp = "INSERT INTO counters (package_id,accountid) VALUES ("
						. $astpp_db->quote( $package->{id} ) . ", "
						. $astpp_db->quote( $carddata->{id} ) . ")";
					$ASTPP->debug(debug =>$tmp);
					$astpp_db->do($tmp);
					$counter = &get_counter( $astpp_db, $package->{id}, $carddata->{id} );
					$ASTPP->debug(debug =>"JUST CREATED COUNTER: $counter->{id}");
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
					$ASTPP->debug(debug =>$tmp);
					$astpp_db->do($tmp);
				}
			}

			if ( $branddata->{markup} ne "" && $branddata->{markup} != 0 ) {
				$numdata->{cost} = $numdata->{cost} + (($numdata->{cost} *  $branddata->{markup}) / 100);
			}
			if ( $numdata->{inc} > 0 ) {
				$increment = $numdata->{inc};
			}
			else {
				$increment = $branddata->{inc};
			}			
			$ASTPP->debug(debug =>"$numdata->{connectcost}, $numdata->{cost}, $cdrinfo->{billsec}, $increment, $numdata->{includedseconds}");
			$cost = &calc_call_cost(
					$numdata->{connectcost}, $numdata->{cost},
					$cdrinfo->{billsec},     $increment,
					$numdata->{includedseconds}
				    );

			$cost = sprintf( "%." . $config->{decimalpoints} . "f", $cost );
			$ASTPP->debug(debug =>"Matching pattern is $numdata->{pattern}\n");
	
			#Blocks all signals so that the program cannot be killed while writing costs.
			my $sigset = POSIX::SigSet->new;
			my $blockset = POSIX::SigSet->new( SIGINT, SIGQUIT, SIGCHLD );
			sigprocmask( SIG_BLOCK, $blockset, $sigset ) or die "Could not block INT,QUIT,CHLD signals: $!\n" if $config->{debug} == 1;			
			$last_cdrid = &post_cdr(
					$astpp_db,               $config,
					$cdrinfo->{uniqueid},    $carddata->{id},
					$cdrinfo->{src},         $cdrinfo->{dst},
					$cdrinfo->{disposition}, $cdrinfo->{billsec},
					$cost,                   $cdrinfo->{start_stamp},
					"",                      $cdrinfo->{trunk},
					$numdata->{comment},$numdata->{pricelist_id}, $numdata->{pattern},
					$cdrinfo->{calltype}, $cdrinfo->{provider},$cdrinfo->{callerip},$cdrinfo->{trunkip},$cdrinfo->{accountname}
					)
				if $config->{posttoastpp} == 1;			
			&update_balance($astpp_db, $carddata->{id}, $cost);	
			&print_csv($config,$carddata,$cdrinfo,$numdata,$cost, @output);			
			sigprocmask( SIG_SETMASK, $sigset ) # Restore the passing of signals			
			or die "Could not restore INT,QUIT,CHLD signals: $!\n" if $config->{debug} == 1;    #			
			$status = 1;
		}
	return ($status,$last_cdrid,$cost);
}

sub catch_zap { # This subroutine will not allow you to kill the process while it's in the most vital portion of rating a call.
	my $shucks = 0;
	my $signame = shift;
	$shucks++;
	die "Somebody sent me a SIG$signame!";
}

# Do call rating process
sub processlist() {
	my ($astpp_db, $config, $cdrinfo,$vars) = @_;
	my ( $status, $uniqueid);
	my ($cust_cdr_lastid,$cust_cdr_cost,$cdr_cost,$cdr_id);
	my ($rese_cdr_lastid,$rese_cdr_cost);
		if ( $cdrinfo->{accountcode} && $cdrinfo->{dst} ne "") {
			my $carddata = &get_account( $astpp_db, $cdrinfo->{accountcode} );
			if($cdrinfo->{calltype} eq 'DID')
			{
			    $carddata->{'pricelist_id'} = &get_pricelist_by_name($astpp_db,$config->{default_brand});
			}
			if ($carddata->{number} && $cdrinfo->{accountcode}) {
				$status = 0;
				
				($status,$cust_cdr_lastid,$cust_cdr_cost) = &rating( $astpp_db, $cdr_db,$config, $cdrinfo, $carddata, $vars,$cdrinfo->{pricelist_id});
				
				$cdr_lastid = $cust_cdr_lastid;
				$cdr_cost = $cust_cdr_cost;
				
				#Calculating in use count for account 
				if($cdrinfo->{calltype} eq 'STANDARD')
				{					
				      &update_inuse($astpp_db,$carddata->{number},'accounts','-1','-'.$config->{min_channel_balance});
				}
				if($cdrinfo->{calltype} eq 'DID')
				{
				      &update_inuse($astpp_db,$cdrinfo->{dst},'dids','-1');
				}
				#$cdrinfo  = &get_cdr( $config, $cdr_db, $uniqueid ) if !$vars;
				if ( $status == 1 ) {
					my $previous_account = $carddata->{id};
					while ( $carddata->{reseller_id} > 0 ) {
						#$cdrinfo  = &get_cdr( $config, $cdr_db, $uniqueid ) if !$vars;
						print STDERR "Charge $uniqueid to $carddata->{reseller_id}\n" if $config->{debug} == 1;
						$carddata = &get_account( $astpp_db, $carddata->{reseller_id} );
						if($cdrinfo->{calltype} eq 'DID')
						{
						    $carddata->{'pricelist_id'} = &get_pricelist_by_name($astpp_db,$config->{default_brand});
						}
						($status,$rese_cdr_lastid,$rese_cdr_cost) = &rating( $astpp_db, $cdr_db, $config, $cdrinfo, $carddata, $vars,$carddata->{pricelist_id});

						$tmp = "UPDATE cdrs SET cost = " . $rese_cdr_cost . " WHERE id = " . $cdr_lastid;
						$astpp_db->do($tmp);
						
						$cdr_cost = $rese_cdr_cost;
						$cdr_lastid = $rese_cdr_lastid;
						
						#Calculating in use count for account 
						if($cdrinfo->{calltype} eq 'STANDARD')
						{
						      &update_inuse($astpp_db,$carddata->{number},'accounts','-1','-'.$config->{min_channel_balance});
						}
						$previous_account = $carddata->{id};
					}
				}
			}
			else {
				$ASTPP->debug(debug =>"----------------------");
				$ASTPP->debug(debug =>"ERROR - ERROR - ERROR - ERROR - ERROR");
				$ASTPP->debug(debug =>"NO ACCOUNT EXISTS IN ASTPP");
				$ASTPP->debug(debug =>"uniqueid: $uniqueid, Account: $cdrinfo->{accountcode}, phoneno: $cdrinfo->{dst}");
				$ASTPP->debug(debug =>"disposition: $cdrinfo->{disposition}");
				$ASTPP->debug(debug =>"----------------------");
			}
		}
		my $phrase = "none";
		if ($config->{trackvendorcharges} == 1) {
			$ASTPP->debug(debug =>"Vendor billing enable");
			&vendor_process_rating_fs( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars,$cdrinfo,$cdr_lastid ) if $config->{softswitch} == 1;
		}	
}


sub vendor_process_rating_fs() {  #Rate Vendor calls.
	my ( $astpp_db, $cdr_db, $config, $phrase, $uniqueid, $vars, $cdrinfo,$cdr_lastid ) = @_;
	my ($sql,$tmp);
	if($cdrinfo->{outbound_route} ne '')
	{
		  my $tmp = "SELECT * FROM outbound_routes WHERE id = "
			  . $astpp_db->quote( $cdrinfo->{outbound_route} );		  
		  my $sql2 = $astpp_db->prepare($tmp);
		  $sql2->execute;
		  $ASTPP->debug(debug =>$tmp);
		  
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
					  (-1 * $cost),              $cdrinfo->{start_stamp},
					  "",                      $cdrinfo->{trunk},
					  $pricerecord->{comment},$pricerecord->{name},$pricerecord->{pattern},
					  $cdrinfo->{calltype}, $cdrinfo->{provider},$cdrinfo->{callerip},$cdrinfo->{trunkip}
					  ) if $config->{posttoastpp} == 1;
			  &update_balance($astpp_db, $cdrinfo->{provider}, $cost);
			  my $tmp = "UPDATE cdrs SET cost = '" . $cost . "' WHERE id = " .$cdr_lastid;
			  $ASTPP->debug(debug =>$tmp);
			  $astpp_db->do($tmp);
			  
		  } 
	}
}


#  This is all stuff out of the old astpp-update-balance.pl.  This is part of the project to seperate the posting of periodic charges with the creation
# of invoices in external applications.

sub update_list_cards() {
    my ($astpp_db, $config, $sweep) = @_;
    my ( $sql, @cardlist, $row );
    $ASTPP->debug(debug =>"Sweep : $sweep");
    
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


##################Periodic Billing Issues####################3
# This will need to be commented yet.


sub calc_charges() {
    my ($astpp_db, $config, $cardno, @output) = @_;
    my $cost = 0;
    my @chargelist = &get_charges($astpp_db, $config, $cardno);
    foreach my $id (@chargelist) {        
	$ASTPP->debug(debug =>"ID: $id");
        my $chargeinfo = &get_astpp_cdr( $astpp_db, $id );
        $cost = $cost + $chargeinfo->{debit} if $chargeinfo->{debit};        
	$ASTPP->debug(debug =>"Debit: $chargeinfo->{debit}  Credit: $chargeinfo->{credit}");
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
    $ASTPP->debug(debug =>"$tmp");
    $sql =
      $astpp_db->prepare($tmp);
    $ASTPP->debug(debug =>"ID: $tmp");
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @chargelist, $record->{id};
    }
    $sql->finish;
    return @chargelist;
}


#Return ANI Data
sub get_ani_map() {
    my ( $astpp_db, $ani_number, $config ) = @_;
    my ( $sql,$tmp,$anidata );
    $tmp =
       "SELECT * FROM ani_map WHERE number = "
          . $astpp_db->quote($ani_number);
    $ASTPP->debug(debug =>"$tmp\n");
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $anidata = $sql->fetchrow_hashref;
    $sql->finish;
    return ($anidata);
}


#Return callingcard cardnumber. Using in ANI Based authentications.
sub get_cardnumber(){
    my ( $astpp_db, $account,$number, $config ) = @_;
    my ( $sql,$tmp,$ccdata );
    $tmp =
       "SELECT * FROM callingcards WHERE (account = "
          . $astpp_db->quote($account)." OR account = ".$astpp_db->quote($number).")";
    $ASTPP->debug(debug =>"$tmp");
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
       "SELECT * FROM currency WHERE currency!="
            . $astpp_db->quote($config->{base_currency});    
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    
    while ( $record = $sql->fetchrow_hashref ) {
        push @currencydata, $record->{currency};
    }
    $sql->finish;
    return @currencydata;        
}

#Update account/DID inuse count
sub update_inuse() {  
        my ( $astpp_db, $cardnumber,$table , $count,$res_balance ) = @_;
        my $sql = "UPDATE $table SET inuse = inuse $count WHERE number = ". $astpp_db->quote( $cardnumber );
	$ASTPP->debug(debug =>"$sql");
        $astpp_db->do($sql);
	
	if($table eq 'accounts')
	{
	    my $sql = "UPDATE accounts SET balance = balance $res_balance WHERE number = ". $astpp_db->quote( $cardnumber );
	    $ASTPP->debug(debug =>$sql);
	    $astpp_db->do($sql);
	}
}

#Get account / Calling card outbound callerid number to override
sub get_outbound_callerid()
{
    my ( $astpp_db,$accountid,$table,$field) = @_;
    my ( $sql, $row, $tmp );
    $tmp = "SELECT * FROM $table where $field='".$accountid."' AND status=1";
    $ASTPP->debug(debug =>$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;    
    $sql->finish;    
    return $row;
}

sub search_for_block_prefixes() {
  my ( $astpp_db,$params,$id) = @_;
  my($temp,$sql,$accrecord);
  $temp = "SELECT * from block_patterns WHERE ".$astpp_db->quote($params)." RLIKE blocked_patterns AND accountid = ".$astpp_db->quote($id);
  $ASTPP->debug(debug =>$tmp);
  $sql = $astpp_db->prepare($temp);
  $sql->execute;
  $accrecord = $sql->fetchrow_hashref();
  return $accrecord;
}

#Use full when calling cad ani method is enabled. 
#This function will return calling card information from customer account number
sub get_callingcard_from_account()
{
    my ( $astpp_db, $cardno, $config ) = @_;
    my ( $sql,$tmp,$carddata );
    $tmp =
       "SELECT * FROM callingcards WHERE account = "
          . $astpp_db->quote($cardno);
    $ASTPP->debug(debug =>$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $carddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $carddata;
}


sub disconnect_db()
{
    my ($astpp_db,$cdr_db,$freeswitch_db) = @_;
    $astpp_db->disconnect if $astpp_db;
}


sub update_balance() {  #Update the available credit on the calling card.
        my ( $astpp_db, $cardid, $cost ) = @_;
        my $sql ="UPDATE accounts SET balance = balance+$cost WHERE id = ". $astpp_db->quote( $cardid );
	$ASTPP->debug(debug =>$sql);
        $astpp_db->do($sql);
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
        "SELECT number FROM accounts WHERE status < 2 AND reseller_id = "
      . $astpp_db->quote($reseller)
      . " ORDER BY number";
    $ASTPP->debug(debug =>$tmp);
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @accountlist, $row->{number};
    }
    $sql->finish;
    return @accountlist;
}


#Intialize the database connections
sub initialize() {
    $config = &load_config();            
    $astpp_db = &connect_db( $config, @output );    
    $ASTPP->set_astpp_db($astpp_db);
    $config = &load_config_db( $astpp_db, $config ) if $astpp_db;    
    $ASTPP->set_freeswitch_db($astpp_db);
    $cdr_db = $astpp_db;     
    $ASTPP->set_verbosity(verbosity_level=>$config->{debug});
}

#return not found xml
sub void_xml()
{
    my ($void_xml);
    $void_xml = header( -type => 'text/plain' );
    $void_xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
    $void_xml .= "<document type=\"freeswitch/xml\">\n";
    $void_xml .= "<section name=\"result\">\n";
    $void_xml .= "<result status=\"not found\" />";
    $void_xml .= "</section>\n";
    $void_xml .= "</document>\n";
    return $void_xml;
}

sub clean_cdr_data()
{
   my ($data) = @_;
   my ($cleandata);
            
   if (ref $data->{callflow} eq 'ARRAY') {
      $cleandata->{src} = uri_unescape($data->{callflow}->[0]->{caller_profile}->{caller_id_number});   
      $cleandata->{uniqueid} = uri_unescape($data->{callflow}->[0]->{caller_profile}->{uuid});
      $cleandata->{trunkip} = uri_unescape($data->{callflow}->[0]->{caller_profile}->{originatee}->{originatee_caller_profile}->{network_addr});  
      $cleandata->{context} = uri_unescape($data->{callflow}->[0]->{caller_profile}->{context});
      $cleandata->{rdnis} = uri_unescape($data->{callflow}->[0]->{caller_profile}->{rdnis});
   }else{
      $cleandata->{src} = uri_unescape($data->{callflow}->{caller_profile}->{caller_id_number});   
      $cleandata->{uniqueid} = uri_unescape($data->{callflow}->{caller_profile}->{uuid});
      $cleandata->{trunkip} = uri_unescape($data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{network_addr});  
      $cleandata->{context} = uri_unescape($data->{callflow}->{caller_profile}->{context});
      $cleandata->{rdnis} = uri_unescape($data->{callflow}->{caller_profile}->{rdnis});
   }
   
   $cleandata->{calltype} = uri_unescape($data->{variables}->{calltype});
   $cleandata->{direction} = uri_unescape($data->{variables}->{direction});      
   
   if($cleandata->{direction} eq 'inbound'){
      $destination_number = uri_unescape($data->{variables}->{effective_destination_number});
      if($destination_number eq "")
      {
	    if (ref $data->{callflow} eq 'ARRAY') {
	      $destination_number = uri_unescape($data->{callflow}->[0]->{caller_profile}->{destination_number});
	    }else{
	      $destination_number = uri_unescape($data->{callflow}->{caller_profile}->{destination_number});
	    }
      }      
   }else{
      $destination_number = $cleandata->{rdnis};
      $cleandata->{calltype} = "DID";
   }   
   $cleandata->{dst} = $destination_number;   
   $cleandata->{callingcard} = uri_unescape($data->{variables}->{callingcard});        
   
#    my $hangup_cause = uri_unescape($data->{variables}->{hangup_cause});
#    if ($hangup_cause eq 'NORMAL_CLEARING' && $data->{variables}->{billsec} == 0)
#    {
#       $hangup_cause = uri_unescape($data->{variables}->{originate_disposition});
#    }
# 
#    if($cleandata->{callingcard} ne "" && $data->{variables}->{last_bridge_hangup_cause} ne "NORMAL_CLEARING") 
#    {
# 	$data->{variables}->{billsec} = 0;
#         $hangup_cause=$data->{variables}->{last_bridge_hangup_cause};
#    }
#       
#    $cleandata->{disposition} = $hangup_cause;
   
   
   my $hangup_cause = uri_unescape($data->{variables}->{hangup_cause});
   $hangup_cause=$data->{variables}->{last_bridge_hangup_cause};  
   if ($hangup_cause eq "")
   {
	$hangup_cause = uri_unescape($data->{variables}->{hangup_cause});
   }
   $cleandata->{disposition} = $hangup_cause;
            
   $cleandata->{accountcode} = uri_unescape($data->{variables}->{accountcode});           
   $cleandata->{caller_id} = uri_unescape($data->{variables}->{caller_id});
   $cleandata->{channel_name} = uri_unescape($data->{variables}->{channel_name});
   $cleandata->{last_app} = uri_unescape($data->{variables}->{last_app});
   $cleandata->{last_arg} = uri_unescape($data->{variables}->{last_arg});
   $cleandata->{start_stamp} = uri_unescape($data->{variables}->{start_stamp});
   $cleandata->{answer_stamp} = uri_unescape($data->{variables}->{answer_stamp});
   $cleandata->{end_stamp} = uri_unescape($data->{variables}->{end_stamp});
   $cleandata->{duration} = uri_unescape($data->{variables}->{duration});
   $cleandata->{billsec} = uri_unescape($data->{variables}->{billsec});
   $cleandata->{originator} = uri_unescape($data->{variables}->{originator});   
   $cleandata->{read_codec} = uri_unescape($data->{variables}->{read_codec});
   $cleandata->{write_codec} = uri_unescape($data->{variables}->{write_codec});
   $cleandata->{provider} = uri_unescape($data->{variables}->{provider});
   $cleandata->{pricelist_id} = uri_unescape($data->{variables}->{pricelist_id});
   $cleandata->{accountname} = uri_unescape($data->{variables}->{accountname});
   $cleandata->{trunk} = uri_unescape($data->{variables}->{trunk});
   $cleandata->{outbound_route} = uri_unescape($data->{variables}->{outbound_route});
   $cleandata->{progressmsec} = uri_unescape($data->{variables}->{progressmsec});
   $cleandata->{answermsec} = uri_unescape($data->{variables}->{answermsec});
   $cleandata->{progress_mediamsec} = uri_unescape($data->{variables}->{progress_mediamsec});   
   $cleandata->{callerip} = uri_unescape($data->{variables}->{sip_contact_host});   
   $cleandata->{callingcard_destination} = $data->{variables}->{callingcard_destination};   
   return $cleandata;   
}


sub process_callingcard_cdr() {
	my ($astpp_db, $config, $cdrinfo,$vars) = @_;
	my ( $cardinfo, $brandinfo, $numberinfo, $pricelistinfo,$cc,$destination);
	
	$destination = $cdrinfo->{callingcard_destination};
	$destination =~ s/@.*//g;
		
	my $uid = $cdrinfo->{uniqueid};
        my $cardnumber = $cdrinfo->{callingcard};
	
        $cardinfo = &get_callingcard( $astpp_db, $cardnumber, $config );       
	$brandinfo = &get_cc_brand( $astpp_db, $cardinfo->{brand_id} );
	if ($brandinfo->{reseller_id}) {
	        $config     = &load_config_db_reseller($astpp_db,$config,$brandinfo->{reseller_id});
	}
	$config     = &load_config_db_brand($astpp_db,$config,$cardinfo->{brand_id});
	$pricelistinfo = &get_pricelist( $astpp_db, $brandinfo->{pricelist_id} );

	$ASTPP->debug(debug =>"THIS IS A CALLINGCARD CALL!");
	$ASTPP->debug(debug =>"CARD: $cardinfo->{cardnumber}");
	
	$numberinfo = &get_route(
	    $astpp_db, $config,
	    $destination,
	    $brandinfo->{pricelist_id}, $cardinfo
	);
	if ( $destination ne '' )
	{	    
	    my $increment;
	    if ( $numberinfo->{inc} > 0 ) {
		$increment = $numberinfo->{inc};
	    }
	    else {
		$increment = $pricelistinfo->{inc};
	    }
	    $ASTPP->debug(debug =>"$numberinfo->{connectcost}, $numberinfo->{cost}, $data->{variables}->{billsec}, $increment, $numberinfo->{includedseconds}",
		verbosity => $verbosity
	    );
	    my $charge = &calc_call_cost(
		$numberinfo->{connectcost},
		$numberinfo->{cost},
		$cdrinfo->{billsec},
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
			($data->{variables}->{billsec} =>
		      $cardinfo->{minute_fee_minutes});
	    }
	    if ( $cardinfo->{min_length_pennies} > 0
		&& ( $cardinfo->{min_length_minutes} * 60 ) >
		  $data->{variables}->{billsec} )
	    {
		$charge =
		  ( ( $cardinfo->{min_length_pennies} * 100 ) +
		      $charge );
	    }
	    
	    &write_callingcard_cdr(
		$astpp_db,
		$config,
		$cardinfo,
		$cdrinfo->{caller_id},
		$destination,
		$cdrinfo->{disposition},
		$cdrinfo->{start_stamp},
		$charge,
		$cdrinfo->{billsec},
		$uid,
		$brandinfo->{pricelist_id},
		$numberinfo->{comment},
		$numberinfo->{pattern}
	    );
	    &callingcard_set_in_use($astpp_db,$cardinfo,0);
	    &callingcard_update_balance($astpp_db,$cardinfo,$charge);
    }	
}
