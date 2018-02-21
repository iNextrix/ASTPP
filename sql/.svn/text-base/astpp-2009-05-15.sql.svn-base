DROP TABLE IF EXISTS `routes`;
CREATE TABLE routes (
id INTEGER NOT NULL AUTO_INCREMENT,
pattern CHAR(40),
comment CHAR(80),
connectcost INTEGER NOT NULL,
includedseconds INTEGER NOT NULL,
cost INTEGER NOT NULL,
pricelist CHAR(80),
inc INTEGER,
reseller CHAR(50) default NULL,
precedence INT(4) NOT NULL DEFAULT 0,
status INTEGER NOT NULL DEFAULT 1,
PRIMARY KEY  (`id`),
KEY `pattern` (`pattern`),
KEY `pricelist` (`pricelist`),
KEY `reseller` (`reseller`),
KEY `status` (`status`)
);

DROP TABLE IF EXISTS `pricelists`;
CREATE TABLE pricelists (
name CHAR(40) NOT NULL,
markup INTEGER NOT NULL DEFAULT 0,
inc INTEGER NOT NULL DEFAULT 0,
status INTEGER DEFAULT 1 NOT NULL,
reseller CHAR(50) default NULL,
PRIMARY KEY  (`name`)
);

DROP TABLE IF EXISTS `callingcardbrands`;
CREATE TABLE callingcardbrands (
name CHAR(40) NOT NULL,
reseller CHAR(40) NOT NULL DEFAULT '',
language CHAR(10) NOT NULL DEFAULT '',
pricelist CHAR(40) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
validfordays CHAR(4) NOT NULL DEFAULT '',
pin INTEGER NOT NULL DEFAULT 0,
maint_fee_pennies INTEGER NOT NULL DEFAULT 0,
maint_fee_days INTEGER NOT NULL DEFAULT 0,
disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0,
minute_fee_minutes INTEGER NOT NULL DEFAULT 0,
minute_fee_pennies INTEGER NOT NULL DEFAULT 0,
min_length_minutes INTEGER NOT NULL DEFAULT 0,
min_length_pennies INTEGER NOT NULL DEFAULT 0,
PRIMARY KEY  (`name`),
  KEY `reseller` (`reseller`),
  KEY `pricelist` (`pricelist`)
);

DROP TABLE IF EXISTS `callingcardcdrs`;
CREATE TABLE callingcardcdrs (
id INTEGER NOT NULL AUTO_INCREMENT,
cardnumber CHAR(50) NOT NULL DEFAULT '',
clid CHAR(80) NOT NULL DEFAULT '',
destination CHAR(40) NOT NULL DEFAULT '',
disposition CHAR(20)NOT NULL DEFAULT '',
callstart CHAR(40) NOT NULL DEFAULT '',
seconds INTEGER NOT NULL DEFAULT 0,
debit DECIMAL(20,6) NOT NULL DEFAULT 0.00000,
credit DECIMAL(20,6) NOT NULL DEFAULT 0.00000,
status INTEGER DEFAULT 0 NOT NULL,
uniqueid VARCHAR(32) NOT NULL DEFAULT '',
notes CHAR(80) NOT NULL DEFAULT '',
pricelist CHAR(80) NOT NULL DEFAULT '',
pattern CHAR(80) NOT NULL DEFAULT '',
 PRIMARY KEY  (`id`),
  KEY `cardnumber` (`cardnumber`)
);

DROP TABLE IF EXISTS `trunks`;
CREATE TABLE trunks (
name VARCHAR(30) NOT NULL,
tech CHAR(10) NOT NULL DEFAULT '',
path CHAR(40) NOT NULL DEFAULT '',
provider CHAR(100) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
dialed_modify TEXT NOT NULL DEFAULT '',
resellers TEXT NOT NULL DEFAULT '',
precedence INT(4) NOT NULL DEFAULT 0,
maxchannels INTEGER DEFAULT 0 NOT NULL,
 PRIMARY KEY  (`name`),
  KEY `provider` (`provider`),
  KEY `provider_2` (`provider`)
);

DROP TABLE IF EXISTS `outbound_routes`;
CREATE TABLE outbound_routes (
pattern CHAR(40),
id INTEGER NOT NULL AUTO_INCREMENT,
comment CHAR(80) NOT NULL DEFAULT '',
connectcost INTEGER NOT NULL DEFAULT 0,
includedseconds INTEGER NOT NULL DEFAULT 0,
cost INTEGER NOT NULL DEFAULT 0,
trunk CHAR(80) NOT NULL DEFAULT '',
inc CHAR(10) NOT NULL DEFAULT '',
strip CHAR(40) NOT NULL DEFAULT '',
prepend CHAR(40) NOT NULL DEFAULT '',
precedence INT(4) NOT NULL DEFAULT 0,
resellers TEXT NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
PRIMARY KEY  (`id`),
  KEY `trunk` (`trunk`),
  KEY `pattern` (`pattern`)
);

DROP TABLE IF EXISTS `dids`;
CREATE TABLE dids (
number CHAR(40) NOT NULL,
account CHAR(50) NOT NULL DEFAULT '',
connectcost INTEGER NOT NULL DEFAULT 0,
includedseconds INTEGER NOT NULL DEFAULT 0,
monthlycost INTEGER NOT NULL DEFAULT 0,
cost INTEGER NOT NULL DEFAULT 0,
inc CHAR(10) NOT NULL DEFAULT '',
extensions CHAR(180) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
provider CHAR(40) NOT NULL DEFAULT '',
country CHAR (80)NOT NULL DEFAULT '',
province CHAR (80) NOT NULL DEFAULT '',
city CHAR (80) NOT NULL DEFAULT '',
prorate int(1) NOT NULL default 0,
setup int(11) NOT NULL default 0,
limittime int(1) NOT NULL default 1,
disconnectionfee INT(11) NOT NULL default 0,
variables TEXT NOT NULL DEFAULT '',
options varchar(40) default NULL,
maxchannels int(4) NOT NULL default 0,
chargeonallocation int(1) NOT NULL default 1,
allocation_bill_status int(1) NOT NULL default 0,
dial_as CHAR(40) NOT NULL DEFAULT '',
PRIMARY KEY  (`number`),
  KEY `account` (`account`)
);

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE accounts (
cc CHAR(20) NOT NULL DEFAULT '',
number CHAR(50) NOT NULL,
reseller CHAR(40) NOT NULL DEFAULT '',
pricelist CHAR(24) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
credit INTEGER NOT NULL DEFAULT 0,
sweep INTEGER NOT NULL DEFAULT 0,
creation TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
pin INTEGER NOT NULL DEFAULT 0,
credit_limit INTEGER NOT NULL DEFAULT 0,
posttoexternal INTEGER NOT NULL DEFAULT 0,
balance DECIMAL(20,6) NOT NULL DEFAULT 0,
password CHAR(80) NOT NULL DEFAULT '',
first_name CHAR(40) NOT NULL DEFAULT '',
middle_name CHAR(40) NOT NULL DEFAULT '',
last_name CHAR(40) NOT NULL DEFAULT '',
company_name CHAR(40) NOT NULL DEFAULT '',
address_1 CHAR(80) NOT NULL DEFAULT '',
address_2 CHAR(80) NOT NULL DEFAULT '',
address_3 CHAR(80) NOT NULL DEFAULT '',
postal_code CHAR(12) NOT NULL DEFAULT '',
province CHAR(40) NOT NULL DEFAULT '',
city CHAR(80) NOT NULL DEFAULT '',
country CHAR(40) NOT NULL DEFAULT '',
telephone_1 CHAR(40) NOT NULL DEFAULT '',
telephone_2 CHAR(40) NOT NULL DEFAULT '',
fascimile CHAR(40) NOT NULL DEFAULT '',
email CHAR(80) NOT NULL DEFAULT '',
language CHAR(2) NOT NULL DEFAULT '',
currency CHAR(3) NOT NULL DEFAULT '',
maxchannels INTEGER DEFAULT 1 NOT NULL,
routing_technique INT(4) NOT NULL DEFAULT 0,
dialed_modify TEXT NOT NULL DEFAULT '',
type INTEGER DEFAULT 0,
tz CHAR(40) NOT NULL DEFAULT '',
PRIMARY KEY  (`number`),
  KEY `pricelist` (`pricelist`),
  KEY `reseller` (`reseller`)
);

DROP TABLE IF EXISTS `counters`;
CREATE TABLE counters (
id INTEGER NOT NULL AUTO_INCREMENT,
package CHAR(40) NOT NULL DEFAULT '',
account VARCHAR(50) NOT NULL,
seconds INTEGER NOT NULL DEFAULT 0,
status INTEGER NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `callingcards`;
CREATE TABLE callingcards (
id INTEGER NOT NULL AUTO_INCREMENT,
cardnumber CHAR(20) NOT NULL DEFAULT '',
language CHAR(10) NOT NULL DEFAULT '',
value INTEGER NOT NULL DEFAULT 0,
used INTEGER NOT NULL DEFAULT 0,
brand VARCHAR(20) NOT NULL DEFAULT '',
created DATETIME,
firstused DATETIME,
expiry DATETIME,
validfordays CHAR(4) NOT NULL DEFAULT '',
inuse INTEGER NOT NULL DEFAULT 0,
pin CHAR(20),
account VARCHAR(50) NOT NULL DEFAULT '',
maint_fee_pennies INTEGER NOT NULL DEFAULT 0,
maint_fee_days INTEGER NOT NULL DEFAULT 0,
maint_day INTEGER NOT NULL DEFAULT 0,
disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0,
minute_fee_minutes INTEGER NOT NULL DEFAULT 0,
minute_fee_pennies INTEGER NOT NULL DEFAULT 0,
min_length_minutes INTEGER NOT NULL DEFAULT 0,
min_length_pennies INTEGER NOT NULL DEFAULT 0,
timeused INTEGER NOT NULL DEFAULT 0,
invoice CHAR(20) NOT NULL DEFAULT 0,
status INTEGER DEFAULT 1 NOT NULL,
PRIMARY KEY  (`id`),
  KEY `brand` (`brand`)
);

CREATE TABLE charge_to_account (
id INTEGER NOT NULL AUTO_INCREMENT,
charge_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(50) NOT NULL DEFAULT '',
status INTEGER NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
);

CREATE TABLE queue_list (
id INTEGER NOT NULL AUTO_INCREMENT,
queue_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '',
PRIMARY KEY (`id`)
);

CREATE TABLE pbx_list (
id INTEGER NOT NULL AUTO_INCREMENT,
pbx_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '',
PRIMARY KEY (`id`)
);

CREATE TABLE extension_list (
id INTEGER NOT NULL AUTO_INCREMENT,
extension_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '',
PRIMARY KEY (`id`)
);

CREATE TABLE cdrs (
id INTEGER NOT NULL AUTO_INCREMENT,
uniqueid varchar(32) NOT NULL DEFAULT '',
cardnum CHAR(50),
callerid CHAR(80),
callednum varchar(80) NOT NULL DEFAULT '',
billseconds INT DEFAULT 0 NOT NULL,
trunk VARCHAR(30),
disposition varchar(45) NOT NULL DEFAULT '',
callstart varchar(80) NOT NULL DEFAULT '',
debit DECIMAL (20,6) NOT NULL DEFAULT 0,
credit DECIMAL (20,6) NOT NULL DEFAULT 0,
status INTEGER DEFAULT 0 NOT NULL,
notes CHAR(80),
provider CHAR(50),
cost DECIMAL(20,6) NOT NULL DEFAULT 0,
pricelist CHAR(80) NOT NULL DEFAULT '',
pattern CHAR(80) NOT NULL DEFAULT '',
PRIMARY KEY  (`id`),
  KEY `cardnum` (`cardnum`),
  KEY `provider` (`provider`),
  KEY `trunk` (`trunk`),
  KEY `uniqueid` (`uniqueid`),
  KEY `status` (`status`)
);

CREATE TABLE packages (
id INTEGER NOT NULL AUTO_INCREMENT,
name CHAR(40) NOT NULL DEFAULT '',
pricelist CHAR(40) NOT NULL DEFAULT '',
pattern CHAR(40) NOT NULL DEFAULT '',
includedseconds INTEGER NOT NULL DEFAULT 0,
reseller VARCHAR(50) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
PRIMARY KEY  (`id`),
  KEY `pricelist` (`pricelist`),
  KEY `reseller` (`reseller`)
);

CREATE TABLE ani_map (
number char(20) NOT NULL,
account char(50) NOT NULL default '',
status int(11) NOT NULL default '0',
context varchar(20) NOT NULL,
  PRIMARY KEY  (`number`),
KEY `account` (`account`)
);

CREATE TABLE `ip_map` (
ip char(15) NOT NULL default '',
account char(20) NOT NULL default '',
prefix varchar(20) NULL,
context varchar(20) NOT NULL,
PRIMARY KEY  (`ip`,`prefix`),
KEY `account` (`account`)
);

CREATE TABLE charges (
id INTEGER NOT NULL AUTO_INCREMENT,
pricelist CHAR(40) NOT NULL DEFAULT '',
description VARCHAR(80) NOT NULL DEFAULT '',
charge INTEGER NOT NULL DEFAULT 0,
sweep INTEGER NOT NULL DEFAULT 0,
reseller CHAR(40) NOT NULL DEFAULT '',
status INTEGER NOT NULL DEFAULT 1,
PRIMARY KEY  (`id`),
  KEY `pricelist` (`pricelist`)
);

CREATE TABLE manager_action_variables (
id INTEGER NOT NULL AUTO_INCREMENT,
name CHAR(60) NOT NULL DEFAULT '',
value CHAR(60) NOT NULL DEFAULT '',
PRIMARY KEY  (`id`)
);

CREATE TABLE callingcard_stats (
uniqueid VARCHAR(48) NOT NULL,
total_time VARCHAR(48) NOT NULL,
billable_time VARCHAR(48) NOT NULL,
timestamp DATETIME NULL,
PRIMARY KEY (`uniqueid`)
);

CREATE TABLE system (
id INTEGER NOT NULL AUTO_INCREMENT,
name VARCHAR(48) NULL,
value VARCHAR(255) NULL,
comment VARCHAR(255) NULL,
timestamp DATETIME NULL,
reseller VARCHAR(48) NULL,
brand VARCHAR(48) NULL,
PRIMARY KEY  (`id`),
  KEY  (`name`),
  KEY `reseller` (`reseller`),
  KEY `brand` (`brand`)
);


INSERT INTO system (name, value, comment) VALUES (
'callout_accountcode','admin','Call Files: What accountcode should we use?');

INSERT INTO system (name, value, comment) VALUES (
'lcrcontext','astpp-outgoing','This is the Local context we use to route our outgoing calls through esp for callbacks');

INSERT INTO system (name, value, comment) VALUES (
'maxretries','3','Call Files: How many times do we retry?');

INSERT INTO system (name, value, comment) VALUES (
'retrytime','30','Call Files: How long do we wait between retries?');

INSERT INTO system (name, value, comment) VALUES (
'waittime','15','Call Files: How long do we wait before the initial call?');

INSERT INTO system (name, value, comment) VALUES (
'clidname','Private','Call Files: Outgoing CallerID Name');

INSERT INTO system (name, value, comment) VALUES (
'clidnumber','0000000000','Call Files: Outgoing CallerID Number');

INSERT INTO system (name, value, comment) VALUES (
'callingcards_callback_context','astpp-callingcards','Call Files: For callingcards what context do we end up in?');

INSERT INTO system (name, value, comment) VALUES (
'callingcards_callback_extension', 's','Call Files: For callingcards what extension do we use?');

INSERT INTO system (name, value, comment) VALUES (
'openser_dbengine', 'MySQL','For now this must be MySQL');

INSERT INTO system (name, value, comment) VALUES (
'openser', '0','Use OPENSER?  1 for yes or 0 for no');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'openser_dbname', 'openser','OPENSER Database Name', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'openser_dbuser', 'root','OPENSER Database User', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'openser_dbhost', 'localhost','OPENSER Database Host', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'openser_dbpass', 'Passw0rd','OPENSER Database Password', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'openser_domain', NULL,'OPENSER Domain', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'company_email', 'email@astpp.org','Email address that email should appear to be from', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'asterisk_dir', '/etc/asterisk','Which directory are asterisk configuration files stored in?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'company_website', 'http://www.astpp.org','Link to your company website', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'company_name', 'ASTPP.ORG','The name of your company.  Used in emails.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'email', '1','Send out email? 0=no 1=yes', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'user_email', '1','Email user on account changes? 0=no 1=yes', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'debug', '1','Enable debugging output? 0=no 1=yes', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'emailadd', 'email@astpp.org','Administrator email address', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'startingdigit', '0','The digit that all calling cards must start with. 0=disabled', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'enablelcr', '1','Use least cost routing 0=no 1=yes', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'log_file', '/var/log/astpp/astpp.log','ASTPP Log file', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'key_home', 'http://www.astpp.org/astpp.pub','Asterisk RSA Key location (optional)', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'rate_engine_csv_file', '/var/log/astpp/astpp.csv','CSV File for call rating data', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'csv_dir', '/var/log/astpp/','CSV File Directory', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'default_brand', 'default','Default pricelist.  If a price is not found in the customers pricelist we check this one.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'new_user_brand', 'default','What is the default pricelist for new customers?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'default_context', 'custom-astpp','What is the default context for new devices?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cardlength', '10','Number of digits in calling cards and cc codes.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'asterisk_server', 'voip.astpp.org','Your default voip server.  Used in outgoing email.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'currency', 'CAD','Name of the currency you use', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'iax_port', '4569','Default IAX2 Port', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'sip_port', '5060','Default SIP Port', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'ipaddr', 'dynamic','Default IP Address for new devices', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'key', 'astpp.pub','Asterisk RSA Key Name (Optional)', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'pinlength', '6','For those calling cards that are using pins this is the number of digits it will have.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'credit_limit', '0','Default credit limit in dollars.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'decimalpoints', '4','How many decimal points do we bill to?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'decimalpoints_tax', '2','How many decimal points do we calculate taxes to?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'decimalpoints_total', '2','How many decimal points do we calculate totals to?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'max_free_length', '100','What is the maximum length (in minutes) of calls that are at no charge?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'trackvendorcharges', '0','Do we track the amount of money we spend with specific providers? 0=no 1=yes', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'company_logo', 'http://www.astpp.org/logo.png','The location of our company logo.', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'company_slogan', 'Welcome to ASTPP','Company slogan', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'version', '1.5Beta', 'ASTPP Version', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'default_language', 'en', 'Default ASTPP Language','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'card_retries','3', 'How many retries do we allow for calling card numbers?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'pin_retries','3', 'How many retries do we allow for pins?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'number_retries','3','How many retries do we allow calling card users when dialing a number?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'booth_context','callshop_booth','Please enter the default context for a callshop booth.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'callingcards_max_length','9000','What is the maximum length (in ms) of a callingcard call?','');

INSERT INTO system (name,value,comment,timestamp) VALUES (
'template_die_on_bad_params','0','Should HTML::Template die on bad parameters?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'results_per_page','30','How many results per page do we should in the web interface?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'astpp_dir','/var/lib/astpp','Where do the astpp configs live?','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'auth','Passw0rd!','This is the override authorization code and will allow access to the system.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_dbengine','MySQL','Database type for Asterisk(tm) -Realtime','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cdr_dbengine','MySQL','Database type for the cdr database','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_dbengine','MySQL','Database type for OSCommerce','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_dbengine','MySQL','Database type for AgileBill(tm)','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_dbengine','MySQL','Database type for FreePBX','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'externalbill','oscommerce','Please specify the external billing application to use.  If you are not using any then leave it blank.  Valid options are "agile" and "oscommerce".','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'callingcards','1','Do you wish to enable calling cards?  1 for yes and 2 for no.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'astcdr','1','Change this one at your own peril.  If you switch it off, calls will not be marked as billed in asterisk once they are billed.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'posttoastpp','1','Change this one at your own peril.  If you switch it off, calls will not be written to astpp when they are calculated.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'sleep','10','How long shall the rating engine sleep after it has been notified of a hangup? (in seconds)','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'users_dids_amp','0','If this is enabled, ASTPP will create users and DIDs in the FreePBX (www.freepbx.org) database.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'users_dids_rt','1','If this is enabled, ASTPP will create users and DIDs in the Asterisk Realtime database.','');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'users_dids_freeswitch','0','If this is enabled, ASTPP will create SIP users in the freeswitch database.','');


INSERT INTO system (name, value, comment) VALUES (
'service_prepend','778','');
INSERT INTO system (name, value, comment) VALUES (
'service_length,','7','');
INSERT INTO system (name, value, comment) VALUES (
'service_filler','4110000','');

INSERT INTO system (name, value, comment) VALUES (
'asterisk_cdr_table','cdr','Which table of the Asterisk(TM) database are the cdrs in?');

-- AgileBill(Trademark of AgileCo) Settings:
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_host','127.0.0.1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_db','agile','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_user','root','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_pass','','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_site_id','1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_charge_status','0','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_taxable','1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_dbprefix','_','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'agile_service_prepend','778','','');

-- OSCommerce Settings (www.oscommerce.org)
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_host','127.0.0.1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_db','oscommerce','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_user','root','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_pass','password','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_product_id','99999999','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_payment_method','"Charge"','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_order_status','1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'osc_post_nc','0','Do we post "free" items to the oscommerce invoice? 0=No 1=Yes','');

-- FreePBX Settings (www.freepbx.org)
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_host','127.0.0.1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_db','asterisk','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_user','root','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_pass','passw0rd','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_iax_table','iax','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_table','sip','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_extensions_table','extensions','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_codec_allow','g729,ulaw,alaw','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_codec_disallow','all','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_mailbox_group','default','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_nat','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_canreinvite','no','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_dtmfmode','rfc2833','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_qualify','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_type','friend','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_callgroup','','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_sip_pickupgroup','','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_iax_notransfer','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_iax_type','friend','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'freepbx_iax_qualify','yes','','');

-- Asterisk -realtime Settings
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_host','127.0.0.1','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_db','realtime','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_user','root','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_pass','','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_iax_table','iax','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_table','sip','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_extensions_table','extensions','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_insecure','very','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_nat','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_canreinvite','no','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_codec_allow','g729,ulaw,alaw','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_codec_disallow','all','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_mailbox_group','default','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_qualify','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_sip_type','friend','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_iax_qualify','yes','','');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'rt_iax_type','friend','','');
INSERT INTO system (name, value, comment) VALUES (
'rt_voicemail_table','voicemail_users','');


INSERT INTO system (name, value, comment) VALUES (
'calling_cards_rate_announce','1','Do we want the calling cards script to announce the rate on calls?');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_timelimit_announce','1','Do we want the calling cards script to announce the timelimit on calls?');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_cancelled_prompt','1','Do we want the calling cards script to announce that the call was cancelled?');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_menu','1','Do we want the calling cards script to present a menu before exiting?');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_connection_prompt','1','Do we want the calling cards script to announce that it is connecting the call?');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_pin_input_timeout','15000','How long do we wait when entering the calling card pin?  Specified in MS');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_number_input_timeout','15000','How long do we wait when entering the calling card number?  Specified in MS');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_dial_input_timeout','15000','How long do we wait when entering the destination number in calling cards?  Specified in MS');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_general_input_timeout','15000','How long do we wait for input in general menus?  Specified in MS');
INSERT INTO system (name, value, comment) VALUES (
'calling_cards_welcome_file','silence/1','What do we play for a welcome file?');

INSERT INTO system (name, value, comment) VALUES (
'sip_ext_prepend','10','What should every autoadded SIP extension begin with?');
INSERT INTO system (name, value, comment) VALUES (
'iax2_ext_prepend','10','What should every autoadded IAX2 extension begin with?');
INSERT INTO system (name, value, comment) VALUES (
'cc_prepend','','What should every autoadded callingcard begin with?');
INSERT INTO system (name, value, comment) VALUES (
'pin_cc_prepend','','What should every autoadded callingcard pin begin with?');
INSERT INTO system (name, value, comment) VALUES (
'pin_act_prepend','','What should every autoadded account pin begin with?');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_directory','/usr/local/freeswitch','What is the Freeswitch root directory?');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_password','ClueCon','Freeswitch event socket password');
INSERT INTO system (name, value, comment) VALUES (
'freeswitch_host','localhost','Freeswitch event socket host');
INSERT INTO system (name, value, comment) VALUES (
'freeswitch_port','8021','Freeswitch event socket port');
INSERT INTO system (name, value, comment) VALUES (
'freeswitch_timeout','30','Freeswitch seconds to expect a heartbeat event or reconnect');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_dbengine', 'MySQL','For now this must be MySQL');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'freeswitch_dbname', 'freeswitch','Freeswitch Database Name', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'freeswitch_dbuser', 'root','Freeswitch Database User', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'freeswitch_dbhost', 'localhost','Freeswitch Database Host', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'freeswitch_dbpass', 'Passw0rd','Freeswitch Database Password', '');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_cdr_table','fscdr','Which table of the cdr database are the Freeswitch cdrs in?');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_domain','$${local_ip_v4}','This is entered as the Freeswitch domain.');

INSERT INTO system (name, value, comment) VALUES (
'freeswitch_context','default','This is entered as the Freeswitch user context.');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cdr_dbname', 'asteriskcdrdb',
'CDR Database Name', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cdr_dbuser', 'root',
'CDR Database User', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cdr_dbhost', 'localhost',
'CDR Database Host', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'cdr_dbpass', 'Passw0rd',
'CDR Database Password', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'astman_user', 'admin','Asterisk(tm) Manager Interface User', '');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'astman_host', 'localhost','Asterisk(tm) Manager Interface Host', '');
INSERT INTO system (name, value, comment, timestamp) VALUES (
'astman_secret', 'amp111','Asterisk(tm) Manager Interface Secret', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'call_max_length','1440000','What is the maximum length (in ms) of a LCR call?','');

------ 3rd Party PBX Mods
INSERT INTO system (name, value, comment, timestamp) VALUES (
'thirdlane_mods','0','Provides a few different modifications across the rating code to work better with Thirdlane(tm) cdrs.','');

--
-- Enough Configuration settings
--

DROP TABLE IF EXISTS `countrycode`;
CREATE TABLE `countrycode` (
  `country` varchar(255) NOT NULL,
  PRIMARY KEY  (`country`),
  KEY `country` (`country`)
);

INSERT INTO `countrycode` (`country`) VALUES
 ('Afghanistan'),
 ('Alaska'),
 ('Albania'),
 ('Algeria'),
 ('AmericanSamoa'),
 ('Andorra'),
 ('Angola'),
 ('Antarctica'),
 ('Argentina'),
 ('Armenia'),
 ('Aruba'),
 ('Ascension'),
 ('Australia'),
 ('Austria'),
 ('Azerbaijan'),
 ('Bahrain'),
 ('Bangladesh'),
 ('Belarus'),
 ('Belgium'),
 ('Belize'),
 ('Benin'),
 ('Bhutan'),
 ('Bolivia'),
 ('Bosnia & Herzegovina'),
 ('Botswana'),
 ('Brazil'),
 ('Brunei Darussalam'),
 ('Bulgaria'),
 ('Burkina Faso'),
 ('Burundi'),
 ('Cambodia'),
 ('Cameroon'),
 ('Canadda'),
 ('Cape Verde Islands'),
 ('Central African Republic'),
 ('Chad'),
 ('Chile'),
 ('China'),
 ('Colombia'),
 ('Comoros'),
 ('Congo'),
 ('Cook Islands'),
 ('Costa Rica'),
 ('Croatia'),
 ('Cuba'),
 ('Cuba Guantanamo Bay'),
 ('Cyprus'),
 ('Czech Republic'),
 ('Denmark'),
 ('Diego Garcia'),
 ('Djibouti'),
 ('Dominican Republic'),
 ('East Timor'),
 ('Ecuador'),
 ('Egypt'),
 ('El Salvador'),
 ('Equatorial Guinea'),
 ('Eritrea'),
 ('Estonia'),
 ('Ethiopia'),
 ('Faroe Islands');
INSERT INTO `countrycode` (`country`) VALUES
 ('Fiji Islands'),
 ('Finland'),
 ('France'),
 ('French Guiana'),
 ('French Polynesia'),
 ('Gabonese Republic'),
 ('Gambia'),
 ('Georgia'),
 ('Germany'),
 ('Ghana'),
 ('Gibraltar'),
 ('Greece'),
 ('Greenland'),
 ('Guadeloupe'),
 ('Guam'),
 ('Guatemala'),
 ('Guinea'),
 ('Guyana'),
 ('Haiti'),
 ('Honduras'),
 ('Hong Kong'),
 ('Hungary'),
 ('Iceland'),
 ('India'),
 ('Indonesia'),
 ('Iran'),
 ('Iraq'),
 ('Ireland'),
 ('Israel'),
 ('Italy'),
 ('Jamaica'),
 ('Japan'),
 ('Jordan'),
 ('Kazakstan'),
 ('Kenya'),
 ('Kiribati'),
 ('Kuwait'),
 ('Kyrgyz Republic'),
 ('Laos'),
 ('Latvia'),
 ('Lebanon'),
 ('Lesotho'),
 ('Liberia'),
 ('Libya'),
 ('Liechtenstein'),
 ('Lithuania'),
 ('Luxembourg'),
 ('Macao'),
 ('Madagascar'),
 ('Malawi'),
 ('Malaysia'),
 ('Maldives'),
 ('Mali Republic'),
 ('Malta'),
 ('Marshall Islands'),
 ('Martinique'),
 ('Mauritania'),
 ('Mauritius'),
 ('MayotteIsland'),
 ('Mexico'),
 ('Midway Islands'),
 ('Moldova'),
 ('Monaco'),
 ('Mongolia'),
 ('Morocco');
INSERT INTO `countrycode` (`country`) VALUES
 ('Mozambique'),
 ('Myanmar'),
 ('Namibia'),
 ('Nauru'),
 ('Nepal'),
 ('Netherlands'),
 ('Netherlands Antilles'),
 ('New Caledonia'),
 ('New Zealand'),
 ('Nicaragua'),
 ('Niger'),
 ('Nigeria'),
 ('Niue'),
 ('Norfolk Island'),
 ('North Korea'),
 ('Norway'),
 ('Oman'),
 ('Pakistan'),
 ('Palau'),
 ('Palestinian Settlements'),
 ('Panama'),
 ('PapuaNew Guinea'),
 ('Paraguay'),
 ('Peru'),
 ('Philippines'),
 ('Poland'),
 ('Portugal'),
 ('Puerto Rico'),
 ('Qatar'),
 ('RéunionIsland'),
 ('Romania'),
 ('Russia'),
 ('Rwandese Republic'),
 ('San Marino'),
 ('São Tomé and Principe'),
 ('Saudi Arabia'),
 ('Senegal '),
 ('Serbia and Montenegro'),
 ('Seychelles Republic'),
 ('Sierra Leone'),
 ('Singapore'),
 ('Slovak Republic'),
 ('Slovenia'),
 ('Solomon Islands'),
 ('Somali Democratic Republic'),
 ('South Africa'),
 ('South Korea'),
 ('Spain'),
 ('Sri Lanka'),
 ('St Kitts - Nevis'),
 ('St. Helena'),
 ('St. Lucia'),
 ('St. Pierre & Miquelon'),
 ('St. Vincent & Grenadines'),
 ('Sudan');
INSERT INTO `countrycode` (`country`) VALUES
 ('Suriname'),
 ('Swaziland'),
 ('Sweden'),
 ('Switzerland'),
 ('Syria'),
 ('Taiwan'),
 ('Tajikistan'),
 ('Tanzania'),
 ('Thailand'),
 ('Togolese Republic'),
 ('Tokelau'),
 ('Tonga Islands'),
 ('Trinidad & Tobago'),
 ('Tunisia'),
 ('Turkey'),
 ('Turkmenistan'),
 ('Tuvalu'),
 ('Uganda'),
 ('Ukraine'),
 ('United Arab Emirates'),
 ('United Kingdom'),
 ('United States of America'),
 ('Uruguay'),
 ('Uzbekistan'),
 ('Vanuatu'),
 ('Venezuela'),
 ('Vietnam'),
 ('Wake Island'),
 ('Wallisand Futuna Islands'),
 ('Western Samoa'),
 ('Yemen'),
 ('Zambia'),
 ('Zimbabwe');

DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `Currency` varchar(3) NOT NULL default '',
  `CurrencyName` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`Currency`)
);

INSERT INTO `currency` (`Currency`,`CurrencyName`) VALUES
 ('USD','US Dollars'),
 ('CAD','Canadian Dollars'),
 ('AUD','Australian Dollars');

CREATE TABLE `language` (
  `language` varchar(5) NOT NULL,
  `languagename` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `language` (`language`,`languagename`,`active`) VALUES
 ('en','English',1),
 ('fr','French',1),
 ('de','German',1);

CREATE TABLE `resellers` (
  name varchar(40) NOT NULL default '',
  status int(11) NOT NULL default '1',
  posttoexternal int(11) NOT NULL default '0',
  agile_site_id int(11) NOT NULL default '0',
  config_file char(80) NOT NULL default 'reseller.conf',
  companyname varchar(255) default NULL,
  slogan varchar(255) default NULL,
  footer varchar(255) default NULL,
  pricelist varchar(255) default NULL,
  currency varchar(255) default NULL,
  logo varchar(255) default NULL,
  website varchar(255) default NULL,
  adminemail varchar(255) default NULL,
  salesemail varchar(255) default NULL,
  phone varchar(45) default NULL,
  fax varchar(45) default NULL,
  address1 varchar(255) default NULL,
  address2 varchar(255) default NULL,
  city varchar(255) default NULL,
  state varchar(255) default NULL,
  postcode varchar(255) default NULL,
  country varchar(255) default NULL,
  defaultbrand varchar(45) NOT NULL default 'default',
  defaultcurrency varchar(45) NOT NULL default 'USD',
  defaultcredit varchar(45) NOT NULL default '0.00',
  externalbill varchar(45) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE templates (
id INTEGER NOT NULL AUTO_INCREMENT,
name VARCHAR(45) NOT NULL default '',
reseller VARCHAR(45) NOT NULL default '',
template TEXT NOT NULL default '',
 PRIMARY KEY  (`id`),
  KEY `reseller` (`reseller`)
);

INSERT INTO templates (name,template) VALUES
('voip_account_refilled','Attention: $vars->{title} $vars->{first} $vars->{last}
Your VOIP account with $config->{company_name} has been refilled.
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('voip_reactivate_account','Attention: $vars->{title} $vars->{first} $vars->{last}
Your VOIP account with $config->{company_name} has been reactivated.
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('email_add_user','Attention: $vars->{title} $vars->{first} $vars->{last}
Your VOIP account with $config->{company_name} has been added.
Your Username is -- $vars->{extension} --
Your Password is -- $vars->{secret} --
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('add_sip_device','Attention: $vars->{title} $vars->{first} $vars->{last}
A new device has been enabled on your account. Here
is the necessary configuration information.
-------  $config->{company_name} Configuration Info --------
In sip.conf:
[$config->{company_name}-in]
type=user
username=$config->{company_name}-in
auth=rsa
inkeys=$config->{key} ;This key may be downloaded from $config->{key_home}
host=$config->{asterisk_server}
context=from-pstn
accountcode=$config->{company_name}
[$config->{company_name}]
type=peer
username=$vars->{extension}
secret=$vars->{secret}
host=$config->{asterisk_server}
callerid= <555-555-5555>
qualify=yes
accountcode=$config->{company_name}   ; for call tracking in the cdr
In the [globals] section add:
register => $vars->{user}:password@$config->{asterisk_server}');

INSERT INTO templates (name,template) VALUES
('add_iax_device','Attention: $vars->{title} $vars->{first} $vars->{last}
A new device has been enabled on your account. Here
is the necessary configuration information.
-------  $config->{company_name} Configuration Info --------
In iax.conf:
At the bottom of the file add:
[$config->{company_name}-in]
;trunk=yes   ;optional .. only works if you have a zaptel or ztdummy driver running
type=user
username=$config->{company_name}-in
auth=rsa
inkeys=$config->{key}  ;This key may be downloaded from $config->{key_home}
host=$config->{asterisk_server}
context=incoming
accountcode=$config->{company_name}        ;for call tracking in the cdr
[$config->{company_name}]
;to simplify and config outgoing calls
;trunk=yes   ;optional .. only works if you have a zaptel driver running
type=peer
username=$vars->{extension}
secret=$vars->{secret}
host=$config->{asterisk_server}
callerid=<555-555-5555>   ;only the number will really be used
qualify=yes
accountcode=$config->{company_name}   ; for call tracking in the cdr
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('email_remove_user','Attention: $vars->{title} $vars->{first} $vars->{last}
Your VOIP Termination with $config->{company_name} has been removed
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('email_calling_card','You have added a $vars->{pricelist} callingcard in the amount of $vars->{pennies} cents.
Card Number $cc Pin: $pin
Thanks for your patronage.
The $config->{company_name} sales team');

INSERT INTO templates (name,template) VALUES
('email_add_did','Attention: $vars->{title} $vars->{first} $vars->{last}
Your DID with $config->{company_name} has been added
The number is: $did
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team
Here is a sample setup which would call a few sip phones with incoming calls:
[incoming]
exten => _1$did,1,Wait(2)
exten => _1$did,2,Dial(SIP/2201&SIP/2202,15,Ttm)  ; dial a couple of phones for 15 secs
exten => _1$did,3,Voicemail(u1000)   ; go to unavailable voicemail (vm box 1000)
exten => _1$did,103,Voicemail(b1000) ; go to busy voicemail (vm box 1000)');

INSERT INTO templates (name,template) VALUES
('email_remove_did','Attention: $vars->{title} $vars->{first} $vars->{last}
Your DID with $config->{company_name} has been removed
The number was: $did
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('email_new_invoice','Invoice # $invoice in the amount of \$$total has been added to your account.
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

INSERT INTO templates (name,template) VALUES
('email_low_balance','Your VOIP account with $config->{company_name} has a balance of \$$balance.
Please visit our website to refill your account to ensure uninterrupted service.
For information please visit $config->{company_website} or
contact our support department at $config->{company_email}
Thanks,
The $config->{company_name} support team');

CREATE TABLE `sweeplist` (
  `Id` int(10) unsigned NOT NULL default '0',
  `sweep` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`Id`)
);

INSERT INTO sweeplist (Id,sweep) VALUES
(0,'daily'),
(1,'weekly'),
(2,'monthly'),
(3,'quarterly'),
(4,'semi-annually'),
(5,'annually')
;

CREATE TABLE userlevels (
userlevelid int(11) NOT NULL,
userlevelname varchar(50) NOT NULL,
PRIMARY KEY  (`userlevelid`)
);

INSERT INTO `userlevels` (`userlevelid`,`userlevelname`) VALUES
 (-1,'Administrator'),
 (0,'Anonymous'),
 (1,'Reseller'),
 (2,'Admin'),
 (3,'Vendor'),
 (4,'Customer Service'),
 (5,'Users');

CREATE TABLE reseller_pricing (
id INTEGER NOT NULL AUTO_INCREMENT,
reseller VARCHAR(50) NOT NULL,
type INTEGER NOT NULL DEFAULT 1,
monthlycost INTEGER NOT NULL DEFAULT 0,
prorate INTEGER NOT NULL DEFAULT 0,
setup INTEGER NOT NULL DEFAULT 0,
cost INTEGER NOT NULL DEFAULT 0,
connectcost INTEGER NOT NULL DEFAULT 0,
includedseconds INTEGER NOT NULL DEFAULT 0,
note VARCHAR(50) NOT NULL DEFAULT '',
disconnectionfee INTEGER NOT NULL DEFAULT 0,
status INTEGER DEFAULT 1 NOT NULL,
inc CHAR(10) NOT NULL DEFAULT '',
PRIMARY KEY  (`id`),
  KEY `reseller` (`reseller`)
);

CREATE TABLE callshops (
id INTEGER NOT NULL AUTO_INCREMENT,
name VARCHAR(50) NOT NULL,
osc_dbname VARCHAR(50) NOT NULL DEFAULT '',
osc_dbpass VARCHAR(50) NOT NULL DEFAULT '',
osc_dbuser VARCHAR(50) NOT NULL DEFAULT '',
osc_dbhost VARCHAR(50) NOT NULL DEFAULT '',
osc_site VARCHAR(50) NOT NULL DEFAULT '',
status INTEGER DEFAULT 1 NOT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`)
);

CREATE TABLE extensions_status (
id INTEGER NOT NULL AUTO_INCREMENT,
tech VARCHAR(6) NULL,
extension VARCHAR(20) NULL,
number VARCHAR(255) NULL,
status VARCHAR(255) NULL,
timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
Privilege VARCHAR(255) NULL,
Channel VARCHAR(255) NULL,
Cause VARCHAR(255) NULL,
Causetxt VARCHAR(255) NULL,
PeerStatus VARCHAR(255) NULL,
Peer VARCHAR(255) NULL,
Context VARCHAR(255) NULL,
Application VARCHAR(255) NULL,
AppData VARCHAR(255) NULL,
Priority VARCHAR(255) NULL,
Uniqueid VARCHAR(255) NULL,
Event VARCHAR(255) NULL,
State VARCHAR(255) NULL,
CallerIDName VARCHAR(255) NULL,
CallerID VARCHAR(255) NULL,
AstExtension VARCHAR(255) NULL,
PRIMARY KEY (`id`),
KEY `extension` (`extension`));

CREATE TABLE activity_logs (
id INTEGER NOT NULL AUTO_INCREMENT,
timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
message TEXT NOT NULL DEFAULT '',
user VARCHAR(50),
PRIMARY KEY (`id`));

CREATE TABLE sql_commands (
id INTEGER NOT NULL AUTO_INCREMENT,
name VARCHAR(45) NOT NULL default '',
sql TEXT NOT NULL default '',
comment TEXT NOT NULL default '',
timestamp TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 PRIMARY KEY  (`id`));

CREATE TABLE `invoices` (
`invoiceid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`customerid` INT( 11 ) NOT NULL ,
`date` DATE NOT NULL ,
`status` TINYINT NOT NULL DEFAULT '0'
) ENGINE = MYISAM;

CREATE TABLE `invoices_total` (
`invoices_total_id` int(10) unsigned NOT NULL auto_increment,
`invoices_id` int(11) NOT NULL,
`title` varchar(255) NOT NULL,
`text` varchar(255) NOT NULL,
`value` decimal(15,4) NOT NULL,
`class` varchar(32) NOT NULL,
`sort_order` int(11) NOT NULL,
PRIMARY KEY (`invoices_total_id`)
);


 CREATE TABLE `payments` (
`id` INT( 11 ) NOT NULL ,
`customerid` INT( 11 ) NOT NULL ,
`credit` DECIMAL NOT NULL DEFAULT '0',
`status` TINYINT NOT NULL DEFAULT '0',
`type` INT NOT NULL ,
`notes` TEXT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM;

 CREATE TABLE `taxes` (
`taxes_id` int(11) NOT NULL auto_increment,
`taxes_priority` int(5) default '1',
`taxes_amount` decimal(7,4) NOT NULL,
`taxes_rate` decimal(7,4) NOT NULL,
`taxes_description` varchar(255) NOT NULL,
`last_modified` datetime default NULL,
`date_added` datetime NOT NULL,
PRIMARY KEY (`taxes_id`)
);

 CREATE TABLE `taxes_to_accounts` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`customerid` VARCHAR( 11 ) NOT NULL ,
`taxes_id` VARCHAR( 11 ) NOT NULL
) ENGINE = MYISAM ;

