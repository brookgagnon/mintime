SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `taskid` int(10) UNSIGNED NOT NULL,
  `start` int(10) UNSIGNED NOT NULL,
  `end` int(10) UNSIGNED DEFAULT NULL,
  `notes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `option_name` varchar(191) NOT NULL,
  `option_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `rate` decimal(6,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `budget` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `currency` char(3) NOT NULL DEFAULT '',
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `taskid` (`taskid`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`option_name`);

ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
