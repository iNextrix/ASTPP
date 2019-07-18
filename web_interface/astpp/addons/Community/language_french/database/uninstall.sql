DELETE FROM `languages` WHERE `code` = 'fre' AND `name`='french' AND `locale`='fr_FR';
ALTER TABLE `translations` DROP `fr_FR`;
