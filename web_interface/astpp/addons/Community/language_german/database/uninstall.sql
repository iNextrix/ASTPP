DELETE FROM `languages` WHERE `code` = 'de' AND `name`='German' AND `locale`='de_DE';
ALTER TABLE `translations` DROP `de_DE`;
