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

require "/usr/local/astpp/astpp-callingcard-functions.pl";
sub xml_process()
{

    $sound_default_location = "/usr/local/freeswitch/sounds/en/us/callie/";

    #Define sound files 
    $sound = &define_sounds($sound_default_location);
    
    #Define default values
    $astpp_calltype = "ASTPP-STANDARD";  
        
    #set Default account name
    my $accountname = "default";
    
    my $destination_number = $params->{'Caller-Destination-Number'};
    
    my $origination_dp_string = "";
    my $termination_dp_string = "";
    
    my ( $resellermaxlength,$rategroup_info,$origination_rates_info,$origination_dp_string_reseller);
        
    #Check if dialed number is calling card access number
    if ($gbl_config->{cc_access_numbers} ne "")
    {
        my @cc = split ( m/,/m, $gbl_config->{cc_access_numbers});
        foreach my $cc (@cc) {
            if ($destination_number eq $cc)
            {
                &generate_cc_dialplan(context=>$params->{'Caller-Context'},destination_number=>$destination_number);
                break;
            }
        }             
    }
    
    #Check if dialed number is DID number.
    my $didinfo = &get_did($destination_number);
    
    if ($didinfo->{did_number}) {
	    $astpp_calltype = "ASTPP-DID";
        &logger("This is a call for a DID: $destination_number");
    	$params->{variable_accountcode} = $didinfo->{account_code};
    }
            
    #IF opensips then check then get account code from $params->{'variable_sip_h_P-Accountcode'}
    if($gbl_config->{opensips}=='1' && $params->{'variable_sip_h_P-Accountcode'} ne '' && $params->{'variable_sip_h_P-Accountcode'} ne '<null>' && $params->{variable_accountcode} eq '')
    {
    	$params->{'variable_accountcode'} = $params->{'variable_sip_h_P-Accountcode'};
    }                
    
    #If accountcode not found then check for ip address authentication.
    if ( !$params->{variable_accountcode} ) {
      
	    ##IF opensips then check then get ip address from $params->{'variable_sip_h_X-AUTH-IP'}
    	if($config->{opensips}=='1' && $params->{'variable_sip_h_X-AUTH-IP'} ne '')
	    {
	        $params->{'Hunt-Network-Addr'} = $params->{'variable_sip_h_X-AUTH-IP'};
	        $accountname = $params->{'Hunt-Username'};
    	}
	
        my $ipinfo = &ip_authentication(
            ip_address  => $params->{'Hunt-Network-Addr'},
            destination => $destination_number
        );
	
        if (defined $ipinfo->{account_code} && $ipinfo->{account_code} ne "") {
            $params->{variable_accountcode} = $ipinfo->{account_code};            
	        $params->{'Caller-Destination-Number'} = substr($destination_number,length($ipinfo->{prefix}));
	        $accountname = $ipinfo->{name};
        }        
    }       

    # If accountcode not found from above any feature then customer is not authenticated to use our system.
    &error_xml_without_cdr($destination_number,"AUTHENTICATION_FAIL") if ( !$params->{variable_accountcode} );
        
    # Fetch all the account info from the db.
    my $carddata = &get_account(field=>"number",value=>$params->{variable_accountcode});
    # If accountcode not found from above any feature then customer is not authenticated to use our system.
    &error_xml_without_cdr($destination_number,"ACCOUNT_INACTIVE_DELETED") if ( !$carddata->{id} );	
        
    #Print customer information
    &xml_logger("[Customer info] : [Account code: $carddata->{number}, Rategroup Id: $carddata->{'pricelist_id'}, Called number: $destination_number]");
    
    #Validate only for outbound/standard calls. (check account balance + validate block prefixes + number translation)
    if($astpp_calltype eq "ASTPP-STANDARD")
    {    
        $destination_number = &validate_account("destination_number"=>$destination_number,"carddata"=>$carddata);  
    }
                
    #Fetch status of length of call and orgination route info
    ($maxlength,$rategroup_info,$origination_rates_info,$origination_dp_string) = &max_length('destination_number'=>$destination_number,'carddata'=>$carddata);    
    
    
    &set_max_channels(name=>$carddata->{number},maxchannels=>$carddata->{maxchannels},interval=>$carddata->{interval}) if ($astpp_calltype eq 'ASTPP-STANDARD');
    
    my $minimumcharge = $origination_rates_info->{cost};    
    my @reseller_list;    
    my @inc_reseller_list;
    
    my $cust_pricelist_id = $carddata->{pricelist_id};
    my $cust_accountid = $carddata->{id};
    my $cust_resellerid = $carddata->{reseller_id};
    my $cust_type = $carddata->{type};
    
    #If customer has any reseller then process for reseller balance and route info.    
    while ( $carddata->{reseller_id} && $maxlength > 0) {
    
        &logger("FINDING LIMIT FOR: $carddata->{reseller_id}");        
        $carddata = &get_account(field=>"id",value=>$carddata->{reseller_id});
        &error_xml_without_cdr($destination_number,"ACCOUNT_INACTIVE_DELETED") if ( !$carddata->{id} );	
        
	    push @reseller_list, $carddata->{id};	
        &logger("ADDING $carddata->{number} to the list of resellers for this account");			    
	
	#Fetch status of length of call and orgination route info
        ( $resellermaxlength,$rategroup_info,$origination_rates_info,$origination_dp_string_reseller) = &max_length('destination_number'=>$destination_number,'carddata'=>$carddata);
	
        if ( $resellermaxlength < $maxlength ) {
            $maxlength = $resellermaxlength;
        }
        
        &xml_logger("Reseller cost = $origination_rates_info->{cost} and customer cost is $minimumcharge and reseller max length = $resellermaxlength");
	
    	#If customer call cost is lesser than reseller call cost, then hangup call. 
        if ( $resellermaxlength < 1 || $origination_rates_info->{cost} > $minimumcharge ) {
                &error_xml_without_cdr($destination_number,"RESELLER_COST_CHEAP");
        }
        $origination_dp_string = $origination_dp_string."||".$origination_dp_string_reseller;
        
        $minimumcharge = $origination_rates_info->{cost};
        
        &set_max_channels(name=>$carddata->{number},maxchannels=>$carddata->{maxchannels},interval=>$carddata->{interval}) if ($astpp_calltype eq 'ASTPP-STANDARD');        
    }

    #Print list of resellers.
    if ( $gbl_config->{debug} == 1 ) {
        &logger("PRINTING LIST OF RESELLERS FOR THIS ACCOUNT" );
        foreach my $reseller (@reseller_list) {
    	    &logger("RESELLER: $reseller" );
        }
    }

    &logger("Max Call Length: $maxlength minutes");
    &error_xml_without_cdr($arg{destination_number},"NO_SUFFICIENT_FUND") if($maxlength<=0);

    
    if ($astpp_calltype eq "ASTPP-DID") {
    
          &set_max_channels(name=>$didinfo->{did_number},maxchannels=>$carddata->{maxchannels}); 
            
	      &fs_dialplan_xml_did(
        	  context => $params->{'Caller-Context'},
	          accountcode => $params->{variable_accountcode},
	          accountid => $cust_accountid,
	          resellerid => $cust_resellerid,
	          account_type => $cust_type,	          
  	          destination_number => $destination_number,
	          max_length => $maxlength,
  		      call_type => $didinfo->{call_type},
		      extensions => $didinfo->{extensions},
		      gbl_xml_channels => ($gbl_xml_channels or ""),
  	          origination_dp_string => $origination_dp_string,
	      );	      
	} else {

		my $tmpbrd;
		
		# Get the list of routes for the phone number.
		my @termination_rates_info = &get_termination_rates(
		    destination_number => $destination_number,
		    carddata => $carddata,
		    origination_rates_info=>$origination_rates_info
	    );
	    
	    my $outboundcallerid;
		if(@termination_rates_info)
		{
		      my $count = 0;
		      my @trunk_duplicate;
		      
		      foreach my $termination_rate (@termination_rates_info) {
		     	if(!&in_array(\@trunk_duplicate,$termination_rate->{trunk_id}))
                        { 
			      &logger("$termination_rate->{name}: cost Termination Rate : $termination_rate->{cost} \t Origination Rate : $origination_rates_info->{cost} \t Code : $termination_rate->{pattern}" );
			      
			      if ( $termination_rate->{cost} > $origination_rates_info->{cost} ) {
				      &logger("$termination_rate->{name}: $termination_rate->{cost} > $origination_rates_info->{cost}, skipping");
			      }
			      else {		          			          
			          
			          my $termination_dp_string = "ID:$termination_rate->{outbound_route_id}|CODE:$termination_rate->{pattern}|DESTINATION:$termination_rate->{comment}|CONNECTIONCOST:$termination_rate->{connectcost}|INCLUDEDSECONDS:$termination_rate->{includedseconds}|COST:$termination_rate->{cost}|INC:$termination_rate->{inc}|TRUNK:$termination_rate->{trunk_id}|PROVIDER:$termination_rate->{provider_id}";
			            				  
				      $tmpbrd .= &fs_dialplan_xml_bridge(
					      destination_number => $destination_number,
					      termination_rate => $termination_rate,				      
					      termination_dp_string => $termination_dp_string
				      );				      
				      if($tmpbrd ne "")
				      {
        					$count++;
				      }
			      }			
			 }
		      }		      
		      &error_xml_without_cdr($arg{destination_number},"TERMINATION_RATES_NOT_FOUND") if($count == 0);
		      
		      #Issues : 37
		      if(!defined $params->{'variable_calltype'})
		      {
    		     	 #Fetch outbound callerid for accounts & If exist and active then override it
	    	      	 $outboundcallerid = &get_outbound_callerid(
	    	         	accountid=>$cust_accountid,
	    	            	table=>'accounts_callerid',
	    	            	field=>'accountid'
	               	 );		
              	      }
		}else{
		    &error_xml_without_cdr($destination_number,"TERMINATION_RATES_NOT_FOUND");
		}
		
		&fs_dialplan_xml_standard(
        	  context => $params->{'Caller-Context'},
	          accountcode => $params->{variable_accountcode},
	          accountid => $cust_accountid,
	          resellerid => $cust_resellerid,
	          account_type => $cust_type,
  	          destination_number => $destination_number,
	          max_length => $maxlength,
	          gbl_xml_logger => $gbl_xml_logger,
	          bridge_string => $tmpbrd,
	          outbound_callerid => $outboundcallerid,
	          origination_dp_string => $origination_dp_string,
	          gbl_xml_channels => ($gbl_xml_channels or "")
	      );
		
	}
}
1;
