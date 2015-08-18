<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
//Succsess Message

//Acconting
$lang['success.add_tax_acc']='Account Tax added successfully!';
$lang['success.remove_tax_acc']='Account Tax removed successfully!';
$lang['success.invconf_update']='Invoice Configuration Updated Successfully!';

//Accounts
$lang['success.removed_succ']='Removed successfully...';
$lang['success.callerid_add']='CallerID Added successfully...';
$lang['success.acc_setup']='Account Setup Completed!';

//Callshop
$lang['success.callshop_add']='Callshop added successfully!';
$lang['success.callshop_remove']='Callshop removed successfully!';
$lang['success.booth_deactivate']='Booth Deactivated Successfully!';
$lang['success.booth_restore']='Booth Restore Successfully!';
$lang['success.boothshop_remove']='Booth Shop Removed Successfully!';
$lang['success.hangup_call']='Hangup Call Done!';
$lang['success.cdr_mark_bill']='CDRS Marked As Billed!';
$lang['success.view_inv']='View Invoice';

//lcr
$lang['success.outbound_add']='Outbound added successfully!';
$lang['success.outbound_update']='Outbound updated successfully!';
$lang['success.outbound_remove']='Outbound removed successfully!';

//opensipsconfig
$lang['success.opensips_add']='Opensips added successfully!';
$lang['success.opensips_update']='Opensips updated successfully!';
$lang['success.opensips_remove']='Opensips removed successfully!';
$lang['success.dispatcher_update']='Dispatcher updated successfully!';
$lang['success.dispatcher_remove']='Dispatcher Removed successfully!';

//package
$lang['success.packages']='Packages';
$lang['success.add']='added successfully!';
$lang['success.package_update']='Packages updated successfully!';

//rates
$lang['success.rate_import']='Origination Rates Imported Successfully';
$lang['success.periodiccharge_add']='Periodic Charge added successfully!';
$lang['success.periodiccharge_update']='Periodic Charge updated successfully!';
$lang['success.periodiccharge_remove']='Periodic Charge removed successfully!';
$lang['success.rout_add']='Route added successfully!';
$lang['success.rout_update']='Route updated successfully!';
$lang['success.rout_remove']='Route removed successfully!';

//statistics
$lang['success.error_remove']='Error removed successfully!';

//switchconfig
$lang['success.switchconf_add']='Switch Configuration added successfully!';
$lang['success.switchconf_update']='Switch Configuration updated successfully!';
$lang['success.switchconf_remove']='Switch Configuration removed successfully!';
$lang['success.acl_remove']='ACL removed successfully!';

//systems
$lang['success.tax_add']='Tax added successfully!';
$lang['success.tax_update']='Tax updated successfully!';
$lang['success.tax_remove']='Tax removed successfully!';
$lang['success.template_add']='Tempalte  added successfully!';
$lang['success.template_update']='Template updated successfully!';
$lang['success.template_remove']='Template removed successfully!';

//user
$lang['success.did_delete']='DID deleted successfully!';
$lang['success.callerid_add']='CallerID Added successfully...';

//useranimapping
$lang['success.mapani_add']='Map ANI added successfully!';
$lang['success.did_update']='DID updated successfully!';
$lang['success.ani_drop']='ANI has been dropped!';
//userdid
$lang['success.did_add']='DID added successfully!';


//-------------------------------------------------------------------------
                             //error message
//------------------------------------------------------------------------
//accounts
$lang['error.acc_notfound']='Account not found!';
$lang['error.not_allow_add_acc']='You are not allowed to add amount to this account.';
$lang['error.invalid_acc_number']='Invalid account number specified.<br />';
$lang['error.invalid_ammount']='Invalid amount to process.<br />';
$lang['error.invalid_file']='Invalid file';

$lang['error.fname_req']='First Name is required.<br />';
$lang['error.acc_num_req']='Account Number is required.<br />';
$lang['error.context_req']='Context is required.<br />';
$lang['error.email_req']='Email is required.<br />';

//astpp
$lang['error.login_fail']='Login Failed! Try Again..';

//callingcards
$lang['error.permission_denied']='Permission Access denied';
$lang['error.brand_notfound']='Brand not found!';
$lang['error.invalid_card_number']='Invalid card number.';
$lang['error.select_brand']='Please Select Brands <br />';
$lang['error.invalid_val_num']='Invalid value, must be a number <br />';
$lang['error.invalid_qty']='Invalid quantity, must be a number <br />';
$lang['error.brandname_req']='Brand Name is required<br />';
$lang['error.invalid_dur']='Invalid validaty duration<br />';
$lang['error.invalid_val']='Invalid value<br />';
$lang['error.invalid_starting_seq']='Invalid starting sequence<br />';
$lang['error.invalid_ending_seq']='Invalid ending sequence<br />';
$lang['error.card_notavailable']='This card is not available.';

//callshop
$lang['error.callshop_notfound']='Callshop not found!';
$lang['error.invalid_callshopname']='Invalid Callshoname<br />';
$lang['error.acc_pass_req']='Account password is required<br />';
$lang['error.credit_req']='Credit limit is required<br />';
$lang['error.oscomm_site_req']='OS Commerce Site is required<br />';
$lang['error.oscomm_db_req']='OS Commerce Site database name is required<br />';
$lang['error.oscomm_dbhost_acc']='OS Commerce Site database host is required<br />';
$lang['error.oscomm_dbpass_acc']='OS Commerce Site database password is required<br />';
$lang['error.oscomm_dbuser_acc']='OS Commerce Site database user is required<br />';

//did
$lang['error.invalid_num']='Number is Invalid<br />';
$lang['error.did_notavailable']='This DID is not available.';

//lcr
$lang['error.peername_req']='Peer name is required<br />';
$lang['error.trunkname_req']='Trunk Name is required<br />';
$lang['error.trunk_noavailable']='This trunk is not available.';
$lang['error.trunk_notfound']='Trunk not found!';
$lang['error.pattern_req']='Pattern is required<br />';
$lang['error.outbound_notfound']='Outbound not found!';

//openship
$lang['error.uname_req']='Username is required<br />';
$lang['error.pass_req']='Password is required<br />';
$lang['error.opensipconf_notavailable']='This Opensip Configuration is not available.';
$lang['error.dispatcher_notavailable']='This Dispatcher is not available.';

//package
$lang['error.rates_notaccessible']='Rates Packages is not accessible.';
$lang['error.name_req']='Name is required<br />';
$lang['error.package_notfound']='Packages not found!';
$lang['error.package_notavailable']='This Packages is not available.';

//rates
$lang['error.call_len_req']='Call length is required.<br />';
$lang['error.ph_req']='Phone number is required<br />';
$lang['error.rate_counter_notaccessible']='Rates Counter is not accessible.';
$lang['error.periodiccharge_notfound']='Periodic Charge not found!';
$lang['error.inc_req']='Increment is required<br />';
$lang['error.markup_req']='Markup is required<br />';
$lang['error.pricelist_notavailable']='This pricelist is not available.';
$lang['error.pricelist_notfound']='Pricelist not found!';
$lang['error.pattern_req']='Pattern is required<br />';
$lang['error.rout_notfound']='Route not found!';
$lang['error.rout_notavailable']='This route is not available.';

//switchconfig
$lang['error.vmpass_req']='VM Password is required<br />';
$lang['error.switchconf_notavailable']='This Switch Configuration is not available.';
$lang['error.switchconf_notfound']='Switch Configuration not found!';
$lang['error.acl_notfound']='ACL not found!';

//system
$lang['error.val_req']='Value is required<br />';
$lang['error.rout_notavailbale']='This route is not available.';
$lang['error.confiten_notfound']='Configuration Item not found!';
$lang['error.priority_req']='Priority is required<br />';
$lang['error.ammount_req']='Ammount is required<br />';
$lang['error.rate_req']='Rate is required<br />';
$lang['error.tax_notavailable']='This Tax is not available.';
$lang['error.tax_notfound']='Tax not found!';
$lang['error.template_req']='Template is required<br />';
$lang['error.template_notfound']='Template not found!';
$lang['error.template_notavailable']='This Template is not available.';

//useranimapping
$lang['error.invalid_ani']='ANI is Invalid<br />';
$lang['error.fail_ani']='ANI FAILED to remove!';
?>
