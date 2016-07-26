-------------------------------------------------------------------------------------
-- ASTPP - Open Source VoIP Billing Solution
--
-- Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
-- Samir Doshi <samir.doshi@inextrix.com>
-- ASTPP Version 3.0 and above
-- License https://www.gnu.org/licenses/agpl-3.0.html
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as
-- published by the Free Software Foundation, either version 3 of the
-- License, or (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
-- 
-- You should have received a copy of the GNU Affero General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------------------

-- Authenticate calling card 
function auth_callingcard()

	  local ani_flag = 0
	    local cardnum = -1
	    local cardinfo
	    local pin=""
	    local carddata
	    local aniinfo
	if(config['cc_ani_auth'] == "1")  then 
		local ani_number = session:getVariable("caller_id_number")
		Logger.debug("[Dialplan] Callerid authentication:" .. ani_number)

		aniinfo = get_ani(ani_number);

		if(aniinfo ~= nil) then

		  cardinfo = get_account(aniinfo['accountid'])       
		  cardnum = cardinfo['number'];
		  
		  Logger.debug("[Dialplan] Authenticated account:" .. cardinfo['number'])

		  local card_flag = validate_card_usage(cardinfo);
		  if (card_flag ~= nil)then
		      error_xml_without_cdr("","ACCOUNT_EXPIRE")
		      session:hangup();
		  end
		  ani_flag = 1
		end 
	end

	if (ani_flag == 0) then

		if(cardnum == -1) then
			local retries = "0"
	        	local authenticated = 0
			Logger.debug("[Dialplan] ANI INFORMATION :" .. ani_flag)
			while (tonumber(authenticated) ~= 1 and tonumber(retries) < tonumber(config['card_retries'])) do
				cardnum = session:playAndGetDigits(1, 15, 1, config['calling_cards_number_input_timeout'], "#", "astpp-accountnum.wav", "", "^[0-9]+$")
				Logger.debug("[Dialplan] Got DTMF digits: ".. cardnum )
			     if (cardnum ~= "") then		    
		        		cardinfo = get_account(cardnum);       
		    	     end
			     if(cardinfo == nil) then
					session:streamFile("astpp-badaccount.wav")      
	    		     else 
					authenticated = 1
					pin = cardinfo['pin']
		    	     end
	    		     retries = retries + 1
			end
			 if ( tonumber(authenticated) ~= 1 and tonumber(retries) == tonumber(config['card_retries']) ) then
				session:streamFile("astpp-goodbye.wav")      
				session:hangup();
		        end
		end
		if ( pin ~= "" ) then
			local retries = 0;
			local authenticated = 0;
			while (tonumber(authenticated) ~= 1 and tonumber(retries) < tonumber(config['pin_retries'])) do

				local pin_number = session:playAndGetDigits(1, 15, 1, config['calling_cards_pin_input_timeout'], "#", "astpp-pleasepin.wav", "", "^[0-9]+$")
				Logger.debug("[Dialplan] We recieved a pi: ".. pin_number )

				if ( pin_number ~= pin ) then	
						session:streamFile("astpp-invalidpin.wav")      
				else 
						authenticated = 1
				end
				retries = retries + 1
			end

			if ( tonumber(authenticated) ~= 1 and tonumber(retries) == tonumber(config['pin_retries']) ) then
				session:streamFile("astpp-invalidpin")      
				session:hangup();
			end

		end 

	      -- Validate customer
	      local card_flag = validate_card_usage(cardinfo);
	      if (card_flag) then	      
		    error_xml_without_cdr("","ACCOUNT_EXPIRE")
		    session:hangup();
	      end

	      --Ask for save callerid for pinless authentication
	      save_ani(cardinfo)
	end 

    -- Check if account have balance
    balance = get_balance(cardinfo);
	  if (balance <= 0) then
--        say_balance(cardinfo)
	    error_xml_without_cdr('CALLINGCARD',"NO_SUFFICIENT_FUND","ASTPP-CALLINGCARD",config['playback_audio_notification']) 
        session:hangup();
	  end

	return cardinfo;
end


-- To save caller id for pinless authentication
function save_ani(cardinfo)
	local result = session:playAndGetDigits(1, 1, 1, 5000, "#", "astpp-register.wav", "", "^[1-1]+$")

	if(tonumber(result) == 1) then
		local query = "INSERT INTO ani_map (number,accountid,status) VALUES ('"..session:getVariable("caller_id_number").."','"..cardinfo['id'].."',0)";
		Logger.debug("[Functions] [SAVE_ANI] Query :" .. query)
		dbh:query(query);
	end
end

function get_account(accountcode)
	local query = "SELECT * FROM "..TBL_USERS.." WHERE (number = \""..accountcode.."\" OR id=\""..accountcode.."\") AND status=0 AND deleted=0 limit 1";
	Logger.debug("[Functions] [DOAUTHORIZATION] Query :" .. query)

	local userinfo;
	assert (dbh:query(query, function(u)
		userinfo = u;	
	end))
	return userinfo;
end



-- Check DID info 
function get_ani(ani_number)
    
    local query = "SELECT * FROM ani_map WHERE number = ".. ani_number .." AND status=0"

    Logger.debug("[Functions] [CHECK_DID] Query :" .. query)
   
    assert (dbh:query(query, function(u)
	ani = u;
    end))
    return ani;
end

--check if account is active or expire. 
function validate_card_usage(carddata) 

    -- Check a few things before saying the card is ok.

    -- Now the card is in use and nobody else can use it.
    if ( carddata['first_used'] == "0000-00-00 00:00:00") then
        -- If "firstused" has not been set, we will set it now.
           local query = "UPDATE accounts SET first_used = 'now()' WHERE id = " .. carddata['id'];
            dbh:query(query);
        if ( tonumber(carddata['validfordays']) > 0 ) then
            -- Check if the card is set to expire and deal with that as appropriate.            
            local query = "UPDATE accounts SET expiry = DATE_ADD('$now', INTERVAL " .. carddata['validfordays'].." day) WHERE id = "..carddata['id']
	        dbh:query(query);
            return 0;
        end
    
    elseif ( tonumber(carddata['validfordays']) > 0 ) then
        
      	--carddata['expiry'] = $gbl_astpp_db->selectall_arrayref("SELECT DATE_FORMAT('$arg{carddata}->{expiry}' , '\%Y\%m\%d\%H\%i\%s')")->[0][0];
        --$now = $gbl_astpp_db->selectall_arrayref("SELECT DATE_FORMAT('$now' , '\%Y\%m\%d\%H\%i\%s')")->[0][0];
        --if($now >= $arg{carddata}->{expiry}) then
        --    local query = "UPDATE accounts SET status = 1 WHERE id = " .. carddata['id'];
    	--    &remove_ani($arg{carddata});
        --    return 1;
        --end
     
	elseif ( tonumber(carddata['validfordays']) < 0) then
       		 return 1;
   	 end
end	

--Calculate balance 
function get_balance(cardinfo)
	local balance = ((cardinfo['credit_limit'] * cardinfo['posttoexternal']) + cardinfo['balance'])
	return balance
end

--IVR playback and option selection
function playback_ivr(userinfo)   
    local retries = 0;
    
    config['ivr_count'] = 3;

		while (tonumber(retries) < tonumber(config['ivr_count'])) do
			result = session:playAndGetDigits(1, 1, 2, config['calling_cards_number_input_timeout'], "#", "astpp-callingcard-menu.wav", "", "^[1-3]+$")
		    Logger.debug("[Dialplan] Got DTMF digits: "..result .."retries:".. retries )
		     retries = retries + 1
		     if (tonumber(result) == 1) then

	    		userinfo = get_account(userinfo['id']);     
				process_destination(userinfo);  
    	     elseif (tonumber(result) == 2) then

				config['calling_cards_balance_announce'] = 1;
				say_balance(userinfo,config); 
				retries = retries - 1   
		     elseif (tonumber(result) == 3) then
				session:streamFile( "astpp-goodbye.wav" );
	        	session:hangup();
    	     end	    		     
		end
end

-- Play balance of account   --Calculate and say the card balance.
function say_balance(cardinfo)
    local balance = get_balance(cardinfo)  
    if ( balance > 0 ) then
       		local balance_value = balance

		if (tonumber(config['calling_cards_balance_announce']) == 1) then

		        local first, last = string.match(tostring(balance_value/1), "([^.]+)%.(.+)")

		    session:streamFile( "astpp-this-card-has-a-balance-of.wav" )
		    if ( first == 1 ) then
		        session:execute( "say", "en number pronounced " ..first );
		        session:streamFile( "astpp-dollar.wav" );
		    
		    elseif ( first ~= 1 ) then
		        session:execute(  "say", "en number pronounced " ..first);
		        session:streamFile( "astpp-dollars.wav" );
		    end
		    if ( last == 1 ) then
		        session:execute(  "say", "en number pronounced " ..last );
		        session:streamFile("astpp-cent.wav" );
		    elseif ( last ~= 1 ) then
		        session:execute(  "say", "en number pronounced " .. last );
		        session:streamFile( "astpp-cents.wav" );
		    end
	       
	    	end
	
	else     
		 session:streamFile( "astpp-card-is-empty.wav" )
		 session:streamFile( "astpp-goodbye.wav" )
	  	 session:hangup()
	  	 return 1
	end
    return balance
end

--Say how much the call will cost.
function say_cost(numberinfo)    

    if ( tonumber(config['calling_cards_rate_announce']) == 2 ) then
        
        if ( numberinfo['cost'] > "0" ) then
            --local number, decimal = string.match(tostring(numberinfo['cost']/1), "([^.]+)%.(.+)")
            local number, decimal=tostring(numberinfo['cost']):match"([^.]*).(.*)"
            Logger.debug("[Functions] [SAY_COST] COST "..tonumber(minutes));    
            session:streamFile( "astpp-willcost.wav" );
            if (tonumber(number) > 0) then
                session:execute(  "say", "en number pronounced " .. number );
                if (number == 1) then
                    session:streamFile( "astpp-dollar.wav" ) ;
                else 
                    session:streamFile( "astpp-dollars.wav" ) ;
                end
            end
            if ( tonumber(decimal) > 0 ) then
                session:execute(  "say", "en number pronounced "  .. decimal );
                if (decimal == 1) then
                    session:streamFile( "astpp-cent.wav" ) ;
                else 
                    session:streamFile( "astpp-cents.wav" ) ;
                end
            end
            session:streamFile( "astpp-per.wav" );
            session:streamFile( "astpp-minutes.wav" );
        end
        
        if ( tonumber(numberinfo['connectcost']) > 0 ) then
            session:streamFile( "astpp-connectcharge.wav" );

		    local connectcost, connectcostdecimal = string.match(tostring(numberinfo['connectcost']/1), "([^.]+)%.(.+)")          
		    if (connectcost ~= nil and connectcost ~= 0) then
		        session:execute(  "say", "en number pronounced " .. connectcost );
		        if (connectcost == 1) then
		            session:streamFile( "astpp-dollar.wav" ) ;
		         else 
		            session:streamFile( "astpp-dollars.wav" ) ;
		        end
		    end            
		    if ( connectcostdecimal > 0 ) then
		        session:streamFile(  "say", "en number pronounced "  ..connectcostdecimal );
		        if (connectcostdecimal == 1) then
		            session:streamFile( "astpp-cent.wav" ) ;
		         else 
		            session:streamFile( "astpp-cents.wav" ) ;
		        end
		    end
	   
            session:streamFile( "astpp-willapply.wav" );
        end
    end
end

--Playback time limit for call
function say_timelimit(minutes) 
	if ( session:ready() ) then
	
	   Logger.debug("[Functions] [SAY_TIMELIMIT] MINUTES "..tonumber(minutes));    
	    
	    if ( tonumber(minutes) > 0 and tonumber(config['calling_cards_timelimit_announce']) == 0 ) then
		session:streamFile( "astpp-this-call-will-last.wav" );
		if ( minutes == 1 ) then
		    session:execute(  "say", "en number pronounced " .. minutes );
		    session:streamFile( "astpp-minute.wav" );
		
		elseif ( minutes > 1 ) then
		    session:execute(  "say", "en number pronounced " .. minutes );
		    session:streamFile( "astpp-minutes.wav" );
		end
	    
	    elseif ( minutes < 1 ) then
		session:streamFile( "astpp-not-enough-credit.wav" );
		session:streamFile( "astpp-goodbye.wav" );
		session:hangup();
	    end
	end 
end


-- To termination call to destination. Have all termination calculation inside this function.
function dialout( original_destination_number, destination_number, maxlength, userinfo, user_rates , origination_dp_string ,number_loop_str)
	if ( session:ready() ) then
		carrier_rates = get_carrier_rates (destination_number,number_loop_str,userinfo['pricelist_id'],user_rates['trunk_id'],user_rates['routing_type'])
		if (carrier_rates ~= nil) then
		    local i = 1
		    local carrier_array = {}
		    local xml_carrier_rates
		    for carrier_key,carrier_value in pairs(carrier_rates) do
			if ( carrier_value['cost'] > user_rates['cost'] ) then		    
			    Logger.notice(carrier_value['path']..": "..carrier_value['cost'] .." > "..user_rates['cost']..", skipping")  
			else
			    Logger.info("=============== Carrier Rates Information ===================")
			    Logger.info("ID : "..carrier_value['outbound_route_id'])  
			    Logger.info("Code : "..carrier_value['pattern'])  
			    Logger.info("Destination : "..carrier_value['comment'])  
			    Logger.info("Connectcost : "..carrier_value['connectcost'])  
			    Logger.info("Free Seconds : "..carrier_value['includedseconds'])  
			    Logger.info("Prefix : "..carrier_value['pattern'])      		    
			    Logger.info("Strip : "..carrier_value['strip'])      		  
			    Logger.info("Carrier id : "..carrier_value['trunk_id'])  		      		    
			    Logger.info("carrier_name : "..carrier_value['path'])      
			    Logger.info("Failover gateway : "..carrier_value['path1'])      		    
			    Logger.info("Vendor id : "..carrier_value['provider_id'])      		    		    
			    Logger.info("Number Translation : "..carrier_value['dialed_modify'])      		    		    		    
			    Logger.info("Max channels : "..carrier_value['maxchannels'])      		    		    		    		    
			    Logger.info("=================================================================")
			    carrier_array[i] = carrier_value
			    i = i+1
			end
	   	    end
		    -- If we get any valid carrier rates then build dialplan for outbound call
		    if (i > 1) then
		        local callstart = os.date("!%Y-%m-%d %H:%M:%S")    
				session:execute("export","call_processed=internal");
				session:execute("export","callstart="..callstart);
				session:execute("export","originated_destination_number="..destination_number);
				session:execute("export","effective_destination_number="..original_destination_number);
				session:execute("set","continue_on_fail=true");
				session:execute("set","hangup_after_bridge=true");
				session:execute("export","account_id="..userinfo['id']);
				session:execute("export","account_type="..userinfo['type']);
				session:execute("export","resellerid="..userinfo['reseller_id']);        
				session:execute("export","accountcode="..userinfo['number']);
				session:execute("export","call_direction=outbound");    
				session:execute("export","calltype=CALLINGCARD");    
				session:execute("export","origination_rates="..origination_dp_string);
				session:execute("set", "execute_on_answer=sched_hangup +" .. (maxlength * 60 ) );
                session:execute("set", "process_cdr=true" );
				session:execute("sched_hangup","+"..(maxlength * 60 ).. "" );
	    		   		    		    		
		    	calleridinfo = get_override_callerid(userinfo)
		    	if (calleridinfo ~= nil) then
				    if (calleridinfo['cid_name'] ~= '')  then
					    session:execute("set", "origination_caller_id_name="..calleridinfo['cid_name']);    
				    end
				    if (calleridinfo['cid_number'] ~= '')  then
				    	session:execute("set", "origination_caller_id_number="..calleridinfo['cid_number']);      
				    end
		    	end
		          
				for carrier_arr_key,carrier_arr_array in pairs(carrier_array) do
	
					if (carrier_arr_array['dialed_modify'] ~= '') then 
						destination_number = do_number_translation(carrier_arr_array['dialed_modify'],destination_number)
					end
				    
					if(carrier_arr_array['prepend'] ~= '' or carrier_arr_array['strip'] ~= '') then
						destination_number = do_number_translation(carrier_arr_array['strip'].."/"..carrier_arr_array['prepend'],destination_number)
					end
	
                    if ( session:ready() ) then
					xml_carrier_rates= "ID:"..carrier_arr_array['outbound_route_id'].."|CODE:"..carrier_arr_array['pattern'].."|DESTINATION:"..carrier_arr_array['comment'].."|CONNECTIONCOST:"..carrier_arr_array['connectcost'].."|INCLUDEDSECONDS:"..carrier_arr_array['includedseconds'].."|COST:"..carrier_arr_array['cost'].."|INC:"..carrier_arr_array['inc'].."|TRUNK:"..carrier_arr_array['trunk_id'].."|PROVIDER:"..carrier_arr_array['provider_id'];
					session:execute("export","termination_rates="..xml_carrier_rates);
    
					session:execute("export","carrier_id="..carrier_arr_array['trunk_id']);        
					session:execute("export","provider_id="..carrier_arr_array['provider_id']);

					if (carrier_arr_array['codec'] ~= '') then 
							session:execute("export","absolute_codec_string="..carrier_arr_array['codec']);
					end
    
					if(tonumber(carrier_arr_array['maxchannels']) > 0) then    
							session:execute("limit_execute","db "..outbound_info['path'].." gw_"..outbound_info['path'].." "..outbound_info['maxchannels'].." bridge sofia/gateway/"..outbound_info['path'].."/"..destination_number);
					else
							session:execute("bridge","sofia/gateway/"..carrier_arr_array['path'].."/"..destination_number);   
					end

					if(carrier_arr_array['path1'] ~= '' and carrier_arr_array['path1'] ~= carrier_arr_array['gateway']) then
						session:execute("bridge","sofia/gateway/"..carrier_arr_array['path1'].."/"..destination_number);   
					end

					if(carrier_arr_array['path2'] ~= '' and carrier_arr_array['path2'] ~= carrier_arr_array['gateway']) then
						session:execute("bridge","sofia/gateway/"..carrier_arr_array['path2'].."/"..destination_number);   
					end

                    end
				end

			else
		    	-- If no route found for outbound call then send no result dialplan for further process in fs
		    	Logger.notice("[Dialplan] No termination rates found...!!!");
		        error_xml_without_cdr(destination_number,"TERMINATION_RATE_NOT_FOUND","ASTPP-CALLINGCARD",config['playback_audio_notification']) 
		    	return
		    end
	       end
        end
end

-- Get destination number and findout origination rates related logic inside this function
function process_destination(userinfo)
	  
	local origination_dp_string

	local destination = session:playAndGetDigits(1, 35, 3, config['calling_cards_dial_input_timeout'], "#", "astpp-phonenum.wav", "astpp-badphone.wav", '^[0-9]+$')

	--------------------------------------- SPEED DIAL --------------------------------------
	if(string.len(destination) == 1 ) then
		Logger.info("[Dialplan] SPEED DIAL NUMBER SECTION ")
		destination_number = get_speeddial_number(destination,userinfo['id'])
		Logger.info("[Dialplan] SPEED DIAL NUMBER : "..destination)
	end

	-----------------------------------------------------------------------------------------

	Logger.debug("[Functions] [CHECK_destination] Dialed destination number :" .. destination)

	local original_destination_number = destination	
	 	--Check if destination blocked

	    local number_loop_string = number_loop(destination,'blocked_patterns')
	    local block_prefix = check_blocked_prefix(userinfo, destination,number_loop_string);
	    if( block_prefix ~= "") then
	   		--error_xml_without_cdr(destination,"DESTINATION_BLOCKED") ;
		        --return 1
	    end
	
	if(userinfo['dialed_modify'] ~= nil) then
	      destination = do_number_translation(userinfo['dialed_modify'],destination)
	end

	local  number_loop_str = number_loop(destination)

	-- Fine max length of call based on origination rates.
  	local tmp_array = get_call_maxlength(userinfo,destination,"",number_loop_str,config)
	    
	if( tmp_array == nil ) then
	    error_xml_without_cdr(destination,"ORIGNATION_RATE_NOT_FOUND","ASTPP-CALLINGCARD",config['playback_audio_notification'],userinfo['num ber']) 
	    return
	end
	
	local  maxlength = tmp_array[1]
	local  user_rates = tmp_array[2]
	local  xml_user_rates = tmp_array[3] or ""

	Logger.debug("[Functions] [CHECK_destination] Dialed destination number :" .. maxlength)

	
	if (user_rates['id'] == "" or  user_rates['id'] == nil) then
		return
	end

	local customer_origination_rates_info = user_rates; 
	local customer_carddata = userinfo	
	local origination_dp_string = xml_user_rates

	if(tonumber(userinfo['maxchannels']) > 0) then 	
		session:execute("limit","db "..userinfo['number'].." db_"..userinfo['number'].." "..userinfo['maxchannels']) 
	end

	local minimumcharge = user_rates['cost'];    
	local reseller_list;    
	local inc_reseller_list;

	local cust_pricelist_id = userinfo['pricelist_id'];
	local cust_accountid = userinfo['id'];
	local cust_resellerid = userinfo['reseller_id'];
	local cust_type = userinfo['type'];


	while (tonumber(userinfo['reseller_id']) > 0 and tonumber(maxlength) > 0 ) do 
		local number_loop_string = number_loop(destination,'blocked_patterns') 

	    	local block_prefix = check_blocked_prefix(userinfo, destination,number_loop_string);
		if( block_prefix ~= "") then
	   		--error_xml_without_cdr(destination,"DESTINATION_BLOCKED") ;
		        --return 1
		end
		Logger.notice("FINDING LIMIT FOR RESELLER: "..userinfo['reseller_id'])

		reseller_userinfo = get_account(userinfo['reseller_id'])

		if (reseller_userinfo == nil) then
	    	    error_xml_without_cdr(destination,"ACCOUNT_INACTIVE_DELETED","ASTPP-CALLINGCARD",config['playback_audio_notification']); 
		    return
		else	    
		    -- If call is pstn and dialed modify defined then do number translation
		if (reseller_userinfo['dialed_modify'] ~= '') then
		    destination = do_number_translation(reseller_userinfo['dialed_modify'],destination)
		end    
		    number_loop_str = number_loop(destination)
		    reseller_ids[i] = reseller_userinfo
		    
		    -- print reseller information 
		Logger.info("=============== Reseller Information ===================")
		Logger.info("User id : "..reseller_userinfo['id'])  
		Logger.info("Account code : "..reseller_userinfo['number'])
		Logger.info("Balance : "..get_balance(reseller_userinfo))  
		Logger.info("Type : "..reseller_userinfo['posttoexternal'].." [0:prepaid,1:postpaid]")  
		Logger.info("Ratecard id : "..reseller_userinfo['pricelist_id'])  
		Logger.info("========================================================")  
		           
		tmp_array_reseller = get_call_maxlength(reseller_userinfo,destination,call_direction,number_loop_str)	    
		    
		reseller_maxlength = tmp_array_reseller[1];
		reseller_rates = tmp_array_reseller[2];
		xml_reseller_rates = tmp_array_reseller[3];
	
		origination_dp_string = origination_dp_string.."||"..xml_reseller_rates
		
		--xml_user_rates = xml_user_rates.."||"..xml_reseller_rates.."|RTI"..reseller_userinfo['pricelist_id'].."|UID"..reseller_userinfo['id']

		    --if (reseller_maxlength <= '0') then
		
		     -- Logger.notice("[Dialplan] Reseller max length of call not found!!!");
		    --  return
		    --end

		    if (tonumber(reseller_maxlength) < tonumber(maxlength)) then 
	    		maxlength = reseller_maxlength
		    end
		    
		    if (tonumber(reseller_maxlength) < 1 or reseller_rates['cost'] > user_rates['cost']) then
			error_xml_without_cdr(destination,"RESELLER_COST_CHEAP","ASTPP-CALLINGCARD",config['playback_audio_notification']); 
		        Logger.info("Reseller cost : "..reseller_rates['cost'].." User cost : "..user_rates['cost'])
		    	Logger.notice("[Dialplan] Reseller call price is cheaper, so we cannot allow call to process!!")
	    		return
		    end
		    rate_carrier_id = reseller_rates['trunk_id']
		    userinfo = reseller_userinfo
		end
		if(userinfo['maxchannels'] > 0) then
			sesion:execute("limit","db "..userinfo['number'].." db_"..userinfo['number'].." "..userinfo['maxchannels']) 
		end
    end -- End while 
	
	maxlength = math.floor(maxlength);
    	Logger.notice("[Dialplan]...................... "..maxlength .." Minutes")

        --&error_xml_without_cdr($destination,"NO_SUFFICIENT_FUND") if($maxlength<=0);

        --  Congratulations, we now have a working card,pin, and phone number.
        say_cost( customer_origination_rates_info )
        say_timelimit(maxlength)

        dialout( original_destination_number, destination, maxlength, customer_carddata, customer_origination_rates_info , origination_dp_string ,number_loop_str);

end
