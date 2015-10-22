TRUNCATE TABLE `address`;
INSERT INTO `address` (`id`, `contactName`, `contactNo`, `street`, `city`, `region`, `country`, `postCode`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, '', '+61 3 5975 8177', 'Unit 4, 8 Satu Way', 'Mornington', 'VIC', 'Australia', '3931', 1, NOW(), 10, NOW(), 10),
(2, 'wholesale@sushiandco.com.au', '+61 3 5975 8177', 'Unit 4, 8 Satu Way', 'Mornington', 'VIC', 'Australia', '3931', 1, NOW(), 10, NOW(), 10),
(3, 'bentons@sushiandco.com.au', '+61 3 5973 5359', 'K2, Bentons Square, 210 Dunns Road', 'Mornington', 'VIC', 'Australia', '3931', 1, NOW(), 10, NOW(), 10),
(4, 'blackburn@sushiandco.com.au', '+61 3 9877 7775', 'Shop 24, North Blackburn Shopping Centre, 66-104 Springfield Road', 'Blackburn', 'VIC', 'Australia', '3130', 1, NOW(), 10, NOW(), 10),
(5, 'karingal@sushiandco.com.au', '+61 3 9789 1709', 'K12, Centro Karingal Shopping Centre, 330 Cranbourne Road', 'Frankston', 'VIC', 'Australia', '3199', 1, NOW(), 10, NOW(), 10),
(6, 'mornington@sushiandco.com.au', '+61 3 5976 8884', '32A, Centro Mornington Shopping Centre, 78 Barkly Street', 'Mornington', 'VIC', 'Australia', '3931', 1, NOW(), 10, NOW(), 10),
(7, 'mount.eliza@sushiandco.com.au', '+61 3 9787 4322', '87/89 Mount Eliza Way', 'Mount Eliza', 'VIC', 'Australia', '3930', 1, NOW(), 10, NOW(), 10);

TRUNCATE TABLE `store`;
INSERT INTO `store` (`id`, `name`, `description`, `addressId`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Corporate Office', 'Corporate Office', 1, 1, NOW(), 10, NOW(), 10),
(2, 'Wholesale Factory', 'Wholesale Factory', 2, 1, NOW(), 10, NOW(), 10),
(3, 'Bentons Store', 'Bentons Store', 3, 1, NOW(), 10, NOW(), 10),
(4, 'Blackburn Store', 'Blackburn Store', 4, 1, NOW(), 10, NOW(), 10),
(5, 'Karingal Store', 'Karingal Store', 5, 1, NOW(), 10, NOW(), 10),
(6, 'Mornington Store', 'Mornington Store', 6, 1, NOW(), 10, NOW(), 10),
(7, 'IGA Mt Eliza', 'IGA Mt Eliza', 7, 1, NOW(), 10, NOW(), 10);