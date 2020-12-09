DROP TABLE IF EXISTS `ibexa_setting`;
CREATE TABLE `ibexa_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(128) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `identifier` varchar(128) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibexa_setting_group_identifier` (`group`, `identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
