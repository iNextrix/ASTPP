#!/usr/bin/perl  
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2006, Aleph Communications
#
# Darren Wiebe <darren@aleph-com.net>
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
#
#
# This program can be called from the web browser.  It will spit out a list of
# the rates of your "default" cards from the database.  It requires a paramater
# "brand" to be included, with the brand that you want reported on.
# ie. http://127.0.0.1/cgi-bin/astpp-pricelist.cgi?brand=default
#############################################################################

use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Getopt::Long;
use Asterisk::Manager;
use Data::Paginate;
use HTML::Template;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{LANGUAGE}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
my $astpp_db;
my @output = ("STDERR");
my $copyright = "ASTPP - Open Source Voip Billing &copy;2004 Aleph Communications";


sub list_prices() {
	my ($body,$sql,$record);
    if ( $params->{brand} eq "" ) {
        $pricelist = $config->{new_user_brand};
    }
    else {
        $pricelist = $params->{brand};
    }
    $pricelistinfo = &get_pricelist($astpp_db, $pricelist);
	if ( $params->{limit} < 1 ) { $params->{limit} = 0 }
    my $results_per_page = $config->{results_per_page};
    if ( $results_per_page eq "" ) { $results_per_page = 25; }
    $body = start_form
      . "<table width=100/% align=\"left\" class=\"default\">"
      . "<tr class=\"header\"><td colspan=7>"
      . gettext("All costs are in 1/100 of a penny")
      . "</td></tr><tr class=\"header\"><td>"
      . gettext("Pattern")
      . "</td><td>"
      . gettext("Comment")
      . "</td><td>"
      . gettext("Connect Fee Includes First Increment")
      . "</td><td>"
      . gettext("Cost per minute")
      . "</td><td>"
      . gettext("Billing Increments")
      . "</td></tr>"
      . "<tr class=\"header\"><td colspan=6><a href=\"astpp-pricelist.cgi?"
      . "mode=download&brand=$pricelist\" target=\"_blank\">"
      . gettext("Download as CSV file (Right Click and select SAVE AS") . "</a>";

## START PAGINATION CODE
# set total_entries *once* then pass it around
# in the object's links from then on for efficiency:

my $verify        = CGI::param('ve') || '';
my $total_entries = int( CGI::param('te') );
my $te_match      = $total_entries ? Digest::MD5::md5_hex("unique_cypher-$total_entries-$where") : '';
if(!$total_entries || $verify ne $te_match) {
      # its not ok so re-fetch
        $sql = $astpp_db->prepare("SELECT COUNT(*) FROM routes WHERE pricelist = '$pricelist'");
        $sql->execute;
        $record = $sql->fetchrow_hashref;
        $total_entries  = $record->{"COUNT(*)"};
        $sql->finish;
        $te_match        = Digest::MD5::md5_hex("unique_cypher-$total_entries-$where");
}
# otherwise its all ok so use it
my $pgr = Data::Paginate->new({
          'total_entries' => $total_entries,
          'entries_per_page' => $results_per_page,
          'total_entries_verify_param_value' => $te_match});

# only SELECT current page's records:
$sql = "SELECT * FROM routes WHERE pricelist = '$pricelist' LIMIT " . $pgr->get_first() . ", " . $pgr->get_entries_on_this_page();
$sql = $astpp_db->prepare($sql);
$sql->execute;
# END OF PAGINATION CODE

    my $count = 0;
    while ($record = $sql->fetchrow_hashref) {
    	$count++;
    	if ( $count % 2 == 0 ) {
			$body .= "<tr class=\"rowtwo\">";
		}
		else {
			$body .= "<tr class=\"rowone\">";
		}
		$record->{pattern} =~ s/[^a-zA-Z0-9]//g;
		$record->{pattern} =~ s/^011//g;
		$body .= "<td>"
    		. $record->{pattern}
    		. "</td><td>"
    		. $record->{comment}
    		. "</td><td>"
    		. sprintf( "%.4f", $record->{connectcost} / 10000 )
    		. "</td><td>"
    		. sprintf( "%.4f", $record->{cost} / 10000 )
    		. "</td><td>"
    		. $record->{includedseconds}
    		. "/";
    	if ($record->{inc} > 0) {
    		$body .= $record->{inc};
    	} else {
    		$body .= $pricelistinfo->{inc};
    	}	
    	$body .= "</td></tr>";   
    }      
    $sql->finish;     
################
# This is the new pagination scheme but Sonia may be changing it.
#    $body .= "<a href=\"astpp-pricelist.cgi?brand=" . $pricelist . "&ve=" . $params->{ve} . "&te=" . $total_entries . "&pg=1\">"
#        . gettext("First Page") . "</a> | ";
#    $body .= "<a href=\"astpp-admin.cgi?brand=" . $pricelist . "&ve=" . $params->{ve} . "&te=" . $total_entries . "&pg=" . $pgr->get_last_page() . "\">"
#        . gettext("Last Page") . "</a>";
#    $body .= "</td></tr><tr><td>";
#    $body .= scalar $pgr->get_navi_html();
################
		for ( my $i = 0 ; $i <= $pagesrequired - 1 ; $i++ ) {
		if ( $i == 0 ) {
			if ( $params->{limit} != 0 ) {
				$body .=
				    "<a href=\"astpp-pricelist.cgi?limit=0\">";
				$body .= $i + 1;
				$body .= "</a>";
			}
			else {
				$body .= $i + 1;
			}
		}
		if ( $i > 0 ) {
			if ( $params->{limit} != ( $i * $results_per_page ) ) {
				$body .=
				    "<a href=\"astpp-pricelist.cgi?limit=";
				$body .= ( $i * $results_per_page );
				$body .= "\">\n";
				$body .= ($i + 1) . "</a>";
			}
			else {
				$pageno = $i + 1;
				$body .= " |";
			}
		}
	}    
    $body .= "</table>";
    return $body;
}

########################### The program starts here ##################
my ($body,$status);
foreach $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n";
}
$config = &load_config;
$astpp_db = &connect_db($config,@output);
$config     = &load_config_db($astpp_db,$config);


if ($params->{mode} eq gettext("download")) {
	print header("text/csv");
     $tmp = "SELECT * FROM routes WHERE reseller IN (NULL,'') AND pricelist = "
          	. $astpp_db->quote($params->{brand})
		. " ORDER BY comment";
	print STDERR $tmp;
	 $sql = $astpp_db->prepare($tmp);
    $sql->execute
          || return gettext("Something is wrong with the routes database!")
          . "\n";
    my $count = 0;
    while ($record = $sql->fetchrow_hashref) {
 		$record->{pattern} =~ s/[^a-zA-Z0-9]//g;
		$record->{pattern} =~ s/^011//g;
		$body .= "\"" . $record->{pattern} . "\",\""
    		. "\",\""
    		. $record->{comment}
    		. "\",\""
    		. sprintf( "%.4f", $record->{connectcost} / 10000 )
    		. "\",\""
    		. sprintf( "%.4f", $record->{cost} / 10000 )
    		. "\",\""
    		. $record->{includedseconds}
    		. "\",\"";
    	if ($record->{inc} > 0) {
    		$body .= $record->{inc};
    	} else {
    		$body .= $pricelistinfo->{inc};
    	}	   	
    	$body .= "\"\n\r";
    }
    print $body;
} else {
	print header();
	$body = &list_prices();
	if (!$astpp_db) {
	$status .= "<i>" . gettext("Database Unavailable!") . "</i>\n";
}	
	

print "<title>ASTPP - Open Source Voip Billing Installation</title>\n"
  . "<STYLE TYPE=\"text/css\">\n"
  . "<!--\n"
  . "  \@import url(/_astpp/style.css); \n" . "-->\n"
  . "</STYLE>\n"
  . "<BODY>\n"
  . "<table><tr><td><img src=\"/_astpp/logo.jpg\"></td>"
  . "<td align=center><H2>ASTPP - Open Source Voip Billing (Pricelist)</H2></td>"
  . "</tr></table>"
  . "<table align=\"left\" width=100\%>"
  . "<tr><td><H2></H2></td></tr></table>\n"
  . $body
  . "</table>" . "<hr>"
  . "<table align=\"left\" width=100\%><tr><td><COPYRIGHT>$copyright</COPYRIGHT></td></tr></table>"
  . "</BODY>";

	
}		


