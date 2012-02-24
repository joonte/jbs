--
-- Структура таблицы `ExtraIPBonuses`
--

CREATE TABLE IF NOT EXISTS `ExtraIPBonuses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `UserID` int(11) NOT NULL,
  `SchemeID` int(11) DEFAULT NULL,
  `DaysReserved` int(11) DEFAULT '0',
  `DaysRemainded` int(11) DEFAULT '0',
  `Discont` float(11,2) DEFAULT '0.00',
  `Comment` char(255) DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPBonusesUserID` (`UserID`),
  KEY `ExtraIPBonusesSchemeID` (`SchemeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPConsider`
--

CREATE TABLE IF NOT EXISTS `ExtraIPConsider` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `ExtraIPOrderID` int(11) NOT NULL,
  `DaysReserved` int(11) DEFAULT '0',
  `DaysRemainded` int(11) DEFAULT '0',
  `DaysConsidered` int(11) DEFAULT '0',
  `Cost` float(11,2) DEFAULT '0.00',
  `Discont` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPConsiderExtraIPOrderID` (`ExtraIPOrderID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPDomainsPolitics`
--

CREATE TABLE IF NOT EXISTS `ExtraIPDomainsPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) DEFAULT NULL,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `DaysPay` int(11) DEFAULT '365',
  `Discont` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPDomainsPoliticsGroupID` (`GroupID`),
  KEY `ExtraIPDomainsPoliticsUserID` (`UserID`),
  KEY `ExtraIPDomainsPoliticsSchemeID` (`SchemeID`),
  KEY `ExtraIPDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPOrders`
--

CREATE TABLE IF NOT EXISTS `ExtraIPOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `OldSchemeID` int(11) DEFAULT NULL,
  `AutoProlong` enum('no','yes') NOT NULL DEFAULT 'yes',
  `OrderType` char(32) NOT NULL,
  `DependOrderID` int(11) NOT NULL,
  `Domain` char(255) DEFAULT '',
  `Parked` text,
  `ServerID` int(11) NOT NULL,
  `Login` char(20) DEFAULT 'noassign',
  `Password` char(64) DEFAULT '',
  `DaysRemainded` int(11) DEFAULT '0',
  `ConsiderDay` int(11) DEFAULT '0',
  `StatusID` char(30) DEFAULT 'UnSeted',
  `StatusDate` int(11) DEFAULT '0',
  `UserNotice` text,
  `AdminNotice` text,
  PRIMARY KEY (`ID`),
  KEY `ExtraIPOrdersOrderID` (`OrderID`),
  KEY `ExtraIPOrdersSchemeID` (`SchemeID`),
  KEY `ExtraIPOrdersServerID` (`ServerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPPolitics`
--

CREATE TABLE IF NOT EXISTS `ExtraIPPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) DEFAULT NULL,
  `DaysPay` int(11) DEFAULT '665',
  `Discont` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPPoliticsGroupID` (`GroupID`),
  KEY `ExtraIPPoliticsUserID` (`UserID`),
  KEY `ExtraIPPoliticsSchemeID` (`SchemeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Структура таблицы `ExtraIPs`
--

CREATE TABLE IF NOT EXISTS `ExtraIPs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` char(30) DEFAULT '',
  `sGroupID` int(11) NOT NULL,
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
  KEY `ExtraIPssGroupID` (`sGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Структура таблицы `ExtraIPSchemes`
--

CREATE TABLE IF NOT EXISTS `ExtraIPSchemes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) DEFAULT '',
  `PackageID` char(30) DEFAULT '',
  `CostDay` float(11,2) DEFAULT '0.00',
  `CostMonth` float(11,2) DEFAULT '0.00',
  `CostInstall` float(11,2) NOT NULL,
  `HostingGroupID` int(11) NOT NULL,
  `VPSGroupID` int(11) NOT NULL,
  `DSGroupID` int(11) NOT NULL,
  `Comment` char(255) NOT NULL,
  `IsAutomatic` enum('no','yes') DEFAULT 'no',
  `IsActive` enum('no','yes') DEFAULT 'yes',
  `IsProlong` enum('no','yes') DEFAULT 'yes',
  `MinDaysPay` int(6) DEFAULT '0',
  `MaxDaysPay` int(6) DEFAULT '0',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPSchemesGroupID` (`GroupID`),
  KEY `ExtraIPSchemesUserID` (`UserID`),
  KEY `ExtraIPSchemessGroupID` (`HostingGroupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Структура таблицы `ExtraIPsGroups`
--

CREATE TABLE IF NOT EXISTS `ExtraIPsGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `FunctionID` char(30) DEFAULT '',
  `Comment` char(255) DEFAULT '',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPBonuses`
--
ALTER TABLE `ExtraIPBonuses`
  ADD CONSTRAINT `ExtraIPBonusesSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPBonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPConsider`
--
ALTER TABLE `ExtraIPConsider`
  ADD CONSTRAINT `ExtraIPConsiderExtraIPOrderID` FOREIGN KEY (`ExtraIPOrderID`) REFERENCES `ExtraIPOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPDomainsPolitics`
--
ALTER TABLE `ExtraIPDomainsPolitics`
  ADD CONSTRAINT `ExtraIPDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPOrders`
--
ALTER TABLE `ExtraIPOrders`
  ADD CONSTRAINT `ExtraIPOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPPolitics`
--
ALTER TABLE `ExtraIPPolitics`
  ADD CONSTRAINT `ExtraIPPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPs`
--
ALTER TABLE `ExtraIPs`
  ADD CONSTRAINT `ExtraIPssGroupID` FOREIGN KEY (`sGroupID`) REFERENCES `ExtraIPsGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


