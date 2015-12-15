SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


TRUNCATE TABLE `systemsettings`;
INSERT INTO `systemsettings` (`id`, `type`, `value`, `description`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'email_server', 'foo', '', 1, '2015-09-01 00:57:44', 10, '2015-11-08 02:23:18', 10),
(2, 'system_email_addr', 'franklan118@gmail.com', '', 1, '2015-09-16 00:57:44', 10, '2015-11-08 02:08:05', 10),
(3, 'system_timezone', 'Australia/Melbourne', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 04:57:44', 10),
(4, 'last_succ_email', '0001-01-01 00:00:00', '', 1, '2015-09-16 00:57:44', 10, '2015-09-15 04:57:44', 10),
(5, 'last_succ_product_sync', '2015-11-08 02:26:57', '', 1, '2015-09-16 00:57:44', 10, '2015-11-07 15:30:15', 10),
(6, 'magento_b2b_username', 'frank', '', 1, '2015-09-16 00:57:44', 10, '2015-11-12 00:47:38', 10),
(7, 'magento_b2b_password', 'b85b2c37a170c7fa6100dcca17ba66d370207744', '', 1, '2015-09-16 00:57:44', 10, '2015-11-12 00:47:42', 10);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
