#!/usr/bin/perl
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################

sub xml_process()
{
    my ($user_count,$void_xml,$xml);
    
     if ($params->{'user'}) {
                  
       	my ($tmp,$sql,@results);
	    $tmp = "SELECT username,dir_params,dir_vars,number as accountcode,accountid FROM sip_devices,accounts WHERE accounts.status=0 AND accounts.deleted=0 AND accounts.id=sip_devices.accountid AND username=" . $gbl_astpp_db->quote($params->{'user'});
	    &logger($tmp);	    	    
	    $sql = $gbl_astpp_db->prepare($tmp);
	    $sql->execute;
	    while (my $record = $sql->fetchrow_hashref) {
		    push @results, $record;
	    }
	    my $rows = $sql->rows;
	    $sql->finish;
	    
        $xml = &fs_xml_header(type=>"Directory",xml=>$xml);
	    $xml .= "<domain name=\"" . $params->{domain} . "\">\n";
	    
	    if ($rows > 0) {	    	
	        foreach my $record (@results) {

		        $xml .= "<user id=\"" . $record->{username} . "\">\n";
    		        $xml .= "<params>\n";
	    	        my %params =  %{ decode_json($record->{'dir_params'}) };
	    	        while (my ($key, $value) = each %params) {	     
	    	            $xml .="<param name=\"".$key."\" value=\"".$params{$key}."\"/>\n";
        	        }		
	        	        $xml .= "<param name=\"allow-empty-password\" value=\"false\"/>\n";
	        	        $xml .= "<param name=\"domain_name\" value=\"" .$params->{domain} . "\"/>\n";
	        	        $xml .= "<param name=\"dial-string\" value=\"{sip_invite_domain=\${domain_name},presence_id=\${dialed_user}\@\${domain_name}}\${sofia_contact(*/\${dialed_user}\@\${domain_name})}\"/>\n";
	    	        $xml .= "</params>\n";
	    	        $xml .= "<variables>\n";
	    	        my %vars =  %{ decode_json($record->{'dir_vars'}) };
	    	        while (my ($key, $value) = each %vars) {	     
	    	            $xml .="<variable name=\"".$key."\" value=\"".$vars{$key}."\"/>\n";
	    	        }
	    	            $xml .= "<variable name=\"accountcode\" value=\"" . $record->{accountcode} . "\"/>\n";
	    	            $xml .= "<variable name=\"account_id\" value=\"" . $record->{accountid} . "\"/>\n";
	    	        $xml .= "</variables>\n";
    	        $xml .= "</user>\n";
	        }
        }
        $xml .= "</domain>\n";      
        $xml = &fs_xml_footer(xml=>$xml);
    }
    else {
	    $xml = &void_xml();    	
    }
    &logger($xml);
    print $xml;
    exit(0);
}
1;
