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

Logger = {}
Logger.__index = Logger

function Logger.console(message) 
  Logger.print("console",message)
end

function Logger.alert(message)  
  Logger.print("alert",message)
end

function Logger.critical(message)  
  Logger.print("critical",message)
end

function Logger.error(message)  
  Logger.print("error",message)
end

function Logger.warning(message)  
  Logger.print("warning",message)
end

function Logger.notice(message)  
  Logger.print("notice",message)
end

function Logger.info(message)  
  Logger.print("info",message)
end

function Logger.debug(message)  
  Logger.print("debug",message)
end

function Logger.print(logtype,message)
	if(message ~= nil) then
	    freeswitch.consoleLog(logtype,"[ASTPP] "..message.. "\n");
	end
end
