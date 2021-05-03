DELETE FROM `languages` WHERE `code` = 'es' AND `name`='Spanish' AND `locale`='es_ES';
ALTER TABLE `translations` DROP `es_ES`;
