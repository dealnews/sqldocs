CREATE TABLE `test` (
  `test_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'This is a column comment',
  `enum_col` enum('VALUE1','VALUE2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_col` decimal(8,2) DEFAULT NULL,
  `datetime_col` datetime DEFAULT NULL,
  `fake_bool` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`test_id`),
  UNIQUE KEY `name` (`name`),
  KEY `date_sort` (`datetime_col`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='This is a test table';
