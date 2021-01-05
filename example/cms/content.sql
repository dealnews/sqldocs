CREATE TABLE `content` (
  `content_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(64) NOT NULL DEFAULT '',
  `headline` varchar(150) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `body` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
