ALTER table subscriber Add accountcode int(11) after `password`;
ALTER table subscriber Add pricelist_id int(11) after `accountcode`;

ALTER TABLE `subscriber` ADD `status` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0:active,1:inactive',
ADD `creation_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
ADD `last_modified_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
