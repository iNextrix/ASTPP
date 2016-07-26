ALTER TABLE `trunks` ADD `failover_gateway_id1` INT( 4 ) NOT NULL DEFAULT '0' AFTER `failover_gateway_id`;
ALTER TABLE `accounts` ADD `interval` INT NOT NULL DEFAULT '0' AFTER `maxchannels`;
UPDATE `system` SET `value` = '2.3' WHERE `system`.`name` ='version';
ALTER TABLE `ip_map` CHANGE `ip` `ip` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
