
DROP TABLE IF EXISTS `Statistics`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `Statistics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Stamp` int(11) NOT NULL,
	`Year` int(4) NOT NULL,
	`Month` int(2) NOT NULL,
	`Day` int(2) NOT NULL,
	`TableID` varchar(64),
	`PackageID` varchar(64),
	`Total` int(11) NOT NULL,
	`Active` int(11) NOT NULL,
	`New` int(11) NOT NULL,
	`Waiting` int(11) NOT NULL,
	`Suspended` int(11) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



