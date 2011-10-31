<?php
require_once("inc/config.php");
require_once("inc/connect.php");

$query = sprintf("SELECT * FROM %spoints LIMIT 1",
mysql_real_escape_string($mysql_prefix));

$result = @mysql_query($query);
if($result){
	print "Tables already exist. Nothing was installed.";
	die();
}

$query = sprintf("CREATE TABLE IF NOT EXISTS `%sgames` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `score` int(10) NOT NULL DEFAULT '0',
  `starttime` int(15) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
mysql_real_escape_string($mysql_prefix));
mysql_query($query);


$query = sprintf("CREATE TABLE IF NOT EXISTS `%shighscores` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT '',
  `score` int(10) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
mysql_real_escape_string($mysql_prefix));
mysql_query($query);


$query = sprintf("CREATE TABLE IF NOT EXISTS `%spoints` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL DEFAULT '',
  `x` int(4) NOT NULL DEFAULT '0',
  `y` int(4) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
mysql_real_escape_string($mysql_prefix));
mysql_query($query);


$query = sprintf("CREATE TABLE IF NOT EXISTS `%squestions` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `point` int(5) NOT NULL DEFAULT '0',
  `game` int(6) NOT NULL DEFAULT '0',
  `start_time` int(20) NOT NULL DEFAULT '0',
  `total_time` int(20) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;",
mysql_real_escape_string($mysql_prefix));
mysql_query($query);


$query = sprintf("INSERT INTO `%spoints` (`id`, `name`, `x`, `y`) VALUES
(1, 'Reykjavik', 32, 152),
(2, 'Tampere', 294, 220),
(3, 'Helsinki', 297, 236),
(4, 'Tallinn', 296, 253),
(5, 'Moscow', 386, 268),
(6, 'Ekaterinburg', 569, 257),
(7, 'Ekaterinburg, USTU', 571, 264),
(8, 'Riga', 300, 284),
(9, 'Kaunas', 305, 307),
(10, 'Lviv', 323, 379),
(11, 'Zaporizhzhya', 415, 376),
(12, 'Ankara', 439, 477),
(13, 'Gdansk', 267, 319),
(14, 'Lodz', 275, 347),
(15, 'Warsaw', 288, 341),
(16, 'Krakow', 292, 371),
(17, 'Gliwice', 279, 368),
(18, 'Brno', 260, 387),
(19, 'Kosice', 298, 386),
(20, 'Bratislava', 270, 396),
(21, 'Vienna', 256, 400),
(22, 'Graz', 252, 413),
(23, 'Budapest', 287, 404),
(24, 'Veszprém', 276, 411),
(25, 'Maribor', 258, 423),
(26, 'Ljubljana', 251, 429),
(27, 'Zagreb', 265, 431),
(28, 'Novi Sad', 300, 433),
(29, 'Timisoara', 311, 423),
(30, 'Belgrade', 307, 440),
(31, 'Iasi', 356, 400),
(32, 'Cluj-Napoca', 329, 406),
(33, 'Brasov', 348, 416),
(34, 'Bucharest', 359, 427),
(35, 'Sofia', 336, 462),
(36, 'Skopje', 319, 472),
(37, 'Thessaloniki', 336, 485),
(38, 'Athens', 350, 517),
(39, 'Patras', 332, 522),
(40, 'Istambul', 395, 465),
(42, 'Chania', 356, 546),
(43, 'Milan', 200, 437),
(44, 'Turin', 188, 445),
(45, 'Rome', 226, 477),
(46, 'Rome, Tor Vergata', 226, 477),
(47, 'Naples', 251, 492),
(48, 'Porto', 29, 478),
(49, 'Lisbon', 19, 500),
(50, 'Almada', 18, 504),
(51, 'Coimbra', 25, 490),
(52, 'Valladolid', 70, 483),
(53, 'Madrid', 78, 488),
(54, 'Madrid Carlos III', 78, 488),
(55, 'Barcelona', 132, 485),
(56, 'Lyon', 161, 432),
(57, 'Grenoble', 172, 445),
(58, 'Nancy', 171, 394),
(59, 'ENSTA', 140, 393),
(60, 'ENSAM', 140, 393),
(61, 'Supelec', 140, 393),
(62, 'Paris, Ecole Centrale', 140, 393),
(63, 'Paris, Polytechnique', 140, 393),
(64, 'Louvain-la-Neuve', 163, 372),
(65, 'Ghent', 158, 366),
(66, 'Brussels', 162, 369),
(67, 'Brussels ULB', 162, 369),
(68, 'Leuven', 165, 370),
(69, 'Liege', 170, 374),
(70, 'Eindhoven', 170, 361),
(71, 'Aalborg', 203, 292),
(72, 'Copenhagen', 223, 310),
(73, 'Lund', 232, 305),
(74, 'Gothenburg', 223, 275),
(75, 'Uppsala', 258, 247),
(76, 'Stockholm', 261, 251),
(77, 'Trondheim', 210, 201);",
mysql_real_escape_string($mysql_prefix));
mysql_query($query);

print "Install finished";