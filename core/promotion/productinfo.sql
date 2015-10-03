CREATE TABLE `productinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(10) NOT NULL DEFAULT 0,
  `value` varchar(255) NOT NULL DEFAULT '',
  `productInfoTypeId` int(10) NOT NULL DEFAULT 0,
  `entityId` int(10) NOT NULL DEFAULT 0,
  `entityName` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `productId` (`productId`),
  KEY `productInfoTypeId` (`productInfoTypeId`),
  KEY `entityId` (`entityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;