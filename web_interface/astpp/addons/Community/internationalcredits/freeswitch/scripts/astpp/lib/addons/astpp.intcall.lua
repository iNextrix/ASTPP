-- If international balance management module installed and destination number prefix start with defined international_prefixes then mark call as international	
function is_call_international(userinfo)
	userinfo['international_call']=false
	if(config['international_prefixes']~=nil) then		
		tmp = split(config['international_prefixes'],",")
    	for tmp_key,tmp_value in pairs(tmp) do
    		local prefix = string.sub(destination_number,0,string.len(tmp_value))
    		if (prefix==tmp_value) then
    			userinfo['international_call']=true
    			Logger.notice("[IS_CALL_INTERNATIONAL] Call ("..destination_number..") marked as international call!!");
    		end
    	end
	end
	return userinfo
end

-- If call found as international call then get balance from international balance and credit field
function get_international_balance_prefix(userinfo)	
	userinfo = is_call_international(userinfo)
	local tmp_prefix=''
	if(userinfo['international_call']==true) then
		tmp_prefix='int_'		
	end
	return tmp_prefix
end

