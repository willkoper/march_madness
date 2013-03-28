CREATE TABLE `matchups` (
  `gameID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_start` datetime DEFAULT NULL,
  `under_dog` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`gameID`)
) ENGINE=InnoDB AUTO_INCREMENT=302601 DEFAULT CHARSET=utf8;

CREATE VIEW today_matchups as
SELECT * from matchups where DATE(game_start) = CURRENT_DATE();

CREATE TABLE `CINDERELLAS` (
	`gameID` int(11),
	`time_left` time,
	`underdog_score` int(4),
	`favorite_score` int(4),
	`alert_sent` bool
);