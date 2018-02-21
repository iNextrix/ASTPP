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

use strict;
use warnings;
use CGI qw/:standard Vars/;


#Configuration xml header
sub fs_xml_header
{
    my (%arg) = @_;	
	$arg{xml} .= header( -type => 'text/plain' );
	$arg{xml} .= "<?xml version=\"1.0\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"".$arg{type}."\" description=\"".$arg{type}."\">\n";
	return $arg{xml};
}

#Configuration xml footer
sub fs_xml_footer
{
	my (%arg) = @_;
	$arg{xml} .= "</section>\n";
	$arg{xml} .= "</document>\n";
	return $arg{xml};
}

#return not found xml
sub void_xml
{
    my ($void_xml);
    $void_xml = header( -type => 'text/plain' );
    $void_xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
    $void_xml .= "<document type=\"freeswitch/xml\">\n";
    $void_xml .= "<section name=\"result\">\n";
    $void_xml .= "<result status=\"not found\" />";
    $void_xml .= "</section>\n";
    $void_xml .= "</document>\n";
    return $void_xml;
}

#Dialplan xml header
sub fs_dialplan_xml_header
{
    my (%arg) = @_;	
    
    my $now = &now();
    
	$arg{xml} .= header( -type => 'text/plain' );
	$arg{xml} .= "<?xml version=\"1.0\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"dialplan\" description=\"Dialplan\">\n";
	$arg{xml} .= "<context name=\"".$arg{context}."\">\n";
#	$arg{xml} .= "<extension name=\"" . $arg{destination_number} . "\">\n";
#	$arg{xml} .= "<condition field=\"destination_number\" expression=\"" . $arg{destination_number} . "\">\n";	
	$arg{xml} .= "<extension name=\\\"" . $params->{'Caller-Destination-Number'} . "\">\n";
    	$arg{xml} .= "<condition field=\"destination_number\" expression=\\\"" . $params->{'Caller-Destination-Number'} . "\">\n";
	$arg{xml} .= "<action application=\"set\" data=\"callstart=$now\"/>\n";
	$arg{xml} .= "<action application=\"set\" data=\"call_processed=internal\"/>\n";
	$arg{xml} .= "<action application=\"set\" data=\"originated_destination_number=$arg{destination_number}\"/>\n";	
	$arg{xml} .= "<action application=\"set\" data=\"hangup_after_bridge=true\"/>\n";
	
	if(defined $arg{calltype} && $arg{calltype} eq "STANDARD")
	{    	
    	$arg{xml} .= "<action application=\"set\" data=\"continue_on_fail=true\"/>\n";
	}

	$arg{xml} .= "<action application=\"set\" data=\"accountid=" . $arg{accountid} . "\"/>\n" if(defined($arg{accountid}));
	$arg{xml} .= "<action application=\"set\" data=\"account_type=" . $arg{account_type} . "\"/>\n" if(defined($arg{account_type}));
	$arg{xml} .= "<action application=\"set\" data=\"resellerid=" . $arg{resellerid} . "\"/>\n" if(defined($arg{resellerid}));
	$arg{xml} .= "<action application=\"set\" data=\"accountcode=" . $arg{accountcode} . "\"/>\n" if(defined($arg{accountcode}));
	$arg{xml} .= "<action application=\"set\" data=\"call_direction=" . $arg{call_direction} . "\"/>\n" if(defined($arg{call_direction}));	
	$arg{xml} .= "<action application=\"set\" data=\"calltype=" . $arg{calltype} . "\"/>\n" if(defined($arg{calltype}));
    $arg{xml} .= "<action application=\"sched_hangup\" data=\"+" . sprintf( "%.0f", $arg{max_length} * 60 ) . " allotted_timeout\"/>\n" if($arg{max_length});
    
    $arg{xml} .= "<action application=\"export\" data=\"origination_caller_id_name=".$arg{outbound_callerid}->{callerid_name}."\"/>\n" if($arg{outbound_callerid}->{callerid_name});
	$arg{xml}.= "<action application=\"export\" data=\"origination_caller_id_number=".$arg{outbound_callerid}->{callerid_number}."\"/>\n" if($arg{outbound_callerid}->{callerid_number});

	return $arg{xml};
}

#Dialplan xml footer
sub fs_dialplan_xml_footer
{
	my (%arg) = @_;
	$arg{xml} .= "</condition>\n";
	$arg{xml} .= "</extension>\n";
	$arg{xml} .= "</context>\n";
	$arg{xml} .= "</section>\n";
	$arg{xml} .= "</document>\n";
	return $arg{xml};
}

#Generating DID dialplan
sub fs_dialplan_xml_did() {
	my (%arg) = @_;
	my ($xml);
	
    $xml = &fs_dialplan_xml_header(
        context => $arg{context},
        accountcode => $arg{accountcode},
        accountid => $arg{accountid},
        resellerid => $arg{resellerid},
        account_type => $arg{account_type},        
        destination_number => $arg{destination_number},
        max_length => $arg{max_length},
        calltype=>'DID',
        call_direction=>'inbound',
        xml => $xml
    );
	$xml .= "<action application=\"set\" data=\"effective_destination_number=$arg{destination_number}\"/>\n";
    if ($arg{gbl_xml_channels} ne "")
    {
        $xml .= $arg{gbl_xml_channels};
    }
    $xml .= "<action application=\"set\" data=\"origination_rates=".$arg{origination_dp_string}."\"/>\n";
	#PSTN Call
	if($arg{call_type} == '0')
	{
		$xml .= "<action application=\"transfer\" data=\"" . $arg{extensions} ." XML default\"/>\n";
	}
	#Local call
	elsif($arg{call_type} == '1')
	{ 			
		#$xml .= "<action application=\"bridge\" data=\"user/$arg{extensions}\@\${domain_name}\"/>\n";
		$xml .= "<action application=\"bridge\" data=\"\{sip_invite_to_uri=<sip:$arg{destination_number}\@\$\${domain_name}>\}user/$arg{extensions}\@\$\${domain_name}\"/>\n";
	}
	#Any other option
	else{		
		$xml .= "<action application=\"bridge\" data=\"" . $arg{extensions} . "\"/>\n";
	}
	
	$xml = &fs_dialplan_xml_footer(xml=>$xml);
	&logger($xml);
    print $xml;
    exit();
}

sub fs_dialplan_xml_bridge() {
    my (%arg) = @_;
	my ( $xml);	
		
	if ($arg{termination_rate}->{dialed_modify} && $arg{termination_rate}->{dialed_modify} ne "") {
        $arg{destination_number}= &number_translation('destination_number'=>$arg{destination_number},'translation'=>$arg{termination_rate}->{dialed_modify});
	}	
		
	if ($arg{termination_rate}->{strip} && $arg{termination_rate}->{strip} ne "") {
        $arg{destination_number}= &number_translation('destination_number'=>$arg{destination_number},'translation'=>"$arg{termination_rate}->{strip}/");
	}
	
	if ($arg{termination_rate}->{prepend} && $arg{termination_rate}->{prepend} ne "") {
        $arg{destination_number}= $arg{termination_rate}->{prepend} . $arg{destination_number};
	}
	
	#$xml .= "<action application=\"set\" data=\"effective_destination_number=$arg{destination_number}\"/>\n";
	$xml .= "<action application=\"set\" data=\"effective_destination_number=$params->{'Caller-Destination-Number'}\"/>\n";	
    $xml .= "<action application=\"set\" data=\"termination_rates=".$arg{termination_dp_string}."\"/>\n";
    
#	if($arg{termination_rate}->{maxchannels} > 0)
#	{
#	   $xml .= "<action application=\"limit\" data=\"db ".$arg{termination_rate}->{path}." gw_".$arg{termination_rate}->{path}." ".$arg{termination_rate}->{maxchannels}."\"/>\n";
#	}


$xml .= ($arg{termination_rate}->{codec} ne '') ? "<action application=\"set\" data=\"absolute_codec_string=".$arg{termination_rate}->{codec}."\"/>\n" : '';
	if($arg{termination_rate}->{maxchannels} > 0)
        {
                $xml .= "<action application=\"limit_execute\" data=\"db ".$arg{termination_rate}->{path}." gw_".$arg{termination_rate}->{path}." ".$arg{termination_rate}->{maxchannels}." bridge sofia/gateway/" . $arg{termination_rate}->{path} . "/" . $arg{destination_number}."\"/>\n";
       }else{
                $xml .= "<action application=\"bridge\" data=\"";
                $xml .= "sofia/gateway/" . $arg{termination_rate}->{path} . "/" . $arg{destination_number};
                $xml .= "\"/>\n";
        }

	if (defined($arg{termination_rate}->{path1}) && $arg{termination_rate}->{path1} ne $arg{termination_rate}->{path})
	{
	    	$xml .= "<action application=\"bridge\" data=\"";	
	    	$xml .= "sofia/gateway/" . $arg{termination_rate}->{path1} . "/" . $arg{destination_number};
	    	$xml .= "\"/>\n";
	}	
	if (defined($arg{termination_rate}->{path2}) && $arg{termination_rate}->{path2} ne $arg{termination_rate}->{path})
	{
	    	$xml .= "<action application=\"bridge\" data=\"";	
	    	$xml .= "sofia/gateway/" . $arg{termination_rate}->{path2} . "/" . $arg{destination_number};
	    	$xml .= "\"/>\n";
	}
	
	return ($xml);	
}

#Generating Standard dialplan
sub fs_dialplan_xml_standard() {
	my (%arg) = @_;
	my ($xml);
	
    $xml = &fs_dialplan_xml_header(
        context => $arg{context},
        accountcode => $arg{accountcode},
        accountid => $arg{accountid},
        resellerid => $arg{resellerid},
        account_type => $arg{account_type},
        destination_number => $arg{destination_number},
        max_length => $arg{max_length},
        outbound_callerid=>$arg{outbound_callerid},
        calltype=>'STANDARD',
        call_direction=>'outbound',
        xml => $xml
    );
    
    $xml .= $arg{gbl_xml_logger};
            
    $xml .= "<action application=\"set\" data=\"origination_rates=".$arg{origination_dp_string}."\"/>\n";
    
    if ($arg{gbl_xml_channels} ne "")
    {
        $xml .= $arg{gbl_xml_channels};
    }
    
	$xml .= $arg{bridge_string};
	
	$xml = &fs_dialplan_xml_footer(xml=>$xml);
	&logger($xml);
    print $xml;
    exit();
}

#set max channels in dialplan 
sub set_max_channels()
{
    my (%arg) = @_;  
    if($arg{maxchannels} > 0)
    {
	my $dstr = $arg{maxchannels};
	if($arg{interval} > 0){
		$dstr = $arg{maxchannels}."/".$arg{interval};
	}
        $gbl_xml_channels .= "<action application=\"limit\" data=\"db ".$arg{name}." db_".$arg{name}." ".$dstr."\"/>\n";
    }
}

#return not found xml
sub error_xml_without_cdr
{
    my ($destination_number,$error_code) = @_;
    
    $destination_number = ($destination_number or "");
    
    my ($xml,$log_type,$log_message,$hangup_cause,$audio_file);
    $audio_file = "";
    
    if ($astpp_calltype ne "ASTPP-CALLINGCARD"){
        $xml = &fs_dialplan_xml_header(xml=>$xml,destination_number=>$destination_number,context=>$params->{'Caller-Context'});
    }
    
    if($error_code eq "AUTHENTICATION_FAIL")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}." is not authenticated!!";
        $hangup_cause = "CALL_REJECTED";
    }
    elsif($error_code eq "ACCOUNT_INACTIVE_DELETED")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}." is either inactive or deleted!!";
        $hangup_cause = "CALL_REJECTED";
    }
    elsif($error_code eq "ACCOUNT_EXPIRE")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}.". Account has been expired!!";
        $hangup_cause = "CALL_REJECTED";
        $audio_file = $sound->{card_has_expired};
    }
    elsif($error_code eq "NO_SUFFICIENT_FUND")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}." doesn't have sufficiant fund!!";
        $hangup_cause = "NORMAL_CLEARING";
        $audio_file = $sound->{not_enough_credit};
    }
    elsif($error_code eq "DESTINATION_BLOCKED")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}.". Dialed number destination ($destination_number) is blocked for account!!";
        $hangup_cause = "CALL_REJECTED";
        $audio_file = $sound->{badnumber};
    }
    elsif($error_code eq "ORIGNATION_RATES_NOT_FOUND")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}.". Dialed number ($destination_number) origination rates not found!!";
        $hangup_cause = "CALL_REJECTED";
        $audio_file = $sound->{destination_incorrect};
    }
    elsif($error_code eq "RESELLER_COST_CHEAP")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}.". Destination number ($destination_number), Reseller call is priced too cheap! Call being barred!!";
        $hangup_cause = "CALL_REJECTED";
        $audio_file = $sound->{destination_incorrect};
    }
    elsif($error_code eq "TERMINATION_RATES_NOT_FOUND")
    {
        $log_type = "WARNING";
        $log_message = "Accountcode ". $params->{variable_accountcode}.". Dialed number ($destination_number) termination rates not found!!";
        $hangup_cause = "CALL_REJECTED";
        $audio_file = $sound->{destination_incorrect};
    }
    
    if ($astpp_calltype ne "ASTPP-CALLINGCARD"){
	    $xml .= "<action application=\"log\" data=\"".$log_type." ".$log_message."\"/>\n";
	    $xml .= "<action application=\"playback\" data=\"".$audio_file."\"/>\n";
	    $xml .= "<action application=\"set\" data=\"process_cdr=false\"/>\n";
	    $xml .= "<action application=\"hangup\" data=\"".$hangup_cause."\"/>\n";
        $xml = &fs_dialplan_xml_footer(xml=>$xml);
        &logger($xml);
        print $xml;
        exit();
    }else{
        $session->streamFile( $audio_file ) if ($audio_file ne "");
    }
}

sub generate_cc_dialplan()
{
    my (%arg)=@_;
    my ($xml);    
    $xml = header( -type => 'text/plain' );
	$xml .= "<?xml version=\"1.0\"?>\n";
	$xml .= "<document type=\"freeswitch/xml\">\n";
	$xml .= "<section name=\"dialplan\" description=\"Dialplan\">\n";
	$xml .= "<context name=\"".$arg{context}."\">\n";
	$xml .= "<extension name=\"" . $arg{destination_number} . "\">\n";
	$xml .= "<condition field=\"destination_number\" expression=\"" . $arg{destination_number} . "\">\n";	
	$xml .= "<action application=\"log\" data=\"INFO ASTPP - Calling Card Call\"/>\n";
	$xml .= "<action application=\"answer\"/>\n";
    $xml .= "<action application=\"perl\" data=\"astpp-callingcards.pl\"/>\n";
	$xml .= "</condition>\n";
	$xml .= "</extension>\n";
	$xml .= "</context>\n";
	$xml .= "</section>\n";
	$xml .= "</document>\n";
	&logger($xml);
    print $xml;
    exit();
}

1;
