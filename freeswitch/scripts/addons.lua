-- ASTPPCOM-1170 Ashish start
local ringgroup = "/usr/local/freeswitch/scripts/astpp/lib/addons/astpp.ringgroup.lua"
local f=io.open(ringgroup,"r")
if f~=nil then
    dofile("/usr/local/freeswitch/scripts/astpp/lib/addons/astpp.ringgroup.lua")
    return true 
else
    return false
end

local siprouting = "/usr/local/freeswitch/scripts/astpp/lib/addons/sip_routing.lua"
local f=io.open(siprouting,"r")
if f~=nil then
    dofile("/usr/local/freeswitch/scripts/astpp/lib/addons/sip_routing.lua")
    return true 
else
    return false
end
-- ASTPPCOM-1170 Ashish End