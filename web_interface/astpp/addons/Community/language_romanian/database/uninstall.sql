DELETE FROM `languages` WHERE `code` = 'ro' AND `name`='Romanian' AND `locale`='ro_RO';
ALTER TABLE `translations` DROP `ro_RO`;
