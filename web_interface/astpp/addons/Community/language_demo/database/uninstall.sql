DELETE FROM `languages` WHERE `code` = 'demo' AND `name`='Demo' AND `locale`='demo';
ALTER TABLE `translations` DROP `demo`;
