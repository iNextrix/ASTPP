DELETE FROM `languages` WHERE `code` = 'pt' AND `name`='Portuguese' AND `locale`='pt_BR';
ALTER TABLE `translations` DROP `pt_BR`;
