#!/usr/bin/perl
#
# ASTPP - Open Source Voip Billing
#
# Copyright (C) 2004, Aleph Communications
#
# Darren Wiebe (darren@aleph-com.net)
#
# This program is Free Software and is distributed under the
# terms of the GNU General Public License version 2.
############################################################
#
################### NOTHING AFTER HERE SHOULD NEED CHANGING ####################
# You will need lines like this in your crontab
#
# @hourly /usr/local/astpp/astpp-update-balance.pl
# @daily /usr/local/astpp/astpp-update-balance.pl sweep=0
# 0 0 * * 0 /usr/local/astpp/astpp-update-balance.pl sweep=1
# 0 0 1 * * /usr/local/astpp/astpp-update-balance.pl sweep=2
# 0 0 1 1,4,7,10 * /usr/local/astpp/astpp-update-balance.pl sweep=3
# 0 0 * 1,7 * /usr/local/astpp/astpp-update-balance.pl sweep=4
# 0 0 * 1 * /usr/local/astpp/astpp-update-balance.pl sweep=5
#
use POSIX;
use POSIX qw(strftime);
use DBI;
use CGI;
use CGI qw/:standard Vars/;
use Getopt::Long;
use Locale::Country;
use Locale::gettext_pp qw(:locale_h);
use Data::Dumper;

#use strict;
use lib './lib', '../lib';
require "/usr/local/astpp/astpp-common.pl";
$ENV{'LANGUAGE'} = "en";    # de, es, br - whatever
print STDERR "Interface language is set to: $ENV{'LANGUAGE'}\n";
bindtextdomain( "ASTPP", "/var/locale" );
textdomain("ASTPP");
use vars qw($enh_config $astpp_db $osc_db $agile_db $cdr_db
  @output @cardlist $config $params);
@output = ( "STDOUT", "LOGFILE" );

sub initialize() {
    $config     = &load_config();
    $enh_config = &load_config_enh();
    $astpp_db = &connect_db( $config,     $enh_config, @output );
    $config     = &load_config_db($astpp_db,$config) if $astpp_db;
    $cdr_db   = &cdr_connect_db( $config, $enh_config, @output );
    $osc_db   = &osc_connect_db( $config, $enh_config, @output )
      if $enh_config->{externalbill} eq "oscommerce";
    open( LOGFILE, ">>$config->{log_file}" )
      || die "Error - could not open $config->{log_file} for writing\n";
}

sub update_list_cards() {
    my ($sweep) = @_;
    my ( $sql, @cardlist, $row );
    if ( $sweep eq "" ) {
        $sql =
          $astpp_db->prepare(
"SELECT number FROM accounts WHERE status < 2 AND reseller IN (NULL,'') AND posttoexternal = 0 "
          );
    }
    else {
        $sql =
          $astpp_db->prepare(
"SELECT number FROM accounts WHERE status < 2 AND reseller IN (NULL,'') AND sweep = "
              . $astpp_db->quote($sweep) );
    }
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @cardlist, $row->{number};
    }
    $sql->finish;
    return @cardlist;
}

sub osc_charges() {
    my ($account) = @_;
    my ( $tmp, $sql, $row, $cdr_count );
    $tmp =
        "SELECT COUNT(*) FROM cdrs WHERE cardnum = "
      . $astpp_db->quote($account)
      . " AND status = 0";
    print STDERR "$tmp \n";
    $sql = $astpp_db->prepare($tmp);
    $sql->execute;
    $row       = $sql->fetchrow_hashref;
    $cdr_count = $row->{"COUNT(*)"};
    $sql->finish;
    print STDERR "OSC_COUNT: $cdr_count \n";

    if ( $cdr_count > 0 ) {
        my (
            $tmp,  $subtotal,  $country_id, $zone_id,      @taxes,
            $sort, $tax_count, $total,      $tax_priority, $tax
        );
        my ( $invoice_id, $country_id, $zone_id ) =
          &osc_create_invoice($account);
        $tmp =
            "SELECT * FROM cdrs WHERE cardnum = "
          . $astpp_db->quote($account)
          . " AND status = 0 ORDER BY callstart";
        $sql = $astpp_db->prepare($tmp);
        $sql->execute;
        while ( $row = $sql->fetchrow_hashref ) {
            &osc_post_charge( $invoice_id, $row );
            &markbilled( $row->{id}, 1 );
        }
        $subtotal = &osc_order_subtotal($invoice_id);
        print STDERR "ORDER $invoice_id SUBTOTAL: $subtotal";
        $sort      = 1;
        $tax_count = 1;
        &osc_post_total( $invoice_id, "Sub-Total:", "\$$subtotal", $subtotal,
            $sort, "ot_subtotal" );
        @taxes = &osc_tax_list( $zone_id, $country_id );
        foreach $tax (@taxes) {
            my ($tax_amount);
            if ( $tax_count == 1 ) {
                $tax_priority = $tax->{tax_priority};
                $tax_amount = $subtotal * ( $tax->{tax_rate} / 100 );
                $sort++;
                &osc_post_total( $invoice_id, $tax->{tax_description},
                    "\$$tax_amount", $tax_amount, $sort, "ot_tax" );
                $tax_count++;
            }
            else {
                if ( $tax->{tax_priority} > $tax_priority ) {
                    $subtotal = &osc_order_total($invoice_id);
                }
                $tax_priority = $tax->{tax_priority};
                $tax_amount = $subtotal * ( $tax->{tax_rate} / 100 );
                $sort++;
                &osc_post_total( $invoice_id, $tax->{tax_description},
                    "\$$tax_amount", $tax_amount, $sort, "ot_tax" );
            }
        }
        $total = &osc_order_total($invoice_id);
        $sort++;
        &osc_post_total( $invoice_id, "Total:", "<b>\$$total</b>", $total,
            $sort, "ot_total" );
        &email_new_invoice( $config, $enh_config, $account, $invoice_id,
            $total );
    }
}

sub osc_order_total() {
    my ($invoice_id) = @_;
    my ( $sql, $tmp, $row );
    $tmp =
      "SELECT SUM(value) FROM orders_total WHERE orders_id="
      . $osc_db->quote($invoice_id);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row->{"SUM(value)"};
}

sub osc_post_total() {
    my ( $invoice_id, $title, $text, $value, $sort, $class ) = @_;
    my ( $sql, $tmp );
    $tmp =
"INSERT INTO `orders_total` (`orders_id`,`title`,`text`,`value`,`class`,`sort_order`) VALUES ("
      . $osc_db->quote($invoice_id) . ", "
      . $osc_db->quote($title) . ", "
      . $osc_db->quote($text) . ", "
      . $osc_db->quote($value) . ", "
      . $osc_db->quote($class) . ", "
      . $osc_db->quote($sort) . ")";
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
}

sub osc_tax_list() {
    my ( $zone_id, $zone_country_id ) = @_;
    my ( $tmp, $row, $sql, @taxes, @tax_list, $count, $tot_count, $tax_list );
    $tmp =
        "SELECT * FROM zones_to_geo_zones WHERE zone_id IN ("
      . $osc_db->quote($zone_id)
      . ", '0') OR zone_id IS NULL AND zone_country_id = "
      . $osc_db->quote($zone_country_id);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $tot_count = 0;
    while ( $row = $sql->fetchrow_hashref ) {
        push @tax_list, $row->{'geo_zone_id'};
        print STDERR "GEO ZONE ID: $row->{'geo_zone_id'} \n";
        $tot_count++;
    }
    $sql->finish;
    if ( $tot_count != 0 ) {
        $count    = 0;
        $tax_list = "IN (";
        foreach my $number (@tax_list) {
            print STDERR "SQL COMMAND GEO ZONE ID: $number \n";
            $tax_list .= $number;
            $count++;
            if ( $count < $tot_count ) {
                $tax_list .= ",";
            }
        }
        $tax_list .= ")";
    }
    else {
        $tax_list = "IS NULL";
    }
    $tmp =
        "SELECT * FROM tax_rates WHERE tax_zone_id "
      . $tax_list
      . " ORDER BY tax_priority DESC";
    print STDERR $tmp;
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    while ( $row = $sql->fetchrow_hashref ) {
        push @taxes, $row;
    }
    $sql->finish;
    return @taxes;
}

sub osc_order_subtotal() {
    my ($invoice_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT SUM(final_price) FROM orders_products WHERE orders_id="
      . $osc_db->quote($invoice_id);
    print STDERR "$tmp";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row->{"SUM(final_price)"};
}

sub osc_get_accountinfo() {
    my ($account) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `customers` WHERE `customers_email_address` = "
      . $osc_db->quote($account);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;

    if ( $row->{customers_email_address} ) {
        return $row;
    }
    $tmp =
      "SELECT * FROM `customers` WHERE `customers_username` = "
      . $osc_db->quote($account);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    if ( $row->{customers_email_address} ) {
        return $row;
    }
    else {
        print STDERR "OSCommerce Account: $account NOT FOUND!!!\n";
        exit(0);
    }
}

sub osc_get_addressinfo() {
    my ($address_book_id) = @_;
    my ( $sql, $row );
    $sql =
      $osc_db->prepare(
        "SELECT * FROM `address_book` WHERE `address_book_id` = "
          . $osc_db->quote($address_book_id) );
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_get_country() {
    my ($country_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `countries` WHERE `countries_id` = "
      . $osc_db->quote($country_id);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_get_zone() {
    my ($zone_id) = @_;
    my ( $tmp, $sql, $row );
    $tmp =
      "SELECT * FROM `zones` WHERE `zone_id` = " . $osc_db->quote($zone_id);
    print STDERR "$tmp \n";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
    $row = $sql->fetchrow_hashref;
    $sql->finish;
    return $row;
}

sub osc_create_invoice() {
    my ($account) = @_;
    my ( $customer_info, $address_info, $customers_name );
    $customer_info = &osc_get_accountinfo($account);
    $address_info  =
      &osc_get_addressinfo( $customer_info->{customers_default_address_id} );
    $customers_name =
        $customer_info->{customers_firstname} . " "
      . $customer_info->{customers_lastname};
    my $country_info   = &osc_get_country( $address_info->{entry_country_id} );
    my $state_info     = &osc_get_zone( $address_info->{entry_zone_id} );
    my $date_purchased = &prettytimestamp;
    my $sql            = $osc_db->prepare(
            "INSERT INTO orders (customers_id,customers_name, "
          . "customers_company,customers_street_address,customers_suburb,customers_city, "
          . "customers_postcode, customers_state, customers_country, customers_telephone, "
          . "customers_email_address, customers_address_format_id, delivery_name, delivery_company, "
          . "delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, "
          . "delivery_state, delivery_country, delivery_address_format_id, billing_name, "
          . "billing_company, billing_street_address, billing_suburb, billing_city, "
          . "billing_postcode, billing_state, billing_country, billing_address_format_id , "
          . "payment_method, cc_type, cc_owner, cc_number, cc_expires, last_modified, "
          . "date_purchased, orders_status, orders_date_finished, currency, currency_value) "
          . " VALUES ( "
          . $osc_db->quote( $customer_info->{customers_id} )
          . ", "                                      # customers_id
          . $osc_db->quote($customers_name) . ", "    #customers_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                      #customers_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                      #customers_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                      #customers_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", " #customers_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "    #customers_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", " #customers_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                              #customers_country
          . $osc_db->quote( $customer_info->{customers_telephone} )
          . ", "    #customers_telephone
          . $osc_db->quote( $customer_info->{customers_email_address} )
          . ", "    #customers_email_address
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "                                    #customers_address_format_id
          . $osc_db->quote($customers_name) . ", "  #delivery_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                    #delivery_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                    #delivery_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                    #delivery_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", "  #delivery_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "                                              #delivery_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", " #delivery_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                              #delivery_country
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "                                     #delivery_address_format_id
          . $osc_db->quote($customers_name) . ", "   #billing_name
          . $osc_db->quote( $address_info->{entry_company} )
          . ", "                                     #billing_company
          . $osc_db->quote( $address_info->{entry_street_address} )
          . ", "                                     #billing_street_address
          . $osc_db->quote( $address_info->{entry_suburb} )
          . ", "                                                 #billing_suburb
          . $osc_db->quote( $address_info->{entry_city} ) . ", " #billing_city
          . $osc_db->quote( $address_info->{entry_postcode} )
          . ", "                                               #billing_postcode
          . $osc_db->quote( $state_info->{zone_code} ) . ", "  #billing_state
          . $osc_db->quote( $country_info->{countries_name} )
          . ", "                                               #billing_country
          . $osc_db->quote( $country_info->{address_format_id} )
          . ", "    #billing_address_format_id
          . $osc_db->quote( $enh_config->{osc_payment_method} )
          . ", "                                      #payment_method
          . "'', "                                    #cc_type
          . "'', "                                    #cc_owner
          . "'', "                                    #cc_number
          . "'', "                                    #cc_expires
          . $osc_db->quote($date_purchased) . ", "    #last_modified
          . $osc_db->quote($date_purchased) . ", "    #date_purchased
          . $osc_db->quote( $enh_config->{osc_order_status} )
          . ", "                                      #orders_status
          . "'',"                                     #orders_date_finished
          . "'USD',"                                  #currency
          . "1)"
    );                                                #currency_value
    $sql->execute;
    my $invoice = $sql->{'mysql_insertid'};
    $sql->finish;
    return (
        $invoice,
        $address_info->{entry_country_id},
        $address_info->{entry_zone_id}
    );
}

sub osc_post_charge() {
    my ( $invoice_id, $row ) = @_;
    my ( $sql, $desc, $tmp, $price );
    $desc  = "$row->{callstart} $row->{callednum} SEC:$row->{billseconds}";
    $price = $row->{debit} / 10000;
    $tmp   =
"INSERT INTO `orders_products` (`orders_products_id`,`orders_id`,`products_id`,`products_name`,`products_price`,"
      . "`final_price`,`products_tax`,`products_quantity`) VALUES ('',"
      . "$invoice_id, "
      . $osc_db->quote( $enh_config->{osc_product_id} ) . ", "
      . $osc_db->quote($desc) . ", "
      . "$price, $price,0,1)";
    $sql = $osc_db->prepare($tmp);
    $sql->execute;
}

sub markbilled() {
    my ( $id, $status ) = @_;
    $astpp_db->do("UPDATE cdrs SET status = $status WHERE id = $id");
}

sub get_charges() {
    my ($number) = @_;
    my ( $sql, @chargelist, $record );
    $sql =
      $astpp_db->prepare( "SELECT * FROM cdrs WHERE cardnum = "
          . $astpp_db->quote($number)
          . " AND status = 0" );
    $sql->execute;
    while ( $record = $sql->fetchrow_hashref ) {
        push @chargelist, $record->{id};
    }
    $sql->finish;
    return @chargelist;
}

sub calc_charges() {
    my $cardno = $_;
    my $cost;
    my @chargelist = &get_charges($cardno);
    foreach my $id (@chargelist) {

        #        my $id = $_;
        foreach my $handle (@output) {
            print $handle "ID: $id\n";
        }
        my $chargeinfo = &get_astpp_cdr( $astpp_db, $id );
        $cost = $cost + $chargeinfo->{debit};
        foreach my $handle (@output) {
            print $handle
              "Debit: $chargeinfo->{debit}  Credit: $chargeinfo->{credit}\n";
        }
        $cost = $cost - $chargeinfo->{credit};
        &markbilled( $id, 1 );
    }
    return $cost;
}
################# Program Starts HERE #################################
foreach my $param ( param() ) {
    $params->{$param} = param($param);
    print STDERR "$param $params->{$param}\n";
}
&initialize();
@cardlist = &update_list_cards();
foreach (@cardlist) {
    my $cardno = $_;
    foreach my $handle (@output) {
        print $handle "Card: $cardno \n";
    }
    my $cardinfo = &get_account( $astpp_db, $cardno );
    my $cost = &calc_charges($cardno);
    if ( $cost != 0 ) {
        my $balance = $cost + $cardinfo->{balance};
        &update_astpp_balance( $astpp_db, $cardno, $balance );
    }
}
if ( $params->{sweep} ) {
    my @pricelistlist = &list_pricelists($astpp_db);
    foreach my $pricelist (@pricelistlist) {
        my @pricelist_charge_list =
          &list_pricelist_charges( $astpp_db, $pricelist );
        my @account_list = &list_pricelist_accounts( $astpp_db, $pricelist );
        if ( $params->{sweep} == 2 ) { #If it's monthly billing we process DIDs.
            foreach my $account (@account_list) {
                my $accountinfo = &get_account( $astpp_db,       $account );
                my @did_list    = &list_dids_account( $astpp_db, $account );
                foreach my $did (@did_list) {
                    my $now = &prettytimestamp;
                    &post_cdr(
                        $astpp_db,
                        $enh_config,
                        '',
                        $account,
                        '',
                        'DID: ' . $did->{number},
                        '',
                        '',
                        $did->{monthlycost},
                        $now,
                        $accountinfo->{posttoexternal},
                        ''
                    );
                    if ( $enh_config->{externalbill} eq "agile" ) {
                        my $cost = $did->{monthlycost} / 100;
                        my $cdrinfo;
                        $cdrinfo->{calldate} = $now;
                        $cdrinfo->{dst}      = $did->{number};
                        &agilesavecdr(
                            $agile_db, $astpp_db,
                            $config,   $enh_config,
                            @output,   $accountinfo,
                            $cost,     $enh_config->{agile_site_id},
                            $cdrinfo,  $enh_config->{agile_dbprefix}
                        );
                    }
                }
            }
        }
        foreach my $account (@account_list) {
            my @account_charge_list =
              &list_account_charges( $astpp_db, $account );
            my $accountinfo = &get_account( $astpp_db, $account );
            foreach my $charge (@pricelist_charge_list) {
                my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
                if ( $chargeinfo->{sweep} == $params->{sweep} ) {
                    my $now = &prettytimestamp;
                    &post_cdr(
                        $astpp_db,
                        $enh_config,
                        '',
                        $account,
                        '',
                        $chargeinfo->{description},
                        '',
                        '',
                        $chargeinfo->{charge},
                        $now,
                        $accountinfo->{posttoexternal},
                        ''
                    );
                    if ( $enh_config->{externalbill} eq "agile" ) {
                        my $cost = $chargeinfo->{charge} / 100;
                        my $cdrinfo;
                        $cdrinfo->{calldate} = $now;
                        $cdrinfo->{dst}      = $chargeinfo->{description};
                        &agilesavecdr(
                            $agile_db, $astpp_db,
                            $config,   $enh_config,
                            @output,   $accountinfo,
                            $cost,     $enh_config->{agile_site_id},
                            $cdrinfo,  $enh_config->{agile_dbprefix}
                        );
                    }
                }
            }
            foreach my $charge (@account_charge_list) {
                my $chargeinfo = &get_charge( $astpp_db, $charge->{charge_id} );
                if ( $chargeinfo->{sweep} == $params->{sweep} ) {
                    my $now = &prettytimestamp;
                    &post_cdr(
                        $astpp_db,
                        $enh_config,
                        '',
                        $account,
                        '',
                        $chargeinfo->{description},
                        '',
                        '',
                        $chargeinfo->{charge},
                        $now,
                        $accountinfo->{posttoexternal},
                        ''
                    );
                    if ( $enh_config->{externalbill} eq "agile" ) {
                        my $cost = $chargeinfo->{charge} / 100;
                        my $cdrinfo;
                        $cdrinfo->{calldate} = $now;
                        $cdrinfo->{dst}      = $chargeinfo->{description};
                        &agilesavecdr(
                            $agile_db, $astpp_db,
                            $config,   $enh_config,
                            @output,   $accountinfo,
                            $cost,     $enh_config->{agile_site_id},
                            $cdrinfo,  $enh_config->{agile_dbprefix}
                        );
                    }
                }
            }
        }
    }
}
if ( $enh_config->{externalbill} eq "oscommerce" ) {
    my @cardlist;
    if ( $params->{sweep} ) {
        @cardlist = &update_list_cards( $params->{sweep} );
    }
    foreach (@cardlist) {
        my $cardno = $_;
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
	        foreach my $handle (@output) {
      		      print $handle "Card: $cardno \n";
   		}
	        &osc_charges($cardno);
	}
    }
}
elsif ( $enh_config->{externalbill} eq "agile" ) {
    my @cardlist;
    my @cardlist = &list_cards($astpp_db);
    foreach my $cardno (@cardlist) {
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
            my @recordlist = &get_charges($cardno);
            foreach my $record (@recordlist) {
                my $cdrinfo = &get_charge($record);
                my $cost;
                if ( $cdrinfo->{debit} ne "" ) {
                    $cost = $cdrinfo->{debit};
                }
                else {
                    $cost = $cdrinfo->{credit} * -1;
                }
                &agilesavecdr(
                    $agile_db, $astpp_db,
                    $config,   $enh_config,
                    @output,   $carddata,
                    $cost,     $enh_config->{agile_site_id},
                    $cdrinfo,  $enh_config->{agile_dbprefix}
                );
            }
        }
    }
}
elsif ( $enh_config->{externalbill} eq "optigold" ) {
    my @cardlist;
    my @cardlist = &list_cards($astpp_db);
    foreach my $cardno (@cardlist) {
        my $carddata = &get_account( $astpp_db, $cardno );
        if ( $carddata->{posttoexternal} == 1 ) {
            my @recordlist = &get_charges($cardno);
            foreach my $record (@recordlist) {
                my $cdrinfo = &get_charge($record);
                my $cost;
                if ( $cdrinfo->{debit} ne "" ) {
                    $cost = $cdrinfo->{debit};
                }
                else {
                    $cost = $cdrinfo->{credit} * -1;
                }
                &ogsavecdr(
                    $carddata->{number},  $cdrinfo->{disposition},
                    $cdrinfo->{calldate}, $cdrinfo->{dst},
                    $cdrinfo->{billsec},  $cost,
                    $cdrinfo->{src}
                );
            }
        }
    }
}
exit(0);
