
DROP TABLE IF EXISTS `TmpData`;

-- SEPARATOR

CREATE TABLE `TmpData` (
	`ID` int(11) NOT NULL auto_increment,
	`CreateDate` int(11) default '0',
	`UpdateDate` int(11) default '0',
	`UserID` int(11) NOT NULL DEFAULT 100,
	`AppID` VarChar(32) NOT NULL,
	`Col1` VarChar(64) default '',
	`Col2` VarChar(64) default '',
	`Col3` VarChar(64) default '',
	`Col4` text CHARACTER SET utf8mb4,
	`Params` LONGTEXT,
	PRIMARY KEY (`ID`),
	KEY `TmpDataUserID` (`UserID`),
	CONSTRAINT `TmpDataUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `AppID` (`AppID`),
	KEY `Col1` (`AppID`),
	KEY `Col2` (`AppID`),
	KEY `Col3` (`AppID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


