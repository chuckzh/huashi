----  2016-06-12
ALTER TABLE `shanding`.`admin_user` ADD COLUMN `salt` CHAR(5) DEFAULT '' NOT NULL AFTER `user_password`;
ALTER TABLE `shanding`.`admin_user` CHANGE `user_password` `user_password` CHAR(13) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;

---- 2016-06-28

---- 2016-08-09
CREATE TABLE `shanding`.`fg_class`( `c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `c_name` CHAR(180) NOT NULL DEFAULT '', `c_parent_id` INT(10) UNSIGNED NOT NULL DEFAULT 0, `c_desc` TEXT NOT NULL, PRIMARY KEY (`c_id`) );

CREATE TABLE `shanding`.`feng_guang`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `title` CHAR(180) NOT NULL DEFAULT '', `fg_class` INT(10) UNSIGNED NOT NULL DEFAULT 0, `desc` TEXT, `photos` TEXT, PRIMARY KEY (`id`) );

CREATE TABLE `shanding`.`zuo_pin`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `title` CHAR(180) NOT NULL DEFAULT '', `author` CHAR(50) NOT NULL DEFAULT '', `zp_class` INT(10) UNSIGNED NOT NULL DEFAULT 0, `photos` TEXT NOT NULL, `desc` TEXT, PRIMARY KEY (`id`) );

CREATE TABLE `shanding`.`zp_class`( `c_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `c_name` CHAR(150) DEFAULT '', `c_parent_id` INT(10) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (`c_id`) );
