
/* ISPsw values added by lissyara 2011-09-06 in 15:24 MSK */

CREATE TABLE IF NOT EXISTS `ISPswLicenses` (
	`ID` int(9) NOT NULL AUTO_INCREMENT,
	`ISPtype` char(12) NOT NULL,
	`IP` char(64) NOT NULL,
	`elid` int(12) NOT NULL,
	`IsInternal` enum('yes','no') NOT NULL DEFAULT 'yes',
	`IsUsed` enum('yes','no') NOT NULL DEFAULT 'yes',
	`ISPname` text,
	`StatusID` char(30) DEFAULT 'UnSeted',
	`CreateDate` int(11) NOT NULL,	-- дата создания лицензии
	`UpdateDate` int(11) NOT NULL,	-- дата последнего обновления инфы с биллинга ISPsystems
	`StatusDate` int(11) NOT NULL,	-- дата последнего изменения IP адреса
	`ExpireDate` int(11) NOT NULL,	-- актуально для триальных лицензий, или со сроком действия
	`Flag` char(32) NOT NULL,
	PRIMARY KEY (`ID`),
	UNIQUE KEY `elid` (`elid`),
	KEY `IP` (`IP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Name` char(60) DEFAULT '',
	`Comment` text,
	`Address` char(60) DEFAULT '',
	`SortID` text,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswSchemes` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) DEFAULT '',
	`PackageID` char(30) DEFAULT '',
	`CostDay` float(11,2) DEFAULT '0.00',
	`CostMonth` float(11,2) DEFAULT '0.00',
	`CostInstall` float(11,2) DEFAULT '0.00',
	`SoftWareGroup` int(11) NOT NULL,
	`Comment` char(255) NOT NULL,
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChangeable` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChange` enum('no','yes') DEFAULT 'yes',
	`IsInternal` ENUM('no','yes') DEFAULT 'no',
	`MinDaysPay` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`IsTimeManage` enum('no','yes') DEFAULT 'yes',
	`SortID` int(11) DEFAULT '10',
	`ISPtype` char(12) DEFAULT '7:7',
	PRIMARY KEY (`ID`),
	KEY `ISPswSchemesGroupID` (`GroupID`),
	KEY `ISPswSchemesUserID` (`UserID`),
	KEY `ISPswSchemesSoftWareGroupID` (`SoftWareGroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `ISPswSchemes`
ADD CONSTRAINT `ISPswSchemesSoftWareGroupID` FOREIGN KEY (`SoftWareGroup`) REFERENCES `ISPswGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswOrders` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) DEFAULT NULL,
	`AutoProlong` enum('no','yes') NOT NULL DEFAULT 'yes',
	`IP` text,
	`DaysRemainded` int(11) DEFAULT '0',
	`ConsiderDay` int(11) DEFAULT '0',
	`StatusID` char(30) DEFAULT 'UnSeted',
	`StatusDate` int(11) DEFAULT '0',
	`UserNotice` TEXT,
	`AdminNotice` TEXT,
	PRIMARY KEY (`ID`),
	KEY `ISPswOrdersOrderID` (`OrderID`),
	KEY `ISPswOrdersSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswOrders`
ADD CONSTRAINT `ISPswOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `ISPswOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysPay` int(11) DEFAULT '665',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `ISPswPoliticsGroupID` (`GroupID`),
	KEY `ISPswPoliticsUserID` (`UserID`),
	KEY `ISPswPoliticsSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswPolitics`
	ADD CONSTRAINT `ISPswPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `ISPswDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `ISPswDomainsPoliticsGroupID` (`GroupID`),
	KEY `ISPswDomainsPoliticsUserID` (`UserID`),
	KEY `ISPswDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `ISPswDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswDomainsPolitics`
	ADD CONSTRAINT `ISPswDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswBonuses` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`UserID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`Discont` float(11,2) DEFAULT '0.00',
	`Comment` char(255) DEFAULT '',
	PRIMARY KEY (`ID`),
	KEY `ISPswBonusesUserID` (`UserID`),
	KEY `ISPswBonusesSchemeID` (`SchemeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `ISPswBonuses`
	ADD CONSTRAINT `ISPswBonusesSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswBonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `ISPswConsider` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`ISPswOrderID` int(11) NOT NULL,
	`DaysReserved` int(11) DEFAULT '0',
	`DaysRemainded` int(11) DEFAULT '0',
	`DaysConsidered` int(11) DEFAULT '0',
	`Cost` float(11,2) DEFAULT '0.00',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `ISPswConsiderISPswOrderID` (`ISPswOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswConsider`
	ADD CONSTRAINT `ISPswConsiderISPswOrderID` FOREIGN KEY (`ISPswOrderID`) REFERENCES `ISPswOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;




