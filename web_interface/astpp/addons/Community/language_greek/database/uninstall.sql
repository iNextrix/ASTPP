DELETE FROM `languages` WHERE `code` = 'el' AND `name`='Greek' AND `locale`='el_GR';
ALTER TABLE `translations` DROP `el_GR`;
