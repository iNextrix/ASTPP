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

-- Dialplan header part
function freeswitch_xml_header(xml,destination_number,accountcode,maxlength,call_direction,accountname,xml_user_rates,customer_userinfo,config,xml_did_rates,reseller_cc_limit,callerid_array)
	local callstart = os.date("!%Y-%m-%d %H:%M:%S")

	table.insert(xml, [[<?xml version="1.0" encoding="UTF-8" standalone="no"?>]]);
	table.insert(xml, [[<document type="freeswitch/xml">]]);
	table.insert(xml, [[<section name="dialplan" description="ASTPP Dialplan">]]);
	table.insert(xml, [[<context name="]]..params:getHeader("Caller-Context")..[[">]]);
	table.insert(xml, [[<extension name="]]..destination_number..[[">]]); 
	table.insert(xml, [[<condition field="destination_number" expression="]]..plus_destination_number(params:getHeader("Caller-Destination-Number"))..[[">]]);
	table.insert(xml, [[<action application="set" data="effective_destination_number=]]..destination_number..[["/>]]); 
	table.insert(xml, [[<action application="sched_hangup" data="+]]..((maxlength) * 60)..[[ normal_clearing"/>]]);  
   
   if (call_direction == "outbound" and config['realtime_billing'] == "0") then
      table.insert(xml, [[<action application="set" data="nibble_account=]]..customer_userinfo["nibble_accounts"]..[["/>]])
      table.insert(xml, [[<action application="set" data="nibble_rate=]]..customer_userinfo["nibble_rates"]..[["/>]])
      table.insert(xml, [[<action application="set" data="nibble_init_inc=]]..customer_userinfo["nibble_init_inc"]..[["/>]])
      table.insert(xml, [[<action application="set" data="nibble_inc=]]..customer_userinfo["nibble_inc"]..[["/>]])
      table.insert(xml, [[<action application="set" data="nibble_connectcost=]]..customer_userinfo["nibble_connect_cost"]..[["/>]])
      table.insert(xml, [[<action application="nibblebill" data="heartbeat 30"/>]])
   end
     
	table.insert(xml, [[<action application="set" data="callstart=]]..callstart..[["/>]]);
	table.insert(xml, [[<action application="set" data="hangup_after_bridge=true"/>]]);    
	table.insert(xml, [[<action application="set" data="continue_on_fail=true"/>]]);  
	--table.insert(xml, [[<action application="set" data="ignore_early_media=true"/>]]);       

	table.insert(xml, [[<action application="set" data="account_id=]]..customer_userinfo['id']..[["/>]]);              
	table.insert(xml, [[<action application="set" data="parent_id=]]..customer_userinfo['reseller_id']..[["/>]]);
	table.insert(xml, [[<action application="set" data="entity_id=]]..customer_userinfo['type']..[["/>]]);
	table.insert(xml, [[<action application="set" data="call_processed=internal"/>]]);    
	table.insert(xml, [[<action application="set" data="call_direction=]]..call_direction..[["/>]]); 	
	table.insert(xml, [[<action application="set" data="accountname=]]..accountname..[["/>]]);

	if (call_direction == "inbound" and tonumber(config['inbound_fax']) > 0) then
		table.insert(xml, [[<action application="export" data="t38_passthru=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38_request=true"/>]]);    
	elseif (call_direction == "outbound" and tonumber(config['outbound_fax']) > 0) then
		table.insert(xml, [[<action application="export" data="t38_passthru=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38_request=true"/>]]);    
	end
	--custom outbound        
	if custom_outbound then custom_outbound(xml) end 

	if(tonumber(config['balance_announce']) == 0) then
		table.insert(xml, [[<action application="sleep" data="1000"/>]]);
		table.insert(xml, [[<action application="playback" data="/usr/local/freeswitch/sounds/en/us/callie/astpp-this-card-has-a-balance-of.wav"/>]]);
		table.insert(xml, [[<action application="say" data="en CURRENCY PRONOUNCED ]].. customer_userinfo['balance']..[["/>]]);

	end
	if(tonumber(config['minutes_announce']) == 0) then
		table.insert(xml, [[<action application="sleep" data="500"/>]]);
		table.insert(xml, [[<action application="playback" data="/usr/local/freeswitch/sounds/en/us/callie/astpp-this-call-will-last.wav"/>]]);
		table.insert(xml, [[<action application="say" data="en NUMBER PRONOUNCED ]].. math.floor(maxlength)..[["/>]]);
		table.insert(xml, [[<action application="playback" data="/usr/local/freeswitch/sounds/en/us/callie/astpp-minute.wav"/>]]);       
	end
    
	if (call_direction == "inbound") then 
		table.insert(xml, [[<action application="set" data="origination_rates_did=]]..xml_user_rates..[["/>]]);
	else
		table.insert(xml, [[<action application="set" data="origination_rates=]]..xml_user_rates..[["/>]]);
	end

	if(xml_did_rates ~= nil) then
		table.insert(xml, [[<action application="set" data="origination_rates=]]..xml_did_rates..[["/>]]);
	end

	-- Issue 383 - Updated Check over the limits function replaces the old code
	-- check maxchannels and maxchannels_in for the account
	-- Set DIDINFO to NA as it Non Applicable on outbound calls.

	check_account_maxchannels(xml,config,customer_userinfo,'NA',call_direction,destination_number)

	-- Set max channel limit for user if > 0 ** Replaced by Line above
	-- if(tonumber(customer_userinfo['maxchannels']) > 0) then
	--     	table.insert(xml, [[<action application="limit" data="db ]]..accountcode..[[ user_]]..accountcode..[[ ]]..customer_userinfo['maxchannels']..[[ !SWITCH_CONGESTION"/>]]);
	-- end

	-- Set CPS limit for user if > 0
	if (tonumber(customer_userinfo['cps']) > 0) then
		table.insert(xml, [[<action application="limit" data="hash CPS_]]..accountcode..[[ CPS_user_]]..accountcode..[[ ]]..customer_userinfo['cps']..[[/1 !SWITCH_CONGESTION"/>]]);
	end

    -- Set max channel limit for resellers
    if (reseller_cc_limit ~= nil) then
        table.insert(xml, reseller_cc_limit);
    end   

	if(tonumber(customer_userinfo['is_recording']) == 0) then 
		table.insert(xml, [[<action application="export" data="is_recording=1"/>]]);
		table.insert(xml, [[<action application="export" data="media_bug_answer_req=true"/>]]);
		table.insert(xml, [[<action application="export" data="RECORD_STEREO=true"/>]]);
		table.insert(xml, [[<action application="export" data="record_sample_rate=8000"/>]]);
		table.insert(xml, [[<action application="export" data="execute_on_answer=record_session $${base_dir}/recordings/${strftime(%Y-%m-%d-%H:%M:%S)}_]]..customer_userinfo['number']..[[.wav"/>]]);
	end

    -- Set original caller id for CDRS
    if (callerid_array['original_cid_name'] ~= '' and callerid_array['original_cid_name'] ~= '<null>')  then
            table.insert(xml, [[<action application="set" data="original_caller_id_name=]]..callerid_array['original_cid_name']..[["/>]]);
    end
    if (callerid_array['cid_number'] ~= '' and callerid_array['cid_number'] ~= '<null>')  then
            table.insert(xml, [[<action application="set" data="original_caller_id_number=]]..callerid_array['original_cid_name']..[["/>]]);
    end
       
	return xml
end

function urlencode(str)
	if (str) then
	str = string.gsub (str, "\n", "\r\n")
	str = string.gsub (str, "([^%w ])",
	function (c) return string.format ("%%%02X", string.byte(c)) end)
		str = string.gsub (str, " ", "+")
	end
	return str    
end

-- Dialplan footer part
function freeswitch_xml_footer(xml)
	table.insert(xml,[[</condition>]]);
	table.insert(xml,[[</extension>]]);
	table.insert(xml,[[</context>]]);
	table.insert(xml,[[</section>]]);
	table.insert(xml,[[</document>]]);
	return xml
end


-- Dialplan for outbound calls
function freeswitch_xml_outbound(xml,destination_number,outbound_info,callerid_array)

    -------------- Caller Id translation ---------
    Logger.warning("[FSXMLOUTBOUND] Caller ID Translation Starts")
    callerid_array['cid_name'] = do_number_translation(outbound_info['cid_translation'],callerid_array['cid_name'])
	callerid_array['cid_number'] = do_number_translation(outbound_info['cid_translation'],callerid_array['cid_number'])    
	xml = freeswitch_xml_callerid(xml,callerid_array)	    	   
    Logger.warning("[FSXMLOUTBOUND] Caller ID Translation Ends")
    ----------------------------------------------------------------------
  	
    local temp_destination_number = destination_number
	if (outbound_info['number_translation'] ~= '') then 
		temp_destination_number = do_number_translation(outbound_info['dialed_modify'],destination_number)
	end

	if(outbound_info['prepend'] ~= '' or outbound_info['strip'] ~= '') then

        if (outbound_info['prepend'] == '') then 
            outbound_info['prepend'] = '*'                        
        end

        if (outbound_info['strip'] == '') then 
            outbound_info['strip'] = '*'
        end

		temp_destination_number = do_number_translation(outbound_info['strip'].."/"..outbound_info['prepend'],temp_destination_number)
	end
    
	xml_termiantion_rates= "ID:"..outbound_info['outbound_route_id'].."|CODE:"..outbound_info['pattern'].."|DESTINATION:"..outbound_info['comment'].."|CONNECTIONCOST:"..outbound_info['connectcost'].."|INCLUDEDSECONDS:"..outbound_info['includedseconds'].."|COST:"..outbound_info['cost'].."|INC:"..outbound_info['inc'].."|INITIALBLOCK:"..outbound_info['init_inc'].."|TRUNK:"..outbound_info['trunk_id'].."|PROVIDER:"..outbound_info['provider_id'];

    table.insert(xml, [[<action application="set" data="calltype=STANDARD"/>]]);        
	table.insert(xml, [[<action application="set" data="termination_rates=]]..xml_termiantion_rates..[["/>]]);        
	table.insert(xml, [[<action application="set" data="trunk_id=]]..outbound_info['trunk_id']..[["/>]]);        
	table.insert(xml, [[<action application="set" data="provider_id=]]..outbound_info['provider_id']..[["/>]]);           
    
	-- Check if is there any gateway configuration params available for it.
	if (outbound_info['dialplan_variable'] ~= '') then 
		Logger.info(" ".. outbound_info['dialplan_variable']);
		local dialplan_variable = split(outbound_info['dialplan_variable'],",")      
		for dialplan_variable_key,dialplan_variable_value in pairs(dialplan_variable) do
			local dialplan_variable_data = split(dialplan_variable_value,"=")  
			Logger.debug("[GATEWAY VARIABLE ] : "..dialplan_variable_data[1] );
			if( dialplan_variable_data[1] ~= nil and dialplan_variable_data[2] ~= nil) then
				table.insert(xml, [[<action application="set" data="]]..dialplan_variable_data[1].."="..dialplan_variable_data[2]..[["/>]]);           	    
			end
		end             
	end
	----------------------- END Gateway configuraiton -------------------------------
	-- Set force codec if configured
    chan_var = "leg_timeout="..outbound_info['leg_timeout']
    if (outbound_info['codec'] ~= '') then
            chan_var = chan_var..",absolute_codec_string=".."^^:"..outbound_info['codec']:gsub("%,", ":")
    end            

	-- Set CPS limit for user if > 0
	if (tonumber(outbound_info['cps']) > 0) then
		table.insert(xml, [[<action application="limit" data="hash CPS_]]..outbound_info['trunk_id']..[[ CPS_trunk_]]..outbound_info['trunk_id']..[[ ]]..outbound_info['cps']..[[/1 !SWITCH_CONGESTION"/>]]);
	end

	if(tonumber(outbound_info['maxchannels']) > 0) then    
		table.insert(xml, [[<action application="limit_execute" data="db ]]..outbound_info['path']..[[ gw_]]..outbound_info['path']..[[ ]]..outbound_info['maxchannels']..[[ bridge []]..chan_var..[[]sofia/gateway/]]..outbound_info['path']..[[/]]..temp_destination_number..[["/>]]);
	else
		table.insert(xml, [[<action application="bridge" data="[]]..chan_var..[[]sofia/gateway/]]..outbound_info['path']..[[/]]..temp_destination_number..[["/>]]);
	end

	if(outbound_info['path1'] ~= '' and outbound_info['path1'] ~= outbound_info['gateway']) then
		table.insert(xml, [[<action application="bridge" data="[]]..chan_var..[[]sofia/gateway/]]..outbound_info['path1']..[[/]]..temp_destination_number..[["/>]]);
	end

	if(outbound_info['path2'] ~= '' and outbound_info['path2'] ~= outbound_info['gateway']) then
		table.insert(xml, [[<action application="bridge" data="[]]..chan_var..[[]sofia/gateway/]]..outbound_info['path2']..[[/]]..temp_destination_number..[["/>]]);
	end
                           
    return xml
end

-- Dialplan for inbound calls
function freeswitch_xml_inbound(xml,didinfo,userinfo,config,xml_did_rates,callerid_array)

    -------------- Caller Id translation ---------
    Logger.warning("[FSXMLINBOUND] Caller ID Translation Starts")
    callerid_array['cid_name'] = do_number_translation(didinfo['did_cid_translation'],callerid_array['cid_name'])
	callerid_array['cid_number'] = do_number_translation(didinfo['did_cid_translation'],callerid_array['cid_number'])
	xml = freeswitch_xml_callerid(xml,callerid_array)	    	   
    Logger.warning("[FSXMLINBOUND] Caller ID Translation Ends")
    -----------------------------------------------

	table.insert(xml, [[<action application="set" data="receiver_accid=]]..didinfo['accountid']..[["/>]]);

	-- Issue 383
	-- Check Accounts limits first -
	check_account_maxchannels(xml,config,userinfo,didinfo,call_direction,destination_number)


	-- Set max channel limit for did if > 0     
	if(tonumber(didinfo['maxchannels']) > 0) then    
	    table.insert(xml, [[<action application="limit" data="db ]]..destination_number..[[ did_]]..destination_number..[[ ]]..didinfo['maxchannels']..[[ !SWITCH_CONGESTION"/>]]);        
	end

        local bridge_str = ""
        local common_chan_var = ""
	--To split the DID destination number string
	local destination_str = {}
	string.gsub(didinfo['extensions'], "([^,|]+)", function(value) destination_str[#destination_str + 1] =     value;  end);
	local deli_str = {}
	string.gsub(didinfo['extensions'], "([,|]+)", function(value) deli_str[#deli_str + 1] =     value;  end);

	if (tonumber(didinfo['call_type']) == 0 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=STANDARD"/>]]);     
		table.insert(xml, [[<action application="set" data="accountcode=]]..didinfo['account_code']..[["/>]]);
		table.insert(xml, [[<action application="set" data="caller_did_account_id=]]..userinfo['id']..[["/>]]);
        table.insert(xml, [[<action application="set" data="origination_rates_did=]]..xml_did_rates..[["/>]]);
		table.insert(xml, [[<action application="transfer" data="]]..didinfo['extensions']..[[ XML default"/>]]);

	elseif (tonumber(didinfo['call_type']) == 1 and didinfo['extensions'] ~= '') then

		table.insert(xml, [[<action application="set" data="calltype=DID-LOCAL"/>]]);             
        if (config['opensips'] == '1') then
          for i = 1, #destination_str do
          		if notify then notify(xml,destination_str[i]) end
                bridge_str = bridge_str.."[leg_timeout="..didinfo['leg_timeout'].."]user/"..destination_str[i].."@${domain_name}"
                if i <= #deli_str then
                        bridge_str = bridge_str..deli_str[i]
                end
          end
          table.insert(xml, [[<action application="bridge" data="]]..bridge_str..[["/>]]);          
        else      
          common_chan_var = "{sip_invite_params=user=LOCAL,sip_from_uri="..didinfo['extensions'].."@${domain_name}}"
          for i = 1, #destination_str do
          	    if notify then notify(xml,destination_str[i]) end
                bridge_str = bridge_str.."[leg_timeout="..didinfo['leg_timeout'].."]sofia/${sofia_profile_name}/"..destination_str[i].."@"..config['opensips_domain']
                if i <= #deli_str then
                        bridge_str = bridge_str..deli_str[i]
                end
          end
          table.insert(xml, [[<action application="bridge" data="]]..common_chan_var..bridge_str..[["/>]]);
        end
        -- To leave voicemail 
        leave_voicemail(xml,destination_number,destination_str[1])
	 elseif (tonumber(didinfo['call_type']) == 3 and didinfo['extensions'] ~= '') then
	    table.insert(xml, [[<action application="set" data="calltype=SIP-DID"/>]]);            
		if (config['opensips'] == '1') then
            common_chan_var = "{sip_contact_user="..destination_number.."}"
            for i = 1, #destination_str do
            	  if notify then notify(xml,destination_str[i]) end
                  bridge_str = bridge_str.."[leg_timeout="..didinfo['leg_timeout'].."]sofia/${sofia_profile_name}/"..destination_number.."${regex(${sofia_contact("..destination_str[i].."@${domain_name})}|^[^@]+(.*)|%1)}"
                  if i <= #deli_str then
                          bridge_str = bridge_str..deli_str[i]
                  end
            end
            table.insert(xml, [[<action application="bridge" data="]]..common_chan_var..bridge_str..[["/>]]);            
        else
            common_chan_var = "{sip_invite_params=user=LOCAL,sip_from_uri="..didinfo['extensions'].."@${domain_name}}"
            for i = 1, #destination_str do
            	  if notify then notify(xml,destination_str[i]) end
                  bridge_str = bridge_str.."[leg_timeout="..didinfo['leg_timeout'].."]sofia/${sofia_profile_name}/"..destination_str[i].."@"..config['opensips_domain']
                  if i <= #deli_str then
                          bridge_str = bridge_str..deli_str[i]
                  end
            end
            table.insert(xml, [[<action application="bridge" data="]]..common_chan_var..bridge_str..[["/>]]);
        end
        -- To leave voicemail 
        leave_voicemail(xml,destination_number,destination_str[1])

	elseif(tonumber(didinfo['call_type']) == 2 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=OTHER"/>]]); 
		table.insert(xml, [[<action application="bridge" data="]]..didinfo['extensions']..[["/>]]);
	elseif(tonumber(didinfo['call_type']) == 4 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=DIRECT-IP"/>]]);
		table.insert(xml, [[<action application="bridge" data="[leg_timeout=]]..didinfo['leg_timeout']..[[]sofia/${sofia_profile_name}/]]..destination_number..[[@]]..didinfo['extensions']..[["/>]]);
	elseif(tonumber(didinfo['call_type']) == 5 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=DID@IP"/>]]);
		table.insert(xml, [[<action application="bridge" data="[leg_timeout=]]..didinfo['leg_timeout']..[[]sofia/${sofia_profile_name}/]]..didinfo['extensions']..[["/>]]);
	else
		--Inbound custom 
		if custom_inbound then custom_inbound(xml,didinfo,userinfo,config,xml_did_rates,callerid_array) end  
		--error_xml_without_cdr(destination_number,"DID_DESTINATION_NOT_FOUND","DID",config['playback_audio_notification'],userinfo['number'])

	end
	return xml
end

-- Dialplan for sip2sip calls
function freeswitch_xml_local(xml,destination_number,destinationinfo,callerid_array)

    -------------- Caller Id translation ---------
    Logger.warning("[FSXMLLOCAL] Caller ID Translation Starts")
    callerid_array['cid_name'] = do_number_translation(destinationinfo['did_cid_translation'],callerid_array['cid_name'])
	callerid_array['cid_number'] = do_number_translation(destinationinfo['did_cid_translation'],callerid_array['cid_number'])
	xml = freeswitch_xml_callerid(xml,callerid_array)	    	   
    Logger.warning("[FSXMLLOCAL] Caller ID Translation Ends")
    ----------------------------------------------------------------------

    table.insert(xml, [[<action application="set" data="calltype=LOCAL"/>]]);
    table.insert(xml, [[<action application="set" data="receiver_accid=]]..destinationinfo['accountid']..[["/>]]);

    if notify then notify(xml,destination_number) end

    if (config['opensips'] == '1') then
      table.insert(xml, [[<action application="bridge" data="[leg_timeout=]]..config['leg_timeout']..[[]user/]]..destination_number..[[@${domain_name}"/>]]);
    else
      table.insert(xml, [[<action application="set" data="sip_h_X-call-type=did"/>]]);
      table.insert(xml, [[<action application="set" data="sip_h_X-did-call-type=DID-LOCAL"/>]]);
      table.insert(xml, [[<action application="bridge" data="{sip_invite_params=user=LOCAL,sip_from_uri=]]..destination_number..[[@${domain_name}}[leg_timeout=]]..config['leg_timeout']..[[]sofia/${sofia_profile_name}/]]..destination_number..[[@]]..config['opensips_domain']..[["/>]]);
    end

    -- To leave voicemail 
    leave_voicemail(xml,destination_number,destination_number)

    return xml
end

-- Set callerid to override in calls
function freeswitch_xml_callerid(xml,calleridinfo)
        if (calleridinfo['cid_name'] ~= '' and calleridinfo['cid_name'] ~= '<null>')  then
                table.insert(xml, [[<action application="set" data="effective_caller_id_name=]]..calleridinfo['cid_name']..[["/>]]);
        end
        if (calleridinfo['cid_number'] ~= '' and calleridinfo['cid_number'] ~= '<null>')  then
                table.insert(xml, [[<action application="set" data="effective_caller_id_number=]]..calleridinfo['cid_number']..[["/>]]);
        end
        return xml
end


-- not found dialplan
function not_found(xml)

	table.insert(xml, [[<?xml version="1.0" encoding="UTF-8" standalone="no"?>]]);
	table.insert(xml, [[<document type="freeswitch/xml">]]);
	table.insert(xml, [[<section name="result">]]);
	table.insert(xml, [[<result status="not found"/>]]);	    
	table.insert(xml, [[</section>]]);
	table.insert(xml, [[</document>]]);
	return xml
end

-- Generate header 
function xml_header(xml,destination_number)
    table.insert(xml, [[<?xml version="1.0" encoding="UTF-8" standalone="no"?>]]);
	table.insert(xml, [[<document type="freeswitch/xml">]]);
	table.insert(xml, [[<section name="dialplan" description="ASTPP Dialplan">]]);
	table.insert(xml, [[<context name="]]..params:getHeader("Caller-Context")..[[">]]);
	table.insert(xml, [[<extension name="]]..destination_number..[[">]]); 
	table.insert(xml, [[<condition field="destination_number" expression="]]..plus_destination_number(destination_number)..[[">]]);
	return xml
end

-- To Leave voicemail dialplan 
function leave_voicemail(xml,vm_alternate_greet_id,vm_destination)
	table.insert(xml, [[<condition field="${cond(${user_data ]]..vm_destination..[[@${domain_name} param vm-enabled} == true ? YES : NO)}" expression="^YES$">]])
    table.insert(xml, [[<action application="answer"/>]]);    
	table.insert(xml, [[<action application="export" data="voicemail_alternate_greet_id=]]..vm_alternate_greet_id..[["/>]]);  
    table.insert(xml, [[<action application="voicemail" data="default $${domain_name} ]]..vm_destination..[["/>]]);    
    table.insert(xml, [[<anti-action application="hangup" data="${originate_disposition}"/>]])
    table.insert(xml, [[</condition>]])
end

-- Generate voicemail dialplan
function xml_voicemail(xml,destination_number)
	local xml = {}
	xml = xml_header(xml,destination_number)
	    table.insert(xml, [[<action application="answer"/>]]);      
	    table.insert(xml, [[<action application="voicemail" data="check default ${domain_name} ]]..params:getHeader("Hunt-Username")..[["/>]]);
	xml = xml_footer(xml)	   	    
	XML_STRING = table.concat(xml, "\n");
	Logger.debug("Generated XML:\n" .. XML_STRING)
	return xml
end

-- XML footer
function xml_footer(xml)
	table.insert(xml,[[</condition>]]);
	table.insert(xml,[[</extension>]]);
	table.insert(xml,[[</context>]]);
	table.insert(xml,[[</section>]]);
	table.insert(xml,[[</document>]]);
	return xml
end

-- Handle calls errors 
function error_xml_without_cdr(destination_number,error_code,calltype,playback_audio_notification,account_id)
     local xml = {};

	--Logger.warning("[ERROR]  :" .. destination_number)
	local log_type
	local log_message
	local hangup_cause 
	local audio_file
	local audio_file = ""
	local sound_path = "/usr/local/freeswitch/sounds/en/us/callie/"
	local accountcode = ""

	if(params:getHeader("variable_accountcode") == nil) then
		if(accountnumber ~= nil) then
			accountcode = accountnumber
		end
	else
		accountcode = params:getHeader("variable_accountcode");
	end
	if(error_code == "AUTHENTICATION_FAIL") then 
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode.." is not authenticated!!";
		hangup_cause = "AUTHENTICATION_FAIL";
		audio_file = sound_path ..  "astpp_expired.wav";
	elseif(error_code == "ACCOUNT_INACTIVE_DELETED") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode.." is either inactive or deleted!!";
		hangup_cause = "ACCOUNT_INACTIVE_DELETED";
		audio_file = sound_path ..  "astpp_expired.wav";
	elseif(error_code == "ACCOUNT_EXPIRE") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode.." Account has been expired!!";
		hangup_cause = "CALL_REJECTED";
		audio_file = sound_path ..  "astpp_expired.wav";
	elseif(error_code == "NO_SUFFICIENT_FUND") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode.." doesn't have sufficiant fund!!";
		hangup_cause = "NO_SUFFICIENT_FUND";
		audio_file = sound_path ..  "astpp-not-enough-credit.wav";
	elseif(error_code == "DESTINATION_BLOCKED") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode..". Dialed number ("..destination_number..") is blocked for account!!";
		hangup_cause = "DESTINATION_BLOCKED";
		audio_file = sound_path ..  "astpp-badnumber.wav";
	elseif(error_code == "ORIGNATION_RATE_NOT_FOUND") then
		log_type = "WARNING";

		log_message = "Accountcode ".. accountcode ..". Dialed number ("..destination_number..")  origination rates not found!!";
		hangup_cause = "ORIGNATION_RATE_NOT_FOUND";
		audio_file = sound_path ..  "astpp-badphone.wav";
	elseif(error_code == "RESELLER_COST_CHEAP") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode..". Dialed number ("..destination_number..") , Reseller call is priced too cheap! Call being barred!!";
		hangup_cause = "RESELLER_COST_CHEAP";
		audio_file = sound_path ..  "astpp-badphone.wav";

	elseif(error_code == "TERMINATION_RATE_NOT_FOUND") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode..". Dialed number ("..destination_number..") termination rates not found!!";
		hangup_cause = "TERMINATION_RATE_NOT_FOUND";
		audio_file = sound_path ..  "astpp-badphone.wav";
	elseif(error_code == "DID_DESTINATION_NOT_FOUND") then
		log_type = "WARNING";
		log_message = "Accountcode ".. accountcode..". Dialed number ("..destination_number..") destination not found!!";
		hangup_cause = "DID_DESTINATION_NOT_FOUND";
		audio_file = sound_path ..  "astpp-badphone.wav";
	end

	if(calltype ~= "ASTPP-CALLINGCARD") then
	    xml = xml_header(xml,destination_number)
    	table.insert(xml, [[<action application="log" data="]]..log_type.." "..log_message..[["/>]]); 

	    if (playback_audio_notification == "1") then
	    	table.insert(xml, [[<action application="playback" data="]]..audio_file..[["/>]]);
	    end


	    local callstart = os.date("!%Y-%m-%d %H:%M:%S")
		
	    if (callerid_array['original_cid_name'] ~= '' and callerid_array['original_cid_name'] ~= '<null>')  then
               table.insert(xml, [[<action application="set" data="original_caller_id_name=]]..callerid_array['original_cid_name']..[["/>]]);
            end
            if (callerid_array['cid_number'] ~= '' and callerid_array['cid_number'] ~= '<null>')  then
               table.insert(xml, [[<action application="set" data="original_caller_id_number=]]..callerid_array['original_cid_name']..[["/>]]);
            end
		
            table.insert(xml, [[<action application="set" data="error_cdr=1"/>]]);
	    table.insert(xml, [[<action application="set" data="callstart=]]..callstart..[["/>]]);
	    table.insert(xml, [[<action application="set" data="account_id=]]..account_id..[["/>]]);
	    table.insert(xml, [[<action application="set" data="call_direction=outbound"/>]]);
	    table.insert(xml, [[<action application="set" data="sip_ignore_remote_cause=true"/>]]);        
	    table.insert(xml, [[<action application="set" data="call_processed=internal"/>]]);
	    table.insert(xml, [[<action application="set" data="effective_destination_number=]]..destination_number..[["/>]]); 
	    --table.insert(xml, [[<action application="set" data="hangup_cause=${last_bridge_hangup_cause}"/>]]);  
	    --table.insert(xml, [[<action application="set" data="process_cdr=false"/>]]);      
	    table.insert(xml, [[<action application="set" data="last_bridge_hangup_cause=]]..hangup_cause..[["/>]]);        
	    table.insert(xml, [[<action application="hangup" data="]]..hangup_cause..[["/>]]);      

	    xml = xml_footer(xml);
	    XML_STRING = table.concat(xml, "\n");
	    Logger.debug("Generated XML:\n" .. XML_STRING)
	    return
	else
		session:execute("set", "process_cdr=false" );
		session:streamFile( audio_file );
	end
end

-- Generate calling card dialplan
function generate_cc_dialplan(destination_number)
	local xml = {};
	table.insert(xml, [[<?xml version="1.0" encoding="UTF-8" standalone="no"?>]]);
	table.insert(xml, [[<document type="freeswitch/xml">]]);
		table.insert(xml, [[<section name="dialplan" description="ASTPP Dialplan">]]);
			table.insert(xml, [[<context name="]]..params:getHeader("Caller-Context")..[[">]]);
				table.insert(xml, [[<extension name="]]..destination_number..[[">]]); 
				table.insert(xml, [[<condition field="destination_number" expression="]]..plus_destination_number(destination_number)..[[">]]);
					table.insert(xml, [[<action application="log" data="INFO ASTPP - Calling Card Call"/>]]);        
					table.insert(xml, [[<action application="answer"/>]]);
					table.insert(xml, [[<action application="sleep" data="2000"/>]]);                    
					table.insert(xml, [[<action application="lua" data="astpp-callingcards.lua"/>]]);    
				table.insert(xml,[[</condition>]]);
				table.insert(xml,[[</extension>]]);
			table.insert(xml,[[</context>]]);
		table.insert(xml,[[</section>]]);
	table.insert(xml,[[</document>]]);
	XML_STRING = table.concat(xml, "\n");
	Logger.debug("Generated XML:\n" .. XML_STRING)
end

-- Set reseller concurrent call limits
function set_cc_limit_resellers(reseller_userinfo)

		local xml_temp = ""
		-- Set CPS limit for reseller if > 0
		if (tonumber(reseller_userinfo['maxchannels']) > 0) then
			xml_temp = "<action application=\"limit\" data=\"db "..reseller_userinfo['number'].. " user_"..reseller_userinfo['number'].." "..reseller_userinfo['maxchannels'].." !SWITCH_CONGESTION\"/>"
		end

		if (tonumber(reseller_userinfo['cps']) > 0) then
			xml_temp = xml_temp.."<action application=\"limit\" data=\"hash CPS_"..reseller_userinfo['number'].. " CPS_user_"..reseller_userinfo['number'].." "..reseller_userinfo['cps'].."/1 !SWITCH_CONGESTION\"/>"
		end

		return xml_temp
end

-- Issue 383 Code changes
-- Log the over the limit into the  overmax table
function log_overlimit(destination_number,call_direction,customerid,xml, limittype, ccmax, ccmaxin, account_maxchannels_type,account_maxchannels, account_maxchannels_reserved, account_maxchannels_in)
	-- Overlimit Codes
	-- 1 - Over max Calls
	-- 2 - Over Max In
	-- 3 - DID over Limit
	-- 4
	-- 5
	-- 9 - Interval Limit.


	local sound_path = "/usr/local/freeswitch/sounds/en/us/callie/"
	local audio_file = sound_path .. "astpp-alllinesused.wav";
	-- local hangup_cause = "USER_BUSY";
	if (call_direction == 'outbound') then
		table.insert(xml, [[<action application="playback" data="]] .. audio_file .. [["/>]]);
	end
	-- table.insert(xml, [[<action application="hangup" data="]] .. hangup_cause .. [["/>]]);
	local startdate = os.date("!%Y-%m-%d %H:%M:%S")
	Logger.info("=============== Log Over Limit Information ===================")
	Logger.info("Direction : "..call_direction)
	Logger.info("Account id : "..customerid)
	Logger.info("Destination Number : "..destination_number)
	Logger.info("Date : "..startdate)
	Logger.info("Limit Type: : "..limittype)
	Logger.info("Total Channels: "..account_maxchannels)
	Logger.info("Total Reserved Channels: "..account_maxchannels_reserved)
	Logger.info("Total Inbound Channels: "..account_maxchannels_in)
	Logger.info("CC MAX: "..ccmax)
	Logger.info("CC MAXIN: "..ccmaxin)
	Logger.info("================================================================")
	local query = "INSERT INTO "..TBL_OVERMAX.." (direction, accountid, datetime, destinationnumber, limittype, ccmax, ccmaxin, maxchannels_type,maxchannels, maxchannels_reserved, maxchannels_in) VALUES ('"..call_direction.."', '"..customerid.."', '"..startdate.."', '"..destination_number.."', '"..limittype.."', '".. ccmax.."', '"..tonumber(ccmaxin).."', '".. account_maxchannels_type.."', '"..account_maxchannels.."', '".. account_maxchannels_reserved.."', '".. account_maxchannels_in.."');";
	Logger.debug("[LOAD_CONF] Query :" .. query)

	dbh:query(query)


end

-- Issue 383 Code changes
-- Check account and get inbound and outbound channels in use.
-- This function will be called at approx line 80 in astpp.xml.lua for OUTBOUND Calls. actual call is check_account_maxchannels(xml,config,userinfo,didinfo,call_direction)
-- This function will be called at appox line 230 in astpp.xml.lua for INBOUND Calls. actual call is check_account_maxchannels(xml,config,userinfo,didinfo,call_direction)
-- New Function get_account_maxchannels was created in astpp.functions.lua, this returns the maxhcannels and maxchannels_in (new table field) from the NON-PROVIDER Account

function check_account_maxchannels(xml,config,customerinfo,didinfo,call_direction,destination_number)

	Logger.info("=============== Check Concurrent Call Limits ===================")
	Logger.info("Call Direction: "..call_direction)
	Logger.info("Destination Number: "..destination_number)
	-- Account Max channels - This is the TOTAL Number of PATHS a customer can use at one time.
	-- This does NOT override the maxchannels on a DID, or trunk, but if the maxchannels number is reached for the account, then any calls to or from this account will be denied!
	--
	--
	-- Settings:
	-- Maxchannels is total paths as defined by Maxchannels Type
	-- Maxchannels Type - 0 - limit outbound only ** Legacy MODE (This is how it works before this patch/update)
	--                  - 1 - limit inbound and outbound
	--                  - 2 - limit inbound only ** Future development if there is a need, possible fraud concern.
	--
	-- Maxchannels_reserve_out - # of channels to reserve for outbound.
	--      IF THIS IS > 0, then the system will always reserve this number of channels to ensure that inbound calls cannot use up all the channels.
	--      IF the total calls is >  inbound and outbound (including the reserved outbound channels), calls will be denied.
	--
	-- Note: This does not override the maxchannels on a trunk or DID, but if all the channels for the account are used up the DID will ring busy.
	--
	--
	--api = freeswitch.API()
	--cc = api:executeString("limit_usage db ".. accountcode .. " user_" .. accountcode);
	if ( call_direction == 'inbound' and type(didinfo) == 'string') then -- The firstime this is called on an inbound call it is the DID from the Provider not Account that owns the did, so ignore it.
		return
	end

	-- Lets Check the Call Direction, if it is inbound then we need to get information of the account that owns the DID.
	-- We need this so we can get the maxchannels_in (new table field) of the Account as well as the Maxchannels (OUT)
	if (call_direction == "inbound") then
		if (tonumber(didinfo['account_maxchannels_type']) == 0) then -- Legacy Mode.
			-- This is an inbound call and we are only using account maxchannels for outbound limiting so ignore and return ** rethink this...
			Logger.info("Concurrent Calls: Legacy mode detected - DO NOT check inbound calls.")
			Logger.info("========================================================")
			return
		end
		accountid = didinfo['accountid']
		accountcode = didinfo['account_code'];
		account_maxchannels_type = tonumber(didinfo['account_maxchannels_type'])
		if (tonumber(didinfo['account_maxchannels_type']) == 1) then -- We are going to limit inbound and outbound - lets get started.
			account_maxchannels = tonumber(didinfo['account_maxchannels']);
			account_maxchannels_in = tonumber(didinfo['account_maxchannels']);
			account_maxchannels_reserved =tonumber(didinfo['account_maxchannels_reserved'])
			if (account_maxchannels_reserved > 0 ) then
				account_maxchannels_in = tonumber(didinfo['account_maxchannels']) - tonumber(didinfo['account_maxchannels_reserved'])
			end
			-- We return USER_BUSY, because this is inbound and we want the caller to hear a busy tone instead of fast busy or a all circuits busy message.account_maxchannels
			-- SWITCH_CONGESTION - should only be used when it is a true congestions or the end point cannot be reached.
			--   table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_calls_]] .. accountcode .. [[ ]] .. account_maxchannels .. [[ !USER_BUSY"/>]]);
			--   table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_in_]] .. accountcode .. [[ ]] .. account_maxchannels_in .. [[ !USER_BUSY"/>]]);
		end
	end
	if (call_direction == "outbound") then
		accountid = customerinfo['id'];
		accountcode = customerinfo['number'];
		account_maxchannels_type = tonumber(customerinfo['maxchannels_type'])
		if (tonumber(customerinfo['maxchannels_type']) == 1) then -- We are going to limit inbound and outbound - lets get started.

			account_maxchannels_type = customerinfo['maxchannels_type'];
			account_maxchannels = tonumber(customerinfo['maxchannels']);
			account_maxchannels_in = tonumber(customerinfo['maxchannels']);
			account_maxchannels_reserved =tonumber(customerinfo['maxchannels_reserved'])
			if (account_maxchannels_reserved > 0 ) then
				account_maxchannels_in = tonumber(customerinfo['maxchannels']) - tonumber(customerinfo['maxchannels_reserved'])
			end
			-- We return USER_BUSY, because this is inbound and we want the caller to hear a busy tone instead of fast busy or a all circuits busy message.account_maxchannels
			-- SWITCH_CONGESTION - should only be used when it is a true congestions or the end point cannot be reached.
			--table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_calls_]] .. accountcode .. [[ ]] .. account_maxchannels .. [[ !USER_BUSY"/>]]);
			--table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_in_]] .. accountcode .. [[ ]] .. account_maxchannels_in .. [[ !USER_BUSY"/>]]);
		else
					account_maxchannels_type = customerinfo['maxchannels_type'];
					account_maxchannels = tonumber(customerinfo['maxchannels']);
					account_maxchannels_in = tonumber(customerinfo['maxchannels']);
					account_maxchannels_reserved =tonumber(customerinfo['maxchannels_reserved'])
		end
	end

	if (tonumber(account_maxchannels) == 0) then -- We are not using max channels - so return
		Logger.info("Concurrent Calls Limiting is NOT Enabled")
		Logger.info("========================================================")
		return
	end
	-- Check to see if we are at or over the limits
	Logger.info("Account ID: "..accountid)
	Logger.info("Account Number: "..accountcode)
	Logger.info("Limit Type: "..account_maxchannels_type)
	Logger.info("Total Channels: "..account_maxchannels)
	Logger.info("Total Reserved Channels: "..account_maxchannels_reserved)
	Logger.info("Total Inbound Channels: "..account_maxchannels_in)
	api = freeswitch.API()
	cc_max = api:executeString("limit_usage db ".. accountcode .. " max_calls_" .. accountcode);
	Logger.info("CC MAX: "..cc_max)
	cc_maxin = api:executeString("limit_usage db ".. accountcode .. " max_in_" .. accountcode);
	Logger.info("CC MAXIN: "..cc_maxin)
	if (tonumber(cc_max) >= tonumber(account_maxchannels)) then -- We hit max limits on all calls - Logit.
		limittype = 1 -- set over limit to maxchannels
		log_overlimit(destination_number,call_direction,accountid,xml, limittype, cc_max,cc_maxin, account_maxchannels_type,account_maxchannels, account_maxchannels_reserved, account_maxchannels_in);
	end

	if (call_direction == 'inbound') then -- On inbound we want to set it so the caller get a busy_tone and not a congestion message.
		if (tonumber(cc_maxin) >= tonumber(account_maxchannels_in)) then
			limittype = 2 -- set over limit to maxchannels_in
			log_overlimit(destination_number,call_direction,accountid,xml, limittype, cc_max, cc_maxin, account_maxchannels_type,account_maxchannels, account_maxchannels_reserved, account_maxchannels_in);
		end
		table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_calls_]] .. accountcode .. [[ ]] .. account_maxchannels .. [[ !USER_BUSY"/>]]);
		table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_in_]] .. accountcode .. [[ ]] .. account_maxchannels_in .. [[ !USER_BUSY"/>]]);
	else -- if it is an outbound call, we only care about the maxchannels, we dont insert the maxchannels_in check.
		table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_calls_]] .. accountcode .. [[ ]] .. account_maxchannels .. [[ !SWITCH_CONGESTION"/>]]);
		-- table.insert(xml, [[<action application="limit" data="db ]] .. accountcode .. [[ max_in_]] .. accountcode .. [[ ]] .. account_maxchannels_in .. [[ !SWITCH_CONGESTION"/>]]);
	end
	Logger.info("================================================================")

end

function print_r(arr, indentLevel)
	local str = ""
	local indentStr = "#"

	if(indentLevel == nil) then
		Logger.warning(print_r(arr, 0))
		return
	end

	for i = 0, indentLevel do
		indentStr = indentStr.."\t"
	end

	for index,value in pairs(arr) do
		if type(value) == "table" then
			str = str..indentStr..index..": \n"..print_r(value, (indentLevel + 1))
		else
			str = str..indentStr..index..": "..value.."\n"
		end
	end
	return str
end


