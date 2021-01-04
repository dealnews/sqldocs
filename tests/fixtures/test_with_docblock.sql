/**
 * This is a test table
 *
 * @column   bigint(unsigned)   test_id        Not Null
 * @column   varchar(255)       name           Not Null   This is a column comment
 * @column   enum               enum_col       Not Null
 * @column   decimal            decimal_col    Nullable   Default: NULL
 * @column   datetime           datetime_col   Nullable   Default: NULL
 * @column   tinyint            fake_bool      Not Null
 *
 * @key   unique   primary      (test_id)
 * @key   unique   name         (name)
 * @key            date_sort    (datetime_col)
 *
 * @name              test
 * @engine            InnoDB
 * @default_charset   utf8mb4
 * @collation         utf8mb4_unicode_ci
 */
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
