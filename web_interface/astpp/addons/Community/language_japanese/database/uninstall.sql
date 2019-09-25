DELETE FROM `languages` WHERE `code` = 'ja' AND `name`='Japanese' AND `locale`='ja_JP';
ALTER TABLE `translations` DROP `ja_JP`;
