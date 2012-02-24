INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `Code`, `Item`, `Emblem`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`)
VALUES
	(30000, 2000000, 1, 1000, 'Поддержка заказа виртуального сервера', 'VPS', 'VPS', '', 'дн.', 'Daily', 0.00, 0.00, 'no', 'yes', 'yes', 'yes', 20);
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSServersGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Name` char(30) NOT NULL,
	`FunctionID` char(30) DEFAULT '',
	`Comment` char(255) DEFAULT '',
	`SortID` int(11) DEFAULT '10',
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSSchemes` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) DEFAULT '',
	`PackageID` char(30) DEFAULT '',
	`CostDay` float(9,2) DEFAULT '0.00',
	`CostMonth` float(9,2) DEFAULT '0.00',
	`ServersGroupID` int(11) NOT NULL,
	`Comment` char(255) NOT NULL,
	`IsReselling` enum('no','yes') DEFAULT 'no',
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChangeable` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChange` enum('no','yes') DEFAULT 'yes',
	`MinDaysPay` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`SortID` int(11) DEFAULT '10',
	`vdslimit` int(3) NOT NULL,
	`QuotaUsers` int(4) DEFAULT '0',
	`disklimit` int(11) DEFAULT '0',
	`ncpu` int(2) NOT NULL,
	`cpu` float(7,2) DEFAULT '0.00',
	`mem` float(7,2) DEFAULT '0.00',
	`maxswap` float(7,2) NOT NULL,
	`traf` int(6) DEFAULT '0',
	`chrate` int(4) NOT NULL,
	`maxdesc` int(9) NOT NULL,
	`proc` int(4) DEFAULT '0',
	`ipalias` int(4) NOT NULL,
	`disktempl` varchar(128) NOT NULL,
	PRIMARY KEY (`ID`),
	KEY `VPSSchemesGroupID` (`GroupID`),
	KEY `VPSSchemesUserID` (`UserID`),
	KEY `VPSSchemesServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR


CREATE TABLE IF NOT EXISTS `VPSServers` (
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
	`UserNotice` text,
	`AdminNotice` text,
	PRIMARY KEY (`ID`),
	KEY `VPSServersServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSServers`
	ADD CONSTRAINT `VPSServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `VPSServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR


CREATE TABLE IF NOT EXISTS `VPSOrders` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) DEFAULT NULL,
	`AutoProlong` INT(1) NOT NULL DEFAULT '1',
	`Domain` char(255) DEFAULT '',
	`Parked` text,
	`ServerID` int(11) NOT NULL,
	`Login` char(20) DEFAULT '',
	`Password` char(30) DEFAULT '',
	`DaysRemainded` int(11) DEFAULT '0',
	`ConsiderDay` int(11) DEFAULT '0',
	`StatusID` char(30) DEFAULT 'UnSeted',
	`StatusDate` int(11) DEFAULT '0',
	PRIMARY KEY (`ID`),
	KEY `VPSOrdersOrderID` (`OrderID`),
	KEY `VPSOrdersSchemeID` (`SchemeID`),
	KEY `VPSOrdersServerID` (`ServerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSOrders`
ADD CONSTRAINT `VPSOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `VPSOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `VPSOrdersServerID` FOREIGN KEY (`ServerID`) REFERENCES `VPSServers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR

CREATE TABLE IF NOT EXISTS `VPSPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysPay` int(11) DEFAULT '665',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `VPSPoliticsGroupID` (`GroupID`),
	KEY `VPSPoliticsUserID` (`UserID`),
	KEY `VPSPoliticsSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSPolitics`
	ADD CONSTRAINT `VPSPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `VPSDomainsPoliticsGroupID` (`GroupID`),
	KEY `VPSDomainsPoliticsUserID` (`UserID`),
	KEY `VPSDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `VPSDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSDomainsPolitics`
	ADD CONSTRAINT `VPSDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `VPSBonuses` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`UserID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`Discont` float(5,2) DEFAULT '0.00',
	`Comment` char(255) DEFAULT '',
	PRIMARY KEY (`ID`),
	KEY `VPSBonusesUserID` (`UserID`),
	KEY `VPSBonusesSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR
ALTER TABLE `VPSBonuses`
	ADD CONSTRAINT `VPSBonusesSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSBonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSConsider` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`VPSOrderID` int(11) NOT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`DaysConsidered` int(11) DEFAULT '0',
	`Cost` float(7,2) DEFAULT '0.00',
	`Discont` float(5,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `VPSConsiderVPSOrderID` (`VPSOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR
ALTER TABLE `VPSConsider`
	ADD CONSTRAINT `VPSConsiderVPSOrderID` FOREIGN KEY (`VPSOrderID`) REFERENCES `VPSOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


