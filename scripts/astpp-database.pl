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

use POSIX qw(strftime);
use DBI;
use strict;
use warnings;

#Intialize the database connections
sub initialize() {

    my $config = &load_config();            
    $gbl_astpp_db = &connect_db( $config);        
    $gbl_config = &load_config_db( $gbl_astpp_db, $config ) if $gbl_astpp_db;
}


# Load Basic Configuration File
no warnings 'redefine';
sub load_config {
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

# Connect to ASTPP database.
sub connect_db() {
    my ( $config) = @_;
    my ( $dbh, $dsn );
    if ( $config->{astpp_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{dbname};host=$config->{dbhost}";
    }
    elsif ( $config->{astpp_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{dbname};host=$config->{dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{dbuser}, $config->{dbpass} );
    if ( !$dbh ) {
        print STDERR "ASTPP DATABASE IS DOWN\n" if $config->{debug} == 1;
	    return 0;
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        return $dbh;
    }
}

# Load configuration from database.  Please pass the configuration from astpp-config.conf along.  This will overwrite
# those settings with settings from the database.
sub load_config_db() {
    my ($astpp_db, $config) = @_;
    my ($sql, @didlist, $row, $tmp );
    
    $tmp = "SELECT name,value FROM system WHERE group_title IN ('callingcard','global','opensips')";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        $config->{$row->{name}} = $row->{value};
    }
    $sql->finish;
    return $config;
}


# Connect to opensips database.
sub connect_opensipsdb() {
    my ( $config, @output ) = @_;
    my ( $dbh, $dsn );
    if ( $config->{opensips_dbengine} eq "MySQL" ) {
        $dsn = "DBI:mysql:database=$config->{opensips_dbname};host=$config->{opensips_dbhost}";
    }
    elsif ( $config->{opensips_dbengine} eq "Pgsql" ) {
        $dsn = "DBI:Pg:database=$config->{opensips_dbname};host=$config->{opensips_dbhost}";
    }
    $dbh = DBI->connect( $dsn, $config->{opensips_dbuser}, $config->{opensips_dbpass} );
    if ( !$dbh ) {        
	    #$ASTPP->debug(debug =>"opensips DATABASE IS DOWN");
    }
    else {
        $dbh->{mysql_auto_reconnect} = 1;
        return $dbh;
    }
}

sub disconnect_db()
{    
    $gbl_astpp_db->disconnect;
}


sub select_query()
{
    my ( $info,$query,$mode ) = @_;
    my ( $sql, $data,$tmp,@list );
    
    $mode = "single" if !$mode;
            
    &logger("$info SQL : $query");
    $sql = $gbl_astpp_db->prepare($query);    
    $sql->execute;
    
    if($mode eq 'single')
    {
        $data = $sql->fetchrow_hashref;
        $sql->finish;
        return $data;
    }else{
	    while ( $data = $sql->fetchrow_hashref ) {
		    push @list, $data;
	    }
	    $sql->finish;
	    return @list;
    }
}

sub insert_update_query()
{
    my ( $info,$query ) = @_;       
    &logger("$info SQL : $query");
    $gbl_astpp_db->do($query);    
}

1;

