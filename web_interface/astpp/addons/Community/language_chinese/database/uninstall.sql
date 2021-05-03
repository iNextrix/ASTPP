DELETE FROM `languages` WHERE `code` = 'zh' AND `name`='Chinese' AND `locale`='zh_CN';
ALTER TABLE `translations` DROP `zh_CN`;
