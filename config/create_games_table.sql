CREATE TABLE `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `player1_score` int(11) NOT NULL DEFAULT '0',
  `player2_score` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL,
  `data` json NOT NULL,
  `player_turn` int(11) NOT NULL,
  PRIMARY KEY (`game_id`),
  UNIQUE KEY `game_id_UNIQUE` (`game_id`),
  KEY `player_turn_id_idx` (`player_turn`),
  KEY `player1_id_key_idx` (`player1_id`),
  KEY `player2_id_key_idx` (`player2_id`),
  CONSTRAINT `player1_id_key` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`),
  CONSTRAINT `player2_id_key` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`),
  CONSTRAINT `player_turn_id` FOREIGN KEY (`player_turn`) REFERENCES `players` (`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
