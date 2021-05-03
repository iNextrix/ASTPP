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

-- Load configuration variables from database 
function load_conf()
	local query = "SELECT name,value FROM "..TBL_CONFIG.." WHERE group_title IN ('global','opensips','callingcard','calls','InternationalPrefixes')";
	Logger.debug("[LOAD_CONF] Query :" .. query)
	local config = {}
	assert (dbh:query(query, function(u)
		config[u.name] = u.value;      
	end))
	return config;
end

-- Get Calling Card Access numbers list
function get_cc_access_number(destination_number)
        local query = "SELECT access_number FROM accessnumber WHERE access_number = '"..destination_number.."' AND status=0 limit 1";
        Logger.debug("[DOAUTHORIZATION] Query :" .. query)
        local cc_access_number;
        assert (dbh:query(query, function(u)
                cc_access_number = u;
        end))
        return cc_access_number;
end

-- Get Speed dial number value
function get_speeddial_number(destination_number,accountid)  
	local query = "SELECT A.number FROM "..TBL_SPEED_DIAL.." as A,"..TBL_USERS.." as B WHERE B.status=0 AND B.deleted=0 AND B.id=A.accountid AND A.speed_num =\"" ..destination_number .."\" AND A.accountid = '"..accountid.."' limit 1";
	Logger.debug("[CHECK_SPEEDDIAL] Query :" .. query)
	assert (dbh:query(query, function(u)
		speeddial = u;
	end))
	if(speeddial and speeddial ~= nil) then        
		return speeddial['number']
	else 
		return destination_number
	end
end
-- Define call direction
function define_call_direction(destination_number,accountcode,config)  
	local didinfo = check_did(destination_number,config); 
	local sip2sipinfo
	if(didinfo == nil) then
  		sip2sipinfo = check_local_call(destination_number);
	end
	if (didinfo ~= nil) then
		call_direction = "inbound";
	elseif (sip2sipinfo ~= nil) then 
		call_direction = "local";
	else		
		call_direction = "outbound";   
	end
	return call_direction;
end
-- Check avilable DID info 
function is_did(destination_number,config)
	local did_localization = nil 
	local check_did_info = ""
	if (config['did_global_translation'] ~= nil and config['did_global_translation'] ~= '' and tonumber(config['did_global_translation']) > 0) then
		did_localization = get_localization(config['did_global_translation'],'O')
		-- @TODO: Apply localization logic for DID global translation
		if (did_localization ~= nil) then
			did_localization['number_originate'] = did_localization['number_originate']:gsub(" ", "")
			destination_number = do_number_translation(did_localization['number_originate'],destination_number)
		end
	end
	--TODO Change query for check DID avilable or not using left join.
	local query = "SELECT * FROM "..TBL_DIDS.." WHERE number =\"" ..destination_number .."\" AND (accountid = 0 OR status = 1) LIMIT 1";
	Logger.debug("[IS_CHECK_DID] Query :" .. query)
	assert (dbh:query(query, function(u)
		check_did_info = u;	 
	end))
	return check_did_info;
end

-- Check DID info 
function check_did(destination_number,config)
	local did_localization = nil 
	if (config['did_global_translation'] ~= nil and config['did_global_translation'] ~= '' and tonumber(config['did_global_translation']) > 0) then
		did_localization = get_localization(config['did_global_translation'],'O')
		-- @TODO: Apply localization logic for DID global translation
		if (did_localization ~= nil) then
			did_localization['number_originate'] = did_localization['number_originate']:gsub(" ", "")
			destination_number = do_number_translation(did_localization['number_originate'],destination_number)
		end
	end
	--TODO Change query for check DID avilable or not using left join.
	local query = "SELECT A.id as id,A.number as did_number,B.id as accountid,B.number as account_code,A.number as did_number,A.connectcost,A.includedseconds,A.cost,A.inc,A.extensions,A.maxchannels,A.call_type,A.city,A.province,A.init_inc,A.leg_timeout,A.status,A.country_id,A.call_type_vm_flag FROM "..TBL_DIDS.." AS A,"..TBL_USERS.." AS B WHERE B.status=0 AND B.deleted=0 AND B.id=A.accountid AND A.number =\"" ..destination_number .."\" LIMIT 1";
	Logger.debug("[CHECK_DID] Query :" .. query)
	assert (dbh:query(query, function(u)
		didinfo = u;	 
		-- B.did_cid_translation as did_cid_translation,
		if (did_localization ~= nil) then	
			did_localization['in_caller_id_originate'] = did_localization['in_caller_id_originate']:gsub(" ", "")
			didinfo['did_cid_translation'] = did_localization['in_caller_id_originate']
		else
			didinfo['did_cid_translation'] = ""
		end
	end))
	return didinfo;
end
-- check Reseller DID
function check_did_reseller(destination_number,userinfo,config)
	local number_translation 
	number_translation = config['did_global_translation'];
	destination_number = do_number_translation(number_translation,destination_number)   
	local query = "SELECT A.id as id, A.number AS number,B.cost AS cost,B.connectcost AS connectcost,B.includedseconds AS includedseconds,B.inc AS inc,A.city AS city,A.province,A.call_type,A.extensions AS extensions,A.maxchannels AS maxchannels,A.init_inc FROM "..TBL_DIDS.." AS A,"..TBL_RESELLER_PRICING.." as B WHERE A.number = \"" ..destination_number .."\"  AND B.type = '1' AND B.reseller_id = \"" ..userinfo['reseller_id'].."\" AND B.note =\"" ..destination_number .."\"";
	Logger.debug("[CHECK_DID_RESELLER] Query :" .. query)
	assert (dbh:query(query, function(u)
		didinfo = u;
	end))
	return didinfo;
end

-- Check local info 
function check_local_call(destination_number)
	local query = "SELECT sip_devices.username as username,accounts.number as accountcode,sip_devices.accountid as accountid,accounts.did_cid_translation as did_cid_translation FROM "..TBL_SIP_DEVICES.." as sip_devices,"..TBL_USERS.." as  accounts WHERE accounts.status=0 AND accounts.deleted=0 AND accounts.id=sip_devices.accountid AND sip_devices.username=\"" ..destination_number .."\" limit 1";
	Logger.debug("[CHECK_LOCAL_CALL] Query :" .. query)
	assert (dbh:query(query, function(u)
		sip2sipinfo = u;
	end))
	return sip2sipinfo;
end

-- Do Authentication 
function doauthentication (destination_number,from_ip)
    return ipauthentication (destination_number,from_ip)
end

-- Do IP base authentication 
function ipauthentication(destination_number,from_ip)
	local query = "SELECT "..TBL_IP_MAP..".*, (SELECT number FROM "..TBL_USERS.." where id=accountid AND status=0 AND deleted=0) AS account_code FROM "..TBL_IP_MAP.." WHERE INET_ATON(\"" .. from_ip.. "\") BETWEEN(INET_ATON(SUBSTRING_INDEX(`ip`, '/', 1)) & 0xffffffff ^((0x1 <<(32 -  SUBSTRING_INDEX(`ip`, '/', -1))) -1 )) AND(INET_ATON(SUBSTRING_INDEX(`ip`, '/', 1)) |((0x100000000 >> SUBSTRING_INDEX(`ip`,'/', -1)) -1))  AND \"" .. destination_number .. "\"  LIKE CONCAT(prefix,'%') ORDER BY LENGTH(prefix) DESC LIMIT 1"
	Logger.debug("[IPAUTHENTICATION] Query :" .. query)
	local ipinfo;
	assert (dbh:query(query, function(u)
		ipinfo = u;
		ipinfo ['type'] = 'acl';
	end))
	return ipinfo;
end
-- Do Account authorization
function doauthorization(field_type,accountcode,call_direction,destination_number,number_loop,config)
    local callstart = os.date("!%Y-%m-%d %H:%M:%S")
    local query = "SELECT * FROM "..TBL_USERS.." WHERE "..field_type.." = \""..accountcode.."\" AND deleted = 0 limit 1";
    Logger.debug("[DOAUTHORIZATION] Query :" .. query)
    
    userinfo = nil;
    assert (dbh:query(query, function(u)
	    userinfo = u;	
    end))

    if (userinfo ~= nil) then
	    userinfo['ACCOUNT_ERROR'] = ''
    
        if (userinfo['charge_per_min'] == nil or userinfo['charge_per_min']== '') then userinfo['charge_per_min'] = 0 end
        if (call_direction == 'local' and userinfo['local_call']=='0' and tonumber(userinfo['charge_per_min'])<=0) then
                    userinfo['balance'] = (tonumber(userinfo['posttoexternal']) == 1) and 0 or 100
        end

	if (tonumber(userinfo['status']) ~= 0 or tonumber(userinfo['deleted']) ~= 0)then
	    	Logger.warning("[DOAUTHORIZATION] ["..accountcode.."] Account is either Deactive/Expire or deleted..!!");
		userinfo['ACCOUNT_ERROR'] = 'ACCOUNT_INACTIVE_DELETED'
	    	return userinfo
	end
	if (userinfo['expiry'] < callstart)then
		if(userinfo['expiry'] ~= '0000-00-00 00:00:00')then
		    	Logger.warning("[DOAUTHORIZATION] ["..accountcode.."] Account is expired..!!");
			userinfo['ACCOUNT_ERROR'] = 'ACCOUNT_EXPIRE'
		    	return userinfo
		end
	end
    	balance = get_balance(userinfo,'',config);
    	if (balance < 0) then
    	    Logger.warning("[DOAUTHORIZATION] ["..accountcode.."] Insufficent balance ("..balance..") to make calls..!!");
    	    userinfo['ACCOUNT_ERROR'] = 'NO_SUFFICIENT_FUND'
    	else
    	    if (call_direction == 'outbound') then     
    		    if (check_blocked_prefix (userinfo,destination_number,number_loop) == "false") then
	    	        Logger.warning("[DOAUTHORIZATION] ["..accountcode.."] You are not allowed to dial number..!!");
                    userinfo['ACCOUNT_ERROR'] = 'DESTINATION_BLOCKED'
	    	        return userinfo
	    	    end
	        end
	    end
    else
    	Logger.warning("[DOAUTHORIZATION] ["..accountcode.."] Account is either Deactive/Expire or deleted..!!");
    	userinfo = {}
        userinfo['ACCOUNT_ERROR'] = 'ACCOUNT_INACTIVE_DELETED'
    	return userinfo
    end
	if( userinfo['first_used'] == "0000-00-00 00:00:00") then
		update_first_used_account(userinfo)
	end

	-- If international balance management module installed and destination number prefix start with defined international_prefixes then mark call as international	
	if is_call_international then userinfo = is_call_international(userinfo) end
	
    return userinfo
end

-- Get balance from account info 
function get_balance(userinfo,rates,config)

	-- If call found as international call then get balance from international balance and credit field
	local tmp_prefix=''
	if get_international_balance_prefix then tmp_prefix = get_international_balance_prefix(userinfo) end 	

    balance = tonumber(userinfo['posttoexternal']) == 1 and tonumber(userinfo[tmp_prefix..'credit_limit'])+(tonumber(userinfo[tmp_prefix..'balance'])*(-1)) or tonumber(userinfo[tmp_prefix..'balance'])
    -- Override balance if call is DID / inbound and coming from provider to avoid provider balance checking upon DID call. 
    if (userinfo['type'] == '3' and call_direction == 'inbound') then
            balance = 10000
    end

    if fraud_check_balance_update then balance=fraud_check_balance_update(userinfo,balance,rates) end
    return balance
end

function update_first_used_account(userinfo)
	local callstart = os.date("!%Y-%m-%d %H:%M:%S")
	local query = "update "..TBL_USERS.." SET first_used = '"..callstart .."' where id = '"..userinfo['id'].."'"
	Logger.debug("[update_first_used_account] Query :" .. query)
	assert (dbh:query(query))
	return true
end

-- Check if dialed number prefix is blocked or not 
function check_blocked_prefix(userinfo,destination_number,number_loop)
    local flag = "true"    
    local query = "SELECT * FROM "..TBL_BLOCK_PREFIX.." WHERE "..number_loop.." AND accountid = "..userinfo['id'].. " limit 1 ";
    Logger.debug("[CHECK_BLOCKED_PREFIX] Query :" .. query)
    assert (dbh:query(query, function(u)
    	flag = "false"
    end))
    return flag
end

function get_localization (id,type)
	local localization = nil
	local query 
	if (type=="O") then
		query = "SELECT id,in_caller_id_originate,out_caller_id_originate,number_originate FROM "..TBL_LOCALIZATION.." WHERE id = "..id.. " AND status=0 limit 1 ";
	elseif(type=="T") then
		query = "SELECT id,out_caller_id_terminate,number_terminate FROM "..TBL_LOCALIZATION.." WHERE id=(SELECT localization_id from accounts where id = "..id.. ") AND status=0 limit 1 ";
	end
    Logger.debug("[GET_LOCALIZATION] Query :" .. query)
    assert (dbh:query(query, function(u)
    	localization = u
    end))
    return localization
end



-- Do number translation 
function do_number_translation(number_translation,destination_number)
    local tmp

    tmp = split(number_translation,",")
    for tmp_key,tmp_value in pairs(tmp) do
      tmp_value = string.gsub(tmp_value, "\"", "")
      tmp_str = split(tmp_value,"/")      
      if(tmp_str[1] == '' or tmp_str[1] == nil)then
	return destination_number
      end
      local prefix = string.sub(destination_number,0,string.len(tmp_str[1]));
      if (prefix == tmp_str[1] or tmp_str[1] == '*') then
	    Logger.notice("[DONUMBERTRANSLATION] Before Localization CLI/DST : " .. destination_number)
		if(tmp_str[2] ~= nil) then
            if (tmp_str[2] == '*') then
    			destination_number = string.sub(destination_number,(string.len(tmp_str[1])+1))
            else
                if (tmp_str[1] == '*') then
        			destination_number = tmp_str[2] .. destination_number
                else
        			destination_number = tmp_str[2] .. string.sub(destination_number,(string.len(tmp_str[1])+1))
                end
            end
		else
		    destination_number = string.sub(destination_number,(string.len(tmp_str[1])+1))
		end
	    Logger.notice("[DONUMBERTRANSLATION] After Localization CLI/DST : " .. destination_number)
      end
    end
    return destination_number
end


-- Find Max length

function get_call_maxlength(userinfo,destination_number,call_direction,number_loop,config,didinfo)     
    local maxlength = 0
    local rates
    local rate_group
    local xml_rates
	local tmp = {}
    
    rate_group = get_pricelists (userinfo,destination_number,number_loop,call_direction)
    if (rate_group == nil) then
		Logger.warning("[FIND_MAXLENGTH] Rate group not found or Inactive!!!")
		return 'ORIGNATION_RATE_NOT_FOUND'
	end

	if (call_direction == "local" and config['free_inbound'] ~= nil) then
		rates = {}
		rates['pattern'] = '^'..destination_number..".*"
		rates['comment'] = "Local"
		rates['inc'] = 60
		rates['call_type'] = 0
		Logger.warning("[FIND_MAXLENGTH] free_inbound!!!"..config['free_inbound'])
		rates['cost'] = userinfo['charge_per_min']
		Logger.warning("[FIND_MAXLENGTH] charge_per_min!!!"..userinfo['charge_per_min'])
		if (userinfo['charge_per_min'] == "") then
			rates['cost']=0
		end		
		rates['includedseconds'] = 0
		rates['connectcost'] = 0
		rates['id'] = 0
		if (didinfo ~= nil) then
			rates['country_id']=didinfo['country_id']
		else
			rates['country_id']=0
		end
	else
        rates = get_rates (userinfo,destination_number,number_loop,call_direction,config)
			Logger.info("call_direction:::::: "..call_direction)
		if (rates == nil) then
			Logger.warning("[FIND_MAXLENGTH] Rates not found!!!")
			return 'ORIGNATION_RATE_NOT_FOUND'
		end
		if( call_direction == "inbound" ) then
			rates['pattern'] = '^'..destination_number..".*"
			if (rates['city'] ~= '' and rates['province'] ~= "" ) then 
				rates['comment'] =  rates['city'] .. " " .. rates['province']
			 else  
				rates['comment'] = destination_number 
			end
			
		end

        if (tonumber(rate_group['markup']) > 0) then
            Logger.info("Markup : "..rate_group['markup'])  
            rates['cost'] = rates['cost'] + ((rate_group['markup']*rates['cost'])/100)
	    end

		Logger.info("=============== Rates Information ===================")
		Logger.info("ID : "..rates['id'])  
		Logger.info("Connectcost : "..rates['connectcost'])  
		Logger.info("Includedseconds : "..rates['includedseconds'])  
		Logger.info("Cost : "..rates['cost'])
		Logger.info("comment : "..rates['comment'])
		Logger.info("Country Id : "..rates['country_id'])
		Logger.info("Accid : "..userinfo['id'])
		if(rates['trunk_id'] ~= nil)then Logger.info("Trunk ID: "..rates['trunk_id']) end
		if(rates['routing_type'] ~= nil)then Logger.info("Routing type: "..rates['routing_type']) end
		Logger.info("================================================================")  
	end
    rates['routing_type'] = rate_group['routing_type']		
	if( call_direction == "inbound" ) then
		if(didinfo['accountid'] == nil) then
			didinfo['accountid'] = userinfo['id'];
		end
		xml_rates = "ID:"..rates['id'].."|CODE:"..rates['pattern'].."|DESTINATION:"..rates['comment'].."|CONNECTIONCOST:"..rates['connectcost'].."|INCLUDEDSECONDS:"..rates['includedseconds'].."|CT:"..rates['call_type'].."|COST:"..rates['cost'].."|INC:"..rates['inc'].."|INITIALBLOCK:"..rates['init_inc'].."|RATEGROUP:0|MARKUP:"..rate_group['markup'].."|CI:"..rates['country_id'].."|ACCID:"..didinfo['accountid'];
	else
		if( tonumber(rates['inc'])  == 0 or rates['inc'] == "" ) then
			rates['inc'] = rate_group['inc'];
		end
		if( tonumber(rates['init_inc'])  == 0 or rates['init_inc'] == "" ) then
			rates['init_inc'] = rate_group['initially_increment'];
		end
		
		if( rates['init_inc'] == nil)then
			rates['init_inc']=0;
		end
		if (rates['country_id']==nil) then rates['country_id']=0 end

		xml_rates = "ID:"..rates['id'].."|CODE:"..rates['pattern'].."|DESTINATION:"..rates['comment'].."|CONNECTIONCOST:"..rates['connectcost'].."|INCLUDEDSECONDS:"..rates['includedseconds'].."|CT:"..rates['call_type'].."|COST:"..rates['cost'].."|INC:"..rates['inc'].."|INITIALBLOCK:"..rates['init_inc'].."|RATEGROUP:"..rate_group['id'].."|MARKUP:"..rate_group['markup'].."|CI:"..rates['country_id'].."|ACCID:"..userinfo['id'];

	end

	balance = get_balance(userinfo,'',config)
	      Logger.info("[FIND_MAXLENGTH] Your"..balance.." balance Accountid "..userinfo['id'].." !!!")
	if (balance < (rates['connectcost'] + rates['cost']) and tonumber(package_id) == 0) then
	      Logger.info("[FIND_MAXLENGTH] Your balance is not sufficent to dial "..destination_number.." !!!")
	      return 'NO_SUFFICIENT_FUND'
	end
	if (tonumber(rates['cost']) > 0 ) then
	      maxlength = ( balance -  rates['connectcost'] ) / rates['cost']
	      if ( config['call_max_length'] and (tonumber(maxlength) > tonumber(config['call_max_length']))) then
			  maxlength = config['call_max_length']		      
		      Logger.notice("[FIND_MAXLENGTH] Limiting call to config max length "..maxlength.." mins!")
	      end
	else
	      Logger.notice("[FIND_MAXLENGTH] Call is free - assigning max length!!! :: " .. config['max_free_length'] )
	      if ( config['call_max_length'] and (tonumber(config['max_free_length']) > tonumber(config['call_max_length']))) then
		      maxlength = config['call_max_length']
	      else
		      maxlength = config['max_free_length']
	      end
	end      

    tmp[1] = maxlength
    tmp[2] = rates
    tmp[3] = xml_rates
    
    return tmp
end

-- Get origination rates 
function get_rates(userinfo,destination_number,number_loop,call_direction,config)
    
	local rates_info
    	Logger.debug("[GET_RATES] call_direction :" .. call_direction)
	if call_direction == "inbound" then
		rates_info = check_did(destination_number,config)
	else 

    	local query  = "SELECT * FROM "..TBL_ORIGINATION_RATES.." WHERE "..number_loop.." AND status = 0 AND (pricelist_id = "..userinfo['pricelist_id'].." OR accountid="..userinfo['id']..")  ORDER BY accountid DESC,LENGTH(pattern) DESC,cost DESC LIMIT 1";

    	Logger.debug("[GET_RATES] Query :" .. query)
		assert (dbh:query(query, function(u)
    		rates_info = u
    	end))  
	end
	return rates_info
end

-- get pricelist information 
function get_pricelists (userinfo,destination_number,number_loop,call_direction) 
	local query = "select * from "..TBL_RATE_GROUP.." WHERE id = " ..userinfo['pricelist_id'].." AND status = 0";
	local rategroup_info
	Logger.debug("[GET_PRICELIST_INFO] Query :" .. query)
	assert (dbh:query(query, function(u)
		rategroup_info = u
	end))  
	if(rategroup_info ~= nil) then
		if (rategroup_info['initially_increment'] == nil or rategroup_info['initially_increment'] == '0') then
			rategroup_info['initially_increment'] = 1
		end

		if (rategroup_info['inc'] == nil or rategroup_info['inc'] == '0') then
			rategroup_info['inc'] = 1
		end
	end
	return rategroup_info
end

-- get intial package information
function package_calculation (destination_number,userinfo,call_direction)
	local package_act_id =  userinfo['id']
	if(call_direction == 'inbound')then
		Logger.debug("[GET_PACKAGE_INFO] call_direction :" .. call_direction)
		if(didinfo and didinfo['accountid'] ~= '')then
			Logger.debug("[GET_PACKAGE_INFO] DID_ACCOUNTID :" .. didinfo['accountid'])
			package_act_id =   didinfo['accountid']
		end
	end
	local tmp = {}
	local remaining_sec
	local package_maxlength
	custom_destination = number_loop(destination_number,"patterns")
	local package_info_arr = {}   
	local i = 1  
	local query = "SELECT *,P.id as package_id,P.product_id as product_id FROM ".. TBL_PACKAGE.." as P inner join "..TBL_PACKAGE_PATTERN.." as PKGPTR on P.product_id = PKGPTR.product_id WHERE ".. custom_destination.." AND accountid = ".. package_act_id .. " ORDER BY LENGTH(PKGPTR.patterns) DESC";
	Logger.debug("[GET_PACKAGE_INFO] Query :" .. query)
	assert (dbh:query(query, function(u)
		package_info_arr[i] = u
	    	i = i+1
		end))  

	if(package_info_arr and package_info_arr ~= nil) then
		for package_info_key,package_info in pairs(package_info_arr) do
			--local counter_info = {}
			local used_seconds = 0
			Logger.info("Package ID : "..package_info['package_id'] );
			Logger.info("Product ID : "..package_info['product_id'] );
			Logger.info("Package Type : "..package_info['applicable_for'] .. " Call Direction : "..call_direction .." [0:inbound,1:outbound,2:both]");
			if( (package_info['applicable_for'] == "0" and call_direction == "inbound") or (package_info['applicable_for'] == "1" and call_direction == "outbound") or (package_info['applicable_for'] == "2") ) then
				local counter_info =  get_counters(userinfo,package_info,package_act_id)
				if(counter_info == nil or counter_info['used_seconds'] == nil) then
					used_seconds = 0
				else			
					used_seconds = counter_info['used_seconds']
				end			
				remaining_minutes = (tonumber(package_info['free_minutes']*60) - tonumber(used_seconds))/60
				Logger.info("Remaining minutes : "..remaining_minutes)
				if(remaining_minutes > 0) then
					if (tonumber(balance) <= 0) then
						userinfo['balance'] = 100
						userinfo['credit_limit'] = 200
						Logger.notice("Actual Balance : "..balance)
						Logger.notice("Allocating static balance for package calls, Balance : "..userinfo['balance'].. ", Credit limit : "..userinfo['credit_limit'])
					end			    	
					userinfo['ACCOUNT_ERROR'] = ''
					--remaining_sec = remaining_sec + 5
					package_maxlength = remaining_minutes;	
					package_id = package_info['package_id']
					Logger.notice("package_id : "..package_id)
					break
				end
			end 
		end
	end
	tmp[1]	= userinfo
	tmp[2] = package_maxlength
	return tmp
end 

function get_counters(userinfo,package_info,package_act_id)
	local counter_info;
	local query_counter = "SELECT used_seconds FROM ".. TBL_COUNTERS.."  WHERE  accountid = "..package_act_id.." AND package_id = ".. package_info['package_id'] .." AND status=1 LIMIT 1";
		Logger.debug("[GET_COUNTER_INFO] Query :" .. query_counter)
		assert (dbh:query(query_counter, function(u)
			counter_info = u
		end))
	return counter_info
end

-- Get carrier rates 
function get_carrier_rates(destination_number,number_loop_str,ratecard_id,rate_carrier_id,routing_type)
    
	local carrier_rates = {}     
	local trunk_id=0     
	local query
	if(routing_type == 1) then
		query = "SELECT TK.id as trunk_id,TK.name as trunk_name,TK.codec,GW.name as path,GW.dialplan_variable,TK.provider_id,TR.init_inc,TK.status,TK.maxchannels,TK.cps,TK.leg_timeout,TR.pattern,TR.id as outbound_route_id,TR.connectcost,TR.comment,TR.includedseconds,TR.cost,TR.inc,TR.prepend,TR.strip,(select name from "..TBL_GATEWAYS.." where status=0 AND id = TK.failover_gateway_id) as path1,(select name from "..TBL_GATEWAYS.." where status=0 AND id = TK.failover_gateway_id1) as path2 FROM (select * from "..TBL_TERMINATION_RATES.." order by LENGTH (pattern) DESC) as TR "..TBL_TRUNKS.." as TK,"..TBL_GATEWAYS.." as GW WHERE GW.status=0 AND GW.id= TK.gateway_id AND TK.status=0 AND TK.id= TR.trunk_id AND "..number_loop_str.." AND TR.status = 0 "
	else
		query = "SELECT TK.id as trunk_id,TK.name as trunk_name,TK.codec,GW.name as path,GW.dialplan_variable,TK.provider_id,TR.init_inc,TK.status,TK.maxchannels,TK.cps,TK.leg_timeout,TR.pattern,TR.id as outbound_route_id,TR.connectcost,TR.comment,TR.includedseconds,TR.cost,TR.inc,TR.prepend,TR.strip,(select name from "..TBL_GATEWAYS.." where status=0 AND id = TK.failover_gateway_id) as path1,(select name from "..TBL_GATEWAYS.." where status=0 AND id = TK.failover_gateway_id1) as path2 FROM "..TBL_TERMINATION_RATES.." as TR,"..TBL_TRUNKS.." as TK,"..TBL_GATEWAYS.." as GW WHERE GW.status=0 AND GW.id= TK.gateway_id AND TK.status=0 AND TK.id= TR.trunk_id AND "..number_loop_str.." AND TR.status = 0 "
	end
	if(rate_carrier_id and rate_carrier_id ~= nil and rate_carrier_id ~= '0' and string.len(rate_carrier_id) >= 1 ) then
		query = query.." AND TR.trunk_id IN ("..rate_carrier_id..") "
	else
		trunk_ids={}
		local query_trunks  = "SELECT GROUP_CONCAT(trunk_id) as ids FROM "..TBL_ROUTING.." WHERE pricelist_id="..ratecard_id.." ORDER by id asc";    
		Logger.debug("[GET_CARRIER_RATES_TRUNKS] Query :" .. query_trunks)
		assert (dbh:query(query_trunks, function(u)
			trunk_ids = u
		end))
		if (trunk_ids['ids'] == "" or trunk_ids['ids'] == nil) then
			trunk_ids['ids']=0
		end
		query = query.." AND TR.trunk_id IN ("..trunk_ids['ids']..")"
	end
	if(routing_type == "1") then
		query = query.." ORDER by TR.cost ASC,TR.precedence ASC, TK.precedence"
	else
		query = query.." ORDER by LENGTH (pattern) DESC,TR.cost ASC,TR.precedence ASC, TK.precedence"
	end
	Logger.debug("[GET_CARRIER_RATES] Query :" .. query)
	local i = 1
	local carrier_ignore_duplicate = {}
	assert (dbh:query(query, function(u)
		if (carrier_ignore_duplicate[u['trunk_id']] == nil) then
			carrier_rates[i] = u
			i = i+1
			carrier_ignore_duplicate[u['trunk_id']] = true
		end
	end))    
	return carrier_rates
end

-- Get outbound callerid to override in calls
function get_override_callerid(userinfo,callerid_name,callerid_number)
    local callerid = {}
    local query  = "SELECT callerid_name as cid_name,callerid_number as cid_number,accountid FROM "..TBL_ACCOUNTS_CALLERID.." WHERE accountid = "..userinfo['id'].." AND status=0 LIMIT 1";    
    Logger.debug("[GET_OVERRIDE_CALLERID] Query :" .. query)
    assert (dbh:query(query, function(u)
	    callerid = u
    end))

    if (callerid['cid_number'] ~= nil and callerid['cid_number'] ~= '') then
        callerid['cid_number'] = callerid['cid_number']
        callerid['cid_name'] = callerid['cid_name']
    end
    
    return callerid
end


-- Create number loop for destination number for queries
function number_loop(destination_number,code,skip_tild)
    --Prepare string for code matching in flow.
    local number_len = string.len(destination_number)
	if (code == nil) then
		code = "pattern"
	end
    number_loop_str = '(';
    while (number_len  > 0) do     
        number_loop_str = number_loop_str.. code.." = '"

        if (skip_tild == nil) then
            number_loop_str = number_loop_str.. "^"
        end
        number_loop_str = number_loop_str..string.sub(destination_number,0,number_len)

        if (skip_tild == nil) then    
            number_loop_str = number_loop_str..".*"
        end

        if (skip_tild == "*") then
        	number_loop_str = number_loop_str.. "*"
        end

        number_loop_str = number_loop_str.."' OR "
        number_len = number_len-1
    end	
    number_loop_str = number_loop_str..code.." ='--')"
	return number_loop_str
end    

-- Adding slash \ if number starting with +. 
function plus_destination_number(destination_number)
    destination_number = destination_number:gsub("%s+", "")
    local dnumber = destination_number
	local dfirst =  string.match(dnumber, "^(.)")
	if (dfirst == "+") then
		dnumber = "\\"..dnumber
	end
    return dnumber
end

-- Get Parentid from accountid 
function get_parentid(userinfoid)
    local parentid = 0;
    local query  = "SELECT reseller_id FROM "..TBL_USERS.." WHERE id = "..userinfoid;    
    Logger.debug("[GET RESELLERID] Query :" .. query)
    assert (dbh:query(query, function(u)
	    parent = u
	    if (parent['reseller_id'] ~= nil and parent['reseller_id'] ~= '') then
        	parentid=tonumber(parent['reseller_id']);
    	end
    end))
    
    return parentid
end
-- load_addon_list
function load_addon_list()

    local query = "SELECT package_name FROM addons";
    Logger.debug("[LOAD_ADDON_CONF] Query :" .. query)
    
    local addon_list = {}
    assert (dbh:query(query, function(u)
      addon_list[u.package_name] = u.package_name;      
    end))
    return addon_list;
end
