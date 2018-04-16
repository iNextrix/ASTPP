-- Accounts table update queries
ALTER TABLE `astpp`.`accounts`
ADD COLUMN `maxchannels_type` TINYINT(1) NOT NULL DEFAULT '0' AFTER `did_cid_translation`,
ADD COLUMN `maxchannels_reserved` TINYINT(1) NOT NULL DEFAULT '0' AFTER `maxchannels_type`;
UPDATE `accounts` SET `maxchannels_type` = 0, `maxchannels_reserved` = 0

CREATE TABLE IF NOT EXISTS `astpp`.`overmax` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `direction` VARCHAR(45) NULL DEFAULT NULL,
  `accountid` VARCHAR(45) NULL DEFAULT NULL,
  `datetime` VARCHAR(45) NULL DEFAULT NULL,
  `destinationnumber` VARCHAR(45) NULL DEFAULT NULL,
  `limittype` INT(11) NULL DEFAULT NULL,
  `ccmax` INT(11) NULL DEFAULT NULL,
  `ccmaxin` INT(11) NULL DEFAULT NULL,
  `maxchannels_type` INT(11) NULL DEFAULT NULL,
  `maxchannels` INT(11) NULL DEFAULT NULL,
  `maxchannels_reserved` INT(11) NULL DEFAULT NULL,
  `maxchannels_in` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1