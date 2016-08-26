CREATE TABLE IF NOT EXISTS `observers` (
  `id` int(11) unsigned NOT NULL,
  `password` varchar(10) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# all other tables are automagically created as-needed
