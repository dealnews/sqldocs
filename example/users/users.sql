/**
 * Table users
 *
 * @column   int(unsigned)   user_id          Not Null   Unique User Id
 * @column   varchar(255)    email            Nullable   User's email address. Default: NULL
 * @column   varchar(255)    email_hash       Nullable   Hash of the user's email address. Default: NULL
 * @column   varchar(255)    password         Not Null   User's encrypted password.
 * @column   datetime        wh_update_date   Nullable   Date the record was last updated. Default: NULL
 * @column   datetime        wh_insert_date   Not Null   Date the recorde was created. Default: CURRENT_TIMESTAMP
 *
 * @key   unique   primary              (user_id)
 * @key   unique   email                (email)
 * @key   unique   username             (username)
 * @key   unique   email_hash           (email_hash)
 * @key            by_wh_update_date    (wh_update_date)
 * @key            by_wh_insert_date    (wh_insert_date)
 *
 * @schema            users
 * @name              users
 * @engine            InnoDB
 * @default_charset   utf8mb4
 * @collation         utf8mb4_unicode_ci
 */
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email_hash` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `wh_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `wh_insert_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email_hash` (`email_hash`),
  KEY `by_wh_update_date` (`wh_update_date`),
  KEY `by_wh_insert_date` (`wh_insert_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
