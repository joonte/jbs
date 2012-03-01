
CREATE TABLE IF NOT EXISTS `DSServersGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `FunctionID` char(30) DEFAULT '',
  `SystemID` char(127) NOT NULL,
  `Comment` char(255) DEFAULT '',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `DSSchemes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) DEFAULT '',
  `PackageID` char(30) DEFAULT '',
  `CostDay` float(9,2) DEFAULT '0.00',
  `CostMonth` float(9,2) DEFAULT '0.00',
  `CostInstall` float(9,2) NOT NULL,
  `ServersGroupID` int(11) NOT NULL,
  `NumServers` int(4) NOT NULL,
  `RemainServers` int(4) NOT NULL,
  `IsCalculateNumServers` enum('no','yes') NOT NULL,
  `IsActive` enum('no','yes') DEFAULT 'yes',
  `IsProlong` enum('no','yes') NOT NULL,
  `MinDaysPay` int(6) DEFAULT '0',
  `MaxDaysPay` int(6) DEFAULT '0',
  `SortID` int(11) DEFAULT '10',
  `cputype` char(16) NOT NULL,
  `cpuarch` char(16) NOT NULL,
  `numcpu` int(2) NOT NULL,
  `numcores` int(3) NOT NULL,
  `cpufreq` int(5) NOT NULL,
  `ram` int(6) NOT NULL,
  `raid` char(128) NOT NULL,
  `disk1` char(128) NOT NULL,
  `disk2` char(128) NOT NULL,
  `disk3` char(128) NOT NULL,
  `disk4` char(128) NOT NULL,
  `chrate` float(9,2) NOT NULL,
  `trafflimit` float(9,2) NOT NULL,
  `traffcorrelation` char(128) NOT NULL,
  `OS` char(128) NOT NULL,
  `UserComment` char(255) NOT NULL,
  `AdminComment` char(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `DSSchemesGroupID` (`GroupID`),
  KEY `DSSchemesUserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- SEPARATOR


CREATE TABLE IF NOT EXISTS `DSServers` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`SystemID` char(30) DEFAULT '',
	`ServersGroupID` int(11) NOT NULL,
	`IsDefault` enum('no','yes') DEFAULT 'no',
	`Domain` char(30) DEFAULT '',
	`Prefix` char(30) DEFAULT 'h',
	`Address` char(30) DEFAULT '',
	`Port` int(5) DEFAULT '80',
	`Protocol` enum('tcp','ssl') DEFAULT 'tcp',
	`Login` char(60) DEFAULT '',
	`Password` char(255) DEFAULT '',
	`IP` char(60) DEFAULT '127.0.0.1',
	`IPsPool` text,
	`Theme` char(30) DEFAULT '',
	`Language` char(30) DEFAULT 'ru',
	`Url` char(60) DEFAULT '',
	`Ns1Name` char(30) DEFAULT '',
	`Ns2Name` char(30) DEFAULT '',
	`Ns3Name` char(30) DEFAULT '',
	`Ns4Name` char(30) DEFAULT '',
	`Services` text,
	`TestDate` int(11) DEFAULT '0',
	`IsOK` enum('no','yes') DEFAULT 'no',
	`Notice` text,
	PRIMARY KEY (`ID`),
	KEY `DSServersServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR

ALTER TABLE `DSServers` ADD CONSTRAINT `DSServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `DSServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR


CREATE TABLE IF NOT EXISTS `DSOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `IP` char(32) NOT NULL DEFAULT 'noassign',
  `ExtraIP` text NOT NULL,
  `AutoProlong` int(1) NOT NULL DEFAULT '1',
  `DaysRemainded` int(11) DEFAULT '0',
  `ConsiderDay` int(11) DEFAULT '0',
  `StatusID` char(30) DEFAULT 'UnSeted',
  `StatusDate` int(11) DEFAULT '0',
  `UserNotice` text,
  `AdminNotice` text,
  PRIMARY KEY (`ID`),
  KEY `DSOrdersOrderID` (`OrderID`),
  KEY `DSOrdersSchemeID` (`SchemeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `DSOrders`
  ADD CONSTRAINT `DSOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `DSOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR

CREATE TABLE IF NOT EXISTS `DSPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysPay` int(11) DEFAULT '665',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `DSPoliticsGroupID` (`GroupID`),
	KEY `DSPoliticsUserID` (`UserID`),
	KEY `DSPoliticsSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `DSPolitics`
	ADD CONSTRAINT `DSPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `DSDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `DSDomainsPoliticsGroupID` (`GroupID`),
	KEY `DSDomainsPoliticsUserID` (`UserID`),
	KEY `DSDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `DSDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `DSDomainsPolitics`
	ADD CONSTRAINT `DSDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `DSBonuses` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`UserID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`Discont` float(5,2) DEFAULT '0.00',
	`Comment` char(255) DEFAULT '',
	PRIMARY KEY (`ID`),
	KEY `DSBonusesUserID` (`UserID`),
	KEY `DSBonusesSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `DSBonuses`
	ADD CONSTRAINT `DSBonusesSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSBonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `DSConsider` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`DSOrderID` int(11) NOT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`DaysConsidered` int(11) DEFAULT '0',
	`Cost` float(7,2) DEFAULT '0.00',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `DSConsiderDSOrderID` (`DSOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `DSConsider`
	ADD CONSTRAINT `DSConsiderDSOrderID` FOREIGN KEY (`DSOrderID`) REFERENCES `DSOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


