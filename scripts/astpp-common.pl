#!/usr/bin/perl
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd. - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################

use POSIX qw(strftime);
use DBI;
use strict;
use warnings;

# Return the details on a specified DID from the ASTPP did table.
sub get_did() {
    my ( $did ) = @_;
    $did= &number_translation('destination_number'=>$did,'translation'=>$gbl_config->{did_global_translation});
    return &select_query("DID","SELECT B.id,B.id,B.number as account_code,A.number as  did_number,A.connectcost,A.includedseconds,A.cost,A.inc,A.extensions,A.maxchannels,A.call_type,A.city,A.province FROM dids AS A,accounts AS B WHERE B.status=0 AND B.deleted=0 AND B.id=A.accountid AND A.number = ".$gbl_astpp_db->quote($did));    
}

# Return information on a DID is the customer belongs to a reseller.
sub get_did_reseller() {

    my (%arg) = @_; 
    $arg{destination_number}= &number_translation('destination_number'=>$arg{destination_number},'translation'=>$gbl_config->{did_global_translation});   
    return &select_query("Reseller DID","SELECT A.id, A.number AS number,B.cost AS cost,B.connectcost AS connectcost,B.includedseconds AS includedseconds,B.inc AS inc,A.city AS city,A.province,A.call_type,A.extensions AS extensions,A.maxchannels AS maxchannels FROM dids AS A,reseller_pricing as B WHERE A.number = ".$gbl_astpp_db->quote($arg{destination_number}). " AND B.type = '1' AND B.reseller_id = ". $arg{carddata}->{reseller_id} . " AND B.note = ". $gbl_astpp_db->quote($arg{destination_number})."");
}

#To do ip based authentication
sub ip_authentication
{
	my (%arg) = @_;			
#	return &select_query("IP Authentication","SELECT ip_map.*, (SELECT number FROM accounts where id=accountid AND status=0 AND deleted=0) AS account_code FROM ip_map WHERE ip = " . $gbl_astpp_db->quote($arg{ip_address}). " AND prefix IN (NULL,'') OR ip = " . $gbl_astpp_db->quote($arg{ip_address}). " AND " . $gbl_astpp_db->quote($arg{destination}) . " RLIKE prefix ORDER BY LENGTH(prefix) DESC LIMIT 1");

	return &select_query("IP Authentication","SELECT ip_map.*, (SELECT number FROM accounts where id=accountid AND status=0 AND deleted=0) AS account_code FROM ip_map WHERE SUBSTRING( ip, 1, CHAR_LENGTH( ip ) -3 ) = " . $gbl_astpp_db->quote($arg{ip_address}). " AND prefix IN (NULL,'') OR SUBSTRING( ip, 1, CHAR_LENGTH( ip ) -3 ) = " . $gbl_astpp_db->quote($arg{ip_address}). " AND " . $gbl_astpp_db->quote($arg{destination}) . " RLIKE prefix ORDER BY LENGTH(prefix) DESC LIMIT 1");
}

# Go looking for an account and only return open accounts.
sub get_account() {
    my (%arg) = @_;  
    return &select_query("Get Account","SELECT * FROM accounts WHERE ".$arg{field}." = ". $gbl_astpp_db->quote($arg{value}). " AND status = 0 AND deleted=0");
}

#Validate account for outbound calls.
sub validate_account()
{
    my (%arg) = @_;  
    
    &error_xml_without_cdr($arg{destination_number},"NO_SUFFICIENT_FUND") if(&get_balance($arg{carddata}) <= 0);
    
    #Check if destination blocked
    my $block_prefix = &validate_block_prefixes("destination_number"=>$arg{destination_number},"accountid"=>$arg{carddata}->{id});
    &error_xml_without_cdr($arg{destination_number},"DESTINATION_BLOCKED") if($block_prefix->{id});
    
    my $card_flag = &validate_card_usage('carddata'=>$arg{carddata});
    &error_xml_without_cdr($arg{destination_number},"ACCOUNT_EXPIRE") if ($card_flag);
    
    #Do number translation if defined in account
    if ($arg{carddata}->{dialed_modify}) {
        $arg{destination_number}= &number_translation('destination_number'=>$arg{destination_number},'translation'=>$arg{carddata}->{dialed_modify});
    }
    return $arg{destination_number};
}

#check if account is active or expire. 
sub validate_card_usage() {

    # Check a few things before saying the card is ok.
    my (%arg) = @_;
    
    my $now = &now();

    # Now the card is in use and nobody else can use it.
    if ($arg{carddata}->{first_used} eq "0000-00-00 00:00:00")
    {
        # If "firstused" has not been set, we will set it now.
        &insert_update_query("Update First use","UPDATE accounts SET first_used = '$now' WHERE id = " . $arg{carddata}->{id});
        
        if ( $arg{carddata}->{validfordays} > 0 )
        {
            #Check if the card is set to expire and deal with that as appropriate.            
            &insert_update_query("Update expiry","UPDATE accounts SET expiry = DATE_ADD('$now', INTERVAL " . " $arg{carddata}->{validfordays} day) WHERE id = ".$arg{carddata}->{id});
            return 0;
        }
    }
    elsif ($arg{carddata}->{validfordays} > 0 ){
        
        $arg{carddata}->{expiry} = $gbl_astpp_db->selectall_arrayref("SELECT DATE_FORMAT('$arg{carddata}->{expiry}' , '\%Y\%m\%d\%H\%i\%s')")->[0][0];
        $now = $gbl_astpp_db->selectall_arrayref("SELECT DATE_FORMAT('$now' , '\%Y\%m\%d\%H\%i\%s')")->[0][0];
        if($now >= $arg{carddata}->{expiry}) {
            &insert_update_query("Update account status","UPDATE accounts SET status = 1 WHERE id = " . $arg{carddata}->{id});
    	    &remove_ani($arg{carddata});
            return 1;
        }
    }elsif ($arg{carddata}->{validfordays} < 0) {
        return 1;
    }
}

#Calculate balance 
sub get_balance()
{
    my ($carddata) = @_;
    return ($carddata->{credit_limit} * $carddata->{posttoexternal}) + $carddata->{balance};
}

#Check block prefixes
sub validate_block_prefixes() {
  my (%arg) = @_; 
  my $custom_destination = &custom_destination_number('destination_number'=>$arg{'destination_number'},'field'=>'blocked_patterns');
  return &select_query("Block Prefixes","SELECT * from block_patterns WHERE ($custom_destination) AND accountid = ".$arg{accountid}." limit 1");
}

#Create custom destination number string
sub custom_destination_number()
{
    my (%arg) = @_;

	$arg{field} = "pattern" if !$arg{field};
    
    my $max_len_prefix  = length($arg{'destination_number'});

    my $number_prefix = '(';
    while ($max_len_prefix  > 0)
    {
        $number_prefix .= "$arg{field}='^".substr($arg{'destination_number'},0,$max_len_prefix).".*' OR ";
        $max_len_prefix--;
    }
    $number_prefix .= "$arg{field}='^defaultprefix.*')";
    return $number_prefix; 
}

#Do number translation
sub number_translation()
{
    my (%arg) = @_;
    my @regexs = split( m/,/m, $arg{translation});
    foreach my $regex (@regexs) {	    
        $regex =~ s/"//g;    #Strip off quotation marks
        my ( $grab, $replace ) = split( m!/!i, $regex );
                    
        &logger("Grab :$grab Replacement: $replace Phone Before: $arg{destination_number}");

	    my $number_prefix = substr($arg{destination_number},0,length($grab));
        if ($number_prefix eq $grab)
        {
             $arg{destination_number} = $replace.substr($arg{destination_number},length($grab));
        }
        &logger("Phone After: $arg{destination_number}")
    }
    return $arg{destination_number};
}

# Calculate the maximum length of a call.
sub max_length() {

	my (%arg) = @_;
	
	my $rategroup = &select_query("Rate group","select * from pricelists WHERE id = " .$arg{carddata}->{pricelist_id}." AND status = 0");
	&error_xml_without_cdr($arg{destination_number},"ORIGNATION_RATES_NOT_FOUND") if(!$rategroup->{id});
			
	my $origination_rates = &get_origination_rates(destination_number=>$arg{destination_number},rategroup=>$rategroup,carddata=>$arg{carddata});    # Find the appropriate rate to charge the customer.
	
	#for calling card only 
	return 1 if(!defined($origination_rates->{id}));
	
	if ($rategroup->{markup} > 0) {
		$origination_rates->{cost} = $origination_rates->{cost} + ( ($rategroup->{markup} * $origination_rates->{cost}) / 100 )
	}
	      
    #Generate string to pass into dialplan
    my $origination_dp_string = "ID:$origination_rates->{id}|CODE:$origination_rates->{pattern}|DESTINATION:$origination_rates->{comment}|CONNECTIONCOST:$origination_rates->{connectcost}|INCLUDEDSECONDS:$origination_rates->{includedseconds}|COST:$origination_rates->{cost}|INC:$origination_rates->{inc}|RATEGROUP:$rategroup->{id}|MARKUP:$rategroup->{markup}|ACCID:$arg{carddata}->{id}";
    		
    &error_xml_without_cdr($arg{destination_number},"NO_SUFFICIENT_FUND") if(&get_balance($arg{carddata}) < $origination_rates->{connectcost});			
    		
	my $maxlength = 0;
		
	if ( $origination_rates->{cost} > 0 ) {
	    
		$maxlength = int ( ( &get_balance($arg{carddata}) - $origination_rates->{connectcost} ) / $origination_rates->{cost});
		
		if ($gbl_config->{call_max_length} && ($maxlength > $gbl_config->{call_max_length} / 1000)){
	        $maxlength = $gbl_config->{call_max_length} / 1000 / 60;
			&logger("LIMITING CALL TO CONFIG MAX LENGTH : ".$maxlength);
		}
	}
	else {			    
		$maxlength = $gbl_config->{max_free_length};    # If the call is set to be free then assign a max length.
		&logger("FREE CALL - LIMITING CALL TO CONFIG MAX LENGTH : ".$maxlength);
	}
	&error_xml_without_cdr($arg{destination_number},"NO_SUFFICIENT_FUND") if($maxlength<=0);
	return (sprintf( "%." . $gbl_config->{decimalpoints} . "f", $maxlength),$rategroup,$origination_rates,$origination_dp_string);
}

# Find origination rates 
sub get_origination_rates() {
    my (%arg) = @_;
    my ($did_record,$record,$sql,$tmp);
    if ($astpp_calltype eq "ASTPP-DID") {

	    $did_record = &get_did_reseller(destination_number=>$arg{destination_number},carddata=>$arg{carddata}) if $arg{carddata}->{reseller_id} ne '0';
	    
	    $did_record = &get_did($arg{destination_number}) if $arg{carddata}->{reseller_id} eq "0";
	    
	    $record->{id}=$did_record->{id};
	    $record->{pattern} = '^.'.$arg{destination_number}.'.*';
	    $record->{comment} = ($did_record->{city} ne '') ?  $did_record->{city} : ($did_record->{province} ne "") ? $did_record->{province} : $arg{destination_number};
	    $record->{connectcost} = $did_record->{connectcost};
	    $record->{includedseconds} = $did_record->{includedseconds};
	    $record->{cost} = $did_record->{cost};	    
	    $record->{inc} = $did_record->{inc};
    }    
    else 
    {	
        my $custom_destination = &custom_destination_number('destination_number'=>$arg{destination_number});
        
        $record = &select_query("Origination Rates","SELECT * FROM routes WHERE ($custom_destination) AND status = 0 AND pricelist_id = ".$arg{rategroup}->{id}." ORDER BY LENGTH(pattern) DESC,cost DESC LIMIT 1");	    	 
	    &error_xml_without_cdr($arg{destination_number},"ORIGNATION_RATES_NOT_FOUND") if(!$record->{pattern});	            
    }
     
    if ($record->{inc} &&( $record->{inc} eq "" || $record->{inc} == 0 )) {
        $record->{inc} = $arg{rategroup}->{inc};
    }
    return $record;
}


# Return the list of outbound routes you should use based either on cost or precedence.
sub get_termination_rates() {

	my (%arg) = @_;
	
	my ( @routelist, $record, $sql,$where );
	
	if($arg{origination_rates_info}->{trunk_id} > 0){
    	  $where = "outbound_routes.trunk_id = ".$arg{origination_rates_info}->{trunk_id};
	}else{
	      $record = &select_query("Routing","SELECT GROUP_CONCAT(trunk_id) as trunk_id FROM routing WHERE pricelist_id=".$arg{origination_rates_info}->{pricelist_id});
	      if($record > 0){
	        $where = "outbound_routes.trunk_id IN (".$record->{'trunk_id'}.")";
    	  }else{
	        &error_xml_without_cdr($arg{destination_number},"TERMINATION_RATES_NOT_FOUND");
	      }
	}
	
	my $custom_destination = &custom_destination_number('destination_number'=>$arg{destination_number});
	return &select_query("Termination rates","SELECT trunks.id as trunk_id,trunks.codec,gateways.name as path,trunks.provider_id,trunks.status,trunks.dialed_modify,trunks.maxchannels,outbound_routes.pattern,outbound_routes.id as outbound_route_id,outbound_routes.connectcost,outbound_routes.comment,outbound_routes.includedseconds,outbound_routes.cost,outbound_routes.inc,outbound_routes.prepend,outbound_routes.strip,(select name from gateways where status=0 AND id = trunks.failover_gateway_id) as path1,(select name from gateways where status=0 AND id = trunks.failover_gateway_id1) as path2 FROM outbound_routes,trunks,gateways WHERE $where AND gateways.status=0 AND gateways.id= trunks.gateway_id AND trunks.status=0 AND trunks.id= outbound_routes.trunk_id AND ($custom_destination) AND outbound_routes.status=0 ORDER BY LENGTH (pattern) DESC,outbound_routes.cost ASC,outbound_routes.precedence ASC, trunks.precedence","multi");
}

#Get account / Calling card outbound callerid number to override
sub get_outbound_callerid()
{
    my (%arg) = @_;
    return &select_query("Outbound callerid","SELECT * FROM $arg{table} where $arg{field}=".$gbl_astpp_db->quote($arg{accountid})." AND status=0");
}

#Remove callerid from expired account.
sub remove_ani(){
    my ($cardinfo) = @_;
    &insert_update_query("Delete ANI","DELETE FROM ani_map WHERE accountid = ". $cardinfo->{id});
}

# Returns a timestamp in a human friendly format.
sub now() {
    my $now = strftime "%Y-%m-%d %H:%M:%S", gmtime;
    return $now;
}

# Convert current date to gmt
sub convert_to_gmt()
{
    my ($data,$variable) = @_;        
    $variable = uri_unescape($variable);
    $data->{variables}->{callstart} = uri_unescape($data->{variables}->{callstart});
    return (defined($variable) && $variable ne '') ? $gbl_astpp_db->selectall_arrayref("SELECT TIMESTAMP(\"".$variable."\",timediff(\"".$data->{variables}->{callstart}."\",\"".$variable."\"))")->[0][0] : '';
}
sub in_array
{
     my ($arr,$search_for) = @_;
     my %items = map {$_ => 1} @$arr;
     return (exists($items{$search_for}))?1:0;
}
1;

