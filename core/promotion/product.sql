CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `barcode` varchar(255) NOT NULL DEFAULT '',
  `size` int(8) NOT NULL DEFAULT 0,
  `usedByVariance` varchar(100) NOT NULL DEFAULT '', 
  `unitPrice` int(10) NOT NULL DEFAULT 0, #THis needs to be changed to decimal(8,2) but not sure how the entity will be #
  `labelVersionNo` int(3) NOT NULL DEFAULT 0, 
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `name` (`name`),
  KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;