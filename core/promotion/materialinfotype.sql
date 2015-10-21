TRUNCATE TABLE `productinfotype`;
INSERT INTO `productinfotype` (`id`, `name`, `description`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Material', 'Material Ref', 1, '2015-10-19 12:08:08', 10, '2015-10-19 01:08:08', 10),
(2, 'Category', 'Category Ref', 1, '2015-10-19 12:08:08', 10, '2015-10-19 01:08:08', 10),
(3, 'Store', 'Store Ref', 1, '2015-10-19 12:08:08', 10, '2015-10-19 01:08:08', 10);

TRUNCATE TABLE `materialinfotype`;
INSERT INTO `materialinfotype` (`id`, `name`, `description`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'INGREDIENT', 'INGREDIENT Ref', 1, '2015-10-19 12:08:08', 10, '2015-10-19 01:08:08', 10);
