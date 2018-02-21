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

#Authenticate calling card
sub auth_callingcard()
{
    return 1 if ( !$session->ready() );   
    
    my $ani_flag = 0;
    my $cardnum = -1;
    my $cardinfo;
    my $pin="";
    my $carddata;
    
    #If ANI authentication enable then validate customer using callerid
    if($gbl_config->{cc_ani_auth}==1)
    {
       my $ani_number = $session->getVariable("caller_id_number");
       &print_console("Callerid authentication : ".$ani_number);

        my $aniinfo = &get_ani(callerid => $ani_number);
        
        if($aniinfo->{number}){                 
        
          $cardinfo = &get_account(field=>"id",value=>$aniinfo->{'accountid'});          
          $cardnum = $cardinfo->{number};
          
          &print_console("Authenticated account : ".$cardinfo->{number});
          my $card_flag = &validate_card_usage('carddata'=>$cardinfo);
          if ($card_flag)
          {
              &error_xml_without_cdr("","ACCOUNT_EXPIRE");
              $session->hangup();
          }
          $ani_flag = 1;
        }
    } 
    
    # If calling card not authenticated using callerid then authenticate using card number and pin number.
    if($ani_flag == 0){
    
        # calling card.
        if (!defined ($cardinfo->{number})) {
	 
	        my $retries = 0;
	        my $authenticated = 0;
	        while ( !$authenticated && $retries < $gbl_config->{card_retries} ) {
                $cardnum = $session->playAndGetDigits(
        	        1,
        	        15,
        	        1,
        	        $gbl_config->{calling_cards_number_input_timeout},
        	        "#",
	                "$sound->{cardnumber}",
        	        "",
        	        '^[0-9]+$'
                );                
                
		    &print_console("We recieved a cardnumber : ".$cardnum);
		    if ($cardnum ne "")
		    {
                $cardinfo = &get_account(field=>"number",value=>$cardnum);       
            }
            
            if($cardinfo->{number} eq ""){
                $session->streamFile($sound->{cardnumber_incorrect} );
            }else{
                $authenticated = 1;
                $pin=$cardinfo->{pin};
            }
            # Flush dtmf digit on queue
            $session->flushDigits();
            $retries++;
         }
         
         if ( !$authenticated && $retries == $gbl_config->{card_retries} ) {
	        $session->streamFile( $sound->{goodbye} );
	        #return 1;
		$session->hangup();
	    }
      }

      if ( $pin ne "" ) {
	      my $retries = 0;
	      my $authenticated = 0;
          while ( !$authenticated && $retries < $gbl_config->{pin_retries} ) {
              my $pin_number = $session->playAndGetDigits(
                  1,
       	          15,
       	          1,
    	          $gbl_config->{calling_cards_pin_input_timeout},
	              "#",
	              "$sound->{pin}",
	              "",
	              '^[0-9]+$'
              );
              
              &print_console("We recieved a pin : ".$pin_number);
              if ( $pin_number != $pin ) {
	            $session->streamFile( $sound->{pin_incorrect} );
              }else{
                $authenticated = 1;
              }
              $retries++;
          }

          if ( !$authenticated && $retries == $gbl_config->{pin_retries} ) {
  	        $session->streamFile( $sound->{pin_incorrect} );
		$session->hangup();
          }
      }      
      
      #Validate customer
      my $card_flag = &validate_card_usage('carddata'=>$cardinfo);
      if ($card_flag)
      {
          &error_xml_without_cdr("","ACCOUNT_EXPIRE");
          $session->hangup();
      }
      
      #Ask for save callerid for pinless authentication
      &save_ani($cardinfo);
    }
    $params->{variable_accountcode} = $cardinfo;    
    return $cardinfo;
}

#Get Callerid 
sub get_ani() {
    my ( %arg ) = @_;    
    return &select_query("Get ANI MAP", "SELECT * FROM ani_map WHERE number = ". $gbl_astpp_db->quote($arg{'callerid'})." AND status=0");
}

#Save callerid
sub save_ani(){
    my ($cardinfo) = @_;

    my $result = $session->playAndGetDigits(
		  1,1,1,
		  5000,
		  "#",
		  "$sound->{register_ani}",
		  "",
		  '^[1-1]+$'
    );
    
    if($result eq "1"){
        &insert_update_query("Insert ANI","INSERT INTO ani_map (number,accountid,status) VALUES (".$gbl_astpp_db->quote($session->getVariable("caller_id_number")).",".$cardinfo->{id}.",0)");
    }
}

# Play balance of account
sub say_balance() {    #Calculate and say the card balance.

    return 1 if ( !$session->ready() );

    my ($cardinfo) = @_;    
    my $balance = &get_balance($cardinfo);
    
    if ( $balance > 0 ) {
    
        if ($gbl_config->{calling_cards_balance_announce} eq "1")
        {
            my @split_balance = split( /\./, ( sprintf( "%.2f", $balance / 1) ) );
	
            $session->streamFile( $sound->{card_has_balance_of} );
            if ( $split_balance[0] eq 1 ) {
                $session->execute( "say", "en number pronounced " .  $split_balance[0] );
                $session->streamFile( $sound->{main_currency} );
            }
            elsif ( $split_balance[0] ne 1 ) {
                $session->execute(  "say", "en number pronounced " .  $split_balance[0] );
                $session->streamFile( $sound->{main_currency_plural} );
            }
            if ( $split_balance[1] eq 1 ) {
                $session->execute(  "say", "en number pronounced " . $split_balance[1] );
                $session->streamFile( $sound->{sub_currency} );
            }
            elsif ( $split_balance[1] ne 1 ) {
                $session->execute(  "say", "en number pronounced " . $split_balance[1] );
                $session->streamFile( $sound->{sub_currency_plural} );
            }
        }
    }
    else 
    {
         $session->streamFile( $sound->{card_is_empty} );
         $session->streamFile( $sound->{goodbye} );
  	     $session->hangup();
  	     return 1;
    }
    return $balance;
}


#Process for destination
sub process_destination()
{
    return 1 if ( !$session->ready() );
    my ($carddata) = @_;
  
    my $destination = $session->playAndGetDigits( 4, 35, 3,$gbl_config->{calling_cards_dial_input_timeout},"#", "$sound->{destination}", "$sound->{destination_incorrect}", '^[0-9]+$' ) ;  
    &print_console("Dialed destination number : ".$destination);
    
    my $original_destination_number = $destination;
    
    #Check if destination blocked
    my $block_prefix = &validate_block_prefixes("destination_number"=>$destination,"accountid"=>$carddata->{id});
    if($block_prefix->{id})
    {
        &error_xml_without_cdr($destination,"DESTINATION_BLOCKED") ;
        return 1;
    }
    
    #Do number translation if defined in account
    if ($carddata->{dialed_modify}) {
        $destination = &number_translation('destination_number'=>$destination,'translation'=>$carddata->{dialed_modify});
    }
    
    #Fetch status of length of call and orgination route info
    my ($maxlength,$rategroup_info,$origination_rates_info,$origination_dp_string) = &max_length('destination_number'=>$destination,'carddata'=>$carddata);
    
    return 1 if(!defined($origination_rates_info->{id}));
    
    my $customer_origination_rates_info = $origination_rates_info; 
    my $customer_carddata = $carddata;

    $session->execute("limit","db ".$carddata->{number}." db_".$carddata->{number}." ".$carddata->{maxchannels}) if($carddata->{maxchannels} > 0);
    
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
        &error_xml_without_cdr($destination,"ACCOUNT_INACTIVE_DELETED") if ( !$carddata->{id} );	
        
	    push @reseller_list, $carddata->{id};
        &logger("ADDING $carddata->{number} to the list of resellers for this account");			    
	
	    #Check if destination blocked
        my $block_prefix = &validate_block_prefixes("destination_number"=>$destination,"accountid"=>$carddata->{id});
        if($block_prefix->{id})
        {
            &error_xml_without_cdr($destination,"DESTINATION_BLOCKED") ;
            return 1;
        }
	
	#Fetch status of length of call and orgination route info
        my ( $resellermaxlength,$rategroup_info,$origination_rates_info,$origination_dp_string_reseller) = &max_length('destination_number'=>$destination,'carddata'=>$carddata);
	    return 1 if(!defined($origination_rates_info->{id}));
	        
        if ( $resellermaxlength < $maxlength ) {
            $maxlength = $resellermaxlength;
        }
        
        &xml_logger("Reseller cost = $origination_rates_info->{cost} and customer cost is $minimumcharge and reseller max length = $resellermaxlength");
	
    	#If customer call cost is lesser than reseller call cost, then hangup call. 
        if ( $resellermaxlength < 1 || $origination_rates_info->{cost} > $minimumcharge ) {
                &error_xml_without_cdr($destination,"RESELLER_COST_CHEAP");
        }
        $origination_dp_string = $origination_dp_string."||".$origination_dp_string_reseller;
        
        $session->execute("limit","db ".$carddata->{number}." db_".$carddata->{number}." ".$carddata->{maxchannels}) if($carddata->{maxchannels} > 0);  
    }

    &logger("Max Call Length: $maxlength minutes");
    &error_xml_without_cdr($destination,"NO_SUFFICIENT_FUND") if($maxlength<=0);
        
    #  Congratulations, we now have a working card,pin, and phone number.
    &say_cost( $customer_origination_rates_info );
    &say_timelimit($maxlength);
    
    &dialout($original_destination_number,$destination,$maxlength,$customer_carddata,$origination_rates_info,$origination_dp_string);

}

#Say how much the call will cost.
sub say_cost() {    
    return 1 if ( !$session->ready() );    
    my ( $numberinfo ) = @_;

    if ( $gbl_config->{calling_cards_rate_announce} == 1 ) {
    
        if ( $numberinfo->{cost} > 0 ) {
            my @call_cost = split( /\./, sprintf( "%.2f", $numberinfo->{cost} / 1) );
            $session->streamFile( $sound->{call_will_cost} );
            if ($call_cost[0] > 0) {
                $session->execute(  "say", "en number pronounced " . $call_cost[0] );
                if ($call_cost[0] == 1) {
                    $session->streamFile( $sound->{main_currency} ) ;
                } else {
                    $session->streamFile( $sound->{main_currency_plural} ) ;
                }
            }
            if ( $call_cost[1] > 0 ) {
                $session->execute(  "say", "en number pronounced "  . $call_cost[1] );
                if ($call_cost[1] == 1) {
                    $session->streamFile( $sound->{sub_currency} ) ;
                } else {
                    $session->streamFile( $sound->{sub_currency_plural} ) ;
                }
            }
            $session->streamFile( $sound->{per} );
            $session->streamFile( $sound->{minute} );
        }
        
        if ( $numberinfo->{connectcost} > 0 ) {
            $session->streamFile( $sound->{a_connect_charge} );
            my @connect_cost = split( /\./, sprintf( "%.2f", $numberinfo->{connectcost} / 1) );            
            if ($connect_cost[0] > 0) {
                $session->execute(  "say", "en number pronounced " . $connect_cost[0] );
                if ($connect_cost[0] == 1) {
                    $session->streamFile( $sound->{main_currency} ) ;
                } else {
                    $session->streamFile( $sound->{main_currency_plural} ) ;
                }
            }            
            if ( $connect_cost[1] > 0 ) {
                $session->execute(  "say", "en number pronounced "  . $connect_cost[1] );
                if ($connect_cost[1] == 1) {
                    $session->streamFile( $sound->{sub_currency} ) ;
                } else {
                    $session->streamFile( $sound->{sub_currency_plural} ) ;
                }
            }
            $session->streamFile( $sound->{will_apply} );
        }
    }
}

#Playback time limit for call
sub say_timelimit() {
    return 1 if ( !$session->ready() );
    my ( $minutes ) = @_;

    $minutes = sprintf( "%.0f", $minutes );
    
    &print_console('Minutes : '.$minutes);    
    
    if ( $minutes > 0 && $gbl_config->{calling_cards_timelimit_announce} == 1 ) {
        $session->streamFile( $sound->{call_will_last} );
        if ( $minutes == 1 ) {
            $session->execute(  "say", "en number pronounced " . $minutes );
            $session->streamFile( $sound->{minute} );
        }
        elsif ( $minutes > 1 ) {
            $session->execute(  "say", "en number pronounced " . $minutes );
            $session->streamFile( $sound->{minutes} );
        }
    }
    elsif ( $minutes < 1 ) {
        $session->streamFile( $sound->{not_enough_credit} );
        $session->streamFile( $sound->{goodbye} );
	    $session->hangup();
    }
}

#Dialout destination number 
sub dialout() {
    return 1 if ( !$session->ready() );           
    # Rig up the LCR stuff and do the outbound dialing.
    # If a call does not go through we give the user the option
    # of trying again.
            
    my($original_destination_number,$destination_number,$maxlength,$carddata,$origination_rates_info,$origination_dp_string) = @_;
		
    # Get the list of routes for the phone number.
    my @termination_rates_info = &get_termination_rates(
        destination_number => $destination_number,
        carddata => $carddata,
        origination_rates_info=>$origination_rates_info
    );

    if(@termination_rates_info)
    {
          my $count = 0;
          my $now = &now();
          
          $session->execute("export","call_processed=internal");
          $session->execute("export","callstart=$now");
          $session->execute("export","originated_destination_number=$destination_number");
          $session->execute("export","effective_destination_number=$original_destination_number");
          $session->setVariable("continue_on_fail","true");
          $session->setVariable("hangup_after_bridge","true");
          $session->execute("export","accountid=$carddata->{id}");
          $session->execute("export","account_type=$carddata->{type}");
          $session->execute("export","resellerid=$carddata->{reseller_id}");        
          $session->execute("export","accountcode=$carddata->{number}");
          $session->execute("export","call_direction=outbound");    
          $session->execute("export","calltype=CALLINGCARD");    
          $session->execute("export","origination_rates=$origination_dp_string");
          $session->execute("set", "execute_on_answer=sched_hangup +" . sprintf( "%.0f", $maxlength * 60 ) );
          $session->execute("sched_hangup","+".sprintf( "%.0f", $maxlength * 60 ). "" );
          
          #Fetch outbound callerid for accounts & If exist and active then override it
          my $outboundcallerid = &get_outbound_callerid(
                accountid=>$carddata->{id},
                table=>'accounts_callerid',
                field=>'accountid'
          );
          
          $session->execute( "export", "origination_caller_id_name=$outboundcallerid->{callerid_name}" ) if($outboundcallerid->{callerid_name});
          $session->execute( "export", "origination_caller_id_number=$outboundcallerid->{callerid_number}" ) if($outboundcallerid->{callerid_number});
          
          foreach my $termination_rate (@termination_rates_info) {
          
	          &logger("$termination_rate->{name}: cost Termination Rate : $termination_rate->{cost} \t Origination Rate : $origination_rates_info->{cost} \t Code : $termination_rate->{pattern}" );
	          
	          if ( $termination_rate->{cost} > $origination_rates_info->{cost} ) {
		          &logger("$termination_rate->{name}: $termination_rate->{cost} > $origination_rates_info->{cost}, skipping");
	          }
	          else {		          			          
	              
	              my $termination_dp_string = "ID:$termination_rate->{outbound_route_id}|CODE:$termination_rate->{pattern}|DESTINATION:$termination_rate->{comment}|CONNECTIONCOST:$termination_rate->{connectcost}|INCLUDEDSECONDS:$termination_rate->{includedseconds}|COST:$termination_rate->{cost}|INC:$termination_rate->{inc}|TRUNK:$termination_rate->{trunk_id}|PROVIDER:$termination_rate->{provider_id}";
	                				  
		          
		          if ($termination_rate->{dialed_modify} && $termination_rate->{dialed_modify} ne "") {
                    $destination_number = &number_translation('destination_number'=>$destination_number,'translation'=>$termination_rate->{dialed_modify});
	              }
	
		          $session->execute("export","termination_rates=$termination_dp_string");
		          #$session->execute("export","effective_destination_number=$destination_number");
		          $session->execute("limit","db ".$termination_rate->{path}." gw_".$termination_rate->{path}." ".$termination_rate->{maxchannels}) if($termination_rate->{maxchannels} > 0);
	              
	              $session->execute("set","absolute_codec_string=$termination_rate->{codec}") if($termination_rate->{codec} ne '');
	              $session->execute("bridge", "sofia/gateway/" . $termination_rate->{path} . "/" . $termination_rate->{prepend} . $destination_number );
		              
            	$session->execute("bridge", "sofia/gateway/" . $termination_rate->{path2} . "/" . $termination_rate->{prepend} . $destination_number) if ($termination_rate->{path2} ne $termination_rate->{path});
	          }			
          }		                      		
    }else{
        &error_xml_without_cdr($destination_number,"TERMINATION_RATES_NOT_FOUND");
    }        
}

#IVR playback and option selection
sub playback_ivr()
{
    my ($carddata) = @_;
    my $retries = 0;
    
    $gbl_config->{'ivr_count'} = 2;
    while ($retries < $gbl_config->{'ivr_count'}) {
        #IVR
        my $result = $session->playAndGetDigits(
	                  1,1,2,
	                  $gbl_config->{calling_cards_number_input_timeout},
	                  "#",
	                  "$sound->{astpp_callingcard_menu}",
	                  "",
	                  '^[1-3]+$'
        );
        $retries++;
        if ($result eq '1')
        {
            $carddata = &get_account(field=>"id",value=>$carddata->{'id'});
            &process_destination($carddata,"");
        }elsif($result eq '2'){
            $gbl_config->{calling_cards_rate_announce} = 1;
            &say_balance($carddata);
        }elsif($result eq '3'){
            $session->streamFile( $sound->{goodbye} );
    	    $session->hangup();
        }
    }
}


#Map sound files to variable names.
sub define_sounds() {
	my ($location) = @_;
	$location = "" if !$location;
	my $sound;
	
    $sound->{cardnumber} = $location . "astpp-accountnum.wav" ;    #Please enter your card number followed by pound.
    $sound->{cardnumber_incorrect} = $location .  "astpp-badaccount.wav";    #Incorrect card number.
    $sound->{pin} = $location . "astpp-pleasepin.wav";    #Please enter your pin followed by pound.
    $sound->{pin_incorrect} = $location . "astpp-invalidpin.wav";    #Incorrect pin.
    $sound->{goodbye}       = $location . "astpp-goodbye.wav";          #Goodbye.
    $sound->{destination}   = $location . "astpp-phonenum.wav"; #Please enter the number you wish to dial followed by pound.
    $sound->{destination_incorrect} = $location . "astpp-badphone.wav";    #Phone number not found!
    $sound->{call_will_cost} = $location . "astpp-willcost.wav"; #This call will cost:
    $sound->{main_currency}  = $location . "astpp-dollar.wav";   #Dollar
    $sound->{sub_currency}   = $location . "astpp-cent.wav";     #Cent
    $sound->{main_currency_plural}     = $location . "astpp-dollars.wav";       #Dollars
    $sound->{sub_currency_plural}      = $location . "astpp-cents.wav";         #cents
    $sound->{per}                      = $location . "astpp-per.wav";           #per
    $sound->{minute}                   = $location . "astpp-minute.wav";        #Minute
    # $sound->{minutes}                  = $location . "astpp-minutes.wav";       #Minutes
    $sound->{minutes}                  = $location . "minutes.wav";       #Minutes
    $sound->{second}                   = $location . "astpp-second.wav";        #Second
    $sound->{seconds}                  = $location . "astpp-seconds.wav";       #Seconds
    $sound->{a_connect_charge}         = $location . "astpp-connectcharge.wav"; #A connect charge of
    $sound->{will_apply}               = $location . "astpp-willapply.wav";     #Will apply
    $sound->{card_is_empty}       = $location . "astpp-card-is-empty.wav";    #This card is empty.
    $sound->{card_has_balance_of} = $location . "astpp-this-card-has-a-balance-of.wav";    #Card has a balance of:
    $sound->{card_has_expired} = $location . "astpp-card-has-expired.wav";   #This card has expired.
    $sound->{call_will_last}   = $location . "astpp-this-call-will-last.wav"; #This call will last:
    $sound->{not_enough_credit} = $location . "astpp-not-enough-credit.wav";    #You do not have enough credit
    $sound->{astpp_callingcard_menu} = $location . "astpp-callingcard-menu.wav"; # #Press one if you wish to place another call, press 2 for your card balance, or press 3 to hangup
    $sound->{badnumber} = $location . "astpp-badnumber.wav";          # "Calls from this location are blocked!"
    $sound->{point} = $location .  "astpp-point.wav";    #point.
    $sound->{register_ani}  = $location . "astpp-register.wav";    # "Register ANI to this card? Press 1 for yes or any other key for no."
    $sound->{card_has_expired} = $location .  "astpp_expired.wav";    #"This card has expired"
    $sound->{card_is_empty}    = $location . "astpp-card-is-empty.wav";   #This card is empty
    return $sound;	
}
1;
