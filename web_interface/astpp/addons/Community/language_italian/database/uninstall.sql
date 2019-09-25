DELETE FROM `languages` WHERE `code` = 'it' AND `name`='Italian' AND `locale`='it_IT';
ALTER TABLE `translations` DROP `it_IT`;
