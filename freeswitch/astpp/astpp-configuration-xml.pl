#!/usr/bin/perl
#
# ASTPP - Open Source VoIP Billing
#
# Copyright (C) 2004/2013 www.astpp.org
#
# ASTPP Team <info@astpp.org>
#
# This program is Free Software and is distributed under the
# Terms of the GNU General Public License version 2.
############################################################

sub xml_process()
{
    my ($params,$ASTPP, $config) = @_;    
    my ($xml);    
    $xml = "";    
    if($params->{key_value} eq 'acl.conf')
    {	
	$xml = header( -type => 'text/plain' );
	$xml = $ASTPP->fs_configuration_xml_header(module=>'acl.conf',desc=>'Various Configuration',xml=>$xml);
	
	#Addded opensips parameters to add opensips ip address in ACL list
	$xml = $ASTPP->acl(module=>$params->{key_value},xml=>$xml,opensips=>$config->{opensips},opensips_ip=>$config->{opensips_ip});
	
	$xml = $ASTPP->fs_configuration_xml_footer(xml=>$xml);	
    }
    elsif($params->{key_value} eq 'sofia.conf')
    {
	$xml = header( -type => 'text/plain' );
	$xml = $ASTPP->fs_configuration_xml_header(module=>'sofia.conf',desc=>'Sip Profile',xml=>$xml);
	
	#Samir Doshi - To bind sip profile with correct fs.
	$xml = $ASTPP->sip_profile_gateway(module=>$params->{key_value},xml=>$xml,freeswitch_ip=>$params->{'FreeSWITCH-IPv4'});
	
	$xml = $ASTPP->fs_configuration_xml_footer(xml=>$xml);	
    }
    elsif($params->{key_value} eq 'post_load_modules.conf')
    {
	$xml = header( -type => 'text/plain' );
	$xml = $ASTPP->fs_configuration_xml_header(module=>'post_load_modules.conf',desc=>'Post Load Modules',xml=>$xml);
	$xml = $ASTPP->post_load_modules(module=>$params->{key_value},xml=>$xml);
	$xml = $ASTPP->fs_configuration_xml_footer(xml=>$xml);
    }
    elsif($params->{key_value} eq 'post_load_switch.conf' || $params->{key_value} eq 'switch.conf')
    {
	$xml = header( -type => 'text/plain' );
	$xml = $ASTPP->fs_configuration_xml_header(module=>'post_load_modules.conf',desc=>'Post Load Modules',xml=>$xml);
	$xml = $ASTPP->post_load_switch(module=>$params->{key_value},xml=>$xml);
	$xml = $ASTPP->fs_configuration_xml_footer(xml=>$xml);	
    }
    else{
	#Samir Doshi - To load modules properly
# 	$xml = header( -type => 'text/plain' );
# 	$xml = $ASTPP->fs_configuration_xml_header(module=>$params->{key_value},desc=>'Various Configuration',xml=>$xml);
# 	$xml = void_xml
# 	$xml = $ASTPP->fs_configuration_xml_footer(xml=>$xml);
	$xml = &void_xml();
    }
#     print STDERR $xml;
    print $xml;
}
1;