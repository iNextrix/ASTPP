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
    my ($user_count,$void_xml,$xml);
    
    $xml = header( -type => 'text/plain' );
    $user_count = 0;
    if ($params->{'user'}) {
       $xml = $ASTPP->fs_directory_xml_header( xml => $xml );
       ($xml,$user_count) = $ASTPP->fs_directory_xml(
           xml    => $xml,
           ip     => $params->{'ip'},
           user   => $params->{'user'},
           domain => $params->{'domain'},
	   debug  => $config->{debug}
       );
       $xml = $ASTPP->fs_directory_xml_footer( xml => $xml );
    }
        
    if ($user_count > 0) {    	
	$ASTPP->debug(debug =>$xml);
    	print $xml;
	exit(0);
    } else {
	$void_xml = &void_xml();    	
	$ASTPP->debug(debug =>$void_xml);
	print $void_xml;
	exit(0);
    }
}
1;