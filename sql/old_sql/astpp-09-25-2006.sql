CREATE TABLE routes (pattern CHAR(40), id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
comment CHAR(80), connectcost INTEGER NOT NULL, includedseconds INTEGER NOT NULL,
cost INTEGER NOT NULL, pricelist CHAR(80), inc INTEGER, reseller CHAR(50) default NULL,
status INTEGER NOT NULL DEFAULT 1);

CREATE TABLE configuration (reseller CHAR(50) default NULL, `key` CHAR(50) NOT NULL, value CHAR(50) default NULL);

CREATE TABLE pricelists (name CHAR(40) PRIMARY KEY, markup INTEGER NOT NULL DEFAULT 0, inc INTEGER NOT NULL DEFAULT 0, status INTEGER DEFAULT 1 NOT NULL, reseller CHAR(50) default NULL);

CREATE TABLE callingcardbrands (name CHAR(40) PRIMARY KEY, language CHAR(10) NOT NULL DEFAULT '', 
pricelist CHAR(40) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, validfordays CHAR(4) NOT NULL DEFAULT '', 
pin INTEGER NOT NULL DEFAULT 0, maint_fee_pennies INTEGER NOT NULL DEFAULT 0, 
maint_fee_days INTEGER NOT NULL DEFAULT 0, disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0, 
minute_fee_minutes INTEGER NOT NULL DEFAULT 0, minute_fee_pennies INTEGER NOT NULL DEFAULT 0);

CREATE TABLE callingcardcdrs (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, cardnumber CHAR(50) NOT NULL DEFAULT '', 
clid CHAR(80) NOT NULL DEFAULT '', destination CHAR(40) NOT NULL DEFAULT '', disposition CHAR(20)NOT NULL DEFAULT '', 
callstart CHAR(40) NOT NULL DEFAULT '', seconds INTEGER NOT NULL DEFAULT 0, debit DECIMAL(20,6) NOT NULL DEFAULT 0, 
credit DECIMAL(20,6) NOT NULL DEFAULT 0, status INTEGER DEFAULT 0 NOT NULL, notes CHAR(80) NOT NULL DEFAULT '');
      
CREATE TABLE trunks (name VARCHAR(30) PRIMARY KEY, tech CHAR(10) NOT NULL DEFAULT '', path CHAR(40) NOT NULL DEFAULT '',
provider CHAR(100) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, maxchannels INTEGER DEFAULT 1 NOT NULL);

CREATE TABLE outbound_routes (pattern CHAR(40), id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, 
comment CHAR(80) NOT NULL DEFAULT '', connectcost INTEGER NOT NULL DEFAULT 0, 
includedseconds INTEGER NOT NULL DEFAULT 0, cost INTEGER NOT NULL DEFAULT 0, trunk CHAR(80) NOT NULL DEFAULT '', 
inc CHAR(10) NOT NULL DEFAULT '', strip CHAR(40) NOT NULL DEFAULT '', prepend CHAR(40) NOT NULL DEFAULT '', 
status INTEGER DEFAULT 1 NOT NULL);

CREATE TABLE dids (number CHAR(40) NOT NULL PRIMARY KEY, account CHAR(50) NOT NULL DEFAULT '', connectcost INTEGER NOT NULL DEFAULT 0, 
includedseconds INTEGER NOT NULL DEFAULT 0, monthlycost INTEGER NOT NULL DEFAULT 0, cost INTEGER NOT NULL DEFAULT 0, extensions CHAR(180) NOT NULL DEFAULT '', 
status INTEGER DEFAULT 1 NOT NULL, provider CHAR(40) NOT NULL DEFAULT '', country CHAR (80)NOT NULL DEFAULT '', 
province CHAR (80) NOT NULL DEFAULT '', city CHAR (80) NOT NULL DEFAULT '');

CREATE TABLE accounts (cc CHAR(20) NOT NULL DEFAULT '', number CHAR(50) PRIMARY KEY, reseller CHAR(40) NOT NULL DEFAULT '', 
pricelist CHAR(24) NOT NULL DEFAULT '', status INTEGER DEFAULT 1 NOT NULL, credit INTEGER NOT NULL DEFAULT 0, sweep INTEGER NOT NULL DEFAULT 0, creation TIMESTAMP, pin INTEGER NOT NULL DEFAULT 0, 
credit_limit INTEGER NOT NULL DEFAULT 0, posttoexternal INTEGER NOT NULL DEFAULT 0, 
balance DECIMAL(20,6) NOT NULL DEFAULT 0, password CHAR(80) NOT NULL DEFAULT '', 
first_name CHAR(40) NOT NULL DEFAULT '', middle_name CHAR(40) NOT NULL DEFAULT '', 
last_name CHAR(40) NOT NULL DEFAULT '', company_name CHAR(40) NOT NULL DEFAULT '', 
address_1 CHAR(80) NOT NULL DEFAULT '', address_2 CHAR(80) NOT NULL DEFAULT '', 
address_3 CHAR(80) NOT NULL DEFAULT '', postal_code CHAR(12) NOT NULL DEFAULT '', 
province CHAR(40) NOT NULL DEFAULT '', city CHAR(80) NOT NULL DEFAULT '', country CHAR(40) NOT NULL DEFAULT '', 
telephone_1 CHAR(40) NOT NULL DEFAULT '', telephone_2 CHAR(40) NOT NULL DEFAULT '', fascimile CHAR(40) NOT NULL DEFAULT '', 
email CHAR(80) NOT NULL DEFAULT '', language CHAR(2) NOT NULL DEFAULT '',
currency CHAR(3) NOT NULL DEFAULT '', maxchannels INTEGER DEFAULT 1 NOT NULL, type INTEGER DEFAULT 0);

CREATE TABLE counters (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, package CHAR(40) NOT NULL DEFAULT '', 
account VARCHAR(50) NOT NULL, seconds INTEGER NOT NULL DEFAULT 0);

CREATE TABLE callingcards (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, cardnumber CHAR(20) NOT NULL DEFAULT '', 
language CHAR(10) NOT NULL DEFAULT '', value INTEGER NOT NULL DEFAULT 0, used INTEGER NOT NULL DEFAULT 0, 
brand VARCHAR(20) NOT NULL DEFAULT '', created DATETIME, firstused DATETIME, expiry DATETIME, 
validfordays CHAR(4) NOT NULL DEFAULT '', inuse INTEGER NOT NULL DEFAULT 0, pin CHAR(20), 
account VARCHAR(50) NOT NULL DEFAULT '', maint_fee_pennies INTEGER NOT NULL DEFAULT 0, 
maint_fee_days INTEGER NOT NULL DEFAULT 0, maint_day INTEGER NOT NULL DEFAULT 0, 
disconnect_fee_pennies INTEGER NOT NULL DEFAULT 0, minute_fee_minutes INTEGER NOT NULL DEFAULT 0, 
minute_fee_pennies INTEGER NOT NULL DEFAULT 0, timeused INTEGER NOT NULL DEFAULT 0,
invoice CHAR(20) NOT NULL DEFAULT 0, status INTEGER DEFAULT 1 NOT NULL);

CREATE TABLE charge_to_account (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, charge_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(50) NOT NULL DEFAULT '', status INTEGER NOT NULL DEFAULT 1);

CREATE TABLE queue_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, queue_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '');

CREATE TABLE pbx_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, pbx_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '');

CREATE TABLE extension_list (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, extension_id INTEGER NOT NULL DEFAULT 0,
cardnum CHAR(20) NOT NULL DEFAULT '');

ALTER TABLE outbound_routes ADD INDEX (trunk);
ALTER TABLE accounts ADD INDEX (pricelist);

CREATE TABLE cdrs (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, uniqueid varchar(32) NOT NULL DEFAULT '', 
cardnum CHAR(50), callerid CHAR(80), callednum varchar(80) NOT NULL DEFAULT '', billseconds INT DEFAULT 0 NOT NULL, trunk VARCHAR(30), 
disposition varchar(45) NOT NULL DEFAULT '', callstart varchar(80) NOT NULL DEFAULT '', 
debit DECIMAL (20,6) NOT NULL DEFAULT 0, credit DECIMAL (20,6) NOT NULL DEFAULT 0, 
status INTEGER DEFAULT 0 NOT NULL, notes CHAR(80), provider CHAR(50), cost DECIMAL(20,6) NOT NULL DEFAULT 0);

CREATE TABLE resellers (name CHAR(50) PRIMARY KEY, status INTEGER DEFAULT 1 NOT NULL, 
posttoexternal INTEGER NOT NULL DEFAULT 0, agile_site_id INTEGER NOT NULL DEFAULT 0, 
config_file CHAR(80) NOT NULL DEFAULT '');

CREATE TABLE packages (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, name CHAR(40) NOT NULL DEFAULT '', 
pricelist CHAR(40) NOT NULL DEFAULT '', pattern CHAR(40) NOT NULL DEFAULT '', includedseconds INTEGER, 
status INTEGER DEFAULT 1 NOT NULL);

CREATE TABLE ani_map (number CHAR(20) NOT NULL PRIMARY KEY, account CHAR(50) NOT NULL DEFAULT '', status INTEGER DEFAULT 0 NOT NULL);

CREATE TABLE ip_map (
ip char(15) NOT NULL default '', 
account char(20) NOT NULL default '', 
PRIMARY KEY (`ip`) );

CREATE TABLE charges (
id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, 
pricelist CHAR(40) NOT NULL DEFAULT '', 
description VARCHAR(80) NOT NULL DEFAULT '', 
charge INTEGER NOT NULL DEFAULT 0, 
sweep INTEGER NOT NULL DEFAULT 0, 
status INTEGER NOT NULL DEFAULT 1);

CREATE TABLE system ( 
name VARCHAR(48) NULL, 
value VARCHAR(255) NULL, 
comment VARCHAR(255) NULL, 
timestamp DATETIME NULL, 
PRIMARY KEY (`name`));

INSERT INTO system (name, value, comment, timestamp) VALUES (
'callout_accountcode', 
'admin',
'Call Files: What accountcode should we use?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'lcrcontext', 
'astpp-outgoing',
'This is the Local context we use to route our outgoing calls through esp for callbacks', '');


INSERT INTO system (name, value, comment, timestamp) VALUES (
'maxretries', 
'3',
'Call Files: How many times do we retry?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'retrytime', 
'30',
'Call Files: How long do we wait between retries?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'waittime', 
'15',
'Call Files: How long do we wait before the initial call?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'clidname', 
'Private',
'Call Files: Outgoing CallerID Name', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'clidnumber', 
'0000000000',
'Call Files: Outgoing CallerID Number', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'callingcards_callback_context', 
'astpp-callingcards',
'Call Files: For callingcards what context do we end up in?', '');

INSERT INTO system (name, value, comment, timestamp) VALUES (
'callingcards_callback_extension', 
's',
'Call Files: For callingcards what extension do we use?', '');


