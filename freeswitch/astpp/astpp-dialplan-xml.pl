#!/usr/bin/perl
#
# ASTPP - Open Source VoIP Billing
#
# Copyright (C) 2004/2013 www.astpp.org
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# Terms of the GNU General Public License version 2.
############################################################

sub xml_process()
{
    my ($params,$ASTPP, $config) = @_;
    my ($didinfo,$astppdid,$ipinfo,$callstatus,$maxlength,$void_xml,$xml,$auth_type,$cust_pricelist_id,$accountname);
    
    $xml = header( -type => 'text/plain' );
    $void_xml = &void_xml();
    
    $astppdid = "ASTPP-STANDARD";  
    #Check if dialed number is DID number.
    $didinfo = &get_did($astpp_db, $params->{'Caller-Destination-Number'});
    if ($didinfo->{did_number}) {
	$astppdid = "ASTPP-DID";
        $ASTPP->debug( debug => "This is a call for a DID: ");
	$params->{variable_accountcode} = $didinfo->{account_code};
    }
            
    #IF opensips then check then get account code from $params->{'variable_sip_h_P-Accountcode'}
    if($config->{opensips}=='1' && $params->{'variable_sip_h_P-Accountcode'} ne '' && $params->{'variable_sip_h_P-Accountcode'} ne '<null>' && $params->{variable_accountcode} eq '')
    {
	$params->{'variable_accountcode'} = $params->{'variable_sip_h_P-Accountcode'};
	$params->{'pricelist_id'} = $params->{'variable_sip_h_P-Pricelist_id'};
    }        
    
    #set Default account name
    $accountname = "default";
    
    #If accountcode not found then check for ip address authentication.
    if ( !$params->{variable_accountcode} ) {
      
	##IF opensips then check then get ip address from $params->{'variable_sip_h_X-AUTH-IP'}
	if($config->{opensips}=='1' && $params->{'variable_sip_h_X-AUTH-IP'} ne '')
	{
	    $params->{'Hunt-Network-Addr'} = $params->{'variable_sip_h_X-AUTH-IP'};
	    $accountname = $params->{'Hunt-Username'};
	}
	
        $ipinfo = $ASTPP->ip_address_authenticate(
            ip_address  => $params->{'Hunt-Network-Addr'},
            destination => $params->{'Caller-Destination-Number'}
        );
	
        if (defined $ipinfo->{account_code} && $ipinfo->{account_code} ne "") {
            $params->{variable_accountcode} = $ipinfo->{account_code};            
	    $params->{'Caller-Destination-Number'} = substr($params->{'Caller-Destination-Number'},length($ipinfo->{prefix}));
	    $auth_type = "ip_auth";
	    $accountname = $ipinfo->{name};
        }        
    }
    
    # Build dialplan header
    $xml = $ASTPP->fs_dialplan_xml_header(
        xml                => $xml,
        destination_number => $params->{'Caller-Destination-Number'},
	context 	   => $params->{'Caller-Context'}
    );

    # If accountcode not found from above any feature then customer is not authenticated to use our system.
    if ( !$params->{variable_accountcode} )
    {              
        $ASTPP->debug( debug => "CANNOT RETRIEVE CARD" );
    	$ASTPP->debug( debug => $void_xml );	
	&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);		
	print $void_xml;
        exit(0);
    }
    
    # Fetch all the account info from the db.
    my $carddata = &get_account( $astpp_db, $params->{variable_accountcode} );            
    
    #Finalize which pricelist we need to use for call for customer
    if(defined $astppdid && ($astppdid ne "ASTPP-DID"))
    {
	if($auth_type ne '' && $auth_type=='ip_auth' && $ipinfo->{pricelist_id} > 0)
	{	    
	      $carddata->{'pricelist_id'} = $ipinfo->{pricelist_id};
	}
	elsif($params->{variable_sip_device_pricelist_id} > 0)
	{
	    $carddata->{'pricelist_id'} = $params->{variable_sip_device_pricelist_id};
	    $accountname = $params->{variable_user_name};
	}
	elsif($config->{opensips}=='1' && $params->{variable_accountcode} ne '' && $params->{'pricelist_id'} > 0)
	{	    
	      $carddata->{'pricelist_id'} = $params->{'pricelist_id'};
	}
	$cust_pricelist_id = $carddata->{'pricelist_id'};
    }
    else
    {
	$carddata->{'pricelist_id'} = &get_pricelist_by_name($astpp_db,$config->{default_brand});
    }
    
    $ASTPP->debug( debug => "Account code :: $carddata->{number}, Pricelist Id  :: $carddata->{'pricelist_id'}, Max channels :: $carddata->{maxchannels}, Inuse :: $carddata->{inuse}, Account : $accountname, Called number :: $params->{'Caller-Destination-Number'}" );
    
    # If dialed number is not DID number, then check for blocked prefix. If Blocked then dont allow to make call.
    if(defined $astppdid && ($astppdid ne "ASTPP-DID"))
    {
      my $blocked_call = &search_for_block_prefixes($astpp_db,$params->{'Caller-Destination-Number'},$carddata->{id});
	if(scalar($blocked_call) > 0){
	  $ASTPP->debug( debug => "CALLSTATUS 2" );
	  $ASTPP->debug( debug => "Blocked Prefixes" );
	  $xml .= "<action application=\"hangup\" data=\"Blocked Prefixes\"/>\n";
	  $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	  $ASTPP->debug( debug => "Returning nothing so dialplan can continue." );
	  $ASTPP->debug( debug => $void_xml );
	  &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	  print $void_xml;
	  exit(0);
	}  
    }   
    
    #If dialed number is not DID number, then calculate in use count for account
    if(!defined $astppdid && $astppdid ne "ASTPP-DID")
    {
	if($carddata->{maxchannels} > 0 && $carddata->{inuse} > $carddata->{maxchannels})
	{
	    $ASTPP->debug( debug => "ACCOUNT MAX CALL CHANNEL LIMIT EXECED" );
	    $xml .= "<action application=\"hangup\" data=\"ACCOUNT MAX CALL CHANNEL LIMIT EXECED\"/>\n";
	    $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	    $ASTPP->debug( debug => "Returning nothing so dialplan can continue." );
	    $ASTPP->debug( debug => $void_xml );
	    &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	    print $void_xml;
	    exit(0);
	}
	&update_inuse($astpp_db,$params->{variable_accountcode},'accounts','+1','+'.$config->{min_channel_balance});
	$carddata->{balance} += $config->{min_channel_balance};
    }    
    
    #If dialed number is DID number, then calculate in use count for DID
    if(defined $astppdid && $astppdid eq "ASTPP-DID")
    {
	if($didinfo->{maxchannels} > 0 && $didinfo->{inuse} > $didinfo->{maxchannels})
	{
	    $ASTPP->debug( debug => "DID MAX CALL CHANNEL LIMIT EXECED" );
	    $xml .= "<action application=\"hangup\" data=\"DID MAX CALL CHANNEL LIMIT EXECED\"/>\n";
	    $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	    $ASTPP->debug( debug => "Returning nothing so dialplan can continue." );
	    $ASTPP->debug( debug => $void_xml );
	    &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	    print $void_xml;
	    exit(0);
	}
	&update_inuse($astpp_db,$params->{'Caller-Destination-Number'},'dids','+1');
    }

    
    # IF dialed number is not DID and customer have value in dialed modify field then do replacement of prefixes 
    if ( $carddata->{dialed_modify} && (defined $astppdid && $astppdid ne "ASTPP-DID")) {
        my @regexs = split( m/,/m, $carddata->{dialed_modify} );	
        foreach my $regex (@regexs) {	    
            $regex =~ s/"//g;    #Strip off quotation marks
            my ( $grab, $replace ) = split( m!/!i, $regex );
            # This will split the variable into a "grab" and "replace" as needed
            $ASTPP->debug( debug => "Grab: $grab" );
            $ASTPP->debug( debug => "Replacement: $replace" );
            $ASTPP->debug( debug => "Phone Before: $params->{'Caller-Destination-Number'}" );
            $params->{'Caller-Destination-Number'} =~ s/$grab/$replace/is;
            $ASTPP->debug( debug => "Phone After: $params->{'Caller-Destination-Number'}" );
        }
    }
    
    $ASTPP->debug( debug => "FINDING LIMIT FOR: " . $carddata->{number} );
    
    #Fetch status of length of call and orgination route info
    ( $callstatus, $maxlength,$pricelistinfo,$routeinfo ) = &max_length( $astpp_db, $config, $carddata,$params->{'Caller-Destination-Number'} );    

    $ASTPP->debug( debug => "Cost: " . $routeinfo->{cost} );
    $ASTPP->debug( debug => "Pricelist: " . $routeinfo->{pricelist} );
    
    my $minimumcharge = $routeinfo->{cost};
    my @reseller_list;    
    my @inc_reseller_list;
    $ASTPP->debug( debug => "CALLSTATUS: $callstatus MAX_LENGTH: $maxlength" );

    #If Max lenght of call less or eqaual zero, then hangup call  
    if ($maxlength <= 0 )  
    {	
	if($astppdid ne "ASTPP-DID")
	{
	  &update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
	}else{
	  &update_inuse($astpp_db,$params->{'Caller-Destination-Number'},'dids','-1');
	}
        $ASTPP->debug( debug => "COULD NOT FIND ROUTE.  EXITING SO DIALPLAN CAN TAKE OVER" );
    	$ASTPP->debug( debug => $void_xml );
	&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	print $void_xml;
	exit(0);
    }
    
    #If customer has any reseller then process for reseller balance and route info.
    # Samir Doshi - To Resolve callerid issue
    my $cust_accountid = $carddata->{id};
    
    while ( $carddata->{reseller_id} && $maxlength > 0 && $callstatus == 1 ) {
        $ASTPP->debug( debug => "FINDING LIMIT FOR: $carddata->{reseller_id}" );
        $carddata = &get_account( $astpp_db, $carddata->{reseller_id} );
	if(defined $astppdid && ($astppdid eq "ASTPP-DID"))
	{
	    $carddata->{'pricelist_id'} = &get_pricelist_by_name($astpp_db,$carddata->{number});
	}        
	push @reseller_list, $carddata->{id};
	
        $ASTPP->debug( debug =>	"ADDING $carddata->{number} to the list of resellers for this account");
		
	#Calculating in use count for account 
	if(defined $astppdid && $astppdid ne "ASTPP-DID")
	{
	    if($carddata->{maxchannels} > 0 && $carddata->{inuse} > $carddata->{maxchannels})
	    {	    
		foreach my $inc_reseller (@inc_reseller_list) {
		    &update_inuse($astpp_db,$inc_reseller,'accounts','-1','-'.$config->{min_channel_balance});
		}
		&update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
		$ASTPP->debug( debug => "RESELLER : ACCOUNT MAX CALL CHANNEL LIMIT EXECED" );
		$xml .= "<action application=\"hangup\" data=\"ACCOUNT MAX CALL CHANNEL LIMIT EXECED\"/>\n";
		$xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
		$ASTPP->debug( debug => "Returning nothing so dialplan can continue." );
		$ASTPP->debug( debug => $void_xml );
		&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
		print $void_xml;
		exit(0);
	    }
	    &update_inuse($astpp_db,$carddata->{number},'accounts','+1','+'.$config->{min_channel_balance});
	    push @inc_reseller_list,$carddata->{number};
	    $carddata->{balance} += $config->{min_channel_balance};
	}
	
	#Fetch status of length of call and orgination route info
        my ( $resellercallstatus, $resellermaxlength,$pricelistinfo,$routeinfo ) =  &max_length( $astpp_db, $config, $carddata, $params->{'Caller-Destination-Number'} );        
	
	if ($resellermaxlength <= 0 )  
	{
	    if($astppdid ne "ASTPP-DID")
	    {
	      foreach my $inc_reseller (@inc_reseller_list) {
		  &update_inuse($astpp_db,$inc_reseller,'accounts','-1','-'.$config->{min_channel_balance});
	      }
	      &update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
	    }
	    $ASTPP->debug( debug => "COULD NOT FIND ROUTE.  EXITING SO DIALPLAN CAN TAKE OVER" );
	    $ASTPP->debug( debug => $void_xml );
	    &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	    print $void_xml;
	    exit(0);
	}
	
        if ( $resellercallstatus != 1 ) {
            $carddata->{reseller_id} = "";
            $callstatus = $resellercallstatus;
        }
        elsif ( $resellermaxlength < $maxlength ) {
            $maxlength = $resellermaxlength;
        }
        $ASTPP->debug( debug =>"Reseller cost = $routeinfo->{cost} and minimum charge is $minimumcharge and reseller max length = $resellermaxlength" );
	
	#If customer call cost is lesser than reseller call cost, then hangup call. 
        if ( $resellermaxlength < 1 || $routeinfo->{cost} > $minimumcharge ) {
	    if($astppdid ne "ASTPP-DID")
	    {
		foreach my $inc_reseller (@inc_reseller_list) {
		    &update_inuse($astpp_db,$inc_reseller,'accounts','-1','-'.$config->{min_channel_balance});
		}	    
		&update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
	    }
            $ASTPP->debug( debug =>"Reseller call is priced too cheap!  Call being barred!" );
            $xml .="<action application=\"hangup\" data=\"Reseller call is priced too cheap!  Call being barred!\"/>\n";
            $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	    &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
            print $xml;
            exit(0);
        }
        
        $ASTPP->debug( debug => "RESELLER Max Length: $resellermaxlength" );
        $ASTPP->debug( debug => "RESELLER Call Status: $resellercallstatus" );	
    }

    #Print list of resellers.
    if ( $config->{debug} == 1 ) {
        $ASTPP->debug( debug => "PRINTING LIST OF RESELLERS FOR THIS ACCOUNT" );
        foreach my $reseller (@reseller_list) {
	    $ASTPP->debug( debug => "RESELLER: $reseller" );            
        }
    }

    $ASTPP->debug(debug => "Max Call Length: $maxlength minutes");
    $ASTPP->debug(debug => "Call Status: $callstatus");

    #If Max lenght of call less or eqaual zero, then hangup call    
    if ( $maxlength <= 0 ) {
	if($astppdid ne "ASTPP-DID")
	{
	    foreach my $inc_reseller (@inc_reseller_list) {
		&update_inuse($astpp_db,$inc_reseller,'accounts','-1','-'.$config->{min_channel_balance});
	    }
	    &update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
	}
        $ASTPP->debug( debug => "NOT ENOUGH CREDIT" );
        $xml .= "<action application=\"hangup\" data=\"NOT ENOUGH CREDIT\"/>\n";
        $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
	&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
        print $xml;
        exit(0);
    } 
    
    #If duration of call greater than defined in config then set it to config max call limit.
    elsif ($config->{call_max_length} && $maxlength > $config->{call_max_length} / 1000){
	$maxlength = $config->{call_max_length} / 1000;
    }
    
    $ASTPP->debug(debug => "Max Call Length: $maxlength minutes");

    #Set limit of max call length for outbound call.  
    $xml = $ASTPP->fs_dialplan_xml_timelimit(
        xml        => $xml,
        max_length => $maxlength,
	accountcode => $params->{variable_accountcode}
    );     

    if (defined $didinfo->{did_number} && $didinfo->{did_number} ) {
	      my ($returned_data) = $ASTPP->fs_dialplan_xml_did(
		      did=> $params->{'Caller-Destination-Number'},
		      variables => $didinfo->{variables},
		      extensions => $didinfo->{extensions},
		      call_type => $didinfo->{call_type}
	      );	      
	      $xml .= "<action application=\"export\" data=\"calltype=DID\"/>\n";
	      $xml .= $returned_data;
	} else {
		$xml .= "<action application=\"export\" data=\"calltype=STANDARD\"/>\n";
		$xml .= "<action application=\"set\" data=\"pricelist_id=".$cust_pricelist_id."\"/>\n";
		$xml .= "<action application=\"set\" data=\"accountname=".$accountname."\"/>\n";
		
		# Get the list of routes for the phone number.
		my @outboundroutes = &get_outbound_routes( $astpp_db, $params->{'Caller-Destination-Number'},$carddata, $routeinfo, @reseller_list );		
		if(@outboundroutes)
		{
		      my $count = 0;
		      my $tmpbrd;
		      foreach my $route (@outboundroutes) {
			      $ASTPP->debug( debug => "$route->{name}: cost Termination Rate : $route->{cost} \t Origination Rate : $routeinfo->{cost} \t $route->{pattern}" );
			      if ( $route->{cost} > $routeinfo->{cost} ) {
				      $ASTPP->debug( debug => "$route->{name}: $route->{cost} > $routeinfo->{cost}, skipping" );
			      }
			      else {				  
				      $tmpbrd .= $ASTPP->fs_dialplan_xml_bridge(
					      destination_number => $params->{'Caller-Destination-Number'},
					      route_prepend      => $route->{prepend},
					      route_id	   => $route->{outbound_route_id},
					      count => $count,
					      trunk_id => $route->{trunk_id},
					      trunk_name => $route->{name},
					      trunk_tech => $route->{tech},
					      trunk_path => $route->{path},
					      trunk_provider => $route->{provider_id},
					      trunk_dialed_modify => $route->{dialed_modify},
					      trunk_maxchannels => $route->{maxchannels}
				      );				      
				      if($tmpbrd ne "")
				      {
					$count++;
				      }
			      }			
		      }		      
		      if($count == 0)
		      {
			  $ASTPP->debug( debug => "NO OUTBOUND ROUTE FOUND" );
			  $xml .= "<action application=\"hangup\" data=\"NO_ROUTE_DESTINATION\"/>\n";
		      }
		      #Fetch outbound callerid for accounts & If exist and active then override it
		      my $outboundcallerid = &get_outbound_callerid($astpp_db,$cust_accountid,'accounts_callerid','accountid');
		      my $tmpxml = $ASTPP->fs_dialplan_xml_bridge_start(
				  origination_caller_id_name => $outboundcallerid->{callerid_name},
				  origination_caller_id_number => $outboundcallerid->{callerid_number}
		      );		      
		      $xml .= $tmpxml.$tmpbrd;
		}else{
		    foreach my $inc_reseller (@inc_reseller_list) {
			  &update_inuse($astpp_db,$inc_reseller,'accounts','-1','-'.$config->{min_channel_balance});
		    }
		    &update_inuse($astpp_db,$params->{variable_accountcode},'accounts','-1','-'.$config->{min_channel_balance});
		    $ASTPP->debug( debug => "NO_ROUTE_DESTINATION" );
		    $xml .= "<action application=\"hangup\" data=\"NO_ROUTE_DESTINATION\"/>\n";
		    $xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml );
		    &disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
		    print $xml;
		    exit(0);
		}
	}
	$xml = $ASTPP->fs_dialplan_xml_footer( xml => $xml);
	$ASTPP->debug( debug => $xml );
	&disconnect_db($astpp_db,$cdr_db,$freeswitch_db);
	print $xml;
# 	exit;
}
1;