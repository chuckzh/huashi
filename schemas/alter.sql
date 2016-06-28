----  2016-06-12
ALTER TABLE `shanding`.`admin_user` ADD COLUMN `salt` CHAR(5) DEFAULT '' NOT NULL AFTER `user_password`;
ALTER TABLE `shanding`.`admin_user` CHANGE `user_password` `user_password` CHAR(13) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;

---- 2016-06-28
