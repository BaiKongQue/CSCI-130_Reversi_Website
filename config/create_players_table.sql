CREATE TABLE `players` (
  `player_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` char(60) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `age` int(11) NOT NULL DEFAULT '1',
  `gender` enum('boy','girl','other') NOT NULL DEFAULT 'other',
  `location` varchar(60) NOT NULL,
  `icon` varchar(45) NOT NULL,
  PRIMARY KEY (`player_id`),
  UNIQUE KEY `player_id_UNIQUE` (`player_id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
