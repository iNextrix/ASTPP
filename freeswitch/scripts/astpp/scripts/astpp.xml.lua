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
       
	-- Set max channel limit for user if > 0
	if(tonumber(customer_userinfo['maxchannels']) > 0) then    		
	    	table.insert(xml, [[<action application="limit" data="db ]]..accountcode..[[ user_]]..accountcode..[[ ]]..customer_userinfo['maxchannels']..[[ !SWITCH_CONGESTION"/>]]);
	end

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
		table.insert(xml, [[<action application="export" data="execute_on_answer=record_session $${recordings_dir}/${strftime(%Y-%m-%d-%H:%M:%S)}_]]..customer_userinfo['number']..[[.wav"/>]]);
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
