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
config = load_conf()
--load_addon_list array
addon_list = load_addon_list()

-- Include file to build xml for fs
dofile(script_path.."scripts/astpp.xml.lua");

-- Include custom file to load custom function
dofile(script_path.."lib/astpp.custom.lua");

-- Load addons files
dirname = script_path..'lib/addons/'
f = io.popen('ls ' .. dirname)
for name in f:lines() do dofile(dirname..name..'') end

if (not params) then
	params = {}
	function params:getHeader(name)
		self.name = name;
	end
	function params:serialize(name)
		self.name = name;
	end
end

if (config['debug']==2) then
    -- print all params 
    if (params:serialize() ~= nil) then
    	Logger.notice ("[xml_handler] Params:\n" .. params:serialize())
    end	

    for param_key,param_value in pairs(XML_REQUEST) do --pseudocode
    	Logger.info ("[xml_REQUEST] "..param_key..": " .. param_value)
    end
end

dofile(script_path.."scripts/astpp."..XML_REQUEST["section"]..".lua")

