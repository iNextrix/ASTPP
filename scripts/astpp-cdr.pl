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

use strict;
use warnings;
use POSIX;

#Normalize rate string to array
sub normalize_rate()
{
    my ($string) = @_;    
    if ($string)
    {
        my $rec = {};
        
        #&logger(uri_unescape($string));
        my @rate_array = split /\|/, uri_unescape($string);
        foreach my $rate_info (@rate_array) {    
            my %rate_tmp = split /\:/, $rate_info;
            my ($key,$value) = each %rate_tmp;       
            $rec->{$key} = $value;
        }
        return $rec;
    }
}

#Normalize origination rate string to array
sub normalize_origination_rate()
{
    my ($string) = @_;
    my $rate_info = {};
    
    my @array = split /\|\|/, uri_unescape($string);
    foreach my $rate (@array) {
        my $rate_tmp = &normalize_rate($rate);
        &logger(Dumper($rate_tmp));
        $rate_info->{$rate_tmp->{ACCID}} = $rate_tmp;
    }
    return $rate_info;
}

#Calculate cost
sub calc_cost()
{
    my ($data,$rate) = @_;    
    
    my $call_cost =0;
    
#    if($data->{variables}->{hangup_cause} and $data->{variables}->{bridge_hangup_cause} ne "")
#    {
#        $data->{variables}->{hangup_cause} = $data->{variables}->{bridge_hangup_cause};
#    }else{
#        if($data->{variables}->{bridge_hangup_cause} and $data->{variables}->{last_bridge_hangup_cause} ne "")
#        {
#            $data->{variables}->{hangup_cause} = $data->{variables}->{last_bridge_hangup_cause};
#        }
#    }
            
#    if ($data->{variables}->{hangup_cause} ne 'NORMAL_CLEARING' && $data->{variables}->{hangup_cause} ne 'ALLOTTED_TIMEOUT') {
#        $duration = 0;
#        $data->{variables}->{billsec} = 0;
#    }   
    
    if (defined($rate))
    {
        if ($data->{variables}->{billsec} > 0 && ($data->{variables}->{hangup_cause} eq 'NORMAL_CLEARING' || $data->{variables}->{hangup_cause} eq 'ALLOTTED_TIMEOUT')) {
            
            $rate->{INC} = ($rate->{INC} == 0) ? 1 : $rate->{INC};
            
            my $total_seconds = ( $data->{variables}->{billsec} - $rate->{'INCLUDEDSECONDS'} ) / $rate->{INC};
	        $total_seconds = ( $total_seconds < 0 ) ? 0 : $total_seconds;
	        	    
	        my $billseconds = ceil($total_seconds) * $rate->{INC};                
            $call_cost = ( $billseconds / 60 ) * $rate->{COST} + $rate->{CONNECTIONCOST};          
        }
        return sprintf( "%." . $gbl_config->{decimalpoints} . "f", $call_cost);
    }else{
        return '0.0000';
    }
}

#Update account balance
sub update_balance()
{
    my(%arg)=@_;
    &insert_update_query("Update Balance","UPDATE accounts SET balance = balance-".$arg{amount}. " WHERE id = ". $arg{accountid});
}

#Package calculation
sub package_calculation()
{
    my(%arg)=@_;
    my $custom_destination = &custom_destination_number('destination_number'=>$arg{'destination_number'},'field'=>'patterns');
    
    my $package = &select_query("Package","SELECT * FROM packages inner join package_patterns on packages.id = package_patterns.package_id WHERE$custom_destination AND status = 0 AND pricelist_id = ". $arg{'origination_rate'}->{RATEGROUP} . " ORDER BY LENGTH(package_patterns.patterns) DESC LIMIT 1");
          
	if ($package->{id}) {
		#my $counter = &get_counter( $astpp_db, $package->{id}, $carddata->{id} );
		my $counter = &get_counter(package_id=>$package->{id},accountid=>$arg{'origination_rate'}->{ACCID});
		
		my $difference;
		if ( !$counter->{id}) {
			&insert_update_query("Insert counter","INSERT INTO counters (package_id,accountid) VALUES (".$package->{id}.",".$arg{'origination_rate'}->{ACCID}.")");		
			$counter = &get_counter(package_id=>$package->{id},accountid=>$arg{'origination_rate'}->{ACCID});
			&logger("JUST CREATED COUNTER: $counter->{id}");
		}
		
		if ( $package->{includedseconds} > $counter->{seconds}) {
			my $availableseconds = $package->{includedseconds} - $counter->{seconds};
			my $freeseconds = ($availableseconds >= $data->{variables}->{billsec}) ? $data->{variables}->{billsec} : $availableseconds;
			$data->{variables}->{billsec} = ($availableseconds >= $data->{variables}->{billsec}) ? $data->{variables}->{billsec} : $availableseconds;
			&insert_update_query("Update counter","UPDATE counters SET seconds = ".($counter->{seconds} + $freeseconds ). " WHERE id = ". $counter->{id});
			$data->{variables}->{package_id}=$package->{id};
			$data->{variables}->{calltype} = "FREE";
		}
	}
}

#Get package counter
sub get_counter()
{
    my(%arg)=@_;
    return &select_query("Get Counter","SELECT * FROM counters WHERE package_id = ". $arg{'package_id'}. " AND accountid = ".$arg{'accountid'});
}

1;

