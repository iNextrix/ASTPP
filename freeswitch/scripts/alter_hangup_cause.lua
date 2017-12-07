-- process_480.lua
-- check for response code and send different one back
--
uuid = argv[1];
api = freeswitch.API();
sip_invite_failure_status = api:executeString("uuid_getvar "..uuid.." sip_invite_failure_status");
if sip_invite_failure_status == "486" then
    reply = api:executeString("uuid_setvar "..uuid.." last_bridge_proto_specific_hangup_cause sip:503");
end
