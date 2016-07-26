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

use CGI qw/:standard Vars/;
use XML::Simple;
use Data::Dumper;
use URI::Escape;
use DateTime;
use DateTime::Format::Strptime;

use strict;
use warnings;

#use Encode qw(decode encode);
use utf8;
use vars qw($params $gbl_config $gbl_astpp_db $gbl_xml_logger $astpp_calltype $data);

require "/usr/local/astpp/astpp-database.pl";
require "/usr/local/astpp/astpp-logger.pl";
require "/usr/local/astpp/astpp-common.pl";
require "/usr/local/astpp/astpp-cdr.pl";

################# Programs start here #######################################
&initialize;

foreach my $param ( param() ) {
    $params->{$param} = param($param);
}

print header( -type => 'text/plain' );

if ( $params->{cdr} or $params->{POSTDATA}) { # PROCESS CDRs.
	  
      # create object
      my $xml = new XML::Simple;

      my $cdr = ($params->{cdr})?$params->{cdr}:$params->{POSTDATA};
      
      # read XML file
      $data = $xml->XMLin($cdr);
                  
      if ($data->{variables}->{effective_destination_number} && $data->{variables}->{call_processed} eq "internal")
      {      
          if($data->{variables}->{calltype} eq "CALLINGCARD" && !$data->{variables}->{originating_leg_uuid})
          {
            exit();
          }
          #&logger(Dumper($data));  
          &logger("========================== CDR Starts : ".$data->{variables}->{effective_destination_number}."=====================");     
    
          if ($data->{variables}->{hangup_cause} ne 'NORMAL_CLEARING' && $data->{variables}->{hangup_cause} ne 'ALLOTTED_TIMEOUT') {
        	  	$data->{variables}->{billsec} = 0;
          }
	 
	 if($data->{variables}->{hangup_cause} eq 'NORMAL_CLEARING' && $data->{variables}->{billsec} == 0)
         {
                $data->{variables}->{hangup_cause} = $data->{variables}->{last_bridge_hangup_cause};
         }	  
            
          #get require parameters from dialplan varaibles  
          my $accountid = $data->{variables}->{accountid};
          my $account_type = ($data->{variables}->{account_type} or '0');
          my $parentid = ($data->{variables}->{resellerid} or '0');
          my $parent_cost = 0;
          my $cost = 0;
          $data->{variables}->{package_id} = 0;         
          my $actual_duration = $data->{variables}->{billsec};
          my $actual_calltype = $data->{variables}->{calltype};
          
          
          #normalize origination rates     
          my ($origination_rate) = &normalize_origination_rate($data->{variables}->{origination_rates});
          &logger("Origination Rate\n".Dumper($origination_rate));
          
          #normalize termination rates
          my ($termination_rate) = &normalize_rate($data->{variables}->{termination_rates});
          &logger("Termination Rate\n".Dumper($termination_rate));
                    
          #Package calculation 
          &package_calculation(destination_number=>$data->{variables}->{effective_destination_number},origination_rate=>$origination_rate->{$accountid},duration=>$actual_duration) if ($actual_duration > 0 && $data->{variables}->{calltype} ne 'DID');
          
          #calculate debit for customer                       
          my $debit = &calc_cost($data,$origination_rate->{$accountid});
                              
          #calculate cost for provider
          my $provider_cost = &calc_cost($data,$termination_rate);
          
          $parent_cost = ($parentid > 0) ? &calc_cost($data,$origination_rate->{$parentid}) : $provider_cost;
          $cost = ($parent_cost > 0) ? $parent_cost : $provider_cost;
                    
          &logger("Customer Debit : ".$debit." ------ Cost : ".$cost." -------- Provider Cost : ".$provider_cost."----- Duration : ".$data->{variables}->{billsec});
          
          #Converting all dates to GMT
          $data->{variables}->{profile_start_stamp} = &convert_to_gmt($data,$data->{variables}->{profile_start_stamp});
          $data->{variables}->{answer_stamp} = &convert_to_gmt($data,$data->{variables}->{answer_stamp});
          $data->{variables}->{bridge_stamp} = &convert_to_gmt($data,$data->{variables}->{bridge_stamp});
          $data->{variables}->{progress_stamp} = &convert_to_gmt($data,$data->{variables}->{progress_stamp});
          $data->{variables}->{progress_media_stamp} = &convert_to_gmt($data,$data->{variables}->{progress_media_stamp});
          $data->{variables}->{end_stamp} = &convert_to_gmt($data,$data->{variables}->{end_stamp});
          
          #$data->{callflow}->{caller_profile}->{originatee}->{originatee_caller_profile}->{network_addr}
          
          my $cdr_string = "".$gbl_astpp_db->quote($data->{variables}->{uuid}).",".$accountid.",".$account_type.",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{caller_id})).",".$gbl_astpp_db->quote($data->{variables}->{effective_destination_number}).",".$actual_duration.",".($termination_rate->{TRUNK} or 0).",".$gbl_astpp_db->quote('').",".$gbl_astpp_db->quote($data->{variables}->{sip_contact_host} or '').",".$gbl_astpp_db->quote($data->{variables}->{hangup_cause}).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{callstart})).",".$debit.",".$cost.",".($termination_rate->{PROVIDER} or 0).",".$origination_rate->{$accountid}->{RATEGROUP}.",".$data->{variables}->{package_id}.",".$gbl_astpp_db->quote($origination_rate->{$accountid}->{CODE}).",".$gbl_astpp_db->quote(($origination_rate->{$accountid}->{DESTINATION}) or '').",".$origination_rate->{$accountid}->{COST}.",".$parentid.",".($gbl_astpp_db->quote($origination_rate->{$parentid}->{CODE} or '')).",".$gbl_astpp_db->quote($origination_rate->{$parentid}->{DESTINATION} or '').",".($origination_rate->{$parentid}->{COST} or '0').",".($gbl_astpp_db->quote(($termination_rate->{CODE} or '')) or '').",".$gbl_astpp_db->quote(($termination_rate->{DESTINATION} or '')).",".$gbl_astpp_db->quote(($termination_rate->{COST} or '0')).",".$provider_cost.",".$gbl_astpp_db->quote($data->{variables}->{call_direction}).",".$gbl_astpp_db->quote($data->{variables}->{calltype}).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{profile_start_stamp})).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{answer_stamp})).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{bridge_stamp})).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{progress_stamp})).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{progress_media_stamp} or '')).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{end_stamp})).",".$data->{variables}->{billmsec}.",".$data->{variables}->{answermsec}.",".$data->{variables}->{waitmsec}.",".$data->{variables}->{progress_mediamsec}.",".$data->{variables}->{flow_billmsec}."";
          
          &print_csv($cdr_string,'customer');
          
          #insert customer & provider cdr 
          &insert_update_query("Customer CDR","INSERT INTO cdrs(uniqueid,accountid,type,callerid,callednum,billseconds,trunk_id,trunkip,callerip,disposition,callstart,debit,cost,provider_id,pricelist_id,package_id,pattern,notes,rate_cost,reseller_id,reseller_code,reseller_code_destination,reseller_cost,provider_code,provider_code_destination,provider_cost,provider_call_cost,call_direction,calltype,profile_start_stamp,answer_stamp,bridge_stamp,progress_stamp,progress_media_stamp,end_stamp,billmsec,answermsec,waitmsec,progress_mediamsec,flow_billmsec) values ($cdr_string)");
                             
          #update customer balance          
          &update_balance(accountid=>$accountid,amount=>$debit) if ($debit > 0 && $data->{variables}->{calltype} ne "FREE");
          
          #update provider balance
          &update_balance(accountid=>$termination_rate->{PROVIDER},amount=>($parent_cost*-1)) if ($parent_cost > 0);
          
          #CDR for resellers
          while ( $parentid > 0 ) {
    
                $data->{variables}->{package_id} = 0;
                $data->{variables}->{calltype} = $actual_calltype;
                
                #get reseller information    
                my $carddata = &get_account(field=>"id",value=>$parentid);
                
                &package_calculation(destination_number=>$data->{variables}->{effective_destination_number},origination_rate=>$origination_rate->{$carddata->{id}},duration=>$actual_duration) if ($actual_duration > 0  && $data->{variables}->{calltype} ne 'DID');
                
                #Calculate reseller debit value 
                my $debit = &calc_cost($data,$origination_rate->{$carddata->{id}});
                
                $parentid = $carddata->{reseller_id};
                
                #finalize cost for reseller                
                $parent_cost = ($parentid > 0) ? &calc_cost($data,$origination_rate->{$parentid}) : $provider_cost;
                $cost = ($parent_cost > 0) ? $parent_cost : $provider_cost;
                
                my $cdr_string = "".$gbl_astpp_db->quote($data->{variables}->{uuid}).",".$carddata->{id}.",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{caller_id})).",".$gbl_astpp_db->quote($data->{variables}->{effective_destination_number}).",".$actual_duration.",".$gbl_astpp_db->quote($data->{variables}->{hangup_cause}).",".$gbl_astpp_db->quote(uri_unescape($data->{variables}->{callstart})).",".$debit.",".$cost.",".$origination_rate->{$carddata->{id}}->{RATEGROUP}.",".$data->{variables}->{package_id}.",".$gbl_astpp_db->quote($origination_rate->{$carddata->{id}}->{CODE} or '').",".$gbl_astpp_db->quote($origination_rate->{$carddata->{id}}->{DESTINATION} or '').",".($origination_rate->{$carddata->{id}}->{COST} or '0').",".$parentid.",".$gbl_astpp_db->quote($origination_rate->{$parentid}->{CODE} or '').",".$gbl_astpp_db->quote($origination_rate->{$parentid}->{DESTINATION} or '').",".($origination_rate->{$parentid}->{COST} or '0').",".$gbl_astpp_db->quote($data->{variables}->{call_direction}).",".$gbl_astpp_db->quote($data->{variables}->{calltype})."";
                
                &print_csv($cdr_string,'reseller');
                
                #Insert cdr for reseller
                &insert_update_query("Reseller CDR","INSERT INTO reseller_cdrs (uniqueid,accountid,callerid,callednum,billseconds,disposition,callstart,debit,cost,pricelist_id,package_id,pattern,notes,rate_cost,reseller_id,reseller_code,reseller_code_destination,reseller_cost,call_direction,calltype) values ($cdr_string)");                              
               
               #update balance of reseller 
               &update_balance(accountid=>$carddata->{id},amount=>$debit) if ($debit > 0 && $data->{variables}->{calltype} ne "FREE");
         }                                                            
         &logger("========================== CDR Ends : ".$data->{variables}->{effective_destination_number}."=====================");
     }   
}
&disconnect_db($gbl_astpp_db);
1;
