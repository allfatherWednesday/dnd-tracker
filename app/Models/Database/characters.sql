SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
--  `characters` table
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE `characters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `data` text NOT NULL,
  `max_health` int(11) NOT NULL,
  `cur_health` int(11) NOT NULL,
  `char_modifiers` text NOT NULL,
  `initiative` int(11) NOT NULL DEFAULT 1,
  `role` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `owner` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Index and AUTO_INCREMENT for `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;
  
ALTER TABLE `characters` 
  ADD `inventory` TEXT NULL AFTER `data`;
COMMIT;