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
use POSIX qw(strftime);

sub logger {
    my ( $log ) = @_;   
    
    #/var/log/astpp   
    if ($gbl_config->{debug} eq '1')
    {
        if ($log)
        {               
            open(my $ASTPP_LOG_FILE, '>>', '/var/log/astpp/astpp.log');
            my $now = strftime "%Y-%m-%d %H:%M:%S", localtime;
            print $ASTPP_LOG_FILE "[$now] $log\n";
            close $ASTPP_LOG_FILE;
        }
    }
}

sub print_console()
{
    my ($log) = @_;
    if($log ne ""){
	    freeswitch::consoleLog( "INFO", $log."\n" );
	    &logger($log);	    
	}
}

sub print_csv {
    my ( $log,$file ) = @_;   
    
    #/var/log/astpp   
    open(my $ASTPP_LOG_FILE, '>>', '/var/log/astpp/'.$file.'_astpp.csv');
    print $ASTPP_LOG_FILE "$log\n";
    close $ASTPP_LOG_FILE;
}

sub xml_logger {
    my ( $log ) = @_;   
    $gbl_xml_logger .= "<action application=\"log\" data=\"INFO " . $log . "\"/>\n";
    &logger($log);
}

1;
