
DROP TABLE IF EXISTS `PaymentSystemsCollation`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `PaymentSystemsCollation` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`IsActive` enum('no','yes') DEFAULT 'no',
	`SortID` int(11) default '1000',
	`Source` varchar(1023),
	`Destination` varchar(1023),
	`Image` varchar(1023),
	`Prompt` longtext,
	`Description` text,
	`AdminNotice` text,
	PRIMARY KEY (`ID`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

