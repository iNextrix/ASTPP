#!/usr/bin/perl
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd. - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
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

    my ($xml);    
    if($params->{key_value} eq 'acl.conf')
    {	
    	$xml = &fs_xml_header(type=>"Configuration",xml=>$xml);
	
        my ($row,$tmp,$sql,$tmp_gw,$sql_gw,$row_gw);
        $xml .= "<configuration name=\"acl.conf\" description=\"Network Lists\">\n";
        $xml .= "<network-lists>\n";
        $xml .= "<list name=\"default\" default=\"deny\">\n";           
        
        #Add customer ip address
        $tmp = "SELECT ip FROM ip_map,accounts WHERE ip_map.accountid=accounts.id AND accounts.status=0 AND deleted=0";
        $sql = $gbl_astpp_db->prepare($tmp);
        $sql->execute;
        while ( $row = $sql->fetchrow_hashref ) {
	     chomp($row->{ip});
	     if( $row->{ip} =~ m/^(\d\d?\d?)\.(\d\d?\d?)\.(\d\d?\d?)\.(\d\d?\d?)\/(\d\d)$/)
	     {			             
	    	     $xml .= "<node type=\"allow\" cidr=\"".$row->{ip}."\"/>\n";
	     }
        }
        
        #Add gateway ip address
        $tmp_gw = "SELECT * FROM gateways WHERE status=0";
        $sql_gw = $gbl_astpp_db->prepare($tmp_gw);
        $sql_gw->execute;
        while ( $row_gw = $sql_gw->fetchrow_hashref ) {	
	        my %data_gw =  %{ decode_json($row_gw->{gateway_data}) };
	        while (my ($key_gw, $value_gw) = each %data_gw) {	     	    
                if($key_gw eq 'proxy')
                {
			        chomp($data_gw{$key_gw});
	                if( $data_gw{$key_gw} =~ m/^(\d\d?\d?)\.(\d\d?\d?)\.(\d\d?\d?)\.(\d\d?\d?)$/ )
          		    {
	         	       $xml .= "<node type=\"allow\" cidr=\"".$data_gw{$key_gw}."/32\"/>\n";
                	}
                 }	
             }
	    }
        
        #Add opensips ip address if opensips is enable
        if($gbl_config->{opensips} eq '1')
        {
	        $xml .= "<node type=\"allow\" cidr=\"".$gbl_config->{opensips_ip}."/32\"/>\n";
        }
        
        $xml .= "</list>\n";
        $xml .= "</network-lists>\n";
        $xml .= "</configuration>\n";	
    	$xml = &fs_xml_footer(xml=>$xml);	
    }
    elsif($params->{key_value} eq 'sofia.conf')
    {
	    $xml = &fs_xml_header(type=>"Configuration",xml=>$xml);
	
        my ($row,$tmp,$sql,$sql_gw,$tmp_gw,$row_gw);
        $xml .= "<configuration name=\"sofia.conf\" description=\"SIP Profile\">\n";
        $xml .= "<profiles>\n";
                
        $tmp = "SELECT * FROM sip_profiles WHERE status=0";
        
        $sql = $gbl_astpp_db->prepare($tmp);
        $sql->execute;
            
        while ( $row = $sql->fetchrow_hashref ) {
	
	    $xml .= "<profile name=\"".$row->{name}."\">\n";
	    $xml .= "<settings>\n";
	    $xml .= "<param name=\"sip-ip\" value=\"".$row->{sip_ip}."\"/>\n";
	    $xml .= "<param name=\"sip-port\" value=\"".$row->{sip_port}."\"/>\n";
     	my %data =  %{ decode_json($row->{profile_data}) };
	        while (my ($key, $value) = each %data) {	     
	             $xml .="<param name=\"".$key."\" value=\"".$data{$key}."\"/>\n";
	        }	
	        $xml .= "</settings>\n";
	        $xml .= "<gateways>\n";
	        $tmp_gw = "SELECT * FROM gateways WHERE sip_profile_id='".$row->{'id'}."' AND status=0";
	        $sql_gw = $gbl_astpp_db->prepare($tmp_gw);
	        $sql_gw->execute;
	        while ( $row_gw = $sql_gw->fetchrow_hashref ) {
	            $xml .= "<gateway name=\"".$row_gw->{name}."\">\n";
	            my %data_gw =  %{ decode_json($row_gw->{gateway_data}) };
	            while (my ($key_gw, $value_gw) = each %data_gw) {	     
		        $xml .="<param name=\"".$key_gw."\" value=\"".$data_gw{$key_gw}."\"/>\n";
	            }
	            $xml .= "</gateway>\n";
	        }
	        $xml .= "</gateways>\n";
	        $xml .= "<domains>\n";
	        $xml .= "<domain name=\"all\" alias=\"true\" parse=\"false\"/>\n";
	        $xml .= "</domains>\n";
	        $xml .= "</profile>\n";	
        }
        $xml .= "</profiles>\n";
        $xml .=  "</configuration>\n";
        $xml = &fs_xml_footer(xml=>$xml);	
    }
    else{
	    $xml = &void_xml();
    }
    &logger($xml);
    print $xml;
}
1;
