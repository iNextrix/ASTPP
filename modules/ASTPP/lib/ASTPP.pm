package ASTPP;

require 5.004;
use strict;
# use warnings;
use DBI;
use Data::Paginate;
use Locale::gettext_pp qw(:locale_h);

bindtextdomain( "astpp", "/usr/local/share/locale" );
textdomain("astpp");

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
		_asterisk_agi           => $_[3],
		_cdr_db                 => $_[4],
		_verbosity_item_level   => $arg{verbosity_item_level}           || 0,
		_script                 => $arg{script}         || "astpp-admin.cgi",
		_config                 => $_[7],
	}, $class;
}

sub set_verbosity_level { $_[0]->{_verbosity_level}     }  #Sets the verbosity level.
sub set_verbosity       { $_[0]->{_verbosity_level}     }  #Sets the verbosity level. One of these needs to be deprecated.

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

=item $ASTPP->set_pagination_script()

Set the location of the script we are working in so that we return the correct url

Example: $ASTPP->set_pagination_script("astpp-admin.cgi")

=cut

sub set_pagination_script
{
	my ($self, $script) = @_;
	$self->{_script} = $script if $script;
}

#sub set_config
#{
#       my ($self, %config_hash) = @_;
#       $self->{_config} = %config_hash if %config_hash;
#}

=item $ASTPP->load_config()

Read the ASTPP configuration file and return it as a hash

Example: $config = $ASTPP->load_config()

=cut

sub load_config
{
	my ($self, %arg) = @_;
    my $config;
    open( CONFIG, "</var/lib/astpp/astpp-config.conf" );
    while (<CONFIG>) {
	chomp;            # no newline
	s/#.*//;          # no comments
	s/^\s+//;         # no leading white
	s/\s+$//;         # no trailing white
	next unless length;    # anything left?
	my ( $var, $value ) = split( /\s*=\s*/, $_, 2 );
	$config->{$var} = $value;
    }
    close(CONFIG);
    return $config;
}

# Load configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
sub load_config_db() {
	my ($self, %arg) = @_;
	my $config = $arg{config};
    my ($sql, @didlist, $row, $tmp );
    $tmp =
      "SELECT name,value FROM system WHERE reseller IS NULL";
    $sql = $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}


=item $ASTPP->connect_db()

Connect to a database and return the connection.  This can be used for either
Postgresql on MySQL.

Example:
$astpp_db = $ASTPP->connect_db(
	dbengine => "MySQL",
	dbname   => "astpp",
	dbhost   => "localhost",
	dbuser   => "root",
	dbpass   => "Passw0rd!"
)

=cut

sub connect_db
{
	my ($self, %arg) = @_;
    my ( $dbh, $dsn );
    if ( $arg{dbengine} eq "MySQL" ) {
	$dsn = "DBI:mysql:database=$arg{dbname};host=$arg{dbhost}";
    }
    elsif ( $arg{dbengine} eq "Pgsql" ) {
	$dsn = "DBI:Pg:database=$arg{dbname};host=$arg{dbhost}";
    }
    $dbh = DBI->connect( $dsn, $arg{dbuser}, $arg{dbpass} );
    if ( !$dbh ) {
	print STDERR "DATABASE: " . $arg{dbname} . " IS DOWN\n";
	return 0;
    }
    else {
	$dbh->{mysql_auto_reconnect} = 1;
	print STDERR "Connected to " . $arg{dbname} . " Database!" . "\n";
	return $dbh;
    }
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
	$tmp = "SELECT * FROM ip_map WHERE ip = " . $self->{_astpp_db}->quote($arg{ip_address})
		. " AND prefix IN (NULL,'') OR ip = " . $self->{_astpp_db}->quote($arg{ip_address});
	$tmp .= " AND " . $self->{_astpp_db}->quote($arg{destination}) . " RLIKE prefix" if $arg{destination};
	$tmp .= " ORDER BY LENGTH(prefix) DESC LIMIT 1";
	print STDERR $tmp . "\n" if $arg{debug} == 1;
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	return $record;
# 	while (my $record = $sql->fetchrow_hashref) {
# 		print STDERR $record->{ip};
# 		push @results, $record;
# 	}
# 	my $rows = $sql->rows;
# 	$sql->finish;
# 	return ($rows,@results);
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
# 	if ($arg{DID} > 0) {
# 		$arg{xml} .= "<context name=\"public\">\n";
# 	} elsif ($arg{IP} ne "" || $arg{ip} > 0) {
# 		$arg{xml} .= "<context name=\"public\">\n";
# 	} else {
# 		$arg{xml} .= "<context name=\"default\">\n";
# 	};
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
	$arg{xml} .= "<action application=\"sched_hangup\" data=\"+" . $arg{max_length} * 60 . "\"/>\n";
	$arg{xml} .= "<action application=\"set\" data=\"accountcode=" . $arg{accountcode} . "\"/>\n";
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
	my $tmp = "SELECT * FROM dids WHERE number = "
			. $self->{_astpp_db}->quote( $arg{did} ) .
			" LIMIT 1";
	print STDERR $tmp;      
	$sql = $self->{_astpp_db}->prepare($tmp); 
	$sql->execute;
	my $diddata = $sql->fetchrow_hashref;
	$sql->finish;
	my @variables = split /,(?!(?:[^",]|[^"],[^"])+")/, $diddata->{variables};
	foreach my $variable (@variables) {
	$arg{xml} .= "<action application=\"set\" data=\"accountcode=" . $arg{accountcode} . "\"/>\n";
		$xml .= "<action application=\"set\" data=\"" . $variable . "\"/>\n";
	}
	$xml .= "<action application=\"set\" data=\"calltype=DID\"/>\n";
	if ($diddata->{extensions} =~ m/^("|)(L|l)ocal.*/m) {
		my ($junk,$ext,$context) = split /,(?!(?:[^",]|[^"],[^"])+")/, $diddata->{extensions};
		#jump to local dialplan
		print STDERR "EXT: $ext\n" if $arg{debug} == 1;
		$ext =~ s/"//mg;
		print STDERR "EXT: $ext \n" if $arg{debug} == 1;
		print STDERR "CONTEXT: $context\n" if $arg{debug} == 1;
		$context =~ s/"//mg;
		print STDERR "CONTEXT: $context \n" if $arg{debug} == 1;
		$xml .= "<action application=\"transfer\" data=\"" . $ext ." XML " .$context . "\"/>\n";
	} else {
		$xml .= "<action application=\"bridge\" data=\"" . $diddata->{extensions} . "\"/>\n";
	}

	return $xml;    
}

sub fs_dialplan_xml_bridge_start() {
	my ($self, %arg) = @_;
	my $dialstring .= "<action application=\"set\" data=\"hangup_after_bridge=true\"/>\n";
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
	my ( $sql, $trunkdata, $dialstring,$data );
	$sql = $self->{_astpp_db}->prepare( "SELECT * FROM trunks WHERE name = ". $self->{_astpp_db}->quote( $arg{trunk_name} ) );
	$sql->execute;
	$trunkdata = $sql->fetchrow_hashref;
	$arg{route_prepend} = "" if !$arg{route_prepend};
	$sql->finish;
	if ($trunkdata->{dialed_modify} && $trunkdata->{dialed_modify} ne "") {
		my @regexs = split(m/","/m, $trunkdata->{dialed_modify});
		foreach my $regex (@regexs) {
			$regex =~ s/"//g;                               #Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
			print STDERR "Grab: $grab\n";
			print STDERR "Replacement: $replace\n";
			print STDERR "Phone Before: $arg{destination_number}\n";
			$arg{destination_number} =~ s/$grab/$replace/is;
			print STDERR "Phone After: $arg{destination_number}\n";
		}
	}
	
	$dialstring .= "<action application=\"set\" data=\"calltype=STANDARD\"/>\n";
	$dialstring .= "<action application=\"set\" data=\"outbound_route=" . $arg{route_id} . "\"/>\n";
	$dialstring .= "<action application=\"set\" data=\"trunk=" . $trunkdata->{name} . "\"/>\n";
	$dialstring .= "<action application=\"set\" data=\"provider=" . $trunkdata->{provider} . "\"/>\n";	
	$dialstring .= "<action application=\"bridge\" data=\"";
	if ( $trunkdata->{tech} eq "Zap" ) {
		$dialstring .= "openzap/" . $trunkdata->{path} . "/1/" . $arg{route_prepend} . $arg{destination_number}; 
		return ($dialstring);
	}
	elsif ( $trunkdata->{tech} eq "SIP" ) {
	  $dialstring .= "sofia/gateway/" . $trunkdata->{path} . "/" . $arg{route_prepend} . $arg{destination_number};
	$dialstring .= "\"/>\n";  
	return ($dialstring);
    }
    else {
# 	print STDERR "CANNOT ROUTE THIS CALL!!!!!\n";
	return "";      
    }
}



sub fs_dialplan_xml_bridge_cc() {
	my ($self, %arg) = @_;
	my ( $sql, $trunkdata, $dialstring,$data );
	$sql = $self->{_astpp_db}->prepare( "SELECT * FROM trunks WHERE name = "
			. $self->{_astpp_db}->quote( $arg{trunk_name} ) );
	$sql->execute;
	$trunkdata = $sql->fetchrow_hashref;
	$arg{route_prepend} = "" if !$arg{route_prepend};
	$sql->finish;
	if ($trunkdata->{dialed_modify} && $trunkdata->{dialed_modify} ne "") {
		my @regexs = split(m/","/m, $trunkdata->{dialed_modify});
		foreach my $regex (@regexs) {
			$regex =~ s/"//g;                               #Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
			print STDERR "Grab: $grab\n";
			print STDERR "Replacement: $replace\n";
			print STDERR "Phone Before: $arg{destination_number}\n";
			$arg{destination_number} =~ s/$grab/$replace/is;
			print STDERR "Phone After: $arg{destination_number}\n";
		}
	}
# 	if ($arg{count} > 0) {
# 		$dialstring = "|";
# 	} else {
# 	}
	if ( $trunkdata->{tech} eq "Zap" ) {
		$dialstring .= "openzap/" . $trunkdata->{path} . "/1/" . $arg{route_prepend} . $arg{destination_number}; 
		print STDERR $dialstring."\n";
		return ($dialstring);
	}
	elsif ( $trunkdata->{tech} eq "SIP" ) {
	      $dialstring .= "sofia/gateway/" . $trunkdata->{path} . "/" . $arg{route_prepend} . $arg{destination_number};
	      print STDERR $dialstring."\n";
	      return ($dialstring);
	}
	else {
	  print STDERR "CANNOT ROUTE THIS CALL!!!!!\n";
	  return "";      
    }
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

=item $ASTPP->fs_directory_xml_header()

Add a SIP user to the Freeswitch database.  The "username" parameter is optional.
if it is not passed then we generate a random one using "accountcode" as a base.

Example:

($status_code,$status_text) = $ASTPP->fs_add_sip_user(
	sip_ext_prepend => $config->{sip_ext_prepend}
	accountcode     => $params->{accountcode}, 
	context         => $config->{freeswitch_context},
	vm_password     => $params->{vmpassword},
	password        => $params->{password},
	username        => $params->{username}
);

=cut

sub fs_add_sip_user() {
	my ($self, %arg) = @_;
	# Find uniqueid to prepend to the login
	my $sipid = 0;
	if (!$arg{username} || $arg{username} eq "") {
	$arg{username} = $arg{accountcode};
	for ( ; ; ) {
		my $count = 1;
		$sipid =
		    int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 )
		  . int( rand() * 9000 + 1000 );
		$sipid = $arg{sip_ext_prepend} . $sipid;
		$sipid = substr( $sipid, 0, 5 );
		$sipid = $arg{username} . $sipid;
		my $sql =
		  $self->{_freeswitch_db}->prepare(
		    "SELECT COUNT(*) FROM directory WHERE username = "
		      . $self->{_freeswitch_db}->quote($sipid) );
		$sql->execute;
		my $record = $sql->fetchrow_hashref;
		$sql->finish;
		if ( $record->{"COUNT(*)"} == 0 ) {
			last;
		}
	}
	} else {
		$arg{username} =~ s/\W//mg;
		$sipid = $arg{username}
	}
	
    my $tmp =
	"INSERT INTO directory (username,domain) VALUES ("
      . $self->{_freeswitch_db}->quote($sipid) . ", "
      . $self->{_freeswitch_db}->quote($arg{freeswitch_domain}). ")";
    print STDERR $tmp . "\n";
    my $sql = $self->{_freeswitch_db}->prepare($tmp);
    if ( !$sql->execute ) {
	print "$tmp failed";
	$sql->finish;
	return (1,"SIP Device Creation Failed!");
    }
    else {
	my $directory_id = $sql->{'mysql_insertid'};
	$sql->finish;
	my $tmp = "INSERT INTO directory_vars (directory_id,var_name,var_value) VALUES ("
		. $self->{_freeswitch_db}->quote($directory_id) . ","
		. "'accountcode',"
		. $self->{_freeswitch_db}->quote($arg{accountcode})
		. "),("
		. $self->{_freeswitch_db}->quote($directory_id) . ","
		. "'user_context',"
		. $self->{_freeswitch_db}->quote($arg{freeswitch_context}) . ")";
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	$tmp = "INSERT INTO directory_params (directory_id,param_name,param_value) VALUES ("
		. $self->{_freeswitch_db}->quote($directory_id) . ","
		. "'vm-password',"
		. $self->{_freeswitch_db}->quote($arg{vm_password})
		. "),("
		. $self->{_freeswitch_db}->quote($directory_id) . ","
		. "'password',"
		. $self->{_freeswitch_db}->quote($arg{password}) . ")";
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);
	return (0, "SIP Device Added!" . "Username:" . " " . $sipid . " " . "Password:" . " " . $arg{password}, $sipid);
    }
}


sub fs_save_sip_user() {
    my ($self, %arg) = @_;
	my $tmp = "UPDATE directory SET username = "
		. $self->{_freeswitch_db}->quote($arg{username})
		. " WHERE id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	$tmp = "UPDATE directory_vars SET var_value = "
		. $self->{_freeswitch_db}->quote($arg{accountcode})
		. " WHERE var_name = 'accountcode'"
		. " AND directory_id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	$tmp = "UPDATE directory_vars SET var_value = "
		. $self->{_freeswitch_db}->quote($arg{freeswitch_context})
		. " WHERE var_name = 'user_context'"
		. " AND directory_id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	$tmp = "UPDATE directory_params SET param_value = "
		. $self->{_freeswitch_db}->quote($arg{vm_password})
		. " WHERE param_name = 'vm-password'"
		. " AND directory_id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	$tmp = "UPDATE directory_params SET param_value = "
		. $self->{_freeswitch_db}->quote($arg{password})
		. " WHERE param_name = 'password'"
		. " AND directory_id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$self->{_freeswitch_db}->do($tmp);

	return (0, "SIP Device Saved!" . "Username:" . " " . $arg{username} . " " . "Password:" . " " . $arg{password}, $arg{username});
}

=item $ASTPP->fs_retrieve_sip_user()

Returns the details on the specified Freeswitch SIP user.

Example:

$user_data = $ASTPP->fs_retrieve_sip_user(
	directory_id    => "1"  #directory_id of sip user you are looking for.
);

=cut

sub fs_retrieve_sip_user() {
	my ($self, %arg) = @_;
	my ($tmp,$record,$sql,$deviceinfo);
	$tmp = "SELECT username FROM directory WHERE id = "
		.  $self->{_freeswitch_db}->quote($arg{directory_id});
	print STDERR $tmp . "\n";
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{username} = $record->{username};

	$tmp = "SELECT var_value FROM directory_vars WHERE directory_id = "
		.  $self->{_freeswitch_db}->quote($arg{directory_id})
		. " AND var_name = 'user_context'";
	print STDERR $tmp . "\n";
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{context} = $record->{var_value};

	$tmp = "SELECT param_value FROM directory_params WHERE directory_id = "
		.  $self->{_freeswitch_db}->quote($arg{directory_id})
		. " AND param_name = 'password' LIMIT 1";
	print STDERR $tmp . "\n";
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{password} = $record->{param_value};

	$tmp = "SELECT param_value FROM directory_params WHERE directory_id = "
		.  $self->{_freeswitch_db}->quote($arg{directory_id})
		. " AND param_name = 'vm-password' LIMIT 1";
	print STDERR $tmp . "\n";
	$sql =  $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{vm_password} = $record->{param_value};

	$tmp = "SELECT var_value FROM directory_vars WHERE directory_id = "
		. $self->{_freeswitch_db}->quote($arg{directory_id})
		. " AND var_name = 'accountcode' LIMIT 1";
	print STDERR $tmp . "\n";
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	$record = $sql->fetchrow_hashref;
	$sql->finish;
	$deviceinfo->{accountcode} = $record->{var_value};
	return $deviceinfo;
}



=item $ASTPP->fs_dele_sip_user()

Delete the specified Freeswitch SIP user.

Example:

$status_code = $ASTPP->fs_delete_sip_user(
	id      => "1"  #directory_id of sip user you are deleting.
);

=cut

sub fs_delete_sip_user() {
	my ($self, %arg) = @_;
	my ($tmp,$sql,@results);
	$tmp = "DELETE FROM directory WHERE id = " . $self->{_freeswitch_db}->quote($arg{id});
	$self->{_freeswitch_db}->do($tmp);
	$tmp = "DELETE FROM directory_vars WHERE directory_id = " . $self->{_freeswitch_db}->quote($arg{id});
	$self->{_freeswitch_db}->do($tmp);
	$tmp = "DELETE FROM directory_params WHERE directory_id = " . $self->{_freeswitch_db}->quote($arg{id});
	$self->{_freeswitch_db}->do($tmp);
	return 0;
}

sub fs_list_sip_usernames
#Return an array with a list of appropriate sip devices.
#accountcode = accountcode
#domain = SIP Domain
#ip = IP address that user is connecting from
#user = SIP Username
#cc = Callingcard number tagged to each account
#accountcode = accountcode
{
	my ($self, %arg) = @_;
	my ($tmp,$sql,@results);
	if ($arg{accountcode} || $arg{cc}) {
		$tmp = "SELECT directory.id AS id, directory.username AS username, directory.domain AS domain FROM " 
			. "directory,directory_vars WHERE directory.id = directory_vars.directory_id "
			. "AND directory_vars.var_name = 'accountcode' "
			. "AND directory_vars.var_value IN ("
			.  $self->{_freeswitch_db}->quote($arg{accountcode})
			. "," .  $self->{_freeswitch_db}->quote($arg{cc}) . ")";
	} else {
		$tmp = "SELECT id,username,domain FROM directory ";
		if ($arg{user}) {
			$tmp .= " WHERE username = " . $self->{_freeswitch_db}->quote($arg{user}); 
			if ($arg{domain}) {
				$tmp .= " AND domain IN( "
					. $self->{_freeswitch_db}->quote($arg{domain})
					. ",'\$\${local_ip_v4}')";
			} 
#               } else {
#                       if ($arg{domain}) {
#                               $tmp .= " WHERE domain = "
#                                       . $self->{_freeswitch_db}->quote($arg{domain});
#                       } 
		}
	}
# 	print STDERR $tmp."\n";
	$sql = $self->{_freeswitch_db}->prepare($tmp);
	$sql->execute;
	while (my $record = $sql->fetchrow_hashref) {
# 		print STDERR $record->{username};
		push @results, $record;
	}
	my $rows = $sql->rows;
	$sql->finish;
	return ($rows,@results);
}
 
sub fs_list_sip_params
#Return the list of parameters set on a freeswitch sip account
{
	my ($self, $id) = @_;
	my ($tmp,$sql,@results);
	$tmp = "SELECT * FROM directory_params WHERE directory_id = " 
		. $self->{_freeswitch_db}->quote($id); 
	$sql = $self->{_freeswitch_db}->prepare($tmp); 
	$sql->execute;
	while (my $record = $sql->fetchrow_hashref) {
		push @results, $record;
	}
	$sql->finish;
	return @results;
}

sub fs_list_sip_vars
#Return the list of variables set on a freeswitch sip account
{
	my ($self, $id) = @_;
	my ($tmp,$sql,@results);
	$tmp = "SELECT * FROM directory_vars WHERE directory_id = " 
		. $self->{_freeswitch_db}->quote($id); 
	$sql = $self->{_freeswitch_db}->prepare($tmp); 
	$sql->execute;
	while (my $record = $sql->fetchrow_hashref) {
		push @results, $record;
	}
	$sql->finish;
	return @results;
}

sub fs_directory_xml
#Return the user detail lines for Freeswitch(TM) sip athentication.
#xml = Current XML code
#ip = IP Address that user is connecting from
#user = SIP Username
#domain = SIP Domain
{
	my ($self, %arg) = @_;
	my ($sql,$sql1,$tmp,$tmp1);
	my $user_count = 0;
	$arg{xml} .= "<domain name=\"" . $arg{domain} . "\">";
	my ($count,@sip_users) = &fs_list_sip_usernames($self,%arg);
	print STDERR "COUNT: $count\n"  if $arg{debug} == 1;
	if ($count > 0) {
	foreach my $record (@sip_users) {
		$user_count++;
		$arg{xml} .= "<user id=\"" . $record->{username} . "\" mailbox=\"" . $record->{mailbox} . "\">\n";
		$arg{xml} .= "<params>\n";
		my @params = &fs_list_sip_params($self,$record->{id});
		foreach my $record (@params) {
			$arg{xml} .= "<param name=\"" . $record->{param_name} . "\" value=\"" . $record->{param_value} . "\"/>\n";
		}
		$arg{xml} .= "</params>\n";
		$arg{xml} .= "<variables>\n";
		my @vars = &fs_list_sip_vars($self,$record->{id});
		foreach my $record (@vars) {
			$arg{xml} .= "<variable name=\"" . $record->{var_name} . "\" value=\"" . $record->{var_value} . "\"/>\n";
		}
#		$arg{xml} .= "<variable name=\"accountcode\" value=\"" . $record->{account} . "\"/>\n";
#		$arg{xml} .= "<variable name=\"user_context\" value=\"" . $record->{context} . "\"/>\n";
		$arg{xml} .= "</variables>\n";
		$arg{xml} .= "</user>\n";
		}
	}
	my @ip_users;
	($count,@ip_users) = &ip_address_authenticate($self,%arg);
	print STDERR "COUNT: $count\n"  if $arg{debug} == 1;
	if ($count > 0) {
	foreach my $record (@ip_users) {
# This is only temporary and should be removed
#
		$record->{id} = 0;
		$arg{xml} .= "<user id=\"" . $record->{account} . $record->{ip} . "\" ip=\"" . $record->{ip} . "\">\n";
		$arg{xml} .= "<params>\n";
		my @params = &fs_list_sip_params($self,$record->{id});
		foreach my $record (@params) {
			$arg{xml} .= "<param name=\"" . $record->{param_name} . "\" value=\"" . $record->{param_value} . "\"/>\n";
		}
		$arg{xml} .= "</params>\n";
		$arg{xml} .= "<variables>\n";
		my @vars = &fs_list_sip_vars($self,$record->{id});
		foreach my $record (@vars) {
			$arg{xml} .= "<variable name=\"" . $record->{var_name} . "\" value=\"" . $record->{var_value} . "\"/>\n";
		}
		$arg{xml} .= "<variable name=\"accountcode\" value=\"" . $record->{account} . "\"/>\n";
		$arg{xml} .= "<variable name=\"user_context\" value=\"" . $record->{context} . "\"/>\n";
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
	$self->{_verbosity_item_level} = $arg{verbosity} if $arg{verbosity};
	print STDERR $arg{debug} . "\n" if $arg{debug} && $self->{_verbosity_item_level} >= $self->{_verbosity_level};
# 	$self->{_asterisk_agi}->verbose($arg{debug} . "\n" , $self->{_verbosity_level}) if $arg{debug} && $self->{_asterisk_agi} && $self->{_verbosity_item_level} <= $self->{_verbosity_level};
	$self->{_astpp_db}->do("INSERT INTO activity_logs (message,user) VALUES (" 
		. $self->{_astpp_db}->quote($arg{debug}) . "," 
		. $self->{_astpp_db}->quote($arg{user}) . ")") if $arg{debug} && $self->{_astpp_db} && $self->{_verbosity_item_level} >= $self->{_verbosity_level};
	return 0;
}

sub pagination #Returns the pagination html code to assist with navigation.
#db = Database connection to use if w're not using the ASTPP database;
{
	my ($self, %arg) = @_;
	my $db;
	# We are using Data::Paginate for the real tough stuff.
	# Therefore we need to pass the Data::Paginate stuff
	# as well as the total number of pages.
	#
	# mode          = What mode do we want.
	# sql           = SQL to select what we want without the limit commands.
	#
	# Check to see if there is a parameter called "results_per_page" set.  If it's set it overrides our defaults.
	if (!$arg{results_per_page} || $arg{results_per_page} > 1 ) {
		$arg{results_per_page} = 25;
	}
	if ($arg{db}) {
		$db = $arg{db};
	} else {
		$db = $self->{_astpp_db};
	}
    ## START PAGINATION CODE
    # set total_entries *once* then pass it around
    # in the object's links from then on for efficiency:
	my ($record,$sql);
	$arg{te} = 0 if !$arg{te};
    my $verify = $arg{ve} || '';
    my $total_entries = int( $arg{te} );
    my $te_match = $total_entries
      ? Digest::MD5::md5_hex("unique_cypher-$total_entries-$arg{sql_check}") : '';
    if ( !$total_entries || $verify ne $te_match ) {
	# its not ok so re-fetch
#        $sql = $self->{_astpp_db}->prepare($arg{sql_count});
	$sql = $db->prepare($arg{sql_count});
	$sql->execute;
	$record        = $sql->fetchrow_hashref;
	$total_entries = $record->{"COUNT(*)"};
	$sql->finish;
	$te_match = Digest::MD5::md5_hex("unique_cypher-$total_entries-$arg{sql_check}");
    }

    #if ($te_match <= 0) { $te_match = 0; }
    if ( $total_entries <= 0 ) { $total_entries = 1; }

    # otherwise its all ok so use it
    my $pgr = Data::Paginate->new(
	{
	    'start_array_index_at_zero'        => 1,
	    'total_entries'                    => $total_entries,
	    'entries_per_page'                 => $arg{results_per_page},
	    'total_entries_verify_param_value' => $te_match
	}
    );

    # only SELECT current page's records:
    if ( $total_entries > $pgr->get_entries_on_this_page() ) {
	$sql =
	    $arg{sql_select} . " LIMIT "
	  . ( $pgr->get_first() - 1 ) . ", "
	  . $pgr->get_entries_on_this_page();
    }
    else {
	$sql = $arg{sql_select};
    }

	# First we decide if we have multiple pages...

	if ($total_entries > 1) {
	  my $html;
	  $html =
	    "<a href=\"" . $self->{_script} . "?mode="
	  . $arg{mode} . "&ve="
	  . $arg{ve} . "&te="
	  . $total_entries
	  . "&pg=1\">"
	  . "First Page"
	  . "</a> | "
	  . scalar $pgr->get_navi_html()
	  . "<a href=\"" . $self->{_script} . "?mode="
	  . $arg{mode} . "&ve="
	  . $arg{ve} . "&te="
	  . $total_entries . "&pg="
	  . $pgr->get_last_page() . "\">"
	  . "Last Page" . "</a>";
	  return ($sql,$html);
	}
	else {
		return ($sql,"Page 1 of 1");
	}
}

sub list_pricelists
{
	my ($self, %arg) = @_;  # Return a list of all pricelists either for the appropriate reseller or without reseller.
	my ( $sql, @pricelistlist, $row, $tmp );
	if ( !$arg{reseller} || $arg{reseller} eq "") {
	$tmp =
		"SELECT name FROM pricelists WHERE status < 2 AND reseller IS NULL ORDER BY name";
	}
	else {
		$tmp =
		    "SELECT name FROM pricelists WHERE status < 2 AND reseller = "
		  . $self->{_astpp_db}->quote($arg{reseller})
		  . " ORDER BY name";
	}
	$sql = $self->{_astpp_db}->prepare($tmp);
	$sql->execute;
	while ( $row = $sql->fetchrow_hashref ) {
		push @pricelistlist, $row->{name};
	}
	$sql->finish;
	return @pricelistlist;
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


sub account_cdr_post
{
	my ($self, %arg) = @_; # Create invoice in ASTPP Internally and return the invoice number.
	$arg{description}  = ""  if !$arg{timestamp};
	$arg{pricelist}  = ""  if !$arg{pricelist};
	$arg{pattern}  = ""  if !$arg{pattern};
	$arg{answeredtime} = "0" if !$arg{answeredtime};
	$arg{uniqueid} = "N/A" if $arg{uniqueid} eq "" || !$arg{uniqueid};
	$arg{clid} = "N/A" if $arg{clid} eq "" || !$arg{clid};

	my $tmp = "INSERT INTO cdrs (uniqueid, cardnum, callednum, debit,"
		. " billseconds, callstart,callerid,pricelist,pattern) VALUES ("
		. $self->{_astpp_db}->quote($arg{uniqueid}) . ", "
		. $self->{_astpp_db}->quote($arg{account}) . ","
		. $self->{_astpp_db}->quote($arg{description}) . ", "
		. $self->{_astpp_db}->quote($arg{amount}) . ", "
		. $self->{_astpp_db}->quote($arg{answeredtime}) . ", "
		. $self->{_astpp_db}->quote($arg{timestamp}) . ", "
		. $self->{_astpp_db}->quote($arg{clid}) . ","
		. $self->{_astpp_db}->quote($arg{pricelist}) . ","
		. $self->{_astpp_db}->quote($arg{pattern}) . ")";

    if ( $self->{_astpp_db}->do($tmp) ) {
	return (1, "POSTED CDR: $arg{account} in the amount of: " . $arg{amount} / 1 . "\n");
    }
    else {
	return (2, $tmp . " FAILED! \n");
    }
}

=item $ASTPP->get_did()

Returns the details on the specified DID.

Example:

$diddata = $ASTPP->get_did(
	reseller => $carddata->{reseller},
	did      => $destination
);

=cut

sub get_did() {
	my ($self, %arg) = @_;
    my ( $tmp, $sql, $diddata );
	if (!$arg{reseller} || $arg{reseller} eq "") {
		$tmp = "SELECT * FROM dids WHERE number = " 
			. $self->{_astpp_db}->quote($arg{did});
	} else {
    $tmp =
	"SELECT dids.number AS number, "
	. "reseller_pricing.monthlycost AS monthlycost, "
	. "reseller_pricing.prorate AS prorate, "
	. "reseller_pricing.setup AS setup, "
	. "reseller_pricing.cost AS cost, "
	. "reseller_pricing.connectcost AS connectcost, "
	. "reseller_pricing.includedseconds AS includedseconds, "
	. "reseller_pricing.inc AS inc, "
	. "reseller_pricing.disconnectionfee AS disconnectionfee, "
	. "dids.provider AS provider, "
	. "dids.country AS country, "
	. "dids.city AS city, "
	. "dids.province AS province, "
	. "dids.extensions AS extensions, "
	. "dids.account AS account, "
	. "dids.variables AS variables, "
	. "dids.options AS options, "
	. "dids.maxchannels AS maxchannels, "
	. "dids.chargeonallocation AS chargeonallocation, "
	. "dids.allocation_bill_status AS allocation_bill_status, "
	. "dids.limittime AS limittime, "
	. "dids.dial_as AS dial_as, "
	. "dids.status AS status "
	. "FROM dids, reseller_pricing "
	. "WHERE dids.number = " . $self->{_astpp_db}->quote($arg{did})
	. " AND reseller_pricing.type = '1' AND reseller_pricing.reseller = "
	. $self->{_astpp_db}->quote($arg{reseller}) . " AND reseller_pricing.note = "
	. $self->{_astpp_db}->quote($arg{did});
	}
    print STDERR "$tmp\n";
    $sql =
      $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    $diddata = $sql->fetchrow_hashref;
    $sql->finish;
    return $diddata;
}

=item $ASTPP->get_account()

Returns the details on the specified ASTPP account. It will search first by
"cardnum" then by "cc" and finally by "accountid".  Accountid is the prefered
method but most installations use cardnum for legacy reasons.

Example:

$carddata = $ASTPP->get_account(
	account => $params->{accountcode}
);

=cut

sub get_account() {
	my ($self, %arg) = @_;
    my ( $sql, $accountdata );
    $sql =
      $self->{_astpp_db}->prepare( "SELECT * FROM accounts WHERE number = "
	  . $self->{_astpp_db}->quote($arg{account})
	  . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $self->{_astpp_db}->prepare( "SELECT * FROM accounts WHERE cc = "
	  . $self->{_astpp_db}->quote($arg{account})
	  . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    }
    if ($accountdata) {
	return $accountdata;
    } else {
    $sql =
      $self->{_astpp_db}->prepare( "SELECT * FROM accounts WHERE accountid = "
	  . $self->{_astpp_db}->quote($arg{account})
	  . " AND status = 1" );
    $sql->execute;
    $accountdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $accountdata;
    }
}

=item $ASTPP->get_pricelist()

Returns the details on the specified pricelist.  Is used both internally and
externally.

Example:

$pricelistdata = $ASTPP->get_pricelist(
	account => $carddata->{pricelist}
);

=cut

sub get_pricelist() {
	my ($self, %arg) = @_;
   my $tmp = "SELECT * FROM pricelists WHERE name = " . $self->{_astpp_db}->quote($arg{pricelist});
    my $sql = $self->{_astpp_db}->prepare($tmp);
    print STDERR "$tmp\n" . "\n";
    $sql->execute;
    my $pricelistdata = $sql->fetchrow_hashref;
    $sql->finish;
    return $pricelistdata;
}

=item $ASTPP->max_length()

Returns the maximum allowable length for a call.

Example:

$max_length = $ASTPP->max_length(
	account_pricelist => $carddata->{pricelist},
	account_credit_limit => $carddata->{creditlimit},
	account         => $carddata->{number}, 
	destination     => $destination,
	call_max_length => $config->{call_max_length},
	max_free_length => $config->{max_free_length}
);

=cut
sub max_length() {
	my ($self, %arg) = @_;
	my ($branddata, $numdata, $credit, $credit_limit, $maxlength);
	$branddata = &get_pricelist($self, pricelist => $arg{account_pricelist} );       # Fetch all the brand info from the db.
	$numdata = &get_route($self, account => $arg{account}, destination => $arg{destination}, pricelist => $arg{account_pricelist} );    # Find the appropriate rate to charge the customer.

	if ( !$numdata->{pattern} ){  # If the pattern doesn't exist, we don't know what to charge the customer
		# and therefore must exit.
		print STDERR "CALLSTATUS 1\n" if $arg{debug} == 1;
		print STDERR "INVALID PHONE NUMBER\n" if $arg{debug} == 1;
		return (1,0);
	}
	print STDERR "Found pattern: $numdata->{pattern}\n";
	$credit = &accountbalance($self, account => $arg{account} ); # Find the available credit to the customer.
	print STDERR "Account Balance: " . $credit * 1;
	$credit_limit = $arg{account_credit_limit} * 1;
	print STDERR "Credit Limit: $credit_limit";
	$credit = ($credit * -1) + ($credit_limit);         # Add on the accounts credit limit.
	#$credit = $credit / $arg{maxchannels} if $arg{maxchannels} > 0;
	print STDERR "Credit: $credit \n";
	if ($branddata->{markup} > 0) {
		$numdata->{connectcost} =
		$numdata->{connectcost} * ( ( $branddata->{markup} / 1 ) + 1 );
		$numdata->{cost} =
		$numdata->{cost} * ( ( $branddata->{markup} / 1 ) + 1 );
	}
	if ( $numdata->{connectcost} > $credit ) {   # If our connection fee is higher than the available money we can't connect.
		return (0,0);
	}
	if ( $numdata->{cost} > 0 ) {
		$maxlength = ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
		if ($arg{call_max_length} && $maxlength < $arg{call_max_length} / 1000){
			$maxlength = $arg{call_max_length} / 1000 / 60;
		}
	}
	else {
		$maxlength = $arg{max_free_length};    # If the call is set to be free then assign a max length.
	}
	if ( $numdata->{cost} > 0 ) {
		$maxlength = ( ( $credit - $numdata->{connectcost} ) / $numdata->{cost} );
		if ($arg{call_max_length} && $maxlength < $arg{call_max_length} / 1000){
			$maxlength = $arg{call_max_length} / 1000 / 60;
		}
	}
	else {
		$maxlength = $arg{max_free_length};        # If the call is set to be free then assign a max length.
	}
	return (1, $maxlength,$numdata);
}

=item $ASTPP->accountbalance()

Return the balance for a specific ASTPP account. 

Example:

$balance .= $ASTPP->max_length(
	account => $carddata->{number},
);

=cut
 
sub accountbalance() {
	my ($self, %arg) = @_;
    my ( $tmp, $sql, $row, $debit, $credit, $balance, $posted_balance );
    $tmp = "SELECT SUM(cdrs.debit),SUM(cdrs.credit),accounts.balance "
	. " from cdrs,accounts WHERE cdrs.cardnum = "
        . $self->{_astpp_db}->quote($arg{account})
	. " AND cdrs.status NOT IN(1,2)"
	. " AND accounts.number = "
        . $self->{_astpp_db}->quote($arg{account});
    $sql =
      $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $credit = $row->{"SUM(cdrs.credit)"};
    $debit = $row->{"SUM(cdrs.debit)"};
    $posted_balance = $row->{"balance"};
    $sql->finish;
    if ( !$credit )         { $credit         = 0; }
    if ( !$debit )          { $debit          = 0; }
    if ( !$posted_balance )          { $posted_balance         = 0; }
    $balance = ( $debit - $credit + $posted_balance );
    $sql->finish;
    return $balance;
}


=item $ASTPP->get_route()

Return the appropriate "route" to use for determining costing on a call.  This
is used both in rating as well as in determining the maximum length of a call.

Example:

$routeinfo = $ASTPP->get_route(
	thirdlane_mods  => $config->{thirdlane_mods},
	account         => $carddata->{number}, #accountnumber
	type            => $userfield, # etc,etc
	reseller        => $carddata->{reseller},
	destination     => $destination, #number we care calling
	default_brand   => $config->{default_brand}
);

=cut

sub get_route() {
    my ($self, %arg) = @_;
    my ($branddata, $record,   $sql,    $tmp );
    my $carddata = &get_account($self, account => $arg{account});
    if ($arg{type} =~ /ASTPP-DID/) {
	print STDERR "Call belongs to a DID.\n";
	$record = &get_did( reseller => $arg{reseller}, did => $arg{destination});
	$record->{comment} = $record->{city} . "," . $record->{province} . "," . $record->{country};
	$record->{pattern} = "DID:" . $arg{destination};
	$record->{pricelist} = $arg{pricelist};
	$branddata = &get_pricelist($self, pricelist => $carddata->{pricelist});
	print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
    }
    elsif ($arg{thirdlane_mods} == 1 && $arg{type} =~ m/.\d\d\d-IN/) {
	print STDERR "Call belongs to a Thirdlane(tm) DID.\n";
	($arg{destination} = $arg{type}) =~ s/-IN//g;
	print STDERR "Destination: $arg{destination} \n";
	$record = &get_did( reseller => $arg{reseller}, did => $arg{destination});
	$record->{comment} = $record->{city} . "," . $record->{province} . "," . $record->{country}; $record->{pattern} = "DID:" . $arg{destination};
	$branddata = &get_pricelist($self, pricelist => $carddata->{pricelist});
	print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
    }
    else {
	my @pricelists = split ( m/,/m, $carddata->{pricelist} );
	foreach my $pricelistname (@pricelists) {
		$pricelistname =~ s/"//g;                               #Strip off quotation marks
		print STDERR "Pricelist: $pricelistname \n";
		$record = &search_for_route($self, destination => $arg{destination}, pricelist => $arg{pricelist});
		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
		last if $record->{pattern}; #Returnes if we've found a match.
	}

	while ( !$record->{pattern} && $carddata->{reseller} ) {
		$carddata = &get_account($self, account => $carddata->{reseller});
		$record = &search_for_route($self, destination => $arg{destination}, pricelist => $arg{pricelist});
		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
	}
	if (!$record->{pattern}) { #If we still haven't found a match then we modify the dialed number as per the regular expressions set
				# in the account.
		my @regexs = split(m/","/m, $carddata->{dialed_modify});
		foreach my $regex (@regexs) {
			$regex =~ s/"//g;                               #Strip off quotation marks
			my ($grab,$replace) = split(m!/!i, $regex);  # This will split the variable into a "grab" and "replace" as needed
			print STDERR "Grab: $grab\n";
			print STDERR "Replacement: $replace\n";
			print STDERR "Phone Before: $arg{destination}\n";
			$arg{destination} =~ s/$grab/$replace/is;
			print STDERR "Phone After: $arg{destination}\n";
		}
		$record = &search_for_route($self, destination => $arg{destination}, pricelist => $arg{default_brand});
		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
	}
	if ( !$record->{pattern} ) { #If we have not found a route yet then we look in the "Default" pricelist.
		$record = &search_for_route($self, destination => $arg{destination}, pricelist => $arg{default_brand});
		print STDERR "pattern: $record->{pattern}\n" if $record->{pattern};
	}
	print STDERR "Route: $record->{comment} Cost: $record->{cost} Pricelist: $record->{pricelist} Pattern: $record->{pattern}\n" if $record;
    }
    if ( $record->{inc} eq "" || $record->{inc} == 0 ) {
	$branddata = &get_pricelist($self, pricelist => $arg{pricelist});
	$record->{inc} = $branddata->{inc};
    }
    return $record;
}


=item $ASTPP->search_for_route()

Return the exact route.  This is used internally and will only very rarely be
used outside of this module.

Example:

$routeinfo = $ASTPP->search_for_route(
	pricelist       => $carddata->{pricelist},
	reseller        => $carddata->{reseller}
);

=cut

sub search_for_route(){
    my ($self, %arg) = @_;
	my ($tmp,$sql,$record);
    $tmp = "SELECT * FROM routes WHERE "
	  . $self->{_astpp_db}->quote($arg{destination})
	  . " RLIKE pattern AND pricelist = "
	  . $self->{_astpp_db}->quote($arg{pricelist})
	  . " ORDER BY LENGTH(pattern) DESC LIMIT 1";
    print STDERR "$tmp\n";
    $sql =
      $self->{_astpp_db}->prepare($tmp);
    $sql->execute;
    $record = $sql->fetchrow_hashref;
    $sql->finish;
    return $record;
};




sub fs_dialplan_xml_header_shout
{
	my ($self, %arg) = @_;
	$arg{xml} .= "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
	$arg{xml} .= "<document type=\"freeswitch/xml\">\n";
	$arg{xml} .= "<section name=\"dialplan\" description=\"ASTPP Dynamic Routing Shout\">\n";	
	$arg{xml} .= "<context name=\"default\">\n";	
	$arg{xml} .= "<extension name=\"shout\">\n";
	$arg{xml} .= "<condition field=\"destination_number\" expression=\"" . $arg{destination_number} . "\">\n";
	return $arg{xml};
}

sub fs_dialplan_xml_footer_shout() {
	my ($self, %arg) = @_;
	$arg{xml} .= "</condition>\n";
	$arg{xml} .= "</extension>\n";
	$arg{xml} .= "</context>\n";
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
