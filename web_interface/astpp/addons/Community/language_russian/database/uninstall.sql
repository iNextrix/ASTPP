DELETE FROM `languages` WHERE `code` = 'ru' AND `name`='Russian' AND `locale`='ru_RU';
ALTER TABLE `translations` DROP `ru_RU`;
