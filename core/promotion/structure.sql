-- Setting Up Database
DROP TABLE IF EXISTS `asset`;
CREATE TABLE `asset` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`assetId` varchar(32) NOT NULL DEFAULT '',
	`type` varchar(20) NOT NULL DEFAULT '',
	`filename` varchar(100) NOT NULL DEFAULT '',
	`mimeType` varchar(50) NOT NULL DEFAULT '',
	`path` varchar(200) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`type`)
	,UNIQUE INDEX (`assetId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sku` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`sku`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `productinfo`;
CREATE TABLE `productinfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sku` varchar(255) NOT NULL DEFAULT '',
	`entityName` varchar(50) NOT NULL DEFAULT '',
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`value` varchar(255) NOT NULL DEFAULT '',
	`productId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`productId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`value`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
	,UNIQUE INDEX (`sku`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`firstName` varchar(50) NOT NULL DEFAULT '',
	`lastName` varchar(50) NOT NULL DEFAULT '',
	`email` varchar(100) NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`firstName`)
	,INDEX (`lastName`)
	,INDEX (`email`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(32) NOT NULL DEFAULT '',
	`data` longtext NOT NULL ,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`key`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `systemsettings`;
CREATE TABLE `systemsettings` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL DEFAULT '',
	`value` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`type`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccount`;
CREATE TABLE `useraccount` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(100) NOT NULL DEFAULT '',
	`password` varchar(255) NOT NULL DEFAULT '',
	`personId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`personId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`password`)
	,UNIQUE INDEX (`username`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccountinfo`;
CREATE TABLE `useraccountinfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`userAccountId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(50) NOT NULL DEFAULT '',
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`value` varchar(255) NOT NULL DEFAULT '',
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`userAccountId`)
	,INDEX (`userAccountId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`value`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccountinfotype`;
CREATE TABLE `useraccountinfotype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`description` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

-- Completed CRUD Setup.