
/* added by lissyara, 2014-12-25 in 21:37 MSK */
INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (4,100,100,'yes','yes','yes','Contracts/Enclosures/Types/DNSmanagerRules/Content','Регламент предоставления услуги вторичного DNS','<NOBODY><P align="justify">
Регламент предоставления услуги вторичного сервера DNS (системы доменных имён) полностью соответствует <a href="/Clause?ClauseID=Contracts/Enclosures/Types/HostingRules/Content">регламенту предоставления услуги хостинга</a>.
</P></NOBODY>');


-- SEPARATOR

DELETE FROM `Services` WHERE `ID` = 52000;
-- SEPARATOR

INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `NameShort`, `Code`, `Item`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`) VALUES
(52000, 2000000, 1, 1100, 'Вторичный DNS', 'Вторичный DNS', 'DNSmanager', 'Вторичный DNS', 'дн.', 'Daily', 0.00, 0.00, 'no', 'yes', 'yes', 'yes', 52000);

-- SEPARATOR
--
-- Table structure for table `DNSmanagerSchemes`
--

DROP TABLE IF EXISTS `DNSmanagerSchemes`;
CREATE TABLE `DNSmanagerSchemes` (
	`ID` int(11) NOT NULL auto_increment,
	`CreateDate` int(11) default '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) default '',
	`PackageID` char(30) default '',
	`CostDay` decimal(11,2) default '0.00',
	`CostMonth` decimal(11,2) default '0.00',
	`ServersGroupID` int(11) NOT NULL,
	`HardServerID` int(11) NULL,
	`Comment` char(255) default '',
	`IsActive` enum('no','yes') default 'yes',
	`IsProlong` enum('no','yes') default 'yes',
	`IsSchemeChangeable` enum('no','yes') default 'yes',
	`IsSchemeChange` enum('no','yes') default 'yes',
	`MinDaysPay` int(6) default '0',			/* минимальное число дней первой оплаты */
	`MinDaysProlong` INT(6) default '0',			/* минимальное число дней продления, для ранее оплаченных заказов */
	`MaxDaysPay` int(6) default '0',			/* максимальное число дней оплаты заказа */
	`MaxOrders` int(6) DEFAULT '0',				/* максимальное число заказов по этому тарифу, на одного пользователя */
	`SortID` int(11) default '10',
	--
	-- Common
	--
	`IsReselling` enum('no','yes') default 'no',		/* тариф предполагает создание реселлера */
	`Reseller` char(255) default '',			/* реселлер от которого будут создаваться юзеры */
	`ViewArea` char(255) default '',			/* view используемая в DNS */
	`DomainLimit` int(11) default '0',			/* ограничение на число доменов */

	PRIMARY KEY  (`ID`),
	KEY `DNSmanagerSchemesGroupID` (`GroupID`),
	CONSTRAINT `DNSmanagerSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesUserID` (`UserID`),
	CONSTRAINT `DNSmanagerSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesServersGroupID` (`ServersGroupID`),
	CONSTRAINT `DNSmanagerSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesHardServerID` (`HardServerID`),
	CONSTRAINT `DNSmanagerSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR
--
-- Table structure for table `DNSmanagerOrders`
--

DROP TABLE IF EXISTS `DNSmanagerOrders`;
CREATE TABLE `DNSmanagerOrders` (
	`ID` int(11) NOT NULL auto_increment,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) default NULL,
	`Login` char(20) default '',
	`Password` char(64) default '',
	`ConsiderDay` int(11) default '0',
	`StatusID` char(30) default 'UnSeted',
	`StatusDate` int(11) default '0',

	PRIMARY KEY  (`ID`),
	KEY `DNSmanagerOrdersOrderID` (`OrderID`),
	CONSTRAINT `DNSmanagerOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerOrdersSchemeID` (`SchemeID`),
	CONSTRAINT `DNSmanagerOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DNSmanagerSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


