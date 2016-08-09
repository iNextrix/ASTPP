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
function freeswitch_xml_header(xml,destination_number,accountcode,maxlength,call_direction,accountname,xml_user_rates,customer_userinfo,config,xml_did_rates)
	local callstart = os.date("!%Y-%m-%d %H:%M:%S")

	table.insert(xml, [[<?xml version="1.0" encoding="UTF-8" standalone="no"?>]]);
	table.insert(xml, [[<document type="freeswitch/xml">]]);
	table.insert(xml, [[<section name="dialplan" description="ASTPP Dialplan">]]);
	table.insert(xml, [[<context name="]]..params:getHeader("Caller-Context")..[[">]]);
	table.insert(xml, [[<extension name="]]..destination_number..[[">]]); 
	table.insert(xml, [[<condition field="destination_number" expression="]]..destination_number..[[">]]);
	table.insert(xml, [[<action application="set" data="effective_destination_number=]]..destination_number..[["/>]]); 
	table.insert(xml, [[<action application="sched_hangup" data="+]]..((maxlength) * 60)..[[ allotted_timeout"/>]]);  

	table.insert(xml, [[<action application="set" data="callstart=]]..callstart..[["/>]]);
	table.insert(xml, [[<action application="set" data="hangup_after_bridge=true"/>]]);    
	table.insert(xml, [[<action application="set" data="continue_on_fail=true"/>]]);  
	table.insert(xml, [[<action application="set" data="ignore_early_media=true"/>]]);       

	table.insert(xml, [[<action application="set" data="account_id=]]..customer_userinfo['id']..[["/>]]);              
	table.insert(xml, [[<action application="set" data="parent_id=]]..customer_userinfo['reseller_id']..[["/>]]);
	table.insert(xml, [[<action application="set" data="entity_id=]]..customer_userinfo['type']..[["/>]]);
	table.insert(xml, [[<action application="set" data="call_processed=internal"/>]]);    
	table.insert(xml, [[<action application="set" data="call_direction=]]..call_direction..[["/>]]); 
	
	table.insert(xml, [[<action application="set" data="accountname=]]..accountname..[["/>]]);
	--Logger.info("[Dialplan]  outbound FAX ::: "..call_direction .."----".. config['outbound_fax']);

	if (call_direction == "inbound" and tonumber(config['inbound_fax']) > 0) then
		table.insert(xml, [[<action application="export" data="t38_passthru=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38_request=true"/>]]);    
	elseif (call_direction == "outbound" and tonumber(config['outbound_fax']) > 0) then
		--Logger.info("[Dialplan]  outbound FAX ::: "..call_direction .."----".. config['outbound_fax']);
		table.insert(xml, [[<action application="export" data="t38_passthru=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38=true"/>]]);    
		table.insert(xml, [[<action application="set" data="fax_enable_t38_request=true"/>]]);    
	end

		--table.insert(xml, [[<action application="ring_ready" data="TRUE"/>]]);

		
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
		local customer_maxchannel = customer_userinfo['maxchannels'] .."/".. customer_userinfo['interval']
	    	table.insert(xml, [[<action application="limit" data="db ]]..accountcode..[[ user_]]..accountcode..[[ ]]..customer_maxchannel..[[ !SWITCH_CONGESTION"/>]]);
	end    

	if(tonumber(customer_userinfo['is_recording']) == 0) then 
		table.insert(xml, [[<action application="export" data="is_recording=1"/>]]);
		table.insert(xml, [[<action application="export" data="media_bug_answer_req=true"/>]]);
		table.insert(xml, [[<action application="export" data="RECORD_STEREO=true"/>]]);
		table.insert(xml, [[<action application="export" data="record_sample_rate=8000"/>]]);
		table.insert(xml, [[<action application="export" data="execute_on_answer=record_session $${base_dir}/recordings/${strftime(%Y-%m-%d-%H:%M:%S)}_]]..customer_userinfo['number']..[[.wav"/>]]);
	end
	--table.insert(xml, [[<action application="sched_broadcast" data="+10 execute_on_answer::lua astpp/lib/realtime_billing.lua ${uuid}"/>]]);       
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
function freeswitch_xml_outbound(xml,destination_number,outbound_info)
  	
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
		Logger.info("[Dialplan]  ".. outbound_info['dialplan_variable']);
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
	-- Set force code if configured
	if (outbound_info['codec'] ~= '') then 
		table.insert(xml, [[<action application="set" data="absolute_codec_string=]]..outbound_info['codec']..[["/>]]);           
	end

	if(tonumber(outbound_info['maxchannels']) > 0) then    
		table.insert(xml, [[<action application="limit_execute" data="db ]]..outbound_info['path']..[[ gw_]]..outbound_info['path']..[[ ]]..outbound_info['maxchannels']..[[ bridge sofia/gateway/]]..outbound_info['path']..[[/]]..temp_destination_number..[["/>]]);   
	else
		table.insert(xml, [[<action application="bridge" data="sofia/gateway/]]..outbound_info['path']..[[/]]..temp_destination_number..[["/>]]);      
	end

	if(outbound_info['path1'] ~= '' and outbound_info['path1'] ~= outbound_info['gateway']) then
		table.insert(xml, [[<action application="bridge" data="sofia/gateway/]]..outbound_info['path1']..[[/]]..temp_destination_number..[["/>]]); 
	end

	if(outbound_info['path2'] ~= '' and outbound_info['path2'] ~= outbound_info['gateway']) then
		table.insert(xml, [[<action application="bridge" data="sofia/gateway/]]..outbound_info['path2']..[[/]]..temp_destination_number..[["/>]]); 
	end
                           
    return xml
end


-- Dialplan for inbound calls
function freeswitch_xml_inbound(xml,didinfo,userinfo,config,xml_did_rates)


	table.insert(xml, [[<action application="set" data="receiver_accid=]]..didinfo['accountid']..[["/>]]);  
	-- Set max channel limit for did if > 0     
	if(tonumber(didinfo['maxchannels']) > 0) then    
	    table.insert(xml, [[<action application="limit" data="db ]]..destination_number..[[ did_]]..destination_number..[[ ]]..didinfo['maxchannels']..[[ !SWITCH_CONGESTION"/>]]);        
	end

	if (tonumber(didinfo['call_type']) == 0 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=STANDARD"/>]]);     
		table.insert(xml, [[<action application="set" data="accountcode=]]..didinfo['account_code']..[["/>]]);
		table.insert(xml, [[<action application="set" data="caller_did_account_id=]]..userinfo['id']..[["/>]]);
        table.insert(xml, [[<action application="set" data="origination_rates_did=]]..xml_did_rates..[["/>]]);
		table.insert(xml, [[<action application="transfer" data="]]..didinfo['extensions']..[[ XML default"/>]]);

	elseif (tonumber(didinfo['call_type']) == 1 and didinfo['extensions'] ~= '') then

		table.insert(xml, [[<action application="set" data="calltype=DID-LOCAL"/>]]);     

        if (config['opensips'] == '1') then
          table.insert(xml, [[<action application="bridge" data="user/]]..didinfo['extensions']..[[@${domain_name}"/>]]);
          table.insert(xml, [[<action application="answer"/>]]);    
    	  table.insert(xml, [[<action application="export" data="voicemail_alternate_greet_id=]]..destination_number..[["/>]]);  
		  table.insert(xml, [[<action application="voicemail" data="default $${domain_name} ]]..didinfo['extensions']..[["/>]]);    
        else      
    	  table.insert(xml, [[<action application="bridge" data="{sip_invite_params=user=LOCAL,sip_from_uri=]]..didinfo['extensions']..[[@${domain_name}}sofia/default/]]..didinfo['extensions']..[[@]]..config['opensips_domain']..[["/>]]);
        end

	 elseif (tonumber(didinfo['call_type']) == 3 and didinfo['extensions'] ~= '') then

		table.insert(xml, [[<action application="set" data="calltype=SIP-DID"/>]]);     
		table.insert(xml, [[<action application="bridge" data="{sip_contact_user=]]..destination_number..[[}sofia/default/]]..destination_number..[[${regex(${sofia_contact(]]..didinfo['extensions']..[[@${domain_name})}|^[^@]+(.*)|%1)}]]..[["/>]]); 
		table.insert(xml, [[<action application="answer"/>]]);    
		table.insert(xml, [[<action application="export" data="voicemail_alternate_greet_id=]]..destination_number..[["/>]]);  
		table.insert(xml, [[<action application="voicemail" data="default $${domain_name} ]]..didinfo['extensions']..[["/>]]);
	elseif(tonumber(didinfo['call_type']) == 2 and didinfo['extensions'] ~= '') then
		table.insert(xml, [[<action application="set" data="calltype=OTHER"/>]]); 
		table.insert(xml, [[<action application="bridge" data="]]..didinfo['extensions']..[["/>]]);
	else
		--error_xml_without_cdr(destination_number,"DID_DESTINATION_NOT_FOUND","DID",config['playback_audio_notification'],userinfo['number'])

	end
	return xml
end

-- Dialplan for sip2sip calls
function freeswitch_xml_local(xml,destination_number,destinationinfo)
	table.insert(xml, [[<action application="set" data="calltype=LOCAL"/>]]);     
	table.insert(xml, [[<action application="set" data="receiver_accid=]]..destinationinfo['accountid']..[["/>]]);    
	table.insert(xml, [[<action application="bridge" data="user/]]..destination_number..[[@${domain_name}"/>]]);
	table.insert(xml, [[<action application="answer"/>]]);      
	table.insert(xml, [[<action application="voicemail" data="default ${domain_name} ]]..destination_number..[["/>]]);
	return xml
end

-- Set callerid to override in calls
function freeswitch_xml_callerid(xml,calleridinfo)    
	if (calleridinfo['cid_name'] ~= '')  then
		table.insert(xml, [[<action application="set" data="effective_caller_id_name=]]..calleridinfo['cid_name']..[["/>]]);    
	end
	if (calleridinfo['cid_number'] ~= '')  then
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
	table.insert(xml, [[<condition field="destination_number" expression="]]..destination_number..[[">]]);
	return xml
end


-- Generate voicemail dialplan
function xml_voicemail(xml,destination_number)
	local xml = {}
	xml = xml_header(xml,destination_number)
	    table.insert(xml, [[<action application="answer"/>]]);      
	    table.insert(xml, [[<action application="voicemail" data="check default ${domain_name} ]]..params:getHeader("Hunt-Username")..[["/>]]);
	xml = xml_footer(xml)	   	    
	XML_STRING = table.concat(xml, "\n");
	Logger.debug("[Dialplan] Generated XML:\n" .. XML_STRING)
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
	    Logger.debug("[Dialplan] Generated XML:\n" .. XML_STRING)
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
				table.insert(xml, [[<condition field="destination_number" expression="]]..destination_number..[[">]]);
					table.insert(xml, [[<action application="log" data="INFO ASTPP - Calling Card Call"/>]]);        
--					table.insert(xml, [[<action application="answer"/>]]);
					table.insert(xml, [[<action application="sleep" data="2000"/>]]);                    
					table.insert(xml, [[<action application="lua" data="astpp-callingcards.lua"/>]]);    
				table.insert(xml,[[</condition>]]);
				table.insert(xml,[[</extension>]]);
			table.insert(xml,[[</context>]]);
		table.insert(xml,[[</section>]]);
	table.insert(xml,[[</document>]]);
	XML_STRING = table.concat(xml, "\n");
	Logger.debug("[Dialplan] Generated XML:\n" .. XML_STRING)
end
