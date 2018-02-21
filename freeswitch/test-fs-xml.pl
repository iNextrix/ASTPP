#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2008, Aleph Communications
#
# Darren Wiebe (darren@aleph-com.net)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################

use Time::HiRes qw( gettimeofday tv_interval );
#use HTTP::Request;
#use LWP::UserAgent;
#use URI::URL;
#use WWW::Curl;
use LWP::Simple;


my $starttime = [gettimeofday];

#my $url = 'http://localhost/cgi-bin/astpp-fs-xml.pl?section=dialplan&Caller-Destination-Number=123456789&variable_accountcode=1000';
my $url = 'http://localhost/perl/astpp-fs-xml.pl?section=dialplan&Caller-Destination-Number=123456789&variable_accountcode=1000';

#my $req = HTTP::Request->new(POST, $url, $headers);
#my $ua = LWP::UserAgent->new();
#my $resp = $ua->request($req);
#if ($resp->is_success) {
#
#	print $resp->content;
#} else {
#	print $resp->message;
#}

my $count = 0;
while ($count < 500) {
print $count;
$count++;
my $content = get $url;
print $content;
}

my $generation_time = tv_interval($starttime);
print "\n\n" . $generation_time . "\n\n";
