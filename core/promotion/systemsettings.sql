TRUNCATE TABLE `systemsettings`;
INSERT INTO `systemsettings` (`id`, `type`, `value`, `description`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'email_server', 'foo', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 14:57:44', 10),
(2, 'system_email_addr', 'foo@bar.com', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 14:57:44', 10),
(3, 'system_timezone', 'Australia/Melbourne', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 14:57:44', 10),
(4, 'last_succ_email', '0001-01-01 00:00:00', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 14:57:44', 10);
