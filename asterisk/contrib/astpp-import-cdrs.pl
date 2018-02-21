#!/usr/bin/perl
# Author:	ASTPP Team <info@astpp.org>
#
# This program was written by ASTPP Team <info@astpp.org>.
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2 or later
# at your option.
#

use DBI;
use Text::CSV;


require "/usr/local/astpp/astpp-common.pl";
$ENV{LANGUAGE} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: " . $ENV{LANGUAGE} . "\n";
use vars qw(@output $astpp_db $config
  $status $config $ASTPP);

$infile = $ARGV[0];
$outfile = $ARGV[1];

sub initialize() {
    $config = &load_config();
    $astpp_db = &connect_db( $config, @output );
    $config = &load_config_db( $astpp_db, $config ) if $astpp_db;
    $cdr_db = &cdr_connect_db( $config,  @output );
}

sub finduniqueid() {
    my ( $cc, $count, $startingdigit, $sql, $record );
    for ( ; ; ) {
        $count = 1;
        $cc    =
            int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 )
          . int( rand() * 9000 + 1000 );
            $sql =
              $cdr_db->prepare(
                "SELECT COUNT(*) FROM cdr WHERE uniqueid  = "
                  . $cdr_db->quote($cc) );
            $sql->execute;
            $record = $sql->fetchrow_hashref;
            $count  = $record->{"COUNT(*)"};
            $sql->finish;
        return $cc if ( $count == 0 );
    }
}


sub read_file {
	open(READFILE,"$infile") ||
	    die "Error - could not open $infile for reading.\n";
	@data =<READFILE>;
	close(READFILE);
		my $csv = Text::CSV->new();
		foreach $temp (@data) {
			print STDERR $temp;
			if ( $csv->parse ($temp) ) {
		                my $tmp;
		                @column = $csv->fields();
				$uniqueid = &finduniqueid;
				print STDERR "DURATION: $column[13] UNIQUEID: $uniqueid  ACCOUNT: $column[0] DST: $column[2]\n";
				&printsql;
			} else {
				my $error = $csv->error_input;
				$status .= "pars() failed on argument: " . $error . "<br>";
			}

		}	
}

sub printsql() {
	open ( OUTFILE, ">>$outfile" ) || die "Could not open outfile";
	$tmp = "INSERT INTO cdr (calldate,clid,src,dst,dcontext,channel,dstchannel,lastapp,lastdata,duration,billsec,disposition,amaflags,accountcode,uniqueid,cost,vendor) VALUES (" .
					$cdr_db->quote($column[9]) . ", " .
					$cdr_db->quote($column[4]) . ", " .
					$cdr_db->quote($column[1]) . ", " .
					$cdr_db->quote($column[2]) . ", " .
					$cdr_db->quote($column[3]) . ", " .
					$cdr_db->quote($column[5]) . ", " .
					$cdr_db->quote($column[6]) . ", " .
					$cdr_db->quote($column[7]) . ", " .
					$cdr_db->quote($column[8]) . ", " .
					$cdr_db->quote($column[12]) . ", " .
					$cdr_db->quote($column[13]) . ", " .
					$cdr_db->quote($column[14]) . ", " .
					$cdr_db->quote($column[15]) . ", " .
					$cdr_db->quote($column[0]) . ", " .
					$cdr_db->quote($uniqueid) . ", " .
					$cdr_db->quote("none") . ", " .
					$cdr_db->quote("none") . ");";
	print STDERR $tmp . "\n";
	print OUTFILE $tmp . "\n";
	close (OUTFILE);
}


if ($infile eq "" || $outfile eq "") {
	print "\n---- Command Line Error ---- \n\n";
	print "Please call this program with your csv file as the first argument.\n";
	print "The next argument should be the name of the file you want the sql dumped\n";
	print "into.  For more info pleas peruse the source code.\n";
	print "ie ./astpp-import-cdrs.pl ./Master.csv ./Master.sql\n\n";
	exit(0);
}

&initialize();
	
&read_file;
