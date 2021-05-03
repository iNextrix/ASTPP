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

-- Define script path 
local script_path = "/usr/share/freeswitch/scripts/astpp/";
local sound_path = "/usr/share/freeswitch/sounds/en/us/callie/"

-- Load config file
dofile("/var/lib/astpp/astpp.lua");

-- Load CONSTANT file
dofile("/usr/share/freeswitch/scripts/astpp/constant.lua");

-- Load json file to decode json string
JSON = (loadfile (script_path .."lib/JSON.lua"))();

-- Load utility file 
dofile(script_path.."lib/astpp.utility.lua");

-- Include Logger file to print messages 
dofile(script_path.."lib/astpp.logger.lua");

-- Include database connection file to connect database
dofile(script_path.."lib/astpp.db.lua");

-- Call database connection
db_connect()

-- Include common functions file 
dofile(script_path.."lib/astpp.functions.lua");
dofile(script_path.."lib/astpp.callingcard.functions.lua")
config = load_conf()

-- Include file to build xml for fs
dofile(script_path.."scripts/astpp.xml.lua");


--local test = string.gsub(debug.getinfo(1).short_src, "^(.+\\)[^\\]+$", "%1");
Logger.notice ("SECTION ")

if (not params) then
	params = {}
	function params:getHeader(name)
		self.name = name;
	end
	function params:serialize(name)
		self.name = name;
	end
end


if (config['debug']==1) then
    -- print all params 
    if (params:serialize() ~= nil) then
    	Logger.notice ("[xml_handler] Params:\n" .. params:serialize())
    end	
end

local userinfo

if (session:ready() ~= true) then
	return
end

session:setAutoHangup(false);

if(config['calling_cards_welcome_file'] ~= "") then
        session:streamFile(sound_path..config['calling_cards_welcome_file']);
else
        session:streamFile(sound_path.."astpp-welcome.wav");          
end

userinfo = auth_callingcard();

if (session:ready() ~= true) then
	return
end

Logger.notice("[Accountcode : ".. userinfo['number'] .."]" )
say_balance(userinfo)


--Process for dialing destination number
process_destination(userinfo);

--IVR playback 
playback_ivr(userinfo);

session:hangup();
