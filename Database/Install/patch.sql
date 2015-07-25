CREATE TABLE `patch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(255) NOT NULL DEFAULT '',
  `patch` int(5) NOT NULL,
  `query` text NOT NULL,
  `status` bit(1) NOT NULL DEFAULT b'0',
  `rollback` text NOT NULL,
  `rollback_status` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;