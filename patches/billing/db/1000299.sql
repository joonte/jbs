
DELETE FROM `Users` WHERE `ID` IN (10);

-- SEPARATOR

INSERT INTO `Users`
	(`ID`,`GroupID`,`Name`,`Watchword`,`Email`,`Sign`,`IsActive`,`IsProtected`)
VALUES
	(10,2000000,'Гость','19e66fdb0d643d2f36080a9dca4c5de4','guest@system.com','С уважением, Гость.','no','yes');

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `RequestLog` (
	`ID` int(12) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`REMOTE_ADDR` int(11) NOT NULL,
	`REQUEST_URI` varchar(1024) NOT NULL,
	`HTTP_REFERER` varchar(1024) NOT NULL,
	`HTTP_USER_AGENT` varchar(1024) NOT NULL,
	`WORK_TIME` float NOT NULL,
	`TIME_MYSQL` float NOT NULL,
	`COUNTER_MYSQL` int(4) NOT NULL,
	`COUNTER_COMPS` int(4) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

