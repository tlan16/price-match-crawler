TRUNCATE TABLE `address`;
INSERT INTO `address` (`id`, `contactName`, `contactNo`, `street`, `city`, `region`, `country`, `postCode`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'contactName', 'contactNo', 'street', 'city', 'region', 'country', 'postCode', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10);

TRUNCATE TABLE `store`;
INSERT INTO `store` (`id`, `name`, `description`, `addressId`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Test Store', 'Test Store', 1, 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10);
