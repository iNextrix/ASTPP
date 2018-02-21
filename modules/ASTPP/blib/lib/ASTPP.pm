package ASTPP;

require 5.004;
use strict;
use warnings;
use DBI;
#use Locale::gettext_pp qw(:locale_h);
use Data::Dumper qw( Dumper );
use JSON;
#bindtextdomain( "astpp", "/usr/local/share/locale" );
#textdomain("astpp");

require Exporter;

our @ISA = qw(Exporter);

# Items to export into callers namespace by default. Note: do not export
# names by default without a very good reason. Use EXPORT_OK instead.
# Do not simply export all your public functions/methods/constants.

# This allows declaration       use ASTPP ':all';
# If you do not need this, moving things directly into @EXPORT or @EXPORT_OK
# will save memory.
our %EXPORT_TAGS = ( 'all' => [ qw(
load_config new set_verbosity_level set_verbosity	
) ] );

our @EXPORT_OK = ( @{ $EXPORT_TAGS{'all'} } );

our @EXPORT = qw(
	
);

our $VERSION = '0.01';


=head1 NAME

ASTPP - Perl extension for ASTPP (www.astpp.org).
Module contains functions to assist with the operation of ASTPP (www.astpp.org)

=head1 EXPORT

Everything by default.


=head1 SYNOPSIS

use ASTPP;

$ASTPP = new ASTPP;

=head1 DESCRIPTION

This module should make it easier to write scripts that interact with ASTPP

=head1 MODULE COMMANDS

=over 4

=cut

sub new
{
	my ($class,%arg) = @_;
	bless {
		_astpp_db               => $_[1],
		_freeswitch_db          => $_[1],
		_verbosity_level        => $arg{verbosity_level}                || 1,
# 		_asterisk_agi           => $_[3],
		_cdr_db                 => $_[4],
# 		_verbosity_item_level   => $arg{verbosity_item_level}           || 0,
# 		_script                 => $arg{script}         || "astpp-admin.cgi",
		_config                 => $_[7],
	}, $class;
}

sub set_verbosity       { 
      my ($self,%arg) = @_;
      $self->{_verbosity_level} = $arg{verbosity_level};
}  #Sets the verbosity level. 

=item $ASTPP->set_astpp_db()

Pushes the ASTPP database connection into module for internal use

Example: $ASTPP->set_astpp_db($astpp_db)

=cut

sub set_astpp_db
{
	my ($self, $astpp_db) = @_;
	$self->{_astpp_db} = $astpp_db if $astpp_db;
}

=item $ASTPP->set_freeswitch_db()

Pushes the Freeswitch database connection into module for internal use

Example: $ASTPP->set_freeswitch_db($freeswitch_db)

=cut

sub set_freeswitch_db
{
	my ($self, $freeswitch_db) = @_;
	$self->{_freeswitch_db} = $freeswitch_db if $freeswitch_db;
}

=item $ASTPP->set_cdr_db()

Pushes the cdr database connection into module for internal use

Example: $ASTPP->set_cdr_db($cdr_db)

=cut

sub set_cdr_db
{
	my ($self, $cdr_db) = @_;
	$self->{_cdr_db} = $cdr_db if $cdr_db;
}

=item $ASTPP->set_asterisk_agi()

Pushes the Asterisk AGI connection into module for internal use

Example: $ASTPP->set_asterisk_agi($AGI)

=cut

sub set_asterisk_agi
{
	my ($self, $asterisk_agi) = @_;
	$self->{_asterisk_agi} = $asterisk_agi if $asterisk_agi;
}


 sub set_config
 {
#       my ($self, %config_hash) = @_;
#       $self->{_config} = %config_hash if %config_hash;
}


=item $ASTPP->ip_address_authenticate()

Authenticates call by caller ip address.  Works with both Asterisk(tm) and
Freeswitch(tm).

Example:
$ipdata = $ASTPP->ip_address_authenticate(
	ip_address      => "192.168.1.1",
	destination     => "18005551212"
)

=cut

sub ip_address_authenticate
{
	my ($self, %arg) = @_;
	my ($sql,$tmp,$record);
	$arg{ip_address} = $arg{ip} if $arg{ip};  #Freeswitch passes the ip in a different format.
	$tmp = "SELECT ip_map.*, accounts.number as account_code FROM ip_map,accounts WHERE accounts.id=ip_map.accountid AND ip = " . $self->{_astpp_db}->quote($arg{ip_address})
		. " AND prefix IN (NULL,'') OR ip = " . $self->{_astpp_db}->quote($arg{ip_address});
	$tmp .= " AND " . $self->{_astpp_db}->quote($arg{destination}) . " RLIKE prefix" if $arg{destination};
	$tmp .= " AND accounts.status=1 ORDER BY LENGTH(prefix) DESC LIMIT 1";
#	print STDERR $tmp . "\n";
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	return $record;
}

=item $ASTPP->fs_dialplan_xml_header()

Return the opening lines of the Freeswitch(TM) xml dialplan.  If a call is
inbound via a DID or if we're authenticating via IP address we need to be in the
public context instead of the default context.

Example:
$xml .= $ASTPP->fs_dialplan_xml_header(
	DID     => $diddata->{number},
	IP      => $ipdata->{account},
	destination_number => $dialed_number
)

=cut

sub fs_dialplan_xml_header
{
	my ($self, %arg) = @_;
	$arg{xml} .= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"dialplan\" description=\"ASTPP Dynamic Routing\">\n";
	$arg{xml} .= "<context name=\"".$arg{context}."\">\n";
	$arg{xml} .= "<extension name=\"" . $arg{destination_number} . "\">\n";
	$arg{xml} .= "<condition field=\"destination_number\" expression=\"" . $arg{destination_number} . "\">\n";
	$arg{xml} .= "<action application=\"set\" data=\"effective_destination_number=" . $arg{destination_number} . "\"/>\n";	
	
	return $arg{xml};
}

=item $ASTPP->fs_dialplan_xml_timelimit()

Return lines of the Freeswitch(TM) xml dialplan that set the accountcode as well
as limit the length of the call.

Example:
$xml .= $ASTPP->fs_dialplan_xml_timelimit(
	accountcode     => $carddata->{number},
	max_length      => $maxlength
)

=cut

    
sub fs_dialplan_xml_timelimit() {
	my ($self, %arg) = @_;
	$arg{xml} .= "<action application=\"sched_hangup\" data=\"+" . sprintf( "%.0f", $arg{max_length} * 60 ) . " allotted_timeout\"/>\n";
	$arg{xml} .= "<action application=\"export\" data=\"accountcode=" . $arg{accountcode} . "\"/>\n";
	return $arg{xml};
}

=item $ASTPP->fs_dialplan_xml_did()

Return the dialplan code for an incoming call to a DID.

Example:
$xml .= $ASTPP->fs_dialplan_xml_did(
	did             => $destination,
	accountcode     => $carddata->{number}
)

=cut

sub fs_dialplan_xml_did() {
	my ($self, %arg) = @_;
	my ( $xml,$sql, $trunkdata, $dialstring,$data );
	my @variables = split /,(?!(?:[^",]|[^"],[^"])+")/, $arg{variables};
	foreach my $variable (@variables) {		
		$xml .= "<action application=\"set\" data=\"" . $variable . "\"/>\n";
	}	
# 	$xml .= "<action application=\"set\" data=\"inherit_codec=true\"/>\n";	

	#PSTN Call
	if($arg{call_type} == '0')
	{
		#my ($pstn_number,$sep2,$context) = split(/([@])/, $arg{extensions});
		my $pstn_number = $arg{extensions};
		$xml .= "<action application=\"transfer\" data=\"" . $pstn_number ." XML default\"/>\n";
	}
	#Local call
	elsif($arg{call_type} == '1')
	{ 			
		my $user = $arg{extensions};
		$xml .= "<action application=\"bridge\" data=\"sofia/default/".$arg{did}."\${regex(\${sofia_contact(".$user.")}|^[^\@]+(.*)|\%1)}\"/>\n";
	}
	#Any other option
	else{		
		$xml .= "<action application=\"bridge\" data=\"" . $arg{extensions} . "\"/>\n";
	}
	return $xml;    
}

sub fs_dialplan_xml_bridge_start() {
	my ($self, %arg) = @_;
	my $dialstring .= "<action application=\"set\" data=\"hangup_after_bridge=false\"/>\n";
#	$dialstring .= "<action application=\"set\" data=\"ignore_early_media=true\" />\n";
	$dialstring .= "<action application=\"set\" data=\"continue_on_fail=true\"/>\n";

	$dialstring .= "<action application=\"export\" data=\"origination_caller_id_name=".$arg{origination_caller_id_name}."\"/>\n" if($arg{origination_caller_id_name});
	$dialstring .= "<action application=\"export\" data=\"origination_caller_id_number=".$arg{origination_caller_id_number}."\"/>\n" if($arg{origination_caller_id_number});
	return $dialstring;
}

sub fs_dialplan_xml_bridge_end() {
# 	my $dialstring = "\"/>\n";
	return my $dialstring;
}


sub trim($)
{
        my $string = shift;
        $string =~ s/^\s+//;
        $string =~ s/\s+$//;
        return $string;
}

=item $ASTPP->fs_dialplan_xml_bridge()

Return the bridge command along with details.  This is only called if a call is approved.

Example:
$xml .= $ASTPP->fs_dialplan_xml_bridge(
      destination_number => $params->{'Caller-Destination-Number'},
      route_prepend      => $route->{prepend},
      trunk_name         => $route->{trunk},
      route_id	   	 => $route->{id},
      count		 => $count,
      provider 	   	 => $route->{provider}	
);

=cut

sub fs_dialplan_xml_bridge() {
	my ($self, %arg) = @_;
	my ( $sql, $trunkdata, $dialstring,$data,$callcount );
	
	$arg{route_prepend} = "" if !$arg{route_prepend};	
	if ($arg{trunk_dialed_modify} && $arg{trunk_dialed_modify} ne "") {
		my @regexs = split(m/","/m, $arg{trunk_dialed_modify});
		foreach my $regex (@regexs) {
			$regex =~ s/"//g;                               #Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
			$arg{destination_number} =~ s/$grab/$replace/is;
		}
	}
	
	$callcount = `/usr/local/bin/fs_cli -x 'limit_usage db $arg{trunk_path} gw_$arg{trunk_path}'`;      
	$callcount = &trim($callcount);
	if($arg{trunk_maxchannels} > 0 && $callcount >= $arg{trunk_maxchannels})
	{	  
	   return $dialstring;
	}
	$dialstring .= "<action application=\"set\" data=\"outbound_route=" . $arg{route_id} . "\"/>\n";
	$dialstring .= "<action application=\"set\" data=\"trunk=" . $arg{trunk_id} . "\"/>\n";
	$dialstring .= "<action application=\"set\" data=\"provider=" . $arg{trunk_provider} . "\"/>\n";
	if($arg{trunk_maxchannels} > 0)
	{
	   $dialstring .= "<action application=\"limit\" data=\"db ".$arg{trunk_path}." gw_".$arg{trunk_path}." ".$arg{trunk_maxchannels}."\"/>\n";
	}
	$dialstring .= "<action application=\"bridge\" data=\"";	
	$dialstring .= "sofia/gateway/" . $arg{trunk_path} . "/" . $arg{route_prepend} . $arg{destination_number};
	$dialstring .= "\"/>\n";
	return ($dialstring);	
}



sub fs_dialplan_xml_bridge_cc() {
	my ($self, %arg) = @_;
	my ( $sql, $trunkdata, $dialstring,$data );
		
	$arg{route_prepend} = "" if !$arg{route_prepend};	
	if ($arg{trunk_dialed_modify} && $arg{trunk_dialed_modify} ne "") {
		my @regexs = split(m/","/m, $arg{trunk_dialed_modify});
		foreach my $regex (@regexs) {
			$regex =~ s/"//g;                               #Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
			$arg{destination_number} =~ s/$grab/$replace/is;
		}
	}
	$dialstring .= "sofia/gateway/" . $arg{trunk_path} . "/" . $arg{route_prepend} . $arg{destination_number};
	return ($dialstring);	
}

=item $ASTPP->fs_dialplan_xml_footer()

Return the closing lines of the Freeswitch(TM) xml dialplan

Example:  $xml .= $ASTPP->fs_dialplan_xml_footer();

=cut

sub fs_dialplan_xml_footer() {
	my ($self, %arg) = @_;
	$arg{xml} .= "</condition>\n";
	$arg{xml} .= "</extension>\n";
	$arg{xml} .= "</context>\n";
	$arg{xml} .= "</section>\n";
	$arg{xml} .= "</document>\n";
	return $arg{xml};
}

=item $ASTPP->fs_directory_xml_header()

Return the opening lines of the Freeswitch(TM) xml directory.

Example:  $xml .= $ASTPP->fs_directory_xml_header(
	xml => ""
);

=cut

sub fs_directory_xml_header() {
	my ($self, %arg) = @_;
	$arg{xml} .= "<?xml version=\"1.0\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"directory\" description=\"User Directory\">\n";
	return $arg{xml};
}


sub fs_list_sip_usernames
#Return an array with a list of appropriate sip devices.
#accountcode = accountcode
#domain = SIP Domain
#ip = IP address that user is connecting from
#user = SIP Username
{
	my ($self, %arg) = @_;
	my ($tmp,$sql,@results);
	$tmp = "SELECT username,dir_params,dir_vars,pricelist_id, (select number from accounts where id=sip_devices.accountid) as accountcode FROM sip_devices,sip_profiles WHERE sip_profiles.id=sip_devices.sip_profile_id AND username=" . $self->{_freeswitch_db}->quote($arg{user})." AND sip_profiles.sip_ip=".$self->{_freeswitch_db}->quote($arg{domain});
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	while (my $record = $sql->fetchrow_hashref) {
		push @results, $record;
	}
	my $rows = $sql->rows;
	$sql->finish;
	return ($rows,@results);
}


sub fs_directory_xml
{
	my ($self, %arg) = @_;
	my ($sql,$sql1,$tmp,$tmp1);
	my $user_count = 0;
	$arg{xml} .= "<domain name=\"" . $arg{domain} . "\">\n";
	my ($count,@sip_users) = &fs_list_sip_usernames($self,%arg);
	print STDERR "COUNT: $count\n"  if $arg{debug} == 1;
	if ($count > 0) {
	foreach my $record (@sip_users) {
		$user_count++;
		$arg{xml} .= "<user id=\"" . $record->{username} . "\">\n";
		$arg{xml} .= "<params>\n";
		#my @params = &fs_list_sip_params($self,$record->{id});
		my %params =  %{ decode_json($record->{'dir_params'}) };
		while (my ($key, $value) = each %params) {	     
		    $arg{xml} .="<param name=\"".$key."\" value=\"".$params{$key}."\"/>\n";
		}			
		$arg{xml} .= "</params>\n";
		$arg{xml} .= "<variables>\n";
		#my @vars = &fs_list_sip_vars($self,$record->{id});
		my %vars =  %{ decode_json($record->{'dir_vars'}) };
		while (my ($key, $value) = each %vars) {	     
		    $arg{xml} .="<variable name=\"".$key."\" value=\"".$vars{$key}."\"/>\n";
		}
		$arg{xml} .= "<variable name=\"accountcode\" value=\"" . $record->{accountcode} . "\"/>\n";
		$arg{xml} .= "<variable name=\"sip_device_pricelist_id\" value=\"" . $record->{pricelist_id} . "\"/>\n";
		$arg{xml} .= "</variables>\n";
		$arg{xml} .= "</user>\n";
		}
	}
	$arg{xml} .= "</domain>\n";
	print STDERR "TOTAL USERS: $user_count \n"  if $arg{debug} == 1;
	return ($arg{xml},$user_count);
}



sub fs_directory_xml_footer
#Return the closing lines of the Freeswitch(TM) xml dialplan
#xml = Current XML code
{
	my ($self, %arg) = @_;
	$arg{xml} .= "</section>\n";
	$arg{xml} .= "</document>\n";
	return $arg{xml};
}

sub debug #Prints debugging if appropriate
# 
{
	my ($self, %arg) = @_;	
	print STDERR $arg{debug}."\n" if $self->{_verbosity_level} > 0;
# 	$self->{_astpp_db}->do("INSERT INTO activity_logs (message,user) VALUES (" 
# 		. $self->{_astpp_db}->quote($arg{debug}) . "," 
# 		. $self->{_astpp_db}->quote($arg{user}) . ")") if $arg{debug} && $self->{_astpp_db} && $self->{_verbosity_item_level} >= $self->{_verbosity_level};
	return 0;
}

sub invoice_cdrs
# Function 1 = count cdrs
# Function 2 = return crds
# Function 3 = Internal Invoices, Post CDRs.
{
	my ($self, %arg) = @_; #Count the cdrs billable on a specific account
	my $tmp;
	if ($arg{function} == 1) {
		$tmp = "SELECT COUNT(*) FROM cdrs WHERE cardnum = ";
	}
	elsif ($arg{function} == 2) {
		$tmp = "SELECT * FROM cdrs WHERE cardnum = ";
	}
	elsif ($arg{function} == 3) {
		$tmp = "UPDATE cdrs SET invoiceid = "
		. $self->{_astpp_db}->quote($arg{invoiceid})
		. ",status = 1 "
		. " WHERE cardnum = ";
	}
	if ($arg{startdate} && $arg{enddate}) {
		$tmp .= $self->{_astpp_db}->quote($arg{cardnum})
		. " AND status = 0"
		. " AND callstart >= DATE(" . $self->{_astpp_db}->quote($arg{startdate}) . ")"
		. " AND callstart <= DATE(" . $self->{_astpp_db}->quote($arg{enddate}) . ")";
	} elsif ($arg{startdate}) {
		$tmp .= $self->{_astpp_db}->quote($arg{cardnum})
		. " AND status = 0"
		. " AND callstart >= DATE(" . $self->{_astpp_db}->quote($arg{startdate}) . ")";
	} elsif ($arg{enddate}) {
		$tmp .= $self->{_astpp_db}->quote($arg{cardnum})
		. " AND status = 0"
		. " AND callstart <= DATE(" . $self->{_astpp_db}->quote($arg{enddate}) . ")";
	} else {
		$tmp .= $self->{_astpp_db}->quote($arg{cardnum})
		. " AND status = 0";
	}
	if ($arg{function} == 2) {
		$tmp .= " GROUP BY type ORDER BY callstart";
	}

	print STDERR "$tmp \n";
	my $sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;

	if ($arg{function} == 1) {
		my $row       = $sql->fetchrow_hashref;
		$sql->finish;
		return(
			$row->{"COUNT(*)"}
		);
	}
	elsif ($arg{function} == 2) {
		my @cdrs;
		while ( my $record = $sql->fetchrow_hashref ) {
			push @cdrs, $record;
		}
		$sql->finish;
		return(
			@cdrs
		);
	}
}

sub invoice_list_internal
{
	my ($self, %arg) = @_; # List Internal Invoices.
	my ($tmp,$sql,@invoices);
	$tmp = "SELECT * FROM invoice_list_view";
	if ($arg{accountid}) {
		$tmp .= " WHERE accountid = "
		. $self->{_astpp_db}->quote($arg{accountid});
	}
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	while ( my $record = $sql->fetchrow_hashref ) {
		push @invoices, $record;
	}
	$sql->finish;
	return @invoices;
}

sub invoice_create_internal
{
	my ($self, %arg) = @_; # Create invoice in ASTPP Internally and return the invoice number.
	my $tmp = "INSERT into invoices (accountid,date) VALUES("
		. $self->{_astpp_db}->quote($arg{accountid})
		. ",curdate())";
	my $sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	my $invoice = $sql->{'mysql_insertid'};
	$sql->finish;
	return (
		$invoice
	);
}

sub invoice_cdrs_subtotal_internal 
{
	my ($self, %arg) = @_; # Create invoice in ASTPP Internally and return the invoice number.
	my ($tmp,$row,$sql,$credit,$debit,$total);
	$tmp = "SELECT SUM(debit) FROM cdrs WHERE invoiceid = "
		. $self->{_astpp_db}->quote($arg{invoiceid});
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	$row   = $sql->fetchrow_hashref;
	$debit = $row->{"SUM(debit)"};
	$sql->finish;
	$tmp = "SELECT SUM(credit) FROM cdrs WHERE invoiceid = "
		. $self->{_astpp_db}->quote($arg{invoiceid});
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	$row   = $sql->fetchrow_hashref;
	$credit = $row->{"SUM(credit)"};
	$sql->finish;
	if ( !$credit )         { $credit         = 0; }
	if ( !$debit )          { $debit          = 0; }
	$total = ( $debit - $credit );
	return ($total/1);

#       $tmp = "INSERT into invoices_total (invoiceid,title,text,value,class,sort_order) VALUES("
#               . $self->{_astpp_db}->quote($arg{invoiceid})
#               . ",'Subtotal','',"
#               . $self->{_astpp_db}->quote($total/1)
#               . ",1,"
#               . $self->{_astpp_db}->quote($arg{sort_order})
#               . ")";
#       $sql = $ $self->{_astpp_db}->prepare($tmp);
#       $sql->execute;
#       return $arg{sort_order}++;
}

sub invoice_subtotal_post_internal
{
	my ($self, %arg) = @_; 
	$arg{value} = sprintf( "%." . $arg{decimalpoints_total} . "f", $arg{value} );
	my $tmp = "INSERT into invoices_total (invoices_id,title,text,value,class,sort_order) VALUES("
		. $self->{_astpp_db}->quote($arg{invoiceid})
		. ","
		. $self->{_astpp_db}->quote($arg{title})
		. ","
		. $self->{_astpp_db}->quote($arg{text})
		. ","
		. $self->{_astpp_db}->quote($arg{value})
		. ","
		. $self->{_astpp_db}->quote($arg{class})
		. ","
		. $self->{_astpp_db}->quote($arg{sort_order})
		. ")";
	my $sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	$sql->finish;
	return $arg{sort_order}++;
}

sub invoice_subtotal_internal
{
	my ($self, %arg) = @_; 
	my $tmp = "SELECT SUM(value) FROM invoices_total WHERE invoices_id = "
		. $self->{_astpp_db}->quote($arg{invoiceid});
	my $sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	my $row   = $sql->fetchrow_hashref;
	my $value = $row->{"SUM(value)"};
	$sql->finish;
	return $value;
}

sub invoice_taxes_internal
# function 1 = list
# function 2 = post
{
	my ($self, %arg) = @_; # Create invoice in ASTPP Internally and return the invoice number.
	my (@taxes,$row,$tmp,$sql);
	$tmp = "SELECT * FROM taxes_to_accounts_view WHERE accountid = "
		. $self->{_astpp_db}->quote($arg{accountid})
		. " ORDER BY taxes_priority ASC";
	$sql = $self->{_astpp_db}->prepare($tmp);
	print STDERR $tmp . "/n";
	$sql->execute;
	while ( $row = $sql->fetchrow_hashref ) {
		push @taxes, $row;
	}
	$sql->finish;
	if ($arg{function} == 1) {
		return @taxes;
	}
	my $tax_count = 1;
	my $sort = 1;
	my $tax_priority = "";
	my $subtotal = $arg{invoice_subtotal};
	foreach my $tax (@taxes) {
		my ($tax_amount);
		if ($tax_priority eq "") {
			$tax_priority = $tax->{taxes_priority};
		} elsif($tax->{taxes_priority} > $tax_priority) {
			$tax_priority = $tax->{taxes_priority};
			my $tmp = "SELECT SUM(value) FROM invoices_total WHERE invoices_id = "
				. $self->{_astpp_db}->quote($arg{invoiceid});
			print STDERR $tmp . "\n";
			my $sql = $self->{_astpp_db}->prepare($tmp);
			$sql->execute;
			my $row   = $sql->fetchrow_hashref;
			$subtotal = $row->{"SUM(value)"};
			$sql->finish;
		}
		print STDERR "Subtotal: $subtotal \n";
		print STDERR "Tax_rate: $tax->{taxes_rate} \n";
		my $tax_total = (($subtotal * ( $tax->{taxes_rate} / 100 )) + $tax->{taxes_amount} );
		print STDERR "Tax Total: $tax_total \n";
		print STDERR "Round to: $arg{decimalpoints_tax} \n";
		$tax_total = sprintf( "%." . $arg{decimalpoints_tax} . "f", $tax_total );
		print STDERR "Tax Total: $tax_total \n";
		my $tmp = "INSERT INTO invoices_total (invoices_id,title,text,value,class,sort_order) VALUES("
		. $self->{_astpp_db}->quote($arg{invoiceid})
		. ",'TAX',"
		. $self->{_astpp_db}->quote($tax->{taxes_description})
		. ","
		. $self->{_astpp_db}->quote($tax_total)
		. ",2,"
		. $self->{_astpp_db}->quote($arg{sort_order})
		. ")";
		print STDERR $tmp . "\n";
		my $sql = $self->{_astpp_db}->prepare($tmp);
		$sql->execute;

		$arg{sort_order}++;
		$sql->finish;
	}
	return $arg{sort_order};
}

#Configuration xml header
sub fs_configuration_xml_header() {
	my ($self, %arg) = @_;	
	$arg{xml} .= "<?xml version=\"1.0\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"Configuration\" description=\"Configuration\">\n";
	return $arg{xml};
}

#Dynamic ACL XML
sub acl
{
    my ($self, %arg) = @_;          
    my ($row,$tmp,$sql,$tmp_gw,$sql_gw,$row_gw);
    $arg{xml} .= "<configuration name=\"".$arg{module}."\" description=\"Network Lists\">\n";
    $arg{xml} .= "<network-lists>\n";
    $arg{xml} .= "<list name=\"default\" default=\"deny\">\n";           
    
    #Add customer ip address
    $tmp = "SELECT ip FROM ip_map,accounts WHERE ip_map.accountid=accounts.id AND accounts.status=1 AND deleted=0";
    $sql = $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
	 $arg{xml} .= "<node type=\"allow\" cidr=\"".$row->{ip}."/32\"/>\n";
    }
    
    #Add gateway ip address
    $tmp_gw = "SELECT * FROM gateways";
    $sql_gw = $self->{_astpp_db}->prepare($tmp_gw);
    $sql_gw->execute;
    while ( $row_gw = $sql_gw->fetchrow_hashref ) {	
	my %data_gw =  %{ decode_json($row_gw->{gateway_data}) };
	while (my ($key_gw, $value_gw) = each %data_gw) {	     	    
	    if($key_gw eq 'proxy')
	    {
		$arg{xml} .= "<node type=\"allow\" cidr=\"".$data_gw{$key_gw}."/32\"/>\n";
	    }
	}	
    }
    
    #Add opensips ip address if opensips is enable
    if($arg{opensips} eq '1')
    {
	$arg{xml} .= "<node type=\"allow\" cidr=\"".$arg{opensips_ip}."/32\"/>\n";
    }
    
    $arg{xml} .= "</list>\n";
    $arg{xml} .= "</network-lists>\n";
    $arg{xml} .= "</configuration>\n";
    return $arg{xml};
}

#Dynamic Sip profile and gateway xml
sub sip_profile_gateway
{
    my ($self, %arg) = @_;          
    my ($row,$tmp,$sql,$sql_gw,$tmp_gw,$row_gw);
    $arg{xml} .= "<configuration name=\"".$arg{module}."\" description=\"SIP Profile\">\n";
    $arg{xml} .= "<profiles>\n";
    
    # Samir Doshi - To bind sip profile with correct fs. 
    $tmp = "SELECT * FROM sip_profiles WHERE (sip_ip=".$self->{_astpp_db}->quote($arg{freeswitch_ip})." OR sip_ip='\$\${local_ip_v4}')";
    
    $sql = $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
        
    while ( $row = $sql->fetchrow_hashref ) {
	
	$arg{xml} .= "<profile name=\"".$row->{name}."\">\n";
	$arg{xml} .= "<settings>\n";
	$arg{xml} .= "<param name=\"sip-ip\" value=\"".$row->{sip_ip}."\"/>\n";
	$arg{xml} .= "<param name=\"sip-port\" value=\"".$row->{sip_port}."\"/>\n";
 	my %data =  %{ decode_json($row->{profile_data}) };
	while (my ($key, $value) = each %data) {	     
	     $arg{xml} .="<param name=\"".$key."\" value=\"".$data{$key}."\"/>\n";
	}	
	$arg{xml} .= "</settings>\n";
	$arg{xml} .= "<gateways>\n";
	$tmp_gw = "SELECT * FROM gateways WHERE sip_profile_id='".$row->{'id'}."'";
	$sql_gw = $self->{_astpp_db}->prepare($tmp_gw);
	$sql_gw->execute;
	while ( $row_gw = $sql_gw->fetchrow_hashref ) {
	    $arg{xml} .= "<gateway name=\"".$row_gw->{name}."\">\n";
	    my %data_gw =  %{ decode_json($row_gw->{gateway_data}) };
	    while (my ($key_gw, $value_gw) = each %data_gw) {	     
		$arg{xml} .="<param name=\"".$key_gw."\" value=\"".$data_gw{$key_gw}."\"/>\n";
	    }
	    $arg{xml} .= "</gateway>\n";
	}
	$arg{xml} .= "</gateways>\n";
	$arg{xml} .= "<domains>\n";
	$arg{xml} .= "<domain name=\"all\" alias=\"true\" parse=\"false\"/>\n";
	$arg{xml} .= "</domains>\n";
	$arg{xml} .= "</profile>\n";	
    }
    $arg{xml} .= "</profiles>\n";
    $arg{xml} .=  "</configuration>\n";
    return $arg{xml};
}


#Dynamic Post load modules XML
sub post_load_modules
{
    my ($self, %arg) = @_;          
    my ($row,$tmp,$sql);
    $arg{xml} .= "<configuration name=\"".$arg{module}."\" description=\"Post Load Modules\">\n";
    $arg{xml} .= "<modules>\n";    
    $tmp = "SELECT * FROM post_load_modules_conf WHERE load_module='1' ORDER BY priority";
    $sql = $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
	 $arg{xml} .= "<load module=\"".$row->{module_name}."\"/>\n";
    }    
    $arg{xml} .= "</modules>\n";
    $arg{xml} .= "</configuration>\n";
    return $arg{xml};
}

sub post_load_switch
{
    my ($self, %arg) = @_;          
    my ($row,$tmp,$sql);
    $arg{xml} .= "<configuration name=\"".$arg{module}."\" description=\"Post Load Switch\">\n";
    $arg{xml} .= "<settings>\n";    
    $tmp = "SELECT * FROM post_load_switch_conf";
    $sql = $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
	 $arg{xml} .= "<param name=\"".$row->{param_name}."\" value=\"".$row->{param_value}."\"/>\n";
    }    
    $arg{xml} .= "</settings>\n";
    $arg{xml} .= "</configuration>\n";
    return $arg{xml};
}


#Configuration xml footer
sub fs_configuration_xml_footer
{
	my ($self, %arg) = @_;
	$arg{xml} .= "</section>\n";
	$arg{xml} .= "</document>\n";
	return $arg{xml};
}

1;

__END__

=head1 SEE ALSO

For more information visit our website at (www.astpp.org)

=head1 AUTHOR

ASTPP Info, E<lt>info@astpp.orgE<gt>

=head1 COPYRIGHT AND LICENSE

Copyright (C) 2007 by Aleph Communications

This library is distributed under the terms of the GPL version 2.

=cut
