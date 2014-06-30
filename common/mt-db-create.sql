
DROP TABLE IF EXISTS `track`;
CREATE TABLE `track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(1000) NOT NULL DEFAULT '',
  `name` varchar(1000) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `agent` varchar(2000) NOT NULL DEFAULT '',
  `offset` bigint(20) NOT NULL DEFAULT '0',
  `processed` tinyint(4) NOT NULL DEFAULT '0',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `track_id` int(11) NOT NULL DEFAULT '0',
  `t` bigint(20) NOT NULL DEFAULT '0',
  `action` varchar(100) NOT NULL DEFAULT '',
  `xtra1` varchar(1000) NOT NULL DEFAULT '',
  `xc` int(11) NOT NULL DEFAULT '0',
  `yc` int(11) NOT NULL DEFAULT '0',
  `xtra2` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
